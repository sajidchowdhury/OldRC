<?php 

$content = '<input type="hidden" id="report_type" value="All" ><input type="hidden" id="date_from" value=""><input type="hidden" id="date_to" value="">';

$content .= '<div class="row">
<div class="col-md-12">

<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Supplier List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Supplier-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>
    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
        <div class="table-responsive ">

        <table class="table table-hover table-condensed table-striped table-bordered datatable" id="MSalary">
        <thead>
        <th>Sl</th>
        <th>Supplier ID</th>
        <th>Supplier Name</th>
        <th>Owner Name</th>
        <th>Description</th>
        <th>e-mail</th>
        <th>Address</th>
        <th>Contact No</th>
        <th>Action</th>

        </thead>
            <tbody>'; 
$sl =1;
$qry = $conn_me->prepare("SELECT * FROM `setup_supplier`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
if ($qry->rowCount() > 0)
{
    foreach($fetch_list AS $fetch) {



        $content .= '<tr>
        <th>'.$sl++.'</th>
        <th>'.$fetch['code'].'</th>
        <th>'.$fetch['supplier_name'].'</th>
        <th>'.$fetch['owner_name'].'</th>
        <th>'.$fetch['description'].'</th>
        <th>'.$fetch['email'].'</th>
        <th>'.$fetch['address'].'</th>
        <th>'.$fetch['mobile'].'</th>
        <td><a href="sales/Supplier-Setup/'.$fetch['id'].'" ><i class="fa fa-edit danger"></i><a></td>
       </tr>';
    }

}
                
                  
$content .= '</tbody> 
      </table>
      </div>
      </div>
            
    </div>

</div>
</div>';

print $content ;




?>

