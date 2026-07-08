<?php 
$xml_customer_list = simplexml_load_file("xml_customerList.xml");


if($_GET['related_id'] == 'New'){

    $related_id = 'new_id';
    $transection_head_id ='66';
    $note = '';
    $transection_by = '';
    $transection_by_id ='';
    $transection_to_id = '';
    $check_number = '';
    $check_date ='';
    $amount  = '0.00';
    $code = '';
    $due = 0.00;
}else{
    $DATA  = SETUP::ACCOUNT_TRANSECTION($_GET['related_id']);


    $related_id = $DATA['id'];
    $transection_head_id = $DATA['transection_head_id'];
    $amount = $DATA['in_amount'];
    $transection_to_id = $DATA['transection_to_id'];
    $note = $DATA['note'];
    $code = $DATA['code'];
    
    
    $transection_by = $DATA['transection_by'];
    $transection_by_id = $DATA['transection_by_id'];
    $check_number = $DATA['check_number'];
    $check_date =$DATA['check_date'];
    $due =$DATA['due'];



}




?>

<ul class="breadcrumb">
    <li><a href="#">Accounts </a></li>           
  <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
             

<div class="row">


          <div class="panel panel-default">
          <input type="hidden" id="related_id" name="related_id" value="<?php print $related_id;?>">
          <input type="hidden" id="transection_to" name="transection_to" value="Customer">
          <input type="hidden" id="data_inserted_from" name="data_inserted_from" value="<?php print $_GET['section'];?>">
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
<th >Select Customer</th>
<td colspan="3">
<select id="transection_to_id" name="transection_to_id" required onchange="findTransectionDue(this.value,'Customer');"  class="form-control select" data-live-search="true">
<option value="">Select One</option>
<?php 
foreach ( $xml_customer_list->ROW as  $value ) { 
    ?>

<option  <?php  if($transection_to_id == $value['id']){?> selected="selected" <?php }else{ } ?>  value="<?php print $value['id'];?>"><?php print $value['mobile'] . '-' . $value['shop_name'];?>
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
$qry = DB::conn()->prepare("SELECT * FROM `setup_bank` where `status`  = 'Active'   ORDER BY `id` ASC");
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
$qry = DB::conn()->prepare("SELECT * FROM `setup_mobile_banking` where `status`  = 'Active'   ORDER BY `id` ASC");
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
$content   = '';

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

    
    <tr>
    <th colspan="5">
    <input type="button"   onclick="income_transection()" class="btn btn-warning pull-right" value="Receive Now">
    </th>
    </tr>



</table>
</div>
</div>
</div>
</div>

<script>

    
    </script>