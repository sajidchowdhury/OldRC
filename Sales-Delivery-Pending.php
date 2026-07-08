<?php 
?>

<style>

.modal-ku {
  width:100%;
  height:100%;
  margin: auto;
}



</style>


<div class="page-content-wrap">

<ul class="breadcrumb">
    <li><a>Slaes & Local Purchase</a></li>      
    <li><a>Slaes </a></li>           
     
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        
<div class="row" style="padding-bottom:10px">
                   </div>
                <!-- END PAGE TITLE -->  

                <div class="row">
                    <div class="col-md-12">
                    <div class="table-responsive">

                        <table class="table table-hover table-condensed table-striped table-bordered datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Invoice No</th>
                                                <th>Customer</th>
                                                 <th>Branch</th>
                                                <th>Copy</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $sl =  1;
                                        $count1 =0;
                                        $count_head_office = 0 ;
                                        $count_nowabpur_office = 0 ;
        $query = $conn_me->prepare("SELECT A.* ,B.brunch, date_format(A.`invoice_date`, '%d-%m-%Y') AS `invoice_date`  FROM `sales_invoice` A 
        JOIN setup_brunch B ON (A.brunch_id = B.id)
        WHERE  A.`status` = 'Done' AND  A.`confirm_by_sales_manager` = 'Done' AND A.`warehouse_dispatch` = 'Pending'  AND A.`dispatch_from_which_brunch` = '".$_SESSION['USER_BRUNCH']."' ORDER BY concat(A.`invoice_date`,' ',`code`) DESC");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) { 
            $count1 = $count1+1;
            $info_customer = SETUP::SETUP_CUSTOMER($fetch['customer_id']);
            ?>             
<input type="text" style="display:none" id="cp_<?php print $count1;?>" value="<?php print $fetch['code'];?>" >
                                            <tr>   
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['invoice_date'];?><br><?php print $fetch['invoice_no'];?></td>
                                                 <td><?php print "Name: $info_customer[shop_name] <br>Mobile: $info_customer[mobile]";?></td>

<td><?php print  $fetch['brunch'] ;?></td>

                                                <td>

                                                <a href="print.php?print=Godown Copy&&code=<?php print $fetch['code'];?>" target="_BLINK">Godown Copy<img src="img/icons/ppt.png" style="height:32px;"></a>

                                                <?php  if(!empty($fetch['dispatcher_id'])){   //  user can delete invoice before generating challan copy ?>


                        
                                                         <?php if($fetch['generate_challan'] == 'Done' ){  ?>
                                                            <a href="challan_copy.php?code=<?php print $fetch['code'];?>" target="_BLINK"> Challan Copy <img src="img/icons/godowncopy.png" style="height:32px;"></a>

                                                        <?php }else{  ?>
                                                            
                                                            <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','cp_<?php print $count1;?>','Challan Copy');">Generate Challan Copy</button>


                                                            <?php } ?>



                                                    <?php }else{  ?>

                                <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','cp_<?php print $count1;?>','Generate Godown Copy');">Generate Godown Copy</button>
                                <?php if(is_null($fetch['transection_id'])){ ?>
                                <input type="button" class="btn btn-danger" value="x" onclick="delete_total_invoice('<?php print $fetch['code'];?>');">
                                <?php }  ?>

                                                        <?php } ?>


                                                        <?php  if($fetch['printed'] == 'Yes'){ ?>
                                                        <i class="fa fa-print">Printed</i>

                                                        <?php } ?>

                                                </td>
                     
                                            </tr>
        <?php } ?>
                                        </tbody>
                                    </table>   
                        
                    </div>  </div>
                </div>                    
                
            </div>
