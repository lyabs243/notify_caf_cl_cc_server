<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/01/2020
 * Time: 14:26
 */

class Team_model extends CI_Model
{
	public function __construct()
	{
		$this->load->database();
	}

	public function add_teams_from_json($json)
	{
		$sql = 'INSERT INTO `spt_team`( `title`,  `is_national_team`, `country_code`, `url_logo`, `category`, top_club, `color`)
 			VALUES (?, ?, ?, ?, ?, ?, ?)';
		$teams = json_decode($json);
		echo 'Total: ' . count($teams) . '<br>';
		foreach($teams as $team){
			$url_logo = 'http://notifygroup.org/notifyapp/api/resource/teams/' . $team->url_logo;
			$category = 1;
			$is_national_team = 0;
			$country_code = $team->country_code;
			$title = $team->name;
			$color = $team->color;
			$top_club = $team->top_club;

			if (!$this->is_team_exist($url_logo)) {
				$args = array($title, $is_national_team, $country_code, $url_logo, $category, $top_club, $color);
				echo $this->db->query($sql, $args) . ' ' . $title . '<br>';
			}
		}
	}

	//temporary function: updates teams api id and url logo
	public function update_teams_from_json($json)
	{
		$teams = json_decode($json);
		echo 'Total: ' . count($teams) . '<br>';
		foreach($teams as $team){
			$data = null;
			$data['api_id'] = $team->api_id;

			if($data['api_id'] > 0) {
				$data['url_logo'] = 'https://media.api-football.com/teams/' . $data['api_id'] . '.png';
			}

			$this->db->where('title', $team->name);
			echo $this->db->update('spt_team', $data) . ' ' . $team->name . '<br>';
		}
	}

	public function update_country_urlflag_from_json($json)
	{
		$countries = json_decode($json);
		echo 'Total: ' . count($countries) . '<br>';
		foreach($countries as $country){
			$data['url_flag'] = 'http://notifygroup.org/notifyapp/api/resource/teams/' . $country->url_flag;

			$this->db->where('iso3', $country->country_code);
			echo $this->db->update('country', $data) . ' ' . $country->name;
		}
	}

	//check if specific team already exist from url logo
	function  is_team_exist($url_team_logo)
	{
		$return = false;
		$query = $this->db->query('SELECT * FROM spt_team WHERE url_logo = ?'
			,array($url_team_logo));
		$results = $query->result();
		foreach ($results as $result)
		{
			$return = true;
		}
		return $return;
	}
}