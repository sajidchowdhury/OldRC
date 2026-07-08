<?php 
        $xml_product_list = simplexml_load_file("xml_productList.xml");
?>
<div class="page-content-wrap" >                
                
                <div class="row">
                <div class="col-md-3"></div>
                    <div class="col-md-6">

                        <!-- START MODALS -->
                        <div class="panel panel-default" style="padding-top:50px">
                      
                            

<div class="row">
<div class="col-sm-1">Branch</div>
<div class="col-sm-3">
<select class="form-control select" id="report_type" name = "report_type"  style=" overflow: visible !important;">
<option value="All"> All </option>
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where  status = 'Active' ");
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


<div class="row" style="padding-top:15px">
<div class="col-sm-9">
<div class="form-group">
                                                <label class="col-sm-3 control-label">Category Wise</label>
                                                <div class="col-sm-9">                                                                                            
                        <select id="category_id" name="category_id[]" data-live-search="true" class="select selectpicker" multiple="multiple" data-selected-text-format="count>2" data-all="false">
                            
    <?php 
            $qry = $conn_me->prepare("SELECT * FROM `setup_category` ORDER BY `id` ASC");
            $qry->execute();
            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch) { ?>
    
                 <option  value="<?php print $fetch['id'];?>"><?php print $fetch['category'];?></option>
    
            <?php } ?>
        </select> <button onclick="selectAllOptions('category_id')">Select All</button>
                                                <button onclick="unselectAllOptions('category_id')">Unselect All</button>
                                                  
                                                   
                                                </div>
                                               

                                            </div>
</div>
<div class="col-sm-3">
<input type="button"  onclick="generateReport('Cold List Report','category_id')" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
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

