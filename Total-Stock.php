<?php
include('auth.php');
include_once('function_query.php');
$permission_management2 = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],'Final','final_demand','onclick="final_pending_mold_demand();"');


?>
<style>
    #table-scroll {
        height: 550px;
        overflow: auto;
        margin-top: 20px;
    }

    .panel-body.panel-body-table td,
    .panel-body.panel-body-table th {
        padding: 1px 8px;
    }
</style>


<div class="panel panel-default tabs">                            
                                    <ul class="nav nav-tabs" role="tablist" style="padding-top:15px;">

                                        <li class="active col-md-6 info"><a href="#tab-first" role="tab" data-toggle="tab">FG Stock</a></li>
                                        <li class="col-md-6"><a href="#tab-second" role="tab" data-toggle="tab">Raw Stock</a></li>

                                    </ul>
                                    <div class="panel-body tab-content">
                                        <div class="tab-pane active" id="tab-first">
                                           
                                        
                                        <div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(' :: Total Stock Report','ATable')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel('Total-Stock','ATable')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>
        <div id="printableArea">


<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">

         
            <div class="panel-body panel-body-table">
           

            <div class="progress">
  <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
</div>
       
<div id="load_report">

</div>

                                

 
</div>                                                

</div></div>
</div>



</div>
                                        
        
                                        </div> <!-- end of fg tab -->
                                        <div class="tab-pane" id="tab-second">
<div class="form-group">
            
                   
                   
            <div class="col-md-8">
                <div class="input-group">
                    <div class="input-group-addon">
                        <span class="fa fa-search"></span>
                    </div>

                    <input class="form-control" onkeyup="filter_data()" id="search_table_row" type="text" placeholder="Search..">



                  
                    <div class="input-group-btn">
                        <input type="button" class="btn btn-primary" id="submit-search_raw" value="Search">

                    </div>
                </div>
            </div>
            

         
<div class="col-md-4">
                
                    
                    <a  href="#modal_large"  data-toggle="modal" data-backdrop="static" class="btn btn-danger block"  data-keyboard="false"  data-whatever1="Filter By Product & Category" data-whatever2="filter_by_product_category" data-whatever3="NEW_DATA" >Filter</a>                        
     

</div>
           




        </div>

        <div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(' :: Total Stock Report','ATable')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel('Total-Stock','ATable')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>



                                        <div id="printableArea">

<div id="printableArea">
<div class="row">
<div class="col-md-12">
<div class="panel panel-default">

 
    <div class="panel-body panel-body-table">
   

<div class="table-responsive">
<div id="table-scroll" >

<table  class="table table-bordered table-striped" style="white-space:nowrap;">
<thead>
<tr  class="headcol">
<th>SL No.</th>
<th> Product</th>

<th> Category </th>
<?php 
$query1 = $conn_me->prepare("SELECT * FROM `setup_warehouse`  ORDER BY `name` ASC "); 
$query1->execute();
$fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list1 AS $fetch1){  ?>

<th colspan="2" style="align:center"><?php print $fetch1['name'];?></th>
<?php } ?>
<th colspan="2">Total</th>

</tr>
<?php 
$query1 = $conn_me->prepare("SELECT * FROM `setup_warehouse`  ORDER BY `name` ASC "); 
$query1->execute();
$fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
?>

<tr>
<td colspan="3"></td>
<?php 
foreach($fetch_list1 AS $fetch1){ ?>

<td >Cartoon</td><td>Pieces</td>
<?php  }?>
<td>Cartoon</td><td>Pieces</td>
</tr>

</thead>
<tbody id="load_report2"><tbody>
</table>
</div>
</div>                                


</div>                                                

</div></div>
</div>

</div>

</div>
                                        
                                        </div>       <!-- end of raw tab -->                                 
                                        
                                    </div>
                                    <div class="panel-footer">        

                                    </div>
                                </div>                                
                            

<script type="text/javascript">
window.onload = function() {
    filter_data()
  };
</script>
