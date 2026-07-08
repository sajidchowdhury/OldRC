<?php 


      

    if($_GET['related_id'] == 'New'){

$supplier_name = '';
$address = '';
$mobile =  '';
$owner_name =  '';
$email =  '';
$related_id = 'new_id';
$description =  '';

}else{

$DATA  = SETUP::SETUP_SUPPLIER($_GET['related_id']);

$supplier_name = $DATA['supplier_name'];
$address =  $DATA['address'];
$mobile =  $DATA['mobile'];
$owner_name =  $DATA['owner_name'];
$email =  $DATA['email'];
$related_id = $DATA['id'];
$description =  $DATA['description'];


}
    $content = '<div class="row animated bounceIn">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-body">
            <form id="myform">
            
            <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
          </div>
          <div class="table-responsive">

            <table class="table table-hover table-condensed table-striped table-bordered">
                <tbody> <tr>
                        <th>Supplier Name  <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > </th>
                        <td><input type="text" class="form-control" value="'.$supplier_name.'" name="supplier_name" id="supplier_name"></td>
                        
                        
                    </tr>

                    <tr>
                        <th>Address</th>
                        <td><input type="text" class="form-control" value="'.$address.'" name="address" id="address"></td>
                        
                        
                    </tr>

                    <tr>
                    <th>Mobile </th>
                    <td><input type="text"  class="form-control" value="'.$mobile.'" name="mobile" id="mobile"></td>
                    
                    
                </tr>

                <tr>
                <th>Owner Name  </th>
                <td><input type="text" class="form-control" value="'.$owner_name.'" name="owner_name" id="owner_name"></td>
                
                
            </tr>

            <tr>
            <th>Email</th>
            <td><input type="text" class="form-control" value="'.$email.'" name="email" id="email"></td>
            
            
        </tr>

        
        <tr>
        <th>Description</th>
        <td><input type="text" class="form-control" value="'.$description.'" name="description" id="description"></td>
        
        
    </tr>



                    
                    <tr>
                        
                        <td style="text-align:center;" colspan=2><input type="button" name="save_supplier" id="save_supplier" class="btn btn-primary" value="Save Supplier"></td>
                    </tr>
              </tbody> 
          </table>
          </div>
        </form>  
                
        </div>

    </div>
</div>';

$content .= '<div class="row">
<div class="col-md-12">

    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
        <div class="table-responsive">

        <table class="table table-hover table-condensed table-striped table-bordered">
        <thead>
        <th>Sl</th>
        <th>Name</th>
        <th>Contact Number</th>
        <th>Address</th>
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
        <th>'.$fetch['supplier_name'].'</th>
        <th>'.$fetch['mobile'].'</th>
        <th>'.$fetch['address'].'</th>
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