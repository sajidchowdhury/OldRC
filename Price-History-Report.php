<?php 

?>

<ul class="breadcrumb hidden-print">
    <li><a href="#">Report </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        

<div class="container hidden-print">
<div class="row">
<div class="col-md-12 hidden-print">

<table class="table table-hover table-condensed table-striped table-bordered hidden-print">

<tr>
                        <th> Type  </th>
                        <th>
                        <select id="report_type" name="report_type" onchange="Change_Type('report_type','level-name','level-data');"  class="form-control select" >
<option value="All">All</option>
<option  value="Finished-Goods">Product Wise</option>

</select>
</th>

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
                        <td colspan="1" style="display:none">
                      With Detail?<label class="switch">
    <input type="checkbox"  id="target1"  name="target1" value="NO"  />
    <span></span>
</label>

                        </td>
                        <td colspan="2" style="text-align:center">
                            <input type="button"  onclick="generateReport('PRODUCT-PRICE-HISTORY','product_id');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>

                            <td >
                        <div class="panel-heading">
                                    <div class="btn-group pull-right" id="printbutton">
                                    
                                       
                                    </div>                                    
                                    
                                </div>
                        </td>

                    </tr>
                   
</table>

</div>
</div>
</div>





<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">

 </div>
   
</div>

