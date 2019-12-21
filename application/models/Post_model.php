<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 21/12/2019
 * Time: 10:18
 */

class Post_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
    }

    public function add_post($data) {
        $this->db->insert('post', $data);
        $id = $this->db->insert_id();

        return $id;
    }

	public function signal_post($data) {
		$this->db->insert('abusive_post', $data);
		$id = $this->db->insert_id();

		return $id;
	}

	public function update_post($id, $idSubscriber, $data) {
		return $this->db->update('post', $data, array('id' => $id, 'id_subscriber' => $idSubscriber));
	}

	public function delete_post($id, $idSubscriber) {
		return $this->db->delete('post', array('id' => $id, 'id_subscriber' => $idSubscriber));
	}

	//get posts of specific subscriber or all posts
	public function get_posts($idSubscriber, $page){
		$timezone = $this->session->timezone;
		$page_start = (((int)$page)-1)*10;
		$posts = array();
		$args[] = $timezone;
		$query = '
            SELECT p.`id`, p.`id_subscriber`, p.`post`, p.`url_image`, p.`type`, p.active, 
             CONVERT_TZ(p.`register_date`,@@session.time_zone,?) as register_date,s.full_name, s.url_profil_pic
            FROM `post` p
            JOIN subscriber s
            ON p.id_subscriber = s.id
            WHERE p.active = 1
            ';
		if((int)$idSubscriber > 0) {
			$query .= ' AND s.id = ? ';
			$args[] = $idSubscriber;
		}
		$query .= ' ORDER BY register_date DESC 
            LIMIT ?,10 ';
		$args[] = $page_start;
		$query = $this->db->query($query, $args);
		$results = $query->result();
		foreach ($results as $result)
		{
			$data = array();
			$data['id'] = $result->id;
			$data['id_subscriber'] = $result->id_subscriber;
			$data['post'] = $result->post;
			$data['url_image'] = $result->url_image;
			$data['subscriber']['full_name'] = $result->full_name;
			$data['subscriber']['url_profil_pic'] = $result->url_profil_pic;
			$data['type'] = $result->type;
			$data['active'] = $result->active;
			$data['register_date'] = $result->register_date;
			$posts[] = $data;
		}
		return $posts;
	}

}