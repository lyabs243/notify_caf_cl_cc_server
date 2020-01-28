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
            $row['id'] = $result->id;
            $row['id_website'] = $result->id_website;
            $row['url_adress'] = $result->url_adress;

            array_push($feeds,$row);
        }
        return $feeds;
    }

	function get_image_from_html($html_string){
		$src = '';
		if(strlen($html_string) > 0){
			$html = str_get_html($html_string);
			foreach($html->find('img') as $element) {
				$src = $element->src;
				break;
			}
		}
		return $src;
	}

	function get_image($item){
		$src = '';
		$html_encoded = (string)$item->{'content:encoded'};
		$html_description = (string)$item->description;
		//get it in encoded tag
		if(strlen($html_encoded) > 0){
			$src = $this->get_image_from_html($html_encoded);
		}
		if(strlen($src) == 0){
			$src = $this->get_image_from_html($html_description);
		}
		if(strlen($src) == 0){
			$src = $item->enclosure['url'];
		}
		return $src;
	}

	function add_feeds_items() {
		$this->load->model('Article_model');
		$feeds = $this->get_feeds();
		foreach($feeds as $feed){

			$rss = Feed::loadRss($feed['url_adress']);
			$data = array();
			foreach ($rss->item as $item) {
				$row = array();
				$row['title'] = (string)$item->title;
				$row['link'] = (string)$item->link;
				$row['timestamp'] = (string)$item->timestamp;
				$row['date_time'] = date('Y-m-d H:i:s', $row['timestamp']);
				$row['description'] = (string)$item->description;
				$row['encoded'] = (string)$item->{'content:encoded'};
				$row['img_url'] = (string)$this->get_image($item);

				array_push($data,$row);
			}

			$result[] = array(
				'feed' => $feed['url_adress'],
				'id_rss_feed' => $feed['id'],
				'id_website' => $feed['id_website'],
				'title' => (string)$rss->title,
				'description' => (string)$rss->description,
				'link' => (string)$rss->link,
				'items' => $data
			);
		}

		$feeds = json_encode($result,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		$this->Article_model->add_from_jsonfeed($feeds);
	}

}