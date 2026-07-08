<?php
include_once('function_query.php');
$conn_me = Database::getInstance();

$get_employeeid = SETUP::ADMIN_SETUP($_SESSION['NEWERP_SESS_MEMBER_ID']);
$set_top_bar =  SETUP::TOP_BAR() ;

$numbers = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
 $current_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


 $result = preg_replace('/\d+/', 'New', $current_url);

if ($result == '/setup/Employee-Personal-Profile/New' || $result == '/InventoryActivity/Finished-Goods-Warehouse-To-Warehouse/New/New' ) {
     $current = 0 ; 
     $parents_id =  0;

 }else{

    $current_menu_id = FIND::getCurrentMenuId($result);

if($current_menu_id['parents'] == 'NOPERMISSION' || $current_menu_id['menu_id'] == 'NOPERMISSION' ){

        echo '<script>window.location.href = "logout.php";</script>';
        die();            
                
    
}else{
    $current = $current_menu_id['menu_id']; 
    $parents_id = $current_menu_id['parents'];
}



}

 

$arr =[];
$query1 = $conn_me->prepare("SELECT A.* FROM `menu_list` A  
JOIN `menu_permission` B  on (A.`id` = B.`menu_id`)  
JOIN `admin` C  on (B.`employee_id` = C.`employee_id`)  

WHERE B.`view_check` = 'Checked' AND C.`id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' ORDER BY A.`sort`  ");
$query1->execute();
    $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
    foreach($fetch_list1 AS $fetch1){
        
    
        $arr[$fetch1['id']]['menu'] = $fetch1['menu'];
        $arr[$fetch1['id']]['parent_id'] = $fetch1['parent_id'];
        $arr[$fetch1['id']]['menu_link'] = $fetch1['menu_link'];
        $arr[$fetch1['id']]['icon'] = $fetch1['icon'];
        $arr[$fetch1['id']]['status'] = $fetch1['status'];
        $arr[$fetch1['id']]['id'] = $fetch1['id'];
        
    }



$html = '';


function buildTreeView($arr, $parent, $level = 0, $prelevel = -1, $current = null, $parents_id = null) {
    
    
    global $html ;

    foreach($arr as $id=>$data){


        
        if($parent == $data['parent_id']){

            if($level > $prelevel){
                if($html == '' ){
                    $html .='<ul style="width:200px;"><li >';
                }else{
                    $html .='<ul><li >';
                }
                
              }
            
              if($level == $prelevel){
                $html .='</li>';
            }

            if( $parent > $prelevel){
                if($data['menu'] == '<span class="xn-text">Dashboard</span>'){
                    $style="";
                }else{
                    $style="padding-left:15px;  max-height: 300px !important; overflow-y: auto !important;";
                }
              
            }else{
                $style="";
            }

            if ($id == $current) {

                if($data['status'] == 'NOPERMISSION' ){
                    echo '<script>window.location.href = "setup/Dashboard/New";</script>';
                    die();            
                
                }else{
                    $html .= '<li style="'.$style.'" class="active block"><a href="' . $data['menu_link'].'">' . $data['icon'].' ' . $data['menu'].'</a>';
                }

            } else {

                $aa = explode(",", $parents_id);
                if(in_array($id, $aa)){
                    $html .= '<li style="'.$style.'" class="xn-openable active block"><a href="' . $data['menu_link'].'">' . $data['icon'].' ' . $data['menu'].'</a>';
                }else{
                    $html .= '<li style="'.$style.'" class="block"><a href="' . $data['menu_link'].'">' . $data['icon'].' ' . $data['menu'].'</a>';
                }
            }

           
           

    
        


            if ($level > $prelevel) {
                $prelevel = $level;
            }
            $level++;
            buildTreeView($arr, $id, $level, $prelevel, $current,$parents_id);
            $level--;
       
               

        }
    }
    if($level == $prelevel){
        $html .='</li></ul>';
     }

return  $html;

}

?>
    <input type="hidden" name="title_tag_text" id="title_tag_text" value="RC">
    <input type="hidden" name="title_company_name" id="title_company_name" value="REMOTE CENTER">
<div class="page-sidebar page-sidebar-fixed scroll hidden-print">
                <!-- START X-NAVIGATION -->
                <ul class="x-navigation">
                    <li class="xn-logo">
                        <a>RC</a>
                        <a href="#" class="x-navigation-control"></a>
                    </li>
                    <li class="xn-profile">
                        <a href="#" class="profile-mini">
                        <img src="upload/employee_photo/<?php print $get_employeeid['photo'];?>" alt="John Doe"/>
                        </a>
                        <div class="profile">
                        <div class="profile-image">
                    <img src="upload/employee_photo/<?php print $get_employeeid['photo'];?>" alt="John Doe"/>
                </div>
                <div class="profile-data">
                    <div class="profile-data-name"><b style="color:red;"><?php print $get_employeeid['employee_code_with_prefix'] . '</b> ' . $get_employeeid['hr_name'];?></div>
                    <div class="profile-data-title"><?php print $get_employeeid['designation'];?></div>
                    <div class="profile-data-title"><?php print "Branch ::: $get_employeeid[brunch_name]";?></div>

                </div>
                <div class="profile-controls">
                    <a href="setup/Employee-Personal-Profile/<?php print  $_SESSION['NEWERP_SESS_MEMBER_ID'];?>" class="profile-control-left"><span class="fa fa-info"></span></a>
                    <a  class="profile-control-right"><span class="fa fa-envelope"></span></a>
                </div>
                        </div>                                                                        
                    </li>
                                      
                     <?php  print buildTreeView($arr,0,0,-1,$current,$parents_id);?>

                    
</ul>
                          
    

                <!-- END X-NAVIGATION -->
            </div>
            <!-- END PAGE SIDEBAR -->



