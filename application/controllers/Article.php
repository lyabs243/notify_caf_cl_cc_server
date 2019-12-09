<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'controllers/NotifyController.php';
class Article extends NotifyController {

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
    }

    public function view_article($id_article,$id_user)
    {
        $save = $this->Article_model->view_article($id_article, $id_user);

        if($save){
            $result['NOTIFYGROUP'] = array('success' => '1');
        }
        else{
            $result['NOTIFYGROUP'] = array('success' => '0');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($result,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function index()
    {
        $this->load->view('welcome_message');
    }

    public function home_news($idUser,$forFeed=0)
    {
        $result['trending_news'] = $this->Article_model->get_trend_news($idUser,$forFeed);
        $result['latest_news'] = $this->Article_model->get_latest_news($idUser,0,0,$forFeed);
        $result['top_story'] = $this->Article_model->get_latest_news($idUser,0,0,$forFeed);

        $news['NOTIFYGROUP'] = $result;

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function latest_news($idUser,$page=0,$idWebsite=0,$forFeed=0)
    {
        $news['NOTIFYGROUP'] = $this->Article_model->get_latest_news($idUser,$page,$idWebsite,$forFeed);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function notify_news($idUser,$forFeed=0,$idCompetition=0)
    {
        $lang = $access = $this->session->lang;
        $news['NOTIFYGROUP'] = $this->Article_model->notify_news($idUser,$forFeed,$idCompetition,$lang);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function search_news($idUser,$searchText,$page,$forFeed=0)
    {
        $news['NOTIFYGROUP'] = $this->Article_model->search_news($idUser,$searchText,$page,$forFeed);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }
}
