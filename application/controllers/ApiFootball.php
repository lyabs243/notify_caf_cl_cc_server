<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 18/01/2020
 * Time: 15:37
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class ApiFootball extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Api_football_model');
	}

	public function get_league_matchs($id_edition, $league_id){
		$this->Api_football_model->init_matchs_from_api($id_edition, $league_id);
	}

}