<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 21/12/2019
 * Time: 10:18
 */

class Post_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
    }

    public function add_post($data) {
        $this->db->insert('post', $data);
        $id = $this->db->insert_id();

        return $id;
    }

}