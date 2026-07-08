<?php 
include('function_query.php');


    $DATA  = SETUP::ACCOUNT_TRANSECTION($_GET['related_id']);

    $details_of_transection_to = $DATA['details_of_transection_to'];

    $related_id = $DATA['id'];
    $transection_head_id = $DATA['transection_head_id'];
    $amount = $DATA['in_amount'];
    $transection_to_id = $DATA['transection_to_id'];
    $note = $DATA['note'];
    $code = $DATA['code'];
    $transection_date = $DATA['transection_date'];

    
    $transection_by = $DATA['transection_by'];
    $transection_by_id = $DATA['transection_by_id'];
    $check_number = $DATA['check_number'];
    $check_date =$DATA['check_date'];
    $due =$DATA['due'];


?>


<div class="row">


          <div class="panel panel-default">
          <input type="hidden" id="related_id" name="related_id" value="<?php print $related_id;?>">
          <input type="hidden" id="transection_head_id" name="transection_head_id" value="44">
          <input type="hidden" id="transection_to" name="transection_to" value="Supplier">
          <input type="hidden" id="data_inserted_from" name="data_inserted_from" value="<?php print $_GET['section'];?>">
          <input type="hidden" id="get_transection_by_id" name="get_transection_by_id" value="<?php print $transection_by_id;?>">
          <input type="hidden" id="get_check_number" name="get_check_number" value="<?php print $check_number;?>">
          <input type="hidden" id="get_check_date" name="get_check_date" value="<?php print $check_date;?>">
          <input type="hidden" id="code" name="code" value="<?php print $code;?>">
          <input type="hidden" id="transection_to_id" name="transection_to_id" value="<?php print $transection_to_id;?>">
          <input type="hidden" id="ledger_id" name="ledger_id" value="2">
          <input type="hidden" id="transection_date" name="transection_date" value="<?php print $transection_date;?>">



          <div class="col-md-12">

<table class="table table-hover table-condensed ">

<tr>
        <th colspan="5" class="text-danger" style="text-align:center;"><?php print  $details_of_transection_to ;?></th>
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

           


    <tr id="transection_by_data"><input type="hidden" class="form-control" id="transection_by_id" value="0" ></tr>
<tr id="transection_by_data2"></tr>
     

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
    <input type="button"   onclick="expense_transection()" class="btn btn-warning pull-right" value="Pay Now">
    </th>
    </tr>



</table>
</div>
</div>
</div>

<script>

    
    </script>