<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'controllers/NotifyController.php';
class Competition extends NotifyController {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    function __construct()
    {
        // this is your constructor
        parent::__construct();
        $this->load->model('Article_model');
        $this->load->model('User_model');
        $this->load->model('Edition_model');
        $this->load->model('Match_model');
        $this->load->model('Competition_model');

        $lang = $access = $this->session->lang;
        $language = 'english';
        if($lang == 'fr')
            $language = 'french';

        $this->lang->load(array('spt_competitions','date_format','spt_match_status'),
            $language);
    }

    public function index()
    {
        $this->load->view('welcome_message');
    }

    public function add_pendding_approval_news($idCompetition)
    {
        $this->Article_model->competion_add_news_pending_approval($idCompetition);
    }

    public function add_news($idCompetition)
    {
        $this->Article_model->competion_add_news($idCompetition);
    }

    public function home_infos($idUser,$idCompetition,$idCompetitionType=0)
    {
        $lang = $access = $this->session->lang;
        $result['trending_news'] = $this->Article_model->get_latest_news($idUser, 0, 0, 0, $idCompetitionType, $lang);
        $result['current_match'] = $this->Match_model->get_current_matchs($idCompetition,0,0,$idCompetitionType);
        $result['fixture'] = $this->Match_model->get_fixture($idCompetition,0,0,$idCompetitionType);
        $result['latest_result'] = $this->Match_model->get_latest_results($idCompetition,0,0,$idCompetitionType);
	    $result['featured_competition'] = $this->Competition_model->get_featured_competitions($idCompetitionType);

        $news['NOTIFYGROUP'] = $result;

        //update user params
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['last_connexion'] = date("Y-m-d H:i:s");
        $this->User_model->update_user($idUser,$data);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function get($idCompetition)
    {
        $data = $this->Competition_model->get_competition($idCompetition);

        if(count($data)){
            $result['NOTIFYGROUP'][] = array('success' => '1','data' => $data);
        }
        else{
            $result['NOTIFYGROUP'][] = array('success' => '0');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($result,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function get_all($category=0,$page=1)
    {
        $data = $this->Competition_model->get_competitions($category,$page);

        if(count($data)){
            $result['NOTIFYGROUP'][] = array('success' => '1','data' => $data);
        }
        else{
            $result['NOTIFYGROUP'][] = array('success' => '0');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($result,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function scorers_edition($idCompetition,$page)
    {
        $data = $this->Edition_model->get_scorers($idCompetition,$page);

        if(count($data)){
            $result['NOTIFYGROUP'] = array('success' => '1','data' => $data);
        }
        else{
            $result['NOTIFYGROUP'] = array('success' => '0');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($result,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function groups($idEditionStage)
    {
        $news['NOTIFYGROUP'] = $this->Edition_model->get_groups($idEditionStage);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function group_details($idStageGroup,$idEditionStage)
    {
        $teams = $this->Edition_model->get_group_teams($idStageGroup);
        $result = array();
        foreach ($teams as $team)
        {
            //get all matchs of a team in a group and calculate points
            $matchs = $this->Match_model->get_matchs_group_teams($team['id_team'],$idEditionStage);

            $matchs_played = 0;
            $points = 0;
            $win = 0;
            $draw = 0;
            $lose = 0;
            $scored = 0;
            $conceded = 0;
            $goal_difference = 0;

            foreach ($matchs as $match)
            {
                $matchs_played++;
                if($team['id_team'] == $match['teamAId'])
                {
                    $matchScored = $match['teamA_goal'];
                    $matchConceded = $match['teamB_goal'];
                }
                else
                {
                    $matchScored = $match['teamB_goal'];
                    $matchConceded = $match['teamA_goal'];
                }
                //verifie si il a gagne le match ou nul ou defaite
                if($matchScored > $matchConceded)
                {
                    $win++;
                }
                else if($matchScored == $matchConceded)
                {
                    $draw++;
                }
                else
                {
                    $lose++;
                }

                $scored+=$matchScored;
                $conceded+=$matchConceded;
            }
            $goal_difference = $scored-$conceded;
            $points = $win*3 + $draw;

            $team['points'] = $points;
            $team['matchs_played'] = $matchs_played;
            $team['win'] = $win;
            $team['draw'] = $draw;
            $team['lose'] = $lose;
            $team['scored'] = $scored;
            $team['conceded'] = $conceded;
            $team['goal_difference'] = $goal_difference;
            array_push($result,$team);
        }

        if(count($teams) > 0)
            $rows['NOTIFYGROUP']['success'] = '1';
        else
            $rows['NOTIFYGROUP']['success'] = '0';
        $rows['NOTIFYGROUP']['data'] = $this->Edition_model->order_group_teams($result);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($rows,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function stages_edition($idCompetition)
    {
        $news['NOTIFYGROUP'] = $this->Edition_model->get_edition_stages($idCompetition);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function news($idUser,$idCompetitionType,$page)
    {
        $lang = $access = $this->session->lang;
        $news['NOTIFYGROUP'] = $this->Article_model->get_latest_news($idUser,$page,0,0,$idCompetitionType,$lang);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function notify_match_step($idUser,$idCompetition)
    {
        $news['NOTIFYGROUP'] = $this->Edition_model->notify_match_step($idUser,$idCompetition);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function current_matchs($idCompetition,$page,$idCompetitionType=0)
    {
        $news['NOTIFYGROUP'] = $this->Match_model->get_current_matchs($idCompetition,0,$page,$idCompetitionType);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function fixture($idCompetition,$page,$idCompetitionType=0)
    {
        $news['NOTIFYGROUP'] = $this->Match_model->get_fixture($idCompetition,0,$page,$idCompetitionType);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function stage_fixture($idCompetition,$idEditionStage,$idGroup,$page)
    {
        $news['NOTIFYGROUP'] = $this->Match_model->get_group_matchs_fixtures($idCompetition,$idEditionStage,$idGroup,$page);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function stage_result($idCompetition,$idEditionStage,$idGroup,$page)
    {
        $news['NOTIFYGROUP'] = $this->Match_model->get_group_matchs_results($idCompetition,$idEditionStage,$idGroup,$page);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function latest_results($idCompetition,$page,$idCompetitionType=0)
    {
        $news['NOTIFYGROUP'] = $this->Match_model->get_latest_results($idCompetition,0,$page,$idCompetitionType);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

	public function other_fixtures($idCompetition,$page,$idCompetitionType=0)
	{
		$news['NOTIFYGROUP'] = $this->Match_model->get_other_fixtures($idCompetition,0,$page,$idCompetitionType);

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}
}
