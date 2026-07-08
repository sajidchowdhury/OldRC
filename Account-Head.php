<?php 

if($_GET['related_id'] == 'New'){

$related_id = 'new_id';
$account_head = '';
$account_type = '';
$button_text = 'Add++';
$description = '';
$status = '';

}else{

$DATA  = SETUP::ACCOUNT_HEAD_SETUP($_GET['related_id']);

$related_id = $DATA['id'];
$account_head = $DATA['account_head'];
$account_type = $DATA['account_type'];
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

?><div class="row">


                        <div class="col-md-12">
                            
                            <form class="form-horizontal">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Creae Account Head</strong> </h3>
                                  <input type="hidden" id="related_id" name="related_id" value="<?php print $related_id;?>">
                                </div>
                                
                                <div class="panel-body">                                                                        
                                    

                                <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label"> Ledger Head</label>
                                        <div class="col-md-6 col-xs-12">                                                                                            
                                        
                                                                                       
      <select name="parent_id" id="parent_id" class="form-control select"  data-live-search="true"><option value="">Select One</option>
      <?php 
        $qry = $conn_me->prepare("SELECT * FROM `setup_ladger_head` where special_id = 'NO' ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { ?>
         <option value="<?php print $fetch['id'];?>"><?php print $fetch['name'];?></option>';
    
    
      <?php   }?></select>
                             </div>
                                <b id="cHbutton"><a onclick="newLadgerHead('YES');" target="_blink" class="btn btn-danger" > <span class="fa fa-plus-circle"> </span></a></b>

                                    </div>

                <div class="form-group" id="new_ladger_div" style="display:none;">
                    <label class="col-md-3 col-xs-12 control-label">New Ledger Head</label>
                    <div class="col-md-6 col-xs-12">                                            
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                            <input type="text" id="new_ladger_head" name = "new_ladger_head" value="" class="form-control"/>
                        </div>                                            
                        
                    </div>
                </div>

                          
                <div id="section_head" style="display:block;padding-bottom:15px;">
                <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Account Head</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" id="account_head" name = "account_head" value="<?php print $account_head;?>" class="form-control"/>
                                            </div>                                            
                                         
                                        </div>
                                    </div>

                                       
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Type</label>
                                        <div class="col-md-6 col-xs-12">                                                                                            
                                            <select class="form-control select" id="account_type" name = "account_type">
                                                <option <?php if($account_type == 'INCOME'){ ?> selected="selected" <?php }else{ } ?> value="INCOME">INCOME</option>
                                                <option <?php if($account_type == 'EXPENSE'){ ?> selected="selected" <?php }else{ } ?> value="EXPENSE">EXPENSE</option>
                                            </select>
                                        </div>
                                    </div>

                </div>

                                   
                               
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Description</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <textarea class="form-control" id="description" name = "description" rows="5"><?php print $description;?></textarea>
                                        </div>
                                    </div>

                                    
                                 

                              



                                   <div id="parent_ac_div"><input type="hidden" name="parent_id" id="parent_id" value="0"></div>

                    
                                  
                                    
                                   
                                    
                                  
                               
                                  

                                </div>
                                <div class="panel-footer">
                                    <input type="button" class="btn btn-info pull-right" value="Save Data" id="create_ac_head"></div>
                            </div>
                            </form>
                            
                        </div>
                    </div>         