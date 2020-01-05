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

}