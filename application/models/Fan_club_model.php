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

	function  get_fan($idSubscriber, $category)
	{
		$query = $this->db->query('
				SELECT sf.id_subscriber, sf.id_team, sf.category, st.title, st.country_code, st.url_logo, st.top_club, st.color
				FROM spt_fan sf
				JOIN spt_team st 
				ON sf.id_team = st.id
				WHERE sf.id_subscriber = ?
				AND sf.category = ?'
			,array($idSubscriber, $category));
		$results = $query->result();
		$data = array();
		foreach ($results as $result)
		{
			$row['id_subscriber'] = $result->id_subscriber;
			$row['id_team'] = $result->id_team;
			$row['category'] = $result->category;
			$row['title'] = $result->title;
			$row['country_code'] = $result->country_code;
			$row['url_logo'] = $result->url_logo;
			$row['top_club'] = $result->top_club;
			$row['color'] = $result->color;

			$data = $row;
		}

		return $data;
	}
}