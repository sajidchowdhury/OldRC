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

<label class="col-md-3 col-xs-12 control-label">Type</label>
<div class="col-md-4 col-xs-12">
<select id="purches_type" name="purches_type"   class="form-control select" >
<option value="">Select One</option>
<option value="raw_local_purches">Raw Material</option>
<option    value="fg_local_purches">Finishied Goods</option>
</select>
</div></div>


<div class="form-group">

<label class="col-md-3 col-xs-12 control-label">Invoice No</label>
<div class="col-md-4 col-xs-12">
   <input type="text"  class="form-control" value="" id="invoice_no">
</div></div>


<div class="form-group">

<label class="col-md-3 col-xs-12 control-label"></label>
<div class="col-md-4 col-xs-12"><input type="button" onclick="find_purchase_invoice_by_no('Purchase Invoice');" class="btn btn-info block" value="SEARCH INVOICE" id="search_invoice" name="search_invoice" >
</div></div>





</div>
</div>


<div class="row">
    <div class="col-12 form-horizontal" id="invoice_load">

    
    </div>
   
</div>