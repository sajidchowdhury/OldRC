<?php 


if($_GET['related_id'] == 'New'){
    $permission_management = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],'Save Data','create_raw_category','');

    $category_name = '';
    $related_id = 'new_id';
    
    }else{
    $permission_management = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],'Update Data','create_raw_category','');

    $DATA  = SETUP::SETUP_RAW_MATERIAL_CATEGORY($_GET['related_id']);
    
    $category_name = $DATA['category'];
    $related_id =  $DATA['id'];
    
    
    }
        $content = '<div class="row animated bounceIn">
        <div class="col-md-12">
    
            <div class="panel panel-default">
                <div class="panel-body">
                <form id="myform">
                
                <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
              </div>
    
                <table class="table table-hover table-condensed table-striped table-bordered">
                    <tbody> <tr>
                            <th>Category  <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > </th>
                            <td><input type="text" class="form-control" value="'.$category_name.'" name="category_name" id="category_name"></td>
                            
                            
                        </tr>
                        
                        <tr>
                            
                            <td style="text-align:center;" colspan=2>';
                                        $content .= $permission_management['save_update_buton'];   
                                        $content .= '</td>
                        </tr>
                  </tbody> 
              </table>
            </form>  
                    
            </div>
    
        </div>
    </div>';
    
    $content .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           
            <table class="table table-hover table-condensed table-striped table-bordered">
            <thead>
            <th>Sl</th>
            <th>Category</th>
            <th>Action</th>
            </thead>
                <tbody>'; 
    $sl =1;
    $qry = $conn_me->prepare("SELECT * FROM `setup_raw_material_category`  ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    if ($qry->rowCount() > 0)
    {
        foreach($fetch_list AS $fetch) {
    
    
    
            $content .= '<tr>
            <th>'.$sl++.'</th>
            <th>'.$fetch['category'].'</th>
            <td><a href="Recipe/Raw-Material-Category-Setup/'.$fetch['id'].'/New" ><i class="fa fa-edit danger"></i><a></td>
           </tr>';
        }
    
    }
                    
                      
    $content .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';


    print $content;?>