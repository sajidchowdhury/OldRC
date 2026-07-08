<?php 
$xml_product_list = simplexml_load_file("xml_productList.xml");


if($_GET['related_id'] == 'New'){


$related_id = 'new_id';
$quantity = '0.00';
$product_id = '';
$pi_no = '';
$accepting_delivery_date = date('d-m-Y');
$button_text = 'Add++';
$send_to = '';
$send_to_id = '';
$note = '';
}else{

$DATA  = SETUP::RAW_REQUEST_RECEIPE_WISE($_GET['related_id']);

$related_id = $DATA['id'];
$quantity = $DATA['batch_quantity'];
$product_id = $DATA['product_id'];
$pi_no = $DATA['pi_no'];
$accepting_delivery_date =  $DATA['accepting_delivery_date'];
$send_to = $DATA['send_to'];
$send_to_id = $DATA['send_to_id'];
$button_text = 'Update';
$note = $DATA['note'];

?>
<script>
    window.onload = function() {
        calculate_batch();
};
</script>
<?php 
}
$permission_management = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],$button_text,'create_recipe_wise_demand','');

?>

<ul class="breadcrumb">
    <li><a href="#">Production </a></li>         
    <li><a href="#">Demand </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
         


<!-- PAGE TITLE -->

                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                    
                    <div class="row">
                    <div class="col-md-8">
                        
                    <div class="panel-body">
                    <div class="table-responsive">

                                <table class="table table-hover table-condensed">
                                <input type="hidden" id="related_id" value="<?php print $related_id;?>">


<tr>
    <td class="form-group required control-label">Assembling From</td>
    <td  colspan="2">
    <select id="send_to" name="send_to" onchange="supplier_or_factory(this.value);"  class="form-control select" data-live-search="true">
    <option value="">Select One</option>
    <option <?php if($send_to == 'Supplier') { ;?> selected="selected" <?php } ;?> value="Supplier">Supplier</option>
    <option <?php if($send_to == 'Factory') { ;?> selected="selected" <?php } ;?>  value="Factory">Factory</option>


    </select>
    </td>

    <td class="form-group required control-label">Deadline</td>
    <td class="tg-0lax" colspan="2" id="load_selct_box">
    <input    type="text" class="date form-control" value="<?php print $accepting_delivery_date ;?>" id="accepting_delivery_date" name="accepting_delivery_date" >

    </td>

  
   



</tr>

<tr>
<td id="level_name" class="form-group required control-label">Select Supplier/Factory</td>
<td   colspan="2" id="level_data"></td>

<td class="form-group required control-label">PI No</td>
    <td class="tg-0lax" colspan="2" >
    <input  type="text" placeholder="Ex. Write PI No." class="form-control" value="<?php print $pi_no;?>" id="pi_no" name="pi_no" >
    </td>

</tr>

<tr>
    <td colspan="3"></td>
<td>Notes</td>
    <td class="tg-0lax" colspan="2" >
    <textarea class="form-control"  placeholder="write any notes" rows="3" id="note" name="note"><?php print $note;?></textarea>
    </td>

</tr>



<tr>
    <td colspan="3"></td>    
    <td class="form-group required control-label"> Product Name</td>
<td  colspan="2">
<select id="product_id" name="product_id" onchange="calculate_batch();"  class="form-control select" data-live-search="true">
    <option value="">Select One</option>
    <?php 
    foreach ( $xml_product_list->ROW as  $value ) { ?>
    <option <?php if($product_id == $value['id'] ){ ?> selected="selected" <?php }else{} ?>  value="<?php print $value['id'];?>"><?php print "$value[product_category] ::: $value[product_name]";?></option>
<?php } ?>



</select>
</td>
</tr>

<tr>
    <td colspan="3"></td>    
    <td class="form-group required control-label">Batch Qty</td>
    <td  colspan="2"> <input type="number" class="form-control" value="<?php print $quantity;?>"   onkeyup="calculate_batch();" id="batch_quantity" name="batch_quantity" >
</tr>

<tr>
    <td  colspan="6" id="for_bulk_uplaod"><?php print  $permission_management['save_update_buton'];?></td>
  </tr>
</table>
    </div>
</div>

                    <div class="panel panel-default">
                                <div class="panel-body">
                                    <h3 class="push-down-10">Receipe List</h3>
                                </div>
                                <div class="panel-body faq" id="refresh_cart"> </div>
                            </div>


                </div>
                        <div class="col-md-4">
                        <div class="push-up-10"  id="refresh_cart2">
                            
                        <?php include('cart_request_raw_material_batch.php');?> 
                        </div>
                        </div>
                        
                        </div>
                    </div>
                                                            
                </div>
                <!-- END PAGE CONTENT WRAPPER -->     