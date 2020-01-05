<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 05/01/2020
 * Time: 11:30
 */
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . "libraries/Goutte-master/vendor/autoload.php";
require_once  APPPATH . "libraries/url_to_absolute.php";

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

include APPPATH.'controllers/NotifyController.php';
class Comment extends NotifyController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Comment_model');
	    $this->load->model('User_model');
    }

	public function update($id, $idUser){
		if($this->User_model->is_user_exist($idUser)) {
			$data['comment'] = $this->input->post('comment');
			$result = $this->Comment_model->update_comment($id, $idUser, $data);
			if ($result)
				$output['NOTIFYGROUP'] = array('success' => '1');
			else
				$output['NOTIFYGROUP'] = array('success' => '0');
		}
		else{
			$output['NOTIFYGROUP'] = array('success' => '0');
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($output,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

	public function delete($id, $idUser){
		if($this->User_model->is_user_exist($idUser)) {
			$result = $this->Comment_model->delete_comment($id, $idUser);
			if ($result)
				$output['NOTIFYGROUP'] = array('success' => '1');
			else
				$output['NOTIFYGROUP'] = array('success' => '0');
		}
		else{
			$output['NOTIFYGROUP'] = array('success' => '0');
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($output,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}
}