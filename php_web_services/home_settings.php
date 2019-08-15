<?php include("includes/header.php");

	require("includes/function.php");
	require("language/language.php");
	 
	
	$qry="SELECT * FROM tbl_home where id='1'";
  $result=mysqli_query($mysqli,$qry);
  $settings_row=mysqli_fetch_assoc($result);

 

  if(isset($_POST['submit']))
  {

    

    $img_res=mysql_query("SELECT * FROM tbl_home WHERE id='1'");
    $img_row=mysql_fetch_assoc($img_res);
    

    if($_FILES['home_banner']['name']!="")
    {        

            unlink('images/'.$img_row['home_banner']);   

            $home_banner=$_FILES['home_banner']['name'];
            $pic1=$_FILES['home_banner']['tmp_name'];

            $tpath1='images/'.$home_banner;      
            copy($pic1,$tpath1);


              $data = array(      
                
              'home_title'  =>  $_POST['home_title'],
              'home_banner'  =>  $home_banner,
              'home_banner_url'  =>  $_POST['home_banner_url']
              
              );

    }
    else
    {
  
                $data = array(

                'home_title'  =>  $_POST['home_title'],
                'home_banner_url'  =>  $_POST['home_banner_url']                 
                  );

    } 

    $settings_edit=Update('tbl_home', $data, "WHERE id = '1'");
  

    $_SESSION['msg']="11";
    header( "Location:home_settings.php");
    exit;
 
  }
 

?>
 
	 <div class="row">
      <div class="col-md-12">
        <div class="card">
		  <div class="page_title_block">
            <div class="col-md-5 col-xs-12">
              <div class="page_title">Home</div>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="row mrg-top">
            <div class="col-md-12">
               
              <div class="col-md-12 col-sm-12">
                <?php if(isset($_SESSION['msg'])){?> 
                 <div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                  <?php echo $client_lang[$_SESSION['msg']] ; ?></a> </div>
                <?php unset($_SESSION['msg']);}?> 
              </div>
            </div>
          </div>
          <div class="card-body mrg_bottom">
            <!-- Nav tabs -->
            <form action="" name="settings_from" method="post" class="form form-horizontal" enctype="multipart/form-data">
              <div class="section">
                <div class="section-body">
                  <div class="form-group">
                    <label class="col-md-3 control-label">Title :-</label>
                    <div class="col-md-6">
                      <input type="text" name="home_title" id="home_title" value="<?php echo $settings_row['home_title'];?>" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Home Banner :-</label>
                    <div class="col-md-6">
                      <div class="fileupload_block">
                        <input type="file" name="home_banner" id="fileupload">
                         
                          <?php if($settings_row['home_banner']!="") {?>
                            <div class="fileupload_img"><img type="image" src="images/<?php echo $settings_row['home_banner'];?>" alt="image" /></div>
                          <?php } else {?>
                            <div class="fileupload_img"><img type="image" src="assets/images/add-image.png" alt="image" /></div>
                          <?php }?>
                        
                      </div>
                    </div>
                  </div>                
                    
                  <div class="form-group">
                    <label class="col-md-3 control-label">URL:-</label>
                    <div class="col-md-6">
                      <input type="text" name="home_banner_url" id="home_banner_url" value="<?php echo $settings_row['home_banner_url'];?>" class="form-control">
                    </div>
                  </div>
                   
                
                  <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                      <button type="submit" name="submit" class="btn btn-primary">Save</button>
                    </div>
                  </div>
                </div>
              </div>
               </form>
          
           

          </div>
        </div>
      </div>
    </div>

        
<?php include("includes/footer.php");?>       
