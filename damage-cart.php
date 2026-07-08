<?php

include_once('function_query.php');


?>


    <div id="table-scroll">
        <div class="table-responsive">
            <table id="mytable" class="table table-bordered table-actions" style="white-space:nowrap;">
                <thead>
                    <tr>

                        <th> Sl</th>
                        <th>Product</th>
                        <th>Details</th>
                        <th>Edit</th>

                    </tr>
                </thead>
                <tbody>


<?php 
                        $sl = 1;

                    

    $query = $conn_me->prepare("SELECT *  FROM `fg_damage_store`  where `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ORDER BY `id` DESC");
    $query->execute();
    $count = $query->rowCount();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

                
                    foreach ($fetch_list as $fetch) { 
                        
                        $CART_DATA = SETUP::SETUP_PRODUCT($fetch['product_id']);
                        ?>

                       

                    <tr style="line-height: 0.5">
                            <td><?php print $sl++ ;?></td>
                            <td><?php print $CART_DATA['product_name'] ;?></td>
                            <td><?php print $fetch['quantity'] ;?></td>

                            <td>
                          
                                <input type="button" class="btn btn-danger btn-rounded btn-sm" onClick="delete_cart_row('damage-cart','fg_damage_store','<?php print $fetch['id'];?>','No');" value="x">
                            </td>

                             </tr>

                   
                             <?php  }?>

          
                   </tbody></table>
        </div>

    </div>
<input type="hidden" name="count_cart" id="count_cart" value="<?php print $count;?>">