<?php 
if($_GET['related_id'] == 'New'){

    $related_id = 'new_id';
    $supplier_id = '';
    $material_id = '';
    $supplier_bill_no = '';
    $purches_type = '';
    $purches_price = 0.00;
    $quantity = '' ;
    $button_text = 'Add++';
    $supplier_bill_date = date('d-m-Y');
    $invoice_no = '';
    $invoice_date = '';
    $note = '';
    $code = '';
   

}else{

$explode = explode("_ID_",$_GET['related_id']);


if($explode[1] == 'New'){

    $related_id = 'new_id';
    $button_text = 'Add++';
    $code = '';

}else{
    $related_id = $explode[1];
    $code = $explode[4];
}
    $supplier_id = '';
    $material_id = '';
    $supplier_bill_no = '';
    $purches_type = $explode[2];

    $purches_price = 0.00;
    $quantity = '' ;
    $supplier_bill_date = '';
    $invoice_no = '';
    $invoice_date = '';
    $note = $explode[3];
    
}
$permission_management1 = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],$button_text,'add_local_purches','');
$permission_management2 = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],'Final Data','final_local_purches','');

?>
<!-- PAGE TITLE -->

<style>
  .salestable>thead>tr>th, .salestable>tbody>tr>th, .salestable>tfoot>tr>th, .salestable>thead>tr>td, .salestable>tbody>tr>td, .salestable>tfoot>tr>td {
    padding: 5px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}

.salestable2>thead>tr>th, .salestable2>tbody>tr>th, .salestable2>tfoot>tr>th, .salestable2>thead>tr>td, .salestable2>tbody>tr>td, .salestable2>tfoot>tr>td {
    padding: 1px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}

</style>


<ul class="breadcrumb">
    <li><a href="#">Inventory Management </a></li>           
    <li><a href="#">Add Stock </a></li>                             
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
           

                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                    
                    <div class="row">
                        <div class="col-md-8">
                            
                            <!-- START DEFAULT DATATABLE -->
                            <div class="panel panel-default">
                       
                                <div class="panel-body">
                                <table class="table table-condensed">
                                <input type="hidden" id="local_related_id" value="<?php print $related_id;?>">
                                <input type="hidden" id="code" value="<?php print $code;?>">



<thead>
  <tr>
    <td class="form-group required control-label">Select Supplier</td>
    <td >
        <select id="supplier_id" name="supplier_id"  onchange="fatch_supplier_info(this.value)"  class="form-control select" data-live-search="true">
<option value="">Select One</option>
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_supplier`  ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) {  ?>

<option <?php if( $supplier_id == $fetch['id'] ){ ?> selected = 'selected' <?php }else{ } ?>
value="<?php print $fetch['id'];?>"><?php print $fetch['mobile'] . '-' . $fetch['supplier_name'];?>
</option>
<?php } ?>

</select>
</td>
    <td >
    <a  href="sales/Supplier-Setup/New" target="_blink" class="btn btn-danger" > <span class="fa fa-plus-circle"> </span></a>

    
   </td>
    <td class="form-group required control-label">Purchase Type</td>
    <td colspan="2">
    <select id="purches_type" name="purches_type"  onchange="fg_or_raw(this.value);"  class="form-control select" >
<option value="">Select One</option>
<option <?php if( $purches_type == 'raw_local_purches' ){ ?> selected = 'selected' <?php }else{ } ?> value="raw_local_purches">Raw Material</option>
<option  <?php if( $purches_type == 'fg_local_purches' ){ ?> selected = 'selected' <?php }else{ } ?>  value="fg_local_purches">Finishied Goods</option>
</select>

    </td>

  </tr>
</thead>
<tbody>
  <tr>
    <td >Mobile</td>
    <td  colspan="2">
    <input type="text" READONLY name="mobile" id="mobile" value=""  style="color:red;"class="form-control">
    </td>
    <td >Supplier Bill No.</td>
    <td  colspan="2">
    <input type="text" name="supplier_bill_no" id="supplier_bill_no" value="<?php print $supplier_bill_no;?>" class="form-control">
    </td>
  </tr>
  <tr>
    <td >Address</td>
    <td  colspan="2">
    <textarea class="form-control" rows="2" READONLY id="address" name="address" style="color:red;" ></textarea>
    </td>
    <td class="form-group required control-label">Supplier Bill Date</td>
    <td  colspan="2">
    <input type="text" name="supplier_bill_date" id="supplier_bill_date" value="<?php print $supplier_bill_date;?>" class="date form-control">

    </td>
  </tr>
  <tr>
    <td >Note</td>
    <td  colspan="2"> <textarea class="form-control"   id="note" name="note"  ><?php print $note;?></textarea></td>

    <td class="form-group required control-label">Product List</td>
    <td  id="load_selct_box">
    <select id="material_id" name="material_id"  class="form-control select" data-live-search="true">
<option value="">Select One</option>

</select>

    </td>
    <td >
    <a  href="Setup/Raw-Material-Setup/New" target="_blink" class="btn btn-danger" > <span class="fa fa-plus-circle"> </span></a>

   
    </td>
  </tr>
  <tr>
    <td  colspan="2" rowspan="2" style="vertical-align : middle;text-align:center;background-color:#E5E5E5"><h2 stype=" font-kerning: none; font-family:Arial;color:#941d63"> <b id="price_tag">0.00</b> &#2547;
</h2></td>
    <td ></td>
    <td >Purchase Price</td>
    <td  colspan="2">
    <input type="number"  onkeyup="calculate_purches_price();" name="purches_price" id="purches_price" value="<?php print $purches_price;?>" class="form-control">
    </td>
  </tr>
  <tr>
    <td ></td>
    <td class="form-group required control-label">Quantity</td>
    <td  colspan="2">

    <div class="input-group">
                        <span class="input-group-addon"><b id="unittext">Unit</b></span>
                       <input type="number" onkeyup="calculate_purches_price();"  name="quantity" id="quantity" value="<?php print $quantity;?>" class="form-control">
    </div>
    </td>
  </tr>
  <tr>
    <td ></td>
    <td ></td>
    <td ></td>
    <td  colspan="3">
        
    <b id="mess_box"></b> <?php print $permission_management1['save_update_buton'];?>
    </td>
  </tr>
</tbody>
</table>
                                </div>
                            </div>
                            <!-- END DEFAULT DATATABLE -->
                            
                            <div class="panel panel-default">
                              
                                <div class="panel-body faq" id="refresh_cart">
                                    
                                <?php include('cart_local_purches.php');?>
                                </div>
                            </div>
                            
                        </div>                        
                        <div class="col-md-4">
                            
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                   
                                    <div class="push-up-10">
                                        <table class="table table-hover table-condensed">
                                 
                                        <tr>
                                            <th>Sub Total</th>
                                            <td><input type="number" READONLY  name="sub_total" id="sub_total" value="<?php print $cart_sub_total ;?>" class="form-control text-info"></td>
                                        </tr>
                                    
                                        <tr>
                                            <th>Transport  Cost</th>
                                            <td><input type="number"    onkeyup="purchase_calculation()"  name="total_transport_cost"  id="total_transport_cost" value="0.00" class="form-control"></td>
                                        </tr>
                                        <tr>
                                            <th>VAT</th>
                                            <td><input type="number"  onkeyup="purchase_calculation()"   name="total_vat_cost"  id="total_vat_cost" value="0.00" class="form-control"></td>
                                        </tr>

                                        <tr>
                                            <th> Invoice Price </th>
                                            <td><input type="number" READONLY  name="invoice_total" id="invoice_total" value="0.00" class="form-control text-info"></td>
                                        </tr>
                                      


                    
                                      
                                        <tr>
                                            <th>Previous Due</th>
                                            <td><input type="number"   name="amount_due"  readonly id="amount_due" value="0.00" class="form-control text-info"></td>
                                        </tr>
                                     
                                        <tr>
                                        <td  colspan="2"> </td>

                                        </tr>
                                        <tr>
                                            <td  colspan="2"> <?php print $permission_management2['save_update_buton'];?></td>
                                            </tr>
                                    </table>

                                        </div>                                       
                                    </div>                                    
                                </div>
                            </div>
                            
                        
                        </div>
                    </div>
                                                            
                </div>
                <!-- END PAGE CONTENT WRAPPER -->     