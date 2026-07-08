<?php 

 
include_once('function_query.php'); 
$info_ledger = SETUP::SETUP_LEDGER($_GET['related_id']);


?>
<div class="row">


<div class="col-md-12">
    
    <form class="form-horizontal">
    <div class="panel panel-default">
        <div class="panel-heading">
          <input type="hidden" id="related_id" name="related_id" value="<?php print $_GET['related_id'];?>">
        </div>
        
        <div class="panel-body">                                                                        
            


<div class="form-group" id="new_ladger_div" >
<label class="col-md-3 col-xs-12 control-label"> Ledger Head</label>
<div class="col-md-6 col-xs-12">                                            
<div class="input-group">
    <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
    <input type="text" id="new_ladger_head" name = "new_ladger_head" value="<?php print $info_ledger['fetch']['name'];?>" class="form-control"/>
</div>                                            

</div>
</div>

  
          
       
    
        </div>
        <div class="panel-footer">
            <input type="button" class="btn btn-info pull-right" value="Save Data"onclick="edit_ledger()"></div>
    </div>
    </form>
    
</div>
</div>         
