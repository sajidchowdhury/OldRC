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
<div class="table-responsive">

<table class="table table-hover table-condensed table-striped table-bordered" style="margin-bottom: 0px;">
                <tbody> 
                    <tr>
                        <th>Type  </th>
                        <td>
                        <select id="report_type" name="report_type" onchange="Change_Type('report_type','level-name','level-data');"  class="form-control select" >
<option value="Monthly-Salery">Monthly Salery </option>
<option value="Payment-Record">Payment Record</option>


</select>
                        </td>

                        <th><b id="level-name"></b></th>
                        <td><b id="level-data"></b></td>    


                    </tr>
                    <tr>
                        <th>Month</th>
                        <td><input  type="text" value="<?php print date('m-Y');?>" id="date_from" class="monthonly form-control text-danger" ></td>
                        <td><input  type="hidden" value="<?php print date('d-m-Y');?>" id="date_to" class="date form-control" ></td>

                    </tr>
                    <tr>
                        <td colspan="1">
                    

                        </td>
                        <td colspan="3" style="text-align:center">
                            <input type="button" onclick="generateReport('Salary-Report','employee_id');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
                    </tr>
</tbody>
</table>
</div>





</div>
</div>

</div>


<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">

 </div>
   
</div>
