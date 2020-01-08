<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH . 'controllers/NotifyController.php';

class Match extends NotifyController {

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
        $this->load->model('Edition_model');
        $this->load->model('Match_model');
        $this->load->model('User_model');
        $this->load->model('Video_model');

        $lang = $access = $this->session->lang;
        $language = 'english';
        if($lang == 'fr')
            $language = 'french';

        $this->lang->load(array('spt_match_status','spt_teams_lang','date_format'),
            $language);
    }

    public function index()
    {
        $this->load->view('welcome_message');
    }

    public function add_composition($idMatch,$idTeam) {
        if($idMatch > 0) {
            $this->load->library('form_validation');
            // définition des règles de validation
            /*$this->form_validation->set_rules('id', '« Id Players »', 'required');

            // ajout du style pour les messages d'erreur
            $this->form_validation->set_error_delimiters('<br /><div class="errorMessage"><span style="font-size: 150%;">&uarr;&nbsp;</span>', '</div>');

            if ($this->form_validation->run() == FALSE) {
                $news['NOTIFYGROUP'][] = array('success' => '0');
            } else {*/
                // succès de la validation : récupération des données passées en post

                $id_players = $this->input->post('id_players');
                $players = json_decode($id_players,true);
                if(count($players) > 0) {
                    foreach ($players as $key => $value) {
                        $listPlay[] = (int)$value['id'];
                    }
                    $idComposition = $this->Match_model->add_match_composition($idMatch);
                    $this->Match_model->add_composition_details($idMatch,$idComposition,$idTeam,$players);

                    $news['NOTIFYGROUP'][] = array('success' => '1');
                }
                else
                {
                    $news['NOTIFYGROUP'][] = array('success' => '0');
                }
            //}
        }
        else
        {
            $news['NOTIFYGROUP'][] = array('success' => '0');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function status($idMatch,$status)
    {
        $result = $this->Match_model->set_status($idMatch,$status);
        //si le staus est : debut match/mi temps/prolongation/tir au but/fin match
        //on ajoute egalement son action
        if($status == 1 || $status == 2 || $status == 3 || $status == 4 || $status == 5) {
            $data['id_match'] = $idMatch;
            $data['id_player'] = 0;
            if ($status == 1)
                $data['type'] = 0;
            else if ($status == 2)
                $data['type'] = 5;
            else if ($status == 3)
                $data['type'] = 6;
            else if ($status == 4)
                $data['type'] = 7;
            else //tirs au but
                $data['type'] = 8;

            $this->Match_model->add_action($data);
        }

        if($result)
            $news['NOTIFYGROUP'][] = array('success' => '1');
        else
            $news['NOTIFYGROUP'][] = array('success' => '0');
        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function actions($idMatch,$actionMin=0)
    {
        $actions = $this->Match_model->get_match_actions($idMatch,$actionMin);
        if(count($actions) > 0)
            $result['NOTIFYGROUP']['success'] = '1';
        else
            $result['NOTIFYGROUP']['success'] = '0';
        $result['NOTIFYGROUP']['data'] = $actions;

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($result,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function lineup($idMatch,$idTeam)
    {
        $lineup = $this->Match_model->get_match_lineup($idMatch,$idTeam);
        if(count($lineup) > 0)
            $result['NOTIFYGROUP']['success'] = '1';
        else
            $result['NOTIFYGROUP']['success'] = '0';
        $result['NOTIFYGROUP']['data'] = $lineup;

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($result,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function team_players($idCompetition,$idEdition=0,$idTeam)
    {
        $news['NOTIFYGROUP'] = $this->Match_model->get_team_players($idCompetition,$idEdition,$idTeam);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function comments($idMatch,$page=1,$idCommentMin=0)
    {
	    $data = $this->Match_model->get_match_comments($idMatch,$page,$idCommentMin);

	    if(count($data)) {
		    $result['NOTIFYGROUP'] = array('success' => '1', 'data' => $data);
	    }
	    else {
		    $result['NOTIFYGROUP'] = array('success' => '0');
	    }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($result,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function video($idMatch)
    {
        $video = $this->Match_model->get_match_video($idMatch);

        if(count($video) > 0)
            $result['NOTIFYGROUP']['success'] = '1';
        else
            $result['NOTIFYGROUP']['success'] = '0';
        $result['NOTIFYGROUP']['data'] = $video;

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($result,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function get($idMatch)
    {
        $news['NOTIFYGROUP'] = $this->Match_model->get_match($idMatch);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function add_comment($idUser,$idMatch) {
	    $this->load->model('Comment_model');
        $idUser = (int)$idUser;
        $idMatch = (int)$idMatch;
        if($this->User_model->is_user_exist($idUser)) {
            $this->load->library('form_validation');
            // définition des règles de validation
            $this->form_validation->set_rules('comment', '« Comment »', 'required');

            // ajout du style pour les messages d'erreur
            $this->form_validation->set_error_delimiters('<br /><div class="errorMessage"><span style="font-size: 150%;">&uarr;&nbsp;</span>', '</div>');

            if ($this->form_validation->run() == FALSE) {
                $news['NOTIFYGROUP'] = array('success' => '0');
            } else {
                // succès de la validation : récupération des données passées en post

                $comment = htmlspecialchars($this->input->post('comment'));
                $data['comment'] = $comment;
                $data['id_user'] = $idUser;
                $data['id_match'] = $idMatch;
                $result = $this->Comment_model->add_comment($data);

                if(count($result))
                    $news['NOTIFYGROUP'] = array('success' => '1', 'data' => $result);
                else
                    $news['NOTIFYGROUP'] = array('success' => '0');
            }

        }
        else
        {
            $news['NOTIFYGROUP'] = array('success' => '0');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function add_action($idMatch,$action) {
        $idMatch = (int)$idMatch;
        $action = (int)$action;
            $data['detail_a'] = $this->input->post('detail_a');
            $data['detail_b'] = $this->input->post('detail_b');
            $data['minute'] = $this->input->post('minute');
            $data['id_player'] = $this->input->post('id_player');
            $data['id_team'] = $this->input->post('id_team');
            $data['id_match'] = $idMatch;
            $data['type'] = $action;

            $result = $this->Match_model->add_action($data);

            if($result > 0)
                $news['NOTIFYGROUP'][] = array('success' => '1');
            else
                $news['NOTIFYGROUP'][] = array('success' => '0');

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }
}
