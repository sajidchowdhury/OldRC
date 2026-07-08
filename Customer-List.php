

<div class="container">
  
<div class="row justify-content-center align-items-center g-2" style="padding-top:15px">
    <div class="col-md-12">

    <table class="table table-hover table-condensed table-striped table-bordered">
        <tr>
            <td>Type</td>
            <td>
            <select id="report_type" name="report_type" onchange="Change_Type('report_type','level-name','level-data');"  class="form-control select" >
            <option value="All">All</option>
            <option value="Multiple-Division-Wise">Division Wise</option>
            <option value="Multiple-District-Wise">District Wise</option>
            <option value="Multiple-Upazila-Wise">Upazila Wise</option>


</select>
            </td>
        </tr>
        <tr>
            <td><b id="level-name"></b></td>
            <td><b id="level-data"></b></td>

        </tr>
        <tr style="display:none">
        <td colspan="2"><input type="text" class="date form-control" value="<?php print date('d-m-Y');?>" id="date_from"></td>
        </tr>

        <tr style="display:none">
        <td colspan="2"><input type="text" class="date form-control" value="<?php print date('d-m-Y');?>" id="date_to"></td>
        </tr>
        <tr>
            <td colspan="2"> <input type="button" onclick="generateReport('Customer List','area_id');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
        </tr>
    </table>
    </div>
   
</div>
</div>

<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">
 
    
    </div>
   
</div>