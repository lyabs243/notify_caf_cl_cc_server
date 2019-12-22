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
}