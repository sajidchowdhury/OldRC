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
        $query = $conn_me->prepare("SELECT *,date_format(`date`, '%d-%m-%Y') AS `date` FROM `raw_request_recipe_wise`  WHERE  `warehouse_dispatch` = 'Done' AND `send_for_fitting` = 'Done' GROUP BY `code` ");
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
                                                <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','batch_<?php print $count4;?>','Batch Fitting');"><i class="fa fa-eye"></i></button>      
                                                <a onclick="send_for_fitting('<?php print $fetch['code'];?>','CONFIRMED')" class="btn btn-info btn-rounded btn-sm" ><i class="fa fa-check-square-o"></i>
        </a>
                                            </td>
                                            </tr>
        <?php } ?>
                                        </tbody>
                                    </table> 
                        
                    </div>
                </div>                    
                
            </div>

