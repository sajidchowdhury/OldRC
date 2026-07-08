<?php 
$content ='';

?>
<div class="page-content-wrap">

<ul class="breadcrumb">
    <li><a href="#">Sales & Local Purchase </a></li>           
    <li><a href="#">Sales </a></li>                             
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        
<style>

.modal-ku {
  width:100%;
  margin: auto;
}
</style>
                <!-- END PAGE TITLE -->  

                <div class="row">
                    <div class="col-md-12">
                        
                        <form class="form-horizontal">
                                                        
                            <div class="panel panel-default tabs">                            
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="active"><a href="#tab-first" role="tab" data-toggle="tab"> Sales Invoice</a></li>
                                    <li><a href="#tab-fourth" role="tab" data-toggle="tab">Pending Demand </a></li>
                                    <li><a href="#tab-second" role="tab" data-toggle="tab"> Pre-Order Invoice</a></li>
                                    <li><a href="#tab-third" role="tab" data-toggle="tab"> Quotation</a></li>

                                </ul>
                                <div class="panel-body tab-content">
                                    <div class="tab-pane active" id="tab-first">
                                       
                                    <div class="table-responsive">
                                    <table class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Date</th>
                                                <th>Customer</th>
                                                <th>Invoice No</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $sl =  1;
                                        $count_invoice = 0;
        $query = $conn_me->prepare("SELECT * , date_format(`invoice_date`, '%d-%m-%Y') AS `invoice_date`  FROM `sales_invoice`  WHERE  `status` = 'Done' AND  `confirm_by_sales_manager` = 'Pending'  AND `brunch_id` = '".$_SESSION['USER_BRUNCH']."'   ORDER BY concat(`date`,' ',`time`) DESC ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) { 
            $count_invoice = $count_invoice+1;
            $info_customer = SETUP::SETUP_CUSTOMER($fetch['customer_id']);
            ?>
            <input type="number" style="display:none;" id="invoice_code_<?php print $count_invoice;?>" value="<?php print $fetch['code'];?>" >
                                            <tr>   
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['invoice_date'];?></td>
                                                <td><?php print "Name: $info_customer[shop_name] <br>Mobile: $info_customer[mobile]";?></td>
                                                <td><?php print $fetch['invoice_no'];?></td>
                                                <td> 
                                                 
                                            <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','invoice_code_<?php print $count_invoice;?>','Pending Sales Invoice');"><i class="fa fa-eye"></i></button>
                                            <input type="button" class="btn btn-danger" value="x" onclick="delete_total_invoice('<?php print $fetch['id'];?>');">

                                                
                                                 </td>
                                            </tr>
        <?php } ?>
                                        </tbody>
                                    </table>  

                                    </div> 
                                    </div>

                                    <div class="tab-pane" id="tab-fourth">

<div class="table-responsive">
                                    <table class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Date</th>
                                                <th>Demand From</th>
                                                <th>Invoice No</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $sl =  1;
                                        $count_invoice = 0;
        $query = $conn_me->prepare("SELECT A.* , DATE_FORMAT(A.`invoice_date`, '%d-%m-%Y') AS `invoice_date`,B.brunch
        FROM `demand` A 
        JOIN setup_brunch  B ON (A.demand_created_from = B.id)
        WHERE A.`status` = 'Done' 
          AND A.`convert_to_invoice` = 'Pending' 
          AND A.`demand_created_to` = '".$_SESSION['USER_BRUNCH']."' 
        ORDER BY CONCAT(A.`date`, ' ', A.`time`) DESC
");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) { 
            $count_invoice = $count_invoice+1;
            ?>
            <input type="number" style="display:none;" id="id_<?php print $count_invoice;?>" value="<?php print $fetch['id'];?>" >
                                            <tr>   
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['invoice_date'];?></td>
                                                <td><?php print "$fetch[brunch]";?></td>
                                               
                                                <td><?php print $fetch['invoice_no'];?></td>
                                                <td> 
                                                 
                                                <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','id_<?php print $count_invoice;?>','Pending Demand');"><i class="fa fa-eye"></i></button>
                                                  
                                                    <a class="btn btn-danger btn-rounded btn-sm" href="demand_record_print.php?copy=Print-Copy&demand_id=<?php print $fetch['id'];?>" target="_BLINK">Print </a>

                                                     <button type="button" class="btn btn-danger btn-rounded btn-sm" onclick="doneFinalAction('<?php print $fetch['id'];?>');"><i class="fa fa-check-circle"></i></button>      

                                                
                                                 </td>
                                            </tr>
        <?php } ?>
                                        </tbody>
                                    </table>  

                                    </div> 

                                    </div>



                                    <div class="tab-pane" id="tab-second">




                                    <div class="table-responsive">

                                    <table class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Invoice No</th>
                                                <th>Customer</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $sll =  1;
                                        $pre_invoice_count = 0;
        $query = $conn_me->prepare("SELECT *,date_format(`date`, '%d-%m-%Y') AS `date`  FROM `preorder_invoice`  WHERE  `status` = 'Done' AND `converat_to_invoice` = 'Pending'  AND `brunch_id` = '".$_SESSION['USER_BRUNCH']."'    ORDER BY concat(`date`,' ',`time`) DESC ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) { 
            $pre_invoice_count = $pre_invoice_count+1;
        
            $info_customer = SETUP::SETUP_CUSTOMER($fetch['customer_id']);

            ?>
                        <input type= "number" style="display:none" id="pre_invoice_code_<?php print $pre_invoice_count;?>" value="<?php print $fetch['code'];?>" >

                                            <tr>
                                                <td><?php print $sll++;?></td>
                                                <td><?php print $fetch['invoice_date'];?><br><?php print $fetch['invoice_no'];?></td>
                                                <td><?php print "Name: $info_customer[shop_name] <br>Mobile: $info_customer[mobile]";?></td>


                                                <td>     <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','pre_invoice_code_<?php print $pre_invoice_count;?>','Pending Pre Order Invoice');"><i class="fa fa-eye"></i></button>

                                                <input type="button" class="btn btn-danger btn-rounded btn-sm" value=" X " onclick="delete_preorder_invoice('<?php print $fetch['id'] ; ?>')">


    </td>
                                            </tr>
        <?php } ?>
                                        </tbody>
                                    </table>   
                                    </div>   
        </div> 
                                    
                                    <div class="tab-pane" id="tab-third">
                                    <div class="table-responsive">

                                    <table class="table datatable">
                                        <thead>
                                            <tr>
                                            <th>Sl</th>
                                                <th>Invoice No</th>
                                                <th>Customer</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $slll =  1;
                                        $quotation_count = 0;
        $query = $conn_me->prepare("SELECT *,date_format(`date`, '%d-%m-%Y') AS `date`  FROM `quotation_invoice`  WHERE   `status` = 'Done' AND `converat_to_invoice` = 'Pending'  AND `brunch_id` = '".$_SESSION['USER_BRUNCH']."'    ORDER BY concat(`date`,' ',`time`) DESC");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {

            $quotation_count = $quotation_count+1;
            $info_customer = SETUP::SETUP_CUSTOMER($fetch['customer_id']);

            
            ?>
                        <input type="number" style="display:none;" id="quotation_count_<?php print $quotation_count;?>" value="<?php print $fetch['code'];?>" >

                                            <tr>
                                                <td><?php print $slll++;?></td>
                                                <td><?php print $fetch['invoice_date'];?><br><?php print $fetch['invoice_no'];?></td>
                                                <td><?php print "Name: $info_customer[shop_name] <br>Mobile: $info_customer[mobile]";?></td>
                                                <td>
                                                    <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','quotation_count_<?php print $quotation_count;?>','Pending Quotation');"><i class="fa fa-eye"></i></button>
    </td>
                                            </tr>
        <?php } ?>
                                        </tbody>
                                    </table> 
        </div>
                                    </div>  

                                

                                    
                                </div>
                            
                            </div>                                
                        
                        </form>
                        
                    </div>
                </div>                    
                
            </div>
