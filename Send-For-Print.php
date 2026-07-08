<?php 

if($_GET['related_id'] == 'New' ){


    $related_id = 'new_id';
    $batch_quantity = '1';
    $button_text = 'Add++';
    $note = '';
$send_to = '';
$print_material_id = '';
}else{
    $explode = explode("_ID_",$_GET['related_id']);

    if($explode[1] == 'New'){

        $related_id = 'new_id';
        $button_text = 'Add++';
    
    }else{
        $related_id = $explode[1];
        $button_text = 'Update';
    }
    $note =  $explode[2];
    $batch_quantity = '1';
    
    $send_to = '';
    $print_material_id = '';
    
}



$permission_management = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],$button_text,'create_print_recipe_wise_demand','');


?>
<!-- PAGE TITLE -->
<ul class="breadcrumb">
    <li><a >Production </a></li>   
    <li><a href="#">Demand </a></li>          
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>              
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                    
                    <div class="row">
                        <div class="col-md-8">
                            
                            <!-- START DEFAULT DATATABLE -->
                            <div class="panel panel-default">
                                <div class="panel-heading">                                
                                    <h3 class="panel-title">Basic Information</h3>
                                    <ul class="panel-controls">
                                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                                    </ul>                                
                                </div>
                                <div class="panel-body">
                                <div class="table-responsive">

                                <table class="table table-hover table-condensed">
                                <input type="hidden" id="related_id" value="<?php print $related_id;?>">


<tr>
    <td class="form-group required control-label">Print From</td>
    <td  colspan="2">
    <select id="send_to" name="send_to" onchange="supplier_or_factory(this.value);"  class="form-control select" data-live-search="true">
    <option value="">Select One</option>
    <option <?php if($send_to == 'Supplier') { ;?> selected="selected" <?php } ;?> value="Supplier">Supplier</option>
    <option <?php if($send_to == 'Factory') { ;?> selected="selected" <?php } ;?>  value="Factory">Factory</option>


    </select>
    </td>

    <td class="form-group required control-label">Deadline</td>
    <td class="tg-0lax" colspan="2" id="load_selct_box">
    <input    type="text" class="date form-control" value="<?php print date('d-m-Y');?>" id="accepting_delivery_date" name="accepting_delivery_date" >

    </td>

  
   



</tr>

<tr>
<td id="level_name" class="form-group required control-label">Select Supplier/Factory</td>
<td   colspan="2" id="level_data"></td>

<td>Notes</td>
    <td class="tg-0lax" colspan="2" >
    <textarea class="form-control"  placeholder="write any notes" rows="3" id="note" name="note"><?php print $note;?></textarea>
    </td>

</tr>





<tr>
    <td colspan="3"></td>    
    <td class="form-group required control-label"> Product Name</td>
<td  colspan="2">
<select id="print_material_id" name="print_material_id" onchange="print_batch_calculation();"  class="form-control select" data-live-search="true">
    <option value="">Select One</option>
<?php 
        $qry = $conn_me->prepare("SELECT * FROM `receip_print`  GROUP BY `print_material_id` ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {  
            $product_info = SETUP::SETUP_RAW_MATERIAL($fetch['print_material_id']);

            ?>

        <option <?php if($print_material_id == $fetch['print_material_id']) { ?>  selected="selected" <?php }else{};?> value="<?php print $fetch['print_material_id'];?>"><?php print $product_info['category'] . '-' .$product_info['product_name'];?>
        </option>
 <?php } ?>

</select>
</td>
</tr>

<tr>
    <td colspan="3"></td>    
    <td class="form-group required control-label">Batch Qty</td>
    <td  colspan="2"> <input type="number" class="form-control" value="<?php print $batch_quantity;?>"   onkeyup="print_batch_calculation();" id="batch_quantity" name="batch_quantity" >
</tr>

<tr>
    <td  colspan="6" ><?php print  $permission_management['save_update_buton'];?></td>
  </tr>
</table>
        </div>



                                </div>
                            </div>
                            <!-- END DEFAULT DATATABLE -->
                            
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h3 class="push-down-10">Receipe List</h3>
                                </div>
                                <div class="panel-body faq" id="refresh_cart"> </div>
                            </div>
                            
                        </div>       
                        <div class="col-md-4">

                        <div class="push-up-10"  id="refresh_cart2">
                            
                        <?php include('cart_request_raw_material_print.php');?> 
                        </div>
                        </div>
                        
                        </div>
                    </div>
                                                            
                </div>
                <!-- END PAGE CONTENT WRAPPER -->     