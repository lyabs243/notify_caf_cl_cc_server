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
		if(!$this->is_fan_exist($data['id_subscriber'], $data['category'])) {
			$this->db->insert('spt_fan', $data);
			return $this->db->insert_id();
		}
		else {
			return $this->update($data['id_subscriber'], $data['category'], $data);
		}
	}

	function  is_fan_exist($idSubscriber,$category)
	{
		$return = false;
		$query = $this->db->query('SELECT * FROM spt_fan WHERE id_subscriber = ? AND category = ?'
			,array($idSubscriber, $category));
		$results = $query->result();
		foreach ($results as $result)
		{
			$return = true;
		}
		return $return;
	}

	public function update($idSubscriber, $competitionCategory, $data) {
		$result = $this->db->update('spt_fan', $data,
			array('id_subscriber' => $idSubscriber, 'category' => $competitionCategory));
		return $result;
	}

	public function delete_badge($idSubscriber, $competitionCategory) {
		return $this->db->delete('spt_fan', array('id_subscriber' => $idSubscriber, 'category' => $competitionCategory));
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

	function  get_countries($category)
	{
		$query = $this->db->query('
				SELECT c.nicename, c.url_flag, c.iso3 as country_code
				FROM country c
				WHERE c.iso3 IN (
					SELECT country_code FROM spt_team 
					WHERE category = ?
				)
				ORDER BY c.nicename ASC'
			,array($category));
		$results = $query->result();
		$data = array();
		foreach ($results as $result)
		{
			$row['nicename'] = $result->nicename;
			$row['url_flag'] = $result->url_flag;
			$row['country_code'] = $result->country_code;

			$data[] = $row;
		}

		return $data;
	}

	function  get_country_clubs($country_code, $category)
	{
		$country_code = strtolower($country_code);
		$query = $this->db->query('
				SELECT st.`id`, st.`title`, st.`title_small`, st.`is_national_team`, st.`country_code`, st.`url_logo`, st.`category`,
				 st.`top_club`, st.`color`, st.`register_date`
				FROM spt_team st
				WHERE st.category = ?
				AND LOWER(st.country_code) = ?
				AND st.is_national_team = 0
				ORDER BY st.top_club DESC, st.title ASC'
			,array($category, $country_code));
		$results = $query->result();
		$data = array();
		foreach ($results as $result)
		{
			$row['id'] = $result->id;
			$row['title'] = $result->title;
			$row['title_small'] = $result->title_small;
			$row['is_national_team'] = $result->is_national_team;
			$row['country_code'] = $result->country_code;
			$row['url_logo'] = $result->url_logo;
			$row['category'] = $result->category;
			$row['top_club'] = $result->top_club;
			$row['color'] = $result->color;
			$row['register_date'] = $result->register_date;

			$data[] = $row;
		}

		return $data;
	}
}