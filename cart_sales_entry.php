<?php

include_once('function_query.php');


?>



  <div class="row">
                        <div class="col-md-12">
                            
                            <form class="form-horizontal">
                                                            
                                <div class="panel panel-default tabs">                            
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li id="CurrentCart" class="active"><a href="#tab-first" onclick="getDraftCode('');" role="tab" data-toggle="tab">Current Cart</a></li>


                                <?php 

                                if($_GET['related_id'] == 'New'){

$query = $conn_me->prepare("
                                SELECT draft_code, id 
                                FROM `sales_invoice_item` 
                                WHERE `poster` = :poster 
                                AND `brunch_id` = :brunch_id 
                                AND `status` = 'Pending' 
                                AND draft_code IS NOT NULL 
                                GROUP BY draft_code 
                                ORDER BY `date` DESC, STR_TO_DATE(`time`, '%h:%i:%s %p') DESC

                                ");
                                $query->execute([
                                ':poster' => $_SESSION['NEWERP_SESS_MEMBER_ID'],
                                ':brunch_id' => $_SESSION['USER_BRUNCH']
                                ]);
                                $mainfetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($mainfetch_list as $mainfetch) { 


                                     $a = explode( "SAJID",$mainfetch['draft_code']);
                                     $client_info = SETUP::findCustomerById($a[1]) ; 
                                     $shop_name = (!empty($client_info['shop_name'])) ? $client_info['shop_name'] : 'Draft' ;
                                    ?>

                                <li class="" id="CurrentCart<?php print $mainfetch['draft_code'];?>"> <a href="#tab-second<?php print $mainfetch['draft_code'];?>"  onclick="getDraftCode('<?php print $mainfetch['draft_code'];?>');" role="tab" data-toggle="tab"><?php print $shop_name;?></a></li>


                                <?php } 


                                } ?>
                                

                                    </ul>
                                    <div class="panel-body tab-content">
                                        




                             <?php    
   if($_GET['related_id'] == 'New'){


                             foreach ($mainfetch_list as $mainfetch) { 



                                   $lightColor = SETUP::getRandomLightColor();


                                ?>

                                        <div style="background-color: <?php print $lightColor;?>" class="tab-pane " id="tab-second<?php print $mainfetch['draft_code'];?>">

<div id="table-scroll">
        <div class="table-responsive">
            <div class="table-responsive">

        <table id="mytable" class="table table-bordered  salestable2" style="white-space:nowrap;">
                <thead>
                    <tr>

                        <th> Sl</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Unit</th>
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

                    if($_GET['related_id'] == 'New' ){
                        $query = $conn_me->prepare("SELECT *  FROM `sales_invoice_item`  where `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `brunch_id` = '".$_SESSION['USER_BRUNCH']."' AND `status` = 'Pending' AND `draft_code` = '".$mainfetch['draft_code']."'  ORDER BY `date` DESC, STR_TO_DATE(`time`, '%h:%i:%s %p') DESC ");
                        $query->execute();
                
                   
                    }else{
                    
                    $query = $conn_me->prepare("SELECT *  FROM `sales_invoice_item`  where `sales_invoice_id` = '".$_GET['related_id']."'  ORDER BY `date` DESC, STR_TO_DATE(`time`, '%h:%i:%s %p') DESC");
                    $query->execute();
                }
      
        $count = $query->rowCount();

 
if($count > 0 ){
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);


    foreach ($fetch_list as $fetch) { 

        $CART_DATA = SETUP::SETUP_PRODUCT($fetch['product_id']);
        $total = number_format((float)( $fetch['recommended_price']*$fetch['sales_quantity']), 2, '.', '');
    
        $carton =   number_format((float)($fetch['sales_quantity']/ ($CART_DATA['pcs_in_cartoon'] ?? 0)), 2, '.', '') ;
      //  $vat =  number_format((float)($total * $CART_DATA['vat']), 2, '.', '') ;
       //  $discounnt =  number_format((float)($total * $CART_DATA['discounnt']), 2, '.', '') ;


        ?>


        
    <tr style="line-height: 0.5">
            <td><?php print $sl++ ;?></td>
            <td><?php print "$CART_DATA[product_code] $CART_DATA[product_name]" ;?></td>
            <td><?php print $fetch['sales_quantity'] ;?></td>
            <td><?php print $CART_DATA['unit'] ;?></td>
            <td><?php print $fetch['recommended_price'] ;?></td>
            <td><?php print  $total;?></td>


            <td>
                

                <a class="btn btn-danger" onClick="delete_sales_row(

                    '<?php print $fetch['id'];?>',
                    '<?php print $fetch['sales_invoice_id'];?>'
                    
                    
                    );"><span class="fa fa-times"></span></a>
            </td>

             </tr>

   
             <?php  
            
            $cart_sub_total += $total;
            $total_carton += $carton;
            }

}else{ ?>
<tr><td colspan="5" style="color:red">Empty Cart</td></tr>
<?php } ?>
                
                 

          
                   </tbody></table></div>
        </div>

    </div>    
<input type="hidden" name="count_cart" id="count_cart" value="<?php print $count;?>">
<input type="hidden" name="cart_sub_total<?php print $mainfetch['draft_code'];?>" id="cart_sub_total<?php print $mainfetch['draft_code'];?>" value="<?php print $cart_sub_total;?>">
<input type="hidden" name="total_carton" id="total_carton" value="<?php print $total_carton;?>">

<div id="transection_modal"></div>


                                            
                                        </div>                                        
                                           <?php }  } ?>


                                           <div class="tab-pane active" id="tab-first">
                                         
                                         <div id="table-scroll">
        <div class="table-responsive">
            <div class="table-responsive">

        <table id="mytable" class="table salestable2" style="white-space:nowrap;">
                <thead>
                    <tr>

                        <th> Sl</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Unit</th>
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

                    if($_GET['related_id'] == 'New' ){
                        $query = $conn_me->prepare("SELECT *  FROM `sales_invoice_item`  where `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `brunch_id` = '".$_SESSION['USER_BRUNCH']."' AND `status` = 'Pending' AND `draft_code`  IS NULL ORDER BY `date` DESC, STR_TO_DATE(`time`, '%h:%i:%s %p') DESC");
                        $query->execute();
                
                   
                    }else{
                    
                    $query = $conn_me->prepare("SELECT *  FROM `sales_invoice_item`  where `sales_invoice_id` = '".$_GET['related_id']."'  ORDER BY `date` DESC, STR_TO_DATE(`time`, '%h:%i:%s %p') DESC");
                    $query->execute();
                }
      
        $count = $query->rowCount();

 
if($count > 0 ){
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);


    foreach ($fetch_list as $fetch) { 

        $CART_DATA = SETUP::SETUP_PRODUCT($fetch['product_id']);
        $total = number_format((float)( $fetch['recommended_price']*$fetch['sales_quantity']), 2, '.', '');
    
        $carton =   number_format((float)($fetch['sales_quantity']/ ($CART_DATA['pcs_in_cartoon'] ?? 0)), 2, '.', '') ;
      //  $vat =  number_format((float)($total * $CART_DATA['vat']), 2, '.', '') ;
       //  $discounnt =  number_format((float)($total * $CART_DATA['discounnt']), 2, '.', '') ;


        ?>


        
    <tr style="line-height: 0.5">
            <td><?php print $sl++ ;?></td>
            <td><?php print "$CART_DATA[product_code] $CART_DATA[product_name]" ;?></td>
            <td><?php print $fetch['sales_quantity'] ;?></td>
            <td><?php print $CART_DATA['unit'] ;?></td>
            <td><?php print $fetch['recommended_price'] ;?></td>
            <td><?php print  $total;?></td>


            <td>
                

                <a class="btn btn-danger" onClick="delete_sales_row(

                    '<?php print $fetch['id'];?>',
                    '<?php print $fetch['sales_invoice_id'];?>'
                    
                    
                    );"><span class="fa fa-times"></span></a>
            </td>

             </tr>

   
             <?php  
            
            $cart_sub_total += $total;
            $total_carton += $carton;
            }

}else{ ?>
<tr><td colspan="5" style="color:red">Empty Cart</td></tr>
<?php } ?>
                
                 

          
                   </tbody></table></div>
        </div>

    </div>    
<input type="hidden" name="count_cart" id="count_cart" value="<?php print $count;?>">
<input type="hidden" name="cart_sub_total" id="cart_sub_total" value="<?php print $cart_sub_total;?>">
<input type="hidden" name="total_carton" id="total_carton" value="<?php print $total_carton;?>">

<div id="transection_modal"></div>


                                        </div>


                                    </div>
                                    <div class="panel-footer">                                                                        
                                    </div>
                                </div>                                
                            
                            </form>
                            
                        </div>
                    </div>        

    