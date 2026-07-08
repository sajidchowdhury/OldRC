<div class="page-title">                    
<h2><span class="fa fa-arrow-circle-o-left"></span> Balance Sheet </h2>
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
                                    <th colspan="2"><input type="button"  id="search_data" value="S E A R C H" class="btn  btn-info" 
                                    onclick="generateReport('Balance-Sheet','')" ></th>
                                   
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

