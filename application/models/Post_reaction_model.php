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

	public function delete_post_reaction($id_post, $id_subscriber) {
		return $this->db->delete('post_reaction', array('id_post' => $id_post, 'id_subscriber' => $id_subscriber));
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

	public function get_post_reactions($id_post, $active_subscriber = 0){
		$reactions_type = array();
		$query = '
            SELECT pr.`reaction_type`, 
            (SELECT  count(reaction_type) FROM  post_reaction WHERE id_post = ? AND reaction_type = pr.reaction_type) as reaction_type_number
            FROM `post_reaction` pr
            WHERE pr.id_post = ?
            GROUP by reaction_type
            ORDER BY reaction_type_number DESC
            LIMIT 3
            ';
		$query = $this->db->query($query, array($id_post, $id_post));
		$results = $query->result();
		foreach ($results as $result)
		{
			$data = array();
			$data['reaction_type'] = $result->reaction_type;
			$reactions_type[] = $data;
		}
		$reaction['id_post'] = $id_post;
		$reaction['total'] = $this->total_post_reactions($id_post);
		$reaction['subscriber_reaction'] = $this->subscriber_post_reaction($id_post, $active_subscriber);
		$reaction['top_reactions'] = $reactions_type;
		return $reaction;
	}

	//count reactions of specific post
	function  total_post_reactions($id_post)
	{
		$total = 0;
		$query = $this->db->query('
		SELECT COUNT(*) as total
		 FROM post_reaction 
		 WHERE id_post = ?'
			,array($id_post));
		$results = $query->result();
		foreach ($results as $result)
		{
			$total = $result->total;
		}
		return $total;
	}

	//get reaction type of a subscriber on a post
	function  subscriber_post_reaction($id_post, $id_subscriber)
	{
		$reaction_type = "0";
		$query = $this->db->query('
		SELECT reaction_type
		 FROM post_reaction 
		 WHERE id_post = ?
		 AND id_subscriber = ?'
			,array($id_post, $id_subscriber));
		$results = $query->result();
		foreach ($results as $result)
		{
			$reaction_type = $result->reaction_type;
		}
		return $reaction_type;
	}
}