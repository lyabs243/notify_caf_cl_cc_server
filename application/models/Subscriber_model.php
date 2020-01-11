<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/03/2019
 * Time: 12:33
 */

class Subscriber_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
        $this->load->model('User_model');
    }

    public function add_subscriber($data) {
        $subscriber = $this->is_subscriber_exist($data['id_account_user'],$data['id_account']);
        if($subscriber == null)
        {
            $subscriber = $data;
            $data['id_user'] = $this->User_model->add_user(true);
            $this->db->insert('subscriber', $data);
            $subscriber['active'] = 1;
            $subscriber['type'] = 1;
            $subscriber['success'] = 1;
            $subscriber['username'] = '';
            $subscriber['id'] = $data['id_user'];
            $subscriber['id_subscriber'] = $this->db->insert_id();
        }
        else
        {
            //si le subscriber existe deja on verifie que ses parametre sont a jour, sinon on les modifie
            if($data['full_name'] != $subscriber['full_name'] || $data['url_profil_pic'] != $subscriber['url_profil_pic'])
            {
                $subscriber['full_name'] = $data['full_name'];
                $subscriber['url_profil_pic'] = $data['url_profil_pic'];
                $newData['full_name'] = $subscriber['full_name'];
                $newData['url_profil_pic'] = $subscriber['url_profil_pic'];
                $this->update_subscriber($subscriber['id_subscriber'],$newData);
            }
        }

        return $subscriber;
    }

    public function update_subscriber($id, $data) {
        // modification dans la table
        $this->db->where('id', $id);
        return $this->db->update('subscriber', $data);
    }

    //get all data of specific subscriber
    public function get($id, $competitionCategory = 1){
	    $this->load->model('Fan_club_model');

        $data = null;
        $query = $this->db->query('SELECT * FROM subscriber WHERE id = ?'
            ,array($id));
        $results = $query->result();
        foreach ($results as $result)
        {
            $data = array();
            $data['success'] = 1;
	        $data['badge'] = $this->Fan_club_model->get_fan($id, $competitionCategory);
            $data['id'] = $result->id_user;
            $data['id_subscriber'] = $result->id;
            $data['id_account_user'] = $result->id_account_user;
            $data['username'] = $result->username;
            $data['full_name'] = $result->full_name;
            $data['url_profil_pic'] = $result->url_profil_pic;
            $data['id_account_type'] = $result->id_account;
            $data['type'] = $result->type;
            $data['active'] = $result->active;
        }
        return $data;
    }

    public function  is_admin($id){
        $admin = $this->get($id);
        if($admin['type'] == 2) {
            return true;
        }
        return false;
    }

	public function  is_active($id){
		$subscriber = $this->get($id);
		return $subscriber['active'];
	}

    public function block_subscriber($idAdmin,$id) {
        //on ne modifie que si c'est un admin qui le fait
        if($this->is_admin($idAdmin)){
            $data['active'] = 0;
            return $this->update_subscriber($id, $data);
        }
        return 0;
    }

    public function unblock_subscriber($idAdmin,$id) {
        //on ne modifie que si c'est un admin qui le fait
        if($this->is_admin($idAdmin)){
            $data['active'] = 1;
            return $this->update_subscriber($id, $data);
        }
        return 0;
    }

    //verifie si le subscriber existe deja en renvoyant ses coordonneessss
    function  is_subscriber_exist($id_account_user, $id_account_type, $competitionCategory=1)
    {
	    $this->load->model('Fan_club_model');

        $data = null;
        $query = $this->db->query('SELECT * FROM subscriber WHERE id_account_user = ? AND id_account = ?'
            ,array($id_account_user,$id_account_type));
        $results = $query->result();
        foreach ($results as $result)
        {
            $data = array();
            $data['success'] = 1;
            $data['id'] = $result->id_user;
	        $data['badge'] = $this->Fan_club_model->get_fan($result->id, $competitionCategory);
            $data['id_subscriber'] = $result->id;
            $data['id_account_user'] = $result->id_account_user;
            $data['username'] = $result->username;
            $data['full_name'] = $result->full_name;
            $data['url_profil_pic'] = $result->url_profil_pic;
            $data['id_account_type'] = $result->id_account;
            $data['type'] = $result->type;
            $data['active'] = $result->active;
        }
        return $data;
    }
}