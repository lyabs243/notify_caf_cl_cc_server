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

}