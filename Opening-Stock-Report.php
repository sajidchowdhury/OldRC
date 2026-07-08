<?php 

?>

<ul class="breadcrumb">
    <li><a href="#">Report </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        

<div class="container">


<div class="row">
<div class="col-md-12 form-horizontal">
<form id="myform">
<table class="table table-hover table-condensed table-striped table-bordered" style="margin-bottom: 0px;">
                <tbody> 
                    <tr >
                        <th>Select Type</th>
                        <td>
                        <select class="form-control select"  name="report_type" id="report_type" onchange="Change_Type('report_type','level-name','level-data');">
                        <option value="">Select One</option>
                        <option value="Multipal-Warehouse-Wise">Warehouse Wise</option>

                         
                             

                            </select>
                        </td>

                        <th><b id="level-name"></b></th>
                        <td><b id="level-data"></b></td>    
                    </tr>
                    <tr>
                        
                        <th>From</th>
                        <td><input  type="date" value="<?php print date('Y-m-d');?>" id="date_from" class="form-control" ></td>
                        <th>To</th>
                        <td><input  type="date" value="<?php print date('Y-m-d');?>" id="date_to" class="form-control" ></td>
                    </tr>
                    <tr>
                        <td colspan="1"></td>
                        <td colspan="3" style="text-align:center">
                            <input type="button" onclick="OpeningStockReport();" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
                    </tr>
</tbody>
</table>





</div>
</div>

</div>



<div class="row" id="block">
    <div class="col-12 form-horizontal" id="laod_report">

 </div>
   
</div>


