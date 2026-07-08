<?php 
include('auth.php');
include('layout/head.php');
include('function_query.php');
$conn_me = Database::getInstance();

$templet_content = TEMPLET::TEMPLET_CONTENT('User-Activity',$_GET['related_id']);
$topbar = SETUP::TOP_BAR('Activity','');



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
                    <li class="active">User-Activity</li>
                </ul>
                <!-- END BREADCRUMB -->                                                
                
                <!-- START CONTENT FRAME -->
                <div class="page-content-wrap">
                <div id="load_content"  style=" position: absolute !important"><?php print $templet_content['content'];?></div>

                    
      
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






