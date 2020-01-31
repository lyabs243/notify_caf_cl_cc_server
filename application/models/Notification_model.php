<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 31/01/2020
 * Time: 12:53
 */

class Notification_model extends CI_Model
{
	public function __construct() {
		$this->load->database();
	}

	public function notify_match_goal($idMatch, $teamA, $teamB, $scoredTeam, $teamAGoals, $teamBGoals) {

		$headingEn = "Goal for $scoredTeam !";
		$headingFr = "But pour $scoredTeam !";

		$contentEn = "$teamA $teamAGoals - $teamBGoals $teamB";
		$contentFr = "$teamA $teamAGoals - $teamBGoals $teamB";

		$data['match_id'] = "$idMatch";
		$data['type'] = "0";

		$this->notify($headingEn, $headingFr, $contentEn, $contentFr, $data, 600);
	}

	public function notify_match_start($idMatch, $teamA, $teamB) {

		$headingEn = "$teamA - $teamB";
		$headingFr = "$teamA - $teamB";

		$contentEn = "Kick-Off !";
		$contentFr = "Coup d'envoi !";

		$data['match_id'] = "$idMatch";
		$data['type'] = "0";

		$this->notify($headingEn, $headingFr, $contentEn, $contentFr, $data, 1800);
	}

	public function notify_match_halftime($idMatch, $teamA, $teamB, $teamAGoals, $teamBGoals) {

		$headingEn = "$teamA $teamAGoals - $teamBGoals $teamB";
		$headingFr = "$teamA $teamAGoals - $teamBGoals $teamB";

		$contentEn = "Half Time !";
		$contentFr = "Mi-Temps !";

		$data['match_id'] = "$idMatch";
		$data['type'] = "0";

		$this->notify($headingEn, $headingFr, $contentEn, $contentFr, $data, 600);
	}

	public function notify_match_fulltime($idMatch, $teamA, $teamB, $teamAGoals, $teamBGoals) {

		$headingEn = "$teamA $teamAGoals - $teamBGoals $teamB";
		$headingFr = "$teamA $teamAGoals - $teamBGoals $teamB";

		$contentEn = "Full Time";
		$contentFr = "Fin du match";

		$data['match_id'] = "$idMatch";
		$data['type'] = "0";

		$this->notify($headingEn, $headingFr, $contentEn, $contentFr, $data, 1800);
	}

	public function notify_match_video($idMatch, $teamA, $teamB) {

		$headingEn = "$teamA - $teamB";
		$headingFr = "$teamA - $teamB";

		$contentEn = "Video available";
		$contentFr = "Vidéo disponible";

		$data['match_id'] = "$idMatch";
		$data['type'] = "2";

		$this->notify($headingEn, $headingFr, $contentEn, $contentFr, $data);
	}

	public function notify_match_lineup($idMatch, $teamA, $teamB) {

		$headingEn = "$teamA - $teamB";
		$headingFr = "$teamA - $teamB";

		$contentEn = "Line-Up available";
		$contentFr = "Composition disponible";

		$data['match_id'] = "$idMatch";
		$data['type'] = "1";

		$this->notify($headingEn, $headingFr, $contentEn, $contentFr, $data, 1800);
	}

	//notify via onesignal
	public function notify($headingEn, $headingFr, $contentEn, $contentFr, $data, $timeToLive=259200) {

		$contents = array(
			'en' => $contentEn,
			'fr' => $contentFr
		);

		$headings = array(
			'en' => $headingEn,
			'fr' => $headingFr
		);

		$fields = array(
			'app_id' => '11010fc6-b149-46a0-89f6-1ec83193e7ff',
			'included_segments' => array('All'),
			'data' => $data,
			'headings'=> $headings,
			'contents' => $contents,
			'ttl' => $timeToLive
		);

		$fields = json_encode($fields);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
			'Authorization: Basic '.'MGJiZDk4ODktMzA3MS00NWEzLTg5ZTMtZWUxNmI3NjZlYWY5'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
	}
}