<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 22/12/2019
 * Time: 09:38
 */

class Post_reaction_model extends CI_Model
{
	public function __construct() {
		$this->load->database();
	}

	public function add_post_reaction($data) {
		$this->db->insert('post_reaction', $data);
		$id = $this->db->insert_id();

		return $id;
	}

	public function update_post_reaction($id_post, $id_subscriber, $data) {
		return $this->db->update('post_reaction', $data, array('id_post' => $id_post, 'id_subscriber' => $id_subscriber));
	}

	function  is_subscriber_reaction_exist($id_post, $id_subscriber)
	{
		$return = false;
		$query = $this->db->query('SELECT * FROM post_reaction WHERE id_post = ? AND id_subscriber = ?'
			,array($id_post, $id_subscriber));
		$results = $query->result();
		foreach ($results as $result)
		{
			$return = true;
		}
		return $return;
	}
}