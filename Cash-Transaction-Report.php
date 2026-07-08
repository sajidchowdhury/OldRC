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
                        <th>Brunch Name</th>
                        <td>
                            <select id="brunch_id" name="brunch_id" class="form-control select">
                                <option value="All">All</option>
                                <?php 
                                $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` ORDER BY `id` ASC");
                                $qry->execute();
                                $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($fetch_list as $fetch) { ?>
                                    <option value="<?php print $fetch['id']; ?>">
                                        <?php print "$fetch[brunch]"; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>

                        <td colspan="2" style="text-align:center"></td>
                    </tr>
                    
                    
                    
                    <tr>
                        <th>Transaction Type</th>
                        <td>
                        <select class="form-control select"  name="report_type" id="report_type" onchange="Change_Type('report_type','level-name','level-data');">
                                <option value="All">All</option>
                                <option value="Receive-Account">Receive</option>
                                <option value="Payment-Account">Payment</option>
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
                            <input type="button" onclick="BankTrReport('Cash Transaction Report','account_head_id');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
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
