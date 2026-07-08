<?php

if($_GET['related_id'] == 'New'){

    $number_of_days = '';
    $related_id = 'new_id';
    $employee_id = '';
    $leave_type_id = '';
    $employee_id = '';
    $department = '';
    
    }else{
    
    $DATA  = SETUP::SETUP_LEAVE_DEFINE($_GET['related_id']);
    
    $employee_id = $DATA['employee_id'];
    $related_id =  $DATA['id'];
    $leave_type_id =  $DATA['leave_type_id'];
    $number_of_days =  $DATA['number_of_days'];
    $department = '';
    
    
    }
?>

<ul class="breadcrumb">
    <li><a href="#">HRM </a></li>           
    <li><a href="#">Leave </a></li>                             
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
             


<input type="hidden" name="related_id" id="related_id" value="<?php print $related_id;?>" >
<input type="hidden" name="leave_left" id="leave_left" value="" >
<input type="hidden" name="year" id="year" value="<?php print date('Y');?>" >
<input type="hidden" name="month" id="month" value="<?php print date('m');?>" >

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
                        <th>Leave Type  </th>

                        <td colspan="5">
                        <select  class="select form-control" id="leave_type_id" onchange="leaveType()" name="leave_type_id" data-rel="chosen">
<option value="">Select One</option>

<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_leave_type`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list AS $fetch) { ?>

    <option <?php  if($leave_type_id == $fetch['id']){ ?> selected="selected" <?php }else{ } ?> value="<?php print $fetch['id'];?>"><?php print $fetch['type_name'];?></option>
<?php } ?> 
</select>
                        </td>
                        
                        
                    </tr>

                    <tr>
                <th>Leave Name</th>
                <td colspan="5">
                    <input type="text" id="leave_name" class="form-control" value="">
                </td>
            </tr>

     

        
                    <tr>
                        <th id="listType"> </th>

                        <td id="employee_list"  colspan="3"></td>

                       <td id="select_clear_toggole"  colspan="2"></td> 
                    </tr>


                    <tr>
            <th class="text-danger" id="leave_history" colspan="6" style="text-align:center"></th>

            </tr>


                    <tr>
                    <th>Leave Date</th>

                    <td colspan="2" ><input class="date form-control" type="text" id="leave_start" value="" > </td> 


                    <th id="level1" style="display:none"> End</th>

                    <td colspan="2" ><input  style="display:none" class="date form-control" type="text" id="leave_end" value="" > </td> 


                    </tr>



                    <tr>
                        <td style="text-align:center;" colspan=2><input type="button" name="save_define_leave" id="save_define_leave" class="btn btn-primary" value="Define Leave"> </td>
                    </tr>
      
          </table>
</div>
          <div class="col-md-4"></div>

        </form>  
                
        </div>

    </div>
</div>

<div class="row">
<div class="col-md-12">

    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
       
        <table class="table datatable">
        <thead>
        <th>Sl</th>
        <th>Leave Type</th>
        <th>Employee Name</th>
        <th>Days</th>
        <th>Action</th>
        </thead>
            <tbody>
                <?php 
$sl =1;
$this_year = date('Y');
$qry = $conn_me->prepare("SELECT `id` FROM `setup_leave_define`  where `year` = '".$this_year."' ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
if ($qry->rowCount() > 0)
{
    foreach($fetch_list AS $fetch) {
    $info = SETUP:: SETUP_LEAVE_DEFINE($fetch['id']);
?>
<tr>
        <th><?php print $sl++ ;?></th>
        <th><?php print $info['leave_type'] ;?></th>
        <th><?php print $info['employee_name'] ;?></th>
        <th><?php print $info['number_of_days'] ;?></th>
        
        <td><a href="HRM/Leave-Define/<?php print $fetch['id'];?>" ><i class="fa fa-edit danger"></i><a></td>
       </tr>

    <?php }

}
            ?>    
                  
</tbody> 
      </table>
      </div>
            
    </div>

</div>
</div>