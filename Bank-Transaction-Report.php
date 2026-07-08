<?php 
?>

<ul class="breadcrumb">
    <li><a href="#">Report</a></li>           
    <li class="active"><?php print $_GET['page_identity']; ?></li>
</ul>

<div class="container">
    <div class="col-md-12 form-horizontal">
        <div class="table-responsive">
            <table class="table table-hover table-condensed table-striped table-bordered" style="margin-bottom: 0px;">
                <tbody> 
                    <tr>
                        <th>Bank Name</th>
                        <td>
                            
                            <select class="form-control select" id="bank_id" name = "bank_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
  
   
         <?php 
                               $qry = $conn_me->prepare("SELECT * FROM `setup_bank` ORDER BY `id` ASC");
                                $qry->execute();
                                $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($fetch_list as $fetch) { ?>
                                    <option value="<?php print $fetch['id']; ?>">
                                        <?php print "$fetch[bank_name] $fetch[account_number]"; ?>
                                    </option>
                                <?php } ?>
                                
                                
         </select>
        <input type="button" class="btn btn-info"  onclick="selectAllOptions('bank_id')" value="Select All" >
           <input type="button"  class="btn btn-danger" onclick="unselectAllOptions('bank_id')" value="Unselect All" >
           
           
                           
                        </td>

                        <th>Transaction Type</th>
                        <td>
                            <select class="form-control select" name="report_type" id="report_type">
                                <option value="All">All</option>
                                <option value="Deposit">Deposit</option>
                                <option value="Withdraw">Withdraw</option>
                            </select>
                        </td>    
                    </tr>

                    <tr>
                        <th>From</th>
                        <td>
                            <input type="text" value="<?php print date('d-m-Y'); ?>" id="date_from" class="date form-control">
                        </td>
                        <th>To</th>
                        <td>
                            <input type="text" value="<?php print date('d-m-Y'); ?>" id="date_to" class="date form-control">
                        </td>
                    </tr>

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

                        <td colspan="2" style="text-align:center">
                            <input type="button" onclick="BankTrReport('Bank Transaction Report','bank_id');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div> <!-- /.table-responsive -->
    </div> <!-- /.col-md-12 -->
</div> <!-- /.container -->

<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">
        <!-- Report content will be loaded here -->
    </div>
</div>
