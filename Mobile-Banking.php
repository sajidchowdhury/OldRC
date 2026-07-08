<?php 

if($_GET['related_id'] == 'New'){


$related_id = 'new_id';
$mobile_bank_name = '';
$mobile_number = '';
$button_text = 'Add++';
$description = '';
$status = '';
}else{

$DATA  = SETUP::BANK_MOBILE_BANKING($_GET['related_id']);

$related_id = $DATA['id'];
$mobile_bank_name = $DATA['mobile_bank_name'];
$mobile_number = $DATA['mobile_number'];
$description = $DATA['description'];
$button_text = 'Update';
$status = $DATA['status'];

?>
<script>
    window.onload = function() {
        calculate_batch();
};
</script>
<?php 
}
$permission_management = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],$button_text,'create_mobile_bank','');

?><div class="row">


                        <div class="col-md-12">
                            
                            <form class="form-horizontal">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Mobile Banking Setup</strong></h3>
                                  <input type="hidden" id="related_id" name="related_id" value="<?php print $related_id;?>">
                                </div>
                                
                                <div class="panel-body">                                                                        
                                    
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Name </label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" id="mobile_bank_name" name = "mobile_bank_name" value="<?php print $mobile_bank_name;?>" class="form-control"/>
                                            </div>                                            
                                         
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Mobile Number</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" id="mobile_number" name = "mobile_number"  value="<?php print $mobile_number;?>" class="form-control"/>
                                            </div>                                            
                                         
                                        </div>
                                    </div>

                                 
                    
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Description</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <textarea class="form-control" id="description" name = "description" rows="5"><?php print $description;?></textarea>
                                        </div>
                                    </div>
                                    
                                   
                                    
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Status</label>
                                        <div class="col-md-6 col-xs-12">                                                                                            
                                            <select class="form-control select" id="status" name = "status">
                                                <option <?php if($status == 'Active'){ ?> selected="selected" <?php }else{ } ?> value="Active">Active</option>
                                                <option <?php if($status == 'Deactive'){ ?> selected="selected" <?php }else{ } ?> value="Deactive">Deactive</option>
                                               
                                            </select>
                                        </div>
                                    </div>
                               
                                  

                                </div>
                                <div class="panel-footer">
                                <a href="Account/Mobile-Banking/New" class="btn btn-success">Add New</a>

                                   <?php print  $permission_management['save_update_buton'];?>
                                </div>
                            </div>
                            </form>
                            
                        </div>
                    </div>         


                    <?php 

                    
    $content = '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           
        <table class="table datatable table-hover table-condensed table-striped table-bordered">
            <thead>
            <th>Sl</th>
            <th>Bank Details</th>
            <th>Status</th>
            <th>Action</th>
            </thead>
                <tbody>'; 
    $sl =1;
    $qry = $conn_me->prepare("SELECT * FROM `setup_mobile_banking`  ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    if ($qry->rowCount() > 0)
    {
        foreach($fetch_list AS $fetch) {
    
    
    
            $content .= '<tr>
            <th>'.$sl++.'</th>
            <th>Bank Name: '.$fetch['mobile_bank_name'].' <br>Number: '.$fetch['mobile_number'].'</th>
            <th>'.$fetch['status'].'</th>

            <td><a href="Account/Mobile-Banking/'.$fetch['id'].'" ><i class="fa fa-edit danger"></i><a></td>
           </tr>';
        }
    
    }
                    
                      
    $content .= '</tbody>  
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';
    print $content ;
    ?>