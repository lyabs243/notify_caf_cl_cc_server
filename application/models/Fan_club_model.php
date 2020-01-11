<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 11/01/2020
 * Time: 08:46
 */

class Fan_club_model extends CI_Model
{
	public function __construct()
	{
		$this->load->database();
	}

	public function add_fan($data) {
		if(!$this->is_fan_exist($data['id_subscriber'], $data['id_team'], $data['category'])) {
			$this->db->insert('spt_fan', $data);
			return $this->db->insert_id();
		}
		return 0;
	}

	function  is_fan_exist($idSubscriber,$idTeam,$category)
	{
		$return = false;
		$query = $this->db->query('SELECT * FROM spt_fan WHERE id_subscriber = ? AND id_team = ? AND category = ?'
			,array($idSubscriber, $idTeam, $category));
		$results = $query->result();
		foreach ($results as $result)
		{
			$return = true;
		}
		return $return;
	}
}