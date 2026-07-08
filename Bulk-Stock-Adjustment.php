<?php


    $related_id = 'new_id';
    $attendance_date = date('d-m-Y');
   
?>

<ul class="breadcrumb">
    <li><a href="#">ADMINSTRATION </a></li>           
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>
        

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
                     
                

                       

        <div class="form-group">
                            <label class="col-md-4 control-label" for="filebutton">Warehouse Name</label>
                            <div class="col-md-4">

                            <select class="form-control select" id="warehouse_id" name = "warehouse_id"  data-live-search="true">
    <option value="">Select One</option>
<?php 
        $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse` ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {  ?>

        <option  value="<?php print $fetch['id'];?>"><?php print $fetch['name']; ?></option>

       <?php  } ?>
 </select>

</div>
                        </div>



                        <div class="form-group">
                            <label class="col-md-4 control-label" for="filebutton">Adjustment Date</label>
                            <div class="col-md-4">
                            <input type="text"  value="" id="adjustment_date" class="date form-control text-danger" >
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="col-md-4 control-label" for="filebutton"></label>
                            <div class="col-md-4">
                            <input  type="button" value="INSERT DATA"  id="take_att" onClick="stock_adjustment_in_bulk()" class="btn btn-danger block" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" ></label>
                            <div class="col-md-4">

                            <p style="color:red">
                                <ul>
                                    <li>CSV file data formet: <br> <b style="color:red;">[  Product Code || Soft Data || Physical Data || Gap (Physical Data - Soft Data) ] </b></li>
                                    <li>Dont give column any name </li>

                                </ul>
                           </p>
                                                </div>
                        </div>


                     
                    </fieldset>
                </form>

</div>
</div>

<div id="load_msg"></div>

<div class="row" style="background-color:white">
<div class="col-md-12">

<div class="row">
                    <div class="col-md-12">
                        
                                                        
                          <div class="table-responsive">
                                    <table class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Adjustment Date</th>
                                                <th>Data Insert Date</th>
                                                <th>Warehouse Name</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $sl =  1;
                                        $count_invoice = 0;
        $query = $conn_me->prepare("SELECT * , date_format(`date`, '%d-%m-%Y') AS `invoice_date`  , date_format(`data_insert_date`, '%d-%m-%Y') AS `datainsertdate`  FROM `balance_product` where note = 'STOCK ADJUSTMENT'  GROUP BY date,warehouse_id  ORDER BY data_insert_date DESC ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) { 
            $count_invoice = $count_invoice+1;
            $warehouse_info = SETUP::SETUP_WAREHOUSE($fetch['warehouse_id']);
           $related_value = $fetch['warehouse_id'] . '@' . $fetch['date'] ; 
            ?>            <input type="text" style="display:none;" id="id_<?php print $count_invoice;?>" value="<?php print $related_value;?>" >

                                            <tr>   
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['invoice_date'];?></td>
                                                <td><?php print $fetch['datainsertdate'];?></td>
                                                <td><?php print $warehouse_info['name'];?></td>
                                                <td> 
                                                 
                                                <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','id_<?php print $count_invoice;?>','Stock Adjustment');"><i class="fa fa-eye"></i></button>
                                                 </td>
                                            </tr>
        <?php } ?>
                                        </tbody>
                                    </table>  

                                    </div>                            
                        
                     
                        
                    </div>
                </div>                    
                
            

</div>
</div>