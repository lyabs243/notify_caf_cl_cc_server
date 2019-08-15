<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/03/2019
 * Time: 12:33
 */

class Favorite_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
    }

    public function add_favorite($data) {
        // insertion dans la table
        $this->db->insert('favorite', $data);
        return $this->db->insert_id();
    }

    public function add_favorite_feed($data) {
        // insertion dans la table
        $this->db->insert('favorite_feed', $data);
        return $this->db->insert_id();
    }

    public function delete_favorite($idUser,$favorites) {
        $idUser = (int)$idUser;
        $this->db->where('id_user',$idUser);
        $where = '(';
        $i = 0;
        foreach ($favorites as $favorite)
        {
            if($i > 0)
            {
                $where .= ' OR ';
            }
            $where .= ' id_website = ' . $favorite;
            $i++;
        }
        $where .= ' )';
        $this->db->where($where);
        return $this->db->delete('favorite') ;
    }

    public function delete_favorite_feed($idUser,$feeds) {
        $idUser = (int)$idUser;
        $this->db->where('id_user',$idUser);
        $where = '(';
        $i = 0;
        foreach ($feeds as $favorite)
        {
            if($i > 0)
            {
                $where .= ' OR ';
            }
            $where .= ' id_feed = ' . $favorite;
            $i++;
        }
        $where .= ' )';
        $this->db->where($where);
        return $this->db->delete('favorite_feed') ;
    }

    function  is_favorite_exist($id_website,$id_user)
    {
        $return = false;
        $query = $this->db->query('SELECT * FROM favorite WHERE id_website = ? AND id_user = ?'
            ,array($id_website,$id_user));
        $results = $query->result();
        foreach ($results as $result)
        {
            $return = true;
        }
        return $return;
    }

    function  is_favorite_feed_exist($id_feed,$id_user)
    {
        $return = false;
        $query = $this->db->query('SELECT * FROM favorite_feed WHERE id_feed = ? AND id_user = ?'
            ,array($id_feed,$id_user));
        $results = $query->result();
        foreach ($results as $result)
        {
            $return = true;
        }
        return $return;
    }

    public function get_favorites($idUser) {
        $sql = "SELECT w.id as cid, w.url_adress as category_name
                FROM `favorite`f
                JOIN website w
                ON f.id_website = w.id
                WHERE f.id_user = ?
                GROUP BY category_name ASC";
        $query = $this->db->query($sql,array($idUser));
        $results = $query->result();
        $news = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['cid'] = $result->cid;
            $row['category_name'] = $result->category_name;

            array_push($news,$row);
        }
        return $news;
    }

    public function get_favorites_feed($idUser) {
        $sql = "SELECT rf.id as cid, rf.title as category_name
                FROM `favorite_feed`ff
                JOIN rss_feed rf
                ON ff.id_feed = rf.id
                WHERE ff.id_user = ?
                GROUP BY category_name ASC";
        $query = $this->db->query($sql,array($idUser));
        $results = $query->result();
        $news = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['cid'] = $result->cid;
            $row['category_name'] = $result->category_name;

            array_push($news,$row);
        }
        return $news;
    }

    public function get_suggest_favs($idCountry,$categories,$start,$length) {
        $sql = "SELECT * FROM `website` 
                WHERE `id` IN
                (
                    SELECT id_website FROM country_website
                    WHERE id_country = ? 
                    AND (";
        $i = 0;
        foreach ($categories as $category)
        {
            if($i > 0)
            {
                $sql .= ' OR ';
            }
            $sql .= ' id_category = ' . $category;
            $i++;
        }
        $sql .= ")
                )
                ORDER BY `url_adress` asc";
        //on specifie les limites seulement si la valeur start a ete specifiee
        if($length > 0)
        {
            $sql .= ' limit ' . $start . ' , ' . $length;
        }
        $query = $this->db->query($sql,array($idCountry));
        $results = $query->result();
        $favs = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['cid'] = $result->id;
            $row['category_name'] = $result->url_adress;

            array_push($favs,$row);
        }
        return $favs;
    }

    public function get_countries($start,$length) {
        $sql = 'SELECT * FROM ((SELECT * FROM country
                                    WHERE id = 240)
                                    UNION
                                    (SELECT * FROM `country`
                                    WHERE id IN
                                    (
                                        SELECT id_country FROM country_website
                                    ))) as t
                                    ORDER BY name ASC
                                     ';
        //on specifie les limites seulement si la valeur start a ete specifiee
        if($length > 0)
        {
            $sql .= ' limit ' . $start . ' , ' . $length;
        }
        $query = $this->db->query($sql,array());
        $results = $query->result();
        $countries = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            $row['iso'] = $result->iso;
            $row['iso3'] = $result->iso3;
            $row['name'] = $result->name;
            $row['nice_name'] = $result->nicename;
            $row['num_code'] = $result->numcode;
            $row['phone_code'] = $result->phonecode;
            $row['url_flag'] = $result->url_flag;
            $row['lang'] = $result->lang;

            array_push($countries,$row);
        }
        return $countries;
    }

    public function get_categories($idCountry,$start,$length) {
        $sql = 'SELECT * FROM `category`
            WHERE id IN
            (
                SELECT DISTINCT w.id_category FROM website w
                LEFT JOIN country_website cw
                ON w.id = cw.id_website
                WHERE id_category IS not null
                AND id_category > 0
                AND cw.id_country = ?
            )';
        //on specifie les limites seulement si la valeur start a ete specifiee
        if($length > 0)
        {
            $sql .= ' limit ' . $start . ' , ' . $length;
        }
        $query = $this->db->query($sql,array($idCountry));
        $results = $query->result();
        $categories = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['cid'] = $result->id;
            $row['category_name'] = $result->description_en;

            array_push($categories,$row);
        }
        return $categories;
    }
}