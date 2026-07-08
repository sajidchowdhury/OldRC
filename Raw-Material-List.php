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
           
            <table class="table datatable">
            <thead>
            <th>Sl</th>
            <th>Product Name</th>
            <th>Action</th>
            </thead>
                <tbody>'; 
    $sl =1;
    $qry = $conn_me->prepare("SELECT * FROM `setup_raw_material`  ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    if ($qry->rowCount() > 0)
    {
        foreach($fetch_list AS $fetch) {
    
    
    
            $content .= '<tr>
            <th>'.$sl++.'</th>
            <th>'.$fetch['material_name'].'</th>
            <td><a href="Recipe/Raw-Material-Setup/'.$fetch['id'].'/New" ><i class="fa fa-edit danger"></i><a></td>
           </tr>';
        }
    
    }
                    
                      
    $content .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';


print $content; ?>

