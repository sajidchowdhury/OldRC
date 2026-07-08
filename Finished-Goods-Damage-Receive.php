<?php 

?>
<div class="page-content-wrap">

<ul class="breadcrumb">
    <li><a href="#">Inventory Management </a></li>           
    <li><a href="#">Damage Info </a></li>                             
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        



                <div class="row">
                    <div class="col-md-12">
                        
                        <form class="form-horizontal">
                                                        
                            <div class="panel panel-default tabs">                            
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="active"><a href="#tab-first" role="tab" data-toggle="tab"> Damage From Store</a></li>
                                    <li><a href="#tab-second" role="tab" data-toggle="tab">Pending Damage Receive</a></li>
                                  

                                </ul>
                                <div class="panel-body tab-content">
                                    <div class="tab-pane active" id="tab-first">
                                    <?php 



if($_GET['related_id'] == 'New'){

$related_id = 'new_id';
$CODE = SETUP::SETUP_CODE('fg_damage_store');
$invoice_no =  '';
$quantity = '';
$product_id = '';
$warehouse_id = '';
$invoice_date = date("d-m-Y");
$notes = 'no notes';
$dispatcher_id = '';


}else{


    $DATA  = SETUP::FG_DAMAGE_STORE($_GET['related_id']);
    $related_id = $DATA['id'];
    
    $product_id = $DATA['product_id'];
    $quantity =  $DATA['quantity'];
    $invoice_no = $DATA['invoice_no'];
    $invoice_date = $DATA['invoice_date'];
    $notes = $DATA['notes'];
    $warehouse_id = $DATA['warehouse_id'];
    $dispatcher_id = $DATA['dispatcher_id'];





}



?>
<div class="row">
                    <div class="col-md-12">
                    <b id="mess_box"></b>
                        <div class="panel panel-default">
                         
<input type="hidden" name="related_id" id="related_id" value=<?php print $related_id;?>>

                            <div class="panel-body">                                                                        
                                
                                <div class="row">
                                    
                                    <div class="col-md-6">
                                        
                                    


                                    <div class="form-group">
                                            <label class="col-md-3 control-label">Product Name</label>
                                            <div class="col-md-9">                                                                                            
                        <select id="product_id" name="product_id"  onchange="itemstock(this.value,<?php print $_SESSION['USER_BRUNCH'];?>,'FG','YES','NO','YES','NO','NO','NO','NO');"   class="form-control select" data-live-search="true">
    <option value="">Select One</option>
<?php 
    $qry = $conn_me->prepare("SELECT * FROM `setup_product` where `in_service` = 'checked' ");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) {  ?>

    <option <?php if( $product_id == $fetch['id'] ){ ?> selected = 'selected' <?php }else{ } ?>
     value="<?php print $fetch['id'];?>"><?php print $fetch['product_name'];?>
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
                                            <label class="col-md-3 control-label"><b style="color:red;">Damage From</b></label>
                                            <div class="col-md-9" id="warehouse_list">                                                                                            
                                       
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
                                            <label class="col-md-3 control-label">Invoice Date</label>
                                            <div class="col-md-9">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                    <input type="text" readonly class="form-control" value="<?php print $invoice_date;?>" id="invoice_date" name="invoice_date" >        
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
                                    <input type="button" id="add_cart_damage"   name="add_cart_damage" class="btn btn-info pull-left" value="Add++"  >
                                    <?php  }else{?>
                                        <input style="margin-left:15px;" type="button" class="btn btn-warning pull-left" value="Update" id="add_cart_damage" name="add_cart_damage" >
                                        <a class="btn btn-info pull-right" href="Inventory/Finished-Goods-Warehouse-To-Warehouse/New" >Add New</a>
                                        <?php  }?>
                           
                            <input type="button" class="btn btn-danger pull-right" value="Final Data" onclick="" id="final_damage" name="final_damage" >
                            </div>

                        </div>
            
                        
                    </div>
                </div>     

                <div class="col-md-12" id="refresh_cart">
<?php include('damage-cart.php');?>
</div>

</div>   
   



                             
                                    <div class="tab-pane" id="tab-second">

                                        
                                    <table class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Date</th>
                                                <th>Invocie No</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $sl =  1;
                                        $count1 = 0;
        $query = $conn_me->prepare("SELECT * , date_format(`invoice_date`, '%d-%m-%Y') AS `invoice_date`  FROM `damage_invoice`  WHERE  `status` = 'Done' AND  `warehouse_receive` = 'Pending'   ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) { 
            
            $count1 = $count1+1;
            ?><input type="text" style="display:none" id="dc_<?php print $count1?>" value="<?php print $fetch['code'];?>" >
                                            <tr>   
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['invoice_date'];?></td>
                                                <td><?php print $fetch['invoice_no'];?></td>
                                                <td> 
                                               <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','dc_<?php print $count1;?>','Pending Damage Receive');"><i class="fa fa-eye"></i></button>



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
