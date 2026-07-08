<?php 



?>

<ul class="breadcrumb">
    <li><a href="#">Sales </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        


<div class="row">
<div class="col-md-12 form-horizontal">
<form id="myform">

<div class="form-group">

<label class="col-md-3 col-xs-12 control-label">Invoice</label>
<div class="col-md-4 col-xs-12">
<select class="form-control select" id="invoice_id" name = "invoice_id"  data-live-search="true">
<option value="">Select One</option>

<?php 
$qry = $conn_me->prepare("SELECT * FROM `sales_invoice` ORDER BY `invoice_date` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { ?>

<option  value="<?php print $fetch['id'];?>"><?php print $fetch['invoice_no'];?></option>
<?php 
}
?>

</select>
</div></div>



<div class="form-group">

<label class="col-md-3 col-xs-12 control-label"><b id="level-name"></b></label>
<div class="col-md-4 col-xs-12">
  <div id="level-data"></div>
</div></div>


<div class="form-group">

<label class="col-md-3 col-xs-12 control-label"></label>
<div class="col-md-4 col-xs-12">
    <input type="button" onclick="generateReport('Invoice Movement Report','invoice_id')" class="btn btn-info block" value="SEARCH INVOICE"  id="search_data" name="search_invoice" >
</div></div>


<input type="hidden"  id="date_from" class="form-control" value="<?php print date('Y-m-d');?>"/>
<input type="hidden"  id="date_to" class="form-control" value="<?php print date('Y-m-d');?>"/>
<input type="hidden"  id="report_type" class="form-control" value="Sales-Invoice"/>

</div>
</div>


<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">

    
    </div>
   
</div>