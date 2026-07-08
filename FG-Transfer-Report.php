<div class="row" style="padding-top:30px;">

<div class="row" >
<div class="col-sm-1">From Warehouse</div>
<div class="col-sm-3">
<select class="form-control select" id="from_warehouse_id" name = "from_warehouse_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_warehouse` ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { ?>
<option  value="<?php print $fetch['id'];?>"><?php print $fetch['name'] ;?></option>
<?php } ?>
</select>
<input type="button" class="btn btn-info"  onclick="selectAllOptions('from_warehouse_id')" value="Select All" >
<input type="button"  class="btn btn-danger" onclick="unselectAllOptions('from_warehouse_id')" value="Unselect All" >

</div>
<div class="col-sm-1">To Warehouse</div>
<div class="col-sm-3">
<select class="form-control select" id="to_warehouse_id" name = "to_warehouse_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_warehouse` ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { ?>
<option  value="<?php print $fetch['id'];?>"><?php print $fetch['name'] ;?></option>
<?php } ?>
</select>
<input type="button" class="btn btn-info"  onclick="selectAllOptions('to_warehouse_id')" value="Select All" >
<input type="button"  class="btn btn-danger" onclick="unselectAllOptions('to_warehouse_id')" value="Unselect All" >
</div>

</div>


<div class="col-sm-1" style="padding-top:15px">From</div>
<div class="col-sm-3" style="padding-top:15px">
<input  type="text" value="<?php print date('d-m-Y');?>" id="date_from" class="date form-control" >
</div>
<div class="col-sm-1" style="padding-top:15px">To</div>
<div class="col-sm-3" style="padding-top:15px">
<input  type="text" value="<?php print date('d-m-Y');?>" id="date_to" class="date form-control" >
</div>

<div class="col-sm-1"></div>
<div class="col-sm-3">
<input type="button" onclick="product_transfer('FG');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>

</div>


</div>





<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(' :: FG-Transfer-Report','MSalary')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel('FG-Transfer-Report','MSalary')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>

<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">
 </div>
   
</div>

