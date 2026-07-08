<?php 

$content ='';

?>
<div class="page-content-wrap">
             <br>
  <!-- PAGE TITLE -->
  <div class="page-title">                    
                <h4><span class="fa fa-anchor"></span> Pending Delivery </h4>
                </div>
                <!-- END PAGE TITLE -->  

                <div class="row">
                    <div class="col-md-12">
                        
                        <form class="form-horizontal">
                                                        
                            <div class="panel panel-default tabs">                            
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="active"><a href="#tab-first" role="tab" data-toggle="tab"> Molding</a></li>
                                    <li><a href="#tab-second" role="tab" data-toggle="tab"> Spray</a></li>
                                    <li><a href="#tab-third" role="tab" data-toggle="tab"> Print</a></li>
                                    <li><a href="#tab-fourth" role="tab" data-toggle="tab"> Batch </a></li>

                                </ul>
                                <div class="panel-body tab-content">
                                    <div class="tab-pane active" id="tab-first">
                                       
                                    <table class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Date</th>
                                                <th>Batch No</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $sl =  1;
        $query = $conn_me->prepare("SELECT * , date_format(`date`, '%d-%m-%Y') AS `date`  FROM `raw_molding`  WHERE  `status` = 'Done' AND  `warehouse_dispatch` = 'Pending' GROUP BY `code` ORDER BY `date`,`time` DESC   ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) { ?>
                                            <tr>   
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['date'];?></td>
                                                <td><?php print $fetch['invoice_no'];?></td>
                                                <td>  <a href="#modal_large" class="btn btn-info btn-rounded btn-sm" data-toggle="modal" data-backdrop="static"   data-keyboard="false"  
        data-whatever1="Pending Warehouse Dispatch For Molding" data-whatever2="load_data_in_modal" 
        data-whatever3=<?php print $fetch['code'];?>><i class="fa fa-eye"></i>

        </a> <a onclick="final_this_task('warehouse_full_dispatch_date','warehouse_dispatch','warehouse_dispatch_by','raw_molding','<?php print $fetch['code'];?>')" class="btn btn-info btn-rounded btn-sm" ><i class="fa fa-check-square-o"></i>
        </a></td>
                                            </tr>
        <?php } ?>
                                        </tbody>
                                    </table>   
                                    </div>
                                    <div class="tab-pane" id="tab-second">
                                       
                                    <table class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Date</th>
                                                <th>Batch No</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $sl =  1;
        $query = $conn_me->prepare("SELECT *,date_format(`date`, '%d-%m-%Y') AS `date`  FROM `raw_spray`  WHERE  `status` = 'Done' AND  `warehouse_dispatch` = 'Pending' GROUP BY `code`  ORDER BY `date`,`time` DESC  ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) { ?>
                                            <tr>
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['date'];?></td>
                                                <td><?php print $fetch['invoice_no'];?></td>
                                                <td>    <a href="#modal_large" class="btn btn-info btn-rounded btn-sm" data-toggle="modal" data-backdrop="static"   data-keyboard="false"  
        data-whatever1="Pending Warehouse Dispatch For Spray" data-whatever2="load_data_in_modal" 
        data-whatever3=<?php print $fetch['code'];?>><i class="fa fa-eye"></i>
        </a>
        </a> <a onclick="final_this_task('warehouse_full_dispatch_date','warehouse_dispatch','warehouse_dispatch_by','raw_spray','<?php print $fetch['code'];?>')" class="btn btn-info btn-rounded btn-sm" ><i class="fa fa-check-square-o"></i>
        </a>
    </td>
                                            </tr>
        <?php } ?>
                                        </tbody>
                                    </table>   
                                    </div>    
                                    
                                    <div class="tab-pane" id="tab-third">
                                       
                                    <table class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Date</th>
                                                <th>Batch No</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $sl =  1;
        $query = $conn_me->prepare("SELECT *,date_format(`date`, '%d-%m-%Y') AS `date`  FROM `raw_print`  WHERE  `status` = 'Done' AND  `warehouse_dispatch` = 'Pending' GROUP BY `code`  ORDER BY `date`,`time` DESC  ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) { ?>
                                            <tr>
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['date'];?></td>
                                                <td><?php print $fetch['invoice_no'];?></td>
                                                <td>  <a href="#modal_large" class="btn btn-info btn-rounded btn-sm" data-toggle="modal" data-backdrop="static"   data-keyboard="false"  
        data-whatever1="Pending Warehouse Dispatch For Print" data-whatever2="load_data_in_modal" 
        data-whatever3=<?php print $fetch['code'];?>><i class="fa fa-eye"></i>
        </a>
        <a onclick="final_this_task('warehouse_full_dispatch_date','warehouse_dispatch','warehouse_dispatch_by','raw_print','<?php print $fetch['code'];?>')" class="btn btn-info btn-rounded btn-sm" ><i class="fa fa-check-square-o"></i>
        </a>
    </td>
                                            </tr>
        <?php } ?>
                                        </tbody>
                                    </table> 

                                    </div>  

                                    <div class="tab-pane" id="tab-fourth">
                                       <table class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Date</th>
                                                <th>Batch No</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $sl =  1;
        $query = $conn_me->prepare("SELECT *,date_format(`date`, '%d-%m-%Y') AS `date`  FROM `raw_request_recipe_wise`  WHERE  `status` = 'Done' AND  `warehouse_dispatch` = 'Pending' GROUP BY `code`  ORDER BY `date`,`time` DESC ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) { 

    $batch_received_return = FIND::RECEIPE_WISE_DEMAND_RECEIVE_REJECT($fetch['code'],'','Only_Invoice_Wise');

    if( $batch_received_return['actual_qty'] == $fetch['batch_quantity']){
         FIND::UPDATE_STATUS('warehouse_full_dispatch_date','warehouse_dispatch','warehouse_dispatch_by','raw_request_recipe_wise',$fetch['code']);

        ?>

   <?php  }
            
    ?>                            <tr>
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['date'];?></td>
                                                <td><?php print $fetch['invoice_no'];?></td>
                                                <td>  <a href="#modal_large" class="btn btn-info btn-rounded btn-sm" data-toggle="modal" data-backdrop="static"   data-keyboard="false"  
        data-whatever1="Pending Receipe wise Demand" data-whatever2="load_data_in_modal" 
        data-whatever3=<?php print $fetch['code'];?>><i class="fa fa-eye"></i>
        </a>
        <a onclick="final_batch_dispatch('<?php print $fetch['code'];?>')" class="btn btn-info btn-rounded btn-sm" ><i class="fa fa-check-square-o"></i>
        </a>       
    </td>
                                            </tr>
        <?php } ?>
                                        </tbody>
                                    </table> 
                                    </div>  


                                    
                                </div>
                            
                            </div>                                
                        
                        </form>
                        
                    </div>
                </div>                    
                
            </div>
