<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 27/08/2019
 * Time: 12:44
 */
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'controllers/NotifyController.php';
class SubscriberAppeal extends NotifyController
{
    function __construct()
    {
        // this is your constructor
        parent::__construct();
        $this->load->model('Subscriber_appeal_model');
    }

    public function add($id_subscriber)
    {
        $data['id_subscriber'] = (int)$id_subscriber;
        $data['is_policie_violate'] = $this->input->post('is_policie_violate');
        $data['is_policie_respect_after_activation'] = $this->input->post('is_policie_respect_after_activation');
        $data['appeal_description'] = $this->input->post('appeal_description');

        $result = $this->Subscriber_appeal_model->add_appeal($data);
        if($result)
            $news['NOTIFYGROUP'][] = array('success' => '1');
        else
            $news['NOTIFYGROUP'][] = array('success' => '0');

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function get_appeals($page=1)
    {
        $page = (int)$page;
        $data = $this->Subscriber_appeal_model->get_appeals($page);

        if(!count($data)){
            $news['NOTIFYGROUP'][] = array('success' => '0');
        }
        else{

            $news['NOTIFYGROUP'][] = array('success' => '1','data' => $data);
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }
}