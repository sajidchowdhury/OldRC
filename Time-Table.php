
<?php 

    $DATA  = SETUP::SETUP_TIMETABLE();
    $related_id=  $DATA['id'];
    $on_duty_time =  $DATA['on_duty_time'];
    $off_duty_time = $DATA['off_duty_time'];
    $late_time =  $DATA['late_time'];    
    $leave_early =  $DATA['leave_early'];    
    
    

    
        $content = '<div class="row animated bounceIn">
        <div class="col-md-12">
        <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > 
            <div class="panel panel-default">
                <div class="panel-body">
                <form id="myform">
                
                <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
              </div>
              <div class="table-responsive">

                <table class="table table-hover table-condensed table-striped table-bordered">
                    <tbody> 
                    

    <tr>
    <th>On Duty Time</th>
    <td><input type="time" class="form-control" value="'.$on_duty_time.'" name="on_duty_time" id="on_duty_time"></td>

    <th>Off Duty Time</th>
    <td><input type="time" class="form-control" value="'.$off_duty_time.'" name="off_duty_time" id="off_duty_time"></td>
    </tr>

    <tr>
    <th>Late Time</th>
    <td><input type="number" class="form-control" value="'.$late_time.'" name="late_time" id="late_time"></td>

    <th>Leave Early Time</th>
    <td><input type="number" class="form-control" value="'.$leave_early.'" name="leave_early" id="leave_early"></td>
    </tr>









                        
                        <tr>
                            
                            <td style="text-align:center;" colspan=2><input type="button" name="save_timetable" id="save_timetable" class="btn btn-primary" value="Save Timetable"></td>
                        </tr>
                  </tbody> 
              </table></div>
            </form>  
                    
            </div>
    
        </div>
    </div>';
    
   

    print     $content;
    ?>