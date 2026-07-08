<?php 

include('layout/head.php');
include('function_query.php');
$conn_me = Database::getInstance();

$templet_content = TEMPLET::TEMPLET_CONTENT($_GET['page_identity'],$_GET['related_id']);

?>

    <body> 
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
            <?php include('layout/common_side_bar.php'); ?>

            <!-- END PAGE SIDEBAR -->
            
            <!-- PAGE CONTENT -->
            <div class="page-content">
                
                         
               <div id="load_breadcrumb"><?php print $templet_content['breadcrumb'];?></div>

                <div class="page-content-wrap">


             
                

                <div id="load_content"><?php print $templet_content['content'];?></div>
                </div>
                                        
            </div>            
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->


        

<?php include('javascripts.php'); ?>

<script type="text/javascript" src="my_sz_script2.js"></script>

    </body>
</html>






