<?php include("includes/header.php");

	require("includes/function.php");
	require("language/language.php");
	 
	
	$qry="SELECT * FROM tbl_channel where id='1'";
  $result=mysqli_query($mysqli,$qry);
  $settings_row=mysqli_fetch_assoc($result);

 

  if(isset($_POST['submit']))
  {

    

    $img_res=mysql_query("SELECT * FROM tbl_channel WHERE id='1'");
    $img_row=mysql_fetch_assoc($img_res);
    

    if($_FILES['channel_logo']['name']!="")
    {        

            unlink('images/'.$img_row['channel_logo']);   

            $channel_logo=$_FILES['channel_logo']['name'];
            $pic1=$_FILES['channel_logo']['tmp_name'];

            $tpath1='images/'.$channel_logo;      
            copy($pic1,$tpath1);


              $data = array(      
                
              'channel_name'  =>  $_POST['channel_name'],
              'channel_logo'  =>  $channel_logo,  
              'channel_url'  =>  $_POST['channel_url'],
              'channel_description'  => addslashes($_POST['channel_description'])                
              );

    }
    else
    {
  
               $data = array(      
                
              'channel_name'  =>  $_POST['channel_name'],
               'channel_url'  =>  $_POST['channel_url'],
              'channel_description'  => addslashes($_POST['channel_description'])                
              );

    } 

    $settings_edit=Update('tbl_channel', $data, "WHERE id = '1'");
  

        $_SESSION['msg']="11";
        header( "Location:channel.php");
        exit;
  
 
  }
 
?>
 
	 <div class="row">
      <div class="col-md-12">
        <div class="card">
		  <div class="page_title_block">
            <div class="col-md-5 col-xs-12">
              <div class="page_title">Channel</div>
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
           <form action="" name="settings_from" method="post" class="form form-horizontal" enctype="multipart/form-data">
              <div class="section">
                <div class="section-body">
                  <div class="form-group">
                    <label class="col-md-3 control-label">Name :-</label>
                    <div class="col-md-6">
                      <input type="text" name="channel_name" id="channel_name" value="<?php echo $settings_row['channel_name'];?>" class="form-control">
                    </div>
                  </div>                 
                  <div class="form-group">
                    <label class="col-md-3 control-label">URL :-</label>
                    <div class="col-md-6">
                      <input type="text" name="channel_url" id="channel_url" value="<?php echo $settings_row['channel_url'];?>" class="form-control">
                    </div>
                  </div>
                   <div class="form-group">
                    <label class="col-md-3 control-label">Logo :-</label>
                    <div class="col-md-6">
                      <div class="fileupload_block">
                        <input type="file" name="channel_logo" id="fileupload">
                         
                           
                            <div class="fileupload_img"><img type="image" src="assets/images/add-image.png" alt="image" /></div>
                         
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">&nbsp; </label>
                    <div class="col-md-6">
                      <?php if($settings_row['channel_logo']!="") {?>
                            <div class="block_wallpaper"><img src="images/<?php echo $settings_row['channel_logo'];?>" alt="category image" /></div>
                          <?php } ?>
                    </div>
                  </div><br> 
                  <div class="form-group">
                    <label class="col-md-3 control-label">App Description :-</label>
                    <div class="col-md-6">
                 
                      <textarea name="channel_description" id="channel_description" class="form-control"><?php echo $settings_row['channel_description'];?></textarea>

                      <script>CKEDITOR.replace( 'channel_description' );</script>
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
