<?php include("includes/connection.php");
 
	include('includes/function.php');

	 
	 	  
		$qry = "SELECT * FROM tbl_users WHERE id = '".$_GET['user_id']."'"; 
		$result = mysqli_query($mysqli,$qry);
		$row = mysqli_fetch_assoc($result);
		 
		$data = array(
 	    'news_id'  => $_GET['news_id'],
 	    'user_id'  => $_GET['user_id'],
 	    'user_name'  => $row['name'],				    
		'user_email'  =>  $row['email'],
		'comment_text'  =>  $_GET['comment_text']
		);		
 		
		$qry = Insert('tbl_comments',$data);									 
					 
		$set['ALL_IN_ONE_NEWS'][]=array('msg' => "Comment post successflly...!",'success'=>'1');
		 
 	 header( 'Content-Type: application/json; charset=utf-8');
     $json = json_encode($set);				
	 echo $json;
	 exit;
	 
?>