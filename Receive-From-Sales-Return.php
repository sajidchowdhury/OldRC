<?php 
?>
  <!-- PAGE TITLE -->


  <ul class="breadcrumb">
    <li><a href="#">Inventory Management </a></li>          
    <li><a href="#">Add Stock </a></li>                    

    <li class="active">Receive From Sales Return</li>
</ul>

                <!-- END PAGE TITLE -->  
<div class="page-content-wrap">
                
                <div class="row">
                    <div class="col-md-12">
                    <div class="table-responsive">

                        <table id="customers2" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Customer</th>
                                                <th>Return Invoice</th>
                                                <th>Return Invoice <br> Created By </th>
                                                <th>Sales Invoice</th>
                                                <th>Sales Invoice <br>Created By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $sl =  1;
                                        $count1 = 0;
        $query = $conn_me->prepare("SELECT A.`id`, E.`name` as `poster_name` ,G.`name` as `sales_name` ,C.`customer_name`,C.`shop_name`,A.`code`, A.`invoice_no`,date_format(A.`invoice_date`, '%d-%m-%Y') AS `returnINvoice`, date_format(B.`invoice_date`, '%d-%m-%Y') AS `salesINvoice`,B.`invoice_no` AS `INV` 
         FROM `sales_return_invoice`  A

        JOIN `sales_invoice`  B ON (A.`sale_invoice_id` = B.`id`) 
        JOIN `setup_customer`  C ON (A.`customer_id` = C.`id`) 

        JOIN `admin`  D ON (A.`poster` = D.`id`) 
        JOIN `setup_employee`  E ON (D.`employee_id` = E.`id`) 

        JOIN `admin`  F ON (B.`sales_person` = F.`id`) 
        JOIN `setup_employee`  G ON (F.`employee_id` = G.`id`) 



        where A.`status` = 'Done' AND A.`warehouse_receive` = 'Pending' ORDER BY A.`code` DESC ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {
            
                        
            
            $count1 = $count1+1;

            ?><input type="text" style="display:none" id="dc_<?php print $count1?>" value="<?php print $fetch['code'];?>" >
                                            <tr>
                                                <td><?php print $sl++;?></td>
                                                <td><?php print $fetch['customer_name'];?> <br> <?php print $fetch['shop_name'];?></td>
                                                <td><?php print $fetch['invoice_no'];?> <br> <?php print $fetch['returnINvoice'];?></td>
                                                <td><?php print $fetch['poster_name'];?> </td>

                                                <td><?php print $fetch['INV'];?> <br> <?php print $fetch['salesINvoice'];?></td>
                                                <td><?php print $fetch['sales_name'];?> </td>

                                                <td>
                                                <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','dc_<?php print $count1;?>','Pending Receive Sales Return');"><i class="fa fa-eye"></i></button>

                                                <input type="button" class="btn btn-danger btn-rounded btn-sm" value=" X " onclick="delete_return_invoice('<?php print $fetch['id'] ; ?>')">
    </td>
                                            </tr>
        <?php } ?>
                                        </tbody>
                                    </table>    
        </div>  
                    </div>
                </div>                    
                
            </div>




