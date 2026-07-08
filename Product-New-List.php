<?php 

?>

<ul class="breadcrumb">
    <li><a href="#">Report </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        

<div class="container">
<input type="hidden" id="section" value="FG-STOCK" >


<div class="row">
<div class="col-md-12 form-horizontal">
<form id="myform">
<table class="table table-hover table-condensed table-striped table-bordered" style="margin-bottom: 0px;">
                <tbody> 
                    <tr>
                        <th>Select Type</th>
                        <td>
                        <select class="form-control select"  name="report_type" id="report_type" onchange="Change_Type('report_type','level-name','level-data');">
                        <option value="">Select One</option>

                                <option value="Multipal-Category-Wise">Category Wise </option>
                                <option value="Multipal-Product-Wise">Product Wise </option>
                                <option value="Multipal-Warehouse-Wise">Warehouse Wise</option>


                            </select>
                        </td>

                        <th><b id="level-name"></b></th>
                        <td><b id="level-data"></b></td>    
                    </tr>
                    <tr>
                        <th>From</th>
                        <td><input  type="text" value="<?php print date('d-m-Y');?>" id="date_from" class="date form-control" ></td>
                        <th>To</th>
                        <td><input  type="text" value="<?php print date('d-m-Y');?>" id="date_to" class="date form-control" ></td>

                    </tr>
                    <tr>
                        <td colspan="1"></td>
                        <td colspan="3" style="text-align:center">
                            <input type="button" onclick="modifyreport('Product-New-List');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
                    </tr>
</tbody>
</table>





</div>
</div>

</div>




<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(' :: Product-New-List','MSalary')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel('Product-New-List','MSalary')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>
<div class="row" id="block">
    <div class="col-12 form-horizontal" id="laod_report">

 </div>
   
</div>

</div>

<script>

 
</script>