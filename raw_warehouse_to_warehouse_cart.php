<?php

include_once('function_query.php');


?>

<style>
    #table-scroll {
        height: 250px;
        overflow: auto;
        margin-top: 20px;
    }

    .panel-body.panel-body-table td,
    .panel-body.panel-body-table th {
        padding: 1px 8px;
    }
</style>






    <div id="table-scroll">
        <div class="table-responsive">
            <table class="table table-bordered table-actions" style="white-space:nowrap;">
                <thead>
                    <tr>

                        <th> Sl</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Transfer</th>
                        <th>Edit</th>

                    </tr>
                </thead>
                <tbody>


<?php 
                        $sl = 1;

                    

    $query = $conn_me->prepare("SELECT *  FROM `raw_warehouse_to_warehouse_transfer`  where `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");

                      
                        $query->execute();
                        $count = $query->rowCount();

                        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

                
                    foreach ($fetch_list as $fetch) { 
                        
                        $CART_DATA = SETUP::RAW_warehouse_to_warehouse_transfer($fetch['id']);
                    
                        ?>

                       

                    <tr>
                            <td><?php print $sl++ ;?></td>
                            <td><?php print $CART_DATA['product_name'] ;?></td>
                            <td><?php print $CART_DATA['quantity'] ;?></td>
                            <td><?php print $CART_DATA['from_warehouse_name'] ;?> <i class="fa fa-arrow-right"></i>

 <?php print $CART_DATA['to_warehouse_name'] ;?></td>

                            <td>
                            <button class="btn btn-default btn-rounded btn-sm"><a href="Inventory/Finished-Goods-Warehouse-To-Warehouse/<?php print $fetch['id'];?>"><span class="fa fa-pencil"></span></a></button>
                            <button class="btn btn-danger btn-rounded btn-sm" onClick="delete_cart_row('warehouse_to_warehouse_cart','fg_warehouse_to_warehouse_transfer','<?php print $fetch['id'];?>','No');"><span class="fa fa-times"></span></button>
                            </td>

                             </tr>

                   
                             <?php  }?>

          
                   </tbody></table>
        </div>

    </div>
    <input type="hidden" name="count_cart" id="count_cart" value="<?php print $count;?>">