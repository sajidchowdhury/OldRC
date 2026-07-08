<?php 
$xml_customer_list = simplexml_load_file("xml_customerList.xml");


if($_GET['related_id'] == 'New'){

    $related_id = 'new_id';
    $transection_head_id ='66';
    $note = '';
    $transection_by = 'Cash';
    $transection_by_id ='';
    $transection_to_id = '';
    $check_number = '';
    $check_date ='';
    $amount  = '0.00';
    $code = '';
    $due = 0.00;
    $collect_by = '';
    $transection_date = date("d-m-Y");
}else{
    $DATA  = SETUP::ACCOUNT_TRANSECTION_PENDING($_GET['related_id']);


    $related_id = $DATA['id'];
    $transection_head_id = $DATA['transection_head_id'];
    $amount = $DATA['in_amount'];
    $transection_to_id = $DATA['transection_to_id'];
    $note = $DATA['note'];
    $code = $DATA['code'];
        $transection_date = date("d-m-Y", strtotime($DATA['transection_date']));

    $transection_by = $DATA['transection_by'];
    $transection_by_id = $DATA['transection_by_id'];
    $check_number = $DATA['check_number'];
    $check_date =$DATA['check_date'];
    $due = 0;

    $collect_by =$DATA['collect_by'];


}



$pending_data = FIND::PENDING_TRANSACTION_POST('BOTH','CUSTOMER-TRANSACTION');


?>

<ul class="breadcrumb">
    <li><a href="#">Accounts </a></li>           
  <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
             

<div class="row">


          <div class="panel panel-default">
          <input type="hidden" id="related_id" name="related_id" value="<?php print $related_id;?>">
          <input type="hidden" id="transection_to" name="transection_to" value="Customer">
          <input type="hidden" id="data_inserted_from" name="data_inserted_from" value="CUSTOMER-TRANSACTION">
          <input type="hidden" id="transaction_type" name="transaction_type" value="INCOME">
          <input type="hidden" id="get_transection_by_id" name="get_transection_by_id" value="<?php print $transection_by_id;?>">
          <input type="hidden" id="get_check_number" name="get_check_number" value="<?php print $check_number;?>">
          <input type="hidden" id="get_check_date" name="get_check_date" value="<?php print $check_date;?>">
          <input type="hidden" id="code" name="code" value="<?php print $code;?>">
          <input type="hidden" id="get_transection_to_id" name="get_transection_to_id" value="<?php print $transection_to_id;?>">
          <input type="hidden" id="transection_head_id" name="transection_head_id" value="<?php print $transection_head_id;?>">
          <input type="hidden" id="ledger_id" name="ledger_id" value="2">
          <input type="hidden" id="extra_field" name="extra_field" value="">



          <div class="col-md-12">
          <div class="table-responsive">

<table class="table table-hover table-condensed ">


<tr>
    <th>Transaction Type</th>
    <td colspan="4" > 
        <select class="form-control select" id="tr_type">
<option value="RECEIVE">RECEIVE</option>
<option value="PAYMENT">PAYMENT</option>

<?php if( $_SESSION['USER_TYPE'] == 'Admin'){ ?>
    <option value="INCREASE-DUE">increase Due</option>
    <option value="DISCOUNT">DISCOUNT</option>
<?php } ?>
    </select>   </td> 
    </tr>


<tr>
    <th>Collected By</th>
    <td colspan="4" > 
        <select name="collect_by" required class="form-control select" data-live-search="true" id="collect_by">
                                            <option value="">Select One</option>
                                            <?php 

                                            // SALES PERSON 
                                            $qry = $conn_me->prepare("SELECT A.`id`,B.`name` FROM `admin` A JOIN `setup_employee` B ON (`A`.`employee_id` = B.`id`)  
                                            WHERE A.hr_status = 'Active' ");
                                            $qry->execute();
                                            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($fetch_list AS $fetch) { 

                                            ?>
                                            <option <?php if($collect_by == $fetch['id'] ){ ?> selected="selected" <?php }else{ } ?> value="<?php print $fetch['id'] ?>" ><?php print $fetch['name'];?></option>
                                            <?php } ?>
                                            </select>  </td> 
    </tr>






<tr>
<th >Select Customer</th>
<td colspan="3">
<select id="transection_to_id" name="transection_to_id"  onchange="findTransectionDue(this.value,'Customer','Brunch_Wise');"  class="form-control select" data-live-search="true">
<option value="">Select One</option>
<?php 
foreach ( $xml_customer_list->ROW as  $value ) { 
    ?>

<option  <?php  if($transection_to_id == $value['id']){?> selected="selected" <?php }else{ } ?>  value="<?php print $value['id'];?>"><?php print $value['mobile'] . '-' . $value['shop_name'] . '-' . $value['address']  ;?>


</option>
<?php } ?>

</select>
</td>
<td >
</td>


</tr>

    <tr>
    <th>Previous Due</th>
    <td colspan="4" >
    <input type="text"  class="form-control text-danger" id="amount_due"  READONLY  name = "amount_due" value="<?php print $due;?> ">
    </td> 
    </tr>


    <tr>
             <th>Transaction By</th>
            <td colspan="4">
            <select class="form-control select" id="transection_by" onchange="transection_by_details(this.value)" name = "transection_by">
            <option value="">Select One</option>
                    <option <?php if($transection_by == 'Bank'){ ?> selected="selected" <?php }else{ } ?> value="Bank">Bank</option>
                    <option <?php if($transection_by == 'Mobile-Banking'){ ?> selected="selected" <?php }else{ } ?> value="Mobile-Banking">Mobile-Banking</option>
                    <option <?php if($transection_by == 'Cash'){ ?> selected="selected" <?php }else{ } ?> value="Cash">Cash</option>
                </select>
            </td> 

    </tr>

           


            <?php 
            
        if($transection_by == 'Bank'){

$content   = '<tr id="transection_by_data">
<th >Bank Details</th>
<td colspan="4">
<select class="form-control select" id="transection_by_id" name = "transection_by_id"  data-live-search="true">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `setup_bank` where `status`  = 'Active'   ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { 
    $bank_details = "$fetch[account_number] :: $fetch[brunch_name] :: $fetch[bank_name]";
    $content .= '<option ';

    if($transection_by_id == $fetch['id']){
        $content .= ' selected="selected"';
    }else{

    }
    $content .= 'value="'.$fetch['id'].'"><b style="font-color:blue">' .$bank_details. '</b></option>';

}
$content .='</select>

    </select>
</td>



</tr>';

$content   .= '<tr id="transection_by_data2">
<th>Check Number</th>
<td><input type="text" class="form-control" id="check_number" value="'.$check_date.'" ></td>


<th >Check Date</th>
<td><input type="date" class="form-control" id="check_date" value="'.$check_date.'" ></td> 

</tr>';

}else if ($transection_by == 'Mobile-Banking'){

$content   = '<tr id="transection_by_data">
<th >Number</th>
<td colspan="4">
<select class="form-control select" id="transection_by_id" name = "transection_by_id"  data-live-search="true">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `setup_mobile_banking` where `status`  = 'Active'   ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { 
    $bank_details = "$fetch[mobile_number] :: $fetch[mobile_bank_name] ";
    $content .= '<option ';
    
    if($transection_by_id == $fetch['id']){
        $content .= ' selected="selected"';
    }else{

    }
    $content .=' value="'.$fetch['id'].'"><b style="font-color:blue">' .$bank_details. '</b></option>';

}
$content .='</select>

    </select>
</td>

</tr>';


}else if($transection_by == 'Cash'){
    $content   = '
    <input type="hidden" class="form-control" id="check_number" value="" >
    <input type="hidden" class="form-control" id="check_date" value="" >
    ';

    $content   .= '<tr id="transection_by_data"><input type="hidden" class="form-control" id="transection_by_id" value="0" ></tr>
    <tr id="transection_by_data2"></tr>';

}else{
$content   = '<tr id="transection_by_data"></tr>
<tr id="transection_by_data2"></tr>';
}

print $content;
?>




    <tr>
    <th>A M O U N T</th>
    <td  colspan="4">
    <input type="number" class="form-control" id="amount" name = "amount" value="<?php print $amount ;?>">
    </td> 

    </tr>
    
        
    <tr>
    <th>Narration</th>
    <td colspan="4"><input type="text" id="note" value="<?php print $note;?>" class="form-control"></td>
    </tr>


    <tr <?php if ($_SESSION['USER_TYPE'] == 'Admin' ){ ?> <?php }else{ ?> style="display: none;" <?php } ?> >


    <th>Transaction Date </th>
    <td colspan="4">
    <input  type="text" value="<?php print $transection_date;?>" id="transection_date" name="transection_date"  class="date form-control" >    
    </td>
    </tr>



    
    <tr>
    <th colspan="5">
    <input type="button"   onclick="pending_income_transection()" class="btn btn-warning pull-right" value="Make Transaction">
    </th>
    </tr>



</table>
</div>
</div>
</div>
</div>

<div class="panel panel-default">
<div class="row">

<ul class="breadcrumb">
    <li><a class="text-danger"><b>Pending Data</b></a></li>           
</ul>
<div class="panel panel-default">
<div class="col-md-12">

<?php if($pending_data > 0 ){ ?>
    <input type="button" id="pending_data_cal_id" value="<?php print $pending_data;?>" class="btn btn-danger block" onclick="get_pending_post('BOTH','CUSTOMER-TRANSACTION','CUSTOMER-TRANSACTION');">
<?php }else{ ?>
    <input type="button" value="NO DATA PENDING TO POST" class="btn btn-info block">

<?php } ?>
</div>         
 </div>

 <div class="col-md-12">
<div id="load_pending_post"></div>
</div>         
 </div>



 <div class="row">
                    <div class="col-md-12">
                        
                        <form class="form-horizontal">
                                                        
                            <div class="panel panel-default tabs">                            
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="active"><a href="#tab-first" role="tab" data-toggle="tab"> Brunch Wise Report</a></li>

                                    <?php if ($_SESSION['USER_TYPE'] == 'Admin' )// due adjustment delete only for linkon vai 
{ ?>
                                    <li><a href="#tab-fourth" role="tab" data-toggle="tab">Delete Due  </a></li>
                                    <li><a href="#tab-second" role="tab" data-toggle="tab"> Delete Discount</a></li>
<?php } ?>
                                </ul>
                                <div class="panel-body tab-content">
                                    <div class="tab-pane active" id="tab-first">
                                       
                                    <ul class="breadcrumb">
    <li><a class="text-danger"><b>Brunch Wise Report</b></a></li>           
</ul>

<div class="row">
<div class="col-sm-1">Branch</div>
<div class="col-sm-3">
<select class="form-control select" id="branch_id" name = "branch_id"  style=" overflow: visible !important;">
<option value="All">All</option>
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where  status = 'Active' ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { ?>
<option  value="<?php print $fetch['id'];?>"><?php print $fetch['brunch'] ;?></option>
<?php } ?>
</select>
</div>
<div class="col-sm-1">From</div>
<div class="col-sm-3">
<input  type="text" value="<?php print date('d-m-Y');?>" id="date_from" class="date form-control" >
</div>
<div class="col-sm-1">To</div>
<div class="col-sm-3">
<input  type="text" value="<?php print date('d-m-Y');?>" id="date_to" class="date form-control" >
</div>
</div>
<div class="row" style="padding-top:15px">
<div class="col-sm-4"></div>
<div class="col-sm-4">
<input type="button" onclick="transectionSummery('2','CUSTOMER-TRANSACTION');" class="btn btn-success block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
</div>
<div class="col-sm-4"></div>

</div>

<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">
 </div>
   
</div>


                                    </div>
                                    <?php if ($_SESSION['USER_TYPE'] == 'Admin' )// due adjustment delete only for linkon vai 
{ ?>
                                    <div class="tab-pane" id="tab-fourth">
                                    <ul class="breadcrumb">
    <li><a class="text-danger"><b>Due Adjustment Delete Only For Specific User </b></a></li>           
</ul>

<div class="row">

<div class="col-sm-1">Branch</div>
<div class="col-sm-3">
<select class="form-control select" id="branch_id2" name = "branch_id2"  style=" overflow: visible !important;">
<option value="All">All</option>
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where  status = 'Active' ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { ?>
<option  value="<?php print $fetch['id'];?>"><?php print $fetch['brunch'] ;?></option>
<?php } ?>
</select>
</div>
<div class="col-sm-1">From</div>
<div class="col-sm-3">
<input  type="text" value="<?php print date('d-m-Y');?>" id="date_from2" class="date form-control" >
</div>
<div class="col-sm-1">To</div>
<div class="col-sm-3">
<input  type="text" value="<?php print date('d-m-Y');?>" id="date_to2" class="date form-control" >
</div>
</div>

<div class="row" style="padding-top:15px">
<div class="col-sm-4"></div>
<div class="col-sm-4">
<input type="button" onclick="find_due_adjustment();" class="btn btn-danger block" value="SEARCH DATA" id="search_data2" name="search_data2" ></td>
</div>
<div class="col-sm-4"></div>

</div>


<div class="row">
    <div class="col-12 form-horizontal" id="laod_report2"></div>
    </div>


                                    </div>



                                    <div class="tab-pane" id="tab-second">

                                    <ul class="breadcrumb">
    <li><a class="text-danger"><b>Discount Delete Only For Specific User </b></a></li>           
</ul>


<div class="row">

<div class="col-sm-1">Branch</div>
<div class="col-sm-3">
<select class="form-control select" id="branch_id3" name = "branch_id3"  style=" overflow: visible !important;">
<option value="All">All</option>
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where  status = 'Active' ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { ?>
<option  value="<?php print $fetch['id'];?>"><?php print $fetch['brunch'] ;?></option>
<?php } ?>
</select>
</div>
<div class="col-sm-1">From</div>
<div class="col-sm-3">
<input  type="text" value="<?php print date('d-m-Y');?>" id="date_from3" class="date form-control" >
</div>
<div class="col-sm-1">To</div>
<div class="col-sm-3">
<input  type="text" value="<?php print date('d-m-Y');?>" id="date_to3" class="date form-control" >
</div>
</div>

<div class="row" style="padding-top:15px">
<div class="col-sm-4"></div>
<div class="col-sm-4">
<input type="button" onclick="find_discount_adjustment();" class="btn btn-warning block" value="SEARCH DATA" id="search_data3" name="search_data3" ></td>
</div>
<div class="col-sm-4"></div>

</div>


<div class="row">
    <div class="col-12 form-horizontal" id="laod_report3"></div>
    </div>
               

        </div> 
                                    
                                  
                                </div>
                            <?php } ?>
                            </div>                                
                        
                        </form>
                        
                    </div>
                </div>                    
                
            </div>




