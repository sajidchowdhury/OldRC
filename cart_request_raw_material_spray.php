<?php
include('auth.php');
include_once('function_query.php');
$permission_management2 = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],'Final','final_demand','onclick="final_pending_spray_demand();"');


?>


<div class="panel panel-default tabs">                            
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="active"><a href="#tab-first" role="tab" data-toggle="tab">Item List</a></li>
                                        <li><a href="#tab-second" role="tab" data-toggle="tab">Material</a></li>

                                    </ul>
                                    <div class="panel-body tab-content">
                                        <div class="tab-pane active" id="tab-first">
                                        <div class="table-responsive">

                                        <table class="table table-hover table-condensed">
                                        <tr>
                                                <th>Sl</th>
                                                <th>Item</th>
                                                <th>Total QTY</th>
                                                <th>Action</th>

                                            </tr>


                                            <?php 
$total_qty = 0;
$data = '';
$aa1 = '1';
$query = $conn_me->prepare("SELECT *  FROM `raw_spray`  where `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ORDER BY `date`,`time` DESC");
$query->execute();
$fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) {
    
    $data .= $fetch['code'] . ',';

    $product_info = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);

    ?>
            <tr>
                <td><?php print  $aa1++;?></td>
                <td><?php print "$product_info[material_code] $product_info[product_name]" ;?></td>
                <td><?php print $fetch['batch_quantity'];?></td>
                <td>
                
                <button  onClick="delete_spray_recipe_wise_demand_and_item('<?php print $fetch['id'];?>');"><span class="fa fa-times"></span></button>
                </td>
            </tr>

<?php 
$total_qty += $fetch['batch_quantity'];
} ?>


                                            

                                        </table>
</div>
                                        <span class="fa-left">Total Item <b style="color:red;font-size:18px;"><?php print $total_qty;?></b> Unit</span> 

                                        </div>
                                        <div class="tab-pane" id="tab-second">
                                        <div class="table-responsive">

                                        <table class="table table-hover table-condensed">
                                        <tr>
                                                <th>Sl</th>
                                                <th>Item</th>
                                                <th>Total QTY</th>

                                            </tr>


                                            <?php 
 
$aa2 = '1';
$total_materials = 0;
$query = $conn_me->prepare("SELECT SUM(`demand_quantity`) AS `total_demand` ,`material_id` FROM `raw_spray_item`  where `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' GROUP BY `material_id` ORDER BY `date`,`time` DESC");
$query->execute();
$fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) {
    
    $product_info = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);

    ?>
                                            <tr>
                                               <td><?php print  $aa2++;?></td>
                                               <td><?php print "$product_info[material_code] $product_info[product_name]" ;?></td>
                                                <td><?php print $fetch['total_demand'];?></td>
                                                
                                            </tr>

<?php
$total_materials += $fetch['total_demand'];
} ?>

                                            

                                        </table>
</div>
                                        <span class="fa-left">Total Material <b style="color:red;font-size:18px;"><?php print $total_materials;?></b> Unit</span> 
                                        </div>                                        
                                        
                                    </div>
                                    <div class="panel-footer">        
    <input type="hidden" id="invoice_code"   value="<?php 
    $data = trim($data,',');
    $data = explode(",",$data);
    $data = array_unique($data);
    $count = count($data);
    for ($x = 0; $x < $count; $x++) {
        
            print $data[$x];
        }?>">   
                                <?php print  $permission_management2['save_update_buton'];?>
                                    </div>
                                </div>                                
                            


