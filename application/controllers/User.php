<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/03/2019
 * Time: 12:44
 */
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'controllers/NotifyController.php';
class User extends NotifyController
{
    function __construct()
    {
        // this is your constructor
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->model('User_model');
    }

    public function test()
    {

        $username = 'username';
        $password = 'password';
        $email = 'email@email.cd';
        $additional_data = array(
                'first_name' => 'first_name',
                'last_name' => 'last_name',
            );
        $group = array();

        $this->ion_auth->register($username, $password, $email, $additional_data, $group);

    }

    public function add()
    {
        $id = $this->User_model->add_user();
        $news['ALL_IN_ONE_NEWS'][] = array
        (
            'success' => '1',
            'id' => $id,
            'id_subscriber' => 0,
            'id_account_user' => '',
            'username' => '',
            'full_name' => '',
            'id_account_type' => 0
        );

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }
}