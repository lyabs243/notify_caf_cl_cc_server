<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 10/03/2019
 * Time: 06:49
 */

class Article_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
    }

    public function view_article($id_article, $id_user)
    {
        $result = 0;
        if(!$this->is_user_viewed_article($id_article,$id_user)) {
            $sql = 'INSERT INTO `article_view`(`id_article`, `id_user`) VALUES (?, ?)';
            $args = array($id_article, $id_user);
            $result = $this->db->query($sql, $args);
        }
        return $result;
    }

    public function get_trend_news($idUser,$forFeed,$idCompetitionType=0,$lang='en') {
        if($idCompetitionType)
        {
            $timezone = $this->session->timezone;
            $sql = "SELECT a.id, a.url_adress as url_article, 0 as cat_id, 'image' as news_type,a.title as news_heading,a.description as news_description,
                0 as news_video_id,'' as news_video_url,CONVERT_TZ(a.`publication_date`,@@session.time_zone,?) as news_date,a.url_img as news_featured_image,
                (SELECT COUNT(*) FROM article_view WHERE id_article = a.id) as total_views, 
                0 as cid, 'General' as category_name, w.url_adress as url_fav
                FROM `article`a
                JOIN website w
                ON a.id_website = w.id
                JOIN rss_feed rf 
                ON rf.id = a.id_rss_feed
                left JOIN article_view av 
                ON a.`id` = av.id_article
                WHERE a.id_rss_feed IN 
                (
                    SELECT id_feed FROM  spt_competition_news_rss_feed
                    WHERE id_competition_category = ?
                )
                AND w.lang = ?
                AND a.register_date >= DATE( DATE_SUB( NOW() , INTERVAL 365 DAY ) )
                GROUP BY id
                ORDER BY total_views desc 
                LIMIT 5";
        }
        else if($forFeed)
        {
            $sql = "SELECT a.id, 0 as cat_id, 'image' as news_type,a.title as news_heading,a.description as news_description,
                0 as news_video_id,'' as news_video_url,CONVERT_TZ(a.`register_date`,@@session.time_zone,'+00:00') as news_date,a.url_img as news_featured_image,
                (SELECT COUNT(*) FROM article_view WHERE id_article = a.id) as total_views,
                0 as cid, 'General' as category_name, rf.title as url_fav
                FROM `article`a
                JOIN website w
                ON a.id_website = w.id
                JOIN rss_feed rf
                ON rf.id_website = w.id
                left JOIN article_view av 
                ON a.`id` = av.id_article
                WHERE a.id_rss_feed IN 
                (
                    SELECT id_feed FROM favorite_feed
                    WHERE id_user = ?
                )
                AND  a.id_rss_feed > 0
                AND a.register_date >= DATE( DATE_SUB( NOW() , INTERVAL 1 DAY ) )
                GROUP BY id
                ORDER BY total_views desc 
                LIMIT 5";
        }
        else {
            $sql = "SELECT a.id, 0 as cat_id, 'image' as news_type,a.title as news_heading,a.description as news_description,
                0 as news_video_id,'' as news_video_url,CONVERT_TZ(a.`register_date`,@@session.time_zone,'+00:00') as news_date,a.url_img as news_featured_image,
                (SELECT COUNT(*) FROM article_view WHERE id_article = a.id) as total_views,
                0 as cid, 'General' as category_name, w.url_adress as url_fav
                FROM `article`a
                JOIN website w
                ON a.id_website = w.id
                left JOIN article_view av 
                ON a.`id` = av.id_article
                WHERE w.id IN 
                (
                    SELECT id_website FROM favorite
                    WHERE id_user = ?
                )
                AND a.register_date >= DATE( DATE_SUB( NOW() , INTERVAL 1 DAY ) )
                GROUP BY id
                ORDER BY total_views desc 
                LIMIT 5";
        }
        if($idCompetitionType)
        {
            $args = array($timezone,$idCompetitionType,$lang);
        }
        else
        {
            $args = array($idUser);
        }
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $news = array();

        $i = 1;
        foreach ($results as $result)
        {
            $row = array();

                $row['id'] = $result->id;
                $row['url_share'] = 'http://news.notifygroup.org/' . $row['id'];
                $row['url_article'] = $result->url_article;
                $row['cat_id'] = $result->cat_id;
                $row['url_fav'] = $result->url_fav;
                $row['news_type'] = $result->news_type;
                $row['news_heading'] = strip_tags($result->news_heading);
                $row['news_description'] = strip_tags($result->news_description);
                $row['news_video_id'] = $result->news_video_id;
                $row['news_video_url'] = $result->news_video_url;
                $row['news_date'] = $result->news_date;
                $row['news_featured_image'] = $result->news_featured_image;
                $row['total_views'] = $result->total_views;
                $row['cid'] = $result->cid;
                $row['category_name'] = $result->category_name;

            $i++;

            array_push($news,$row);
        }
        return $news;
    }

    public function get_latest_news($idUser,$page=0,$idWebsite=0,$forFeed=0,$idCompetitionType=0,$lang='en') {
        $lastViewedArticle = $this->User_model->get_latest_viewed_article($idUser);
        $timezone = $this->session->timezone;
        if($idCompetitionType)
        {
            $sql = "SELECT a.id, a.url_adress as url_article, 0 as cat_id, 'image' as news_type,a.title as news_heading,a.description as news_description,
                0 as news_video_id,'' as news_video_url,CONVERT_TZ(a.`publication_date`,@@session.time_zone,?) as news_date,a.url_img as news_featured_image,
                (SELECT COUNT(*) FROM article_view WHERE id_article = a.id) as total_views,
                0 as cid, 'General' as category_name, w.url_adress as url_fav
                FROM `article`a
                JOIN website w
                ON a.id_website = w.id
                left JOIN article_view av 
                ON a.`id` = av.id_article
                WHERE a.id_rss_feed IN 
                (
                    SELECT id_feed FROM  spt_competition_news_rss_feed
                    WHERE id_competition_category = ?
                )
                AND w.lang = ?
                 ";
        }
        else if($forFeed)
        {
            $sql = "SELECT a.id, 0 as cat_id, 'image' as news_type,a.title as news_heading,a.description as news_description,
                0 as news_video_id,'' as news_video_url,CONVERT_TZ(a.`register_date`,@@session.time_zone,'+00:00') as news_date,a.url_img as news_featured_image,
                (SELECT COUNT(*) FROM article_view WHERE id_article = a.id) as total_views,
                0 as cid, 'General' as category_name, rf.title as url_fav
                FROM `article`a
                JOIN website w
                ON a.id_website = w.id
                JOIN rss_feed rf
                ON rf.id_website = w.id
                left JOIN article_view av 
                ON a.`id` = av.id_article
                WHERE a.id_rss_feed IN 
                (
                    SELECT id_feed FROM favorite_feed
                    WHERE id_user = ?
                )
                AND a.id_rss_feed > 0
                 ";
        }
        else {
            $sql = "SELECT a.id, 0 as cat_id, 'image' as news_type,a.title as news_heading,a.description as news_description,
                0 as news_video_id,'' as news_video_url,CONVERT_TZ(a.`publication_date`,@@session.time_zone,'+00:00') as news_date,a.url_img as news_featured_image,
                (SELECT COUNT(*) FROM article_view WHERE id_article = a.id) as total_views,
                0 as cid, 'General' as category_name, w.url_adress as url_fav
                FROM `article`a
                JOIN website w
                ON a.id_website = w.id
                left JOIN article_view av 
                ON a.`id` = av.id_article
                WHERE w.id IN 
                (
                    SELECT id_website FROM favorite
                    WHERE id_user = ?
                )
                 ";
        }

        if($page <= 0) {
            $sql .= "AND a.register_date >= DATE( DATE_SUB( NOW() , INTERVAL 1 DAY ) )
                    ORDER BY news_date desc
                    LIMIT 5";
            $args = array($idUser);
        }
        else {
            $page_start = (((int)$page)-1)*10;

            if($idWebsite > 0)
            {
                if($forFeed)
                {
                    $sql .= " AND rf.id = ? ";
                }
                else {
                    $sql .= " AND w.id = ? ";
                }
                $args = array($idUser,$idWebsite,$page_start);
            }
            else
            {
                if($idCompetitionType)
                {
                    $args = array($timezone,$idCompetitionType,$lang,$page_start);
                }
                else
                {
                    $args = array($idUser,$page_start);
                }
            }
            if($idCompetitionType)
            {
                $sql .= "AND a.register_date >= DATE( DATE_SUB( NOW() , INTERVAL 365 DAY ) )";
            }
            else
            {
                $sql .= "AND a.register_date >= DATE( DATE_SUB( NOW() , INTERVAL 30 DAY ) )";
            }
            $sql .= "ORDER BY news_date desc
                     LIMIT ?,10";
        }
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $news = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            if($row['id'] > $lastViewedArticle)
            {
                $lastViewedArticle = $row['id'];
            }
            $row['url_share'] = 'http://news.notifygroup.org/'.$row['id'];
            $row['url_article'] = $result->url_article;
            $row['cat_id'] = $result->cat_id;
            $row['url_fav'] = $result->url_fav;
            $row['news_type'] = $result->news_type;
            $row['news_heading'] = $result->news_heading;
            $row['news_description'] = $result->news_description;
            $row['news_video_id'] = $result->news_video_id;
            $row['news_video_url'] = $result->news_video_url;
            $row['news_date'] = $result->news_date;
            $row['news_featured_image'] = $result->news_featured_image;
            $row['total_views'] = $result->total_views;
            $row['cid'] = $result->cid;
            $row['category_name'] = $result->category_name;

            array_push($news,$row);
        }

        //update user params
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['last_viewed_article'] = $lastViewedArticle;
        $data['last_connexion'] = date("Y-m-d H:i:s");
        $this->User_model->update_user($idUser,$data);

        return $news;
    }

    public function notify_news($idUser,$forFeed=0,$idCompetition=0,$lang='en') {
        $lastViewedArticle = $this->User_model->get_latest_viewed_article($idUser);
        if($forFeed)
        {
            $sql = "SELECT a.id, 0 as cat_id, 'image' as news_type,a.title as news_heading,a.description as news_description,
                0 as news_video_id,'' as news_video_url,CONVERT_TZ(a.`register_date`,@@session.time_zone,'+00:00') as news_date,a.url_img as news_featured_image,
                (SELECT COUNT(*) FROM article_view WHERE id_article = a.id) as total_views,
                0 as cid, 'General' as category_name, rf.title as url_fav
                FROM `article`a
                JOIN website w
                ON a.id_website = w.id
                JOIN rss_feed rf
                ON rf.id_website = w.id
                JOIN favorite_feed ff 
                ON ff.id_feed = rf.id
                left JOIN article_view av 
                ON a.`id` = av.id_article
                WHERE a.`register_date` >= DATE_SUB(NOW(),INTERVAL 1 HOUR)
                        AND ff.id_user = ?
                        AND a.`id` > ?
                        AND
                        (
                            a.`id_rss_feed` IN
                            (
                                SELECT id_feed FROM favorite_feed
                                WHERE id_user = ?
                            )
                        )
                        ORDER BY total_views DESC
                        LIMIT 1
                 ";
        }
        elseif ($idCompetition)
        {
            $sql = "SELECT a.id, 0 as cat_id, 'image' as news_type,a.title as news_heading,a.description as news_description,
                0 as news_video_id,'' as news_video_url,CONVERT_TZ(a.`register_date`,@@session.time_zone,'+00:00') as news_date,a.url_img as news_featured_image,
                (SELECT COUNT(*) FROM article_view WHERE id_article = a.id) as total_views,
                0 as cid, 'General' as category_name, w.url_adress as url_fav
                FROM `article`a
                JOIN website w
                ON a.id_website = w.id
                JOIN spt_competition_news scn
                ON a.id = scn.id_article
                left JOIN article_view av 
                ON a.`id` = av.id_article
                WHERE a.`register_date` >= DATE_SUB(NOW(),INTERVAL 2 DAY)
                        AND a.`id` > ?
                        AND scn.id_competition = ?
                        AND scn.lang = ?
                        ORDER BY total_views DESC
                        LIMIT 1";
        }
        else {
            $sql = "SELECT a.id, 0 as cat_id, 'image' as news_type,a.title as news_heading,a.description as news_description,
                0 as news_video_id,'' as news_video_url,CONVERT_TZ(a.`register_date`,@@session.time_zone,'+00:00') as news_date,a.url_img as news_featured_image,
                (SELECT COUNT(*) FROM article_view WHERE id_article = a.id) as total_views,
                0 as cid, 'General' as category_name, w.url_adress as url_fav
                FROM `article`a
                JOIN website w
                ON a.id_website = w.id
                JOIN favorite f 
                ON f.id_website = w.id
                left JOIN article_view av 
                ON a.`id` = av.id_article
                WHERE a.`register_date` >= DATE_SUB(NOW(),INTERVAL 1 HOUR)
                        AND f.id_user = ?
                        AND a.`id` > ?
                        AND
                        (
                            a.`id_rss_feed` IN
                            (
                                SELECT id_rss_feed FROM user_rss_feed
                                WHERE  id_user = ?
                            )
                            OR
                            a.`id_rss_feed` <= 0
                        )
                        ORDER BY total_views DESC
                        LIMIT 1
                 ";
        }
        if($idCompetition)
        {
            $args = array($lastViewedArticle, $idCompetition, $lang);
        }
        else {
            $args = array($idUser, $lastViewedArticle, $idUser);
        }
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $news = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            if($row['id'] > $lastViewedArticle)
            {
                $lastViewedArticle = $row['id'];
            }
            $row['url_share'] = 'http://news.notifygroup.org/'.$row['id'];
            $row['url_article'] = 'http://news.notifygroup.org/q/'.$row['id'];
            $row['cat_id'] = $result->cat_id;
            $row['url_fav'] = $result->url_fav;
            $row['news_type'] = $result->news_type;
            $row['news_heading'] = $result->news_heading;
            $row['news_description'] = $result->news_description;
            $row['news_video_id'] = $result->news_video_id;
            $row['news_video_url'] = $result->news_video_url;
            $row['news_date'] = $result->news_date;
            $row['news_featured_image'] = $result->news_featured_image;
            $row['total_views'] = $result->total_views;
            $row['cid'] = $result->cid;
            $row['category_name'] = $result->category_name;

            array_push($news,$row);
        }

        //update user params
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['last_viewed_article'] = $lastViewedArticle;
        $data['last_connexion'] = date("Y-m-d H:i:s");
        $this->User_model->update_user($idUser,$data);

        return $news;
    }

    public function get_competition_news_keywords($idCompetition,$requireApproval)
    {
        $sql = "SELECT id_competition, keyword, require_approval 
                FROM `spt_competition_news_keyword`
                WHERE id_competition = ?
                AND require_approval = ?";
        $args = array($idCompetition,$requireApproval);
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $data = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id_competition'] = $result->id_competition;
            $row['keyword'] = $result->keyword;
            $row['require_approval'] = $result->require_approval;

            array_push($data,$row);
        }

        return $data;
    }

    public function competion_add_news_pending_approval($idCompetition)
    {
        $keywords = $this->get_competition_news_keywords($idCompetition,1);
        if(count($keywords)) {
            $sql = 'INSERT INTO spt_competition_news
                (id_competition, id_article, lang, status)
                SELECT 1, a.id, w.lang, 0
                FROM article a
                JOIN website w
                ON w.id = a.id_website 
                WHERE w.public = 1 
                AND a.id > (SELECT max(id_article) FROM spt_competition_news WHERE status = 0)
                AND a.register_date >= DATE_SUB(NOW(),INTERVAL 1 HOUR )
                AND a.id NOT IN
                (
                  SELECT id_article FROM spt_competition_news WHERE id_competition = ?
                )
                AND (';
            $args = array($idCompetition);
            foreach ($keywords as $index => $keyword) {
                if ($index > 0) {
                    $sql .= ' OR ';
                }
                $sql .= ' LOWER(a.title) LIKE ? ';
                $sql .= ' OR LOWER(a.description) LIKE ? ';
                array_push($args, '%' . $keyword['keyword'] . '%');
                array_push($args, '%' . $keyword['keyword'] . '%');
            }
            echo $sql;
            var_dump($args);
            $sql .= ')';
            $this->db->query($sql, $args);
        }
    }

    public function competion_add_news($idCompetition)
    {
        $keywords = $this->get_competition_news_keywords($idCompetition,0);
        if(count($keywords)) {
            $sql = 'INSERT INTO spt_competition_news
                (id_competition, id_article, lang, status)
                SELECT 1, a.id, w.lang, 0
                FROM article a
                JOIN website w
                ON w.id = a.id_website 
                WHERE w.public = 1 
                AND a.id > (SELECT max(id_article) FROM spt_competition_news WHERE status = 1)
                AND a.register_date >= DATE_SUB(NOW(),INTERVAL 1 MONTH)
                AND (';
            $args = array();
            foreach ($keywords as $index => $keyword) {
                if ($index > 0) {
                    $sql .= ' OR ';
                }
                $sql .= ' LOWER(a.title) LIKE ? ';
                $sql .= ' OR LOWER(a.description) LIKE ? ';
                array_push($args, '%' . $keyword['keyword'] . '%');
                array_push($args, '%' . $keyword['keyword'] . '%');
            }
            echo $sql;
            var_dump($args);
            $sql .= ')';
            $this->db->query($sql, $args);
        }
    }

    public function search_news($idUser,$searchText,$page,$forFeed=0) {

        $searchText = '%'.strtolower(htmlspecialchars($searchText)).'%';
        if($forFeed)
        {
            $sql = "SELECT a.id, 0 as cat_id, 'image' as news_type,a.title as news_heading,a.description as news_description,
                0 as news_video_id,'' as news_video_url,CONVERT_TZ(a.`register_date`,@@session.time_zone,'+00:00') as news_date,a.url_img as news_featured_image,
                (SELECT COUNT(*) FROM article_view WHERE id_article = a.id) as total_views,
                0 as cid, 'General' as category_name, rf.title as url_fav
                FROM `article`a
                JOIN website w
                ON a.id_website = w.id
                JOIN rss_feed rf
                ON rf.id_website = w.id
                left JOIN article_view av 
                ON a.`id` = av.id_article
                WHERE a.id_rss_feed > 0
                AND rf.active = 1 
                 ";
        }
        else {
            $sql = "SELECT a.id, 0 as cat_id, 'image' as news_type,a.title as news_heading,a.description as news_description,
                0 as news_video_id,'' as news_video_url,CONVERT_TZ(a.`register_date`,@@session.time_zone,'+00:00') as news_date,a.url_img as news_featured_image,
                (SELECT COUNT(*) FROM article_view WHERE id_article = a.id) as total_views,
                0 as cid, 'General' as category_name, w.url_adress as url_fav
                FROM `article`a
                JOIN website w
                ON a.id_website = w.id
                left JOIN article_view av 
                ON a.`id` = av.id_article
                WHERE w.public = 1 
                 ";
        }
        $page_start = (((int)$page)-1)*10;
        $args = array($searchText,$searchText,$page_start);
        $sql .= " AND (LOWER(a.title) like ? OR LOWER(a.description) like ?)
                  AND a.register_date >= DATE( DATE_SUB( NOW() , INTERVAL 30 DAY ) )
                  ORDER BY news_date desc
                  LIMIT ?,10";
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $news = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            $row['url_share'] = 'http://news.notifygroup.org/'.$row['id'];
            $row['url_article'] = 'http://news.notifygroup.org/q/'.$row['id'];
            $row['cat_id'] = $result->cat_id;
            $row['url_fav'] = $result->url_fav;
            $row['news_type'] = $result->news_type;
            $row['news_heading'] = $result->news_heading;
            $row['news_description'] = $result->news_description;
            $row['news_video_id'] = $result->news_video_id;
            $row['news_video_url'] = $result->news_video_url;
            $row['news_date'] = $result->news_date;
            $row['news_featured_image'] = $result->news_featured_image;
            $row['total_views'] = $result->total_views;
            $row['cid'] = $result->cid;
            $row['category_name'] = $result->category_name;

            array_push($news,$row);
        }
        return $news;
    }

    public function get_top_story($idUser) {
        $lastViewedArticle = $this->User_model->get_latest_viewed_article($idUser);
        $sql = "SELECT a.id, 0 as cat_id, 'image' as news_type,a.title as news_heading,a.description as news_description,
                0 as news_video_id,'' as news_video_url,CONVERT_TZ(a.`register_date`,@@session.time_zone,'+00:00') as news_date,a.url_img as news_featured_image,
                (SELECT COUNT(*) FROM article_view WHERE id_article = a.id) as total_views,
                0 as cid, 'General' as category_name, w.url_adress as url_fav
                FROM `article`a
                JOIN website w
                ON a.id_website = w.id
                left JOIN article_view av 
                ON a.`id` = av.id_article
                WHERE w.id IN 
                (
                    SELECT id_website FROM favorite
                    WHERE id_user = ?
                )
                AND a.register_date >= DATE( DATE_SUB( NOW() , INTERVAL 1 DAY ) )
                ORDER BY RAND()
                LIMIT 5";
        $query = $this->db->query($sql,array($idUser));
        $results = $query->result();
        $news = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            if($row['id'] > $lastViewedArticle)
            {
                $lastViewedArticle = $row['id'];
            }
            $row['url_share'] = 'http://news.notifygroup.org/'.$row['id'];
            $row['url_article'] = 'http://news.notifygroup.org/q/'.$row['id'];
            $row['cat_id'] = $result->cat_id;
            $row['url_fav'] = $result->url_fav;
            $row['news_type'] = $result->news_type;
            $row['news_heading'] = $result->news_heading;
            $row['news_description'] = $result->news_description;
            $row['news_video_id'] = $result->news_video_id;
            $row['news_video_url'] = $result->news_video_url;
            $row['news_date'] = $result->news_date;
            $row['news_featured_image'] = $result->news_featured_image;
            $row['total_views'] = $result->total_views;
            $row['cid'] = $result->cid;
            $row['category_name'] = $result->category_name;

            array_push($news,$row);
        }

        //update user params
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['last_viewed_article'] = $lastViewedArticle;
        $data['last_connexion'] = date("Y-m-d H:i:s");
        $this->User_model->update_user($idUser,$data);

        return $news;
    }

    //verifie si u article existe deja
    function  is_article_exist($url_adress)
    {
        $return = false;
        $query = $this->db->query('SELECT * FROM article WHERE url_adress = ?'
            ,array($url_adress));
        $results = $query->result();
        foreach ($results as $result)
        {
            $return = true;
        }
        return $return;
    }

    function  is_user_viewed_article($id_article, $id_user)
    {
        $return = false;
        $query = $this->db->query('SELECT * FROM article_view WHERE id_article = ? AND id_user = ?'
            ,array($id_article,$id_user));
        $results = $query->result();
        foreach ($results as $result)
        {
            $return = true;
        }
        return $return;
    }

    public function add_from_jsonfeed($json)
    {
        $sql = 'INSERT INTO `article`(`id_website`, `url_adress`, `url_img`, `title`, `description`, 
              `publication_date`, `id_rss_feed`)
                VALUES (?,?,?,?,?,?,?)';
        $feeds = json_decode($json);

        foreach($feeds as $feed){
            foreach($feed->items as $article) {
                if (!$this->is_article_exist($article->link)) {
                    $args = array($feed->id_website, $article->link, $article->img_url, $article->title, $article->description,
                        $article->date_time, $feed->id_rss_feed);

                    echo $this->db->query($sql, $args) . '<br>';
                }
            }
        }
    }


}