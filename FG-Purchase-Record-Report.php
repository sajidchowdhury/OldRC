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
                    <tr>
                        <th>Type  </th>
                        <td>
                        <select id="report_type" name="report_type" onchange="Change_Type('report_type','level-name','level-data');"  class="form-control select" >
<option value="All">All</option>
<option value="Supplier-Wise">Supplier Wise</option>
<option  value="Finished-Goods">Product Wise</option>
<option  value="FG-Category">Category Wise</option>
<option  value="By-Purchase-Invoice">By Invoice</option>
<option  value="Employee-Wise">By Employee</option>

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
                        <td colspan="1">
                      With Detail?<label class="switch">
    <input type="checkbox"  id="target1"  name="target1" value="NO"  />
    <span></span>
</label>

                        </td>
                        <td colspan="3" style="text-align:center">
                            <input type="button" onclick="purchase_record_report('target1','FG');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
                    </tr>
</tbody>
</table>





</div>
</div>

</div>
<div class="row">

 



    <div class="col-12 form-horizontal" id="laod_report">

 </div>
   
</div>
