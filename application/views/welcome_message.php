<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>

	<style type="text/css">

	::selection { background-color: #E13300; color: white; }
	::-moz-selection { background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
	}

	p.footer {
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container {
		margin: 10px;
		border: 1px solid #D0D0D0;
		box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
</head>
<body>

<div id="container">
	<h1>Welcome to CodeIgniter!</h1>

	<div id="body">
        <form method="post" action="favorite/delete/1">
            <input name="id_favs" type="text" value='[{"id":"11"},{"id":"14"},{"id":"15"}]'>
            <input type="submit" value="OK">
        </form>
        <p>Test add compos</p>
        <form method="post" action="match/add_composition/1/1">

            <input name="id_players" type="text" value='[{"id":"12"},{"id":"14"},{"id":"15"}]'>
            <input type="submit" value="OK">
        </form>
        <form method="post" action="favorite/delete_feeds/1">
            <input name="id_favs" type="text" value='[{"id":"31"},{"id":"32"}]'>
            <input type="submit" value="OK">
        </form>
        <p>Test get match</p>
        <form method="post" action="match/get/4">
            <input name="access_api" type="text" value='hweuridhabgnrimak*$243'>
            <input type="submit" value="OK">
        </form
		<p>The page you are looking at is being generated dynamically by CodeIgniter.</p>

		<p>If you would like to edit this page you'll find it located at:</p>
		<code>application/views/welcome_message.php</code>

		<p>The corresponding controller for this page is found at:</p>
        <code>Test Add Match action</code>
        <form method="post" action="match/add_action/1/1">
            <input name="minute" placeholder="Minute" type="text">
            <input name="id_player" placeholder="Id Player" type="text">
            <input name="id_team" placeholder="id Team" type="text">
            <input name="detail_a" placeholder="Detail A" type="text">
            <input name="detail_b" placeholder="Detail B" type="text">
            <input type="submit" value="OK">
        </form>
		<code>Test Add subscriber</code>
        <form method="post" action="http://notifygroup.org/notifyapp/api/index.php/subscriber/add">
            <input name="full_name" placeholder="Full name" type="text">
            <input name="url_profil_pic" placeholder="Url Profile Pic" type="text">
            <input name="id_account" placeholder="id account" type="text">
            <input name="id_account_type" placeholder="id account type" type="text">
            <input type="submit" value="OK">
        </form>
        <code>Test Add subscriber appeal</code>
        <form method="post" action="http://notifygroup.org/notifyapp/api/index.php/subscriberAppeal/add/3">
            <input name="is_policie_violate" placeholder="is policie violate" type="text">
            <input name="is_policie_respect_after_activation" placeholder="is policie respect after activation" type="text">
            <input name="appeal_description" placeholder="appeal description" type="text">
            <input type="submit" value="OK">
        </form>

        <code>Test get countries</code>
        <form method="post" action="favorite/country/6">
            <input name="start" placeholder="Start" type="text">
            <input name="length" placeholder="Length" type="text">
            <input type="submit" value="OK">
        </form>

        <code>Test get categories</code>
        <form method="post" action="favorite/category/240">
            <input name="start" placeholder="Start" type="text">
            <input name="length" placeholder="Length" type="text">
            <input type="submit" value="OK">
        </form>

        <code>Test suggest favs</code>
        <form method="post" action="favorite/suggest/240">
            <input name="categories" type="text" value='[{"id":"1"},{"id":"14"},{"id":"2"}]'>
            <input name="start" placeholder="Start" type="text">
            <input name="length" placeholder="Length" type="text">
            <input type="submit" value="OK">
        </form>

        <code>Test add websites</code>
        <form method="post" action="favorite/multiple_add/1">
            <input name="id_websites" type="text" value='[{"id":"1"},{"id":"14"},{"id":"2"}]'>
            <input type="submit" value="OK">
        </form>

        <code>Test add feed</code>
        <form method="post" action="favorite/add_feed/6">
            <input name="url_adress" type="text" value='http://127.0.0.1/wordpress/comments/feed/'>
            <input type="submit" value="OK">
        </form>

        <code>Test add comment</code>
        <form method="post" action="match/add_comment/1/1">
            <input name="comment" type="text" value=''>
            <input type="submit" value="OK">
        </form>

        <code>Test add post comment</code>
        <form method="post" action="post/add_comment/1/1">
            <input name="comment" type="text" value=''>
            <input type="submit" value="OK">
        </form>

        <code>Test add Rss feed json file</code>
        <form method="post" action="rssFeed/add_feed_file/" enctype="multipart/form-data">
            <input name="file_contents" type="file">
            <input type="submit" value="OK">
        </form>

        <code>Test Add postt</code>
        <form method="post" action="post/add/5/0" enctype="multipart/form-data">
            <input name="img_post" type="file">
            <textarea name="post" placeholder="post"></textarea>
            <input name="type" type="text" placeholder="type">
            <input type="submit" value="OK">
        </form>

        <code>Test Signal postt</code>
        <form method="post" action="post/signal/11/8" enctype="multipart/form-data">
            <textarea name="message" placeholder="message"></textarea>
            <input type="submit" value="OK">
        </form>

        <code>Test Update post</code>
        <form method="post" action="post/update/9/7" enctype="multipart/form-data">
            <textarea name="post" placeholder="post"></textarea>
            <input type="submit" value="OK">
        </form>

		<p>If you are exploring CodeIgniter for the very first time, you should start by reading the <a href="user_guide/">User Guide</a>.</p>
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
</div>

</body>
</html>