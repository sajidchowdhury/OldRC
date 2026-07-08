<?php 
$xml_raw_material_list = simplexml_load_file("xml_raw_material_list.xml");



    if($_GET['related_id'] == 'New'){
        $related_id = 'new_id';

$invoice_no =  '';
$quantity = '';
$product_id = '';
$writeoff_date = date("d-m-Y");
$notes = 'no notes';
$warehouse_id = '';


}else{

$DATA  = SETUP::RAW_OPENINGSTOCK($_GET['related_id']);
$related_id = $DATA['id'];

$product_id = $DATA['product_id'];
$quantity =  $DATA['quantity'];
$invoice_no = $DATA['invoice_no'];
$writeoff_date = $DATA['invoice_date'];
$notes = $DATA['notes'];
$warehouse_id = $DATA['warehouse_id'];

}
?>
<ul class="breadcrumb">
    <li><a href="#">Inventory Management </a></li>          
    <li><a href="#">Add Stock </a></li>                    

    <li class="active">RAW-Opening-Stock</li>
</ul>


    <div class="row">
                        <div class="col-md-12">
                            
                            <form class="form-horizontal">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Add RAW Material Opening Stock </strong> <b id="mess_box"></b>  </h3>
                                    <ul class="panel-controls" >
                                    
                                    </ul>
                                </div>
    <input type="hidden" name="related_id" id="related_id" value=<?php print $related_id;?>>
                                <div class="panel-body">                                                                        
                                    
                                    <div class="row">
                                        
                                        <div class="col-md-6">
                                            
                                        <div class="form-group">
                                                <label class="col-md-3 control-label">Material Name</label>
                                                <div class="col-md-9">                                                                                            
                                                <select id="product_id" name="product_id"   class="form-control select" data-live-search="true">
        <option value="">Select One</option>
        <?php 
    foreach ( $xml_raw_material_list->ROW as  $value ) { ?>
    <option <?php if($product_id == $value['id'] ){ ?> selected="selected" <?php }else{} ?>  value="<?php print $value['id'];?>"><?php print "$value[product_category] ::: $value[product_name]";?></option>
<?php } ?>

</select>
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">                                        
                                                <label class="col-md-3 control-label">Quantity</label>
                                                <div class="col-md-9 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="number" class="form-control" value="<?php print $quantity;?>" id="quantity" name="quantity" >
                                            </div>            
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>
                                            
                                          
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Warehouse</label>
                                                <div class="col-md-9">                                                                                            
                                                <select id="warehouse_id" name="warehouse_id"   class="form-control select" data-live-search="true">
        <option value="">Select One</option>
<?php 
        $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse`  ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {  ?>

        <option <?php if( $warehouse_id == $fetch['id'] ){ ?> selected = 'selected' <?php }else{ } ?>
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
                                                <label class="col-md-3 control-label">Receive Date</label>
                                                <div class="col-md-9">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                        <input type="text" readonly class="form-control" value="<?php print $writeoff_date;?>" id="writeoff_date" name="writeoff_date" >        
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
                                        <input type="button" id="add_cart_raw_writeoff" name="add_cart_raw_writeoff" class="btn btn-info pull-right" value="Add++"  >
                                        <?php  }else{?>
                                            <input style="margin-left:15px;" type="button" class="btn btn-warning pull-right" value="Update" id="add_cart_raw_writeoff" name="add_cart_raw_writeoff" >
                                            <a class="btn btn-info pull-right" href="Production/RAW-Opening-Stock/New" >Add New</a>
                                            <?php  }?>
                               
                                <input type="button" class="btn btn-danger pull-left" value="Final Data" onclick="" id="final_raw_writeoff" name="final_raw_writeoff" >
                                </div>

                            </div>
                            </form>
                            
                        </div>
                    </div>     

<div class="col-md-12" id="refresh_cart">
<?php include('raw-opening-stock-cart.php');?>
</div>