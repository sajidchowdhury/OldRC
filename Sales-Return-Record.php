<?php 



?>

<ul class="breadcrumb">
    <li><a href="Report/All-Report">Report </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        

<div class="row">
<div class="col-md-12 form-horizontal">

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
<div class="col-sm-1">Report Type</div>
<div class="col-sm-3">
<select id="report_type" name="report_type" onchange="Change_Type('report_type','level-name','level-data');"  class="form-control select" >
<option value="">Select One</option>
<option  value="Multipal-Product-Wise">Product Wise</option>
<option  value="Multipal-Customer-Wise">Customer Wise</option>

</select>
</div>
<div class="col-sm-1"><b id="level-name"></b></div>
<div class="col-sm-3">
<b id="level-data"></b></div>
</div>
<div class="row" style="padding-top:15px">
<div class="col-sm-4"></div>
<div class="col-sm-4">
</div>
<div class="col-sm-4"></div>

</div>
</div>



<div class="row">
<div class="col-md-12 form-horizontal">

<div class="row">
<div class="col-sm-1">From</div>
<div class="col-sm-3">
<input  type="text" value="<?php print date('d-m-Y');?>" id="date_from" class="date form-control" >

</div>
<div class="col-sm-1">To</div>
<div class="col-sm-3">
<input  type="text" value="<?php print date('d-m-Y');?>" id="date_to" class="date form-control" >
</div>
<div class="col-sm-1"></div>
<div class="col-sm-3">
</div>
</div>
<div class="row" style="padding-top:15px">
<div class="col-sm-4"></div>
<div class="col-sm-4">
<input type="button" onclick="CommonReportGenerator('Sale Return Record');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
</div>
<div class="col-sm-4"></div>

</div>
</div>

<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">
 
    
    </div>
   
</div>