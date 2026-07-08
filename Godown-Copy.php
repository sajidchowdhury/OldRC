<div class="page-content-wrap">                
                
                <div class="row">
                <div class="col-md-3"></div>
                    <div class="col-md-6">

                        <!-- START MODALS -->
                        <div class="panel panel-default">
                      

                        <div class="row" style="padding-top:30px">
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
<div class="col-sm-1">From</div>
<div class="col-sm-3">
<input  type="text" value="<?php print date('d-m-Y');?>" id="date_from" class="date form-control" >
</div>
<div class="col-sm-1">To</div>
<div class="col-sm-3">
<input  type="text" value="<?php print date('d-m-Y');?>" id="date_to" class="date form-control" >
</div>
</div>
                            <div class="panel-body">
                            <div class="table-responsive">
                            <table class="table table-striped">
                  

                          <tr>
                                    <th colspan="2" style="text-align:center"><input type="button"  id="search_data" value="S E A R C H" class="btn  btn-info" 
                                    onclick="generateReport('All Godown Copy','branch_id')" ></th>
                                   
                                </tr>



                            </table>
                                                </div>
                          
                            </div>
                        </div>
                        <!-- END MODALS -->

                    </div>
                    <div class="col-md-3"></div>

<input type="hidden" id="report_type" value="">
</div>

<div class="row">
                <div class="col-md-12" id="laod_report"></div>

</div>
</div>

