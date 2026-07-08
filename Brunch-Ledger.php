

<div class="page-content-wrap">                
                
                <div class="row">
                    <div class="col-md-12">

                        <!-- START MODALS -->
                        <div class="panel panel-default">
                      
                            <div class="panel-body">
                            <div class="table-responsive">
                            <table class="table table-striped">
                               
                            <tr > 

    <th style="text-align:right">Branch Name</th>
    <td colspan="2">
    <select class="form-control select" id="branch_id" name = "branch_id"  >
<option value="">Select One</option>
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where  status = 'Active'");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { ?>
<option  value="<?php print $fetch['id'];?>"><?php print $fetch['brunch'] ;?></option>
<?php } ?>
</select>
    </td>


   
</tr>
<input type="hidden" value="branch_id" id="report_wise_code">
<input type="hidden" id="report_type"  name = "report_type"   value="Brunch-Wise">

                                

                                <tr>
                                    <th>Date From</th>
                                    <td><input type="text"  class="date form-control" value="<?php print date('d-m-Y');?>" id="date_from" name="date_from" ></td>
                       
                                    <th>Date To</th>
                                    <td><input type="text"  class="date form-control" value="<?php print date('d-m-Y');?>" id="date_to" name="date_to" ></td>
                                </tr>

                               <tr>
                                    <th colspan="2"><input type="button"  id="search_data" value="S E A R C H" class="btn  btn-info" 
                                    onclick="CommonReportGenerator('Brunch Wise Ledger Report')" ></th>
                                   
                                </tr>



                            </table>
                                                </div>
                          
                            </div>
                        </div>
                        <!-- END MODALS -->

                    </div>


</div>

<div class="row">
                <div class="col-md-12" id="laod_report"></div>

</div>
</div>

