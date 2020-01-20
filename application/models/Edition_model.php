<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/03/2019
 * Time: 12:33
 */

class Edition_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
    }

    public function get_latest_competition_edition($id_competition) {
        $lastId = 0;
        $query = $this->db->query('SELECT MAX(id) as id FROM spt_competition_edition WHERE id_competition = ?',array($id_competition));
        $results = $query->result();
        foreach ($results as $result)
        {
            $lastId = $result->id;
            break;
        }
        return $lastId;
    }

    public function notify_match_step($idUser,$idCompetition) {
        $lastViewedMatchStep = $this->User_model->get_latest_viewed_match_step($idUser);

        $sql = "SELECT sms.id, sms.id_match, sms.status, sms.action, sms.id_team, sms.id_player, sms.minute,
                st.title as team_name, st.title_small as team_name_small, st.url_logo as url_flag,
                sp.name, sm.id_team_a as teamAId, sm.id_team_b as teamBId, sta.title as teamA, stb.title as teamB, 
                sta.title_small as teamA_small, stb.title_small as teamB_small, sta.url_logo as teamA_logo, stb.url_logo as teamB_logo
                FROM `spt_match_step` sms
                JOIN spt_match sm
                ON sms.id_match = sm.id
                JOIN spt_edition_stage ses 
                ON sm.id_edition_stage = ses.id
                JOIN spt_competition_edition sce
                ON ses.id_edition = sce.id
                JOIN spt_team sta
                ON sm.id_team_a = sta.id
                JOIN spt_team stb
                ON sm.id_team_b = stb.id
                LEFT JOIN spt_team st
                ON sms.id_team = st.id
                LEFT JOIN spt_player sp
                ON sms.id_player = sp.id
                WHERE sms.id > ?
                AND sce.id_competition = ?
                ORDER BY sms.`register_date` DESC
                LIMIT 1";
        $args = array($lastViewedMatchStep, $idCompetition);
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $match_steps = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            if($row['id'] > $lastViewedMatchStep)
            {
                $lastViewedMatchStep = $row['id'];
            }
            $row['id_match'] = $result->id_match;
            $row['status'] = $result->status;
            $row['action'] = $result->action;
            $row['id_team'] = $result->id_team;
            $row['match'] = $this->Match_model->get_match($row['id_match']);
            $row['id_player'] = $result->id_player;
            $row['minute'] = $result->minute;
            $row['team_name'] = $result->team_name;
            $row['team_name_small'] = $result->team_name_small;
            $row['url_flag'] = $result->url_flag;
            $row['name'] = $result->name;
            array_push($match_steps,$row);
        }

        //update user params
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['last_viewed_match_step'] = $lastViewedMatchStep;
        $data['last_connexion'] = date("Y-m-d H:i:s");
        $this->User_model->update_user($idUser,$data);

        return $match_steps;
    }

    public function get_scorers($idCompetition,$page,$idEdition=0) {
        if(!$idEdition)
        {
            $idEdition = $this->get_latest_competition_edition($idCompetition);
        }
        $sql = "SELECT sce.`api_player_id`, sce.`api_team_id`, sce.name, st.title, st.title_small, st.url_logo,
                sce.goals, sce.penalty_goals as goalsPenalty
                FROM `spt_scorers_edition` sce
                JOIN spt_team st 
                ON sce.api_team_id = st.api_id
                WHERE sce.id_edition = ?
                ORDER BY goals DESC
                LIMIT ?,10";
        $page_start = (((int)$page)-1)*10;
        $args = array($idEdition,$page_start);
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $scorers = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id_player'] = $result->api_player_id;
            $row['id_team'] = $result->api_team_id;
            $row['name'] = $result->name;
            $row['url_flag'] = $result->url_logo;
            $row['team_name'] = $result->title;
	        $row['team_name_small'] = $result->title_small;
            if($result->title_small == null) {
	            $row['team_name_small'] = substr($result->title_small, 0, 3);
            }
            $row['goals'] = $result->goals;
            $row['goalsPenalty'] = $result->goalsPenalty;

            array_push($scorers,$row);
        }
        return $scorers;
    }

    public function get_edition_stages($idCompetition,$idEdition=0) {
        if(!$idEdition)
        {
            $idEdition = $this->get_latest_competition_edition($idCompetition);
        }
        $sql = "SELECT ses.id, ses.`id_edition`, ses.`title`, ses.`type`
                FROM `spt_edition_stage` ses
                JOIN spt_competition_edition sce
                ON ses.id_edition = sce.id
                JOIN spt_competition sc
                ON sce.id_competition = sc.id
                WHERE ses.id_edition = ?
                AND sc.id = ?
                ORDER BY ses.register_date DESC";
        $args = array($idEdition,$idCompetition);
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $stages = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            $row['id_edition'] = $result->id_edition;
            $row['title'] = $result->title;
            $row['type'] = $result->type;
            $row['groups'] = array();

            //recupere les group si il y en a
            if($row['type'] == 1)
            {
                $row['groups'] = $this->get_groups($row['id']);
            }

            array_push($stages,$row);
        }
        if(count($results) > 0){
            $data['success'] = '1';
            $data['data'] = $stages;
        }
        else{
            $data['success'] = '0';
        }
        return $data;
    }

    public function get_groups($idEditionStage) {
            $sql = "SELECT  `id`, `id_edition_stage`, `title`
                    FROM `spt_stage_group`
                    WHERE `id_edition_stage` = ?";
        $args = array($idEditionStage);
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $stages = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            $row['id_edition_stage'] = $result->id_edition_stage;
            $row['title'] = $result->title;

            array_push($stages,$row);
        }
        return $stages;
    }

    public function get_group_teams($idStageGroup) {
        $sql = "SELECT stg.`id`, stg.`id_stage_group`, stg.`id_team`, st.title, st.title_small,st.url_logo
                FROM `spt_team_group`  stg
                JOIN spt_team st
                ON st.id = stg.`id_team`
                WHERE stg.`id_stage_group` = ?";
        $args = array($idStageGroup);
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $stages = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            $row['id_stage_group'] = $result->id_stage_group;
            $row['id_team'] = $result->id_team;
            $row['title'] = $result->title;
            $row['title_small'] = ($result->title_small == null)?
	            substr($row['title'], 0, 3) : $result->title_small;
            $row['url_logo'] = $result->url_logo;

            array_push($stages,$row);
        }
        return $stages;
    }

    //order teams in group
    public function order_group_teams($teams) {
        for ($i = 0; $i < count($teams)-1; $i++)
        {
            for($j = $i+1;$j < count($teams); $j++)
            {
                $temp = null;
                //verify points
                if($teams[$j]['points'] > $teams[$i]['points'])
                {
                    $temp = $teams[$j];
                    $teams[$j] = $teams[$i];
                    $teams[$i] = $temp;
                }
                else if($teams[$j]['points'] == $teams[$i]['points'])
                {
                    //if points equals, verify goal diff
                    if($teams[$j]['goal_difference'] > $teams[$i]['goal_difference'])
                    {
                        $temp = $teams[$j];
                        $teams[$j] = $teams[$i];
                        $teams[$i] = $temp;
                    }
                    else if($teams[$j]['goal_difference'] == $teams[$i]['goal_difference'])
                    {
                        //if always equal, verify goal scored
                        if($teams[$j]['scored'] > $teams[$i]['scored'])
                        {
                            $temp = $teams[$j];
                            $teams[$j] = $teams[$i];
                            $teams[$i] = $temp;
                        }
                    }
                }
            }
        }
        return $teams;
    }

}