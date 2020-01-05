<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 21/12/2019
 * Time: 09:55
 */
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'controllers/NotifyController.php';
class Post extends NotifyController
{
    function __construct()
    {
        // this is your constructor
        parent::__construct();
        $this->load->model('Subscriber_model');
        $this->load->model('Post_model');
    }

	public function update($id, $idSubscriber){
		if($this->Subscriber_model->is_active($idSubscriber)) {
			$data['post'] = $this->input->post('post');
			$result = $this->Post_model->update_post($id, $idSubscriber, $data);
			if ($result)
				$output['NOTIFYGROUP'][] = array('success' => '1');
			else
				$output['NOTIFYGROUP'][] = array('success' => '0');
		}
		else{
			$output['NOTIFYGROUP'][] = array('success' => '0');
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($output,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

	public function delete($id, $idSubscriber){
		if($this->Subscriber_model->is_active($idSubscriber)) {
			$result = $this->Post_model->delete_post($id, $idSubscriber);
			if ($result)
				$output['NOTIFYGROUP'][] = array('success' => '1');
			else
				$output['NOTIFYGROUP'][] = array('success' => '0');
		}
		else{
			$output['NOTIFYGROUP'][] = array('success' => '0');
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($output,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

	public function get_posts($active_subscriber=0, $idSubscriber=0, $page=1)
	{
		$page = (int)$page;
		$idSubscriber = (int)$idSubscriber;
		$data = $this->Post_model->get_posts($active_subscriber, $idSubscriber, $page);

		if(!count($data)){
			$news['NOTIFYGROUP'] = array('success' => '0');
		}
		else{

			$news['NOTIFYGROUP'] = array('success' => '1','data' => $data);
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

	public function get_post($id_post, $active_subscriber=0)
	{
		$data = $this->Post_model->get_post($id_post, $active_subscriber);

		if(!count($data)){
			$news['NOTIFYGROUP'] = array('success' => '0');
		}
		else{
			$news['NOTIFYGROUP'] = array('success' => '1','data' => $data);
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

	public function get_abusive_posts($id_admin, $page=1)
	{
		$page = (int)$page;
		$id_admin = (int)$id_admin;
		if($this->Subscriber_model->is_admin($id_admin)) {
			$data = $this->Post_model->get_abusive_posts($page, $id_admin);
			$result = count($data);
		}
		else {
			$result = false;
		}

		if(!$result){
			$output['NOTIFYGROUP'] = array('success' => '0');
		}
		else{

			$output['NOTIFYGROUP'] = array('success' => '1','data' => $data);
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($output,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

    public function add($id_subscriber, $uploadImage = false)
    {
    	$result = false;
    	$error = null;
    	if($this->Subscriber_model->is_active($id_subscriber)) {

		    $config['upload_path'] = './resource/images/posts/';
		    $config['allowed_types'] = 'gif|jpg|png';
		    $config['max_size'] = 5000;
		    //$config['max_width'] = 3000;
		    //$config['max_height'] = 3000;
		    $config['encrypt_name'] = TRUE;

		    $this->load->library('upload', $config);
		    $result = true;

		    if ($uploadImage) {
			    if (!$this->upload->do_upload('img_post')) {
				    $result = false;
				    $error = $this->upload->display_errors();
			    } else {
				    $uploadData = $this->upload->data();
				    $data['url_image'] = 'http://www.notifygroup.org/notifyapp/api/resource/images/posts/' . $uploadData['file_name'];
			    }
		    }

		    if ($result) {
			    $data['id_subscriber'] = (int)$id_subscriber;
			    $data['post'] = $this->input->post('post');
			    $data['type'] = (int)$this->input->post('type');
			    $result = $this->Post_model->add_post($data);
		    }
	    }

        if($result)
            $news['NOTIFYGROUP'][] = array('success' => '1');
        else
            $news['NOTIFYGROUP'][] = array('success' => '0', 'error' => $error);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

	public function signal($id_post, $id_subscriber)
	{
		$result = false;
		if($this->Subscriber_model->is_active($id_subscriber)) {
			$data['id_subscriber'] = (int)$id_subscriber;
			$data['id_post'] = (int)$id_post;
			$data['message'] = $this->input->post('message');
			$result = $this->Post_model->signal_post($data);
		}

		if($result)
			$news['NOTIFYGROUP'][] = array('success' => '1');
		else
			$news['NOTIFYGROUP'][] = array('success' => '0');

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

	public function block_post($id,$id_admin){
		if($this->Subscriber_model->is_admin($id_admin)) {
			$data['active'] = 0;
			$result = $this->Post_model->update_post_status($id, $data);
			if ($result)
				$output['NOTIFYGROUP'][] = array('success' => '1');
			else
				$output['NOTIFYGROUP'][] = array('success' => '0');
		}
		else{
			$output['NOTIFYGROUP'][] = array('success' => '0');
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($output,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

	public function active_post($id,$id_admin){
		if($this->Subscriber_model->is_admin($id_admin)) {
			$data['active'] = 1;
			$result = $this->Post_model->update_post_status($id, $data);
			if ($result)
				$output['NOTIFYGROUP'][] = array('success' => '1');
			else
				$output['NOTIFYGROUP'][] = array('success' => '0');
		}
		else{
			$output['NOTIFYGROUP'][] = array('success' => '0');
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($output,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

	//desactive les signalement de post
	public function deactivate_abusive_post($id,$id_admin){
		if($this->Subscriber_model->is_admin($id_admin)) {
			$data['active'] = 0;
			$result = $this->Post_model->update_abusive_post_status($id, $data);
			if ($result)
				$output['NOTIFYGROUP'][] = array('success' => '1');
			else
				$output['NOTIFYGROUP'][] = array('success' => '0');
		}
		else{
			$output['NOTIFYGROUP'][] = array('success' => '0');
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($output,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

	public function active_abusive_post($id,$id_admin){
		if($this->Subscriber_model->is_admin($id_admin)) {
			$data['active'] = 1;
			$result = $this->Post_model->update_abusive_post_status($id, $data);
			if ($result)
				$output['NOTIFYGROUP'][] = array('success' => '1');
			else
				$output['NOTIFYGROUP'][] = array('success' => '0');
		}
		else{
			$output['NOTIFYGROUP'][] = array('success' => '0');
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($output,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

	public function add_comment($idUser,$idPost) {
		$idUser = (int)$idUser;
		$idPost = (int)$idPost;
		if($this->User_model->is_user_exist($idUser)) {
			$this->load->library('form_validation');
			// définition des règles de validation
			$this->form_validation->set_rules('comment', '« Comment »', 'required');

			// ajout du style pour les messages d'erreur
			$this->form_validation->set_error_delimiters('<br /><div class="errorMessage"><span style="font-size: 150%;">&uarr;&nbsp;</span>', '</div>');

			if ($this->form_validation->run() == FALSE) {
				$news['NOTIFYGROUP'] = array('success' => '0');
			} else {
				// succès de la validation : récupération des données passées en post

				$comment = htmlspecialchars($this->input->post('comment'));
				$data['comment'] = $comment;
				$data['id_user'] = $idUser;
				$data['id_post'] = $idPost;
				$result = $this->Post_model->add_comment($data);

				if($result > 0)
					$news['NOTIFYGROUP'] = array('success' => '1');
				else
					$news['NOTIFYGROUP'] = array('success' => '0');
			}

		}
		else
		{
			$news['NOTIFYGROUP'] = array('success' => '0');
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}
}