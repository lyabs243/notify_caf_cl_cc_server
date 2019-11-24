<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/03/2019
 * Time: 12:33
 */

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

class RssFeed_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
    }

    public function add_rss_feed($data) {
        // insertion dans la table
        $this->db->insert('rss_feed', $data);
        return $this->db->insert_id();
    }

    //verifie si le site existe deja
    function  is_rss_feed_exist($feed)
    {
        $return = false;
        $query = $this->db->query('SELECT * FROM rss_feed WHERE url_adress = ?'
            ,array($feed['url_adress']));
        $results = $query->result();
        foreach ($results as $result)
        {
            //if titles is differents, update all datas
            if($result->title != $feed['title'])
            {
                $this->db->where('id', $result->id);
                $this->db->update('rss_feed', $feed);
            }
            $return = true;
        }
        return $return;
    }

    public function get_feeds() {
        $query = $this->db->get('rss_feed');
        $results = $query->result();
        $feeds = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['url_adress'] = $result->url_adress;

            array_push($feeds,$row);
        }
        return $feeds;
    }
}