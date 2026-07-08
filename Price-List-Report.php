<?php 



?>

<ul class="breadcrumb">
    <li><a href="Report/All-Report">Report </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        


<div class="row">
<div class="col-md-12 form-horizontal">
<form id="myform">

<div class="form-group">

<label class="col-md-3 col-xs-12 control-label">Category</label>
<div class="col-md-4 col-xs-12">
<select  style="width:100%!imortant" id= "category_id" name="category_id[]" data-live-search=true class="selectpicker" multiple data-selected-text-format="count>3" data-all="false">
<?php 
        $qry = $conn_me->prepare("SELECT * FROM `setup_category` ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {  ?>

    <option  value="<?php print $fetch['id'];?>"><?php print $fetch['category'];?></option>

        <?php } ?>
</select>
</div><input type="button" class="btn btn-info"  onclick="selectAllOptions('category_id')" value="Select All" >
        <input type="button"  class="btn btn-danger" onclick="unselectAllOptions('category_id')" value="Unselect All" ></div>


<div class="form-group">

<label class="col-md-3 col-xs-12 control-label" ><b id="level-name"></b></label>
<div class="col-md-4 col-xs-12"><b id="level-data"></b>
</div></div>


<div class="form-group">

<label class="col-md-3 col-xs-12 control-label"></label>
<div class="col-md-4 col-xs-12"><input type="button" onclick="generateReport('Price List','category_id');" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" >
</div></div>


<input  type="hidden" value="" id="date_to" class="" >
<input  type="hidden" value="" id="date_from" class="" >

<input  type="hidden" value="FG-Category" id="report_type" class="" >

</div>
</div>


<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">
 
    
    </div>
   
</div>