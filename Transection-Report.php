<div class="page-title">                    
<h2><span class="fa fa-arrow-circle-o-left"></span> Transaction Report</h2>
</div>

<div class="page-content-wrap">                
                
                <div class="row">
                <div class="col-md-3"></div>
                    <div class="col-md-6">

                        <!-- START MODALS -->
                        <div class="panel panel-default">
                      
                            <div class="panel-body">
                            <div class="table-responsive">

                            <table class="table table-striped">
                                
                                                <tr > 

<?php if ( $_SESSION['USER_TYPE'] == 'Admin') { ?>
    <th style="text-align:right">Branch Name</th>
    <td colspan="2">
    <select class="form-control select" id="branch_id" name = "branch_id"  >
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
                                    <th>Report Type</th>
                                    <th>
                                        <select name="report_type" id="report_type"   class="form-control select">
                                            <option value="All">Transaction History</option>
                                            <option value="INCOME">Income History</option>
                                            <option value="EXPENCE">Expence History</option>


                                        </select>
                                    </th>
                                </tr>

                                <tr>
                                    <th>Date From</th>
                                    <th>
                                    <input type="date"  id="date_from" class="form-control" value="<?php print date('Y-m-d');?>"/>
                                    </th>
                                </tr>

                                <tr>
                                    <th>Date To</th>
                                    <th>
                                    <input type="date"  id="date_to" class="form-control" value="<?php print date('Y-m-d');?>"/>
                                    </th>
                                </tr>

                               <tr>
                                    <th colspan="2"><input type="button" id="search_data" value="S E A R C H" class="btn  btn-info" 
                                    onclick="generateReport('Transaction-Report','branch_id')" ></th>
                                   
                                </tr>



                            </table></div>
                          
                            </div>
                        </div>
                        <!-- END MODALS -->

                    </div>
                    <div class="col-md-3"></div>


</div>

<div class="row">
                <div class="col-md-12" id="laod_report"></div>

</div>
</div>

