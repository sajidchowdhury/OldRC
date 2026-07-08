<div class="page-title">                    
<h2><span class="fa fa-arrow-circle-o-left"></span> Account Head Wise Report</h2>
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
                                <tr>
                                <th>Brunch</th>
                                    <th>
                                    <select class="form-control select" id="branch_id" name = "branch_id"  style=" overflow: visible !important;">
<option value="All">All</option>
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where  status = 'Active'");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { ?>
<option  value="<?php print $fetch['id'];?>"><?php print $fetch['brunch'] ;?></option>
<?php } ?>
</select>
                                    </th>

                                    <th></th>
                        <td></td>   
                                </tr>
                                <tr>
                                    <th>Report Type</th>
                                    <th>
                                        <select name="report_type" id="report_type"   class="form-control select"  onchange="Change_Type('report_type','level-name','level-data');" >
                                        <option value="">Select One</option>
                                        <option value="Multipal-Ledger-Wise">Ledger Wise</option>
                                        <option value="Multipal-Head-Wise">Ac Head Wise</option>


                                        </select>
                                    </th>

                                    <th><b id="level-name"></b></th>
                        <td><b id="level-data"></b></td>    


                                </tr>

                                <tr>
                                    <th>Date From</th>
                                    <th>
                                    <input type="date"  id="date_from" class="form-control" value="<?php print date('Y-m-d');?>"/>
                                    </th>
                       
                                    <th>Date To</th>
                                    <th>
                                    <input type="date"  id="date_to" class="form-control" value="<?php print date('Y-m-d');?>"/>
                                    </th>
                                </tr>

                               <tr>
                                    <th colspan="2"><input type="button"  id="search_data" value="S E A R C H" class="btn  btn-info" 
                                    onclick="CommonReportGenerator('Account Head Wise Report')" ></th>
                                   
                                </tr>



                            </table>
                                                </div>
                          
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

