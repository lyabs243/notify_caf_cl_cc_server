<?php
/**
 * Created by PhpStorm.
 * User: lyabs
 * Date: 31/01/2020
 * Time: 12:53
 */

require_once APPPATH . "libraries/vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;
class Notification_model extends CI_Model
{
	const  TYPE_GOAL = 1;
	const  TYPE_LINEUP = 2;
	const  TYPE_MATCHSTART = 3;
	const  TYPE_MATCHEND = 4;

	public function __construct() {
		$this->load->database();
	}

	public function notify_match_goal($idMatch, $teamA, $teamB, $scoredTeam, $teamAGoals, $teamBGoals, $countryCode=null,
	                                  $competition='', $competitionStage='') {
		if(!$this->is_notification_exist($idMatch, Notification_model::TYPE_GOAL,
			"$teamAGoals-$teamBGoals")) {
			$headingEn = "Goal for $scoredTeam !";
			$headingFr = "But pour $scoredTeam !";

			$contentEn = "$teamA $teamAGoals - $teamBGoals $teamB";
			$contentFr = "$teamA $teamAGoals - $teamBGoals $teamB";

			$data['match_id'] = "$idMatch";
			$data['type'] = "0";

			$this->notify($headingEn, $headingFr, $contentEn, $contentFr, $data, 600, $countryCode);

			//tweet
			$this->tweetMatch($headingEn, $contentEn, $competition, $competitionStage, $teamA, $teamB);
		}
	}

	public function notify_match_start($idMatch, $teamA, $teamB, $countryCode=null, $competition='', $competitionStage='') {
		if(!$this->is_notification_exist($idMatch, Notification_model::TYPE_MATCHSTART)) {
			$headingEn = "$teamA - $teamB";
			$headingFr = "$teamA - $teamB";

			$contentEn = "Kick-Off !";
			$contentFr = "Coup d'envoi !";

			$data['match_id'] = "$idMatch";
			$data['type'] = "0";

			$this->notify($headingEn, $headingFr, $contentEn, $contentFr, $data, 1800, $countryCode);

			//tweet
			$this->tweetMatch($headingEn, $contentEn, $competition, $competitionStage, $teamA, $teamB);
		}
	}

	public function notify_secondhalf_start($idMatch, $teamA, $teamB, $countryCode=null, $competition='', $competitionStage='') {

		$headingEn = "$teamA - $teamB";
		$headingFr = "$teamA - $teamB";

		$contentEn = "Second half start !";
		$contentFr = "Début deuxième Mi-Temps !";

		$data['match_id'] = "$idMatch";
		$data['type'] = "0";

		$this->notify($headingEn, $headingFr, $contentEn, $contentFr, $data, 1800, $countryCode);

		//tweet
		$this->tweetMatch($headingEn, $contentEn, $competition, $competitionStage, $teamA, $teamB);
	}

	public function notify_match_halftime($idMatch, $teamA, $teamB, $teamAGoals, $teamBGoals, $countryCode=null,
	                                      $competition='', $competitionStage='') {

		$headingEn = "$teamA $teamAGoals - $teamBGoals $teamB";
		$headingFr = "$teamA $teamAGoals - $teamBGoals $teamB";

		$contentEn = "Half Time !";
		$contentFr = "Mi-Temps !";

		$data['match_id'] = "$idMatch";
		$data['type'] = "0";

		$this->notify($headingEn, $headingFr, $contentEn, $contentFr, $data, 600, $countryCode);

		//tweet
		$this->tweetMatch($headingEn, $contentEn, $competition, $competitionStage, $teamA, $teamB);
	}

	public function notify_match_fulltime($idMatch, $teamA, $teamB, $teamAGoals, $teamBGoals, $competition='', $competitionStage='') {
		if(!$this->is_notification_exist($idMatch, Notification_model::TYPE_MATCHEND)) {
			$headingEn = "$teamA $teamAGoals - $teamBGoals $teamB";
			$headingFr = "$teamA $teamAGoals - $teamBGoals $teamB";

			$contentEn = "Full Time";
			$contentFr = "Fin du match";

			$data['match_id'] = "$idMatch";
			$data['type'] = "0";

			$this->notify($headingEn, $headingFr, $contentEn, $contentFr, $data, 1800);

			//tweet
			$this->tweetMatch($headingEn, $contentEn, $competition, $competitionStage, $teamA, $teamB);
		}
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

	public function notify_match_lineup($idMatch, $teamA, $teamB, $countryCode=null) {
		if(!$this->is_notification_exist($idMatch, Notification_model::TYPE_LINEUP)) {
			$headingEn = "$teamA - $teamB";
			$headingFr = "$teamA - $teamB";

			$contentEn = "Line-Up available";
			$contentFr = "Composition disponible";

			$data['match_id'] = "$idMatch";
			$data['type'] = "1";

			$this->notify($headingEn, $headingFr, $contentEn, $contentFr, $data, 1800, $countryCode);
		}
	}

	function  is_notification_exist($idMatch, $notificationType, $score='?-?')
	{
		$id = 0;
		$query = $this->db->query('SELECT * FROM spt_match_notification_history 
									WHERE id_match = ? AND type = ? AND score = ? ',
			array($idMatch, $notificationType, $score));
		$results = $query->result();
		foreach ($results as $result)
		{
			$id = $result->id;
			break;
		}

		if(!$id) {
			$this->db->insert('spt_match_notification_history', array('id_match' => $idMatch,
				'type' => $notificationType, 'score' => $score));
		}
		return $id;
	}

	//notify via onesignal
	public function notify($headingEn, $headingFr, $contentEn, $contentFr, $data, $timeToLive=259200, $countryCode=null) {

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

		if($countryCode != null) {
			$fields['filters'] = array(array("field" => "country",  "relation" => "=", "value" => $countryCode),);
		}

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

	function tweetMatch($heading, $content, $competition, $competitionStage, $teamA, $teamB) {

		if (!defined('CONSUMER_KEY')) define('CONSUMER_KEY', 'uQdL3Jhi5zfONirpRtOjpQ8bU');
		if (!defined('CONSUMER_SECRET')) define('CONSUMER_SECRET', 'I9zTB6omtP3UyFga7YzmZxbvw5gcY3yrupiitmdAZY9J9jFkJP');
		if (!defined('ACCESS_TOKEN')) define('ACCESS_TOKEN', '982963896100249600-5mA2pdZM8ohSveGuqzeXKK7CSGKm97Y');
		if (!defined('ACCESS_TOKEN_SECRET')) define('ACCESS_TOKEN_SECRET', 'bJAswN48kPEk7dy4c2nDSv14nVxvsJ9ytGwMRIhds9PsH');

		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

		$status = "
				$competition - $competitionStage
	
				$heading
	
				$content
	
				Enjoy live score by installing for free Notify Afrofoot on http://bit.ly/2TjUrj1
				#football #africa";
		$competitionStage = $this->sentenceToHashTags($competition);
		foreach ($competitionStage as $tag) {
			$status .= ' ' . $tag;
		}
		$status .= ' #' . $this->getLongestWord($teamA);
		$status .= ' #' . $this->getLongestWord($teamB);
		$statusLength = strlen($status);
		$fields['status'] = substr($status, 0, 280);
		$nbTweets = ceil($statusLength / 280);

		//create space for "(1/2)"
		if($nbTweets > 1) {
			$fields['status'] = "(1/$nbTweets)
			" . substr($fields['status'], 0, 274);
		}

		$post_tweets = $connection->post("statuses/update", $fields);
		$tweet_id = $post_tweets->id;

		//reply to main tweet
		for($i = 1; $i < $nbTweets; $i++) {
			$tStatus = 	substr($status, 274*$i, 274);
			$fields['status'] = $tStatus;
			$numTweet = $i + 1;
			$fields['status'] = "($numTweet/$nbTweets)
			" . $fields['status'];
			$fields['in_reply_to_status_id'] = $tweet_id;
			$connection->post("statuses/update", $fields);
		}
	}

	//transform a sentence in hashtags with separate words
	function sentenceToHashTags($sentence) {
		$words = explode(' ', $sentence);

		$hashTags = array();
		foreach($words as $word) {
			$hashTags[] = '#' . $word;
		}
		return $hashTags;
	}

	//get most length word of a sentence
	function getLongestWord($sentence) {
		$words = explode(' ', $sentence);

		$longestWord = '';

		$nbWords = count($words);

		if($nbWords > 0) {
			$longestWord = $words[0];
		}

		for($i = 0; $i < $nbWords; $i++) {
			if(strlen($words[$i]) > strlen($longestWord)) {
				$longestWord = $words[$i];
			}
		}
		return $longestWord;
	}
}