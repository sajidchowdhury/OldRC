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
    
<div class="row">
<div class="col-sm-1">Branch</div>
<div class="col-sm-3">
<select class="form-control select" id="branch_id" name = "branch_id"  style=" overflow: visible !important;">
<option value="All">All</option>
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch`  where status = 'Active'  ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { ?>
<option  value="<?php print $fetch['id'];?>"><?php print $fetch['brunch'] ;?></option>
<?php } ?>
</select>
</div>
<div class="col-sm-1"></div>
<div class="col-sm-3">
<input type="button" onclick="generateReport('PREORDER-PRODUCT-LIST','branch_id');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>

<input  type="hidden" value="<?php print date('d-m-Y');?>" id="date_from" class="date form-control" >
</div>
<div class="col-sm-1"></div>
<div class="col-sm-3">
<input  type="hidden" value="<?php print date('d-m-Y');?>" id="date_to" class="date form-control" >
</div>
</div>
<div class="row" style="padding-top:15px">
<div class="col-sm-4"></div>
<div class="col-sm-4">
</div>
<div class="col-sm-4"></div>

</div>





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