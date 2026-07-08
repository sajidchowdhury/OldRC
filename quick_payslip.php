<?php

   
?>

<ul class="breadcrumb">
    <li><a href="#">HRM </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        
<div class="row">
<div class="col-md-12">
<div id="load_msg"></div>
</div></div>
<div class="row">
   <div class="col-md-12">

   <table class="table table-hover table-condensed table-striped table-bordered datatable">

   <tr>
   <th>Sl</th>

<th>Employee Name</th>
<th>Action</th>


   </tr>

   <?php 
$sl = 1;
$query = $conn_me->prepare("SELECT *  FROM `quick_pay_slip` ");
$query->execute();
$fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { 
    $employe_code = str_replace("E", "", $fetch['employe_code']);


    $info_emp = SETUP::SETUP_EMPLOYEEY_BY_CODE($employe_code);
    $info = SETUP::SETUP_EMPLOYEEY($info_emp['id']);

    ?>

<tr>
<td><?php print $sl++;?></td>
<td><?php print $info['name'];?></td>
<td><a target = "_BLINK" href="quick_slip.php?id=<?php print $fetch['id'];?>">View Slip</a></td>


   </tr>
<?php } ?>
   </table>
</div>
</div>

