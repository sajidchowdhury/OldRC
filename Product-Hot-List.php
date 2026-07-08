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
                        <option value="Current-Stock">Current Stock</option>

                                <option value="FG-Category">Category Wise Stock</option>
                                <option value="Finished-Goods">Product Wise Stock</option>


                            </select>
                        </td>

                        <th><b id="level-name"></b></th>
                        <td><b id="level-data"></b></td>    
                    </tr>
                    <tr>
                        <th></th>
                        <td><input  type="hidden" value="<?php print date('d-m-Y');?>" id="date_from" class="date form-control" ></td>
                        <th></th>
                        <td><input  type="hidden" value="<?php print date('d-m-Y');?>" id="date_to" class="date form-control" ></td>

                    </tr>
                    <tr>
                        <td colspan="1"></td>
                        <td colspan="3" style="text-align:center">
                            <input type="button" onclick="SHORT_STOCK_REPORT();" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
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
        <li><a onclick="exportToExcel('Product-Hot-List','MSalary2')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>

<div class="row" id="block">
    <div class="col-12 form-horizontal" id="laod_stock_report">

 </div>
   
</div>
<div class="container">
<div class="row">
    <div class="col-sm-4"></div>
    <div class="col-sm-4"> <img src="img\blueimp\loading.gif" id="laoding" style="height:100px;padding:15px;display:none"></div>
    <div class="col-sm-4"></div>
 </div>   
</div>