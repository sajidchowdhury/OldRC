<?php 


?>

<style>

.modal-ku {
  width:100%;
  margin: auto;
}
</style>

<ul class="breadcrumb">
    <li><a href="#">Inventory Management </a></li>          
    <li><a href="#">Add Stock </a></li>                    

    <li class="active">Pending Receive</li>
</ul>


<div class="page-content-wrap">
             <br>
  <!-- PAGE TITLE -->
  <div class="page-title">                    
                <h4><span class="fa fa-anchor"></span> Pending Receive </h4>
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
                                        $count1 = 0;
        $query = $conn_me->prepare("SELECT *,date_format(`date`, '%d-%m-%Y') AS `date` FROM `raw_molding`  WHERE  `status` = 'Done' GROUP BY `code` ORDER BY `date`,`time` ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) { 
            $count1 = $count1+1;
            ?>

<input type="text" style="display:none" id="mold_receive_<?php print $count1?>" value="<?php print $fetch['code'];?>" >
                                            <tr>
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['date'];?></td>
                                                <td><?php print $fetch['invoice_no'];?></td>
                                                <td> 
                                                <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','mold_receive_<?php print $count1;?>','Mold Receive From <?php print $fetch['send_to'];?>');"><i class="fa fa-eye"></i></button>

                                               </td>
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
                                        $count2 = 0;

        $query = $conn_me->prepare("SELECT *,date_format(`date`, '%d-%m-%Y') AS `date`  FROM `raw_spray`  WHERE  `status` = 'Done' GROUP BY `code` ORDER BY `date`,`time` ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {
            $count2 = $count2+1;
            ?>
            <input type="text" style="display:none" id="spray_<?php print $count2?>" value="<?php print $fetch['code'];?>" >
                                            <tr>
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['date'];?></td>
                                                <td><?php print $fetch['invoice_no'];?></td>
                                                <td> 
                                                <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','spray_<?php print $count2;?>','Spray Receive From <?php print $fetch['send_to'];?>');"><i class="fa fa-eye"></i></button>    
                                                
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
                                        $count3 = 0;
        $query = $conn_me->prepare("SELECT *,date_format(`date`, '%d-%m-%Y') AS `date` FROM `raw_print`  WHERE  `status` = 'Done' GROUP BY `code` ORDER BY `date`,`time` ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {$count3 = $count3+1; ?>
                    <input type="text" style="display:none" id="print_<?php print $count3?>" value="<?php print $fetch['code'];?>" >

                                            <tr>
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['date'];?></td>
                                                <td><?php print $fetch['invoice_no'];?></td>
                                                <td>  
                                                <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','print_<?php print $count3;?>','Print Receive From <?php print $fetch['send_to'];?>');"><i class="fa fa-eye"></i></button>    
                                                
                                                
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
                                        $count4 = 0;
        $query = $conn_me->prepare("SELECT *,date_format(`date`, '%d-%m-%Y') AS `date` FROM `raw_request_recipe_wise`  WHERE  `warehouse_dispatch` = 'Done' AND `send_for_fitting` = 'Pending' GROUP BY `code` ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {

            
            $count4 = $count4+1;
            ?>

<input type="text" style="display:none" id="batch_<?php print $count4?>" value="<?php print $fetch['code'];?>" >

                                            <tr>
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['date'];?></td>
                                                <td><?php print $fetch['invoice_no'];?></td>
                                                <td>  
                                                <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','batch_<?php print $count4;?>','Batch Receive:: From <?php print $fetch['send_to'];?>');"><i class="fa fa-eye"></i></button>      
                                                <a onclick="send_for_fitting('<?php print $fetch['code'];?>','DONE')" class="btn btn-info btn-rounded btn-sm" ><i class="fa fa-check-square-o"></i>
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

