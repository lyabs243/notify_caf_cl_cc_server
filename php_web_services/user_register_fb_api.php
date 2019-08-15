<?php include("includes/connection.php");
 
	include('includes/function.php');

	 
	 	  
		$qry = "SELECT * FROM tbl_users WHERE email = '".$_GET['email']."' or fb_id = '".$_GET['fb_id']."'"; 
		$result = mysqli_query($mysqli,$qry);
		$row = mysqli_fetch_assoc($result);
		
		if($row['fb_id']!="")
		{
 
			$set['ALL_IN_ONE_NEWS'][]=array('user_id' => $row['id'],'name'=>$row['name'],'success'=>'1');
		}
		else if($row['gplus_id']!='')
		{
 
			$set['ALL_IN_ONE_NEWS'][]=array('msg'=>'Email address already used!','success'=>'0');
		}
		else
		{ 
 			
 				if($_GET['email']!='')
 				{
 					$email=$_GET['email'];
 					 
 				}
 				else
 				{
 					$email='';
 					 
 				}

 				if($_GET['phone']!='')
 				{
  					$phone=$_GET['phone'];
 				}
 				else
 				{
 					$phone='';
 					
 				}
 			 
 				$data = array(
 					'user_type'=>'Facebook',											 
				    'name'  => $_GET['name'],				    
					'email'  =>  $email,
					'phone'  =>  $phone,
					'fb_id'  =>  $_GET['fb_id'],
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