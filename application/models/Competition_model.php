<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 07/09/2019
 * Time: 21:04
 */

class Competition_model extends CI_Model
{
    public function __construct() {
        $this->load->database();
    }

    public function get_competition($id) {
        $timezone = $this->session->timezone;
        $sql = "SELECT sc.`id`, sc.`title`,sc.title_small, sc.`description`, sc.`trophy_icon_url`, CONVERT_TZ(sc.`register_date`,@@session.time_zone,?) as register_date 
                FROM `spt_competition` sc
                WHERE sc.id = ?
                ORDER BY sc.id ASC";
        $args = array($timezone,$id);
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $competitions = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            $row['title_small'] = $result->title_small;
            $row['title'] = $this->lang->line($row['title_small']);;
            $row['description'] = $result->description;
            $row['trophy_icon_url'] = $result->trophy_icon_url;
            $row['register_date'] = $result->register_date;

            array_push($competitions,$row);
        }
        return $competitions;
    }

    public function get_competitions($page=1) {
        $timezone = $this->session->timezone;
        $page_start = ((int)$page-1)*10;
        $sql = "SELECT sc.`id`, sc.`title`,sc.title_small, sc.`description`, sc.`trophy_icon_url`, CONVERT_TZ(sc.`register_date`,@@session.time_zone,?) as register_date 
                FROM `spt_competition` sc
                ORDER BY sc.id ASC
                LIMIT ?,10";
        $args = array($timezone,$page_start);
        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $competitions = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            $row['title_small'] = $result->title_small;
            $row['title'] = $this->lang->line($row['title_small']);;
            $row['description'] = $result->description;
            $row['trophy_icon_url'] = $result->trophy_icon_url;
            $row['register_date'] = $result->register_date;

            array_push($competitions,$row);
        }
        return $competitions;
    }
}