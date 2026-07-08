<?php 
include('xml_employee_list.php');
$xml_employee_list = simplexml_load_file("xml_employee_list.xml");


$content = '<ul class="breadcrumb">
<li><a>HRM </a></li><li><a>Settings </a></li>              
<li class="active">'.$_GET['page_identity'].'</li>
</ul>';
$content .= '<div class="row">
<div class="col-md-12">

    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
       
        <table class="table datatable">
        <thead>
        <th>Photo</th>
        <th>ID</th>
        <th>Name</th>
        <th>Designation</th>
        <th>Contact No</th>
        <th>Status</th>
        <th>Action</th>
        </thead>
            <tbody>'; 
$sl =1;

foreach ( $xml_employee_list->ROW as  $value ) { 



        $content .= '<tr>
        <td><img style="height:70px" src="upload/employee_photo/'.$value['photo'].'"></td>
        <td>E'.$value['code'].'</td>
        <td>'.$value['name'].'</td>
        <td>'.$value['designation'].'</td>
        <td>'.$value['mob_no'].'</td>
        <td>'.$value['status'].'</td>
        <td><a href="HRM/Employee-Profile/'.$value['id'].'" ><i class="fa fa-edit danger"></i><a></td>
       </tr>';
    }



                
                  
$content .= '</tbody> 
      </table>
      </div>
            
    </div>

</div>
</div>';

print $content ;?>