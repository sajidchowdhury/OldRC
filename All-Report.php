<?php 

?>
<div class="page-content-wrap">
                
<ul class="breadcrumb">
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        
<style>
    .tile .fa {

        font-size: 30px;
    }
</style>
<div class="panel-body">
<a class="label label-default" onclick="SectionWiseReport('All');" style="background-color:danger">All</a>      
<a class="label label-default" onclick="SectionWiseReport('Accounts');" style="background-color:Teal">Accounts</a>                              
<a class="label label-default" onclick="SectionWiseReport('Administration Module');" style="background-color:#28B463">Administration</a>
<a class="label label-default" onclick="SectionWiseReport('Human Resource Management');" style="background-color:#9B59B6">HRM</a>
<a class="label label-default" onclick="SectionWiseReport('Inventory Management');" style="background-color:#2E86C1">Inventory Management</a>
<a class="label label-default" onclick="SectionWiseReport('Production');" style="background-color:Salmon">Production</a>

<a class="label label-default" onclick="SectionWiseReport('Sales & Local Purchase');" style="background-color:#CB4335">Sales & Purchase</a>



                                </div>
<div class="row">
<div class="col-sm-12">   
<input type="text" class="form-control block" value="" id="search_report" placeholder="search report"  onkeyup="serachreport();">
</div>
</div>
                <!-- TILES -->                

<?php  $qry = $conn_me->prepare("SELECT *   FROM `menu_list` where  `type` = 'Report' GROUP BY `section` ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

foreach($fetch_list AS $fetch) {
?>
                      
                      <div class="row" id="reportList">

           <div class="col-sm-12">    

        <?php  $qry2 = $conn_me->prepare("SELECT *   FROM `menu_list` where  `section` = '".$fetch['section']."' AND `type` = 'Report' ORDER BY  `sort`");
        $qry2->execute();
        $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);

        foreach($fetch_list2 AS $fetch2) { ?>
           <div class="col-sm-3">    
                                    
                <a href="<?php print $fetch2['menu_link'];?>" class="tile" style="background-color:<?php print $fetch2['status'];?>">
                <?php print  $fetch2['icon'];?>
                    <p><?php print $fetch2['menu'];?></p>                            
                    <div class="informer informer-danger dir-tr"></div>
                </a>                        
            </div>
            <?php  } ?>
            </div>    </div>  
           <?php  } ?>
                
    


                   </div>   