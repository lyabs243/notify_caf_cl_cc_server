<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 05/01/2020
 * Time: 11:35
 */

class Comment_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
    }

	public function update_comment($id, $idUser, $data) {
		return $this->db->update('spt_comment', $data, array('id' => $id, 'id_user' => $idUser));
	}

	public function delete_comment($id, $idUser) {
		return $this->db->delete('spt_comment', array('id' => $id, 'id_user' => $idUser));
	}

	function  total_post_comments($id_post)
	{
		$total = 0;
		$query = $this->db->query('
		SELECT COUNT(*) as total
		 FROM spt_comment 
		 WHERE id_post = ?'
			,array($id_post));
		$results = $query->result();
		foreach ($results as $result)
		{
			$total = $result->total;
		}
		return $total;
	}

	public function add_comment($data) {
		$this->load->model('Comment_model');
		$this->db->insert('spt_comment', $data);
		$data = array();
		$id = $this->db->insert_id();
		if($id > 0) {
			$data = $this->Comment_model->get_comment($id);
		}
		return $data;
	}

	//get a specific comment
	public function get_comment($id) {
		$this->load->model('Subscriber_model');
		$timezone = $this->session->timezone;
		$sql = "SELECT sc.id,sc.`id_user`, sc.`comment`, CONVERT_TZ(sc.`register_date`,@@session.time_zone,?) as register_date,
 				s.full_name, s.url_profil_pic, s.id_account_user, sc.id_post, sc.id_match, s.id as id_subscriber
                FROM `spt_comment` sc
                JOIN subscriber s
                ON sc.id_user = s.id_user
                WHERE sc.id = ?";
		$args = array($timezone, $id);

		$query = $this->db->query($sql,$args);
		$results = $query->result();
		$comment = null;
		foreach ($results as $result)
		{
			$row['id'] = $result->id;
			$row['id_post'] = $result->id_post;
			$row['id_match'] = $result->id_match;
			$row['id_user'] = $result->id_user;
			$row['comment'] = $result->comment;
			$row['subscriber'] = $this->Subscriber_model->get($result->id_subscriber);
			$row['register_date'] = $result->register_date;

			$comment = $row;
		}

		return $comment;
	}

}