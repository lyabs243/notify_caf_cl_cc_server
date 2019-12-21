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

	public function get_posts($idSubscriber=0, $page=1)
	{
		$page = (int)$page;
		$idSubscriber = (int)$idSubscriber;
		$data = $this->Post_model->get_posts($idSubscriber, $page);

		if(!count($data)){
			$news['NOTIFYGROUP'][] = array('success' => '0');
		}
		else{

			$news['NOTIFYGROUP'][] = array('success' => '1','data' => $data);
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		die;
	}

    public function add($id_subscriber, $uploadImage = false)
    {
    	$result = false;
    	if($this->Subscriber_model->is_active($id_subscriber)) {

		    $config['upload_path'] = './resource/images/posts/';
		    $config['allowed_types'] = 'gif|jpg|png';
		    $config['max_size'] = 2000;
		    $config['max_width'] = 2000;
		    $config['max_height'] = 2000;
		    $config['encrypt_name'] = TRUE;

		    $this->load->library('upload', $config);
		    $result = true;

		    if ($uploadImage) {
			    if (!$this->upload->do_upload('img_post')) {
				    $result = false;
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
            $news['NOTIFYGROUP'][] = array('success' => '0');

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }
}