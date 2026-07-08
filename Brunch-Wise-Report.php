<?php 


?>

<ul class="breadcrumb">
    <li><a href="#">Report </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
<input type="hidden" id="branch_id" value="All" >


<div class="container">
  
<div class="row justify-content-center align-items-center g-2">
    <div class="col-md-12">

    <table class="table table-hover table-condensed table-striped table-bordered">
        <tr>
            <td>
            <select id="brunch_id" name="brunch_id"   class="form-control select" >
            <option value="">Select One</option>

            <?php 
            $qry = $conn_me->prepare("SELECT * FROM `setup_brunch`  where status = 'Active' ");
            $qry->execute();
            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
            foreach($fetch_list as  $value) { ?>
            <option   value="<?php print $value['id'];?>"> <?php print $value['brunch'];?></option>';

            <?php   } ?>



</select>
            </td>
        </tr>
       

        <tr>
        <td colspan="2"><input type="text" class="date form-control" value="<?php print date('d-m-Y');?>" id="date_to"></td>
        </tr>
        <tr>
            <td colspan="2"> <input type="button" onclick="BrunchWiseDue();" class="btn btn-info block" value="SEARCH DATA" id="search_data" name="search_data" ></td>
        </tr>
    </table>
    </div>
   
</div>
</div>

<div class="row">
    <div class="col-12 form-horizontal" id="laod_report">

 </div>
   
</div>