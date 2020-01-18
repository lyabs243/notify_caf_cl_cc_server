<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/01/2020
 * Time: 14:12
 */
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'controllers/NotifyController.php';
class Team extends NotifyController {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Team_model');
	}

	public function add_teams_from_json(){
		$json = file_get_contents('./resource/json/teams.json');
		$this->Team_model->add_teams_from_json($json);
	}

	public function add_teams_from_api_json($competition=1){
		if($competition == 2) { //caf confederation cup
			$json = file_get_contents('./resource/json/caf_cc.json');
		}
		else { //caf champpions league
			$json = file_get_contents('./resource/json/caf_cl.json');
		}

		$this->Team_model->add_teams_from_api_json($json);
	}

	public function update_country_flag_from_json(){
		$json = file_get_contents('./resource/json/country.json');
		$this->Team_model->update_country_urlflag_from_json($json);
	}

	public function update_teams_from_json(){
		$json = file_get_contents('./resource/json/teams.json');
		$this->Team_model->update_teams_from_json($json);
	}

}