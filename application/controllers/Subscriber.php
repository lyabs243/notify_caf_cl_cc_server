<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/03/2019
 * Time: 12:44
 */
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'controllers/NotifyController.php';
class Subscriber extends NotifyController
{
    function __construct()
    {
        // this is your constructor
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Subscriber_model');
    }

    public function block($idAdmin,$idSubscriber){
        $result = $this->Subscriber_model->block_subscriber($idAdmin,$idSubscriber);
        if($result){
            $news['NOTIFYGROUP'][] = array('success' => '1');
        }
        else{
            $news['NOTIFYGROUP'][] = array('success' => '0');
        }
        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function unblock($idAdmin,$idSubscriber){
        $result = $this->Subscriber_model->unblock_subscriber($idAdmin,$idSubscriber);
        if($result){
            $news['NOTIFYGROUP'][] = array('success' => '1');
        }
        else{
            $news['NOTIFYGROUP'][] = array('success' => '0');
        }
        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function get($id)
    {
        $result = $this->Subscriber_model->get($id);
        $news['NOTIFYGROUP'][] = $result;
        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function add()
    {
        $this->load->library('form_validation');
        // définition des règles de validation
        $this->form_validation->set_rules('full_name', '« Full Name »', 'required');
        $this->form_validation->set_rules('id_account', '« Id Account »', 'required');
        $this->form_validation->set_rules('id_account_type', '« Id Account Type »', 'required');

        // ajout du style pour les messages d'erreur
        $this->form_validation->set_error_delimiters('<br /><div class="errorMessage"><span style="font-size: 150%;">&uarr;&nbsp;</span>', '</div>');

        if ($this->form_validation->run() == FALSE) {
            $news['NOTIFYGROUP'][] = array('success' => '0');
        } else {
            // succès de la validation : récupération des données passées en post

            $data['full_name'] = $this->input->post('full_name');
            $data['url_profil_pic'] = $this->input->post('url_profil_pic');
            if($data['url_profil_pic'] == null)
            {
                $data['url_profil_pic'] = '';
            }
            $data['id_account_user'] = $this->input->post('id_account');
            $data['id_account'] = $this->input->post('id_account_type');

            $result = $this->Subscriber_model->add_subscriber($data);
            $news['NOTIFYGROUP'][] = $result;
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }
}