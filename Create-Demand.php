<?php 
$xml_product_list = simplexml_load_file("xml_productList.xml");





$DATA  = DEMAND::SETUP_DEMAND_ITEM($_GET['related_id']);

$invoice_id = !empty($DATA['fetch']['demand_id']) ? $DATA['fetch']['demand_id'] : 'New' ; 
$DATA2  = DEMAND::SETUP_DEMAND($invoice_id);

$related_id = !empty($DATA['fetch']['id']) ? $DATA['fetch']['id'] : 'New';
$product_id = !empty($DATA['fetch']['product_id']) ? $DATA['fetch']['product_id'] : '';
$invoice_no = !empty($DATA2['fetch']['invoice_no']) ? $DATA2['fetch']['invoice_no'] : 'NEW DEMAND';
$quantity =  !empty($DATA['fetch']['quantity']) ? $DATA['fetch']['quantity'] : '';
$invoice_date = !empty($DATA2['fetch']['invoice_date']) ? $DATA2['fetch']['invoice_date'] : date("d-m-Y");
$notes = !empty($DATA2['fetch']['notes']) ? $DATA2['fetch']['notes'] : 'no notes';
$brunch_id = !empty($DATA2['fetch']['brunch_id']) ? $DATA2['fetch']['brunch_id'] : '';
$demand_type = !empty($DATA2['fetch']['demand_type']) ? $DATA2['fetch']['demand_type'] : '';

$edit_bustton = '<a class="btn btn-info pull-right" href="edit/Create-Demand/New/'.$invoice_id.'" > Add New </a>';


?>

<input type="hidden" READONLY class="form-control" value="<?php print $invoice_date;?>" id="invoice_date" name="invoice_date" >        


<ul class="breadcrumb">
    <li><a href="#">Demand </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>

    <div class="row">
                        <div class="col-md-12">
                            
                            <form class="form-horizontal">
                            <div class="panel panel-default">
                               
    <input type="hidden" name="related_id" id="related_id" value=<?php print $related_id;?>>
    <input type="hidden" name="invoice_id" id="invoice_id" value=<?php print $invoice_id;?>>

                                <div class="panel-body">                                                                        
                                    
                                    <div class="row">
                                        
                                        <div class="col-md-6">
                                            
                                        <div class="form-group">
                                                <label class="col-md-3 control-label">Product Name</label>
                                                <div class="col-md-9">                                                                                            
                                                <select id="product_id" name="product_id"   class="form-control select" data-live-search="true">
        <option value="">Select One</option>
        <?php 
    foreach ( $xml_product_list->ROW as  $value ) { ?>
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
                                                <label class="col-md-3 control-label"></label>
                                                <div class="col-md-9 col-xs-12">
                                            <div class="input-group">
                                               <?php if($_GET['related_id'] == 'New'){ ?>
                                        <input type="button" id="add_cart" name="add_cart" onclick="add_cart_demand();" class="btn btn-info pull-right" value="Add++"  >
                                        <?php  }else{?>
                                            <input style="margin-left:15px;" type="button" onclick="add_cart_demand();"  class="btn btn-warning pull-right" value="Update" id="add_cart" name="add_cart" >
 
                                            
                                            <?php  print $edit_bustton ; }?>
                                            </div>            
                                                                                                        <b id="mess_box"></b>

                                                </div>
                                            </div>

                                            
                                          
                                        
                                            
                                        </div>
                                        <div class="col-md-6">
                                            

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Brunch Name</label>
                                                <div class="col-md-9">                                                                                            
      <select class="form-control select" id="brunch_id" name = "brunch_id"  >
                <option value="">Select One</option>

<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where  id <> '".$_SESSION['USER_BRUNCH']."' AND   status = 'Active'");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { ?>
<option  <?php if($brunch_id == $fetch['id'] ){ ?> selected="selected" <?php }else{} ?> value="<?php print $fetch['id'];?>"><?php print $fetch['brunch'] ;?></option>
<?php } ?>
</select>
       

                                                    <span class="help-block"></span>
                                                </div>
                                            </div>


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
                                                <label class="col-md-3 control-label">Notes</label>
                                                <div class="col-md-9 col-xs-12">                                            
                                                    <textarea class="form-control" rows="5" id="notes" name="notes"><?php print $notes;?></textarea>
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>
                                          

                                          <div class="form-group">                                        
                                                <label class="col-md-3 control-label"></label>
                                                <div class="col-md-9 col-xs-12">
                                            <div class="input-group">
                                               <input type="button" class="btn btn-danger pull-left"  onclick="final_demand();"  value="Final Data" id="fianl_data" name="fianl_data" >
                                            </div>            
                                                    
                                                </div>
                                            </div>

                                            
                                        </div>
                                        
                                    </div>

                                </div>
                              
                                

                            </div>
                            </form>
                            
                        </div>
                    </div>     

<div class="col-md-12" id="refresh_cart">
<?php include('demand-cart.php');?>
</div>