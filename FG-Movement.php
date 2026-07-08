<?php 
    $xml_product_list = simplexml_load_file("xml_productList.xml");

?>

<ul class="breadcrumb hidden-print">


    <li><a href="#">Report </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
<div class="col-md-12">
<table class="table table-hover table-condensed table-striped table-bordered">
    <tr>
        <td>Product List</td>
        <td>
        <select id="product_id" name="product_id"   class="form-control select" data-live-search="true">
      <option value="">Select One</option> 
<?php
      foreach ( $xml_product_list->ROW as  $value ) { ?>

      <option value="<?php print $value['id'];?>"><?php print "$value[product_category] ::: $value[product_name]";?></b></option>

    <?php } ?> 
</select>
        </td>
        <td>Report Type</td>
        <td>
            <select name="" id="report_type" class="form-control select" onchange="Change_Type('report_type','level-name','level-data');">
            <option value="">Select One</option>
            <option value="Multipal-Warehouse-Wise">Warehouse Wise</option>
            <option value="Multipal-Branch-Wise">Branch Wise</option>

            </select>
        </td>

        <th><b id="level-name"></b></th>
        <td><b id="level-data"></b></td>    

        <td>From</td>
        <td><input type="text"  class="date form-control" value="<?php print date('d-m-Y');?>" id="date_from" name="date_from" ></td>
        <td>To</td>
        <td><input type="text"  class="date form-control" value="<?php print date('d-m-Y');?>" id="date_to" name="date_to" ></td>
        <td><input type="button" onclick="PRODUCT_MOVEMENT_REPORT('Finished Goods Movement');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>

    </tr>
    <tr>
      

        </td>
    </tr>
</table>

</div>

<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(' :: FG Movement Report','MSalary')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel('Movement-Report','MSalary')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>

<div class="row hidden-print">
    <div class="col-12 form-horizontal" id="laod_report">

 </div>
   
</div>
