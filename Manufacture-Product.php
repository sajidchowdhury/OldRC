<?php 



$flow_chart1 = MANUFACTUR_PRODUCT::MATERIAL_COLLECTION('','All Data');
$flow_chart2 = MANUFACTUR_PRODUCT::REQUISITION('','All Data');
$flow_chart3 = MANUFACTUR_PRODUCT::ASSEMBLING('','All Data');


?>
                    <!-- START CONTENT FRAME TOP -->
                    <ul class="breadcrumb">
    <li><a >Production </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>               
                 


                    <!-- START CONTENT FRAME BODY -->
                    <div class="content-frame-body">
                                                
                        <div class="row push-up-10">
                            <div class="col-md-4">
                                
                                <h3 class="text-danger">Raw Material Collection </h3>

                                <div class="form-group has-error">
                                <input type="text" class="form-control" id="search_colection" placeholder="Search By Batch No | Pi No" onkeyup="search_related(this.value,'search_colection','tasks_collection')">
                                </div>
                                <div class="tasks" id="tasks_collection" style="overflow-y: scroll;height:600px;">


                                     <?php print $flow_chart1['raw_material_collection'];?>
                                    

                                   
                                    
                                </div>                            

                            </div>
                            <div class="col-md-4">
                                <h3  class="text-warning">Requisitioned Raw Material</h3>
                                <div class="form-group has-warning">
                                <input type="text" class="form-control" id="search_requsation" onkeyup="search_related(this.value,'search_requsation','tasks_requsation')">
                                </div>

                                <div class="tasks" id="tasks_requsation"  style="overflow-y: scroll;height:600px;">


                             
                                <?php print $flow_chart2['requisition'];?>

                                                              
                                    
                                  
                                </div>
                            </div>

                            <div class="col-md-4">
                                <h3  class="text-success">Assembling  </h3>
                                <div class="form-group has-success">
                                <input type="text" class="form-control" id="search_assembling" onkeyup="search_related(this.value,'search_assembling','tasks_assembling')">
                                </div>

                                <div class="tasks" id="tasks_assembling"  style="overflow-y: scroll;height:600px;">


                             
                                <?php print $flow_chart3['assembling'];?>

                                                              
                                
                                    
                                </div>
                            </div>


                        </div>                        
                                                
                    </div>
                    <!-- END CONTENT FRAME BODY -->
                    
 

    <!-- START SCRIPTS -->
        <!-- START PLUGINS -->
        <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>

        
        <script type="text/javascript" src="js/demo_tasks.js"></script>
        <!-- END TEMPLATE -->

        