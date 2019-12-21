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

    public function add($id_subscriber, $uploadImage = false)
    {

	    $config['upload_path']          = './resource/images/posts/';
	    $config['allowed_types']        = 'gif|jpg|png';
	    $config['max_size']             = 2000;
	    $config['max_width']            = 2000;
	    $config['max_height']           = 2000;
	    $config['encrypt_name']         = TRUE;

	    $this->load->library('upload', $config);
	    $result = true;

	    if($uploadImage) {
		    if (!$this->upload->do_upload('img_post')) {
			    $result = false;
		    } else {
			    $uploadData = $this->upload->data();
			    $data['url_image'] = 'http://www.notifygroup.org/notifyapp/api/resource/images/posts/' . $uploadData['file_name'];
		    }
	    }

	    if($result) {
		    $data['id_subscriber'] = (int)$id_subscriber;
		    $data['post'] = $this->input->post('post');
		    $data['type'] = (int)$this->input->post('type');
		    $result = $this->Post_model->add_post($data);
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