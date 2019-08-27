<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 27/08/2019
 * Time: 12:33
 */

class Subscriber_appeal_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
    }

    public function add_appeal($data) {
        $this->db->insert('subscriber_appeal', $data);
        $id = $this->db->insert_id();

        return $id;
    }

}