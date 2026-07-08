<div class="row" style="padding-top:30px;">

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
<input type="button" onclick="product_damage('FG');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>

</div>


</div>
<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(' :: FG-Damage-Report','MSalary')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel('FG-Damage-Report','MSalary')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>

<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">
 </div>
   
</div>

