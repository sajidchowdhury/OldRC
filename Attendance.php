<?php


    $related_id = 'new_id';
    $attendance_date = date('d-m-Y');
   
?>

<ul class="breadcrumb">
    <li><a href="#">HRM </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        
<div class="row">
<div class="col-md-12">
<div id="load_msg"></div>
</div></div>
<div class="row">
   <div class="col-md-12">

<input type="hidden" name="related_id" id="related_id" value="<?php print $related_id;?>" >



<form class="form-horizontal panel panel-default" action="" method="post" name="upload_excel" enctype="multipart/form-data">
                    <fieldset>
                        <!-- Form Name -->
                        <!-- File Button -->

                       


                        <div class="form-group">
                            <label class="col-md-4 control-label" for="filebutton">Select File</label>
                            <div class="col-md-4">
                                <input type="file" name="fileToUpload" id="fileToUpload" class="input-large">
                            </div>
                        </div>
                        <!-- Button -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="singlebutton">Import data</label>
                            <div class="col-md-4">
                                <button type="button" onclick = "importAttandanceCsv()" id="submit" name="Import" class="button-loading" data-loading-text="Loading..."><img src='img/icons/mc.png' width="50px"/> Import From Machine</button>
                            </div>
                        </div>
                
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="filebutton">Attendance Date</label>
                            <div class="col-md-4">
                            <input type="text"  value="<?php print $attendance_date ;?>" id="attendance_date" class="date form-control text-danger" >
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="col-md-4 control-label" for="filebutton"></label>
                            <div class="col-md-4">
                            <input  type="button" value="SEARCH DATA"  id="take_att" onClick="getEmloyeeForattanance()" class="btn btn-danger block" >
                            </div>
                        </div>


                    </fieldset>
                </form>

</div>
</div>


<div class="row" style="background-color:white">
<div class="col-md-12">

        <div class="panel-body "  id="employee_data">

       
      </div>
            

</div>
</div>