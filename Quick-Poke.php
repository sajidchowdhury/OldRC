<?php 
include('auth.php');

include('layout/head.php');
include('function_query.php');
$conn_me = Database::getInstance();

$topbar = SETUP::TOP_BAR('Notification','');



?>

    <body> 
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
            <?php include('layout/common_side_bar.php'); ?>

            <!-- END PAGE SIDEBAR -->
            
            <!-- PAGE CONTENT -->
            <div class="page-content">
                
                         
            <div id="load_breadcrumb"><?php print $topbar['top_bar_content'];?>   </div>

               
<div class="container">


<div class="row">
<div class="col-md-12 form-horizontal">
<form id="myform">
<table class="table table-hover table-condensed table-striped table-bordered" style="margin-bottom: 0px;">
                <tbody> 
                    <tr>
                        <th>Type  </th>
                        <td>
                        <select id="report_type" name="report_type" onchange="Change_Type('report_type','level-name','level-data');"  class="form-control select" >
<option value="">Select One</option>
<option value="All">All Employee </option>
<option value="Department-Wise">Department Wise</option>
<option value="Employee-Wise">Employee Wise</option>
<option value="Branch-Wise">Branch Wise</option>
</select>
                        </td>
                        </tr>
                        <tr>
                        <th><b id="level-name"></b></th>
                        <td><b id="level-data"></b></td>    


                    </tr>
                    <tr>
                        <th>Poke Mess</th>
                        <td><input  type="text" value="" id="poke_mess" class="form-control text-danger" ></td>
                    </tr>
                    <tr>
                        <td colspan="1">
                    

                        </td>
                        <td colspan="3" style="text-align:center">
                            <input type="button" onclick="pokeMess();" class="btn btn-info block" value="Poke Now" id="search_data" name="search_data" ></td>
                    </tr>
</tbody>
</table>





</div>
</div>

</div>
<div class="row">
    <div class="col-12 form-horizontal" id="load_push_mess">

 </div>
   
</div>

                                        
            </div>            
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->


        

<?php include('javascripts.php'); ?>

<script type="text/javascript" src="my_sz_script2.js"></script>

    </body>
</html>






