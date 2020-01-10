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

}