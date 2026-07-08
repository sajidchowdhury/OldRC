<?php 



    if($_GET['related_id'] == 'New'){

        $related_id = 'new_id';

$quantity = '';
$supporting_id = '';
$raw_material_id= '';


}else{

$DATA  = SETUP::SETUP_SUPPORTING_RECIPE($_GET['related_id']);
$related_id = $DATA['id'];
$supporting_id = $DATA['supporting_id'];
$raw_material_id = $DATA['raw_material_id'];
$quantity = $DATA['quantity'];




}
?>
    <div class="row">
                        <div class="col-md-12">
                            
                            <form class="form-horizontal">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Recipe For Supporting Goods  </strong> <b id="mess_box"></b>  </h3>
                                    <ul class="panel-controls" >
                                    
                                    </ul>
                                </div>
    <input type="hidden" name="related_id" id="related_id" value="<?php print $related_id;?>">
                                <div class="panel-body">                                                                        
                                    
                                    <div class="row">
                                        
                                        <div class="col-md-12">
                                            
                                        <div class="form-group">
                                                <label class="col-md-3 control-label">Supporting Material Name</label>
                                                <div class="col-md-9">                                                                                            
                                                <select id="supporting_id" name="supporting_id"    class="form-control select" data-live-search="true">
        <option value="">Select One</option>
<?php 
        $qry = $conn_me->prepare("SELECT * FROM `setup_raw_material`  where `supporting_product` = 'Yes' ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {  ?>

        <option <?php if( $supporting_id == $fetch['id'] ){ ?> selected = 'selected' <?php }else{ } ?>
         value="<?php print $fetch['id'];?>"><?php print $fetch['material_name'];?>
        </option>
 <?php } ?>

</select>
                                                    <span   class="help-block1"></span>
                                                </div>
                                            </div>
                                            


                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Estimated Supporting Unit</label>
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
                                                <label class="col-md-3 control-label">Estimated Supporting Qty </label>
                                                <div class="col-md-9 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon">

                                                

                                                </span>
                                                <input type="number" class="form-control" value="1"  onkeyup="calculate_actual_qty();" id="ess_fg_quantity" name="ess_fg_quantity" >
                                            </div>            
                                                    <span class="help-block"></span>
                                                </div>
                                            </div> 

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Raw Material Name</label>
                                                <div class="col-md-9">                                                                                            
                                                <select id="raw_material_id" name="raw_material_id"  class="form-control select" data-live-search="true">
                                                <option value="">Select One</option><?php 
        $qry = $conn_me->prepare("SELECT * FROM `setup_raw_material`  ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {  ?>

        <option <?php if( $raw_material_id == $fetch['id'] ){ ?> selected = 'selected' <?php }else{ } ?>
         value="<?php print $fetch['id'];?>"><?php print $fetch['material_name'];?>
        </option>
 <?php } ?>

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
                                            
                                          
                                   
                                           
                                            
                                        </div>
                                        
                                        
                                    </div>

                                </div>
                              
                                <div class="panel-footer">
                                    <?php if($_GET['related_id'] == 'New'){ ?>
                                        <input type="button" id="add_cart_supporting_recipe" name="add_cart_supporting_recipe" class="btn btn-info pull-right" value="Add++"  >
                                        <?php  }else{?>
                                            <input style="margin-left:15px;" type="button" class="btn btn-warning pull-right" value="Update" id="add_cart_supporting_recipe" name="add_cart_supporting_recipe" >
                                            <a class="btn btn-info pull-right" href="Recipe/FG-Recipe-Setup/New" >Add New</a>
                                            <?php  }?>
                               
                                <input type="button" class="btn btn-danger pull-left" value="Final Data" onclick="" id="final_supporting_recipe" name="final_supporting_recipe" >
                                </div>

                            </div>
                            </form>
                            
                        </div>
                    </div>     

<div class="col-md-12" id="refresh_cart">
<?php include('supporting-recipe-cart.php');?>
</div>