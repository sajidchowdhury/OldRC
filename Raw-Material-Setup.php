<?php 
include('xml_raw_material_list.php');
$xml_raw_material_list = simplexml_load_file("xml_raw_material_list.xml");




if($_GET['related_id'] == 'New'){
    $permission_management = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],'Save Data','save_raw_material','');

    $product_code = SETUP::SETUP_CODE('setup_raw_material');

    $product_name = '';
    $category_id = '';
    $supporting_product = 'No';
    $spray_product = 'No';
    $print_product = 'No';
    $weight = '';
    $mold_product = 'No';
    $unit_id = '';
    $pcs_in_cartoon = '';
    $code = $product_code['code'];
    $related_id = 'new_id';
    $minimum_stock_qty = '0.00';
    
    }else{
        $permission_management = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],'Update Data','save_raw_material','');

    $DATA  = SETUP::SETUP_RAW_MATERIAL($_GET['related_id']);
    
    $product_name = $DATA['product_name'];
    $related_id =  $DATA['id'];
    $category_id = $DATA['category_id'];
    $unit_id = $DATA['unit_id'];
    $code = $DATA['code'];
    $pcs_in_cartoon = $DATA['pcs_in_cartoon'];
    $supporting_product = $DATA['supporting_product'];
    $mold_product = $DATA['mold_product'];
    $spray_product = $DATA['spray_product'];
    $print_product = $DATA['print_product'];
    $weight = $DATA['weight'];
    $minimum_stock_qty = $DATA['minimum_stock_qty'];

    
    }
        $content = '<div class="row animated bounceIn">
        <div class="col-md-12">
    
            <div class="panel panel-default">
                <div class="panel-body">
                <form id="myform">
                
                <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
              </div>
    
                <table class="table table-hover table-condensed table-striped table-bordered">
                    <tbody> 
                    <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > 
                    <tr>
                    <th>Product Code  </th>
                    <td><input type="text" READONLY  class="form-control" value="'.$code.'" name="product_code" id="product_code"></td>
                    </tr>

                    <tr>
                    <th>Product Name</th>
                    <td><input type="text" class="form-control" value="'.$product_name.'" name="product_name" id="product_name"></td>
                    </tr>

                    <tr>
                    <th>Category   </th>
                    <td>';

                    $content .= '<div class="form-group">

                    <div class="col-md-10 col-xs-12">
                            <select id="category_id" name="category_id"   class="form-control select" data-live-search="true">
                            <option value="">Select One</option>';
                                $qry = $conn_me->prepare("SELECT * FROM `setup_raw_material_category`  ORDER BY `id` ASC");
                                $qry->execute();
                                $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                                 foreach($fetch_list AS $fetch) { 
                                    $content .=  '<option ';
                                    if($category_id == $fetch['id']){ 
                                        $content .=' selected="selected"';
                                    }else { }
                                    $content .=' value="'.$fetch['id'].'">'.$fetch['category'].'</option>'; 
                                 } 
                                 $content .= '</select>
                
                        
                    </div>';
                    
                    $content .='<label class="switch">
                    <input type="checkbox"  id="target3" onchange=HIDE_AND_SHOW(\'target_div_3\',\'target3\',\'category_id\'); name="target3" value="1"  />
                    <span></span>
                    Add</label>';
                    
                    
                    $content .= '</div>';
                    
                    $content .= '<div id="target_div_3" style="display:none;padding-bottom:15px;">
                    <div class="form-group" >
                    <label class="col-md-2 col-xs-12 control-label">Category Name</label>
                    <div class="col-md-8 col-xs-12">
                       <input type="text" name="new_category_name" id="new_category_name"  class="form-control" value=""></div>
                    </div>
                    </div>';
                    $content .= '</td>
                    </tr>
                    <tr>
                    <th>Unit  </th>
                    <td><div class="form-group">

                    <div class="col-md-10 col-xs-12">
                            <select id="unit_id" name="unit_id"   class="form-control select" data-live-search="true">
                            <option value="">Select One</option>';
                                $qry = $conn_me->prepare("SELECT * FROM `setup_unit`  ORDER BY `id` ASC");
                                $qry->execute();
                                $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                                 foreach($fetch_list AS $fetch) { 
                                    $content .=  '<option ';
                                    if($unit_id == $fetch['id']){ 
                                        $content .=' selected="selected" ';
                                    }else { }
                                    $content .=  ' value="'.$fetch['id'].'">'.$fetch['unit'].'</option>'; 
                                 } 
                                 $content .= '</select>
                
                        
                    </div></td>


                    </tr>


                    <tr>
                    <th>Pcs Per Carton </th>
                    <td><input type="text" class="form-control" value="'.$pcs_in_cartoon.'" name="pcs_in_cartoon" id="pcs_in_cartoon"></td>


                    </tr>

                    <tr>
                    <th>Weight </th>
                    <td><input type="text" class="form-control" value="'.$weight.'" name="weight" id="weight"></td>


                    </tr>


                    <tr>
                    <th>SUpporting Product? </th>
                    <td>
                    <select id="supporting_product" name="supporting_product"  class="form-control select" >';
                    
                    $content .= '<option '; if($supporting_product == 'No'){ $content .= ' selected = selected'; }else{ $content .= ' '; } $content .= '  value="No">No</option>';
                    $content .= '<option '; if($supporting_product == 'Yes'){ $content .= ' selected = selected'; }else{ $content .= ' '; } $content .= '  value="Yes">Yes</option>

                    </select>
                    </td>


                    </tr>

                    
                    <tr>
                    <th>Mold Product? </th>
                    <td>
                    <select id="mold_product" name="mold_product"  class="form-control select" >';
                    
                    $content .= '<option '; if($mold_product == 'No'){ $content .= ' selected = selected'; }else{ $content .= ' '; } $content .= '  value="No">No</option>';
                    $content .= '<option '; if($mold_product == 'Yes'){ $content .= ' selected = selected'; }else{ $content .= ' '; } $content .= '  value="Yes">Yes</option>

                    </select>
                    </td>


                    </tr>


                    </tr>
                    <tr>
                    <th>Spray Product? </th>
                    <td>
                    <select id="spray_product" name="spray_product"  class="form-control select" >';
                    
                    $content .= '<option '; if($spray_product == 'No'){ $content .= ' selected = selected'; }else{ $content .= ' '; } $content .= '  value="No">No</option>';
                    $content .= '<option '; if($spray_product == 'Yes'){ $content .= ' selected = selected'; }else{ $content .= ' '; } $content .= '  value="Yes">Yes</option>

                    </select>
                    </td>


                    </tr>
                    </tr>
                    <tr>
                    <th>Print Product? </th>
                    <td>
                    <select id="print_product" name="print_product"  class="form-control select" >';
                    
                    $content .= '<option '; if($print_product == 'No'){ $content .= ' selected = selected'; }else{ $content .= ' '; } $content .= '  value="No">No</option>';
                    $content .= '<option '; if($print_product == 'Yes'){ $content .= ' selected = selected'; }else{ $content .= ' '; } $content .= '  value="Yes">Yes</option>

                    </select>
                    </td>


                    </tr>


                    <tr>
                    <th>Minimum Stock </th>
                    <td><input type="text" class="form-control" value="'.$minimum_stock_qty.'" name="minimum_stock_qty" id="minimum_stock_qty"></td>


                    </tr>

                   

                        <tr>
                            
                            <td style="text-align:center;" colspan=2>';
                            $content .= $permission_management['save_update_buton'];   

                        $content .'</tr>
                  </tbody> 
              </table>
            </form>  
                    
            </div>
    
        </div>
    </div>';
    
    

print $content; ?>