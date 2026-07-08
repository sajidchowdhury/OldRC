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
             
                
                                                   
                
                <!-- START CONTENT FRAME -->
                <div class="page-content-wrap">
              
            
                <div id="load_content">

                <?php include("$_GET[page_identity].php");?>



                
                </div>

                </div>
      
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






