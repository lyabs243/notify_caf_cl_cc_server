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

	//add top scorers
	public function add_top_scorers($league_id, $id_edition)
	{
		$this->load->model('Edition_model');
		$url = "https://api-football-v1.p.rapidapi.com/v2/topscorers/$league_id";

		$json = $this->get_api_data($url);

		if ($json != null) {
			$json_decode = json_decode($json);
			$scorers = $json_decode->api->topscorers;
			foreach($scorers as $scorer) {
				$data = null;

				$data['id_edition'] = $id_edition;
				$data['api_player_id'] = $scorer->player_id;
				$data['api_team_id'] = $scorer->team_id;
				$data['name'] = $scorer->player_name;
				$data['goals'] = $scorer->goals->total;
				$data['penalty_goals'] = $scorer->penalty->success;

				$this->Edition_model->add_scorer($id_edition, $data);

				echo '<br>' . $data['name'] . '<br>';
			}
		}
	}

	function init_match_from_api($match, $editionStage=0) {
		$data = null;
		$data['api_id'] = $match->fixture_id;
		$data['minute'] = $match->elapsed;
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
		$this->load->model('Notification_model');
		//get all matchs  that date in intervall of 3 minutes before to 3 hour after
		$matchs = $this->Match_model->get_matchs_in_intervall('5 MINUTE', '3 HOUR');

		if(is_array($matchs)) {
			foreach ($matchs as $match) {
				echo $match['teamA'] . 'VS' . $match['teamB'] . ' ' . '<br><br><br>';
				$fixture_api_id = $match['api_id'];

				//get and update match data
				$url = "https://api-football-v1.p.rapidapi.com/v2/fixtures/id/$fixture_api_id";
				$json = $this->get_api_data($url);
				$json_decode = json_decode($json);
				$fixtures = $json_decode->api->fixtures;

				foreach ($fixtures as $fixture) {

					$data = null;
					$data = $this->init_match_from_api($fixture);

					if ($match['status'] != $data['status']) {
						if ($data['status'] == 1) { //match start
							$this->Notification_model->notify_match_start($match['id'], $match['teamA'], $match['teamB']);
						} else if ($data['status'] == 2) { //half time
							$this->Notification_model->notify_match_halftime($match['id'], $match['teamA'], $match['teamB'],
								$match['teamA_goal'], $match['teamB_goal']);
						} else if ($data['status'] == 1) { //full time
							$this->Notification_model->notify_match_fulltime($match['id'], $match['teamA'], $match['teamB'],
								$match['teamA_goal'], $match['teamA_goal']);
						}
					}

					//home team scored
					if ($data['team_a_goal'] > $match['teamA_goal']) {
						$this->Notification_model->notify_match_goal($match['id'], $match['teamA'], $match['teamB'],
							$match['teamA'], $match['teamA_goal'], $match['teamB_goal']);
					}
					else if ($data['team_b_goal'] > $match['teamB_goal']) {
						$this->Notification_model->notify_match_goal($match['id'], $match['teamA'], $match['teamB'],
							$match['teamB'], $match['teamA_goal'], $match['teamB_goal']);
					}
					echo $this->db->update('spt_match', $data, array('api_id' => $data['api_id'])) . ' ' . $data['api_round'] . '<br><br><br>';

					//get match events
					$events = $fixture->events;
					if (is_array($events)) {
						$actions = array();
						$index = 0;
						foreach ($events as $event) {
							$action = null;

							$action['id'] = "$index";
							$action['id_match'] = $match['id'];
							$type = $this->get_action_type($event->type, $event->detail);
							$action['type'] = "$type";
							$action['detail_a'] = ($event->player == null)? 'Unknow...' : $event->player;
							$action['detail_b'] = ($event->assist == null)? '' : $event->assist;
							$action['detail_c'] = '';
							$action['detail_d'] = '';
							$action['teamA_goal'] = $match['teamA_goal'];
							$action['teamB_goal'] = $match['teamB_goal'];
							$action['minute'] = "$event->elapsed";
							$action['api_id_player'] = $event->player_id;
							$action['api_id_player_assist'] = $event->assist_id;
							$action['api_id_team'] = $event->team_id;
							$action['id_team'] = $this->Match_model->get_team_id($action['api_id_team']);

							//gere la position de l action dans la vue(gauche ou droite)
							if ($action['id_team'] == $match['teamAId']) {
								$action['position'] = 0;
							} else {
								$action['position'] = 1;
							}

							$actions[] = $action;
							echo $action['detail_a'] . ' ' . $action['minute'] . '<br>';
							$index++;
						}
						echo $this->Match_model->add_actions_json($match['id'], $actions) . ' ' . $action['minute'] . '<br>';
					}
				}
			}
		}
	}

	//add line ups for matchs
	public function add_matchs_lineup()
	{
		$this->load->model('Match_model');
		$this->load->model('Notification_model');
		//get all matchs  that date in intervall of 3 minutes before to 3 hour after
		$matchs = $this->Match_model->get_matchs_in_intervall('1 HOUR', '2 HOUR');

		if(is_array($matchs)) {
			foreach ($matchs as $match) {
				echo $match['teamA'] . 'VS' . $match['teamB'] . ' ' . '<br><br><br>';
				$fixture_api_id = $match['api_id'];

				//get and update match data
				$url = "https://api-football-v1.p.rapidapi.com/v2/fixtures/id/$fixture_api_id";
				$json = $this->get_api_data($url);
				$json_decode = json_decode($json);
				$fixtures = $json_decode->api->fixtures;

				foreach ($fixtures as $fixture) {

					$data = null;
					$data = $this->init_match_from_api($fixture);

					//get match lineup
					$lineups = $fixture->lineups;
					foreach ($lineups as $lineup) {
						$items = $lineup->startXI;
						$players = array();
						if (is_array($items)) {
							foreach ($items as $item) {
								$player = null;

								$player['id_match'] = $match['id'];
								$player['id_composition'] = $this->Match_model->add_match_composition($match['id']);
								$player['api_id_player'] = $item->player_id;
								$player['api_id_team'] = $item->team_id;
								$player['description'] = $item->player;
								$player['number'] = $item->number;
								$player['position'] = $item->pos;

								$players[] = $player;

							}
						}

						echo $this->Match_model->add_composition_details($players) . ' ' . $player['description'] . '<br>';
					}
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