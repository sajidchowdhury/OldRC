<?php 

?>

<ul class="breadcrumb">
    <li><a href="#">Report </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        


<div class="row">
<div class="col-md-12 form-horizontal">
<form id="myform">

<div class="form-group">

<label class="col-md-3 col-xs-12 control-label">Type</label>
<div class="col-md-4 col-xs-12">
<select id="report_type" name="report_type" onchange="Change_Type('report_type','level-name','level-data');"  class="form-control select" >
<option value="All">All</option>
<option value="Supplier-Wise">Supplier Wise</option>
</select>
</div></div>


<div class="form-group">

<label class="col-md-3 col-xs-12 control-label" ><b id="level-name"></b></label>
<div class="col-md-4 col-xs-12"><b id="level-data"></b>
</div></div>


<div class="form-group">

<label class="col-md-3 col-xs-12 control-label"></label>
<div class="col-md-4 col-xs-12"><input type="button" onclick="generateReport('Supplier Due Report','supplier_id');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" >
</div></div>


<input  type="hidden" value="" id="date_to" class="" >
<input  type="hidden" value="" id="date_from" class="" >


</div>
</div>


<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">

 </div>
   
</div>