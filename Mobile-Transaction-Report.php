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
                        <th>Mobile Banking  </th>
                        <td>
                        <select id="mobile_bank_id" name="mobile_bank_id"   class="form-control select" >
<option value="">Select One</option>
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_mobile_banking` ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { ?>

    <option  value="<?php print $fetch['id'];?>" > <?php print "$fetch[mobile_bank_name] $fetch[mobile_number]";?></option>
<?php 
}
?>
</select>
                        </td>

                        <th>Transaction Type</th>
                        <td>
                            <select class="form-control select"  name="report_type" id="report_type">
                                <option value="All">All</option>
                                <option value="Deposit">Deposit</option>
                                <option value="Withdraw">Withdraw</option>
                            </select>
                        </td>    


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
                            <input type="button" onclick="generateReport('Mobile Banking Transaction Report','mobile_bank_id');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
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
