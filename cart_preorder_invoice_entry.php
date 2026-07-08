<?php

include_once('function_query.php');


?>





    <div id="table-scroll">
        <div class="table-responsive">
            <h3>Pre-Order Invoice</h3>
        <table id="mytable" class="table salestable2" style="white-space:nowrap;">
                <thead>
                    <tr>

                        <th> Sl</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Total Amount</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>


<?php 
                        $sl = 1;

                    $cart_sub_total = 0;
                    $total_carton = 0;
                    $total_vat = 0;
        $query = $conn_me->prepare("SELECT *  FROM `pre_order_invoice_item`  where `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `brunch_id` = '".$_SESSION['USER_BRUNCH']."' AND `status` = 'Pending' ORDER BY `id` DESC");
        $query->execute();
        $count = $query->rowCount();

 
if($count > 0 ){
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);


    foreach ($fetch_list as $fetch) { 

        $CART_DATA = SETUP::SETUP_PRODUCT($fetch['product_id']);
        $total = number_format((float)( $fetch['recommended_price']*$fetch['quantity']), 2, '.', '');
    
        $carton =   number_format((float)($fetch['quantity']/$CART_DATA['pcs_in_cartoon']), 2, '.', '') ;
        $vat =  number_format((float)($total * ($CART_DATA['vat_percentage']/100)), 2, '.', '') ;
        

        ?>


        
    <tr style="line-height: 0.5">
            <td><?php print $sl++ ;?></td>
            <td><?php print "$CART_DATA[product_code] $CART_DATA[product_name]" ;?></td>
            <td><?php print $fetch['quantity'] ;?></td>
            <td><?php print $fetch['recommended_price'] ;?></td>
            <td><?php print  $total;?></td>


            <td>
            <button class=""><a onClick="edit_sale_cart(
                '<?php print $fetch['id'];?>',
                '<?php print $fetch['note'];?>',
                '<?php print $fetch['product_id'];?>',
                '<?php print $fetch['quantity'];?>',
                '<?php print $fetch['recommended_price'];?>',
                'Preorder'

                );"><span class="fa fa-pencil"></span></a></button>
                <button class="" onClick="delete_cart_row('cart_preorder_invoice_entry','pre_order_invoice_item','<?php print $fetch['id'];?>','No');"><span class="fa fa-times"></span></button>
            </td>

             </tr>

   
             <?php  
            
            $cart_sub_total += $total;
            $total_vat += $vat;
            $total_carton += $carton;
            }

}else{ ?>
<tr><td colspan="5" style="color:red">Empty Cart</td></tr>
<?php } ?>
                
                 

          
                   </tbody></table>
        </div>

    </div>    
<input type="hidden" name="count_cart" id="count_cart" value="<?php print $count;?>">
<input type="hidden" name="cart_sub_total" id="cart_sub_total" value="<?php print $cart_sub_total;?>">
<input type="hidden" name="total_carton" id="total_carton" value="<?php print $total_carton;?>">
<input type="hidden" name="total_vat" id="total_vat" value="<?php print $total_vat;?>">

<div id="transection_modal"></div>