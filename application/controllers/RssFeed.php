<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 27/08/2019
 * Time: 12:44
 */
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'controllers/NotifyController.php';
require_once APPPATH.'libraries/feed/Feed.php';
include APPPATH.'libraries/simple_html_dom.php';
class RssFeed extends CI_Controller
{
    function __construct()
    {
        // this is your constructor
        parent::__construct();
        $this->load->model('RssFeed_model');
        $this->load->model('Article_model');
    }

    public function get_feeds()
    {
        $data = $this->RssFeed_model->get_feeds();

        if(!count($data)){
            $news['NOTIFYGROUP'] = array('success' => '0');
        }
        else{

            $news['NOTIFYGROUP'] = array('success' => '1','data' => $data);
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function add_feed_file(){
        $config['upload_path']          = './resource/json/';
        $config['allowed_types']        = 'gif|jpg|png|txt';
        $config['max_size']             = 1000;
        $config['max_width']            = 1024;
        $config['max_height']           = 768;

        $this->load->library('upload', $config);
        if (file_exists($config['upload_path'].'json_feed.txt')) {
            unlink($config['upload_path'].'json_feed.txt');
        }
        if ( ! $this->upload->do_upload('file_contents'))
        {
            $error = array('error' => $this->upload->display_errors());
            print_r($error);
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            print_r($data);
        }
    }

    public function add_news_from_feed(){
        $this->RssFeed_model->add_feeds_items();
    }

    public function desactivate_appeal($id,$id_admin){
        if($this->Subscriber_model->is_admin($id_admin)) {
            $data['active'] = 0;
            $result = $this->Subscriber_appeal_model->update_appeal($id, $data);
            if ($result)
                $news['NOTIFYGROUP'][] = array('success' => '1');
            else
                $news['NOTIFYGROUP'][] = array('success' => '0');
        }
        else{
            $news['NOTIFYGROUP'][] = array('success' => '0');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function approve_appeal($id,$id_admin,$id_subscriber){
        $data['approve'] = 1;
        if($this->Subscriber_model->is_admin($id_admin)) {
            $result = $this->Subscriber_appeal_model->update_appeal($id, $data);
            if ($result) {
                //unclock subscriber
                $result1 = $this->Subscriber_model->unblock_subscriber($id_admin, $id_subscriber);
                if ($result1) {
                    $news['NOTIFYGROUP'][] = array('success' => '1');
                } else {
                    $news['NOTIFYGROUP'][] = array('success' => '0');
                }
            } else
                $news['NOTIFYGROUP'][] = array('success' => '0');
        }
        else{
            $news['NOTIFYGROUP'][] = array('success' => '0');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }
}