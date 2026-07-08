<?php 


    $content = '<ul class="breadcrumb">
    <li><a>HRM </a></li><li><a>Settings </a></li>        
    <li class="active">'.$_GET['page_identity'].'</li>
</ul>';

if($_GET['related_id'] == 'New'){
    $permission_management = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],'Save Data','save_employee_data','');

    $employee_code = SETUP::SETUP_CODE('setup_employee');

    $related_id = 'new_id';
    $employee_code = $employee_code['code'];

    $name =  '';
    $joining_department =  '';
    $present_department =  '';
    $present_section = '';
    $joining_section =  '';
    $designation =  '';
    $joining_designation =  '';
    $fa_name = '';
    $mo_name =  '';
    $birth_date = date('d-m-Y');
    $mob_no =  '';
    $nationality = '';
    $division_id =  '';
    $district_id =  '';
    $upazila_id =  '';
    $union_id =  '';
    $village =  '';
    $po_office =  '';
    $house =  '';
    $nid =  '';
    $religion =  '';
    $email = '';
    $edu_qul =  '';

    $gender  =  '';
    $matrial_status  =  '';
    $referrer  =  '';
    $supervisor  =  '';
    $nominee_information  =  '';
    $bank_account  =  '';


    $previous_company = '';
    $joining_salary =  '';
    $present_salary =  '';
    $house_rent =  0.00;
    $medical =  0.00;
    $mob_bill =  '';
    $ta =  0.00;
    $provident_fund = 0.00 ;
    $da =  0.00;
    $basic =  0.00;
    $over_time_bill =  0.00;
    $other_allowance =  '';
    $join_d = date('d-m-Y');
    $status = '';
    }else{
        $permission_management = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],'Update Data','save_employee_data','');

    $DATA  = SETUP::SETUP_EMPLOYEEY($_GET['related_id']);

    $related_id = $DATA['id'];
    $name = $DATA['name'];
    $joining_department = $DATA['joining_department'];
    $employee_code = $DATA['employee_code'];
    $present_department = $DATA['present_department'];
    $present_section = $DATA['present_section'];
    $joining_section = $DATA['joining_section'];
    $designation = $DATA['designation'];
    $joining_designation = $DATA['joining_designation'];
    $fa_name = $DATA['fa_name'];
    $mo_name = $DATA['mo_name'];
    $birth_date = $DATA['birth_date'];
    $mob_no = $DATA['mob_no'];
    $nationality = $DATA['nationality'];
    $division_id = $DATA['division_id'];
    $district_id = $DATA['district_id'];
    $upazila_id = $DATA['upazila_id'];
    $union_id = $DATA['union_id'];
    $village = $DATA['village'];
    $po_office = $DATA['po_office'];
    $house = $DATA['house'];
    $nid = $DATA['nid'];
    $religion = $DATA['religion'];
    $email = $DATA['email'];
    $edu_qul = $DATA['edu_qul'];
    $previous_company = $DATA['previous_company'];
    $joining_salary = $DATA['joining_salary'];
    $present_salary = $DATA['present_salary'];
    $house_rent = $DATA['house_rent'];
    $medical = $DATA['medical'];
    $mob_bill = $DATA['mob_bill'];
    $ta =$DATA['ta'];
    $provident_fund = $DATA['provident_fund'];
    $da = $DATA['da'];

    $gender  = $DATA['gender'];
    $matrial_status  =  $DATA['matrial_status'];
    $referrer  =  $DATA['referrer'];
    $supervisor  =  $DATA['supervisor'];
    $nominee_information  = $DATA['nominee_information'];
    $bank_account  =  $DATA['bank_account'];


    $basic = $DATA['basic'];
    $over_time_bill = $DATA['over_time_bill'];
    $other_allowance = $DATA['other_allowance'];
    $join_d = $DATA['join_d'];
    $status = $DATA['status'];
  
}


$content .= '<div class="row animated bounceIn"><div class="row">
<div class="col-md-12">

<form class="form-horizontal">
                                
    <div class="panel panel-default tabs">                            
        <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="#tab-first" role="tab" data-toggle="tab">BASIC INFO</a></li>
            <li><a href="#tab-second" role="tab" data-toggle="tab">Address</a></li>
            <li><a href="#tab-third" role="tab" data-toggle="tab">Education & Experience</a></li>
            <li><a href="#tab-fourth" role="tab" data-toggle="tab">Salary</a></li>

        </ul>
        <div class="panel-body tab-content">
            <div class="tab-pane active" id="tab-first">
                
<table class="table table-hover table-condensed">
<tbody>';

$content .= '<input type="hidden" id="related_id" name="related_id" value="'.$related_id.'">';

$content .= '<tr>
<th class="control-label">ID NO </th>
<td><input READONLY required type="text" class="form-control text-danger" value="'.$employee_code.'" id="show_code"  name="show_code" 
placeholder="Last Code " autofocus></td>

<th class="control-label">Name <b class="text-danger">*</b></th>
<td><input required type="text" class="form-control" value="'.$name.'" id="name" name="name"></td>
</tr>';

$content .= ' <tr>
<th class="control-label">Joining Department <b class="text-danger">*</b></th>
<td><select class="select form-control" required id="joining_department" name="joining_department" data-rel="chosen">
    <option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `setup_department`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list AS $fetch) { 
$content .=  '<option ';
if($joining_department == $fetch['id']){
    $content .=  'selected="selected"';
}else{

}
$content .= ' value="'.$fetch['id'].'">'.$fetch['department'].'</option>'; 
} 
$content .= '</select>
</td>

<th class="control-label">Date Of Joining <b class="text-danger">*</b></th>
<td><input required type="text" class="date form-control" value="'.$join_d.'" name= "join_d" id="join_d"></td>
</tr>';


$content .= '<tr>
<th class="control-label">Present Department <b class="text-danger">*</b></th>
<td><select required class="select form-control" id="present_department" name="present_department" data-rel="chosen">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `setup_department`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list AS $fetch) { 

$content .=  '<option ';
if($present_department == $fetch['id']){
    $content .=  'selected="selected"';
}else{

}
$content .=  ' value="'.$fetch['id'].'">'.$fetch['department'].'</option>'; 
} 
$content .= '</select>
</td>



<th class="control-label">Tel/Cell <b class="text-danger">*</b></th>
<td><input required type="text" class="form-control" id= "mob_no"  value="'.$mob_no.'" name="mob_no"></td>																			
</tr>';


$content .= '<tr>
<th class="control-label">Joining Section </th>
<td><select required class="select form-control" id="joining_section" name="joining_section" data-rel="chosen">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `setup_section`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list AS $fetch) { 
$content .=  '<option ';
if($joining_section == $fetch['id']){
    $content .=  'selected="selected"';
}else{

}
$content .=  ' value="'.$fetch['id'].'">'.$fetch['section'].'</option>'; 
} 
$content .= '</select>
</td>

<th class="control-label">Present Section </th>
<td><select required class="select form-control" id="present_section" name="present_section" data-rel="chosen">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `setup_section`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list AS $fetch) { 
$content .=  '<option ';
if($present_section == $fetch['id']){
    $content .=  'selected="selected"';
}else{

}

$content .=  ' value="'.$fetch['id'].'">'.$fetch['section'].'</option>'; 
} 
$content .= '</select>
</td>
</tr>';


$content .= '<tr>
    <th class="control-label">Joining Designation <b class="text-danger">*</b></th>
    <td><select required class="select form-control" id="joining_designation" name="joining_designation" data-rel="chosen">
    <option value="">Select One</option>';
    $qry = $conn_me->prepare("SELECT * FROM `setup_designation`  ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
     foreach($fetch_list AS $fetch) { 
        $content .=  '<option ';
        if($joining_designation == $fetch['id']){
            $content .=  'selected="selected"';
        }else{

        }

        $content .=  ' value="'.$fetch['id'].'">'.$fetch['designation'].'</option>'; 
     } 
     $content .= '</select>
    </td>
    

    <th class="control-label">Present Designation <b class="text-danger">*</b></th>
    <td><select required class="select form-control" id="designation" name="designation" data-rel="chosen" >
            
    <option value="">Select One</option>';
    $qry = $conn_me->prepare("SELECT * FROM `setup_designation`  ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
     foreach($fetch_list AS $fetch) { 
        $content .=  '<option ';
        if($designation == $fetch['id']){
            $content .=  'selected="selected"';
        }else{

        }

        $content .=  ' value="'.$fetch['id'].'">'.$fetch['designation'].'</option>'; 
     } 
     $content .= '</select>
    </td>
</tr>';


$content .= '<tr>
    <th class="control-label">Gender </th>
    <td>
    <select id="gender" class="form-control">';
    $content .='<option '; if($gender == 'MALE' ) { $content .=' selected= "selected" '; } else{ } $content .=' value="MALE" >MALE</option> ';
    $content .=' <option '; if(  $gender == 'FEMALE' ) { $content .=' selected= "selected" '; } else{ }   $content .='value="FEMALE">FEMALE</option>  ';
    $content .='</select>
    </td>


    <th class="control-label">Marital Status </th>
    <td>
    <select id="matrial_status" class="form-control">';
    $content .='<option '; if($matrial_status == 'Married' ) { $content .=' selected= "selected" '; } else{ } $content .=' value="Married" >Married</option> ';
    $content .=' <option '; if(  $matrial_status == 'Unmarried' ) { $content .=' selected= "selected" '; } else{ }   $content .='value="Unmarried">Unmarried</option>  ';

    $content .='</select>
    </td>
</tr>';


$content .= '<tr>
    <th class="control-label">Fathers Name </th>
    <td><input required type="text" class="form-control" value="'.$fa_name.'" id="fa_name" name="fa_name"></td>


    <th class="control-label">Mothers Name </th>
    <td><input required type="text" class="form-control" value="'.$mo_name.'" id="mo_name" name="mo_name"></td>
</tr>';


$content .= '<tr>
    <th class="control-label">Birth Day </th>
    <td><input required type="text" class="date form-control" value="'.$birth_date.'" id="birth_date" name="birth_date"></td>


    <th class="control-label">Referrer </th>
    <td><input required type="text" class="form-control" value="'.$referrer.'" id="referrer" name="referrer"></td>
</tr>';

$content .= '<tr>
    <th class="control-label">Supervisor </th>
    <td><input required type="text" class=" form-control" value="'.$supervisor.'" id="supervisor" name="supervisor"></td>


    <th class="control-label">Nominee Information </th>
    <td><input required type="text" class="form-control" value="'.$nominee_information.'" id="nominee_information" name="nominee_information"></td>
</tr>';

$content .= '<tr>
    <th class="control-label">Bank Account </th>
    <td><input required type="text" class=" form-control" value="'.$bank_account.'" id="bank_account" name="bank_account"></td>


    <th class="control-label"> </th>
    <td></td>
</tr>';


$content .= '</tbody></table>
                
            </div>';
            $content .='<div class="tab-pane" id="tab-second">';
                
            $content .= '<table class="table table-hover table-condensed"><tbody>';
            $content .= '<tr>
            <th class="control-label"> Nationality </th>
            <td class="control-label"><input required type="text" class="form-control" value="'.$nationality.'" name="nationality" id="nationality" ></td>

            
            <th class="control-label">Divisions </th>
                <td>
                <select id="division_id" name="division_id" required class="select form-control" class="form-control" data-live-search="true"
             onchange="find_related_data(\'districts\',\'Districts\',\'division_id\',this.value);" name="dist" id="dist" data-rel="chosen">
             <option value="">Select One</option>';
             $qry = $conn_me->prepare("SELECT * FROM `divisions`  ORDER BY `id` ASC");
             $qry->execute();
             $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
              foreach($fetch_list AS $fetch) { 
                $content .=  '<option ';
                if($division_id == $fetch['id']){
                    $content .=  'selected="selected"';
                }else{

                }

                 $content .=  ' value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
              } 
              $content .= '</select>
                </td>
        </tr>';


        $content .= '<tr>
        <th id="Load_districts_level" class="control-label">Districts</th>';

        $content .= '<td id="Load_districts_value">';
        
        $content .= '<select id= "district_id" required class="select form-control" class="form-control" data-live-search="true"  data-rel="chosen">
        <option value="">Select One</option>';

        $qry = $conn_me->prepare("SELECT * FROM `districts`  ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
         foreach($fetch_list AS $fetch) { 
           $content .=  '<option ';
           if($district_id == $fetch['id']){
               $content .=  'selected="selected"';
           }else{

           }

            $content .=  ' value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
         } 


          $content .= '</select>';
       $content .='</td>	
    
        <th id="Load_upazilas_level" class="control-label">Upazila/Thana</th>';

        $content .= '<td id="Load_upazilas_value">';
        
        $content .= '<select id= "upazilla_id" required class="select form-control" class="form-control" data-live-search="true"  data-rel="chosen">
        <option value="">Select One</option>';

        $qry = $conn_me->prepare("SELECT * FROM `upazilas`  ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
         foreach($fetch_list AS $fetch) { 
           $content .=  '<option ';
           if($upazila_id == $fetch['id']){
               $content .=  'selected="selected"';
           }else{

           }

            $content .=  ' value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
         } 




          $content .= '</select>';
       $content .='</td>	
       

     
        </tr>';

        $content .= '<tr>

        <th id="Load_unions_level" class="control-label">Union</th>';

        $content .= '<td id="Load_unions_value">';
        
        $content .= '<select id= "union_id" required class="select form-control" class="form-control" data-live-search="true"  data-rel="chosen">
        <option value="">Select One</option>';
        $qry = $conn_me->prepare("SELECT * FROM `unions`  ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
         foreach($fetch_list AS $fetch) { 
           $content .=  '<option ';
           if($union_id == $fetch['id']){
               $content .=  'selected="selected"';
           }else{

           }

            $content .=  ' value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
         } 
      
          $content .= '</select>';
       $content .='</td>

       
    

        <th class="control-label" class="control-label">Village </th>
        <td><input required type="text" class="form-control" value="'.$village.'" name="village" id="village"></td>
        </tr>';

        $content .= '<tr>
        
        <th class="control-label" class="control-label">Post Office </th>
        <td><input required type="text" class="form-control" value="'.$po_office.'" name="po_office"  id="po_office"></td>
        <th class="control-label">House/Holdings </th>

        <td><input required type="text" class="form-control" value="'.$house.'" name="house" id="house"></td>
     </tr>';

     $content .= '<tr>
     <th class="control-label">NID <b class="text-danger">*</b></th>
     <td><input type="text" class="form-control" value="'.$nid.'" name="nid" id="nid"></td>
     
     
     <th class="control-label">Religion </th>
     <td><input required type="text" class="form-control" value="'.$religion.'" name="religion" id="religion"  ></td>
 </tr></tbody></table>
            </div> ';  
            
            
            $content .= '<div class="tab-pane" id="tab-third">';
              
            $content .= '<table class="table table-hover table-condensed"><tbody>';
            
            $content .= '<tr>
            <th class="control-label">Email </th>
            <td><input type="text" class="form-control" value="'.$email.'" id="email" placeholder="example@enafood.com"></td>
            
            
            <th class="control-label"></th>
            <td></td>
        </tr>';
        
       
        $content .= '<tr>
            <th class="control-label"> Academic Qualifications </th>
            <td><input required type="text" class="form-control" value="'.$edu_qul.'" id="edu_qul" 
            placeholder="">
            
            
            </td>
        
        
            <th class="control-label"> Previous Company Name </th>
            <td><input type="text" class="form-control" value="'.$previous_company.'" id="previous_company"></td>
        </tr>
        </tbody> </table>
                

            </div>';

            $content .= '<div class="tab-pane" id="tab-fourth">';

            $content .= '<table class="table table-hover table-condensed"><tbody>';

            $content .= '<tr>
            <th class="control-label">Joining Gross Salary <b class="text-danger">*</b></th>
            <td><div class="input-prepend input-append"><input class="form-control" id="joining_salary"   type="number" value="'.$joining_salary.'"></div></td>
        
     
        
            <th class="control-label">Present Gross Salary <b class="text-danger">*</b></th>
            <td><div class="input-prepend input-append"><input id="present_salary"   class="form-control"
            type="number" onkeyup="calTada();"  value="'.$present_salary.'"></div></td>
        </tr>';



            $content .= '<tr>
            <th class="control-label">Medical <b class="text-danger">*</b></th>
            <td><div class="input-prepend input-append"><input id="medical" READONLY class="form-control text-danger"  type="number"  value="'.$medical.'"></div></td>
        
        
            <th class="control-label">House Rent <b class="text-danger">*</b></th>
            <td><div class="input-prepend input-append"><input id="house_rent" READONLY  type="number" class="form-control text-danger"  value="'.$house_rent.'"></div></td>
        </tr>';


            $content .= '
            
            <tr>
                <th class="control-label">Basic<b class="text-danger">*</b></th>
                <td><div class="input-prepend input-append"><input  READONLY class="form-control text-danger" type="number"  id="basic" value="'.$basic.'"></div></td>	

                <th class="control-label" > Over Time Bill </th>
                <td><div class="input-prepend input-append"><input id="over_time_bill" READONLY  type="number" class="form-control text-danger"  value="'.$over_time_bill.'"></div></td>
                
            </tr>';

          
$content .= '<tr>
                
            
            
            <th class="control-label">Transport Allowance</th>
            <td><input type="text" class="form-control" value="'.$ta.'" id="ta" >
            </td>


            <th class="control-label"> Others</th>
            <td><input type="text" class="form-control" value="'.$da.'" id="da" ></td>


        </tr>		
        
       ';

       $content .= '<tr>
                
            
            
            <th class="control-label">Provident Fund (%)</th>
            <td><input type="text" class="form-control" value="'.$provident_fund.'" id="provident_fund" >
            </td>


            <th class="control-label"> </th>
            <td></td>


        </tr>		
        
       ';


       

            $content .= '</tbody> </table>


            </div>
        </div>
        <div class="panel-footer"> ';                                                                       
        $content .= $permission_management['save_update_buton'];   
        $content .=' </div>
    </div>                                



</div>
</div></div>   </form>';




print $content ;

?>