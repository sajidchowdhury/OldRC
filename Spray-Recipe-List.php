<?php 





$content = '<ul class="breadcrumb">
<li><a href="#">Production </a></li>           
<li><a href="#">Settings </a></li>                             
<li class="active">'.$_GET['page_identity'].'</li>
</ul>';
$content .= '<div class="row">
<div class="col-md-12">

    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
        <div class="table-responsive">

        <table class="table datatable">
        <thead>
        <th>ID</th>
        <th>Product</th>
       
        <th>Action</th>
        </thead>
            <tbody>'; 
$sl =1;
$qry = $conn_me->prepare("SELECT * FROM `receip_spray` GROUP BY `spray_material_id`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
if ($qry->rowCount() > 0)
{
    foreach($fetch_list AS $fetch) {

        $CART_DATA = SETUP::SETUP_SPRAY_RECIPE($fetch['id']);

        $content .= '<tr>
        <td>'.$sl++.'</td>
        <td>'.$CART_DATA['product_name'].'</td>
    
        <td><a href="Recipe/Spray-Recipe-Setup/New/'.$fetch['spray_material_id'].'"><i class="fa fa-edit danger"></i><a></td>
       </tr>';
    }

}
                
                  
$content .= '</tbody> 
      </table>
      ,/div>
      </div>
            
    </div>

</div>
</div>';


print $content;?>