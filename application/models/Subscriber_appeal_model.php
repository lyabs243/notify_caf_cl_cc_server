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

    //get non aprove and actives appeals of subscribers
    public function get_appeals($page){
        $page_start = (((int)$page)-1)*10;
        $appeals = array();
        $query = $this->db->query('
            SELECT sa.`id`, sa.`id_subscriber`, sa.`is_policie_violate`, sa.`is_policie_respect_after_activation`, sa.`appeal_description`, sa.`active`, `approve`, sa.`register_date`,s.full_name
            FROM `subscriber_appeal` sa
            JOIN subscriber s
            ON sa.id_subscriber = s.id
            WHERE sa.active = 1
            AND sa.approve = 0
            ORDER BY register_date ASC
            LIMIT ?,10'
            ,array($page_start));
        $results = $query->result();
        foreach ($results as $result)
        {
            $data = array();
            $data['id'] = $result->id;
            $data['id_subscriber'] = $result->id_subscriber;
            $data['is_policie_violate'] = $result->is_policie_violate;
            $data['is_policie_respect_after_activation'] = $result->is_policie_respect_after_activation;
            $data['full_name'] = $result->full_name;
            $data['appeal_description'] = $result->appeal_description;
            $data['approve'] = $result->approve;
            $data['register_date'] = $result->register_date;
            $data['active'] = $result->active;
            $appeals[] = $data;
        }
        return $appeals;
    }

    public function update_appeal($id, $data) {
        // modification dans la table
        $this->db->where('id', $id);
        return $this->db->update('subscriber_appeal', $data);
    }

}