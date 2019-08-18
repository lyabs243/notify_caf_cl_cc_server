<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/03/2019
 * Time: 12:33
 */

class User_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
    }

    public function add_user($directRegister=false) {
        $id = 0;
        $ip = $_SERVER['REMOTE_ADDR'];
        if(!$directRegister) {
            $id = $this->is_ip_user_exist($ip);
        }
        if($id <= 0)
        {
            $this->db->insert('user', array('ip' => $ip));
            $id = $this->db->insert_id();
        }

        return $id;
    }

    //verifie si le site existe deja a partir de son id
    function  is_user_exist($id_user)
    {
        $return = false;
        $query = $this->db->query('SELECT * FROM user WHERE id = ?',array($id_user));
        $results = $query->result();
        foreach ($results as $result)
        {
            $return = true;
        }
        return $return;
    }

    //verifie si le site existe deja a partir de son ip
    function  is_ip_user_exist($ip_user)
    {
        $id = 0;
        $query = $this->db->query(
            'SELECT * FROM user u
              WHERE ip = ? 
              AND id NOT IN 
              (
                SELECT id_user FROM subscriber WHERE id_user = u.id
              )
              ORDER BY id ASC
              LIMIT 1'
            ,array($ip_user));
        $results = $query->result();
        foreach ($results as $result)
        {
            $id = $result->id;
            break;
        }
        return $id;
    }

    //verifie si un code d acces a l api est valide
    function  can_access_api($access)
    {
        $query = $this->db->query('SELECT * FROM api_access WHERE access = ?',array($access));
        $results = $query->result();
        $canAccess = false;
        foreach ($results as $result)
        {
            $canAccess = true;
            break;
        }
        return $canAccess;
    }

    public function get_latest_viewed_article($id_user) {
        $lastId = 0;
        $query = $this->db->get_where('user', array('id' => $id_user));
        $this->db->where('id',$id_user);
        $lastId = (int) $query->row_array()['last_viewed_article'];
        return $lastId;
    }

    public function get_latest_viewed_match_step($id_user) {
        $lastId = 0;
        $query = $this->db->get_where('user', array('id' => $id_user));
        $this->db->where('id',$id_user);
        $lastId = (int) $query->row_array()['last_viewed_match_step'];
        return $lastId;
    }

    public function update_user($id, $data) {
        // modification dans la table
        $this->db->where('id', $id);
        return $this->db->update('user', $data);
    }
}