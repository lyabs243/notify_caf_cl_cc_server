<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/03/2019
 * Time: 12:44
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class NotifyController extends CI_Controller
{
    function __construct()
    {
        // this is your constructor
        parent::__construct();
        $this->load->library('session');
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $nData = array();
        // définition des règles de validation
        $this->form_validation->set_rules('access_api', '« Api »', 'required');

        // ajout du style pour les messages d'erreur
        $this->form_validation->set_error_delimiters('<br /><div class="errorMessage"><span style="font-size: 150%;">&uarr;&nbsp;</span>', '</div>');

        /*if ($this->form_validation->run() == FALSE) {
            header('location: http://notifygroup.org');
        } else {*/
            // succès de la validation : récupération des données passées en post
            $access = $this->input->post('access_api');
            $_SESSION['lang'] = $this->input->post('lang');
            if($_SESSION['lang'] != 'fr' && $_SESSION['lang'] != 'en')
            {
                $_SESSION['lang'] = 'en';
            }

            $_SESSION['timezone'] = $this->input->post('timezone');
            if(!isset($_SESSION['timezone']))
            {
                $_SESSION['timezone'] = '+00:00';
            }

            if(!$this->User_model->can_access_api($access))
            {
                //header('location: http://notifygroup.org');
            }
        //}
    }
}