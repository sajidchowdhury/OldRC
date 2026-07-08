<?php 



?>

<ul class="breadcrumb">
    <li><a href="#">Sales </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        
<input  type="hidden" value="" id="date_from"  >
<input  type="hidden" value="" id="date_to" >
<input  type="hidden" value="Today" id="report_type" >

<div class="row">
<div class="col-md-12 form-horizontal">


<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Activity</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(' :: Sales Invoice','MSalary')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel('Sales Invoice','MSalary')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
        <li> <a  class="list-group-item" onclick="call_it_a_day()"><img src='img/icons/json.png' width="24"/> Call it a day</a></li>
    </ul>
</div>                                    

</div>



                        
                          

</div>
</div>
<br>

<div class="row">
<div class="col-md-12 form-horizontal">

<div class="panel-body panel-body-table">
                             
 <div class="table-responsive">
     <table class="table table-bordered table-striped datatable" id="MSalary">
         <thead>
             <tr>
             <th >Sl</th>

                 <th >Invoice</th>
                 <th >Customer</th>
                 <th >Status</th>
                 <th >Payment Received</th>

             </tr>
         </thead>
         <tbody>
       <?php
         $sl =1;
         $count_invoice = 0;
         $qry = $conn_me->prepare("SELECT * , date_format(`invoice_date`, '%d-%m-%Y') AS `invoice_date`   FROM `sales_invoice` where `call_it_a_day` = 'NO' AND `brunch_id` = '".$_SESSION['USER_BRUNCH']."' ORDER BY concat(`date`,' ',`time`) DESC ");
         $qry->execute();
         $count_this = $qry->rowCount();
         $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
         if ($count_this > 0)
         {
             foreach($fetch_list AS $fetch) {
           
                $count_invoice = $count_invoice+1;
                $info_customer = SETUP::SETUP_CUSTOMER($fetch['customer_id']);
                 $info_status = FIND::SALES_INVOICE_STATUS($fetch['id']);

                 ?>
            <input type="number" style="display:none;" id="invoice_code_<?php print $count_invoice;?>" value="<?php print $fetch['code'];?>" >
            <input type="number"  style="display:none;" id="inid<?php print $count_invoice;?>" value="<?php print $fetch['id'];?>" >

                     <tr>
                        <td><?php print $sl++ ;?></td>
                 <td>Invoice No. <strong><?php print $fetch['invoice_no'];?></strong><br>Invoice Date. <strong><?php print$fetch['invoice_date'];?></strong></td>
                 <td>Name: <?php print $info_customer['shop_name'];?> <br>Mobile: <?php print $info_customer['mobile'] ;?></td>
                 <td>
                 <?php print $info_status['status'];?> 
                 
                 <?php if ( $fetch['confirm_by_sales_manager'] == 'Done' ){ ?>
                    
<a href="invoice_copy.php?code=<?php print $fetch['code'];?>" target="_BLINK"><span class="fa fa-file-text"></span></a>       
<button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('RECEIVE-SALES_INVOICE','invoice_code_<?php print $count_invoice;?>','Invoice Wise Payment');"><i class="fa fa-eye"></i></button>

                 <?php if (   $fetch['generate_challan'] == 'Pending' && $fetch['warehouse_dispatch'] == 'Done' ){ ?>

<button type="button" class="btn btn-warning btn-rounded btn-sm"" onclick="PushToSalesReport('inid<?php print $count_invoice;?>');"><i class="fa fa-share-square-o"></i></button>


                <?php } } ?>
      

 <?php if ( !empty($fetch['generate_challan']) ){ }else{?>
                    
    <a href="sales/Sales-Entry/<?php print $fetch['id'];?>" target="_BLINK" class="btn btn-info btn-rounded btn-sm"> <span class="fa fa-pencil"></span> </a>       



                <?php } ?>
               
            
                 </td>
                 <td>
                    <?php if(!empty($fetch['transection_id'])){?> <b style="color:green">YES</b> <?php }else{ ?> <b style="color:red">NO</b> <?php } ?>
                 </td>
             </tr>
            <?php  }                                     
         } ?>

         <input type="number" style="display:none;" id="count_total_invoice"  value="<?php print $count_this;?>">
           </tbody>
     </table>
 </div>
 
</div>

</div>
</div>


