<?php include("includes/header.php");

$file_path = 'http://'.$_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']).'/';
?>
<div class="row">
      <div class="col-sm-12 col-xs-12">
     	 	<div class="card">
		        <div class="card-header">
		          Example API urls
		        </div>
       			    <div class="card-body no-padding">
         		
         			 <pre><code class="html"><b>Home</b><br><?php echo $file_path."api.php?home"?><br><br><b>Search News</b><br><?php echo $file_path."api.php?search_text=test&page=1"?><br><br><b>Category List</b><br><?php echo $file_path."api.php"?><br><br><b>News by Cat ID</b><br><?php echo $file_path."api.php?cat_id=3&page=1"?><br><br><b>Latest</b><br><?php echo $file_path."api.php?latest&page=1"?><br><br><b>User Latest</b><br><?php echo $file_path."api.php?user_latest=2,3"?><br><br><b>Single News</b><br><?php echo $file_path."api.php?news_id=3"?><br><br><b>News Comment</b><br><?php echo $file_path."api.php?comment_news_id=3&page=1"?><br><br><b>Channel</b><br><?php echo $file_path."api.php?channel"?><br><br><b>User Register</b><br><?php echo $file_path."user_register_api.php?name=john&email=john@gmail.com&password=123456&phone=1234567891"?><br><br><b>User Login</b><br><?php echo $file_path."user_login_api.php?email=john@gmail.com&password=123456"?><br><br><b>User Profile</b><br><?php echo $file_path."user_profile_api.php?id=2"?><br><br><b>User Profile Update</b><br><?php echo $file_path."user_profile_update_api.php?user_id=2&name=john&email=john@gmail.com&password=123456&phone=1234567891"?><br><br><b>Forgot Password</b><br><?php echo $file_path."user_forgot_pass_api.php?email=john@gmail.com"?><br><br><b>User Comment</b><br><?php echo $file_path."user_comment_api.php?news_id=1&user_id=8&comment_text=test"?><br><br><b>App Details</b><br><?php echo $file_path."api.php?app_details"?></code></pre>
       		
       				</div>
          	</div>
        </div>
</div>
    <br/>
    <div class="clearfix"></div>
        
<?php include("includes/footer.php");?>       
