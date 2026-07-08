<?php
include('auth.php');
include_once('function_query.php');
$permission_management2 = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],'Final','final_demand','onclick="final_single_demand();"');


?>
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
$query = $conn_me->prepare("SELECT *  FROM `raw_spray`  where `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ORDER BY `id` DESC");
$query->execute();
$fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) {
    
    $data .= $fetch['code'] . ',';

    $product_info = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);

    ?>
            <tr>
                <td><?php print  $aa1++;?></td>
                <td><?php print $product_info['product_name'];?></td>
                <td><?php print $fetch['batch_quantity'];?></td>
                <td>
                <button ><a  href="Production/Send-For-Spray/<?php print $fetch['id'];?>"><span class="fa fa-pencil"></span></a></button>
                
                <button  onClick="delete_spray_recipe_wise_demand_and_item('<?php print $fetch['id'];?>');"><span class="fa fa-times"></span></button>
                </td>
            </tr>

<?php 
$total_qty += $fetch['batch_quantity'];
} ?>


                                            

                                        </table></div>