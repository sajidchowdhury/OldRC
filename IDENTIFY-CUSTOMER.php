<?php 
$xml_customer_list = simplexml_load_file("xml_customerList.xml");

include('function_query.php');

  
    $DATA  = SETUP::ACCOUNT_TRANSECTION_PENDING($_GET['related_id']);
    $posting_transection_id = $DATA['id'];
    $amount = $DATA['in_amount'];
    $transection_to_id = $DATA['transection_to_id'];
    $note = $DATA['note'];
    $transection_by = $DATA['transection_by'];
    $transection_by_id = $DATA['transection_by_id'];
    $check_number = $DATA['check_number'];
    $check_date =$DATA['check_date'];
    $transection_date = $DATA['transection_date'];
    $transection_id = $DATA['transection_id'];



?>


<div class="row">

          <input type="hidden" id="posting_transection_id" name="posting_transection_id" value="<?php print $posting_transection_id;?>">
          <input type="hidden" id="transection_id" name="transection_id" value="<?php print $transection_id;?>">

          <div class="panel panel-default">
        


          <div class="col-md-12">
          <div class="table-responsive">

<table class="table table-hover table-condensed ">




<tr>
<th >Select Customer</th>
<td colspan="3">
<select id="identified_transection_to_id" name="identified_transection_to_id" required  class="form-control select" data-live-search="true">
<option value="">Select One</option>
<?php 
foreach ( $xml_customer_list->ROW as  $value ) { 
    ?>

<option  <?php  if($transection_to_id == $value['id']){?> selected="selected" <?php }else{ } ?>  value="<?php print $value['id'];?>"><?php print $value['mobile'] . '-' . $value['shop_name'] . '-' . $value['upazila_name']  . '-' . $value['district_name'] ;?>
</option>
<?php } ?>

</select>
</td>
<td >
</td>


</tr>

    <tr>
             <th>Transaction By</th>
            <td colspan="4">
            <input type="text" readonly class="form-control text-danger" id="transection_by" value="<?php print $transection_by ;?>" >
           
            </td> 

    </tr>

           


            <?php 
            
        if($transection_by == 'Bank'){

$content   = '<tr id="transection_by_data">
<th >Bank Details</th>
<td colspan="4">
<select class="form-control select text-danger" id="transection_by_id" disabled name = "transection_by_id"  data-live-search="true">
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
<td><input type="text" class="form-control" readonly id="check_number text-danger" value="'.$check_date.'" ></td>


<th >Check Date</th>
<td><input type="date" class="form-control text-danger" readonly id="check_date" value="'.$check_date.'" ></td> 

</tr>';

}else if ($transection_by == 'Mobile-Banking'){

$content   = '<tr id="transection_by_data">
<th >Number</th>
<td colspan="4">
<select class="form-control select" disabled id="transection_by_id" name = "transection_by_id"  data-live-search="true">
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
    <input type="hidden" class="form-control" id="transection_by_id" value="" >';

}else{
$content   = '<tr id="transection_by_data"></tr>
<tr id="transection_by_data2"></tr>';
}

print $content;
?>




    <tr>
    <th>A M O U N T</th>
    <td  colspan="4">
    <input type="number" readonly class="form-control text-danger" id="amount" name = "amount" value="<?php print $amount ;?>">
    </td> 

    </tr>
    
        
    <tr>
    <th>Narration</th>
    <td colspan="4"><input type="text" id="note" value="<?php print $note;?>" class="form-control"></td>
    </tr>

    
    <tr>
    <th colspan="5">
    <input type="button"   onclick="convert_to_identified_customer()" class="btn btn-warning pull-right" value="Save">
    </th>
    </tr>



</table>
</div>
</div>
</div>
</div>

<script>

    
    </script>
