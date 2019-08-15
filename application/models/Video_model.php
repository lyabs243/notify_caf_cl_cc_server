<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/03/2019
 * Time: 06:49
 */

class Video_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
    }

    public function get_toutube_video_details($idVideo) {
        $url_youtube_api = 'https://www.googleapis.com/youtube/v3/videos?part=snippet&id='.
         $idVideo .'&key=AIzaSyAFpBdW2WqKM-IIYVQ0bTfuDEwnorQ5rUY';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_youtube_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close($ch);
        $json = $server_output;
        $video = json_decode($json, true);

        if(isset($video['items'][0])) {
            $data['title'] = $video['items'][0]['snippet']['title'];
            $data['thumbnails'] = $video['items'][0]['snippet']['thumbnails']['high']['url'];
            $data['channelTitle'] = $video['items'][0]['snippet']['channelTitle'];
        }
        return $data;
    }

    public function get_match_actions($idMatch) {
        $sql = "SELECT sma.`id`, sma.`id_match`, sma.`type`, sma.`detail_a`, sma.`detail_b`, sma.`detail_c`, sma.`detail_d`, sma.`minute`, sma.`id_team`, sma.`id_admin_user`, sma.`register_date`, sm.id_team_a, sm.id_team_b,
                (SELECT COUNT(*) FROM `spt_match_action` WHERE (type = 1 OR type = 2) AND id_team = sm.id_team_a AND id_match = sm.id AND register_date <= sma.register_date) as teamA_goal,
                (SELECT COUNT(*) FROM `spt_match_action` WHERE (type = 1 OR type = 2) AND id_team = sm.id_team_b AND id_match = sm.id AND register_date <= sma.register_date) as teamB_goal
                FROM `spt_match_action` sma
                JOIN spt_match sm
                ON sma.id_match = sm.id
                WHERE sma.id_match = ?
                ORDER BY sma.register_date DESC
                 ";
        $args = array($idMatch);

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
        $sql = "SELECT scd.`id_player`, scd.`id_team`, scd.`description`, sp.name, spe.num_player
                FROM `spt_composition_detail` scd
                LEFT JOIN spt_player sp
                ON scd.id_player = sp.id
                JOIN spt_player_edition spe
                ON sp.id = spe.id_player
                WHERE scd.id_match = ?
                AND scd.id_team = ?";
        $args = array($idMatch,$idTeam);

        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $lineup = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id_player'] = $result->id_player;
            $row['id_team'] = $result->id_team;
            $row['description'] = $result->description;
            $row['name'] = $result->name;
            $row['num_player'] = $result->num_player;

            array_push($lineup,$row);
        }

        return $lineup;
    }

    public function add_comment($data) {
        // insertion dans la table
        $this->db->insert('spt_comment', $data);
        return $this->db->insert_id();
    }

    public function get_match_comments($idMatch,$page=1) {
        $sql = "SELECT sc.`id_user`, sc.`comment`, sc.`register_date`, s.full_name, s.url_profil_pic, s.id_account_user
                FROM `spt_comment` sc
                JOIN subscriber s
                ON sc.id_user = s.id_user
                WHERE sc.id_match = ?
                ORDER BY sc.register_date DESC 
                LIMIT ?,10";
        $page_start = (((int)$page)-1)*10;
        $args = array($idMatch,$page_start);

        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $comments = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id_user'] = $result->id_user;
            $row['id_account_user'] = $result->id_account_user;
            $row['comment'] = $result->comment;
            $row['register_date'] = $result->register_date;
            $row['full_name'] = $result->full_name;
            $row['url_profil_pic'] = $result->url_profil_pic;

            array_push($comments,$row);
        }

        return $comments;
    }

    public function get_match_video($idMatch) {
        $sql = "SELECT sc.`youtube_video_id`
                FROM `spt_video` sv
                WHERE sv.id_match = ?
                ORDER BY sc.register_date DESC 
                LIMIT ?,10";
        $args = array($idMatch);

        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $video = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['youtube_video'] = $result->youtube_video_id;

            array_push($video,$row);
            break;
        }

        return $video;
    }

    public function get_latest_results($idCompetition,$idEdition=0,$page=0) {
        //si l edition n est pas specifie on prend la derniere edition
        if($idEdition<=0) {
            $idEdition = $this->Edition_model->get_latest_competition_edition($idCompetition);
        }
        $sql = "SELECT sm.id, sta.id as teamAId, stb.id as teamBId, sta.title as teamA, stb.title as teamB, sta.url_logo as teamA_logo, stb.url_logo as teamB_logo,
                (SELECT COUNT(*) FROM `spt_match_action` WHERE (type = 1 OR type = 2) AND id_team = sta.id AND id_match = sm.id) as teamA_goal,
                (SELECT COUNT(*) FROM `spt_match_action` WHERE (type = 1 OR type = 2) AND id_team = stb.id AND id_match = sm.id) as teamB_goal,
                sta.title_small as teamA_small, stb.title_small as teamB_small, sm.match_date, sm.status
                FROM spt_match sm
                JOIN spt_team sta 
                ON sm.id_team_a = sta.id
                JOIN spt_team stb
                ON sm.id_team_b = stb.id
                JOIN spt_edition_stage ses 
                ON ses.id = sm.id_edition_stage
                JOIN spt_competition_edition sce 
                ON sce.id = ses.id_edition
                WHERE (sm.status = 3)
                AND ses.id_edition = ?
                ORDER BY sm.match_date DESC
                 ";

        if($page <= 0) {
            $sql .= "
                    LIMIT 5";
            $args = array($idEdition);
        }
        else {
            $page_start = (((int)$page)-1)*10;
            $args = array($idEdition,$page_start);
            $sql .= "
                     LIMIT ?,10";
        }
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $matchs = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            $row['teamAId'] = $result->teamAId;
            $row['teamBId'] = $result->teamBId;
            $row['teamA'] = $result->teamA;
            $row['teamB'] = $result->teamB;
            $row['teamA_small'] = $result->teamA_small;
            $row['teamB_small'] = $result->teamB_small;
            $row['teamA_logo'] = $result->teamA_logo;
            $row['teamB_logo'] = $result->teamB_logo;
            $row['teamA_goal'] = $result->teamA_goal;
            $row['teamB_goal'] = $result->teamB_goal;
            $row['match_date'] = $result->match_date;
            $row['status'] = $result->status;

            array_push($matchs,$row);
        }

        return $matchs;
    }
}