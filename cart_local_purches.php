<?php

include_once('function_query.php');


?>





    <div id="table-scroll">
        <div class="table-responsive">
            <table id="mytable" class="table salestable2" style="white-space:nowrap;">
                <thead>
                    <tr>

                        <th> Sl</th>
                        <th>Supplier Name</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Purchase Rate</th>
                        <th>Quantity</th>
                        <th>Total Amount</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>


<?php 
                        $sl = 1;
$cart_sub_total = 0;
                    
    if(!empty($_GET['purches_type'])){
        
        $query = $conn_me->prepare("SELECT *  FROM `{$_GET['purches_type']}`  where `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ORDER BY `id` DESC");
        $query->execute();
        $count = $query->rowCount();
        $DATABASE = $_GET['purches_type'];
    }else{
        $aa = '1';
        $query = $conn_me->prepare("SELECT *  FROM `raw_local_purches`  where `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ORDER BY `id` DESC");
        $query->execute();
        $count = $query->rowCount();
        $DATABASE = 'raw_local_purches';
    }
   
if($count > 0 ){
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($fetch_list as $fetch) { 

        $ID = "_ID_$fetch[id]_ID_$DATABASE";

        $total = number_format((float)( $fetch['purches_price']*$fetch['quantity']), 2, '.', '');



        if(!empty($fetch['supplier_id'])){
            $info_su = SETUP::SETUP_SUPPLIER($fetch['supplier_id']);
            $supplier_name = $info_su['supplier_name'];
        }else{
            $supplier_name = '<b style="color:red;">Please edit & add supplier</b>';
        }



        if($DATABASE == 'fg_local_purches'){

            $CART_DATA = SETUP::SETUP_PRODUCT($fetch['product_id']);
            $product_name = "$CART_DATA[product_code] $CART_DATA[product_name]";
         
        }else{
            $CART_DATA = SETUP::SETUP_RAW_MATERIAL($fetch['product_id']);
            $product_name = "$CART_DATA[material_code] $CART_DATA[product_name]";


        }

        ?>



    <tr style="line-height: 0.5">
            <td><?php print $sl++ ;?></td>
            <td><?php print $supplier_name ;?></td>
            <td><?php print "$product_name" ;?></td>

            <td><?php print $CART_DATA['category'] ;?></td>
            <td><?php print $fetch['purches_price'] ;?></td>
            <td><?php print $fetch['quantity'] ;?></td>
            <td><?php print $total ;?></td>


            <td>
            <a onClick="edit_purches(
                '<?php print $fetch['id'];?>',
                '<?php print $DATABASE;?>',
                '<?php print $fetch['supplier_id'];?>',
                '<?php print $fetch['note'];?>',
                '<?php print $fetch['product_id'];?>',
                '<?php print $fetch['supplier_bill_no'];?>',
                '<?php print $fetch['supplier_bill_date'];?>',
                '<?php print $fetch['invoice_no'];?>',
                '<?php print $fetch['date'];?>',
                '<?php print $fetch['purches_price'];?>',
                '<?php print $fetch['quantity'];?>'
                );"><button class="btn btn-default btn-rounded btn-sm"><span class="fa fa-pencil"></span></button></a>
                <button class="btn btn-danger btn-rounded btn-sm" onClick="delete_cart_row('cart_local_purches','<?php print $DATABASE;?>','<?php print $fetch['id'];?>','No');"><span class="fa fa-times"></span></button>
            </td>

             </tr>

   
             <?php 
                        $cart_sub_total += $total;
 
            }

}else{ ?>
<tr><td colspan="5" style="color:red">Empty Cart</td></tr>
<?php } ?>
                
                 

          
                   </tbody></table>
        </div>

    </div>    
<input type="hidden" name="count_cart" id="count_cart" value="<?php print $count;?>">
<input type="hidden" name="cart_sub_total" id="cart_sub_total" value="<?php print $cart_sub_total;?>">

<div id="transection_modal"></div>