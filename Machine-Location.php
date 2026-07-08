<?php 

$DATA  = SETUP::MACHINE_LOCATION();

$file_path = $DATA['file_path'];

?>
<div class="row animated bounceIn">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-body">
            <form id="myform">
            
            <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
          </div>

            <table class="table table-hover table-condensed table-striped table-bordered">
                <tbody> <tr>
                        <th>Machine Location   </th>
                        <td><input type="text" class="form-control" value="<?php print $file_path;?>" name="file_path" id="file_path"></td>
                        
                        
                    </tr>
                    
                    <tr>
                        
                        <td style="text-align:center;" colspan=2><input type="button" name="save_file_path" id="save_file_path" class="btn btn-primary" value="Save Location"></td>
                    </tr>
              </tbody> 
          </table>
        </form>  
                
        </div>

    </div>
</div>

