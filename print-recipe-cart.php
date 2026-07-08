<?php

include_once('function_query.php');


?>








    <div id="table-scroll">
        <div class="table-responsive">
            <table id="mytable" class="table table-bordered table-actions" style="white-space:nowrap;">
                <thead>
                    <tr>

                        <th> Sl</th>
                        <th>Spray Material</th>
                        <th>Material</th>
                        <th>Qty</th>

                        <th>Edit</th>

                    </tr>
                </thead>
                <tbody>


<?php 
                        $sl = 1;

                    
if($_GET['related_id'] == 'New' && $_GET['product_id'] == 'New'){
    $query = $conn_me->prepare("SELECT *  FROM `receip_print`  where `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ORDER BY `id` DESC");

}else{
    $query = $conn_me->prepare("SELECT *  FROM `receip_print`  where `print_material_id` = '".$_GET['product_id']."' ORDER BY `id` DESC");

}




                      
                        $query->execute();
                        $count = $query->rowCount();

                        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
if($count > 0 ){

    foreach ($fetch_list as $fetch) { 
                        
        $CART_DATA = SETUP::SETUP_PRINT_RECIPE($fetch['id']);
        ?>

       

    <tr style="line-height: 0.5">
            <td><?php print $sl++ ;?></td>
            <td><?php print $CART_DATA['product_name'] ;?></td>
            
            <td><?php print $CART_DATA['raw_material_name'] ;?></td>
            <td><?php print $fetch['quantity'] ;?></td>


            <td>
            <button class="btn btn-default btn-rounded btn-sm"><a href="Recipe/Print-Recipe-Setup/<?php print $fetch['id'];?>/<?php print $fetch['print_material_id'];?>"><span class="fa fa-pencil"></span></a></button>
                <button class="btn btn-danger btn-rounded btn-sm" onClick="delete_cart_row('print-recipe-cart','receip_print','<?php print $fetch['id'];?>','No');"><span class="fa fa-times"></span></button>
            </td>

             </tr>

   
             <?php  }

}else{ ?>
<tr><td colspan="5" style="color:red">Empty Cart</td></tr>
<?php } ?>
                
                 

          
                   </tbody></table>
        </div>

    </div>
<input type="hidden" name="count_cart" id="count_cart" value="<?php print $count;?>">