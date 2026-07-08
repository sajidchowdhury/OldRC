<?php 

 
include_once('function_query.php'); 
$info_ac = SETUP::ACCOUNT_HEAD_SETUP($_GET['related_id']);


?>
<div class="row">


<div class="col-md-12">
    
    <form class="form-horizontal">
    <div class="panel panel-default">
        <div class="panel-heading">
          <input type="hidden" id="related_id" name="related_id" value="<?php print $_GET['related_id'];?>">
        </div>
        
        <div class="panel-body">                                                                        
        
        <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label"> Ledger Head</label>
                                        <div class="col-md-6 col-xs-12">                                                                                            
                                        
                                                                                       
      <select name="new_ledger_id" id="new_ledger_id" class="form-control select"  data-live-search="true"><option value="">Select One</option>
      <?php 
        $qry = $conn_me->prepare("SELECT * FROM `setup_ladger_head` where special_id = 'NO' ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { ?>
         <option <?php if( $info_ac['ledger_id'] == $fetch['id'] ){ ?> selected="selected" <?php } else{} ?> value="<?php print $fetch['id'];?>"><?php print $fetch['name'];?></option>';
    
    
      <?php   }?></select>
                             </div>

                                    </div>


                                           
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Type</label>
                                        <div class="col-md-6 col-xs-12">                                                                                            
                                            <select class="form-control select" id="new_account_type" name = "new_account_type">
                                                <option <?php if($info_ac['account_type'] == 'INCOME'){ ?> selected="selected" <?php }else{ } ?> value="INCOME">INCOME</option>
                                                <option <?php if($info_ac['account_type'] == 'EXPENSE'){ ?> selected="selected" <?php }else{ } ?> value="EXPENSE">EXPENSE</option>
                                            </select>
                                        </div>
                                    </div>



<div class="form-group" >
<label class="col-md-3 col-xs-12 control-label"> Account Head</label>
<div class="col-md-6 col-xs-12">                                            
<div class="input-group">
    <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
    <input type="text" id="new_account_head" name = "new_account_head" value="<?php print $info_ac['account_head'];?>" class="form-control"/>
</div>                                            

</div>
</div>

  
          
    
        </div>
        <div class="panel-footer">
            <input type="button" class="btn btn-info pull-right" value="Save Data"onclick="edit_ac_head()"></div>
    </div>
    </form>
    
</div>
</div>         
