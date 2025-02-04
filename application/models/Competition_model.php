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
        $sql = "SELECT sc.`id`, sc.`title`,sc.title_small, sc.`description`, sc.`trophy_icon_url`, sc.category,
                CONVERT_TZ(sc.`register_date`,@@session.time_zone,?) as register_date 
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
	        $row['title'] = $result->title;
	        if($this->lang->line($row['title_small'])) {
		        $row['title'] = $this->lang->line($row['title_small']);
	        }
            $row['description'] = $result->description;
            $row['trophy_icon_url'] = $result->trophy_icon_url;
            $row['category'] = $result->category;
            $row['register_date'] = $result->register_date;

            array_push($competitions,$row);
        }
        return $competitions;
    }

    public function get_competitions($category=0,$page=1) {
        $timezone = $this->session->timezone;
        $page_start = ((int)$page-1)*10;
        $sql = "SELECT sc.`id`, sc.`title`,sc.title_small, sc.`description`, sc.`trophy_icon_url`, sc.category,
                CONVERT_TZ(sc.`register_date`,@@session.time_zone,?) as register_date 
                FROM `spt_competition` sc
                ";
        if($category){
            $sql .= "WHERE sc.category = ? ";
        }
        $sql .= "ORDER BY sc.id ASC
                LIMIT ?,10";
        if($category){
            $args = array($timezone,$category,$page_start);
        }
        else{
            $args = array($timezone,$page_start);
        }

        $query = $this->db->query($sql,$args);
        $results = $query->result();
        $competitions = array();
        foreach ($results as $result)
        {
            $row = array();
            $row['id'] = $result->id;
            $row['title_small'] = $result->title_small;
	        $row['title'] = $result->title;
	        if($this->lang->line($row['title_small'])) {
		        $row['title'] = $this->lang->line($row['title_small']);
	        }
            $row['description'] = $result->description;
            $row['trophy_icon_url'] = $result->trophy_icon_url;
            $row['category'] = $result->category;
            $row['register_date'] = $result->register_date;

            array_push($competitions,$row);
        }
        return $competitions;
    }

	/**
	 * Get competitions in order of their next ascending match
	 *
	 * @param $category
	 * @return array
	 */
	public function get_featured_competitions($category) {
		$timezone = $this->session->timezone;
		$sql = "SELECT sc.id, sc.title, sc.title_small, sc.description, sc.trophy_icon_url, sc.category,
 				CONVERT_TZ(sc.`register_date`,@@session.time_zone,?) as register_date
                FROM spt_match sm
                JOIN spt_edition_stage ses 
                ON ses.id = sm.id_edition_stage
                JOIN spt_competition_edition sce 
                ON sce.id = ses.id_edition 
                JOIN spt_competition sc 
                ON sc.id = sce.id_competition
                WHERE (sm.status = 0 AND sm.match_date >= DATE(NOW()))
                AND ses.visible > 0
                AND sc.category = ?
                GROUP BY id
                ORDER BY sm.match_date ASC
				LIMIT 5";
		$args = array($timezone,$category);

		$query = $this->db->query($sql,$args);
		$results = $query->result();
		$competitions = array();
		foreach ($results as $result)
		{
			$row = array();
			$row['id'] = $result->id;
			$row['title_small'] = $result->title_small;
			$row['title'] = $result->title;
			if($this->lang->line($row['title_small'])) {
				$row['title'] = $this->lang->line($row['title_small']);
			}
			$row['description'] = $result->description;
			$row['trophy_icon_url'] = $result->trophy_icon_url;
			$row['category'] = $result->category;
			$row['register_date'] = $result->register_date;

			array_push($competitions,$row);
		}
		return $competitions;
	}
}