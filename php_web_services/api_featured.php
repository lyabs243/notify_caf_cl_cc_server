 <?php include("includes/connection.php");
 	  include("includes/function.php"); 


 	    $jsonObj_2= array();	

		$query_all="SELECT * FROM tbl_channels
		LEFT JOIN tbl_category ON tbl_channels.cat_id= tbl_category.cid
		WHERE tbl_channels.featured_channel=1 AND tbl_channels.status=1 ORDER BY tbl_channels.id DESC";

		$sql_all = mysqli_query($mysqli,$query_all)or die(mysqli_error());

		while($data_all = mysqli_fetch_assoc($sql_all))
		{
			$row2['id'] = $data_all['id'];
			$row2['cat_id'] = $data_all['cat_id'];
			$row2['channel_title'] = $data_all['channel_title'];
			$row2['channel_url'] = $data_all['channel_url'];
			$row2['channel_thumbnail'] = $data_all['channel_thumbnail'];
			$row2['channel_desc'] = $data_all['channel_desc'];

			$row2['cid'] = $data_all['cid'];
			$row2['category_name'] = $data_all['category_name'];
			$row2['category_image'] = $data_all['category_image'];
			
			

			array_push($jsonObj_2,$row2);
		
		}

		//$row['featured_channels']=$jsonObj_2; 

		$set['LIVETV'] = $jsonObj_2;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

?>