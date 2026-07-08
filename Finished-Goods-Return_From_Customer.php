<?php 



?>

<style>

.modal-ku {
  width:100%;
  margin: auto;
}
</style>


<ul class="breadcrumb">
    <li><a href="#">Sales </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        


<div class="row">
<div class="col-md-12 form-horizontal">
<form id="myform">


<div class="form-group">

<label class="col-md-3 col-xs-12 control-label">Invoice No</label>
<div class="col-md-4 col-xs-12">
   <input type="text"  class="form-control" value="" id="invoice_no">
</div></div>


<div class="form-group">

<label class="col-md-3 col-xs-12 control-label"></label>
<div class="col-md-4 col-xs-12"><input type="button" onclick="find_sales_invoice_by_no('Sales Return');" class="btn btn-info block" value="SEARCH INVOICE" id="search_invoice" name="search_invoice" >
</div></div>





</div>
</div>


<div class="row">
    <div class="col-12 form-horizontal" id="invoice_load">

    
    </div>
   
</div>