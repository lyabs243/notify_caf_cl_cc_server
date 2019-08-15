<?php include("includes/connection.php");
 
	include('includes/function.php');

	 
	 	  
		$qry = "SELECT * FROM tbl_users WHERE email = '".$_GET['email']."' or gplus_id = '".$_GET['gplus_id']."'"; 
		$result = mysqli_query($mysqli,$qry);
		$row = mysqli_fetch_assoc($result);
		
		if (!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) 
		{
			$set['ALL_IN_ONE_NEWS'][]=array('msg' => "Invalid email format!",'success'=>'0');
		}
		else if($row['email']!="" AND $row['gplus_id']!="")
		{
 
			$set['ALL_IN_ONE_NEWS'][]=array('user_id' => $row['id'],'name'=>$row['name'],'success'=>'1');
		}
		else if($row['fb_id']!='')
		{
 
			$set['ALL_IN_ONE_NEWS'][]=array('msg'=>'Email address already used!','success'=>'0');
		}
		else
		{ 
 			
 			 
 				$data = array(
 					'user_type'=>'GPlus',											 
				    'name'  => $_GET['name'],				    
					'email'  =>  $_GET['email'],
					'gplus_id'  =>  $_GET['gplus_id'],
 					'status'  =>  '1'
					);		
 			 

			$qry = Insert('tbl_users',$data);									 
					 
			
			$qry1 = "SELECT * FROM tbl_users WHERE email = '".$_GET['email']."'"; 
		    $result1 = mysqli_query($mysqli,$qry1);
		    $row1 = mysqli_fetch_assoc($result1);
			//$set['ALL_IN_ONE_NEWS'][]=array('msg' => "Register successflly...!",'success'=>'1');

			$set['ALL_IN_ONE_NEWS'][]=array('user_id' => $row1['id'],'name'=>$row1['name'],'msg' => "Register successflly...!",'success'=>'1');
					
		}

	  
 	 header( 'Content-Type: application/json; charset=utf-8');
     $json = json_encode($set);				
	 echo $json;
	 exit;
	 
?>