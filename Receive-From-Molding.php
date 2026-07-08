   <!-- START CONTENT FRAME BODY -->
   <div class="content-frame-body">
                                                
                                                <div class="row push-up-10">
                                                    <div class="col-md-12">
                                                        
                                                        <h3>Supplier wise Pending Molding Receive</h3>
                                                        
                                                        <div class="tasks" id="tasks">
                        
                                                         
                                                     
<?php




$sl =1;
$qry = $conn_me->prepare("SELECT * FROM `raw_molding` GROUP BY `supplier_id` ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
if ($qry->rowCount() > 0)
{
    foreach($fetch_list AS $fetch) {
        $supplier_info = SETUP::SETUP_SUPPLIER($fetch['supplier_id']);
        $pending_molding = FIND::SUPPLIER_WISE_PENDING_MOLDING($fetch['supplier_id'])
        ?>
<div class="col-md-2">
        <div class="task-item task-primary">                                    
        <div class="task-text"><?php print "Supplier: <b style=\"color:red\">$supplier_info[supplier_name]</b> <br>Total Product <b style=\"color:orange\">$pending_molding[pending] </b> Unit" ;?></div>
        <div class="task-footer">
            <div class="pull-left">'<a href="#modal_large" class="btn btn-info btn-rounded btn-sm" data-toggle="modal" data-backdrop="static"   data-keyboard="false"  
                    data-whatever1="Supplier wise Molding Workstation" data-whatever2="load_data_in_modal" 
                    data-whatever3="<?php print $fetch['supplier_id'];?>"><i class="fa fa-expand"></i>
                    </a>
</div>                                    
        </div>                                    
    </div>
    </div>





   <?php } }

    ?>


       
                                                            
</div>                            
                        
                        </div>
                        
                    
                    </div>                        
                                            
                </div>
                <!-- END CONTENT FRAME BODY -->