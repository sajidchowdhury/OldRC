<ul class="breadcrumb">
    <li><a>HRM </a></li>           
    <li><a>Report </a></li>          
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>

<div class="row animated bounceIn">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-body">
            <form id="myform">
            
            <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
          </div>

          <div class="col-md-4"></div>
            <div class="col-md-4">
            <table class="table table-hover table-condensed" >

           


                <tr>
                        <th>Department </th>

                        <td colspan="2"><select onchange="getEmloyeeForattanance(this.value)" class="select form-control" id="report_type" name="report_type" data-rel="chosen">
<option value="All">All Department</option>
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_department`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list AS $fetch) { ?>

    <option value="<?php print $fetch['id'];?>"><?php print $fetch['department'];?></option>
<?php } ?> 
</select></td>
                        
                        
                    </tr>
                    
                    <tr>
                  <th>Attendance Date</th>

                        <td  colspan="2"><input  type="text" value="<?php print date('d-m-Y') ;?>" id="date_from" class="date form-control text-danger" >
                        <input  type="hidden" value="" id="date_to" class="" >
</td>
</tr>
<tr>
                 

                        <td  colspan="3"><input  type="button" id="search_data" value="Search Data" onclick="generateReport('Daily-Attendance-Report','')" class="btn btn-info block" ></td>
</tr>
                
      
          </table>
</div>
          <div class="col-md-4"></div>

        </form>  
                
        </div>

    </div>
</div>
</div>
<div class="row" style="background-color: white;">

        <div class="panel-body "  id="laod_report">

       
      </div>
            

</div>
