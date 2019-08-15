<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CronJob extends CI_Controller {

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
        $this->load->library('session');
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $this->load->model('Article_model');
        $this->load->model('Edition_model');
        $this->load->model('Match_model');

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

    public function add_pendding_approval_news($idCompetition)
    {
        $this->Article_model->competion_add_news_pending_approval($idCompetition);
    }

    public function add_news($idCompetition)
    {
        $this->Article_model->competion_add_news($idCompetition);
    }
}
