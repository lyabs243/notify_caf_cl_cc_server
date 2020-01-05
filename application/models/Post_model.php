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
	public function get_posts($active_subscriber, $idSubscriber, $page){
		$this->load->model('Post_reaction_model');
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
			//get reaction for each post
			$data['reaction'] = $this->Post_reaction_model->get_post_reactions($result->id, $active_subscriber);
			$posts[] = $data;
		}
		return $posts;
	}

	//get a specific post
	public function get_post($id_post, $active_subscriber){
		$this->load->model('Post_reaction_model');
		$timezone = $this->session->timezone;
		$post = null;
		$args[] = $timezone;
		$args[] = $id_post;
		$query = '
            SELECT p.`id`, p.`id_subscriber`, p.`post`, p.`url_image`, p.`type`, p.active, 
             CONVERT_TZ(p.`register_date`,@@session.time_zone,?) as register_date,s.full_name, s.url_profil_pic
            FROM `post` p
            JOIN subscriber s
            ON p.id_subscriber = s.id
            WHERE p.active = 1
            AND p.id = ?
            ';
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
			//get reaction for each post
			$data['reaction'] = $this->Post_reaction_model->get_post_reactions($result->id, $active_subscriber);
			$post = $data;
		}
		return $post;
	}

	public function add_comment($data) {
		// insertion dans la table
		$this->db->insert('spt_comment', $data);
		return $this->db->insert_id();
	}

	public function get_abusive_posts($page, $id_admin){
		$this->load->model('Post_reaction_model');
		$timezone = $this->session->timezone;
		$page_start = (((int)$page)-1)*10;
		$query = '
            SELECT ap.id, ap.message, ap.active, ap.id_post, ap.id_subscriber, CONVERT_TZ(ap.register_date,@@session.time_zone,?) as register_date, p.`id` as id_post, p.`id_subscriber` as id_subscriber_post, p.`post`,
             p.`url_image`, p.`type`, p.active as active_post, CONVERT_TZ(p.`register_date`,@@session.time_zone,?) as register_date_post,
             s.full_name as full_name_post, s.url_profil_pic as url_profil_pic_post,aps.full_name, aps.url_profil_pic
            FROM `abusive_post` ap
            JOIN subscriber aps 
            ON ap.id_subscriber = aps.id
            JOIN `post` p
            ON ap.id_post = p.id
            JOIN subscriber s
            ON p.id_subscriber = s.id
            WHERE ap.active = 1
            ORDER BY register_date ASC 
            LIMIT ?,10 ';
		$query = $this->db->query($query, array($timezone, $timezone, $page_start));
		$results = $query->result();
		$abusive_posts = [];
		foreach ($results as $result)
		{
			$data = array();

			$data['id'] = $result->id;
			$data['id_post'] = $result->id_post;
			$data['message'] = $result->message;
			$data['active'] = $result->active;
			$data['id_subscriber'] = $result->id_subscriber;
			$data['register_date'] = $result->register_date;

			$data['subscriber']['full_name'] = $result->full_name;
			$data['subscriber']['url_profil'] = $result->url_profil_pic;

			$data['post']['id'] = $result->id_post;
			$data['post']['id_subscriber'] = $result->id_subscriber_post;
			$data['post']['post'] = $result->post;
			$data['post']['url_image'] = $result->url_image;
			$data['post']['subscriber']['full_name'] = $result->full_name_post;
			$data['post']['subscriber']['url_profil'] = $result->url_profil_pic_post;
			$data['post']['type'] = $result->type;
			$data['post']['active'] = $result->active_post;
			$data['post']['register_date'] = $result->register_date_post;
			//get reaction for each post
			$data['post']['reaction'] = $this->Post_reaction_model->get_post_reactions($result->id_post, $id_admin);
			$abusive_posts[] = $data;
		}
		return $abusive_posts;
	}

	public function update_post_status($id, $data) {
    	//on block post, deactivate also abusive posts signal related to this post
		if(!$data['active']) {
			$this->db->update('abusive_post', array('active' => 0), array('id_post' => $id));
		}
		$this->db->where('id', $id);
		return $this->db->update('post', $data);
	}

	public function update_abusive_post_status($id, $data) {
		$this->db->where('id', $id);
		return $this->db->update('abusive_post', $data);
	}

}