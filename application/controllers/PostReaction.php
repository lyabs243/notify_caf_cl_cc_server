<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 22/12/2019
 * Time: 09:36
 */

defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'controllers/NotifyController.php';
class PostReaction extends NotifyController
{

	function __construct()
	{
		// this is your constructor
		parent::__construct();
		$this->load->model('Subscriber_model');
		$this->load->model('Post_reaction_model');
	}

	public function add($id_post, $id_subscriber, $reaction)
	{
		$result = false;
		if($this->Subscriber_model->is_active($id_subscriber)) {
			$data['id_subscriber'] = (int)$id_subscriber;
			$data['id_post'] = (int)$id_post;
			$data['reaction_type'] = (int)$reaction;
			$result = $this->Post_reaction_model->add_post_reaction($data);
		}

		if($result)
			$output['NOTIFYGROUP'][] = array('success' => '1');
		else
			$output['NOTIFYGROUP'][] = array('success' => '0');

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($output,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

}