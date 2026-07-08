<?php 
    $xml_raw_material_list = simplexml_load_file("xml_raw_material_list.xml");

?>

<ul class="breadcrumb">
    <li><a href="#">Report </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        


<div class="row">
<div class="col-md-12 form-horizontal">
<form id="myform">

<div class="form-group">

<label class="col-md-3 col-xs-12 control-label">Material List</label>
<div class="col-md-4 col-xs-12">
<select id="product_id" name="product_id"   class="form-control select" data-live-search="true">
      <option value="">Select One</option> 
<?php
      foreach ( $xml_raw_material_list->ROW as  $value ) { ?>

      <option value="<?php print $value['id'];?>"><?php print "$value[product_category] ::: $value[product_name]";?></b></option>

    <?php } ?> 
</select>
</div></div>

<div class="form-group">

<label class="col-md-3 col-xs-12 control-label"></label>
<div class="col-md-4 col-xs-12"><input type="text"  class="date form-control" value="<?php print date('d-m-Y');?>" id="date_from" name="date_from" >
</div></div>

<div class="form-group">

<label class="col-md-3 col-xs-12 control-label"></label>
<div class="col-md-4 col-xs-12"><input type="text"  class="date form-control" value="<?php print date('d-m-Y');?>" id="date_to" name="date_to" >
</div></div>


<div class="form-group">

<label class="col-md-3 col-xs-12 control-label"></label>
<div class="col-md-4 col-xs-12"><input type="button" onclick="generateReport('RAW Goods Movement','product_id');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" >
</div></div>


<input  type="hidden" value="RAW Goods Movement" id="report_type" class="" >


</div>
</div>

<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(' :: RAW Movement Report','MSalary')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel('Movement-Report','MSalary')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>


<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">

 </div>
   
</div>