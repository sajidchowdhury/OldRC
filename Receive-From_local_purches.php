<?php 

?>
  <!-- PAGE TITLE -->
  <br>

  <ul class="breadcrumb">
    <li><a href="#">Inventory Management </a></li>          
    <li><a href="#">Add Stock </a></li>                    

    <li class="active">Receive From Local Purchase</li>
</ul>


                <!-- END PAGE TITLE -->  
<div class="page-content-wrap">
                
                <div class="row">
                    <div class="col-md-12">
                        
                        <form class="form-horizontal">
                                                        
                            <div class="panel panel-default tabs">                            
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="active"><a href="#tab-first" role="tab" data-toggle="tab">Raw Local Purchase</a></li>
                                    <li><a href="#tab-second" role="tab" data-toggle="tab">FG Local Purchase</a></li>
                                </ul>
                                <div class="panel-body tab-content">
                                    <div class="tab-pane active" id="tab-first">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Date</th>
                                                <th>Batch No</th>
                                                <th>P.I</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $sl =  1;
        $query = $conn_me->prepare("SELECT *,SUM(`quantity`) AS `total_qty` , date_format(`date`, '%d-%m-%Y') AS `date`  FROM `raw_local_purches` where `status` = 'Done' AND `warehouse_receive` = 'Pending'  group by `code` ORDER BY `date`,`time` DESC ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {
            
            $batch_received_return = FIND::RAW_LOCAL_PURCHES_RECEIVE_REJECT($fetch['code'],'','Only_Invoice_Wise');

            
            if( $batch_received_return['actual_receive'] == $fetch['total_qty']){
                FIND::UPDATE_STATUS('warehouse_receive_date','warehouse_receive','warehouse_receive_by','raw_local_purches',$fetch['code']);
            }
            ?>
                                            <tr>
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['date'];?></td>
                                                <td><?php print $fetch['invoice_no'];?></td>
                                                <td><?php print $fetch['note'];?></td>
                                                <td><a href="#modal_large" class="btn btn-danger btn-rounded btn-sm" data-toggle="modal" data-backdrop="static"   data-keyboard="false"  
        data-whatever1="Pending Receive Raw Local Purches" data-whatever2="load_data_in_modal" 
        data-whatever3="<?php print $fetch['code'];?>"><i class="fa fa-eye"></i>
        </a></td>
                                            </tr>
        <?php } ?>
                                        </tbody>
                                    </table>        
                                    </div>
                                    <div class="tab-pane" id="tab-second">
                                       
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Date</th>
                                                <th>Batch No</th>
                                                <th>P.I</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $sl =  1;
        $query = $conn_me->prepare("SELECT * ,  date_format(`invoice_date`, '%d-%m-%Y') AS `invoice_date` FROM `fg_local_purches` where `status` = 'Done' AND `warehouse_receive` = 'Pending'  group by `code`  ORDER BY `invoice_date`,`time` DESC");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) { ?>
                                            <tr>
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['invoice_date'];?></td>
                                                <td><?php print $fetch['invoice_no'];?></td>
                                                <td><?php print $fetch['note'];?></td>
                                                <td><a href="#modal_large" class="btn btn-danger btn-rounded btn-sm" data-toggle="modal" data-backdrop="static"   data-keyboard="false"  
        data-whatever1="Pending Receive FG Local Purchase" data-whatever2="load_data_in_modal" 
        data-whatever3="<?php print $fetch['code'];?>"><i class="fa fa-eye"></i>
        </a></td>
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




