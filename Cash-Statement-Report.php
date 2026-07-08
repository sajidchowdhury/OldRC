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

<?php if ( $_SESSION['USER_TYPE'] == 'Admin') { ?>
    <th style="text-align:right">Branch Name</th>
    <td colspan="2">
    <select class="form-control select" id="branch_id" name = "branch_id"  >
<option value="All">All</option>
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where  status = 'Active' ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { ?>
<option  value="<?php print $fetch['id'];?>"><?php print $fetch['brunch'] ;?></option>
<?php } ?>
</select>
    </td>
<?php   }else{ ?>

<input type="hidden" id="branch_id"  name = "branch_id"   value="<?php print $_SESSION['USER_BRUNCH'];?>">

<?php   } ?>
   
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
                            <input type="button" onclick="generateReport('Cash Statement Report','branch_id');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
                    </tr>
</tbody>
</table>
</div>
<input  type="hidden" value="Cash Statement Report" id="report_type" >



</div>
</div>

</div>
<div class="row" style="background-color:white">
    <div class="col-12 form-horizontal" id="laod_report">

 </div>
   
</div>
