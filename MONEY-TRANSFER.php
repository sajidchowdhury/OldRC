<?php 

$info_branch = SETUP::setup_brunch($_SESSION['USER_BRUNCH']);



?>
<ul class="breadcrumb">
    <li><a href="#">Accounts </a></li>           
  <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>

<div class="row">

          <div class="panel panel-default">

          <div class="col-md-12">

<table class="table table-hover table-condensed ">

<tr>
        <th>Section</th>
        <td colspan="4">
        <select class="form-control select"  name="report_type" id="report_type" onchange="Change_Type('report_type','level-name','level-data');" >
        <option value="">Select One</option>
        <option value="Same_Branch">Same Branch</option>
        <option value="Branch-Wise">Another Branch</option>
    
</select>
        </td>
    </tr>

<tr>
<th>Current Branch</th>
<td><input type="text" readonly value="<?php print $info_branch['brunch'];?>" class="form-control text-danger" ></td>
</tr>


<tr>
<th><b id="level-name"></b></th>
<td><b id="level-data"></b><input type="hidden" id="brunch_id" value="<?php print $_SESSION['USER_BRUNCH'];?>"  ></td>    
</tr>


<tr>
        <th>Transfer Type</th>
        <td colspan="4">
        <select name="tr_type" id="tr_type" class="form-control select" onchange="TransferType(this.value)"  >
        <option value="">Select One</option>

        <option value="Cash_To_Cash">Cash To Cash</option>
        <option value="Cash_To_Mobile">Cash To Mobile</option>
        <option value="Cash_To_Bank">Cash To Bank</option>
        <option value="Bank_To_Mobile">Bank To Mobile</option>
        <option value="Bank_To_Cash">Bank To Cash</option>
        <option value="Mobile_To_Cash">Mobile To Cash</option>
        <option value="Mobile_To_Bank">Mobile To Bank</option>


</select>
        </td>
    </tr>




    <tr>
        <td id="level_data" colspan="2">

        <input type="hidden" value="" id="transection_by_from">
        <input type="hidden" value="" id="transection_by_to">
          <input type="hidden" value="0" id="tr_from">
        <input type="hidden" value="0" id="tr_to">
        </td>
    </tr>



           



    <tr>
    <th>A M O U N T</th>
    <td  colspan="4">
    <input type="number" class="form-control" id="amount" name = "amount" value="">
    </td> 

    </tr>
    
        
    <tr>
    <th>Narration</th>
    <td colspan="4"><input type="text" id="note" value="" class="form-control"></td>
    </tr>

    <tr>
    <th>Transaction Date </th>
    <td colspan="4">
    <input type="text"  class="date form-control" value="<?php print date('d-m-Y');?>" id="transection_date" name="transection_date" >        
    </td>
    </tr>


    
    <tr>
    <th colspan="5">
    <input type="button"   onclick="transfer_transaction()" class="btn btn-warning pull-right" value="Transfer Now">
    </th>
    </tr>



</table>
</div>
</div>
</div>

<div class="row">
<div class="col-sm-1">Branch</div>
<div class="col-sm-3">
<select class="form-control select" id="branch_id" name = "branch_id"  style=" overflow: visible !important;">
<option value="All">All</option>
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch`  where status = 'Active'  ");
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
<input type="button" onclick="transectionSummery('32','MONEY-TRANSFER');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
</div>
<div class="col-sm-4"></div>

</div>

<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">
 </div>
   
</div>