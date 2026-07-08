<?php 



    if($_GET['related_id'] == 'New'){

$related_id = 'new_id';
$CODE = SETUP::SETUP_CODE('fg_warehouse_to_warehouse_transfer');
$invoice_no =  '';
$quantity = '';
$product_id = '';
$to_warehouse_id = '';
$from_warehouse_id = '';
$send_date = date("d-m-Y");
$notes = 'no notes';
$dispatcher_id = '';

}else{

    
$DATA  = SETUP::FG_warehouse_to_warehouse_transfer($_GET['related_id']);
$related_id = $DATA['id'];

$product_id = $DATA['product_id'];
$quantity =  $DATA['quantity'];
$invoice_no = $DATA['invoice_no'];
$send_date = $DATA['invoice_date'];
$notes = $DATA['notes'];
$to_warehouse_id =$DATA['to_warehouse_id'];
$from_warehouse_id = $DATA['from_warehouse_id'];
$dispatcher_id = $DATA['dispatcher_id'];




}

if(isset($_GET['invoice_id'])){

    $related_invoice_id = ($_GET['invoice_id'] === 'New') ? 0 : $_GET['invoice_id'];

}else{
    $related_invoice_id = 0;
}


?>
    <div class="row">
                        <div class="col-md-12">
                            
                            <form class="form-horizontal">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Warehouse to Warehouse Transfer  </strong> <b id="mess_box"></b>  </h3>
                                    <ul class="panel-controls" >
                                    
                                    </ul>
                                </div>
    <input type="hidden" name="related_id" id="related_id" value=<?php print $related_id;?>>
    <input type="hidden" name="related_invoice_id" id="related_invoice_id" value=<?php print $related_invoice_id;?>>
    

                                <div class="panel-body">                                                                        
                                    
                                    <div class="row">
                                        
                                        <div class="col-md-6">
                                            
                                        


                                        <div class="form-group">
                                                <label class="col-md-3 control-label">Product Name</label>
                                                <div class="col-md-9">                                                                                            
                            <select id="product_id" name="product_id"  tabindex="1" onchange="itemstock(this.value,<?php print $_SESSION['USER_BRUNCH'];?>,'FG','YES','NO','YES','NO','NO','NO','NO');"   class="form-control select" data-live-search="true">
        <option value="">Select One</option>
<?php 
        $qry = $conn_me->prepare("SELECT * FROM `setup_product` where `in_service` = 'checked'  ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {  ?>

        <option <?php if( $product_id == $fetch['id'] ){ ?> selected = 'selected' <?php }else{ } ?>
         value="<?php print $fetch['id'];?>">P<?php print $fetch['code'];?> - <?php print $fetch['product_name'];?>
        </option>
 <?php } ?>

</select>
<input type="hidden" id="product_price" value="0.00">

                                                    <span class="help-block"></span>
                                                </div>
                                            </div>
                                           
                                            <div class="form-group">                                        
                                                <label class="col-md-3 control-label">Item Stock</label>
                                                <div class="col-md-9 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon">PCS</span>
                                                <input type="number"  READONLY class="form-control text-danger" value="" id="stock_in_pcs" name="stock_in_pcs" >
                                            </div>            
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>




                                            <div class="form-group">                                        
                                                <label class="col-md-3 control-label">Quantity</label>
                                                <div class="col-md-9 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="number" tabindex="2" class="form-control" value="<?php print $quantity;?>" id="quantity" name="quantity" >
                                            </div>            
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label"><b style="color:red;">Transfer From</b></label>
                                                <div class="col-md-9" id="warehouse_list">                                                                                            
                                           
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <label class="col-md-3 control-label"><b style="color:green;">Transfer To</b></label>
                                                <div class="col-md-9">                                                                                            
                                                <select id="to_warehouse_id"  tabindex="4" name="to_warehouse_id"   class="form-control select" data-live-search="true">
        <option value="">Select One</option>
<?php 
        $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse` where status = 1 ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {  ?>

        <option <?php if( $to_warehouse_id == $fetch['id'] ){ ?> selected = 'selected' <?php }else{ } ?>
         value="<?php print $fetch['id'];?>"><?php print $fetch['name'];?>
        </option>
 <?php } ?>

</select>
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>
                                          
                                            
                                           
                                           
                                            
                                        </div>
                                        <div class="col-md-6">
                                            
                                           <div class="form-group">                                        
                                                <label class="col-md-3 control-label">Invoice No.</label>
                                                <div class="col-md-9 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text"  READONLY style="color:red;" class="form-control" value="<?php print $invoice_no;?>" id="invoice_no" name="invoice_no" >
                                            </div>            
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Dispatcher</label>
                                                <div class="col-md-9">                                                                                            
                                                <select id="dispatcher_id" name="dispatcher_id"   class="form-control select" data-live-search="true">
        <option value="">Select One</option>
<?php 
        $qry = $conn_me->prepare("SELECT A.`id`,B.`name` FROM `admin` A JOIN `setup_employee` B ON (`A`.`employee_id` = B.`id`)  WHERE B.`designation` = '20'; ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {  ?>

        <option <?php if( $dispatcher_id == $fetch['id'] ){ ?> selected = 'selected' <?php }else{ } ?>
         value="<?php print $fetch['id'];?>"><?php print $fetch['name'];?>
        </option>
 <?php } ?>

</select>
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>


                                            <div class="form-group">                                        
                                                <label class="col-md-3 control-label">Send Date</label>
                                                <div class="col-md-9">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                        <input type="text" readonly class="form-control" value="<?php print $send_date;?>" id="send_date" name="send_date" >        
                                                    </div>
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>
                                         
                                            
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Notes</label>
                                                <div class="col-md-9 col-xs-12">                                            
                                                    <textarea class="form-control" rows="5" id="notes" name="notes"><?php print $notes;?></textarea>
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>
                                          
                                            
                                        </div>
                                        
                                    </div>

                                </div>
                              
                                <div class="panel-footer">
                                    <?php if($_GET['related_id'] == 'New'){ ?>
                                        <input type="button" id="addcartfgwTow" tabindex="5" onclick="add_cart_fg_wTow()"  name="addcartfgwTow" class="btn btn-info pull-left" value="Add++"  >
                                        <?php  }else{?>
                                            <input style="margin-left:15px;" tabindex="5" type="button" class="btn btn-warning pull-left" value="Update" onclick="add_cart_fg_wTow()"  id="addcartfgwTow" name="addcartfgwTow" >
                                            <a class="btn btn-info pull-right" href="Inventory/Finished-Goods-Warehouse-To-Warehouse/New" >Add New</a>
                                            <?php  }?>
                               
                                <input type="button" class="btn btn-danger pull-right" value="Final Data" onclick="" id="final_wTow" name="final_wTow" >
                                </div>

                            </div>
                            </form>
                            
                        </div>
                    </div>     

<div class="col-md-12" id="refresh_cart">
<?php include('warehouse_to_warehouse_cart.php');?>
</div>


