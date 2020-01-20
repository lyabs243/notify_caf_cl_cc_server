<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 18/01/2020
 * Time: 15:39
 */

class Api_football_model extends CI_Model
{
	public function __construct()
	{
		$this->load->database();
		$this->load->model('Team_model');
	}

	public function init_matchs_from_api($id_edition, $league_id)
	{
		$urlRounds = "https://api-football-v1.p.rapidapi.com/v2/fixtures/rounds/$league_id";
		$json = $this->get_api_data($urlRounds);
		if($json != null) {
			$this->get_all_rounds_api($id_edition, $league_id, $json);
		}
	}

	public function get_all_rounds_api($id_edition, $league_id, $json)
	{
		$json_decode = json_decode($json);
		$rounds = $json_decode->api->fixtures;
		foreach($rounds as $round){
			$notifyRoundDormat = $round;
			if(strpos($round, '-') > 0) {
				$notifyRoundDormat = substr($round, 0, strpos($round, '-'));
			}
			$editionStage = $this->is_round_exist($notifyRoundDormat, $id_edition);
			if($editionStage) {
				//check if round has fixture if no, register fixture of that round
				//or register fixture if round has unfinished matchs
				if(!$this->is_round_contains_fixtures($round, $editionStage) ||
					$this->is_round_contains_fixtures($round, $editionStage, true)) {
					$this->add_round_matchs($league_id, $editionStage, $round);
				}
			}
			else {
				$editionStage = $this->add_round($id_edition, $notifyRoundDormat);
				$this->add_round_matchs($league_id, $editionStage, $round);
			}
		}
	}

	public function add_round_matchs($league_id, $editionStage, $round_code)
	{
		$this->load->model('Match_model');
		$url = "https://api-football-v1.p.rapidapi.com/v2/fixtures/league/$league_id/$round_code";

		$json = $this->get_api_data($url);

		if ($json != null) {
			$json_decode = json_decode($json);
			$matchs = $json_decode->api->fixtures;
			foreach($matchs as $match) {
				$data = null;

				$data = $this->init_match_from_api($match, $editionStage);

				if (!$this->Match_model->is_match_exist($data['api_id'])) {
					echo $this->db->insert('spt_match', $data) . ' ' . $data['api_round'] . '<br>';
				}
				else { //update fixture in database
					echo  $this->db->update('spt_match', $data, array('api_id' => $data['api_id'])) . ' ' . $data['api_round'] . '<br>';
				}
			}
		}
	}

	function init_match_from_api($match, $editionStage=0) {
		$data = null;
		$data['api_id'] = $match->fixture_id;
		$data['api_round'] = $match->round;
		if($editionStage > 0) {
			$data['id_edition_stage'] = $editionStage;
		}
		$data['id_team_a'] = $this->Team_model->get_teamid_from_apiid($match->homeTeam->team_id);
		$data['id_team_b'] = $this->Team_model->get_teamid_from_apiid($match->awayTeam->team_id);
		$data['team_a_goal'] = ($match->goalsHomeTeam == null)? 0 : $match->goalsHomeTeam;
		$data['team_b_goal'] = ($match->goalsAwayTeam == null)? 0 : $match->goalsAwayTeam;
		if(strlen($match->score->penalty) > 0) {
			$data['team_a_penalty'] = substr($match->score->penalty, 0, strpos($match->score->penalty, '-'));
			$data['team_b_penalty'] = substr($match->score->penalty, strpos($match->score->penalty, '-') + 1);
		}
		$data['status'] = $this->get_status_code($match->statusShort);
		$data['match_date'] = date('Y-m-d H:i:s',$match->event_timestamp);

		return $data;
	}

	//check if round exist, if it exist, return edition stage id
	function  is_round_exist($round_code, $id_edition)
	{
		$return = 0;
		$query = $this->db->query('SELECT id FROM spt_edition_stage WHERE api_round_id = ? AND id_edition = ?'
			,array($round_code, $id_edition));
		$results = $query->result();
		foreach ($results as $result)
		{
			$return = $result->id;
		}
		return $return;
	}

	//add a new round and return his id edition stage
	public function add_round($id_edition, $round_code)
	{

		$data['api_round_id'] = $round_code;
		$data['id_edition'] = $id_edition;
		$data['type'] = 2;
		$data['title'] = str_replace('_', ' ', $round_code );

		$this->db->insert('spt_edition_stage', $data);
		return $this->is_round_exist($round_code, $id_edition);
	}

	//add events matchs from api
	public function add_matchs_actions()
	{
		$this->load->model('Match_model');
		//get all matchs  that date in intervall of 3 minutes before to 3 hour after
		$matchs = $this->Match_model->get_matchs_in_intervall('5 MINUTE', '3 HOUR');

		foreach ($matchs as $match) {
			echo $match['teamA'] . 'VS' . $match['teamB'] . ' ' . '<br><br><br>';
			$fixture_api_id = $match['api_id'];

			//get and update match data
			$url = "https://api-football-v1.p.rapidapi.com/v2/fixtures/id/$fixture_api_id";
			$json = $this->get_api_data($url);
			$json_decode = json_decode($json);
			$fixtures = $json_decode->api->fixtures;

			foreach($fixtures as $fixture) {

				$data = null;
				$data = $this->init_match_from_api($fixture);

				echo $this->db->update('spt_match', $data, array('api_id' => $data['api_id'])) . ' ' . $data['api_round'] . '<br><br><br>';

				//get match events
				$events = $fixture->events;
				foreach ($events as $event) {
					$action = null;

					$action['id_match'] = $match['id'];
					$action['type'] = $this->get_action_type($event->type, $event->detail);
					$action['detail_a'] = $event->player;
					$action['detail_b'] = $event->assist;
					$action['minute'] = $event->elapsed;
					$action['api_id_player'] = $event->player_id;
					$action['api_id_player_assist'] = $event->assist_id;
					$action['api_id_team'] = $event->team_id;

					echo $this->Match_model->add_action($action) . ' ' . $action['minute'] . '<br>';
				}

				//get match lineup
				$lineups = $fixture->lineups;
				$idComposition = $this->Match_model->add_match_composition($match['id']);

				foreach ($lineups as $lineup) {
					$items = $lineup->startXI;
					$players = array();

					foreach ($items as $item) {
						$player = null;

						$player['id_match'] = $match['id'];
						$player['id_composition'] = $idComposition;
						$player['api_id_player'] = $item->player_id;
						$player['api_id_team'] = $item->team_id;
						$player['description'] = $item->player;
						$player['number'] = $item->number;
						$player['position'] = $item->pos;

						$players[] = $player;

					}

					echo $this->Match_model->add_composition_details($players) . ' ' . $player['description'] . '<br>';
				}
			}
		}
	}

	/**
	 * check if round contains fixtures
	 *
	 * @param $round_code: the api rounde code
	 * @param $id_edition_stage
	 * @param bool $gameFinished : help to check if round has still have fixtures that not
	 * finished
	 * @return bool
	 */
	function  is_round_contains_fixtures($round_code, $id_edition_stage, $gameFinished=false)
	{
		$return = false;
		$query = $this->db->query('SELECT id FROM spt_match WHERE api_round = ? AND id_edition_stage = ?'
			. (($gameFinished)? ' AND status <> 3' : '')
			,array($round_code, $id_edition_stage));
		$results = $query->result();
		foreach ($results as $result)
		{
			$return = true;
		}
		return $return;
	}

	function get_api_data($url) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json; charset=utf-8',
				"x-rapidapi-host: api-football-v1.p.rapidapi.com",
				"x-rapidapi-key: c9eb379b40mshf2207671fe03b16p1c5a42jsn322c0fa5b8a1"
			),
		));

		$json = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		return $json;
	}

	//get action type from api
	function  get_action_type($api_type, $api_detail) {
		$api_type = strtolower($api_type);
		$api_detail = strtolower($api_detail);
		$type = -1;

		if($api_type == 'card') {
			if($api_detail == 'red card') {
				$type = 12;
			}
			else { //yellow card
				$type = 11;
			}
		}
		else if($api_type == 'goal') {
			$type = 1;
		}
		else if($api_type == 'subst') {
			$type = 10;
		}

		return $type;
	}

	//get status code from api status code
	function  get_status_code($api_status) {
		$status = 0;
		switch ($api_status) {
			case 'NS':
			case 'TBD':
				$status = 0;
			break;
			case '1H':
			case '2H':
			case 'INT':
				$status = 1;
				break;
			case 'HT':
				$status = 2;
				break;
			case 'FT':
			case 'AET':
			case 'PEN':
				$status = 3;
				break;
			case 'ET':
				$status = 4;
				break;
			case 'P':
				$status = 5;
				break;
			case 'PST':
				$status = 6;
				break;
		}

		return $status;
	}
}