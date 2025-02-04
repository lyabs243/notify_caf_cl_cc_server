<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/03/2019
 * Time: 06:49
 */

class Match_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
	    $this->load->model('Notification_model');
    }

    public function set_status($idMatch,$status) {
        $sql = "UPDATE spt_match SET status = ?
                WHERE id = ? 
                 ";

        $args = array($status,$idMatch);
        $result = $this->db->query($sql,$args);
        return $result;
    }

    public function add_action($data) {
    	if(!$this->is_match_action_exist($data)) {
		    $this->db->insert('spt_match_action', $data);
		    //notify if it is a goal
		    if($data['type'] == 1) {
		    	$match = $this->get_match($data['id_match']);
			    $scoredTeam = $this->get_team_name($data['api_id_team']);
			    $this->Notification_model->notify_match_goal($data['id_match'], $match[0]['teamA'], $match[0]['teamB'],
				    $scoredTeam, $match[0]['teamA_goal'], $match[0]['teamB_goal'], $match[0]['competition']['country_code'],
				    $match[0]['competition']['title'], $match[0]['edition_stage']['title']);
		    }
		    return $this->db->insert_id();
	    }
    }

    public function add_actions_json($idMatch, $actions) {
    	$data['id_match'] = $idMatch;
	    $data['actions_json'] = json_encode($actions);
	    if(!$this->is_match_actions_json_exist($idMatch)) {
		    $this->db->insert('spt_match_action', $data);
		    return $this->db->insert_id();
	    }
	    else {
		    return $this->db->update('spt_match_action', $data, array('id_match' => $idMatch));
	    }
    }

    public function add_match_video($id_match, $youtube_id) {
    	$data['id_match'] = $id_match;
    	$data['youtube_video_id'] = $youtube_id;
	    if (!$this->is_match_video_exist($id_match)) {
		    $match = $this->get_match($id_match);
		    $this->Notification_model->notify_match_video($id_match, $match[0]['teamA'], $match[0]['teamB']);
		    return $this->db->insert('spt_match_video', $data);
	    }
	    else { //update in database
		    return $this->db->update('spt_match_video', $data, array('id_match' => $id_match));
	    }
    }

	function  is_match_video_exist($id_match)
	{
		$id = 0;
		$query = $this->db->query('SELECT * FROM spt_match_video WHERE id_match = ?',array($id_match));
		$results = $query->result();
		foreach ($results as $result)
		{
			$id = $result->id;
			break;
		}
		return $id;
	}

	//verifie si la composition d un match existe deja
	function  is_match_action_exist($data)
	{
		$id = 0;
		$query = $this->db->query('SELECT * FROM spt_match_action WHERE id_match = ? AND type = ? AND minute = ? 
		AND api_id_team = ?',array($data['id_match'], $data['type'], $data['minute'], $data['api_id_team']));
		$results = $query->result();
		foreach ($results as $result)
		{
			$id = $result->id;
			break;
		}
		return $id;
	}

	function  is_match_actions_json_exist($idMatch)
	{
		$id = 0;
		$query = $this->db->query('SELECT * FROM spt_match_action WHERE id_match = ? AND actions_json is not null',array($idMatch));
		$results = $query->result();
		foreach ($results as $result)
		{
			$id = $result->id;
			break;
		}
		return $id;
	}

	//get team name from team api id
	function  get_team_name($api_id)
	{
		$title = '';
		$query = $this->db->query('SELECT title FROM spt_team WHERE api_id = ?',array($api_id));
		$results = $query->result();
		foreach ($results as $result)
		{
			$title = $result->title;
			break;
		}
		return $title;
	}

	function  get_team_id($api_id)
	{
		$id = 0;
		$query = $this->db->query('SELECT id FROM spt_team WHERE api_id = ?',array($api_id));
		$results = $query->result();
		foreach ($results as $result)
		{
			$id = $result->id;
			break;
		}
		return $id;
	}

	function  is_composition_detail_exist($data)
	{
		$id = 0;
		$query = $this->db->query('SELECT * FROM spt_composition_detail WHERE id_match = ? AND id_composition = ? AND description = ? 
		AND api_id_team = ?',array($data['id_match'], $data['id_composition'], $data['description'], $data['api_id_team']));
		$results = $query->result();
		foreach ($results as $result)
		{
			$id = $result->id;
			break;
		}
		return $id;
	}

    //verifie si la composition d un match existe deja
    function  is_composition_exist($idMatch)
    {
        $id = 0;
        $query = $this->db->query('SELECT * FROM spt_composition WHERE id_match = ?',array($idMatch));
        $results = $query->result();
        foreach ($results as $result)
        {
            $id = $result->id;
            break;
        }
        return $id;
    }

	function  is_match_exist($apiId)
	{
		$id = 0;
		$query = $this->db->query('SELECT * FROM spt_match WHERE api_id = ?',array($apiId));
		$results = $query->result();
		foreach ($results as $result)
		{
			$id = $result->id;
			break;
		}
		return $id;
	}

    public function add_match_composition($idMatch) {
        $id = $this->is_composition_exist($idMatch);
        if($id <= 0)
        {
            $this->db->insert('spt_composition', array('id_match' => $idMatch));
            $id = $this->db->insert_id();
            $match = $this->get_match($idMatch);
	        $this->Notification_model->notify_match_lineup($idMatch, $match[0]['teamA'], $match[0]['teamB'], $match[0]['competition']['country_code']);
        }

        return $id;
    }

    public function add_composition_details($players) {
        $result = false;
        foreach ($players as $player)
        {
        	if(!$this->is_composition_detail_exist($player)) {
		        $this->db->insert('spt_composition_detail', $player);
		        $result = true;
	        }
        }
        $this->tweet_composition_details($players);
        return $result;
    }

    function tweet_composition_details($players) {
		if(count($players) > 0) {
			$match = $this->get_match($players[0]['id_match']);
			$team = $this->get_team_name($players[0]['api_id_team']);
			$competition = $match[0]['competition']['title'];
			$competitionStage = $match[0]['edition_stage']['title'];
			$teamA = $match[0]['teamA'];
			$teamB = $match[0]['teamB'];

			$heading = "
			Starting XI for $team ";

			$player0 = (strlen($players[0]['description']) > 15)? substr($players[0]['description'], 0, 15) . '.'
				: $players[0]['description'];
			$player1 = (strlen($players[1]['description']) > 15)? substr($players[1]['description'], 0, 15) . '.'
				: $players[1]['description'];
			$player2 = (strlen($players[2]['description']) > 15)? substr($players[2]['description'], 0, 15) . '.'
				: $players[2]['description'];
			$player3 = (strlen($players[3]['description']) > 15)? substr($players[3]['description'], 0, 15) . '.'
				: $players[3]['description'];
			$player4 = (strlen($players[4]['description']) > 15)? substr($players[4]['description'], 0, 15) . '.'
				: $players[4]['description'];
			$player5 = (strlen($players[5]['description']) > 15)? substr($players[5]['description'], 0, 15) . '.'
				: $players[5]['description'];
			$player6 = (strlen($players[6]['description']) > 15)? substr($players[6]['description'], 0, 15) . '.'
				: $players[6]['description'];
			$player7 = (strlen($players[7]['description']) > 15)? substr($players[7]['description'], 0, 15) . '.'
				: $players[7]['description'];
			$player8 = (strlen($players[8]['description']) > 15)? substr($players[8]['description'], 0, 15) . '.'
				: $players[8]['description'];
			$player9 = (strlen($players[9]['description']) > 15)? substr($players[9]['description'], 0, 15) . '.'
				: $players[9]['description'];
			$player10 = (strlen($players[10]['description']) > 15)? substr($players[10]['description'], 0, 15) . '.'
				: $players[10]['description'];

			$content = "
			- $player0
			- $player1
			- $player2
			- $player3
			- $player4
			- $player5
			- $player6
			- $player7
			- $player8
			- $player9
			- $player10
			";

			$this->Notification_model->tweetMatch($heading, $content, $competition, $competitionStage, $teamA, $teamB);
		}
    }

    public function get_player_name($idPlayer) {
        $sql = "SELECT id, name 
                FROM `spt_player`
                WHERE id = ?";
        $args = array($idPlayer);
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $name = '';
        foreach ($results as $result)
        {
            $name = $result->name;
        }
        return $name;
    }

    private function get_query_match_header(){
        $query = "SELECT sm.id, sm.minute, sm.api_id, sm.api_round, sm.api_update, sta.id as teamAId, stb.id as teamBId, sta.title as teamA, stb.title as teamB,
 				sta.url_logo as teamA_logo, stb.url_logo as teamB_logo, sm.team_a_goal as teamA_goal, sm.team_b_goal as teamB_goal,
                sta.title_small as teamA_small, stb.title_small as teamB_small, CONVERT_TZ(sm.match_date,@@session.time_zone,?) as match_date, sm.status,
                sm.team_a_penalty, sm.team_b_penalty, sc.id as comp_id, sc.title as comp_title, sc.title_small as comp_title_small,
                sc.description as comp_description, sc.trophy_icon_url as comp_trophy_icon_url, sc.category as comp_category, sc.country_code as comp_country_code, sc.register_date as comp_register_date,
                ses.id as editstage_id, ses.id_edition as editstage_id_edition, ses.title as editstage_title, ses.type as editstage_type, ses.register_date as editstage_register_date,
                (SELECT stg.id_stage_group 
                FROM `spt_team_group` stg
                JOIN spt_stage_group ssg
                ON stg.`id_stage_group` = ssg.id
                WHERE ssg.id_edition_stage = sm.id_edition_stage
                AND stg.id_team = teamAId) as idGroupA,
                (SELECT stg.id_stage_group 
                FROM `spt_team_group` stg
                JOIN spt_stage_group ssg
                ON stg.`id_stage_group` = ssg.id
                WHERE ssg.id_edition_stage = sm.id_edition_stage
                AND stg.id_team = teamBId) as idGroupB
                FROM spt_match sm
                JOIN spt_team sta 
                ON sm.id_team_a = sta.id
                JOIN spt_team stb
                ON sm.id_team_b = stb.id
                JOIN spt_edition_stage ses 
                ON ses.id = sm.id_edition_stage
                JOIN spt_competition_edition sce 
                ON sce.id = ses.id_edition 
                JOIN spt_competition sc 
                ON sc.id = sce.id_competition ";

        return $query;
    }

    private function get_match_array_from_result($results, $getAction = false){
        $matchs = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
	        $row['api_id'] = $result->api_id;
	        $row['api_round'] = $result->api_round;
	        $row['api_update'] = $result->api_update;
            $row['teamAId'] = $result->teamAId;
            $row['teamBId'] = $result->teamBId;
            $row['teamA_small'] = $result->teamA_small;
            $row['teamB_small'] = $result->teamB_small;
            $row['teamA'] = $result->teamA;
            $row['teamB'] = $result->teamB;
            $row['teamA_logo'] = $result->teamA_logo;
            $row['teamB_logo'] = $result->teamB_logo;
            
            $row['teamA_goal'] = $result->teamA_goal;
            $row['teamB_goal'] = $result->teamB_goal;
            $row['team_a_penalty'] = $result->team_a_penalty;
            $row['team_b_penalty'] = $result->team_b_penalty;
            $row['match_date'] = $result->match_date;
            $row['status'] = $result->status;
            $row['idGroupA'] = $result->idGroupA;
            $row['idGroupB'] = $result->idGroupB;

            if($getAction) {
	            $row['actions'] = $this->get_match_actions_json($result->id);
            }

            $row['edition_stage']['id'] = $result->editstage_id;
            $row['edition_stage']['id_edition'] = $result->editstage_id_edition;
            $row['edition_stage']['title'] = $result->editstage_title;
            $row['edition_stage']['type'] = $result->editstage_type;
            $row['edition_stage']['register_date'] = $result->editstage_register_date;

            $row['competition']['id'] = $result->comp_id;
            $row['competition']['title_small'] = $result->comp_title_small;
            $row['competition']['title'] = (strlen(strval($this->lang->line($row['competition']['title_small']))) == 0)?
											$result->comp_title:
											strval($this->lang->line($row['competition']['title_small']));
            $row['competition']['description'] = $result->comp_description;
            $row['competition']['trophy_icon_url'] = $result->comp_trophy_icon_url;
            $row['competition']['country_code'] = $result->comp_country_code;
	        $row['competition']['register_date'] = $result->comp_register_date;
            //on change l affichage de la date du match par rapport au status
            $row['match_status'] = strval($this->getMatchDate($row['id'],$row['status'],$row['match_date'], $result->minute));

            array_push($matchs,$row);
        }
        return $matchs;
    }

	public function update_match($id, $data) {
		return $this->db->update('spt_match', $data, array('id' => $id));
	}

	public function update_match_date($id, $matchDate) {
		$timezone = '+00:00';
		$result = $this->db->query('UPDATE spt_match
 						  set match_date = CONVERT_TZ(?,@@session.time_zone,?)
 						  WHERE id = ? ', array($matchDate, $timezone, $id));
		return $result;
	}

    public function get_current_matchs($idCompetition,$idEdition=0,$page=0,$idCompetitionType=0) {
        $timezone = $this->session->timezone;
        //si l edition n est pas specifie on prend la derniere edition
        if($idEdition<=0) {
            $idEdition = $this->Edition_model->get_latest_competition_edition($idCompetition);
        }
        $sql = $this->get_query_match_header() . "
                WHERE (sm.status <> 6
                AND sm.status <> 0
                AND sm.status <> 11
                AND sm.status <> 7
                AND sm.status <> 3)
                 AND ses.visible > 0 ";
        if($idCompetitionType > 0) {
            $sql .= "AND sc.category = ? ";
            $idArg2 = $idCompetitionType;
        }
        else {
            $sql .= "AND ses.id_edition = ? ";
            $idArg2 = $idEdition;
        }

        $sql .= "ORDER BY sm.match_date ASC
                 ";

        if($page <= 0) {
            $sql .= "
                    LIMIT 2";
            $args = array($timezone,$idArg2);
        }
        else {
            $page_start = (((int)$page)-1)*10;
            $args = array($timezone,$idArg2,$page_start);
            $sql .= "
                     LIMIT ?,10";
        }
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $matchs = $this->get_match_array_from_result($results);

        return $matchs;
    }

    public function get_latest_results($idCompetition,$idEdition=0,$page=0,$idCompetitionType=0) {
        $timezone = $this->session->timezone;
        //si l edition n est pas specifie on prend la derniere edition
        if($idEdition<=0) {
            $idEdition = $this->Edition_model->get_latest_competition_edition($idCompetition);
        }
        $sql = $this->get_query_match_header() . "
                WHERE (sm.status = 3)
                AND ses.visible > 0
                 ";
        if($idCompetitionType > 0) {
            $sql .= "AND sc.category = ? ";
            $idArg2 = $idCompetitionType;
        }
        else {
            $sql .= "AND ses.id_edition = ? ";
            $idArg2 = $idEdition;
        }

        $sql .= "ORDER BY sm.match_date DESC ";

        if($page <= 0) {
            $sql .= "
                    LIMIT 2";
            $args = array($timezone,$idArg2);
        }
        else {
            $page_start = (((int)$page)-1)*10;
            $args = array($timezone,$idArg2,$page_start);
            $sql .= "
                     LIMIT ?,10";
        }
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $matchs = $this->get_match_array_from_result($results);

        return $matchs;
    }

	/**
	 * Get postponed and matchs to define and old matchs that does not have results
	 *
	 * @param $idCompetition
	 * @param int $idEdition
	 * @param int $page
	 * @param int $idCompetitionType
	 * @return array
	 */
	public function get_other_fixtures($idCompetition,$idEdition=0,$page=0,$idCompetitionType=0) {
		$timezone = $this->session->timezone;
		//si l edition n est pas specifie on prend la derniere edition
		if($idEdition<=0) {
			$idEdition = $this->Edition_model->get_latest_competition_edition($idCompetition);
		}
		$sql = $this->get_query_match_header() . "
                WHERE (sm.status = 7 OR sm.status = 6 OR (sm.status <> 3 AND sm.match_date < DATE(NOW())))
                AND ses.visible > 0
                 ";
		if($idCompetitionType > 0) {
			$sql .= "AND sc.category = ? ";
			$idArg2 = $idCompetitionType;
		}
		else {
			$sql .= "AND ses.id_edition = ? ";
			$idArg2 = $idEdition;
		}

		$sql .= "ORDER BY sm.match_date DESC ";

		$page_start = (((int)$page)-1)*10;
		$args = array($timezone,$idArg2,$page_start);
		$sql .= " LIMIT ?,10";

		$query = $this->db->query($sql,$args);
		$results = $query->result();
		$matchs = $this->get_match_array_from_result($results);

		return $matchs;
	}

    public function get_fixture($idCompetition,$idEdition=0,$page=0,$idCompetitionType=0) {
        $timezone = $this->session->timezone;
        //si l edition n est pas specifie on prend la derniere edition
        if($idEdition<=0) {
            $idEdition = $this->Edition_model->get_latest_competition_edition($idCompetition);
        }
        $sql = $this->get_query_match_header() . "
                WHERE (sm.status = 0)
                AND ses.visible > 0
                AND sm.match_date >= DATE(NOW())
                 ";

        if($idCompetitionType > 0) {
            $sql .= "AND sc.category = ? ";
            $idArg2 = $idCompetitionType;
        }
        else {
            $sql .= "AND ses.id_edition = ? ";
            $idArg2 = $idEdition;
        }

        $sql .= "ORDER BY sm.match_date ASC ";

        if($page <= 0) {
            $sql .= "
                    LIMIT 2";
            $args = array($timezone,$idArg2);
        }
        else {
            $page_start = (((int)$page)-1)*10;
            $args = array($timezone,$idArg2,$page_start);
            $sql .= "
                     LIMIT ?,10";
        }
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $matchs = $this->get_match_array_from_result($results);

        return $matchs;
    }

    public function get_group_matchs_fixtures($idCompetition,$idEditionStage,$idGroup=0,$page=0) {
        $timezone = $this->session->timezone;
        $sql = $this->get_query_match_header();

        $page_start = (((int)$page)-1)*10;
        if($idGroup > 0)
        {
            $sql.=" JOIN  spt_team_group stg
                ON sta.id = stg.id_team ";
        }
        $sql.=" WHERE (sm.status = 0)
                AND ses.visible > 0
                AND sm.match_date >= DATE(NOW())
                AND ses.id = ? ";
        if($idGroup > 0)
        {
            $sql.=" AND stg.id_stage_group = ? ";
            $args = array($timezone,$idEditionStage,$idGroup,$page_start);
        }
        else
        {
            $args = array($timezone,$idEditionStage,$page_start);
        }
        $sql.=" ORDER BY sm.match_date ASC 
                     LIMIT ?,10 ";
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $matchs = $this->get_match_array_from_result($results);

        return $matchs;
    }

    public function get_group_matchs_results($idCompetition,$idEditionStage,$idGroup=0,$page=0) {
        $timezone = $this->session->timezone;
        $sql = $this->get_query_match_header();

        $page_start = (((int)$page)-1)*10;

        if($idGroup > 0)
        {
            $sql.=" JOIN  spt_team_group stg
                ON sta.id = stg.id_team ";
        }
        $sql .=" WHERE (sm.status = 3)
                AND ses.visible > 0
                AND ses.id = ? ";
        if($idGroup > 0)
        {
            $sql.=" AND stg.id_stage_group = ? ";
            $args = array($timezone,$idEditionStage,$idGroup,$page_start);
        }
        else
        {
            $args = array($timezone,$idEditionStage,$page_start);
        }
        $sql.=" ORDER BY sm.match_date DESC
                     LIMIT ?,10 ";
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $matchs = $this->get_match_array_from_result($results);

        return $matchs;
    }

	public function get_match_actions_json($idMatch) {
		$sql = "SELECT actions_json 
				FROM `spt_match_action` sma
                WHERE sma.id_match = ?
                 ";
		$args = array($idMatch);

		$query = $this->db->query($sql,$args);
		$results = $query->result();
		$actions = array();
		foreach ($results as $result)
		{
			if($result->actions_json != null) {
				$actions = json_decode($result->actions_json);
			}
		}

		return array_reverse($actions);
	}

    public function get_match_actions($idMatch,$actionMin=0) {
        $sql = "SELECT sma.`id`, sma.`id_match`, sma.`type`, sma.`detail_a`, sma.`detail_b`, sma.`detail_c`, sma.`detail_d`, sma.`minute`, st.`id` as id_team, sma.`id_admin_user`, sma.`register_date`, sm.id_team_a, sm.id_team_b,
                sm.team_a_goal as teamA_goal, sm.team_b_goal as teamB_goal
                FROM `spt_match_action` sma
                JOIN spt_match sm
                ON sma.id_match = sm.id
                JOIN spt_team st 
                ON sma.api_id_team = st.api_id
                WHERE sma.id_match = ?
                AND sma.id > ?
                ORDER BY minute DESC
                 ";
        $args = array($idMatch,$actionMin);

        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $actions = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            $row['id_match'] = $result->id_match;
            $row['type'] = $result->type;
            $row['detail_a'] = ($result->detail_a == null)? '' : $result->detail_a;
            $row['detail_b'] = ($result->detail_b == null)? '' : $result->detail_b;
            $row['detail_c'] = ($result->detail_c == null)? '' : $result->detail_c;
            $row['detail_d'] = ($result->detail_d == null)? '' : $result->detail_d;
            $row['teamA_goal'] = $result->teamA_goal;
            $row['teamB_goal'] = $result->teamB_goal;
            $row['minute'] = $result->minute;
            $row['id_team'] = $result->id_team;

            //gere la position de l action dans la vue(gauche ou droite)
            if($result->id_team == $result->id_team_a)
            {
                $row['position'] = 0;
            }
            else
            {
                $row['position'] = 1;
            }

            array_push($actions,$row);
        }

        return $actions;
    }

    public function get_match_lineup($idMatch,$idTeam) {
        $sql = "SELECT scd.`api_id_player`, st.`id` as id_team, scd.`description`, scd.description as name, scd.number as num_player
                FROM `spt_composition_detail` scd
                JOIN  spt_team st 
                ON st.api_id = scd.api_id_team
                WHERE scd.id_match = ?
                AND st.id = ?";
        $args = array($idMatch,$idTeam);

        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $lineup = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id_player'] = $result->api_id_player;
            $row['id_team'] = $result->id_team;
            $row['description'] = $result->description;
            $row['name'] = $result->name;
            $row['num_player'] = $result->num_player;

            array_push($lineup,$row);
        }

        return $lineup;
    }

    public function get_team_players($idCompetition,$idEdition=0,$idTeam) {
        if($idEdition<=0) {
            $idEdition = $this->Edition_model->get_latest_competition_edition($idCompetition);
        }
        $sql = "SELECT spt.`id_player`, spt.`id_team`, sp.name, spe.num_player
                FROM `spt_player_team` spt
                JOIN spt_player sp
                ON spt.id_player = sp.id
                JOIN spt_player_edition spe
                ON sp.id = spe.id_player
                WHERE spt.id_team = ?
                AND spe.id_edition = ? ";
        $args = array($idTeam,$idEdition);

        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $lineup = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id_player'] = $result->id_player;
            $row['id_team'] = $result->id_team;
            $row['description'] = $result->name;
            $row['name'] = $result->name;
            $row['num_player'] = $result->num_player;

            array_push($lineup,$row);
        }

        return $lineup;
    }

    public function get_match_comments($idMatch,$page=1,$idCommentMin=0) {
	    $this->load->model('Subscriber_model');
	    $timezone = $this->session->timezone;
        $sql = "SELECT sc.id,sc.`id_user`, sc.`comment`, CONVERT_TZ(sc.`register_date`,@@session.time_zone,?) as register_date,
 				s.full_name, s.url_profil_pic, s.id_account_user, s.id as id_subscriber
                FROM `spt_comment` sc
                JOIN subscriber s
                ON sc.id_user = s.id_user
                WHERE sc.id_match = ?
                AND sc.id > ?
                ORDER BY sc.register_date DESC 
                LIMIT ?,10";
        $page_start = (((int)$page)-1)*10;
        $args = array($timezone, $idMatch, $idCommentMin, $page_start);

        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $comments = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
	        $row['id_match'] = $idMatch;
            $row['id_user'] = $result->id_user;
            $row['comment'] = $result->comment;
            $row['subscriber'] = $this->Subscriber_model->get($result->id_subscriber);
            $row['register_date'] = $result->register_date;

            array_push($comments,$row);
        }

        return $comments;
    }

    public function get_match_video($idMatch) {
        $sql = "SELECT smv.`youtube_video_id`
                FROM `spt_match_video` smv
                WHERE smv.id_match = ?
                ORDER BY smv.register_date DESC ";
        $args = array($idMatch);

        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $video = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['youtube_video'] = $result->youtube_video_id;
            $youtube_details = $this->Video_model->get_toutube_video_details($row['youtube_video']);
            $row['title'] = '';
            $row['channelTitle'] = '';
            $row['thumbnails'] = '';
            if(count($youtube_details)) {
                $row['title'] = $youtube_details['title'];
                $row['channelTitle'] = $youtube_details['channelTitle'];
                $row['thumbnails'] = $youtube_details['thumbnails'];
            }
            array_push($video,$row);
            break;
        }

        return $video;
    }

    //get all matchs of a team in a group
    public function get_matchs_group_teams($idTeam,$idEditionStage) {
        $timezone = $this->session->timezone;
        $sql = $this->get_query_match_header() . "
                WHERE (sm.status = 3 OR sm.status = 1)
                AND (sta.id = ? OR stb.id = ?)
                AND ses.id = ?
                ORDER BY sm.match_date DESC";
        $args = array($timezone,$idTeam,$idTeam,$idEditionStage);
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $matchs = $this->get_match_array_from_result($results);
        return $matchs;
    }

    public function get_match($idMatch) {
	    $this->load->model('Api_football_model');

	    if($this->Api_football_model->can_request_api_call('30 SECOND')) {
		    $this->Api_football_model->add_matchs_actions(false);
	    }

	    $timezone = $this->session->timezone;
        $sql = $this->get_query_match_header() . "
                WHERE sm.id = ?";
        $args = array($timezone,$idMatch);
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $matchs = $this->get_match_array_from_result($results, true);

        return $matchs;
    }

	/**
	 * Get all matchs in specific intervall of date start and not finished matchs
	 * @param $idMatch
	 * @return array
	 */
	public function get_matchs_in_intervall($intervallStart, $intervallEnd, $lineUp=0) {
		$timezone = $this->session->timezone;
		$sql = $this->get_query_match_header() . "
                WHERE NOW() >= DATE_SUB(sm.match_date, INTERVAL $intervallStart) 
                AND NOW() <= DATE_ADD(sm.match_date , INTERVAL $intervallEnd) 
                AND sc.live_score = 1
                AND sm.status <> 3 
                ";
		$args = array($timezone);
		$query = $this->db->query($sql,$args);
		$results = $query->result();
		$matchs = $this->get_match_array_from_result($results);

		//return match only if composition is not available
		if($lineUp) {
			$filters = array();
			foreach ($matchs as $match) {
				if(!$this->is_composition_exist($match['id'])) {
					$filters[] = $match;
				}
			}
			$matchs = $filters;
		}

		return $matchs;
	}

    private function getMatchDate($idMatch,$status,$date,$minute)
    {
        $date = strtotime($date);
        $result = $date;
        //le match n a paa encore commence
        if($status == 0)
        {
            //si la date du match c est la date d aujourd hui
            if(date($this->lang->line('date')) == date($this->lang->line('date'),$date))
            {
                $result = date($this->lang->line('time'),$date);
            }
            else
            {
                $result = date($this->lang->line('date_time'),$date);
            }
        }
        //le match est en cour
        elseif ($status == 1)
        {
            $result = $minute . '\'';
        }
        //la mi temps
        elseif ($status == 2)
        {
            $result = $this->lang->line('half_time');
        }
        //fin du match
        elseif ($status == 3)
        {
            $result = $this->lang->line('full_time');
        }
        //prolongation
        elseif ($status == 4)
        {
            $result = $this->lang->line('extra_time');
        }
        //seance de tirs au but
        elseif ($status == 5)
        {
            $result = $this->lang->line('penalty_kick');
        }
        //match reporte
        elseif ($status == 6)
        {
            $result = $this->lang->line('postponed');
        }
        //to define manually
        elseif ($status == 7)
        {
            $result = $this->lang->line('to_define_manually');
        }

        return $result;
    }

    private function get_match_status_date($idMatch,$status) {
        $sql = "SELECT register_date
                FROM `spt_match_step`
                WHERE id_match = ?
                AND status = ? ";
        $args = array($idMatch,$status);

        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $date = date('Y-m-d H:i');
        foreach ($results as $result)
        {
            $date= $result->register_date;
            break;
        }

        return $date;
    }

    private function get_current_time()
    {
        $sql = "SELECT NOW() as time FROM DUAL";
        $args = array();

        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $date = date('Y-m-d H:i:s');
        foreach ($results as $result)
        {
            $date= $result->time;
            break;
        }

        return $date;
    }
}