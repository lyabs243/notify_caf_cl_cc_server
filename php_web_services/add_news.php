<?php include("includes/header.php");

	require("includes/function.php");
	require("language/language.php");

	require_once("thumbnail_images.class.php");

  //All Category
	$cat_qry="SELECT * FROM tbl_category ORDER BY category_name";
  $cat_result=mysqli_query($mysqli,$cat_qry);  
	
	if(isset($_POST['submit']))
	{
	   
      if ($_POST['news_type']=='video')
        {

              $video_url=$_POST['video_url'];

              $youtube_video_url = addslashes($_POST['video_url']);
              parse_str( parse_url( $youtube_video_url, PHP_URL_QUERY ), $array_of_vars );
              $video_id=  $array_of_vars['v'];

              $video_thumbnail='';     

        }
        else
        {
             $video_id='';
             $youtube_video_url ='';

        }


     $file_name= str_replace(" ","-",$_FILES['news_featured_image']['name']);

	   $news_featured_image=rand(0,99999)."_".$file_name;
		 	 
     //Main Image
	   $tpath1='images/'.$news_featured_image; 			 
     $pic1=compress_image($_FILES["news_featured_image"]["tmp_name"], $tpath1, 80);
	 
		 //Thumb Image 
	   $thumbpath='images/thumbs/'.$news_featured_image;		
     $thumb_pic1=create_thumb_image($tpath1,$thumbpath,'200','200');   
      
          
       $data = array( 
			   'cat_id'  =>  $_POST['cat_id'],
         'news_type'  =>  $_POST['news_type'],
         'news_heading'  =>  addslashes($_POST['news_heading']),
         'news_description'  =>  addslashes($_POST['news_description']),
         'news_date'  =>  strtotime($_POST['news_date']),
			   'news_featured_image'  =>  $news_featured_image,
         'news_video_id'  =>  $video_id,
         'news_video_url'  =>  $youtube_video_url
			    );		

 		$qry = Insert('tbl_news',$data);	

    $news_id=mysqli_insert_id($mysqli);

   $size_sum = array_sum($_FILES['news_gallery_image']['size']);
     
  if($size_sum > 0)
   { 
      for ($i = 0; $i < count($_FILES['news_gallery_image']['name']); $i++) 
      {
           $file_name= str_replace(" ","-",$_FILES['news_gallery_image']['name'][$i]);
             
           $news_gallery_image=rand(0,99999)."_".$file_name;
         
           //Main Image
           $tpath1='images/'.$news_gallery_image;       
           $pic1=compress_image($_FILES["news_gallery_image"]["tmp_name"][$i], $tpath1, 80);

            $data1 = array(
                'news_id'=>$news_id,
                'news_gallery_image'  => $news_gallery_image                         
                );      

            $qry1 = Insert('tbl_news_gallery',$data1); 

      }
    }

 	    
		$_SESSION['msg']="10";
 
		header( "Location:manage_news.php");
		exit;	

		
	}
	 

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

<script type="text/javascript">
$(document).ready(function(e) {
           $("#news_type").change(function(){
         
           var type=$("#news_type").val();
              //alert(type);
              if(type=="video")
              {                 
                $("#youtube_url_display").show();
                 $("#image_news").hide();
               }             
              else
              {   
                $("#image_news").show();     
                $("#youtube_url_display").hide();
                 
              }    
              
         });
 
           
        });
</script>
<div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="page_title_block">
            <div class="col-md-5 col-xs-12">
              <div class="page_title">Add News</div>
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
            <form action="" name="addeditcategory" method="post" class="form form-horizontal" enctype="multipart/form-data">
 
              <div class="section">
                <div class="section-body">
                <div class="form-group">
                    <label class="col-md-3 control-label">News Type :-</label>
                    <div class="col-md-6">
                      <select name="news_type" id="news_type" class="select2" required>
                        <option value="image">Image</option>
                        <option value="video">Video</option>                       
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Category :-</label>
                    <div class="col-md-6">
                      <select name="cat_id" id="cat_id" class="select2" required>
                        <option value="">--Select Category--</option>
                        <?php
                            while($cat_row=mysqli_fetch_array($cat_result))
                            {
                        ?>                       
                        <option value="<?php echo $cat_row['cid'];?>"><?php echo $cat_row['category_name'];?></option>                           
                        <?php
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">News Title :-</label>
                    <div class="col-md-6">
                      <input type="text" name="news_heading" id="news_heading" value="" class="form-control" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Description :-</label>
                    <div class="col-md-6">
                 
                      <textarea name="news_description" id="news_description" class="form-control"></textarea>

                      <script>CKEDITOR.replace( 'news_description' );</script>
                    </div>
                  </div>
                  <div class="form-group">&nbsp;</div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Featured Image :-</label>
                    <div class="col-md-6">
                      <div class="fileupload_block">
                        <input type="file" name="news_featured_image" value="" id="fileupload">
                            
                        	  <div class="fileupload_img"><img type="image" src="assets/images/add-image.png" alt="Featured image" /></div>
                        	 
                      </div>
                    </div>
                  </div>
                  <div class="form-group" id="image_news">
                    <label class="col-md-3 control-label">Gallery Image :-</label>
                    <div class="col-md-6">
                      <div class="fileupload_block">
                        <input type="file" name="news_gallery_image[]" value="" id="fileupload" multiple>
                            
                            <div class="fileupload_img"><img type="image" src="assets/images/add-image.png" alt="Featured image" /></div>
                           
                      </div>
                    </div>
                  </div>
                 
                  <div id="youtube_url_display" class="form-group" style="display:none;">
                    <label class="col-md-3 control-label">YouTube Video URL :-</label>
                    <div class="col-md-6">
                      <input type="text" name="video_url" id="video_url" value="" class="form-control">
                    </div>
                  </div>
                   
                  <div class="form-group">
                    <label class="col-md-3 control-label">Date :-</label>
                    <div class="col-md-6">
                      <input type="text" name="news_date" id="news_date" value="" class="form-control datepicker" required>
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
