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

                    


if($_GET['related_id'] == 'New' ){
   $query = $conn_me->prepare("SELECT A.*,B.product_name  FROM `demand_item` A  JOIN setup_product B ON (A.product_Id = B.id) where A.`poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND A.`status` = 'Pending'  ORDER BY `id` DESC");
}else{

 $query = $conn_me->prepare("SELECT A.*,B.product_name  FROM `demand_item` A  JOIN setup_product B ON (A.product_Id = B.id) where A.`poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."'  AND demand_id = '".$_GET['related_id']."'  ORDER BY `id` DESC");

}


   
    $query->execute();
    $count = $query->rowCount();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

                
                    foreach ($fetch_list as $fetch) { 
                        
                        $demand_id =  !empty($fetch['demand_id']) ? $fetch['demand_id'] : 'New';

                        ?>

                       

                    <tr style="line-height: 0.5">
                            <td><?php print $sl++ ;?></td>
                            <td><?php print $fetch['product_name'] ;?></td>
                            <td><?php print $fetch['quantity'] ;?></td>

                            <td>
                                <?php if( $fetch['converat_to_invoice'] == 'Pending'){ ?>




                                <button class="btn btn-danger btn-rounded btn-sm" onClick="delete_cart_row('demand-cart','demand_item','<?php print $fetch['id'];?>','No');"><span class="fa fa-times"></span></button>

                                <?php }else{ ?>

                                    <button class="btn btn-success btn-rounded btn-sm" ><span class="fa fa-check"></span></button>

                                  
                                <?php } ?>
                            
                            </td>

                             </tr>

                   
                             <?php  }?>

          
                   </tbody></table>
        </div>

    </div>
<input type="hidden" name="count_cart" id="count_cart" value="<?php print $count;?>">