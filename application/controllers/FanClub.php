<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 11/01/2020
 * Time: 08:44
 */
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'controllers/NotifyController.php';
class FanClub extends NotifyController {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Fan_club_model');
		$this->load->model('Subscriber_model');
	}

	public function add($idSubscriber,$idTeam,$category=1)
	{
		$data['id_subscriber'] = $idSubscriber;
		$data['id_team'] = $idTeam;
		$data['category'] = $category;

		$data = $this->Fan_club_model->add_fan($data);

		if($data) {
			$result['NOTIFYGROUP'] = array('success' => '1');
		}
		else {
			$result['NOTIFYGROUP'] = array('success' => '0');
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($result,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

	public function delete($idSubscriber, $competitionCategory=1){
		if($this->Subscriber_model->is_active($idSubscriber)) {
			$result = $this->Fan_club_model->delete_badge($idSubscriber, $competitionCategory);
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

	public function update($idSubscriber, $idTeam, $competitionCategory=1){
		if($this->Subscriber_model->is_active($idSubscriber)) {
			$data['id_team'] = $idTeam;
			$result = $this->Fan_club_model->update($idSubscriber, $competitionCategory, $data);
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

	public function get($idSubscriber, $category=1)
	{
		$data = $this->Fan_club_model->get_fan($idSubscriber, $category);

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
}