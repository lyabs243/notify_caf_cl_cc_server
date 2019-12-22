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
			$data['reaction_type'] = (int)$reaction;
			//subscriber post reaction exist, update it, lese add it
			if($this->Post_reaction_model->is_subscriber_reaction_exist($id_post, $id_subscriber)) {
				$result = $this->Post_reaction_model->update_post_reaction($id_post, $id_subscriber, $data);
			}
			else {
				$data['id_subscriber'] = (int)$id_subscriber;
				$data['id_post'] = (int)$id_post;
				$result = $this->Post_reaction_model->add_post_reaction($data);
			}
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