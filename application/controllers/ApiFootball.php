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
		$this->load->library('session');
		$this->load->model('Api_football_model');
		$_SESSION['timezone'] = $this->input->post('timezone');
		if(!isset($_SESSION['timezone']))
		{
			$_SESSION['timezone'] = '+00:00';
		}
	}

	public function get_league_matchs($id_edition, $league_id){
		$this->Api_football_model->init_matchs_from_api($id_edition, $league_id);
	}

	public function add_league_scorers($id_edition, $league_id){
		$this->Api_football_model->add_top_scorers($league_id, $id_edition);
	}

	public function add_matchs_actions(){
		$this->Api_football_model->add_matchs_actions();
	}

	public function add_matchs_lineups(){
		$this->Api_football_model->add_matchs_lineup();
	}

}