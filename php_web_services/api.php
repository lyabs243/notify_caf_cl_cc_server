<?php include("includes/connection.php");
 	  include("includes/function.php"); 	
 

 	if(isset($_GET['cat_id']))
	{
		$post_order_by=API_CAT_POST_ORDER_BY;

		$cat_id=$_GET['cat_id'];	

		
		$query_rec = "SELECT COUNT(*) as num FROM tbl_news
		LEFT JOIN tbl_category ON tbl_news.cat_id= tbl_category.cid 
		WHERE tbl_news.cat_id='".$cat_id."' AND tbl_news.status=1 ORDER BY tbl_news.id DESC";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$page_limit=API_PAGE_LIMIT;
			
		$limit=($_GET['page']-1) * $page_limit;



		$jsonObj= array();	
	
	    $query="SELECT * FROM tbl_news
		LEFT JOIN tbl_category ON tbl_news.cat_id= tbl_category.cid 
		WHERE tbl_news.cat_id='".$cat_id."' AND tbl_news.status=1 ORDER BY tbl_news.".$post_order_by." LIMIT $limit, $page_limit";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error());

		while($data = mysqli_fetch_assoc($sql))
		{
			$row['pagination_limit'] = $page_limit;
			$row['total_news'] = $total_pages['num'];
			$row['id'] = $data['id'];
			$row['cat_id'] = $data['cat_id'];
			$row['news_type'] = $data['news_type'];
			$row['news_heading'] = stripslashes($data['news_heading']);
			$row['news_description'] = stripslashes($data['news_description']);
			$row['news_video_id'] = $data['news_video_id'];
			$row['news_video_url'] = $data['news_video_url'];
			$row['news_date'] = date('m-d-Y',$data['news_date']);
			$row['news_featured_image'] = $data['news_featured_image'];
			$row['total_views'] = $data['total_views'];

			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
			 

			array_push($jsonObj,$row);
		
		}

		$set['ALL_IN_ONE_NEWS'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

		
	} 
	else if(isset($_GET['user_latest']))
	{
		$post_order_by=API_CAT_POST_ORDER_BY;
		$cat_ids=$_GET['user_latest'];	
 

		$jsonObj= array();	
	
	    $query="SELECT * FROM tbl_news
		LEFT JOIN tbl_category ON tbl_news.cat_id= tbl_category.cid 
		WHERE tbl_news.cat_id IN ($cat_ids) AND tbl_news.status=1 ORDER BY RAND() DESC limit 25";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error());

		while($data = mysqli_fetch_assoc($sql))
		{
			 
			$row['id'] = $data['id'];
			$row['cat_id'] = $data['cat_id'];
			$row['news_type'] = $data['news_type'];
			$row['news_heading'] = stripslashes($data['news_heading']);
			$row['news_description'] = stripslashes($data['news_description']);
			$row['news_video_id'] = $data['news_video_id'];
			$row['news_video_url'] = $data['news_video_url'];
			$row['news_date'] = date('m-d-Y',$data['news_date']);
			$row['news_featured_image'] = $data['news_featured_image'];
			$row['total_views'] = $data['total_views'];

			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
			 

			array_push($jsonObj,$row);
		
		}

		$set['ALL_IN_ONE_NEWS'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}
	else if(isset($_GET['home']))
	{


			$jsonObj0= array();	

			$query0="SELECT * FROM tbl_news
			LEFT JOIN tbl_category ON tbl_news.cat_id= tbl_category.cid 
			WHERE tbl_news.status=1 ORDER BY tbl_news.total_views DESC LIMIT 5";
			$sql0 = mysqli_query($mysqli,$query0)or die(mysqli_error());

			while($data0 = mysqli_fetch_assoc($sql0))
			{
				$row0['id'] = $data0['id'];
				$row0['cat_id'] = $data0['cat_id'];
				$row0['news_type'] = $data0['news_type'];
				$row0['news_heading'] = stripslashes($data0['news_heading']);
				$row0['news_description'] = stripslashes($data0['news_description']);
				$row0['news_video_id'] = $data0['news_video_id'];
				$row0['news_video_url'] = $data0['news_video_url'];
				$row0['news_date'] = date('m-d-Y',$data0['news_date']);
				$row0['news_featured_image'] = $data0['news_featured_image'];
				$row0['total_views'] = $data0['total_views'];

				$row0['cid'] = $data0['cid'];
				$row0['category_name'] = $data0['category_name'];
 				 

				array_push($jsonObj0,$row0);
			
			}

		$row['trending_news']=$jsonObj0;

 
			$jsonObj1= array();	

			$query="SELECT * FROM tbl_news
			LEFT JOIN tbl_category ON tbl_news.cat_id= tbl_category.cid 
			WHERE tbl_news.status=1 ORDER BY tbl_news.id DESC LIMIT 5";
			$sql = mysqli_query($mysqli,$query)or die(mysqli_error());

			while($data = mysqli_fetch_assoc($sql))
			{
				$row1['id'] = $data['id'];
				$row1['cat_id'] = $data['cat_id'];
				$row1['news_type'] = $data['news_type'];
				$row1['news_heading'] = stripslashes($data['news_heading']);
				$row1['news_description'] = stripslashes($data['news_description']);
				$row1['news_video_id'] = $data['news_video_id'];
				$row1['news_video_url'] = $data['news_video_url'];
				$row1['news_date'] = date('m-d-Y',$data['news_date']);
				$row1['news_featured_image'] = $data['news_featured_image'];
				$row1['total_views'] = $data['total_views'];

				$row1['cid'] = $data['cid'];
				$row1['category_name'] = $data['category_name'];
 				 

				array_push($jsonObj1,$row1);
			
			}

		$row['latest_news']=$jsonObj1;	

		$jsonObj_2= array();	

		$query_all="SELECT * FROM tbl_news
			LEFT JOIN tbl_category ON tbl_news.cat_id= tbl_category.cid 
			WHERE tbl_news.status=1 ORDER BY RAND() DESC LIMIT 5";

		$sql_all = mysqli_query($mysqli,$query_all)or die(mysqli_error());

		while($data_all = mysqli_fetch_assoc($sql_all))
		{
			    $row2['id'] = $data_all['id'];
				$row2['cat_id'] = $data_all['cat_id'];
				$row2['news_type'] = $data_all['news_type'];
				$row2['news_heading'] = stripslashes($data_all['news_heading']);
				$row2['news_description'] = stripslashes($data_all['news_description']);
				$row2['news_video_id'] = $data_all['news_video_id'];
				$row2['news_video_url'] = $data_all['news_video_url'];
				$row2['news_date'] = date('m-d-Y',$data_all['news_date']);
				$row2['news_featured_image'] = $data_all['news_featured_image'];
				$row2['total_views'] = $data_all['total_views'];

				$row2['cid'] = $data_all['cid'];
				$row2['category_name'] = $data_all['category_name'];
 				 

 				array_push($jsonObj_2,$row2);
		}

		$row['top_story']=$jsonObj_2; 

		$set['ALL_IN_ONE_NEWS'] = $row;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();


	}
	else if(isset($_GET['latest']))
	{
		$page_limit=API_PAGE_LIMIT;


		$query_rec = "SELECT COUNT(*) as num FROM tbl_news
		LEFT JOIN tbl_category ON tbl_news.cat_id= tbl_category.cid 
		WHERE tbl_news.status=1 ORDER BY tbl_news.id DESC";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
			
		$limit=($_GET['page']-1) * $page_limit;

 
		$jsonObj= array();	

		$query="SELECT * FROM tbl_news
		LEFT JOIN tbl_category ON tbl_news.cat_id= tbl_category.cid 
		WHERE tbl_news.status=1 ORDER BY tbl_news.id DESC LIMIT $limit, $page_limit";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error());

		while($data = mysqli_fetch_assoc($sql))
		{
			$row['pagination_limit'] = $page_limit;
			$row['total_news'] = $total_pages['num'];
			$row['id'] = $data['id'];
			$row['cat_id'] = $data['cat_id'];
			$row['news_type'] = $data['news_type'];
			$row['news_heading'] = stripslashes($data['news_heading']);
			$row['news_description'] = stripslashes($data['news_description']);
			$row['news_video_id'] = $data['news_video_id'];
			$row['news_video_url'] = $data['news_video_url'];
			$row['news_date'] = date('m-d-Y',$data['news_date']);
			$row['news_featured_image'] = $data['news_featured_image'];
			$row['total_views'] = $data['total_views'];

			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
 			 

			array_push($jsonObj,$row);
		
		}

		$set['ALL_IN_ONE_NEWS'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}		
	else if(isset($_GET['search_text']))
	{	
		$page_limit=API_PAGE_LIMIT;


		$query_rec = "SELECT COUNT(*) as num FROM tbl_news
		LEFT JOIN tbl_category ON tbl_news.cat_id= tbl_category.cid 
		WHERE tbl_news.status=1 AND news_heading like '%".$_GET['search_text']."%' ";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
			
		$limit=($_GET['page']-1) * $page_limit;

		$jsonObj= array();	

		$query="SELECT * FROM tbl_news
		LEFT JOIN tbl_category ON tbl_news.cat_id= tbl_category.cid 
		WHERE tbl_news.status=1 AND news_heading like '%".$_GET['search_text']."%' LIMIT $limit, $page_limit";
		$sql = mysqli_query($mysqli,$query);
		
		if(mysqli_num_rows($sql)>0){

				while($data = mysqli_fetch_assoc($sql))
				{
					$row['pagination_limit'] = $page_limit;
					$row['total_news'] = $total_pages['num'];
					$row['id'] = $data['id'];
					$row['cat_id'] = $data['cat_id'];
					$row['news_type'] = $data['news_type'];
					$row['news_heading'] = stripslashes($data['news_heading']);
					$row['news_description'] = stripslashes($data['news_description']);
					$row['news_video_id'] = $data['news_video_id'];
					$row['news_video_url'] = $data['news_video_url'];
					$row['news_date'] = date('m-d-Y',$data['news_date']);
					$row['news_featured_image'] = $data['news_featured_image'];
					$row['total_views'] = $data['total_views'];

					$row['cid'] = $data['cid'];
					$row['category_name'] = $data['category_name'];
 					 

					array_push($jsonObj,$row);
				
				}

				$set['ALL_IN_ONE_NEWS'] = $jsonObj;
				
				header( 'Content-Type: application/json; charset=utf-8' );
			    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
				die();

		}
		else
		{
			    $set['ALL_IN_ONE_NEWS'][]=array('msg' => 'No News Found! Try Different Keyword','Success'=>'0');

		  	    header( 'Content-Type: application/json; charset=utf-8' );
			    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
				die();
		}
	
	}
	else if(isset($_GET['news_id']))
	{ 

		$jsonObj= array();	

		$query="SELECT * FROM tbl_news
		LEFT JOIN tbl_category ON tbl_news.cat_id= tbl_category.cid 
		WHERE tbl_news.status=1 AND tbl_news.id='".$_GET['news_id']."'";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error());

		while($data = mysqli_fetch_assoc($sql))
		{
			$row['id'] = $data['id'];
			$row['cat_id'] = $data['cat_id'];
			$row['news_type'] = $data['news_type'];
			$row['news_heading'] = stripslashes($data['news_heading']);
			$row['news_description'] = stripslashes($data['news_description']);
			$row['news_video_id'] = $data['news_video_id'];
			$row['news_video_url'] = $data['news_video_url'];
			$row['news_date'] = date('m-d-Y',$data['news_date']);
			$row['news_featured_image'] = $data['news_featured_image'];
			$row['total_views'] = $data['total_views'];

			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
 			 
			 //Gallery Images
		      $qry1="SELECT * FROM tbl_news_gallery where news_id='".$_GET['news_id']."'";
		      $result1=mysqli_query($mysqli,$qry1); 

		      if($result1->num_rows > 0)
		      {
		      		while ($row_img=mysqli_fetch_array($result1)) {
 		      	
		 		      	$row1['image_name'] = $row_img['news_gallery_image'];

		 		      	$row['galley_image'][]= $row1;
				      }
		     
		      }
		      else
		      {	
		      		 
		      		$row['galley_image'][]= '';
		      }

		      //Comments
		      $qry2="SELECT * FROM tbl_comments where news_id='".$_GET['news_id']."' ORDER BY tbl_comments.id DESC LIMIT 5";
		      $result2=mysqli_query($mysqli,$qry2); 

		      if($result2->num_rows > 0)
		      {
		      		while ($row_comments=mysqli_fetch_array($result2)) {
 		      			
 		      			$row2['comment_id'] = $row_comments['id'];
		 		      	$row2['news_id'] = $row_comments['news_id'];
		 		      	$row2['user_id'] = $row_comments['user_id'];
		 		      	$row2['user_name'] = $row_comments['user_name'];
		 		      	$row2['user_email'] = $row_comments['user_email'];
		 		      	$row2['comment_text'] = $row_comments['comment_text'];

		 		      	$row['user_comments'][]= $row2;
				      }
		     
		      }
		      else
		      {	
		      		 
		      		$row['user_comments'][]= '';
		      }
		      
			array_push($jsonObj,$row);
		
		}

		$view_qry=mysqli_query($mysqli,"UPDATE tbl_news SET total_views = total_views + 1 WHERE id = '".$_GET['news_id']."'");

		$set['ALL_IN_ONE_NEWS'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}
	else if(isset($_GET['comment_news_id']))
	{ 

		$jsonObj= array();	
		$query_rec = "SELECT COUNT(*) as num FROM tbl_comments
 		WHERE tbl_comments.news_id='".$_GET['comment_news_id']."' ORDER BY tbl_comments.id DESC";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$page_limit=50;
			
		$limit=($_GET['page']-1) * $page_limit;


		$jsonObj= array();	
	
	    $query="SELECT * FROM tbl_comments
 		WHERE tbl_comments.news_id='".$_GET['comment_news_id']."' ORDER BY tbl_comments.id DESC LIMIT $limit, $page_limit";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error());

		while($data = mysqli_fetch_assoc($sql))
		{
			$row['pagination_limit'] = $page_limit;
			$row['total_news'] = $total_pages['num'];
			$row['comment_id'] = $data['id'];
			$row['news_id'] = $data['news_id'];
	      	$row['user_id'] = $data['user_id'];
	      	$row['user_name'] = $data['user_name'];
	      	$row['user_email'] = $data['user_email'];
	      	$row['comment_text'] = $data['comment_text']; 
			 

			array_push($jsonObj,$row);
		
		}
		  
		 
		$set['ALL_IN_ONE_NEWS'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}		
	else if(isset($_GET['channel']))
	{
		 //echo $_GET['channel_id'];
		$SQL1="select * from tbl_channel where id='1'";	
		$result1 = mysqli_query($mysqli,$SQL1)or die(mysqli_error());	
						
			$jsonObj= array();

			while ($row1 = mysqli_fetch_assoc($result1)) 
			{
	  
					$row=array();
					$row['id'] = $row1['id'];
					$row['channel_name'] = $row1['channel_name'];
					$row['channel_url'] = $row1['channel_url'];
					$row['channel_description'] = $row1['channel_description'];
					$row['channel_logo'] = $row1['channel_logo'];
				 						 
	  				array_push($jsonObj,$row);
	  
			}
				 
				
		$set['ALL_IN_ONE_NEWS'] = $jsonObj;				

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 

	}
	else if(isset($_GET['app_details']))
	{
		$jsonObj= array();	

		$query="SELECT * FROM tbl_settings WHERE id='1'";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error());

		while($data = mysqli_fetch_assoc($sql))
		{
			 
			$row['app_name'] = $data['app_name'];
			$row['app_logo'] = $data['app_logo'];
			$row['app_version'] = $data['app_version'];
			$row['app_author'] = $data['app_author'];
			$row['app_contact'] = $data['app_contact'];
			$row['app_email'] = $data['app_email'];
			$row['app_website'] = $data['app_website'];
			$row['app_description'] = $data['app_description'];
			$row['app_developed_by'] = $data['app_developed_by'];

			$row['app_privacy_policy'] = stripslashes($data['app_privacy_policy']);

			$row['publisher_id'] = $data['publisher_id'];
			$row['interstital_ad'] = $data['interstital_ad'];
			$row['interstital_ad_id'] = $data['interstital_ad_id'];
			$row['interstital_ad_click'] = $data['interstital_ad_click'];
 			$row['banner_ad'] = $data['banner_ad'];
 			$row['banner_ad_id'] = $data['banner_ad_id'];
 

			array_push($jsonObj,$row);
		
		}

		$set['ALL_IN_ONE_NEWS'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	else if(isset($_GET['android_token']))
	{

	  $qry="SELECT * FROM tbl_user_token WHERE token='".$_GET['android_token']."'";
      $result=mysqli_query($mysqli,$qry);
      $row=mysqli_fetch_assoc($result);
      
      if($row['token']==$_GET['android_token'])
      {
       		$set['ALL_IN_ONE_NEWS'][]=array('msg' => "token already added",'success'=>'0');
      }
      else
	  {
			 
		     $data = array(            
               'token'  =>  $_GET['android_token'],
               );  
      
     		$qry = Insert('tbl_user_token',$data);

            $set['ALL_IN_ONE_NEWS'][]=array('msg' => 'Success','Success'=>'1');
				
	  }

 
	 	header( 'Content-Type: application/json; charset=utf-8' );
    	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}		
	else
	{
		 

		$jsonObj= array();
		
		$cid=API_CAT_ORDER_BY;


		$query="SELECT cid,category_name FROM tbl_category ORDER BY tbl_category.".$cid."";
		$sql = mysqli_query($mysqli,$query)or die(mysql_error());

		while($data = mysqli_fetch_assoc($sql))
		{
			
			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
 			 

			array_push($jsonObj,$row);
		
		}

		$set['ALL_IN_ONE_NEWS'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	} 
	 
?>