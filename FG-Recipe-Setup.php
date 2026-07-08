<?php 

$xml_product_list = simplexml_load_file("xml_productList.xml");
$xml_raw_material_list = simplexml_load_file("xml_raw_material_list.xml");


    if($_GET['related_id'] == 'New'){

$related_id = 'new_id';
$quantity = '';
$product_id = '';
$raw_material_id= '';

if($_GET['product_id'] == 'New'){
    $product_id = '';
}else{
    $product_info  = SETUP::SETUP_PRODUCT($_GET['product_id']);
    $product_id = $product_info['id'];
}

}else{

$DATA  = SETUP::SETUP_FG_RECIPE($_GET['related_id']);
$related_id = $DATA['id'];
$product_id = $DATA['product_id'];
$raw_material_id = $DATA['raw_material_id'];
$quantity = $DATA['quantity'];




}
?>

<ul class="breadcrumb">
    <li><a href="#">Production </a></li>           
    <li><a href="#">Settings </a></li>                             
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>


    <div class="row">
                        <div class="col-md-12">
                            
                            <form class="form-horizontal">
                            <div class="panel panel-default">
                                
    <input type="hidden" name="related_id" id="related_id" value="<?php print $related_id;?>">
    <input type="hidden" name="previous_recipe" id="previous_recipe" value="">

    

                                <div class="panel-body">                                                                        
                                    
                                    <div class="row">
                                        
                                        <div class="col-md-12">
                                            
                                        <fieldset>
                                        <legend>Finished Good Information</legend><b id="mess_box"></b>

                                        <div class="form-group">
                                                <label class="col-md-3 control-label">Product Name</label>
                                                <div class="col-md-9">                                                                                            
                                                <select id="product_id" name="product_id"  onchange="findRecipeSetupOrnot('receip_fg','product_id',this.value)"  class="form-control select" data-live-search="true">
        <option value="">Select One</option>
<?php 
    
foreach ( $xml_product_list->ROW as  $value ) { ?>
    <option <?php if($product_id == $value['id'] ){ ?> selected="selected" <?php }else{} ?>  value="<?php print $value['id'];?>"><?php print "$value[product_category] ::: $value[product_name]";?></option>
<?php }
?>

</select>
                                                    <span   class="help-block text-danger" id="mess_load"></span>
                                                </div>
                                            </div>
                                    
                                              
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Estimated FG Unit</label>
                                                <div class="col-md-9">                                                                                            
                                                <select id="fg_unit" name="fg_unit"    class="form-control select">
        <option value="">Select One</option><?php 
        $qry = $conn_me->prepare("SELECT * FROM `setup_unit`  ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {  ?>

        <option  value="<?php print $fetch['id'];?>"><?php print $fetch['unit'];?>
        </option>
 <?php } ?>

</select>
                                                    <span   class="help-block1"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">                                        
                                                <label class="col-md-3 control-label">Estimated FG Qty </label>
                                                <div class="col-md-9 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon">

                                                

                                                </span>
                                                <input type="number" class="form-control" value="1"  onkeyup="calculate_actual_qty();" id="ess_fg_quantity" name="ess_fg_quantity" >
                                            </div>            
                                                    <span class="help-block"></span>
                                                </div>
                                            </div> 

                                    </fieldset>


                                      
                                    <fieldset>
                                        <legend>RAW Material Information</legend>

                                        <div class="form-group">
                                                <label class="col-md-3 control-label">Raw Material Name</label>
                                                <div class="col-md-9">                                                                                            
                                                <select id="raw_material_id" name="raw_material_id"  class="form-control select" data-live-search="true">
                                                <option value="">Select One</option>
                                                <?php 
    
foreach ( $xml_raw_material_list->ROW as  $value ) { ?>
    <option <?php if($raw_material_id == $value['id'] ){ ?> selected="selected" <?php }else{} ?>  value="<?php print $value['id'];?>"><?php print "$value[product_category] ::: $value[product_name]";?></option>
<?php }
?>
                                           

</select>
                                                    <span  class="help-block2"></span>
                                                </div>
                                            </div>

                                               
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Estimated Raw Unit</label>
                                                <div class="col-md-9">                                                                                            
                                                <select id="raw_unit" name="raw_unit"    class="form-control select" data-live-search="true">
                                                <option value="">Select One</option><?php 
        $qry = $conn_me->prepare("SELECT * FROM `setup_unit`  ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {  ?>

        <option  value="<?php print $fetch['id'];?>"><?php print $fetch['unit'];?>
        </option>
 <?php } ?>

</select>
                                                    <span   class="help-block1"></span>
                                                </div>
                                            </div>



                                            <div class="form-group">                                        
                                                <label class="col-md-3 control-label">Estimated Raw Qty </label>
                                                <div class="col-md-9 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span id="help-block2"></span></span>
                                                <input type="number" class="form-control" value="1" onkeyup="calculate_actual_qty();" id="ess_raw_quantity" name="ess_raw_quantity" >
                                            </div>            
                                                    <span class="help-block"></span>
                                                </div>
                                            </div> 



                                            <div class="form-group">                                        
                                                <label class="col-md-3 control-label">Actual Quantity</label>
                                                <div class="col-md-9 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span id="help-block3"></span></span>
                                                <input type="number" class="form-control" value="<?php print $quantity;?>" id="quantity" name="quantity" >
                                            </div>            
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>
                                            

                                        </fieldset>
                                           

                                            
                                          
                                   
                                           
                                            
                                        </div>
                                        
                                        
                                    </div>

                                </div>
                              
                                <div class="panel-footer">
                                    <?php if($_GET['related_id'] == 'New' && $_GET['product_id'] == 'New'){ ?>
                                        <input type="button" id="add_cart_fg_recipe" name="add_cart_fg_recipe" class="btn btn-info pull-right" value="Add++"  >
                                        <?php  }else{?>
                                            <input style="margin-left:15px;" type="button" class="btn btn-warning pull-right" value="Update" id="add_cart_fg_recipe" name="add_cart_fg_recipe" >
                                            <a class="btn btn-info pull-right" href="Recipe/FG-Recipe-Setup/<?php print $_GET['related_id'];?>/New" >Add New</a>
                                            <?php  }?>
                               
                                <input type="button" class="btn btn-danger pull-left" value="Final Data" onclick="" id="final_fg_recipe" name="final_fg_recipe" >
                                </div>

                            </div>
                            </form>
                            
                        </div>
                    </div>     

<div class="col-md-12" id="refresh_cart" >
<?php include('fg-recipe-cart.php');?>
</div>