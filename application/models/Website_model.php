<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/03/2019
 * Time: 12:33
 */

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

class Website_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
    }

    public function add_website($data) {
        // insertion dans la table
        $this->db->insert('website', $data);
        return $this->db->insert_id();
    }

    //verifie si le site existe deja
    function  is_webSite_exist($url_adress)
    {
        $return = false;
        $query = $this->db->query('SELECT * FROM website WHERE url_adress = ?'
            ,array($url_adress));
        $results = $query->result();
        foreach ($results as $result)
        {
            $return = true;
        }
        return $return;
    }

    public function get_website($url_adress) {
        $query = $this->db->get_where('website', array('url_adress' => $url_adress));
        return $query->row_array();
    }

    function get_url_without_http($url)
    {
        if(substr($url,0,7) == 'http://')
            $new_url = substr($url,7);
        else if(substr($url,0,8) == 'https://')
            $new_url = substr($url,8);
        else
            $new_url = $url;
        //on supprime le www
        if(substr($new_url,0,4) == 'www.')
            $new_url = substr($new_url,4);
        //on retire un '/' a la fin
        if(substr($new_url,strlen($new_url)-1,1) == '/')
            $new_url = substr($new_url,0,strlen($new_url)-1);
        return $new_url;
    }

    //verifie si une url existe
    function url_exist($url)
    {
        $result = false;
        try
        {
            $client = new Client();
            $crawler = $client->request('GET', $url);
            $results = $crawler->filter('html')->each(function ($node) {
                return $node->text();
            });
            //si on trouve une balise html, donc l�rl existe
            if(count($results))
            {
                $result = true;
            }
        }
        catch (Exception $e){       }
        return $result;
    }
}