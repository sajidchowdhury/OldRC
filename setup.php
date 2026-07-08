<?php 

include('auth.php');
include('layout/head.php');
include('function_query.php');
$conn_me = Database::getInstance();
$templet_content = TEMPLET::TEMPLET_CONTENT($_GET['page_identity'],$_GET['related_id']);
$topbar = SETUP::TOP_BAR($_GET['page_identity'],'');



?>

    <body> 

<div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
            <div class="page-sidebar">
            <?php include('layout/common_side_bar.php'); ?>

            </div>
            <!-- END PAGE SIDEBAR -->
            
            <!-- PAGE CONTENT -->
            <div class="page-content">
                
           <?php print $topbar['top_bar_content'];?> 
             
                
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb push-down-0">
                    <li><a href="#">Administration Module </a></li>                    
                    <li class="active"><?php print $_GET['page_identity'];?></li>
                </ul>
                <!-- END BREADCRUMB -->                                                
                
                <!-- START CONTENT FRAME -->
                <div class="page-content-wrap">
                
        <?php if($_GET['page_identity'] == 'Block-User' ){ ?>
        <div class="row"><div class="col-md-12"><input placeholder="search by name " type="text" onkeyup="findUserToBlock(this.value)" value="" class="form-control"></div></div><br>
        <?php } ?>
        <div id="load_content"><?php print $templet_content['content'];?></div>
                    
      
                </div>
                <!-- END CONTENT FRAME -->
                
                
                
            </div>            
            <!-- END PAGE CONTENT -->
        </div>


<?php include('javascripts.php'); ?>

<script type="text/javascript" src="my_sz_script2.js">
$(".datatable").dataTable();
</script>

    </body>
</html>






