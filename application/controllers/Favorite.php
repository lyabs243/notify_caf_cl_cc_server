<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/03/2019
 * Time: 12:44
 */
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . "libraries/Goutte-master/vendor/autoload.php";
require_once  APPPATH . "libraries/url_to_absolute.php";

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

include APPPATH.'controllers/NotifyController.php';
class Favorite extends NotifyController
{
    function __construct()
    {
        // this is your constructor
        parent::__construct();
        $this->load->model('Favorite_model');
        $this->load->model('Website_model');
        $this->load->model('RssFeed_model');
        $this->load->model('User_model');
    }

    public function all($idUser)
    {
        $news['ALL_IN_ONE_NEWS'] = $this->Favorite_model->get_favorites($idUser);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function all_feeds($idUser)
    {
        $news['ALL_IN_ONE_NEWS'] = $this->Favorite_model->get_favorites_feed($idUser);

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function add($idUser,$urlAdress)
    {
        if($this->User_model->is_user_exist($idUser)) {
            $urlAdress = strip_tags($urlAdress);
            $urlAdress = $this->Website_model->get_url_without_http($urlAdress);
            //si le site web n existe pas encore on l ajoute
            if(!$this->Website_model->is_website_exist($urlAdress))
            {
                $favicon = '';
                    //on ajoute le premier article
                if ($this->Website_model->url_exist('http://' . $urlAdress)) {
                    $guzzleClient = new GuzzleClient(array(
                        'timeout' => 60,
                    ));
                    $client = new Client();
                    $client->setClient($guzzleClient);

                    $crawler2 = $client->request('GET', 'http://' . $urlAdress);
                    $favicons = $crawler2->filter('link')->each(function ($node) {
                        if ($node->attr('rel') == 'icon') {
                            return $node->attr('href');
                        } else if ($node->attr('rel') == 'shortcut icon') {
                            return $node->attr('href');
                        }
                        return '';
                    });
                    for ($i = 0; $i < count($favicons); $i++) {
                        if ($favicons[$i] != '') {
                            $favicon = $favicons[$i];
                        }
                    }
                    $favicon = url_to_absolute('http://' . $urlAdress, $favicon);
                    $data['type'] = 1;
                } //on lénregistre comme un hashtag
                else {
                    $data['type'] = 2;
                }
                $data['url_adress'] = $urlAdress;
                $data['url_favicon'] = $favicon;
                $this->Website_model->add_website($data);
            }
            $website = $this->Website_model->get_website($urlAdress);
            if(!$this->Favorite_model->is_favorite_exist($website['id'],$idUser))
            {
                $data['id_user'] = (int)$idUser;
                $data['id_website'] = $website['id'];
                $this->Favorite_model->add_favorite($data);
            }
            $news['ALL_IN_ONE_NEWS'][] = array('success' => '1','url_adress' => $website['url_adress']);
        }
        else
        {
            $news['ALL_IN_ONE_NEWS'][] = array('success' => '0','url_adress' => '');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function add_feed($idUser)
    {
        if($this->User_model->is_user_exist($idUser)) {
            $this->load->library('form_validation');
            // définition des règles de validation
            $this->form_validation->set_rules('url_adress', '« Url Adress »', 'required');

            // ajout du style pour les messages d'erreur
            $this->form_validation->set_error_delimiters('<br /><div class="errorMessage"><span style="font-size: 150%;">&uarr;&nbsp;</span>', '</div>');

            if ($this->form_validation->run() == FALSE) {
                $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
            } else {
                $urlAdress = $this->input->post('url_adress');
                $data_feed['id_user'] = $idUser;
                $urlAdress = strip_tags($urlAdress);
                $url_get_feed_header = 'http://notifygroup.org/api/feedreader/get_feeds_header.php';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url_get_feed_header);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "url_feed=" . $urlAdress
                ."&api_key=cjekisurejhalower");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $server_output = curl_exec($ch);

                curl_close($ch);
                $json = $server_output;
                $feed = json_decode($json, true);

                $data['url_adress'] = $feed[0]['url'];
                $data['title'] = $feed[0]['title'];
                $data['description'] = $feed[0]['description'];
                $data['language'] = $feed[0]['language'];
                //si le site web n existe pas encore on l ajoute
                if (!$this->RssFeed_model->is_rss_feed_exist($data)) {

                    //add website of feed if it doesnt exist
                    //sinon on recupre le website
                    if (!$this->Website_model->is_website_exist($feed[0]['url_website'])) {
                        $data_website['url_adress'] = $feed[0]['url_website'];
                        $data['id_website'] = $this->Website_model->add_website($data_website);
                    } else {
                        $website = $this->Website_model->get_website($feed[0]['url_website']);
                        $data['id_website'] = $website['id'];
                    }
                    $data_feed['id_feed'] = $this->RssFeed_model->add_rss_feed($data);
                } else {
                    $feedResult = $this->RssFeed_model->get_feed($data['url_adress']);
                    $data_feed['id_feed'] = $feedResult['id'];
                }
                if (!$this->Favorite_model->is_favorite_feed_exist($data_feed['id_feed'], $idUser)) {
                    $this->Favorite_model->add_favorite_feed($data_feed);
                }
                $news['ALL_IN_ONE_NEWS'][] = array('success' => '1', 'url_adress' => $urlAdress);
            }
        }
        else
        {
            $news['ALL_IN_ONE_NEWS'][] = array('success' => '0','url_adress' => '');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function multiple_add($idUser=0) {
        if($idUser > 0) {
            $this->load->library('form_validation');
            // définition des règles de validation
            $this->form_validation->set_rules('id_websites', '« Id Website »', 'required');

            // ajout du style pour les messages d'erreur
            $this->form_validation->set_error_delimiters('<br /><div class="errorMessage"><span style="font-size: 150%;">&uarr;&nbsp;</span>', '</div>');

            if ($this->form_validation->run() == FALSE) {
                $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
            } else {
                // succès de la validation : récupération des données passées en post

                $idWebsites = $this->input->post('id_websites');
                $websites = json_decode($idWebsites,true);
                if(count($websites) > 0) {
                    foreach ($websites as $key => $value) {
                        $id = (int)$value['id'];
                        if(!$this->Favorite_model->is_favorite_exist($id,$idUser))
                        {
                            $data['id_user'] = (int)$idUser;
                            $data['id_website'] = $id;
                            $this->Favorite_model->add_favorite($data);
                        }
                    }
                    $news['ALL_IN_ONE_NEWS'][] = array('success' => '1');
                }
                else
                {
                    $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
                }
            }
        }
        else
        {
            $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function delete($idUser=0) {
        if($idUser > 0) {
            $this->load->library('form_validation');
            // définition des règles de validation
            $this->form_validation->set_rules('id_favs', '« Id Favourite »', 'required');

            // ajout du style pour les messages d'erreur
            $this->form_validation->set_error_delimiters('<br /><div class="errorMessage"><span style="font-size: 150%;">&uarr;&nbsp;</span>', '</div>');

            if ($this->form_validation->run() == FALSE) {
                $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
            } else {
                // succès de la validation : récupération des données passées en post

                $id_favs = $this->input->post('id_favs');
                $favourites = json_decode($id_favs,true);
                if(count($favourites) > 0) {
                    foreach ($favourites as $key => $value) {
                        $listFav[] = (int)$value['id'];
                    }
                    $this->Favorite_model->delete_favorite($idUser, $listFav);

                    $news['ALL_IN_ONE_NEWS'][] = array('success' => '1');
                }
                else
                {
                    $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
                }
            }
        }
        else
        {
            $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function delete_feeds($idUser=0) {
        if($idUser > 0) {
            $this->load->library('form_validation');
            // définition des règles de validation
            $this->form_validation->set_rules('id_favs', '« Id Favourite »', 'required');

            // ajout du style pour les messages d'erreur
            $this->form_validation->set_error_delimiters('<br /><div class="errorMessage"><span style="font-size: 150%;">&uarr;&nbsp;</span>', '</div>');

            if ($this->form_validation->run() == FALSE) {
                $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
            } else {
                // succès de la validation : récupération des données passées en post

                $id_favs = $this->input->post('id_favs');
                $favourites = json_decode($id_favs,true);
                if(count($favourites) > 0) {
                    foreach ($favourites as $key => $value) {
                        $listFav[] = (int)$value['id'];
                    }
                    $this->Favorite_model->delete_favorite_feed($idUser, $listFav);

                    $news['ALL_IN_ONE_NEWS'][] = array('success' => '1');
                }
                else
                {
                    $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
                }
            }
        }
        else
        {
            $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function suggest($id_country) {
        $this->load->library('form_validation');
        // définition des règles de validation
        $this->form_validation->set_rules('categories', '« Categories »', 'required');
        $this->form_validation->set_rules('start', '« Start »', 'required');
        $this->form_validation->set_rules('length', '« Length »', 'required');

        $this->form_validation->set_error_delimiters('<br /><div class="errorMessage"><span style="font-size: 150%;">&uarr;&nbsp;</span>', '</div>');
        if ($this->form_validation->run() == FALSE) {
            $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
        } else {
            $start = $this->input->post('start');
            $length = $this->input->post('length');

            $id_categories = $this->input->post('categories');
            $categories = json_decode($id_categories,true);
            if(count($categories) > 0) {
                foreach ($categories as $key => $value) {
                    $listCat[] = (int)$value['id'];
                }
                $news['ALL_IN_ONE_NEWS'] = $this->Favorite_model->get_suggest_favs($id_country,$listCat,$start, $length);
            }
            else
            {
                $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
            }
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function country() {
        $this->load->library('form_validation');
        // définition des règles de validation
        $this->form_validation->set_rules('start', '« Start »', 'required');
        $this->form_validation->set_rules('length', '« Length »', 'required');

        $this->form_validation->set_error_delimiters('<br /><div class="errorMessage"><span style="font-size: 150%;">&uarr;&nbsp;</span>', '</div>');
        if ($this->form_validation->run() == FALSE) {
            $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
        } else {
            $start = $this->input->post('start');
            $length = $this->input->post('length');

            $news['ALL_IN_ONE_NEWS'] = $this->Favorite_model->get_countries($start, $length);
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }

    public function category($id_country) {
        $this->load->library('form_validation');
        // définition des règles de validation
        $this->form_validation->set_rules('start', '« Start »', 'required');
        $this->form_validation->set_rules('length', '« Length »', 'required');

        $this->form_validation->set_error_delimiters('<br /><div class="errorMessage"><span style="font-size: 150%;">&uarr;&nbsp;</span>', '</div>');
        if ($this->form_validation->run() == FALSE) {
            $news['ALL_IN_ONE_NEWS'][] = array('success' => '0');
        } else {
            $start = $this->input->post('start');
            $length = $this->input->post('length');

            $news['ALL_IN_ONE_NEWS'] = $this->Favorite_model->get_categories($id_country,$start, $length);
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode($news,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }
}