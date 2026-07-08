<?php
include_once("auth.php");
include('function_query.php');
$conn_me = Database::getInstance();

?>

 <?php



if($_POST['action'] == 'Overall FG Inventory' ){


    $query1 = null;
    unset($query1);


    $xml_product_list = simplexml_load_file("xml_productList.xml");
    $content = '';

    $content .= '<div class="table-responsive"><table  class="table table-bordered table-striped datatable" id="ATable" style="white-space:nowrap;">
<thead>
  <tr>
    <th>SL No.</th>
    <th> Product</th>

    <th> Category </th>';
    
    $query1 = $conn_me->prepare("SELECT * FROM `setup_warehouse`  ORDER BY `name` ASC "); 
    $query1->execute();
    $count =  $query1->rowCount();
    $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
    foreach($fetch_list1 AS $fetch1){ 

        $content .= '<th colspan="2" style="align:center">'.$fetch1['name'].'</th>';
       } 
       $content .= '   <th colspan="2">Total</th>

    </tr>';

    $content .= '    <tr>
    <td colspan="3"></td>';

    for ($i=0; $i < $count; $i++) { 

        $content .= '    <td >Cartoon</td><td>Pieces</td>';
       }
       $content .= '  <td>Cartoon</td><td>Pieces</td>
    </tr>

</thead>';
    $sl =1;
  foreach ( $xml_product_list->ROW as  $value ) { 
        $total_cartoon = 0;
        $total_qty = 0;
    $content .='<tr>
    <td data-toggle="tooltip" title="Data Serial">'.$sl++.'</td>
    <td data-toggle="tooltip" title="Product Name" >'.$value['product_name'].'</td>
    <td data-toggle="tooltip" title="Category">'.$value['product_category'].'</td>';

    foreach($fetch_list1 AS $fetch1){ 
        $stock_data = STOCK::FG_ITEM_WISE_STOCK($fetch1['id'],$value['id'],'warehouse_wise');
        if($value['pcs_in_cartoon'] > 0 ){
            $in_cartoon = number_format((float)($stock_data['ITEM_STOCK']/$value['pcs_in_cartoon']), 2, '.', '') ;
        }else{
            $in_cartoon = 0.00; 
        }
        if($stock_data['ITEM_STOCK'] > 0 ){ $stock_qty =  $stock_data['ITEM_STOCK'];  }else{  $stock_qty = '';  }
        if($in_cartoon > 0 ){ $in_cartoon_qty =  $in_cartoon;  }else{  $in_cartoon_qty = '';  }

        $content .='<td   data-toggle="tooltip" title="Cartoon :: '.$fetch1['name'].' :: '.$value['product_name'].'">'.$in_cartoon_qty.'</td>
                    <td   data-toggle="tooltip"  title="Pieces :: '.$fetch1['name'].' :: '.$value['product_name'].'">'.$stock_qty.'</td>';

                    $total_qty += $stock_data['ITEM_STOCK'];
                    $total_cartoon += $in_cartoon;           
    }



    $content .='<td data-toggle="tooltip" title="Cartoon :: Total">';
    if($total_cartoon > 0 ){ $content .= $total_cartoon; } else { } $content .='</td>';
    $content .='<td data-toggle="tooltip" title="Pieces :: Total">';
    if($total_qty > 0 ){ $content .= $total_qty; } else { } $content .='</td>';

    
    $content .='</tr>';

  

    }
  
    $content .='</table></div>';


print $content ;


}else if ( $_POST['action'] == 'Short-List'){



    
    if( $_POST['report_type'] == 'Multipal-Product-Wise'  || $_POST['report_type'] == 'Multipal-Category-Wise' || $_POST['report_type'] == 'Multipal-Warehouse-Wise' ){

    if($_POST['report_type'] == '' ){

        print 'Select Report Type';




    }else {

        if(!empty($_POST['EXTRAFILED'])){

            $ids = implode(',', $_POST['EXTRAFILED']);      


            if ( $_POST['report_type'] == 'Multipal-Product-Wise' ){

                $QUERY = " AND  p.`id` IN  ({$ids})  " ;
        
            }else if (  $_POST['report_type'] == 'Multipal-Category-Wise'){
        
                $QUERY = "   AND   p.`category_id` IN  ({$ids})    " ;

            }else if (  $_POST['report_type'] == 'Multipal-Warehouse-Wise' ){

                $QUERY = "   AND   bp.`warehouse_id` IN  ({$ids})  " ;
            }else{
                
                $QUERY = " ";
            }
        


            
        
                $content = '<div class="row mydivclass">
                <div class="col-md-12">';
                

                  
    $content .= '<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' '.$_POST['report_type'].'\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\''.$_POST['report_type'].'\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div> ';



$content .= ' <div class="panel panel-default">
                        <div class="panel-body" id="load_table">
                       <div class="table-responsive">
                    <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">
                        <thead>';
            
    
                        $sl=1;
        
                $content .='<tr>
                <td>Sl</td>
                <td>Product Name </td>
                <td>Category </td>
                <td>Current Stock (Pcs)</td>
                <td>Unit</td>
                <td>Current Stock (Carton)</td>
                <td>Last Purchased Date</td>
                <td>Last Purchased Qty (Pcs)</td>
                <td>Unit</td>
                <td>Last Purchased Qty (Carton)</td>
    
             ';
           
            
                $content .='</thead>
                <tbody>
                ';
    
                $sl =0;
                $ck131 = $conn_me->prepare("SELECT 
                p.id, 
                p.safty_stock, 
                FLOOR((COALESCE(SUM(bp.stock_in), 0) - COALESCE(SUM(bp.stock_out), 0))) as current_stock,
                IFNULL(MAX(hlp.invoice_date), 'NOT PURCHASED YET') as last_purchase_date,
                IFNULL((SELECT IFNULL(hlp2.receive_quantity, 0) FROM history_local_fg_purches hlp2 WHERE hlp2.product_id = p.id AND hlp2.invoice_date = MAX(hlp.invoice_date)), 0) as last_purchase_qty
              FROM 
                setup_product p 
                LEFT JOIN balance_product bp ON p.id = bp.product_id 
                LEFT JOIN history_local_fg_purches hlp ON p.id = hlp.product_id 
                WHERE p.`in_service` = 'checked' $QUERY
              GROUP BY 
                p.id 
              HAVING 
                current_stock < p.safty_stock
              
              
              
             ");
                $ck131->execute();
                $fe_ck31 = $ck131->fetchAll(PDO::FETCH_ASSOC);
                foreach($fe_ck31 AS $item){
                    $info_product = SETUP::SETUP_PRODUCT($item['id']);
               
    
            if($info_product['pcs_in_cartoon'] > 0 ){
                $in_cartoon = number_format((float)($item['current_stock']/ ($info_product['pcs_in_cartoon'] ?? 0 )), 2, '.', '') ;
                $in_cartoon2 = number_format((float)($item['last_purchase_qty']/ ($info_product['pcs_in_cartoon'] ?? 0 )), 2, '.', '') ;
    
            }else{
                $in_cartoon = 0.00; 
                $in_cartoon2 = 0.00; 
            }
    
    
    
                 
                    $sl++;
                 
                    $content .='<tr>
                    <td >'.$sl.'</td>
                    <td>'.$info_product['product_name'].'</td>
                    <td>'.$info_product['category'].' </td>
                    <td>'.$item['current_stock'].' </td>
                    <td>'.$info_product['unit'].'</td>
                    <td>'.$in_cartoon.'</td>
                    <td>'.$item['last_purchase_date'].' </td>
                    <td>'.$item['last_purchase_qty'].' </td>
                    <td>'.$info_product['unit'].'</td>
                    <td>'.$in_cartoon2.'</td>
    
                 ';
                 $content .='</tr>';
                    
                 
    
            }
    
    
    
             
                     
                 
             
             
                $content .= '</tbody> 
                </table>
                </div>
                </div>      
              </div>
            
            </div>
            </div>';
           
            print $content;
         
        
        }else{


            print 'Select Report Type';

        }

    }

}else if( $_POST['report_type'] == 'Finished-Goods'){

    $info_product = SETUP::SETUP_PRODUCT($_POST['EXTRAFILED']);

    
    $content = '<div class="row mydivclass">
                <div class="col-md-12">
                
                    <div class="panel panel-default">
                        <div class="panel-body" id="load_table">
                       <div class="table-responsive">
                    <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">';

                
                    $content .='<tr>
                    <td>Recent Safety Stock</td>
                    <td>'.$info_product['safty_stock'].' '.$info_product['unit'].'</td>
                    </tr>';

                    $content .='<tr>
                    <td>Update Safety Stock</td>
                    <td><input type="number" class="form-control" value="0.00" id="update_safty_stock" ></td>
                    </tr>';

                    $content .='<tr>
                    <td></td>
                    <td><input type="button" class="btn btn-info"c onclick="updateSaftystock(\''.$_POST['EXTRAFILED'].'\',\'product_id\');"  value="UPDATE"  ></td>
                    </tr>';





    $content .= '</tbody> 
    </table>
    </div>
    </div>      
  </div>

</div>
</div>';


    print $content;

    
}else if( $_POST['report_type'] == 'FG-Category'){


    
    $content = '<div class="row mydivclass">
                <div class="col-md-12">
                
                    <div class="panel panel-default">
                        <div class="panel-body" id="load_table">
                       <div class="table-responsive">
                    <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">';

                
                    $content .='<tr>
                    <td>Update Safety Stock</td>
                    <td><input type="number" class="form-control" value="0.00" id="update_safty_stock" ></td>
                    </tr>';

                    $content .='<tr>
                    <td></td>
                    <td><input type="button" class="btn btn-info"c onclick="updateSaftystock(\''.$_POST['EXTRAFILED'].'\',\'category_id\');"  value="UPDATE"  ></td>
                    </tr>';





    $content .= '</tbody> 
    </table>
    </div>
    </div>      
  </div>

</div>
</div>';


    print $content;

}else{
    print '';
}

}else if ( $_POST['action'] == 'Multiple-Godown'){


    
    if($_POST['report_type'] == '' ){

        print 'Select Report Type';



    }else {

        if(!empty($_POST['EXTRAFILED'])){

            $ids = implode(',', $_POST['EXTRAFILED']);      


                        
            if ( $_POST['report_type'] == 'Multipal-Product-Wise' ){

                $QUERY = " where  A.`product_id` IN  ({$ids}) " ;
        
            }else if (  $_POST['report_type'] == 'Multipal-Category-Wise'){
        
                $QUERY = "  JOIN `setup_product` B ON (A.`product_id` = B.`id`)  where   B.`category_id` IN  ({$ids})  " ;
        
    
            }else{
                
                $QUERY = " ";
            }
        


            $content = '';
        
            $content = '<div class="row mydivclass">
            <div class="col-md-12">
            
            <div class="panel-heading">
            <div class="btn-group pull-right">
                <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                <ul class="dropdown-menu">
                    <li><a  onclick="printButtn(\' :: Cold Product List\',\'MSalary2\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
                    <li><a onclick="exportToExcel(\'Cold Product List\',\'MSalary2\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
                </ul>
            </div>                                    
            
            </div>

                <div class="panel panel-default">
                    <div class="panel-body" id="load_table">
                   <div class="table-responsive">
                <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary2">
                    <thead>';
        

                    $sl=1;
    
            $content .='<tr>
            <td>Sl</td>
            <td>Product Name </td>
            <td>Category </td>

            <td>Name of the Godown</td>
            <td>Number of Product (in Pcs)</td>
            <td>Unit</td>
            <td>Number of Product (in Carton)</td>
         ';
       
        
            $content .='</thead>
            <tbody>
            ';

            $sl =0;
            $ck131 = $conn_me->prepare("SELECT A.product_id,GROUP_CONCAT(DISTINCT A.warehouse_id ORDER BY A.warehouse_id ASC) AS warehouse_ids, COUNT(DISTINCT A.warehouse_id) AS warehouse_count
            FROM balance_product A
            $QUERY  
            GROUP BY A.product_id
            HAVING warehouse_count > 1  ");
            $ck131->execute();
            $fe_ck31 = $ck131->fetchAll(PDO::FETCH_ASSOC);
            foreach($fe_ck31 AS $item){
                $info_product = SETUP::SETUP_PRODUCT($item['product_id']);

                $sl++;
             
                $content .='<tr>
                <td >'.$sl.'</td>
                <td>'.$info_product['product_name'].'</td>
                <td>'.$info_product['category'].' </td>
    
                <td></td>
                <td></td>
                <td></td>
                <td></td>
             ';


             $warehouse_ids = explode(',', $item['warehouse_ids']);

foreach ($warehouse_ids as $warehouse_id) {
    $info_warehouse = SETUP::SETUP_WAREHOUSE($warehouse_id);
    $stock_data = STOCK::FG_ITEM_WISE_STOCK($warehouse_id,$item['product_id'],'warehouse_wise');

    if($stock_data['ITEM_STOCK'] > 0 ){

        if($info_product['pcs_in_cartoon'] > 0 ){
            $in_cartoon = number_format((float)($stock_data['ITEM_STOCK']/$info_product['pcs_in_cartoon']), 2, '.', '') ;
        }else{
            $in_cartoon = 0.00; 
        }
        $content .='<tr>
        <td></td>
        <td></td>
        <td></td>

        <td>'.$info_warehouse['name'].'</td>
        <td>'.$stock_data['ITEM_STOCK'].'</td>
        <td>'.$info_product['unit'].' </td>
        <td>'.$in_cartoon.'</td>
     ';

    }
 


           
}
               
                
             

            }




         
                 
             
         
         
            $content .= '</tbody> 
            </table>
            </div>
            </div>      
          </div>
        
        </div>
        </div>';
       
        print $content;

        }else{

            print 'Select Report Type';
        }
    }


}else if ( $_POST['action'] == 'Product-New-List'){



  
    if($_POST['report_type'] == '' ){

        print 'Select Report Type';



    }else {
        
        $date_from = date("Y-m-d", strtotime($_POST['date_from']));
        $date_to = date("Y-m-d", strtotime($_POST['date_to']));
    
        
        if(!empty($_POST['EXTRAFILED'])){
            
            $ids = implode(',', $_POST['EXTRAFILED']);      


            
            if ( $_POST['report_type'] == 'Multipal-Product-Wise' ){

                $QUERY = " where  ( A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) AND A.`product_id` IN  ({$ids}) " ;
        
            }else if (  $_POST['report_type'] == 'Multipal-Category-Wise'){
        
                $QUERY = "   JOIN `setup_product` B ON (A.`product_id` = B.`id`)  where   B.`category_id` IN  ({$ids})  AND  ( A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' )" ;
        
            }else if (  $_POST['report_type'] == 'Multipal-Warehouse-Wise'){
        
                $QUERY = " where  ( A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' )  AND A.`warehouse_id`  IN  ({$ids}) " ;
        
            }else{
                
                $QUERY = " ";
            }
        
        
        
            $new_array = [];
            $count = 0;
        
            $ck131 = $conn_me->prepare("SELECT A.*  FROM `history_local_fg_purches` A     $QUERY  ");
            $ck131->execute();
            if($ck131->rowCount()>0){
            
                $fe_ck31 = $ck131->fetchAll(PDO::FETCH_ASSOC);
                foreach($fe_ck31 AS $fetch31){
                if(!empty($fetch31['receive_quantity'])){
                      $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch31['warehouse_id']);
                      $info_product = SETUP::SETUP_PRODUCT($fetch31['product_id']);
        
                      
        
                $in_carton = (is_null($fetch31['receive_quantity']) || $fetch31['receive_quantity'] == 0 ) ? 0.00 :  number_format((float)($fetch31['receive_quantity']/$info_product['pcs_in_cartoon']), 2, '.', '') ;


        if($fetch31['receive_quantity'] > 0 ){
            $count  = $count+1;
            $new_array[$count]['serial'] = $count;
            $new_array[$count]['date'] = $fetch31['invoice_date'];
            $new_array[$count]['qty'] = $fetch31['receive_quantity'];
            $new_array[$count]['carton'] = $in_carton;
            $new_array[$count]['product_name'] = $info_product['product_name'];
            $new_array[$count]['product_category'] = $info_product['category'];
            $new_array[$count]['warehouse_name'] = $info_warehouse['name'];
            $new_array[$count]['unit'] = $info_product['unit'];
            $new_array[$count]['source'] = 'Purchase';
            $new_array[$count]['sales_rate'] = $info_product['sales_rate'];
        
        }
                  
                }    
               
            }
            
            }
        
        
        
            $ck7 = $conn_me->prepare("SELECT A.* FROM `fg_opening_stock` A  $QUERY ");
            $ck7->execute();
            
            $fe_ck7 = $ck7->fetchAll(PDO::FETCH_ASSOC);
            foreach($fe_ck7 AS $fetch7){
            
                    $count  = $count+1;
        
                    $info_product = SETUP::SETUP_PRODUCT($fetch7['product_id']);
                    $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch7['warehouse_id']);
        
        
                    if( is_null($fetch7['quantity']) || $fetch7['quantity'] == 0 ){
                        $in_carton = 0;
                    }else{
                        if($info_product['pcs_in_cartoon'] > 0 ){
                            $in_carton = number_format((float)($fetch7['quantity']/$info_product['pcs_in_cartoon']), 2, '.', '');

                            


        
                        }else{
                            $in_carton = 0;
                        }
            
                    }
        




                    if($fetch7['quantity'] > 0 ){
                    $new_array[$count]['serial'] = $count;
                    $new_array[$count]['date'] = $fetch7['invoice_date'];
                    $new_array[$count]['qty'] = $fetch7['quantity'];
                    $new_array[$count]['carton'] = $in_carton;
                    $new_array[$count]['product_name'] = $info_product['product_name'];
                    $new_array[$count]['product_category'] = $info_product['category'];
                    $new_array[$count]['warehouse_name'] = $info_warehouse['name'];
                    $new_array[$count]['unit'] = $info_product['unit'];
                    $new_array[$count]['source'] = 'Opening Stock';
                    $new_array[$count]['sales_rate'] = $info_product['sales_rate'];
        
                    }
            }
            
        
        
             $content = '';
        
             $content = '<div class="row mydivclass">
             <div class="col-md-12">
             
                 <div class="panel panel-default">
                     <div class="panel-body" id="load_table">
                    <div class="table-responsive">
                 <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">
                     <thead>';
         

                     $sl=1;
        
                     $date_asc = array_column($new_array, 'date');
                     array_multisort($date_asc, SORT_ASC, $new_array);

     

             $content .='<tr>
             <td>Sl</td>
             <td>Date</td>
             <td>Source</td>
             <td>Product Name</td>
             <td>Category</td>
             <td> Warehouse </td>
             <td>Number of Product added (in Pcs)</td>
             <td>Unit</td>
             <td>Number of Product added  (in Carton)</td>
             <td>Current Sales Rate</td>
          ';
        
         
             $content .='</thead>
             <tbody>
             ';

        
        
        
        
        
                 foreach($new_array as $item) {
        
         
        
                 $content .='<tr>
                 <td>'.$sl++.'</td>
                 <td>'.$item['date'].'</td>
                 <td>'.$item['source'].'</td>
                 <td>'.$item['product_name'].'</td>
                 <td>'.$item['product_category'].'</td>
                 <td>'.$item['warehouse_name'].'</td>
                 <td>'.$item['qty'].'</td>
                 <td>'.$item['unit'].'</td>
                 <td>'.$item['carton'].'</td>
                 <td>'.$item['sales_rate'].'</td>';
               
             
             }
         
         
         
                 $content .=' </tr>';
         
                 
             
         
         
             $content .= '</tbody> 
             </table>
             </div>
             </div>      
           </div>
         
         </div>
         </div>';
        
         print $content;
        }else{
            print "Select Something";

        }
        

    }


   


 

}else if ( $_POST['action'] == 'Product-Hot-List'){


    $page = $_POST['start'];
    $limit=20;
    $row = ($page-1)*$limit;


    
if($_POST['report_type'] == 'Current-Stock' ){

    $QUERY = " where B.`in_service` = 'checked' "; 

}else if ($_POST['report_type'] == 'FG-Category'){

$QUERY = " where B.`in_service` = 'checked' AND  B.`category_id` = '".$_POST['EXTRAFILED']."'    "; 

}else if ($_POST['report_type'] == 'Finished-Goods'){

    $QUERY = " WHERE B.`id` = '".$_POST['EXTRAFILED']."'  "; 
}else {
    $QUERY = ''; 
}



    $content = '';


    $content = '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
        <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary2">
            <thead>';

    $content .='<tr>
    <th>Product Name</th>
    <th>Current Stock (in Pcs)</th>
    <th>Current Stock (in Carton)</th>
    <th>Last Purchased</th>
    <th>Purchased Last Time (in Pcs)</th>
    <th>Purchased Last Time (in Carton)</th>
    </td>';



    $query1 = $conn_me->prepare("SELECT A.product_id,B.pcs_in_cartoon,B.code,B.product_name, 
    SUM(A.stock_in) AS stock_in, 
    SUM(A.stock_out) AS stock_out,
    B.safty_stock,
    (SUM(A.stock_in) - SUM(A.stock_out)) AS ITEM_STOCK
FROM balance_product A
JOIN setup_product B ON A.product_id = B.id
$QUERY 
GROUP BY A.product_id
HAVING ITEM_STOCK < B.safty_stock;
"); 
    $query1->execute();
    $count = $query1->rowCount();
    if($count > 0 ){
        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){ 
    
            $info_last = FIND::LAST_PURCHASED($fetch1['product_id']);

            
            $stcok_carton = $fetch1['ITEM_STOCK'] > 0 ? round($fetch1['ITEM_STOCK']/$fetch1['pcs_in_cartoon']) : 0.00 ;
            $last_carton = $info_last['receive_quantity'] > 0 ? round($info_last['receive_quantity']/$fetch1['pcs_in_cartoon']) : 0.00 ;


                $content .= '<tr><input type="text" style="display:none" id="product_id'.$fetch1['product_id'].'" value="'.$fetch1['product_id'].'" >';
                $content .= '<td>'.$fetch1['code'].' '.$fetch1['product_name'].'</td>';
                $content .= '<td>'.$fetch1['ITEM_STOCK'].'</td>';
                $content .= '<td>'.$stcok_carton.'</td>';
                $content .= '<td>'.$info_last['invoice_date'].'</td>';
                $content .= '<td>'.$info_last['receive_quantity'].'</td>';
                $content .= '<td>'.$last_carton.'</td>';
                $content .= '</tr>';
        
           
    
        }
    
    }else{
        $content .= '<tr><td colspan="5">No More Data</td></tr>';
    }



    $content .= '</tbody> 
    </table>
    </div>
    </div>      
  </div>

</div>
</div>';



    
   



    print   $content . '_SAJID_' . $count ;



}else if ( $_POST['action'] == 'Product-Sale-Summery'){

    
    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));

    if($_POST['report_type'] == 'All' ){

        $QUERY = " where A.`date` BETWEEN '".$date_from."' AND '".$date_to."'  A.`product_id`" ;


    }else if ( $_POST['report_type'] == 'Finished-Goods' ){

        $QUERY = " where A.`product_id` = '".$_POST['EXTRAFILED']."'  AND A.`date` BETWEEN '".$date_from."' AND '".$date_to."' GROUP BY  A.`date` " ;

    }else if (  $_POST['report_type'] == 'FG-Category'){

        $QUERY = " JOIN `setup_product` B ON (A.`product_id` = B.`id`)  where   B.`category_id` = '".$_POST['EXTRAFILED']."' AND  A.`date` BETWEEN '".$date_from."' AND '".$date_to."' GROUP BY A.`date` " ;


    }else{
        
        $QUERY = " ";
    }


    $content = '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
    <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$_POST['action'].' From '.$_POST['date_from'].'  To '.$_POST['date_to'].'</th></tr> ';
    
    $content .= '</table></div>';




    $content = '<div class="row">
   
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
        <table class="table  table-hover table-condensed table-striped table-bordered datatable" id="MSalary">
            <thead>
            <th>Sl</th>
            <th>Product Name</th>
            <th>Number of Product Sold (in Pcs)</th>
            <th>Number of Product Sold (in Carton)</th>
            </thead>
                <tbody>'; 
    $sl =1;
    $total_qty = 0;
    $total_carton = 0;
    $qry11 = $conn_me->prepare("SELECT 
    SUM(si.sales_quantity) AS total_sales_quantity, 
    (SELECT SUM(bp.stock_in - bp.stock_out) 
     FROM balance_product bp 
     WHERE bp.product_id = si.product_id 
         AND bp.date < '".$date_from."' ) AS opening_stock,
    (SELECT SUM(bp.stock_in - bp.stock_out) 
     FROM balance_product bp 
     WHERE bp.product_id = si.product_id 
         AND bp.date < '".$date_from."'  ) AS closing_balance
FROM 
    sales_invoice_item AS si
    INNER JOIN sales_invoice AS s ON si.sales_invoice_id = s.id
WHERE 
    s.invoice_date BETWEEN '".$date_from."'  AND '".$date_to."'
    AND s.generate_challan = 'Done' AND si.product_id = 1; ");
    $qry11->execute();
    $fetch_list11 = $qry11->fetchAll(PDO::FETCH_ASSOC);

    if ($qry11->rowCount() > 0)
    {
        foreach($fetch_list11 AS $fetch22) {
    
            if($fetch22['QTY'] > 0 ){
                $info_product = SETUP::SETUP_PRODUCT($fetch22['product_id']);
                $carton = round($fetch22['QTY']/$info_product['pcs_in_cartoon']);
              $content .= '<tr>
              <th>'.$sl++.'</th>
              <th>'.$info_product['product_name'].'</th>
              <th>'.$fetch22['QTY'].'</th>
              <th>'.$carton.'</th>
  
             </tr>';
             $total_qty += $fetch22['QTY'];
             $total_carton += $carton;

            }
           
        }
    
    }else{
        $total_in = 0;
    }
           
    $content .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>';


    
    print $content ;

    

}else if ( $_POST['action'] == 'Nawabpur-Branch-Statements'){




    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


   //  "14,15" id of  NAWABPUR VAI VAI MARKET  NAWABPUR SHOP (202)




   $movement_array = [];
   $count = 0;


   if($_POST['report_type'] == 'All' || $_POST['report_type'] == 'Transfer'){   
 
$ck1 = $conn_me->prepare("SELECT A.code,A.`invoice_date`,A.`product_id`,A.`quantity`,B.`brunch_id` FROM `fg_warehouse_to_warehouse_transfer` A JOIN `admin` B ON (A.`poster` = B.`id`) where B.`brunch_id` = 1 AND `A`.`TO_warehouse_id` IN ('14','15')AND (  A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' )
");
$ck1->execute();

$fe_ck1 = $ck1->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck1 AS $fetch1){

    $product_price = SETUP::getProductPriceOnTransferDate($fetch1['invoice_date'],$fetch1['product_id']);


$count  = $count+1;
$movement_array[$count]['serial'] = $count;
$movement_array[$count]['date'] = $fetch1['invoice_date'];
$movement_array[$count]['product_id'] = $fetch1['product_id'];
$movement_array[$count]['price'] = $product_price ;
$movement_array[$count]['qty'] = $fetch1['quantity'];
$movement_array[$count]['description'] = "Transfer from Head Office";
$movement_array[$count]['link'] = '<a href= "print.php?print=FG-WAREHOUSE-TO-WAREHOUSE-TRANSFER-RECEIPT&code='.$fetch1['code'].'" >COPY</a>';;

}

}




if($_POST['report_type'] == 'All' || $_POST['report_type'] == 'Sales-Delivery'){

$ck2 = $conn_me->prepare("SELECT code,invoice_date,id FROM `sales_invoice` where brunch_id = 3 and dispatch_from_which_brunch = 1 AND ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."') 
");
$ck2->execute();
$fe_ck2 = $ck2->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck2 AS $fetch2){

    $ck3 = $conn_me->prepare("SELECT  product_id,sales_rate,sales_quantity FROM `sales_invoice_item` where sales_invoice_id = '".$fetch2['id']."'
    ");
    $ck3->execute();
    $fe_ck3 = $ck3->fetchAll(PDO::FETCH_ASSOC);
    foreach($fe_ck3 AS $fetch3){



$count = $count+1;
$movement_array[$count]['serial'] = $count;
$movement_array[$count]['date'] = $fetch2['invoice_date'];
$movement_array[$count]['product_id'] = $fetch3['product_id'];
$movement_array[$count]['price'] =  $fetch3['sales_rate'];
$movement_array[$count]['qty'] = $fetch3['sales_quantity'];
$movement_array[$count]['description'] = "Sales Delivery from Head Office";
$movement_array[$count]['link'] = '<a href= "invoice_copy.php?code='.$fetch2['code'].'" >INVOICE</a>';;
   

}


}

}


$content = '<div class="row mydivclass">
<div class="col-md-12">

    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
       <div class="table-responsive">
    <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">
        <thead>';

$content .='<tr>
<td>Sl</td>
<td>Date</td>
<td>Product Name</td>
<td>Delivery Source </td>
<td> Quantity </td>
<td>Unit</td>
<td>Quantity CTN</td>
<td>Sales rate</td>
<td>Total Price</td>
';


$content .='</thead>
<tbody>
';
$sl=1;

$date_asc = array_column($movement_array, 'date');
array_multisort($date_asc, SORT_ASC, $movement_array);




$sum_total = 0 ;
    foreach($movement_array as $item) {


  $info_product = SETUP::SETUP_PRODUCT($item['product_id']);

  if($info_product['pcs_in_cartoon'] > 0 ){
    $carton = round($item['qty']/ ($info_product['pcs_in_cartoon'] ?? 0));
}else{
    $carton = 0.00;
}

$total = $item['price']*$item['qty'] ; 

    $content .='<tr>
    <td>'.$sl++.'</td>
    <td>'.$item['date'].'</td>
    <td>'.$info_product['product_name'].' '.$item['link'].'</td>
    <td>'.$item['description'].'</td>
    <td>'.$item['qty'].'</td>
    <td>'.$info_product['unit'].'</td>
    <td>'.$carton.'</td>
    <td>'.$item['price'].'</td>
    <td>'.$total.'</td>';
  
    $sum_total += $total ;
}



    $content .=' </tr>';

    
    $content .= '<tfoot>
    <tr>
        <th colspan="8" style="text-align:right"><b>Total</b></th>
        <th>' . $sum_total . '</th>


    </tr>
    </tfoot>';
    


$content .= '</tbody> 
</table>
</div>
</div>      
</div>

</div>
</div>';

print $content;


}else if ( $_POST['action'] == 'Brunch Wise Ledger Report'){


    $FROMDATE = date("Y-m-d", strtotime($_POST['date_from']));
    $TODATE = date("Y-m-d", strtotime($_POST['date_to']));


    FIND::BRUNCH_LEDGER($_POST['related_id'],$FROMDATE,$TODATE,$_POST['branch_id']);

    
    $report = '<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
    <li><a  onclick="printButtn(\' :: Brunch Wise Ledger Report\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
    <li><a onclick="exportToExcel(\'Brunch Wise Ledger Report\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
    </div> ';
 
    $report .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body">
           
            <table class="table datatable" id="MSalary">
            <thead>
            <th>Sl</th>

            <th>Date</th>
            <th>Description</th>
            <th>IN Amount</th>
            <th>OUT Amount</th>
            <th>Due</th>
            </thead>
                <tbody>'; 

                $total_in_amount =0;
                $total_out_amount =0;
   $due = 0 ;
// Initialize variables


$query = $conn_me->prepare("
SELECT 
link, 
note,
movment_date,
IN_AMOUNT,
    OUT_AMOUNT,
    (@balance := @balance + IN_AMOUNT - OUT_AMOUNT ) AS Due
FROM 
tempTable_brtunch_ledger_movement,
    (SELECT @balance := 0) AS balance_init
ORDER BY 
movment_date;



");

// Execute the main query
$query->execute();

// Fetch the results
$fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

            
$sl = 1;

        foreach($fetch_list AS $fetch) {

            $report .= '<tr>
            <td>'.$sl++.'</td>
            <td>'.date("d-m-Y", strtotime($fetch['movment_date'])).'</td>';
            
            if($fetch['link'] === 'No LInk' ){
                $report .= '<td>'.$fetch['note'].'</td>';
            }else{
                $report .= '<td><a href="'.$fetch['link'].'">'.$fetch['note'].'</a></td>';
            }
            

            
            if($fetch['note'] == 'Closing Balance' ){
            $report .= '<td></td>';
            $report .= '<td></td>';
            $inAmount = 0 ;
            $outAmount = 0 ;
            }else{
            $report .= '<td>'.$fetch['IN_AMOUNT'].' </td>';
            $report .= '<td>'.$fetch['OUT_AMOUNT'].'</td>';

            $inAmount = $fetch['IN_AMOUNT'] ;
            $outAmount = $fetch['OUT_AMOUNT'] ;
            }

            $report .= '<td>'.$fetch['Due'].' </td>
           
           </tr>';

            $total_in_amount += $inAmount;
            $total_out_amount +=$outAmount;
            $due = number_format((float)( $fetch['Due'] ), 2, '.', '') ;

        }
    
    
        $report .= '<tfoot>
        <tr>
            <th colspan="3" style="text-align:right"><b>Total</b></th>
            <th>' . $total_in_amount . '</th>
            <th>' . $total_out_amount . '</th>
            <th style="color:red;font-size:18px;"> '.$due.'</th>

        </tr>
        </tfoot>';
        
        
              
    $report .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';

        
        print  $report ;

$stm = $conn_me->prepare("DROP table IF EXISTS tempTable_brtunch_ledger_movement");
$stm->execute();



}else if ( $_POST['action'] == 'Customer Wise Ledger Report'){



    $FROMDATE = date("Y-m-d", strtotime($_POST['date_from']));
    $TODATE = date("Y-m-d", strtotime($_POST['date_to']));


    FIND::CUSTOMER_LEDGER($_POST['related_id'],$FROMDATE,$TODATE,$_POST['branch_id']);
    $customer_info = SETUP::SETUP_CUSTOMER($_POST['related_id']);

      
        $report = '<div class="btn-group pull-right">
        <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
        <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Customer Wise Ledger Report\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Customer Wise Ledger Report\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
        </ul>
        </div> ';
 
    $report .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body">
           
            <table class="table datatable" id="MSalary">
            <thead>
            <th>Sl</th>

            <th>Date</th>
            <th>Description</th>
                        <th>User</th>

            <th>OUT Amount</th>
            <th>IN Amount</th>
            <th>Due</th>
            </thead>
                <tbody>'; 

                $total_in_amount =0;
                $total_out_amount =0;
   $due = 0 ;
// Initialize variables


$query = $conn_me->prepare("
SELECT 
    COALESCE(C.name, ' ') AS User,
    A.poster,
    A.tr_type,
    A.link, 
    A.note,
    A.movment_date,
    A.IN_AMOUNT,
    A.OUT_AMOUNT,
    (@balance := @balance + A.IN_AMOUNT - A.OUT_AMOUNT) AS Due
FROM 
    tempTable_customer_ledger_movement A
LEFT JOIN 
    admin B ON A.poster IS NOT NULL AND A.poster = B.id
LEFT JOIN 
    setup_employee C ON B.employee_id = C.id
CROSS JOIN 
    (SELECT @balance := 0) AS balance_init
ORDER BY 
    A.movment_date;


");

// Execute the main query
$query->execute();

// Fetch the results
$fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

            
$sl = 1;

        foreach($fetch_list AS $fetch) {

            $report .= '<tr>
            <td>'.$sl++.'</td>
            <td>'.date("d-m-Y", strtotime($fetch['movment_date'])).'</td>';
            
            if($fetch['link'] === 'No LInk' ){
                $report .= '<td>'.$fetch['note'].' '.$fetch['tr_type'].'</td>';
            }else{
                $report .= '<td><a href="'.$fetch['link'].'">'.$fetch['note'].' '.$fetch['tr_type'].'</a></td>';
            }
            
                $report .= '<td>'.$fetch['User'].' </td>';

            
            if($fetch['note'] == 'Closing Balance' ){
            $report .= '<td></td>';
            $report .= '<td></td>';
            $inAmount = 0 ;
            $outAmount = 0 ;
            }else{
            $report .= '<td>'.$fetch['IN_AMOUNT'].' </td>';
            $report .= '<td>'.$fetch['OUT_AMOUNT'].'</td>';

            $inAmount = $fetch['IN_AMOUNT'] ;
            $outAmount = $fetch['OUT_AMOUNT'] ;
            }

            $report .= '<td>'.$fetch['Due'].' </td>
           
           </tr>';

            $total_in_amount += $inAmount;
            $total_out_amount +=$outAmount;
            $due = number_format((float)( $fetch['Due'] ), 2, '.', '') ;

        }
    
    
        $report .= '<tfoot>
        <tr>
            <th colspan="4" style="text-align:right"><b>Total</b></th>
            <th>' . $total_in_amount . '</th>
            <th>' . $total_out_amount . '</th>
            <th style="color:red;font-size:18px;"> '.$due.'</th>

        </tr>
        </tfoot>';
        
        
              
    $report .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';

        
        print  $report ;

$stm = $conn_me->prepare("DROP table IF EXISTS tempTable_customer_ledger_movement");
$stm->execute();

}else if ( $_POST['action'] == 'Collection-Report'){

    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));
    
    
    
     $content = '<div class="btn-group pull-right">
     <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
     <ul class="dropdown-menu">
         <li><a  onclick="printButtn(\' :: Account Head Wise Report\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
         <li><a onclick="exportToExcel(\'Account Head Wise Report\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
     </ul>
 </div> ';

    $content .= '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
           <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="MSalary">
           <thead>';

    $content .='<tr>
    <th >Sl</th>
    <th >Date  </th>
    <th >Cash.C</th>
    <th >Bank.C</th>
     <th >Mobile.C</th>
        <th >Details</th>
        <th >Notes</th>

    </tr> 
    
    </thead>';

$sl=1;
$total_in = 0;
$total_out = 0;
$qry1 = $conn_me->prepare("SELECT 
    A.note,
  DATE_FORMAT(A.transection_date, '%d/%m/%Y') AS date,  
    CASE  
        WHEN A.transection_by = 'Cash' THEN A.in_amount  
        ELSE 0  
    END AS `By Cash`,  
    CASE  
        WHEN A.transection_by = 'Bank' THEN A.in_amount  
        ELSE 0  
    END AS `By Bank`, 
    CASE  
        WHEN A.transection_by = 'Mobile-Banking' THEN A.in_amount  
        ELSE 0  
    END AS `By Mobile`, 
    CASE  
        WHEN A.transection_by = 'Bank' THEN CONCAT(B.bank_name, ' - ', B.account_number)  
        WHEN A.transection_by = 'Mobile-Banking' THEN CONCAT(C.mobile_bank_name, ' - ', C.mobile_number)  
        ELSE ''  
    END AS `Details`  
FROM account_transection A  
LEFT JOIN setup_bank B ON A.transection_by = 'Bank' AND A.transection_by_id = B.id  
LEFT JOIN setup_mobile_banking C ON A.transection_by = 'Mobile-Banking' AND A.transection_by_id = C.id  
WHERE A.poster = '".$_POST['related_id']."' 
AND A.transection_date BETWEEN '".$date_from."' AND '".$date_to."' 
AND A.transection_type = 'INCOME';
");
$qry1->execute();
$fetch_list11 = $qry1->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list11 AS $fetch22){



    $content .= '<tr>
                <td>'.$sl++.'</td>

            <td>'.$fetch22['date'].'</td>
            <td>'.$fetch22['By Cash'].'</td>
            <td>'.$fetch22['By Bank'].'</td>
                        <td>'.$fetch22['By Mobile'].'</td>

            <td>'.$fetch22['Details'].'</td>
            <td>'.$fetch22['note'].'</td>


           </tr>';

$total_bank += $fetch22['By Bank'];
$total_cash += $fetch22['By Cash'];
$total_mobile += $fetch22['By Mobile'];
}



$content .= '<tfoot>
<tr>
    <th colspan="2" style="text-align:right"><b>Total</b></th>
    <th>' . $total_cash . '</th>
    <th>' . $total_bank . '</th><th>' . $total_mobile . '</th><th></th><th></th>
</tr>
</tfoot>';

$content .= '</table>';



print $content;


    
}else if ( $_POST['action'] == 'Account Head Wise Report'){


    if($_POST['branch_id'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND  A.`brunch_id` = '".$_POST['branch_id']."' ";
     }



    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));

     $selectedValues = implode(',', $_POST['related_id']);


     if($_POST['report_type'] == 'Multipal-Ledger-Wise' ){
        $QUERY = " WHERE A.ledger_id   IN  ({$selectedValues})  AND  A.transection_date BETWEEN '".$date_from."' AND '".$date_to."'  ";

     }else if ($_POST['report_type'] == 'Multipal-Head-Wise'){
        $QUERY = "  where A.`transection_head_id` IN  ({$selectedValues})  AND A.transection_date BETWEEN '".$date_from."' AND '".$date_to."'  ";

     }else{

        $QUERY = "";
     }


  
     $content = '<div class="btn-group pull-right">
     <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
     <ul class="dropdown-menu">
         <li><a  onclick="printButtn(\' :: Account Head Wise Report\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
         <li><a onclick="exportToExcel(\'Account Head Wise Report\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
     </ul>
 </div> ';

    $content .= '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
           <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="MSalary">
           <thead>';

    $content .='<tr>
    <th>Date</th>
    <th>Ledger  </th>
    <th>Account Head   </th>
    <th>Note</td>
    <th>In Amount</th>
    <th>Out Amount</th>


    </tr> </thead>';

$sl=1;
$total_in = 0;
$total_out = 0;
$qry1 = $conn_me->prepare("SELECT A.*  ,B.`account_head`,C.`name`,
 
A.in_amount, A.out_amount, (A.in_amount - A.out_amount) AS closing_stock
FROM account_transection A
JOIN `setup_ac_head` B ON (A.`transection_head_id` = B.`id`)
JOIN `setup_ladger_head` C ON (A.`ledger_id` = C.`id`)

$QUERY  $BRUNCH_QUERY ");
$qry1->execute();
$fetch_list11 = $qry1->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list11 AS $fetch22){



    $content .= '<tr>
            <td>'.$fetch22['transection_date'].'</td>
            <td>'.$fetch22['name'].'</td>
            <td>'.$fetch22['account_head'].'</td>
            <td>'.$fetch22['note'].'</td>
            <td>'.$fetch22['in_amount'].'</td>
            <td>'.$fetch22['out_amount'].'</td>

           </tr>';

$total_in += $fetch22['in_amount'];
$total_out += $fetch22['out_amount'];

}



$content .= '<tfoot>
<tr>
    <th colspan="4" style="text-align:right"><b>Total</b></th>
    <th>' . $total_in . '</th>
    <th>' . $total_out . '</th>
</tr>
</tfoot>';

$content .= '</table>';



print $content;



}else if ( $_POST['action'] == 'Supplier Payment History'){


    
    if($_POST['branch_id'] == 'All' ){
        $BRUNCH_QUERY = " ";
     }else {
       $BRUNCH_QUERY = " AND  A.`brunch_id` = '".$_POST['branch_id']."' ";
     }



    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));

     $selectedValues = implode(',', $_POST['related_id']);


     if($_POST['report_type'] == 'Multipal-Supplier-Wise' ){

        $QUERY = " WHERE A.transection_to_id   IN  ({$selectedValues})  AND  A.`transection_to` = 'Supplier' AND ";


     }else{
        $QUERY = " ";
     }


  
     $content = '<div class="btn-group pull-right">
     <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
     <ul class="dropdown-menu">
         <li><a  onclick="printButtn(\' :: Supplier Payment History\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
         <li><a onclick="exportToExcel(\'Supplier Payment History\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
     </ul>
 </div> ';


     $content .= '<div class="row mydivclass">
     <div class="col-md-12">
     
         <div class="panel panel-default">
             <div class="panel-body" id="load_table">
            <div class="table-responsive">
            <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="MSalary">
            <thead>';
 
     $content .='<tr>
     <th>Sl</th>
     <th>Transaction ID</th>
     <th>Date  </th>
     <th> Name</th>
     <th>Address</td>
     <th>Payment Method</th>
     <th>Amount</th>
 
 
     </tr> </thead>';
 
 $sl=1;
 $total_in = 0;
 $total_out = 0;
 $qry1 = $conn_me->prepare("SELECT A.*,B.`address`,B.`supplier_name`,B.`address`
 
 FROM account_transection A 
 JOIN `setup_supplier` B ON (A.`transection_to_id` = B.`id`) 
 
 $QUERY  A.`transection_date` BETWEEN '".$date_from."' AND '".$date_to."' $BRUNCH_QUERY ");
 $qry1->execute();
 $fetch_list11 = $qry1->fetchAll(PDO::FETCH_ASSOC);
 foreach($fetch_list11 AS $fetch22){
 
 
 
     $content .= '<tr>
             <td>'.$sl++.'</td>
             <td>'.$fetch22['invoice_no'].'</td>
             <td>'.$fetch22['transection_date'].'</td>
             <td>'.$fetch22['supplier_name'].'</td>
             <td>'.$fetch22['address'].'</td>
             <td>'.$fetch22['transection_by'].'</td>
             <td>'.$fetch22['out_amount'].'</td>
 
            </tr>';
 
 $total_in += $fetch22['out_amount'];
 
 }
 
 
 
 $content .= '<tfoot>
 <tr>
     <th colspan="6" style="text-align:right"><b>Total</b></th>
     <th>' . $total_in . '</th>
 </tr>
 </tfoot>';
 
 $content .= '</table>';
 
 
 
 print $content;
 



}else if ( $_POST['action'] == 'Customer Payment History'){


    if($_POST['branch_id'] == 'All' ){
        $BRUNCH_QUERY = " ";
     }else {
       $BRUNCH_QUERY = " AND  A.`brunch_id` = '".$_POST['branch_id']."' ";
     }



    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));

     $selectedValues = implode(',', $_POST['related_id']);


     if($_POST['report_type'] == 'Multipal-Customer-Wise' ){

        $QUERY = " WHERE A.transection_to_id   IN  ({$selectedValues})  AND  A.`transection_to` = 'Customer' AND ";

     }else if ($_POST['report_type'] == 'Multipal-Branch-Wise'){

        $QUERY = "  where A.`brunch_id` IN  ({$selectedValues})  AND  ";

    }else if ($_POST['report_type'] == 'Multipal-Sales-Person'){

    $qry1 = $conn_me->prepare("SELECT group_concat(`transection_id`) as `transectionids` FROM `sales_invoice` where sales_person IN  ({$selectedValues}) AND `transection_id` IS NOT NULL AND  `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ");
    $qry1->execute();
    $fetch_list = $qry1->fetch(PDO::FETCH_ASSOC);

    $QUERY = "  where A.`id` IN  ({$fetch_list['transectionids']})  AND  ";


}else if ($_POST['report_type'] == 'Multipal-Sales-By'){

    $qry1 = $conn_me->prepare("SELECT group_concat(`transection_id`) as `transectionids` FROM `sales_invoice` where sales_by IN  ({$selectedValues}) AND `transection_id` IS NOT NULL AND  `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ");
    $qry1->execute();
    $fetch_list = $qry1->fetch(PDO::FETCH_ASSOC);

    $QUERY = "  where A.`id` IN  ({$fetch_list['transectionids']})  AND  ";

}else if ($_POST['report_type'] == 'Multiple-Division-Wise'){

    $qry1 = $conn_me->prepare("SELECT group_concat(`id`) as `customerids` FROM `setup_customer` where `division_id` IN  ({$selectedValues}) AND `in_service` = 'checked' ");
    $qry1->execute();
    $fetch_list = $qry1->fetch(PDO::FETCH_ASSOC);

    $QUERY = " WHERE A.transection_to_id   IN  ({$fetch_list['customerids']})   AND  A.`transection_to` = 'Customer' AND   ";


}else if ($_POST['report_type'] == 'Multiple-District-Wise'){

    $qry1 = $conn_me->prepare("SELECT group_concat(`id`) as `customerids` FROM `setup_customer` where `district_id` IN  ({$selectedValues}) AND `in_service` = 'checked' ");
    $qry1->execute();
    $fetch_list = $qry1->fetch(PDO::FETCH_ASSOC);

    $QUERY = " WHERE A.transection_to_id   IN  ({$fetch_list['customerids']})   AND  A.`transection_to` = 'Customer' AND   ";


}else if ($_POST['report_type'] == 'Multiple-Upazila-Wise'){

    $qry1 = $conn_me->prepare("SELECT group_concat(`id`) as `customerids` FROM `setup_customer` where `upazila_id` IN  ({$selectedValues}) AND `in_service` = 'checked' ");
    $qry1->execute();
    $fetch_list = $qry1->fetch(PDO::FETCH_ASSOC);

    $QUERY = " WHERE A.transection_to_id   IN  ({$fetch_list['customerids']})   AND  A.`transection_to` = 'Customer' AND   ";


     }else{

        $QUERY = "";
     }


  
     $content = '<div class="btn-group pull-right">
     <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
     <ul class="dropdown-menu">
         <li><a  onclick="printButtn(\' :: Customer Payment History\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
         <li><a onclick="exportToExcel(\'Customer Payment History\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
     </ul>
 </div> ';
     
    $content .= '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
           <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="MSalary">
           <thead>';

    $content .='<tr>
    <th>Sl</th>
    <th>Transaction ID</th>
    <th>Date  </th>
    <th>Shop Name</th>
    <th>Address</td>
    <th>Received Branch</th>
    <th>Payment Method</th>
    <th>Amount</th>


    </tr> </thead>';

$sl=1;
$total_in = 0;
$total_out = 0;
$qry1 = $conn_me->prepare("SELECT A.*,B.`address`,B.`shop_name`,C.`brunch`

FROM account_transection A 
JOIN `setup_customer` B ON (A.`transection_to_id` = B.`id`) 
JOIN `setup_brunch` C ON (A.`brunch_id` = C.`id`) 

$QUERY  A.`transection_date` BETWEEN '".$date_from."' AND '".$date_to."' $BRUNCH_QUERY ");
$qry1->execute();
$fetch_list11 = $qry1->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list11 AS $fetch22){



    $content .= '<tr>
            <td>'.$sl++.'</td>
            <td>'.$fetch22['invoice_no'].'</td>
            <td>'.$fetch22['transection_date'].'</td>
            <td>'.$fetch22['shop_name'].'</td>
            <td>'.$fetch22['address'].'</td>
            <td>'.$fetch22['brunch'].'</td>
            <td>'.$fetch22['transection_by'].'</td>
            <td>'.$fetch22['in_amount'].'</td>

           </tr>';

$total_in += $fetch22['in_amount'];

}



$content .= '<tfoot>
<tr>
    <th colspan="7" style="text-align:right"><b>Total</b></th>
    <th>' . $total_in . '</th>
</tr>
</tfoot>';

$content .= '</table>';



print $content;


}else if ( $_POST['action'] == 'Balance-Sheet'){

    

    $FROMDATE = date("Y-m-d", strtotime($_POST['date_from']));
    $TODATE = date("Y-m-d", strtotime($_POST['date_to']));



    $content = '<div class="row">
   
    <div class="col-md-6">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <h2>CASH IN </h2>
        <table class="table  table-hover table-condensed table-striped table-bordered">
            <thead>
            <th>Account Head</th>
            <th>Amount</th>

            </thead>
                <tbody>'; 
    $sl =1;
    $total_in = 0;
    $qry11 = $conn_me->prepare("SELECT 
    sum(`in_amount`) as `income` ,id
    FROM `account_transection` where transection_type = 'INCOME' AND `brunch_id` = '". $_SESSION['USER_BRUNCH']."' AND  `transection_date` BETWEEN '".$FROMDATE."' AND '".$TODATE."'  GROUP BY `id`;");
    $qry11->execute();
    $fetch_list11 = $qry11->fetchAll(PDO::FETCH_ASSOC);

    if ($qry11->rowCount() > 0)
    {
        foreach($fetch_list11 AS $fetch22) {
    
    $info_head = SETUP::ACCOUNT_TRANSECTION($fetch22['id']);
    
            $content .= '<tr>
            <th>'.$info_head['transection_head_name'].'</th>
            <th>'.$fetch22['income'].'</th>
           </tr>';
           $total_in += $fetch22['income'];
        }
    
    }else{
        $total_in = 0;
    }
           
    $content .= '<tr><th  class="pull-right"> Total Cash In </th><th>'. $total_in .'</th></tr>';
    $content .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    <div class="col-md-6">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
            <h2>CASH OUT     </h2>
        <table class="table  table-hover table-condensed table-striped table-bordered">
            <thead>
            <th>Account Head</th>
            <th>Amount</th>

            </thead>
                <tbody>'; 
    $sl =1;
    $total_out = 0;
    $qry11 = $conn_me->prepare("SELECT 
    sum(`out_amount`) as `expense` ,id
    FROM `account_transection` where transection_type = 'EXPENSE' AND `brunch_id` = '". $_SESSION['USER_BRUNCH']."' AND  `transection_date` BETWEEN '".$FROMDATE."' AND '".$TODATE."'   GROUP BY `id`;");
    $qry11->execute();
    $fetch_list11 = $qry11->fetchAll(PDO::FETCH_ASSOC);

    if ($qry11->rowCount() > 0)
    {
        foreach($fetch_list11 AS $fetch22) {
    
            $info_head = SETUP::ACCOUNT_TRANSECTION($fetch22['id']);
    
            $content .= '<tr>
            <th>'.$info_head['transection_head_name'].'</th>
            <th>'.$fetch22['expense'].'</th>
           </tr>';
           $total_out += $fetch22['expense'];
        }
    
    }else{
        $total_out = 0;
    }
    $content .= '<tr><th  class="pull-right"> Total Cash Out</th><th>'. $total_out .'</th></tr>';

    $content .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';
    $total = $total_in-$total_out;
    $content .= '<div class="row">
   
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <h3 class="text-info" style="text-align:center">CASH BALANCE ::: '.$total.'</h2>
           </div>
           </div>
           </div>
           </div>';


print   $content;

}else if ( $_POST['action'] == 'Transaction-Report'){



    $brunch_id = $_POST['EXTRAFILED'];
    $BRUNCH_DATA  = SETUP::SETUP_BRUNCH($brunch_id);
   

    $FROMDATE = date("Y-m-d", strtotime($_POST['date_from']));
    $TODATE = date("Y-m-d", strtotime($_POST['date_to']));



    if($_POST['report_type'] == 'INCOME' ){
        $CONDITION = " AND `transection_type` = 'INCOME' ";
        $CONDITION2 = " sum(`in_amount`)  ";
    }else if ($_POST['report_type'] == 'EXPENSE'){
        $CONDITION = " AND `transection_type` = 'EXPENSE' ";
        $CONDITION2 = "  sum(`out_amount`) ";

    }else{
        $CONDITION = '';
        $CONDITION2 = " sum(`in_amount`) - sum(`out_amount`) ";

    }

    

$movement_array = [];
$count = 0;



$ck1 = $conn_me->prepare("SELECT $CONDITION2 AS `balance` FROM balance_transection WHERE  `date` <  '".$FROMDATE."'  AND  brunch_id = '".$brunch_id ."' ");
$ck1->execute();

$fe_ck1 = $ck1->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck1 AS $fetch1){

$count  = $count+1;
$movement_array[$count]['serial'] = $count;
$movement_array[$count]['date'] = $FROMDATE;

$movement_array[$count]['in_amount'] = $fetch1['balance'];
$movement_array[$count]['out_amount'] =  0;

$movement_array[$count]['description'] = "Closing Balance";


}

$ck2 = $conn_me->prepare("SELECT *  FROM account_transection  WHERE ( transection_date BETWEEN '".$FROMDATE."' AND '".$TODATE."' ) AND brunch_id = '".$brunch_id ."'  $CONDITION");
$ck2->execute();

$fe_ck2 = $ck2->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck2 AS $fetch2){

    $info1 = SETUP::ACCOUNT_TRANSECTION($fetch2['id']);


$count  = $count+1;
$movement_array[$count]['serial'] = $count;
$movement_array[$count]['date'] = $fetch2['transection_date'];

if($_POST['report_type'] == 'INCOME' ){
    $movement_array[$count]['in_amount'] = $fetch2['in_amount'];
    $movement_array[$count]['out_amount'] =  0;
    }else if ($_POST['report_type'] == 'EXPENSE'){
        $movement_array[$count]['in_amount'] = 0;
        $movement_array[$count]['out_amount'] =  $fetch2['out_amount'];
    
    }else{
        $movement_array[$count]['in_amount'] = $fetch2['in_amount'];
        $movement_array[$count]['out_amount'] =  $fetch2['out_amount'];
    }

$movement_array[$count]['description'] = $info1['details_of_transection_to'];

}



$stm = $conn_me->prepare("DROP table IF EXISTS tempTable_transection_report");
$stm->execute();

$stmt2 = $conn_me->prepare("CREATE TABLE tempTable_transection_report (
    id int NOT NULL AUTO_INCREMENT,
    movment_date date  NULL,
    note varchar(255)  NULL,
    in_amount float(20,2)  NULL,
    out_amount float(20,2)  NULL,
    PRIMARY KEY (id)
);
");
$stmt2->execute();


foreach($movement_array as $item) {
   
    $date = date("Y-m-d", strtotime($item['date']));



    $query = $conn_me->exec("INSERT INTO `tempTable_transection_report` 
    ( 
        `id`,`movment_date`,`note`,`in_amount`, `out_amount`
    
    ) 
    VALUES
    (
        '0',
        '".$date."',
        '".$item['description']."',
        '".$item['in_amount']."',
        '".$item['out_amount']."'

    ) ");

}
     

  
$content = '<div class="btn-group pull-right">
<button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
<ul class="dropdown-menu">
    <li><a  onclick="printButtn(\' :: Transaction-Report\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
    <li><a onclick="exportToExcel(\'Transaction-Report\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
</ul>
</div> ';


    $content .= '<div class="row">
    <div class="col-md-12">';
    
       $content = '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
    <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$_POST['action'].' of ' .$BRUNCH_DATA['brunch'] .'</th></tr> ';
    
    $content .= '</table></div>
    
    
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           
        <table class="table  table-hover table-condensed table-striped table-bordered" id="MSalary">
            <thead>
            <th>Date</th>
            <th>Notes</th>
            <th>Income</th>
            <th>Expense</th>
            <th>Closing</th>

            </thead>
                <tbody>'; 
    $sl =1;
    $total_in = 0;
    $total_out = 0;
    
    $query =$conn_me->prepare("
    SELECT `movment_date`,`note`,
           `in_amount`,
           `out_amount`,
           (@rt:=@rt + (`in_amount` - `out_amount`)) AS runningbalance
    FROM `tempTable_transection_report`, (SELECT @rt:=0) rt
   
   ");
    $query->execute();

    foreach($query as $item) {
    
  
            $content .= '<tr>
            <td>'.$item['movment_date'].'</td>
            <td>'.$item['note'].'</td>';
            if($item['note'] == 'Closing Balance' ){
                $content .= '<td><p style="display:none">'.$item['in_amount'].' </p></td>';
                $content .= '<td><p style="display:none">'.$item['out_amount'].'</p></td>';
            }else{
                $content .= '<td>' . number_format((float)( $item['in_amount']), 2, '.', '') . ' </td>';
                $content .= '<td>' . number_format((float)( $item['out_amount']), 2, '.', '') . ' </td>';

            }           

            $content .= ' <td>' . number_format((float)( $item['runningbalance']), 2, '.', '') . ' </td>';

            $content .= '  </tr>';

            $total_in += $item['in_amount'];
            $total_out += $item['out_amount'];
        }
    
    
                    
                      
    $content .= '</tbody> 

    <tfoot>
    <tr>
        <th colspan="2" style="text-align:right"><b>Total</b></th>
        <th>' . number_format((float)( $total_in), 2, '.', '') . '</th>
        <th>' . number_format((float)( $total_out), 2, '.', '') . '</th>
          <th></th>
    </tr>
</tfoot>


          </table>
          </div>
                
        </div>
    
    </div>
    </div>';


print   $content;



}else if ( $_POST['action'] == 'Monthly-Leave-Report'){



    $explode = explode("-",$_POST['date_from']);
    $month = $explode[0];
    $year = $explode[1];
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year); // number of days in the month
                    
    
    $content ='<table class="table table-bordered" style="background-color:white">
      <thead>
        <tr>
           <tr> <td>SL</td>
            <td>Date</td><td>Leave Type</td>';
            
     $content .='</tr>
      </thead>
      <tbody> ';
    
        $sl =1;

        $query = $conn_me->prepare("SELECT *  FROM `apply_leave`  WHERE `employee_id` = '".$_POST['EXTRAFILED']."' ");
        $query->execute();
        $fetchall = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetchall as $fetch) { 

            /*
            $info_leave = SETUP::SETUP_LEAVE_APPLY($fetch['id']);
            

        if($info_leave['number_of_days'] >  1){
            
            for ($i=1; $i <= $info_leave['number_of_days'] ; $i++) { 
                
                 $FROMDATE = date("Y-m-d", strtotime($fetch['leave_from_date']));
                  $newDate = new DateTime($FROMDATE);
                   $newDate->modify('+'.$i.' day');
                  $newDate = $newDate->format('d/m/Y');

                $content .='<tr>';
                $content .='<th>'.$sl++.'</th>';
                $content .='<th>'.$newDate.'</th>';
                $content .='<th>'.$info_leave['leave_type'].'</th>';
                $content .='</tr>';
            }

        }else{
            $content .='<tr>';
            $content .='<th>'.$sl++.'</th>';
            $content .='<th>'.$info_leave['leave_to_date'].'</th>';
            $content .='<th>'.$info_leave['leave_type'].'</th>';
            $content .='</tr>';

        }

        $content .='</tr>';
        */
    } 

 
    $content .='  </tbody>
    </table>';
    


print   $content;


}else if ( $_POST['action'] == 'Monthly-Attendance-Report'){

    $explode = explode("-",$_POST['date_from']);
    $month = $explode[0];
    $year = $explode[1];
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year); // number of days in the month
    
    
    $vacation_days = [];
               
    $query = $conn_me->prepare("SELECT DAY(`holiday`) as holiday  FROM setup_holiday  WHERE MONTH(`holiday`) = '".$month."' AND `holiday_year` = '".$year."' ;
    ");
    $query->execute();
    $fetch_all = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_all as $fetch) {
      $vacation_days[] = $fetch['holiday'];
    }
    
    
    
    $content ='<table class="table table-bordered datatable" id="MSalary">
      <thead>
        <tr>
           <tr> <td>SL</td>
            <td>EMP ID</td>
            <td>NAME</td>';
            
    
     for ($i = 1; $i <= $days_in_month; $i++) { 
     $content .='<th>'.$i.'</th>';
     } 
    
     $content .='</tr>
      </thead>
      <tbody> ';
    
      $sl =1;
       $query1 = $conn_me->prepare("SELECT * FROM `take_attandance` where  DATE_FORMAT(`attandance_date`, '%m-%Y') = '".$_POST['date_from']."'  GROUP BY `employee_id` order by `employee_id` ASC  "); 
       $query1->execute();
       $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
       foreach($fetch_list1 AS $fetch1){ 
        $info_employee = SETUP::SETUP_EMPLOYEEY($fetch1['employee_id']);
    
    
    
        $content .='    <tr>
          <td>'.$sl++.'</td>
          <td>'.$info_employee['employee_code'].'</td>
          <td >'.$info_employee['name'].'</td>';
    
     for ($i = 1; $i <= $days_in_month; $i++) {
        $date = new DateTime("$year-$month-$i");
        $day_of_week = $date->format('N');
        $day_of_month = $date->format('j');
    
        if (in_array($day_of_month, $vacation_days)) { 
            $content .=' <td><b style="color:blue">H.Day</b></td>';
     }else{ 
      
    
          if ($day_of_week == 5) { 
            $content .=' <td class=""><b style="color:blue">H.Day</b></td>';
     } else { 
     $attandance_date = date("Y-m-d", strtotime("$year-$month-$day_of_month"));
     $info_att = FIND::checkAttendanceStatus($fetch1['employee_id'],$attandance_date);
    
    
     $content .='<td>'.$info_att.'</td>';
    
                } 
    
       }  
     
    } 
    $content .='</tr>';
    }
    $content .='  </tbody>
    </table>';
    


print   $content;

}else if ( $_POST['action'] == 'Raw Goods Stock Report' ){

    
if($_POST['report_type'] == 'Current-Stock' ){

    $QUERY = " "; 

}else if ($_POST['report_type'] == 'Raw-Category'){

$QUERY = " WHERE `category_id` = '".$_POST['EXTRAFILED']."'     "; 

}else if ($_POST['report_type'] == 'Raw-Material'){

    $QUERY = " WHERE `id` = '".$_POST['EXTRAFILED']."'  "; 
}else {
    $QUERY = ''; 
}



    $content = '';

    

if($_POST['report_type'] == 'Warehouse-Wise'){


    $content = '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
        <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">
            <thead>';

    $content .='<tr>
    <th>Product Id	</th>
    <th>Product Name	</th>
    <th>Unit 	</th>
    <th>Warehouse</td>';

     $content .='<th>Current Stock (Pcs)</th>
    <th>Current Stock (Carton)	</th>
    </tr> </thead>';



    $query1 = $conn_me->prepare("SELECT `id` FROM `setup_raw_material`  $QUERY   "); 
    $query1->execute();

        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){ 
    
            $info_product = SETUP::SETUP_RAW_MATERIAL($fetch1['id']);

            $stock_info = STOCK::RAW_ITEM_WISE_STOCK($_POST['EXTRAFILED'],$fetch1['id'],'warehouse_wise');

            $content .= '<tr>';
            $content .= '<td>'.$info_product['material_code'].'</td>';
            $content .= '<td>'.$info_product['product_name'].'</td>';
            $content .= '<td>'.$info_product['unit'].'</td>';
            $content .= '<td>'.$info_product['category'].'</td>';
            $content .= '<td>'.$stock_info['ITEM_STOCK'].'</td>';
            $content .= '<td>'.$stock_info['ITEM_STOCK_CARTOON'].'</td>';

            $content .= '</tr>';
    
        }
    
   


    $content .= '</tbody> 
    </table>
    </div>
    </div>      
  </div>

</div>
</div>';


}else{

    $content = '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
        <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;">
            <thead>';

            $content .='<tr>
            <th>Product Id	</th>
            <th>Product Name	</th>
            <th>Unit 	</th>
            <th>Category</td>
            <th>Warehouse</td>';


             $content .='<th>Current Stock (Pcs)</th>
            <th>Current Stock (Carton)	</th>
            </tr> </thead>';



    $query1 = $conn_me->prepare("SELECT `id` FROM `setup_raw_material` $QUERY   "); 
    $query1->execute();
        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){ 
    
            $info_product = SETUP::SETUP_RAW_MATERIAL($fetch1['id']);

            $stock_info = STOCK::RAW_ITEM_WISE_STOCK('',$fetch1['id'],'product_wise');
    
            $content .= '<tr><input type="text" style="display:none" id="product_id'.$fetch1['id'].'" value="'.$fetch1['id'].'" >';
            $content .= '<td>'.$info_product['material_code'].'</td>';
            $content .= '<td>'.$info_product['product_name'].'</td>';
            $content .= '<td>'.$info_product['unit'].'</td>';

            $content .= '<td>'.$info_product['category'].'</td>';
            $content .= '<td><button type="button" class="btn btn-warning block" id="#exampleModalCenter" onclick="modal_wihout_refresh(\'load_data_in_modal\',\'product_id'.$fetch1['id'].'\',\'Raw Warehouse List\');">Click Here</button></td>';
            $content .= '<td>'.$stock_info['ITEM_STOCK'].'</td>';
            $content .= '<td>'.$stock_info['ITEM_STOCK_CARTOON'].'</td>';

            $content .= '</tr>';
    
        }
    




    $content .= '</tbody> 
    </table>
    </div>
    </div>      
  </div>

</div>
</div>';



}

    
   



    print   $content;

}else if ( $_POST['action'] == 'All Challan Record' ){

    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


    if($_POST['EXTRAFILED'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND  `dispatch_from_which_brunch` = '".$_POST['EXTRAFILED']."' ";
     }



    $content = '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
           <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="MSalary">
           <thead>';

    $content .='<tr>
    <th>Sl	</th>
    <th>Date</th>
    <th>Invoice No    </th>
    <th>Shop Name</td>
    <th>Address</th>
    <th>Sales Person	</th>
    <th>Dispatcher</th>
    <th> Total Pcs  </th>
    <th> Total Carton    </th>

    <th> Action  </th>

    </tr> </thead>';


$sl =1;
    $query1 = $conn_me->prepare("SELECT `warehouse_dispatch`,`code` FROM `sales_invoice`  WHERE  ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."')  AND `generate_challan` = 'Done'  $BRUNCH_QUERY ORDER BY concat(`invoice_date`,' ',`code`) DESC  "); 
    $query1->execute();
    $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){ 
    
            $info_invoice = SETUP::SETUP_SALES_INVOICE($fetch1['code']);
         
       
                $content .= '<tr>';
                $content .= '<td>'.$sl++.'</td>';
                $content .= '<td>'.$info_invoice['invoice_date'].'</td>';
                $content .= '<td>'.$info_invoice['invoice_no'].'</td>';
                $content .= '<td>'.$info_invoice['shop_name'].'</td>';
                $content .= '<td>'.$info_invoice['address'].'</td>';

                $content .= '<td>'.$info_invoice['sales_person_name'].'</td>';
                $content .= '<td>'.$info_invoice['dispatcher_name'].'</td>';
                $content .= '<td>'.$info_invoice['total_pcs'].'</td>';
                $content .= '<td>'.$info_invoice['total_ctn'].'</td>';

                $content .= '<td><a target="_BLINK" href="challan_copy.php?code='.$info_invoice['code'].'">Challan Copy </a></td>';

                $content .= '</tr>';
        
           
    
        }
    
  



    $content .= '</tbody> 
    </table>
    </div>
    </div>      
  </div>

</div>
</div>';



print   $content;


}else if ( $_POST['action'] == 'All Godown Copy' ){


    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


    if($_POST['EXTRAFILED'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND  `dispatch_from_which_brunch` = '".$_POST['EXTRAFILED']."' ";
     }




    $content = '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
           <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="example">
           <thead>';

    $content .='<tr>
    <th>Sl	</th>
    <th>Date</th>
    <th>Invoice No    </th>
    <th>Shop Name</td>
    <th>Address</th>
    <th>Sales Person	</th>
    <th>Dispatcher</th>
    <th> Status  </th>
    <th> Action  </th>

    </tr> </thead>';


$sl =1;
    $query1 = $conn_me->prepare("SELECT invoice_no,`warehouse_dispatch`,`code`,`id`,`dispatcher_id` FROM `sales_invoice`  WHERE   ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."')   $BRUNCH_QUERY  ORDER BY concat(`invoice_date`,' ',`code`) DESC  "); 
    $query1->execute();
    $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){ 
    
            $info_invoice = SETUP::SETUP_SALES_INVOICE($fetch1['code']);
            $info_status = FIND::SALES_INVOICE_STATUS($fetch1['id']);

       
                $content .= '<tr>';
                $content .= '<td>'.$sl++.'</td>';
                $content .= '<td>'.$info_invoice['invoice_date'].'</td>';
                $content .= '<td>'.$info_invoice['invoice_no'].'</td>';
                $content .= '<td>'.$info_invoice['shop_name'].'</td>';
                $content .= '<td>'.$info_invoice['address'].'</td>';

                $content .= '<td>'.$info_invoice['sales_person_name'].'</td>';
                $content .= '<td>'.$info_invoice['dispatcher_name'].'</td>';
                $content .= '<td>'. $info_status['status'] .'</td>';

               if(!empty($fetch1['dispatcher_id'])){

                $content .= '<td><a target="_BLINK" href="print.php?print=Godown Copy&code='.$info_invoice['code'].'">Godown Copy </a></td>';
               }else{
                $content .= '<td></td>';
               }
                $content .= '</tr>';
        
           
    
        }
    
  



    $content .= '</tbody> 
    </table>
    </div>
    </div>      
  </div>

</div>
</div>';



print   $content;


}else if ( $_POST['action'] == 'Invoice Movement Report' ){

   
        $content = '<div class="row mydivclass">
        <div class="col-md-12">
        
            <div class="panel panel-default">
                <div class="panel-body" id="load_table">
               <div class="table-responsive">
               <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="MSalary">
               <thead>';
    
        $content .='<tr>
        <th>Product Id	</th>
        <th>Product Name	</th>
        <th>Unit 	</th>
        <th>Category</td>
        <th>Warehouse</th>
        <th>Sales Rate	</th>
        <th>Quantity</th>
        <th> Carton </th>
        </tr> </thead>';
    
    
    
        $query1 = $conn_me->prepare("SELECT *  FROM `sales_invoice_item` where `sales_invoice_id` = '".$_POST['EXTRAFILED']."'  "); 
        $query1->execute();

            $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
            foreach($fetch_list1 AS $fetch1){ 
        
                $info_product = SETUP::SETUP_PRODUCT($fetch1['product_id']);
                $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch1['warehouse_id']);
    
           
                    $content .= '<tr>';
                    $content .= '<td>'.$info_product['product_code'].'</td>';
                    $content .= '<td>'.$info_product['product_name'].'</td>';
                    $content .= '<td>'.$info_product['unit'].'</td>';
                    $content .= '<td>'.$info_product['category'].'</td>';
                    $content .= '<td>'.$info_warehouse['name'].'</td>';
    
                    $content .= '<td>'.$info_product['sales_rate'].'</td>';
                    $content .= '<td>'.$fetch1['sales_quantity'].'</td>';
                    $content .= '<td>'.$fetch1['carton_receive'].'</td>';
    
                    $content .= '</tr>';
            
               
        
            }
        

    
    
        $content .= '</tbody> 
        </table>
        </div>
        </div>      
      </div>
    
    </div>
    </div>';
    

   
print   $content;

}else if ( $_POST['action'] == 'Cold Cusrtomer List Report' ){
    
    
    
    
    
    
    

    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to'])); 


    if($_POST['EXTRAFILED'] == 'All' ){

        $BRUNCH_QUERY1 = "";
        $BRUNCH_QUERY2 = " ";
        $BRUNCH_QUERY3 = "  ";
     }else {

       $BRUNCH_QUERY1 = " AND si2.brunch_id = '".$_POST['EXTRAFILED']."' ";
       $BRUNCH_QUERY2 = " AND si.brunch_id = '".$_POST['EXTRAFILED']."' ";
       $BRUNCH_QUERY3 = " WHERE clt.last_brunch_id = '".$_POST['EXTRAFILED']."' ";


     }


     $content = '
     <div class="col-md-12">
         <div class="panel panel-default">
             <div class="panel-body" id="load_table">
            <div class="table-responsive">
              <table id="example" class="display nowrap" style="width:100%">
            <thead>';
 


            if ($_POST['report_type'] == 'Invoice-And-Payment' ){

                $movement_array = [];
                $count = 0;


                $content .='<tr>
                <th>Sl	</th>
                <th>Customer Name	</th>
                <th>Address</th>
                <th>Sales (Out) </td>
                <th>Payment (In) </td>
                <th>Current Due</td>
           
                </tr> </thead>';

               $query = $conn_me->prepare("SELECT 
                    si.customer_id,
                    si.invoice_date,
                    si.time,
                    (SUM(sii.sales_quantity * sii.sales_rate) 
                        - si.discount 
                        - si.transport_cost 
                        - si.total_vat_cost) AS actual_invoice_price
                FROM 
                    sales_invoice si
                JOIN 
                    sales_invoice_item sii ON si.id = sii.sales_invoice_id
                WHERE 
                    si.generate_challan = 'Done'  
                    AND si.invoice_date BETWEEN '".$date_from."' AND '".$date_to."'
                    $BRUNCH_QUERY2
                GROUP BY 
                    si.id, si.customer_id
            ");
            

            $query->execute();
            $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch5) { 
                    $count  = $count+1;
                    $movement_array[$count]['serial'] = $count;
                    $movement_array[$count]['date'] = $fetch5['invoice_date'];
                    $movement_array[$count]['time'] = $fetch5['time'];
                    $movement_array[$count]['customer_id'] = $fetch5['customer_id'];
                    $movement_array[$count]['sales'] = $fetch5['actual_invoice_price'];
                    $movement_array[$count]['payment'] = 0;            
                }
            
                $query = $conn_me->prepare("
                SELECT 
                si.transection_to_id,
               si.`transection_date` ,
               si.time,
                -- Calculate the actual invoice price by subtracting the discount, transport cost, and VAT
                (SUM(si.in_amount - si.out_amount) )
                  AS actual_invoice_price
            
            FROM 
            account_transection si

            WHERE 
            si.`transection_to` = 'Customer'  AND   ( si.`transection_date` BETWEEN '".$date_from."' AND '".$date_to."' ) $BRUNCH_QUERY2 
            GROUP BY 
               si.transection_to_id
                ");
                $query->execute();
                $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list as $fetch5) { 
                    $count  = $count+1;
                    $movement_array[$count]['serial'] = $count;
                    $movement_array[$count]['date'] = $fetch5['transection_date'];
                    $movement_array[$count]['time'] = $fetch5['time'];
                    $movement_array[$count]['customer_id'] = $fetch5['transection_to_id'];
                    $movement_array[$count]['sales'] = 0;
                    $movement_array[$count]['payment'] =$fetch5['actual_invoice_price'];          
                }




                $stm = $conn_me->prepare("DROP table IF EXISTS tempTable_payment_invoice");
                $stm->execute();
                
                $stmt2 = $conn_me->prepare("CREATE TABLE tempTable_payment_invoice (
                    id int NOT NULL AUTO_INCREMENT,
                    movment_date date  NULL,
                    time varchar(255)  NULL,
                    customer_id int(11) NOT  NULL,
                    sales float(20,2)  NULL,
                    payment float(20,2)  NULL,
                    PRIMARY KEY (id)
                );
                ");
                $stmt2->execute();
                

                foreach($movement_array as $item) {
   
                    $date = date("Y-m-d", strtotime($item['date']));
                
                
                
                    $query = $conn_me->exec("INSERT INTO `tempTable_payment_invoice` 
                    ( 
                        `id`, `movment_date`,`time`,`customer_id`,`sales`, `payment`
                    
                    ) 
                    VALUES
                    (
                        '0',
                        '".$date."',
                      '".$item['time']."',
                        '".$item['customer_id']."',
                        '".$item['sales']."',
                        '".$item['payment']."'                
                    ) ");
                
                }
                       
                
               $sl = 1; 



                $customerInfo  = SETUP::findAllCustomer();
                $data = FIND::getAllCustomerDues('',$customerInfo, date("Y-m-d"));

                $due_array = [];
                $count_two = 0;
                foreach ($data as $row) {
                  $count_two  = $count_two+1;
                  $due_array[$count_two]['customer_id'] = $row['customer_id'];
                  $due_array[$count_two]['customer_due'] = $row['customer_due'];

                }



                $query =$conn_me->prepare("SELECT customer_id, sum(sales) AS SALES , sum(payment) AS PAID FROM `tempTable_payment_invoice` group BY customer_id ");
                $query->execute();
                $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
            
                    foreach($fetch_list AS $fetch) {
                        
   $customerInfo  = SETUP::findCustomerById($fetch['customer_id']);
   $shop_name = (!empty($customerInfo['shop_name'])) ? $customerInfo['shop_name'] :  '';
   $address = (!empty($customerInfo['address'])) ? $customerInfo['address'] :  '';
   $SALES = (!empty($fetch['SALES'])) ? $fetch['SALES'] :  0.00;
   $PAID = (!empty($fetch['PAID'])) ? $fetch['PAID'] :   0.00;

                        
                        $content .= '<tr>
                        <td>'.$sl++.'</td>
                        <td>'.$shop_name.'</td>
                        <td>'.$address.'</td>
                        <td>'.$SALES.'</td>
                        <td>'.$PAID.'</td>';

$content .= '<td>' ; 
        foreach ($due_array as $row) {
            // Check if the id attribute matches the given customerId
            if ((string)$row['customer_id'] === (string)$fetch['customer_id']) {
                // Return customer details as an associative array
                  $content .=  $row['customer_due'];
            }
        }

                     
            
                       $content .= '</td></tr>';
            
                    
                    }
    
 
                    

            }else{


                 $content .='<tr>
                <th>Customer Id </th>
                <th>Shop Name   </th>
                <th>Address  </th>
                <th>Mobile</td>
                <th>Last Active</td>
                <th>Due</td>
           
                </tr> </thead>';
            
        
         $movement_array = [];
                $count = 0;



           
              // Step 1: Create the non_transacting_customers table
$query1 = $conn_me->prepare("CREATE TEMPORARY TABLE non_transacting_customers AS
SELECT 
    c.id AS customer_id,
    c.code,
    c.shop_name,
    c.mobile,
    c.address
FROM 
    setup_customer c
LEFT JOIN 
    account_transection si 
ON 
    c.id = si.transection_to_id 
    AND si.transection_to = 'Customer' 
    AND si.transection_date BETWEEN '".$date_from."' AND '".$date_to."'
WHERE 
    si.transection_to_id IS NULL;

");
$query1->execute();

// Step 2: Create the last_transactions table
$query2 = $conn_me->prepare("
CREATE TEMPORARY TABLE customer_last_transactions AS
SELECT 
    si.transection_to_id AS customer_id,
    MAX(si.transection_date) AS last_transection_date,
    SUBSTRING_INDEX(
        GROUP_CONCAT(si.brunch_id ORDER BY si.transection_date DESC), ',', 1
    ) AS last_brunch_id
FROM 
    account_transection si
WHERE 
    si.transection_to = 'Customer'
GROUP BY 
    si.transection_to_id;
");
$query2->execute();

// Step 3: Fetch the final results
$query3 = $conn_me->prepare("SELECT 
    nc.customer_id,
    nc.code,
    nc.shop_name,
    nc.mobile,
    nc.address,
    clt.last_transection_date,
    clt.last_brunch_id
FROM 
    non_transacting_customers nc
JOIN 
    customer_last_transactions clt 
ON 
    nc.customer_id = clt.customer_id
$BRUNCH_QUERY3;
");
$query3->execute();

$fetch_list1 = $query3->fetchAll(PDO::FETCH_ASSOC);

// Loop through the results
foreach ($fetch_list1 as $fetch5) {
    // Process each customer
   

 $last_invoice_date = !empty($fetch5['last_transection_date']) 
        ? date("d/m/Y", strtotime($fetch5['last_transection_date'])) 
        : ' ';


                    $count  = $count+1;
                    $movement_array[$count]['serial'] = $count;
                    $movement_array[$count]['last_invoice_date'] = $last_invoice_date;
                    $movement_array[$count]['customer_id'] = $fetch5['customer_id'];
                    $movement_array[$count]['code'] = $fetch5['code'];
                    $movement_array[$count]['shop_name'] = $fetch5['shop_name'];
                    $movement_array[$count]['mobile'] = $fetch5['mobile'];
                    $movement_array[$count]['address'] = $fetch5['address'];

                    }
           





                $customerInfo  = SETUP::findAllCustomer();
                $data = FIND::getAllCustomerDues('Brunch-Wise-All-Customer',$customerInfo, date("Y-m-d"),$_POST['EXTRAFILED']);



                $due_array = [];
                $count_two = 0;
                foreach ($data as $row) {
                  $count_two  = $count_two+1;
                  $due_array[$count_two]['customer_id'] = $row['customer_id'];
                  $due_array[$count_two]['customer_due'] = $row['customer_due'];

                }



                foreach ($movement_array as $fetch) {




 $content .= '<tr><input type="text" style="display:none" id="customer_id'.$fetch['serial'].'" value="'.$fetch['customer_id'].'" >';

                    $content .= '<td><a type="button" id="#exampleModalCenter" onclick="modal_wihout_refresh(\'load_data_in_modal\',\'customer_id'.$fetch['serial'].'\',\'Transaction History\');">'.strval($fetch['code']).'</a></td>';

                           $content .= '<td>'.$fetch['shop_name'].'</td>';
                           $content .= '<td>'.$fetch['address'].'</td>';
                           $content .= '<td>'.$fetch['mobile'].'</td>';
                           $content .= '<td>'. $fetch['last_invoice_date'] .' </td>';
           

           $content .= '<td>' ; 
        foreach ($due_array as $row) {
            // Check if the id attribute matches the given customerId
            if ((string)$row['customer_id'] === (string)$fetch['customer_id']) {
                // Return customer details as an associative array
                  $content .=  $row['customer_due'];
            }
        }



                           $content .= '</tr>';


                 
                 }
             
 
                       
            }
            
 
     $content .= '</tbody> 
     </table>
     </div>
     </div>      
   </div>
 
 </div>
 </div>';
 





$stm = $conn_me->prepare("DROP TEMPORARY TABLE IF EXISTS last_transactions");
$stm->execute();

$stm = $conn_me->prepare("DROP TEMPORARY TABLE IF EXISTS non_transacting_customers");
$stm->execute();

 
 print   $content;

}else if ( $_POST['action'] == 'Cold List Report' ){


    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));

    if($_POST['report_type'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND sii.brunch_id = '".$_POST['report_type']."'  ";
                        

     }

   



    $content = '<div class="row mydivclass">
    <div class="col-md-12">
    <div class="panel-heading">
    <div class="btn-group pull-right">
        <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
        <ul class="dropdown-menu">
            <li><a  onclick="printButtn(\' :: Cold Product List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
            <li><a onclick="exportToExcel(\'Cold Product List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
        </ul>
    </div>                                    
    
    </div>
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
 <table class="table table-hover table-condensed table-striped table-bordered" id="example"  style="width:100%">           <thead>';

    $content .='<tr>
    <th>Product Id	</th>
    <th>Product Name	</th>
    <th>Unit 	</th>
    <th>Category</td>
    <th>Sales Rate	</th>
    <th>Current Stock (Pcs)</th>
    <th>Current Stock (CTN)</th>
    <th>Last Sold</td>

    </tr> </thead>';






    $category_ids = implode(',', $_POST['EXTRAFILED']);        





        $query1 = $conn_me->prepare("SELECT sp.id, sii.brunch_id,
        CASE
          WHEN MAX(sii.sales_manager_confirm_date) IS NULL THEN 'NOT SOLD AT ALL'
          WHEN MAX(sii.sales_manager_confirm_date) < '".$date_from."' THEN MAX(sii.sales_manager_confirm_date)
          ELSE 'SOLD WITHIN DATE RANGE'
        END AS last_sold_date
      FROM setup_product sp
      LEFT JOIN sales_invoice_item sii ON sp.id = sii.product_id $BRUNCH_QUERY AND sii.sales_manager_confirm_date BETWEEN '".$date_from."' AND '".$date_to."'
      WHERE sp.category_id IN ({$category_ids}) AND sp.`in_service` = 'checked'
      GROUP BY sp.id
      HAVING COUNT(sii.id) = 0 OR MAX(sii.sales_manager_confirm_date) < '".$date_from."'
      

        ");
  
    
$total_ctn = 0 ;$total_qty = 0 ;
    $query1->execute();
        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){ 
    
            $info_product = SETUP::SETUP_PRODUCT($fetch1['id']);
            $stock_info = STOCK::FG_ITEM_WISE_STOCK('',$fetch1['id'],'product_wise');
            $item_stock = $stock_info['ITEM_STOCK'] ; 

           // $info_last = FIND::LAST_PURCHASED($fetch1['id']);

            if($info_product['pcs_in_cartoon'] > 0 ){
                $carton = round($stock_info['ITEM_STOCK']/ ($info_product['pcs_in_cartoon'] ?? 0));
            }else{
                $carton = 0.00;
            }


       
                $content .= '<tr>';
                $content .= '<td>'.$info_product['product_code'].'</td>';
                $content .= '<td>'.$info_product['product_name'].'</td>';
                $content .= '<td>'.$info_product['unit'].'</td>';
                $content .= '<td>'.$info_product['category'].'</td>';
                $content .= '<td>'.$info_product['sales_rate'].'</td>';
                $content .= '<td>'.$item_stock.'</td>';
                $content .= '<td>'.$carton.'</td>';
                $content .= '<td>'.$fetch1['last_sold_date'].'</td>';

                $content .= '</tr>';
        
           $total_ctn += $carton ;$total_qty += $item_stock ;
    
        }
    
 


    $content .= '</tbody> ';
    
        $content .= '<tfoot>
<tr>
    <th colspan="5" style="text-align:right"><b>Total</b></th>
            <th>' . number_format((float)( $total_qty ), 2, '.', '') . '</th>

        <th>' . number_format((float)( $total_ctn ), 2, '.', '') . '</th>
    <th></th>
   
</tr>
</tfoot>
    </table>
    </div>
    </div>      
  </div>

</div>
</div>';



print   $content;



}else if ( $_POST['action'] == 'Finished Goods Stock Report' ){
   





    $date_to = date("Y-m-d", strtotime($_POST['date_to']));

if($_POST['report_type'] == 'Current-Stock' ){

    $QUERY = "   WHERE  A.`in_service` = 'checked' "; 

}else if ($_POST['report_type'] == 'FG-Category'){

$QUERY = " WHERE  A.`in_service` = 'checked' AND  A.`category_id` = '".$_POST['EXTRAFILED']."'     "; 

}else if ($_POST['report_type'] == 'Finished-Goods'){

    $QUERY = " WHERE A.`id` = '".$_POST['EXTRAFILED']."'  "; 

}else {

    $QUERY = ''; 
}



   
$content = '<h3 style="color:red;text-align:center">Report:: Stock Report till '.date("d-m-Y", strtotime($_POST['date_to'])).'</h3>';


  
$content .= '<div class="btn-group pull-right">
<button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
<ul class="dropdown-menu">
    <li><a  onclick="printButtn(\' :: ' . $_POST['action'] .' \',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
    <li><a onclick="exportToExcel(\'' . $_POST['action'] .'\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
</ul>
</div> ';

if($_POST['report_type'] == 'Multipal-Warehouse-Wise' ){

    $warehouse_id = implode(',', $_POST['EXTRAFILED']);        
    

    $content .= '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
           <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="MSalary">
           <thead>';

    $content .='<tr>
    <th>Product Id	</th>
    <th>Product Name	</th>
    <th>Unit 	</th>
    <th>Category</td>
    <th>Sales Rate	</th>
    <th>Current Stock (Pcs)</th>
    <th>Current Stock (Carton)	</th>
    <th>Stock Value	</th>

    </tr> </thead>';


    $total_product_value = 0 ;
        $total_ctn_value = 0 ;
 $total_ctn_value = 0 ;
 $total_pcs_value = 0 ;
    $query1 = $conn_me->prepare("SELECT A.product_id, A.warehouse_id,
        SUM(A.stock_in) - SUM(A.stock_out) AS current_stock,
        D.category,C.unit,B.pcs_in_cartoon,B.code,B.product_name,B.sales_rate
        FROM balance_product A
        JOIN setup_product B ON (A.product_id = B.id)
        JOIN setup_unit C ON (B.unit_id = C.id)
        JOIN setup_category D ON (B.category_id = D.id)
        WHERE A.warehouse_id IN ({$warehouse_id}) AND A.date <= '".$date_to."'
        GROUP BY A.product_id ;
"); 
    $query1->execute();

        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){ 
    

    
        $in_carton = ($fetch1['pcs_in_cartoon'] > 0) ? number_format((float)($fetch1['current_stock']/$fetch1['pcs_in_cartoon']), 2, '.', '') : 0.00;
             
       
            if($fetch1['current_stock'] > 0 ){

 
                $product_price = SETUP::getProductPriceOnTransferDate($date_to,$fetch1['product_id']);
                $price =  (!empty($product_price)) ? $product_price : $fetch1['sales_rate'] ;
                $product_value = $fetch1['current_stock']*$price;


                $content .= '<tr>';
                $content .= '<td>P'.$fetch1['code'].'</td>';
                $content .= '<td>'.$fetch1['product_name'].'</td>';
                $content .= '<td>'.$fetch1['unit'].'</td>';
                $content .= '<td>'.$fetch1['category'].'</td>';
                $content .= '<td>'.$price.'</td>';
                $content .= '<td>'.$fetch1['current_stock'].'</td>';
                $content .= '<td>'.$in_carton.'</td>';
                $content .= '<td>'.$product_value.'</td>';
                
                $content .= '</tr>';

                $total_product_value += number_format((float)$product_value, 2, '.', '') ; 
                
                
                 $total_ctn_value += number_format((float)$in_carton, 2, '.', '') ; 
                                  $total_pcs_value += number_format((float)$fetch1['current_stock'], 2, '.', '') ; 


            }
           
    
        }
    



    $content .= '</tbody> ';


    $content .= '<tfoot>
<tr>
    <th colspan="5" style="text-align:right"><b>Total</b></th>
            <th>' . number_format((float)( $total_pcs_value ), 2, '.', '') . '</th>

        <th>' . number_format((float)( $total_ctn_value ), 2, '.', '') . '</th>
    <th>' . number_format((float)( $total_product_value ), 2, '.', '') . '</th>
   
</tr>
</tfoot>



    </table>
    </div>
    </div>      
  </div>

</div>
</div>';

print   $content ;
exit();
    }

if($_POST['report_type'] == 'Current-Stock' || $_POST['report_type'] == 'FG-Category'  || $_POST['report_type'] == 'Finished-Goods' ){

    $content .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body">
           <div class="table-responsive">
        <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="MSalary" >
           ';

    $content .='<thead><tr>
    <th>Sl	</th>

    <th>Product Id	</th>
    <th>Product Name	</th>

    <th>Category</th>
    <th>Unit 	</th>

    <th>Sales Rate	</th>

    <th style="text-align:center;background-color:#c4c4f0" >Head Office Pcs</th>
    <th  style="text-align:center;background-color:#c4c4f0" >Head Office CTN</th>
    <th  style="text-align:center;background-color:#c4c4f0" >Head Office Tk</th>

    <th style="text-align:center;background-color:#bfdfda" >Patuatuli Pcs</th>
    <th  style="text-align:center;background-color:#bfdfda" >Patuatuli CTN</th>
    <th  style="text-align:center;background-color:#bfdfda" >Patuatuli Tk</th>

    <th  style="text-align:center;background-color:#efc996" >Nowabpur Pcs</th>
    <th style="text-align:center;background-color:#efc996" >Nowabpur CTN</th>
    <th style="text-align:center;background-color:#efc996" >Nowabpur Tk</th>


    <th style="text-align:center;">Warehouse</th>
    <th  style="text-align:center;">Total Pcs</th>
    <th  style="text-align:center;">Total CTN</th>
    <th  style="text-align:center;">Total Tk</th>

    </tr></thead>';


    
// Prepare all queries and execute them once

$current_stock = 0; 
$total_carton = 0 ;
$total_product_value = 0 ;




$query1 = $conn_me->prepare("
    SELECT 
        A.in_service, 
        C.category, 
        B.unit, 
        A.id, 
        A.pcs_in_cartoon, 
        A.code, 
        A.product_name, 
        A.sales_rate 
    FROM `setup_product` A 
    JOIN setup_unit B ON A.unit_id = B.id 
    JOIN setup_category C ON A.category_id = C.id 
    $QUERY

");
$query1->execute();
$fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);

$result = array();

// Prepare batch queries for stock info
foreach ($fetch_list1 as $fetch1) {

    $productId = $fetch1['id'];
    $product_price = SETUP::getProductPriceOnTransferDate($date_to,$productId);
    $price =  (!empty($product_price)) ? $product_price : $fetch1['sales_rate'] ;

    // Fetch brunch-related warehouse info in a single query
$brunchIds = [1, 2, 3];
$brunchQueries = [];

foreach ($brunchIds as $brunchId) {

    $brunchWarehouseIds = SETUP::getWarehouseIds($brunchId);


    $brunchQueries[] = "
        SELECT
            '$brunchId' AS brunch_id,
            COALESCE(SUM(stock_in - stock_out), 0) AS stock
        FROM balance_product
        WHERE product_id = '$productId' AND
              warehouse_id IN ($brunchWarehouseIds) AND date <= '".$date_to."'
        GROUP BY product_id 
      
    ";
}





$combinedBrunchQuery = implode(' UNION ALL ', $brunchQueries);
$combinedBrunch = $conn_me->query($combinedBrunchQuery)->fetchAll(PDO::FETCH_ASSOC);


$result[] = array(
    'product_id' => $productId,
    'code' => $fetch1['code'],
    'product_name' => $fetch1['product_name'],
    'pcs_in_cartoon' => $fetch1['pcs_in_cartoon'],
    'unit' => $fetch1['unit'],
    'category' => $fetch1['category'],
    'sales_rate' => $price,
    'code' => $fetch1['code'],
    'brunch_stock' => $combinedBrunch

    
);


}



$sl = 1; 
$count = 0 ; 


$total_h_value = 0 ;
$total_p_value = 0 ;
$total_n_value = 0 ;

$total_h_pcs = 0 ;
$total_p_pcs = 0 ;
$total_n_pcs = 0 ;
$all_ctn = 0 ;
$all_value = 0;
$all_pcs = 0; 
foreach ($result as $key => $value) {

    $productId = $value['product_id'];


    $content .= '<input type="text" style="display:none" id="product_id' . $productId . '" value="' . $productId . '" >';

    $content .= '<tr>';
    $content .= '<td>' . $sl++ . '</td>';
    $content .= '<td>P' . $value['code'] . '</td>';

    $content .= '<td>' . $value['product_name'] . '</td>';
    $content .= '<td>' . $value['category'] . '</td>';
    $content .= '<td>' . $value['unit'] . '</td>';
    $content .= '<td>' . number_format((float)$value['sales_rate'], 2, '.', '') . '</td>';


    $brunchStocks = [1 => 0, 2 => 0, 3 => 0];

    foreach ($value['brunch_stock'] as $brunch) {
        if (in_array($brunch['brunch_id'], [1, 2, 3])) {
            $brunchStocks[$brunch['brunch_id']] = $brunch['stock'];
        }

    }


  

    $current_stock = 0;
    $total_carton = 0 ;
    $total_product_value = 0 ;

    foreach ($brunchStocks as $brunch_id => $stock) {

        
        if ($value['pcs_in_cartoon'] != 0) { 
            $carton = round($stock / ($value['pcs_in_cartoon'] ?? 0), 2);
        }


         $product_value = $stock*$value['sales_rate'];
  
         $color1 = ($brunch_id == 1 ) ? ' #c4c4f0' : '' ;
         $color2 = ($brunch_id == 2 ) ? ' #bfdfda' : '' ;
         $color3 = ($brunch_id == 3 ) ? ' #efc996' : '' ;
 
      



        $content .= '<td style="background-color:'.$color1. $color2 . $color3. '">' . number_format((float)$stock, 2, '.', '') . '</td>';
        $content .= '<td style="background-color:'.$color1. $color2 . $color3. '">' . $carton . '</td>';
        $content .= '<td style="background-color:'.$color1. $color2 . $color3. '">' . $product_value . '</td>';

        $current_stock += number_format((float)$stock, 2, '.', '') ; 
        $total_carton += number_format((float)$carton, 2, '.', '') ; 
        $total_product_value += number_format((float)$product_value, 2, '.', '') ; 


          
        $total_h_value += ($brunch_id == 1 ) ? $product_value : 0  ;
        $total_p_value += ($brunch_id == 2 ) ? $product_value : 0  ;
        $total_n_value += ($brunch_id == 3 ) ? $product_value : 0  ;

        $total_h_pcs += ($brunch_id == 1 ) ? $stock : 0  ;
        $total_p_pcs += ($brunch_id == 2 ) ? $stock : 0  ;
        $total_n_pcs += ($brunch_id == 3 ) ? $stock : 0  ;

        
    }


    $content .= '<td>
        <button type="button" class="btn btn-warning" onclick="modal_wihout_refresh(\'load_data_in_modal\',\'product_id' . $productId . '\',\'Warehouse List\');">Branch</button>
        <button type="button" class="btn btn-info" onclick="modal_wihout_refresh(\'load_data_in_modal\',\'product_id' . $productId . '\',\'Central Warehouse List\');">Central</button>
    </td>';
    
    $content .= '<td >' . number_format((float)$current_stock, 2, '.', '') . '</td>';
    $content .= '<td>' . $total_carton . '</td>';
    $content .= '<td>' . $total_product_value . '</td>';

    $content .= '</tr>';
    $count++ ;
    $all_value += $total_product_value ;
    $all_pcs += $current_stock ;
    $all_ctn +=  $total_carton ;

}


$content .= '<tfoot>
<tr>
    <th colspan="6" style="text-align:right"><b>Total</b></th>
    <th style="text-align:center;background-color:#c4c4f0">' . number_format((float)( $total_h_pcs ), 2, '.', '') . '</th>
    <th style="text-align:center;background-color:#c4c4f0"></th>
    <th style="text-align:center;background-color:#c4c4f0">' . number_format((float)( $total_h_value ), 2, '.', '') . '</th>
    <th style="text-align:center;background-color:#bfdfda">' . number_format((float)( $total_p_pcs ), 2, '.', '') . '</th>
    <th style="text-align:center;background-color:#bfdfda"></th>
    <th style="text-align:center;background-color:#bfdfda">' . number_format((float)( $total_p_value ), 2, '.', '') . '</th>
    <th style="text-align:center;background-color:#efc996">' . number_format((float)( $total_n_pcs ), 2, '.', '') . '</th>
    <th style="text-align:center;background-color:#efc996"></th>
    <th style="text-align:center;background-color:#efc996">' . number_format((float)( $total_n_value ), 2, '.', '') . '</th>

    <th ></th>
    <th>' . number_format((float)( $all_pcs ), 2, '.', '') . '</th>
    <th>' . number_format((float)( $all_ctn ), 2, '.', '') . '</th>
    <th>' . number_format((float)( $all_value ), 2, '.', '') . '</th>

</tr>
</tfoot>';


$content .= '</table>
</div>
</div>
</div></div></div>';



print   $content ;
exit();
}
    
   


}else if ( $_POST['action'] == 'Profit-Loss-Report'){

    
    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));

    if($_POST['branch_id'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND A.`brunch_id` = '".$_POST['branch_id']."'  ";
                        

     }





    $content = '';




    $content .= '<div class="row">';
    $content .= '<div class="col-sm-12">';
    $content .= '<table class="table">';


    $content .= '<tr>';
    $content.= '<th colspan="2" style="text-align:center"> Profit & Loss Report '.$_POST['date_from'].' >>> To '.$_POST['date_to'].'
 </th>';
    $content .= '</tr>';



    $content .= '</table>';
    $content .= '</div>';
    $content .= '</div>';


    $content .= '<div class="row">';
    $content .= '<div class="col-sm-6">';
    $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';


    $content .= '<tr>';
    $content.= '<td colspan="2" style="text-align:left"> # REVENUE  </td>';
    $content .= '</tr>';

    $SALES_REVENUE = FIND::SALES_REVENUE($date_from,$date_to,$_POST['branch_id']);

    $content .= '<tr>';
    $content.= '<td style="padding-left:25px;">Sales Revenue</td>';
    $content.= '<td style="text-align:right">'.number_format((float)$SALES_REVENUE['balance'], 2, '.', '').'</td>';
    $content .= '</tr>';


    $OTHER_REVENUE = FIND::OTHER_REVENUE($date_from,$date_to,$_POST['branch_id']);

    $content .= '<tr>';
    $content.= '<td style="padding-left:25px;">Other Revenue</td>';
    $content.= '<td style="text-align:right">'.number_format((float)$OTHER_REVENUE['balance'], 2, '.', '').'</td>';
    $content .= '</tr>';

    $gross_revenue =number_format((float)($SALES_REVENUE['balance']+$OTHER_REVENUE['balance']), 2, '.', '');


    $content .= '<tr>';
    $content.= '<th style="text-align:right">Gross Revenue</th>';
    $content.= '<th style="text-align:right">'.number_format((float)($gross_revenue), 2, '.', '').'</th>';
    $content .= '</tr>';



    $content .= '<tr>';
    $content.= '<td colspan="2" style="text-align:left"> # COST OF GOODS SOLD  </td>';
    $content .= '</tr>';


    $COST_OF_GOODS = FIND::COST_OF_GOODS($date_from,$date_to,$_POST['branch_id']);


    $content .= '<tr>';
    $content.= '<td style="padding-left:25px;" >GOODS COST</td>';
    $content.= '<td style="text-align:right">'.number_format((float)$COST_OF_GOODS['total_purchase_price'], 2, '.', '').'</td>';
    $content .= '</tr>';


    $content .= '<tr>';
    $content.= '<th style="text-align:right">GROSS PROFIT</th>';
    $content.= '<th style="text-align:right">'.number_format((float)($gross_revenue - $COST_OF_GOODS['total_purchase_price']), 2, '.', '').'</th>';
    $content .= '</tr>';




    $content .= '</table>';
    $content .= '</div>';


    $content .= '<div class="col-sm-6">';
    $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';


    $content .= '<tr>';
    $content.= '<td colspan="2" style="text-align:left"> # EXPENSE  </td>';
    $content .= '</tr>';

    $total_expense = 0;
    $qry1 = $conn_me->prepare("SELECT  C.`name`,SUM(A.out_amount) AS `total_out` 
    FROM account_transection A
    JOIN `setup_ladger_head` C ON (A.`ledger_id` = C.`id`)
    
      where A.`transection_type` = 'EXPENSE' AND ( A.transection_date BETWEEN '". $date_from ."' AND '". $date_to ."')   $BRUNCH_QUERY GROUP BY A.`ledger_id`  ");
    $qry1->execute();
    $fetch_list11 = $qry1->fetchAll(PDO::FETCH_ASSOC);
    foreach($fetch_list11 AS $fetch22){

        $content .= '<tr>';
        $content.= '<td style="padding-left:25px;">'.$fetch22['name'].'</td>';
        $content.= '<td style="text-align:right">'.number_format((float)$fetch22['total_out'], 2, '.', '').'</td>';
        $content .= '</tr>';


        $total_expense += number_format((float)$fetch22['total_out'], 2, '.', '');
    }


    $content .= '<tr>';
    $content.= '<th style="text-align:right">TOTAL EXPENSE </th>';
    $content.= '<th style="text-align:right">'.number_format((float)($total_expense), 2, '.', '').'</th>';
    $content .= '</tr>';



    $content .= '<tr>';
    $content.= '<th style="text-align:right">NET INCOME </th>';
    $content.= '<th style="text-align:right">'.number_format((float)(($gross_revenue - $COST_OF_GOODS['total_purchase_price']) - $total_expense ), 2, '.', '').'</th>';
    $content .= '</tr>';




    $content .= '</table>';
    $content .= '</div>';


    
    $content .= '</div>';



    print     $content ;




}else if ( $_POST['action'] == 'Day Book Report'){

    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));

    $content = '';


    $content .= '<div class="row">';
    $content .= '<div class="col-sm-12">';
    $content .= '<table class="table">';


    $content .= '<tr>';
    $content.= '<th colspan="2" style="text-align:center">
    <input type="button" class="btn btn-info hidden-print" onclick="subtractDay(\''.$_POST['action'].'\')" value="<<<">  Day Book Report of '.$_POST['date_from'].' 
    <input type="button" class="btn btn-info hidden-print" onclick="addDay(\''.$_POST['action'].'\')" value=">>>"> </th>';
    $content .= '</tr>';

    $content .= '<tr>';
    $content.= '<th colspan="2" style="text-align:center"><input type="button" class="btn btn-info hidden-print" onclick="SimplePrint()" value="Print-Me"></th>';
    $content .= '</tr>';


    $content .= '</table>';
    $content .= '</div>';
    $content .= '</div>';


    if($_POST['EXTRAFILED'] == 'All' || $_POST['EXTRAFILED'] == '1' ){
        $content .=     TEST_BOOK::DAY_BOOK_HEAD_OFFICE($date_from,$_POST['EXTRAFILED']);

    }else{
        $content .=     TEST_BOOK::DAY_BOOK_NOWABPUR_AND_PATUWATULI_BRUNCH($date_from,$_POST['EXTRAFILED']);

    }


    print     $content ;



}else if ( $_POST['action'] == 'Cash Statement Report'){


    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


    if($_POST['EXTRAFILED'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND  `brunch_id` = '".$_POST['EXTRAFILED']."' ";
     }



     
    $content = '';



    $content .= '<div class="row">';

    $content .= '<div class="col-sm-6">';

    $total_cash_in1 = 0;
    // Sales 
    $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
    $content .= '<tr><td colspan="4" class="text-center" style="background-color:#8eb1544f">Sales</td></tr>';
    $content .= '<tr>';
    $content .= '<td>Invoice</td>';
    $content .= '<td>Date</td>';
    $content .= '<td>Customer</td>';
    $content .= '<td>Invoice Price</td>';
    $content .= '</tr>';

    $query1 = $conn_me->prepare("SELECT `code` FROM `sales_invoice` where `generate_challan` = 'Done' AND (`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."')   $BRUNCH_QUERY  "); 
    $query1->execute();
    $count1 = $query1->rowCount();
    if($count1 > 0 ){
        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){ 

            $info1 = SETUP::SETUP_SALES_INVOICE($fetch1['code']);

            $content .= '<tr>';
            $content .= '<td>'.$info1['invoice_no'].'</td>';
            $content .= '<td>'.$info1['invoice_date'].'</td>';
            $content .= '<td>'.$info1['customer_name'].'</td>';
            $content .= '<td>'.$info1['total_invoice_price'].'</td>';
            $content .= '</tr>';
            $total_cash_in1 += $info1['total_invoice_price'];
        }
        $content .= '<tr><td colspan="3" class="text-center">Total</td><td><b>'. $total_cash_in1.'</b></td></tr>';

        }else{
            $content .= '<tr><td colspan="4" class="text-center" style="background-color:#8eb1544f">no data found</td></tr>';
    
            }


    $content .= '</table>';
     // Received from Customers ends

    
     $total_cash_in2 = 0;
    // Received from Customers  
    $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
    $content .= '<tr><td colspan="4" class="text-center" style="background-color:#8eb1544f">Received from Customers</td></tr>';
    $content .= '<tr>';
    $content .= '<td>Invoice</td>';
    $content .= '<td>Date</td>';
    $content .= '<td>Customer</td>';
    $content .= '<td>Received</td>';
    $content .= '</tr>';

    $query2 = $conn_me->prepare("SELECT `id` FROM `account_transection` where `in_amount` > 0 AND  `transection_to` = 'Customer' AND `transection_type` = 'INCOME' AND (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."')  $BRUNCH_QUERY  "); 
    $query2->execute();
    $count2 = $query2->rowCount();
    if($count2 > 0 ){
        $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list2 AS $fetch2){ 
            $info2 = SETUP::ACCOUNT_TRANSECTION($fetch2['id']);

            $content .= '<tr>';
            $content .= '<td>'.$info2['invoice_no'].'</td>';
            $content .= '<td>'.$info2['transectiondate'].'</td>';
            $content .= '<td>'.$info2['details_of_transection_to'].'</td>';
            $content .= '<td>'.$info2['in_amount'].'</td>';
            $content .= '</tr>';
            $total_cash_in2 += $info2['in_amount'];
        }
        $content .= '<tr><td colspan="3" class="text-center">Total</td><td><b>'. $total_cash_in2.'</b></td></tr>';

        }else{
            $content .= '<tr><td colspan="4" class="text-center" style="background-color:#8eb1544f">no data found</td></tr>';
    
            }


    $content .= '</table>';
     // Received from Customers ends



     
     $total_cash_in3 = 0;
    // Received from Suppliers
    $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
    $content .= '<tr><td colspan="4" class="text-center" style="background-color:#8eb1544f">Received from Supplier</td></tr>';
    $content .= '<tr>';
    $content .= '<td>Invoice</td>';
    $content .= '<td>Date</td>';
    $content .= '<td>Supplier</td>';
    $content .= '<td>Received</td>';
    $content .= '</tr>';

    $query3 = $conn_me->prepare("SELECT `id` FROM `account_transection` where  `transection_to` = 'Supplier' AND  `transection_type` = 'INCOME' AND  (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."') $BRUNCH_QUERY  "); 
    $query3->execute();
    $count3 = $query3->rowCount();
    if($count3 > 0 ){
        $fetch_list3 = $query3->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list3 AS $fetch3){ 
            $info3 = SETUP::ACCOUNT_TRANSECTION($fetch3['id']);

            $content .= '<tr>';
            $content .= '<td>'.$info3['invoice_no'].'</td>';
            $content .= '<td>'.$info3['transectiondate'].'</td>';
            $content .= '<td>'.$info3['details_of_transection_to'].'</td>';
            $content .= '<td>'.$info3['in_amount'].'</td>';
            $content .= '</tr>';
            $total_cash_in3 += $info3['in_amount'];
        }
        $content .= '<tr><td colspan="3" class="text-center">Total</td><td><b>'. $total_cash_in3.'</b></td></tr>';

        }else{
            $content .= '<tr><td colspan="4" class="text-center" style="background-color:#8eb1544f">no data found</td></tr>';
    
            }


    $content .= '</table>';
     // Received from Customers ends


     $total_cash_in4 = 0;
     // Cash Received
     $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
     $content .= '<tr><td colspan="4" class="text-center" style="background-color:#8eb1544f">Cash Received</td></tr>';
     $content .= '<tr>';
     $content .= '<td>Transaction Id</td>';
     $content .= '<td>Date</td>';
     $content .= '<td>Account Name</td>';
     $content .= '<td>Received</td>';
     $content .= '</tr>';
 
     $query4 = $conn_me->prepare("SELECT `id`,`transection_head_id`,`ledger_id` FROM `account_transection` where  `transection_to` = 'Account Head' AND `transection_by` = 'Cash' AND `transection_type` = 'INCOME' AND  (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."') $BRUNCH_QUERY  "); 
     $query4->execute();
     $count4 = $query4->rowCount();
     if($count4 > 0 ){
         $fetch_list4 = $query4->fetchAll(PDO::FETCH_ASSOC);
         foreach($fetch_list4 AS $fetch4){ 
             $info4 = SETUP::ACCOUNT_TRANSECTION($fetch4['id']);
             $head_info = SETUP::ACCOUNT_HEAD_SETUP($fetch4['transection_head_id']);
             $led_info = SETUP::SETUP_LEDGER($fetch4['ledger_id']);

             $content .= '<tr>';
             $content .= '<td>'.$info4['invoice_no'].'</td>';
             $content .= '<td>'.$info4['transectiondate'].'</td>';
             $content .= '<td>'.$led_info['fetch']['name'].' > '.$head_info['account_head'].'</td>';
             $content .= '<td>'.$info4['in_amount'].'</td>';
             $content .= '</tr>';
             $total_cash_in4 += $info4['in_amount'];
         }
         $content .= '<tr><td colspan="3" class="text-center">Total</td><td><b>'. $total_cash_in4.'</b></td></tr>';
 
         }else{
             $content .= '<tr><td colspan="4" class="text-center" style="background-color:#8eb1544f">no data found</td></tr>';
     
             }
 
 
     $content .= '</table>';
      // Received from Customers ends


      $total_cash_in5 = 0;
      //Bank Deposits

      $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
      $content .= '<tr><td colspan="6" class="text-center" style="background-color:#8eb1555f">Bank Deposits</td></tr>';
      $content .= '<tr>';
      $content .= '<td>Sl</td>';
      $content .= '<td>Account Name	</td>';
      $content .= '<td>Account Number</td>';
      $content .= '<td>Bank Name</td>';
      $content .= '<td>Date</td>';
      $content .= '<td>Deposit</td>';

      $content .= '</tr>';
      
      $sl5 =1;
      $query5 = $conn_me->prepare("SELECT `id`,`in_amount`,`transection_by_id`,date_format(transection_date, '%d-%m-%Y') AS `transection_date` FROM `account_transection` where  `transection_to` = 'Account Head' AND `transection_by` = 'Bank' AND `transection_type` = 'INCOME' AND  (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."')  $BRUNCH_QUERY "); 
      $query5->execute();
      $count5 = $query5->rowCount();
      if($count5 > 0 ){
          $fetch_list5 = $query5->fetchAll(PDO::FETCH_ASSOC);
          foreach($fetch_list5 AS $fetch5){ 
              $bank_info = SETUP::BANK_SETUP($fetch5['transection_by_id']);
      
              $content .= '<tr>';
              $content .= '<td>'.$sl5++.'</td>';
              $content .= '<td>'.$bank_info['account_name'].'</td>';
              $content .= '<td>'.$bank_info['account_number'].'</td>';
              $content .= '<td>'.$bank_info['bank_name'].'</td>';
              $content .= '<td>'.$fetch5['transection_date'].'</td>';
              $content .= '<td>'.$fetch5['in_amount'].'</td>';
              $content .= '</tr>';
              $total_cash_in5 += $fetch5['in_amount'];
          }
          $content .= '<tr><td colspan="5" class="text-center">Total</td><td><b>'. $total_cash_in5.'</b></td></tr>';
      
          }else{
              $content .= '<tr><td colspan="6" class="text-center" style="background-color:#8eb1544f">no data found</td></tr>';
      
              }
      
      
      $content .= '</table>';
       // Bank Deposits ends
      

       $total_cash_in6 = 0;
       //Mobile Deposits
 
       $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
       $content .= '<tr><td colspan="5" class="text-center" style="background-color:#8eb1555f">Mobile Deposits</td></tr>';
       $content .= '<tr>';
       $content .= '<td>Sl</td>';
       $content .= '<td>Operator	</td>';
       $content .= '<td>Mobile Number</td>';
       $content .= '<td>Date</td>';
       $content .= '<td>Deposit</td>';
 
       $content .= '</tr>';
       
       $sl6 =1;
       $query6 = $conn_me->prepare("SELECT `id`,`in_amount`,`transection_by_id`,date_format(transection_date, '%d-%m-%Y') AS `transection_date` FROM `account_transection` where  `transection_to` = 'Account Head' AND `transection_by` = 'Mobile' AND  `transection_type` = 'INCOME' AND  (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."')  $BRUNCH_QUERY  "); 
       $query6->execute();
       $count6 = $query6->rowCount();
       if($count6 > 0 ){
           $fetch_list6 = $query6->fetchAll(PDO::FETCH_ASSOC);
           foreach($fetch_list6 AS $fetch6){ 
               $bank_info = SETUP::BANK_MOBILE_BANKING($fetch6['transection_by_id']);
       
               $content .= '<tr>';
               $content .= '<td>'.$sl6++.'</td>';
               $content .= '<td>'.$bank_info['mobile_bank_name'].'</td>';
               $content .= '<td>'.$bank_info['mobile_number'].'</td>';
               $content .= '<td>'.$fetch6['transection_date'].'</td>';
               $content .= '<td>'.$fetch6['in_amount'].'</td>';
               $content .= '</tr>';
               $total_cash_in6 += $fetch6['in_amount'];
           }
           $content .= '<tr><td colspan="5" class="text-center">Total</td><td><b>'. $total_cash_in6.'</b></td></tr>';
       
           }else{
               $content .= '<tr><td colspan="5" class="text-center" style="background-color:#8eb1544f">no data found</td></tr>';
       
               }
       
       
       $content .= '</table>';
        // Mobile Deposits ends
       
 
    $content .= '</div>';
    

    $content .= '<div class="col-sm-6">';

    $total_cash_out7 = 0;
    // FG Purchase 
    $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
    $content .= '<tr><td colspan="4" class="text-center" style="background-color:#f2b5b478">FG Purchases</td></tr>';
    $content .= '<tr>';
    $content .= '<td>Invoice</td>';
    $content .= '<td>Date</td>';
    $content .= '<td>Supplier</td>';
    $content .= '<td>Invoice Price</td>';
    $content .= '</tr>';

    $query7 = $conn_me->prepare("SELECT `code` FROM `fg_local_purches` where  (`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."')  GROUP BY  code "); 
    $query7->execute();
    $count7 = $query7->rowCount();
    if($count7 > 0 ){
        $fetch_list7 = $query7->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list7 AS $fetch7){ 

            $info7 = SETUP::SETUP_FG_LOCAL_PURCHASE_HISTORY($fetch7['code']);


            $content .= '<tr>';
            $content .= '<td>'.$info7['invoice_no'].'</td>';
            $content .= '<td>'.$info7['invoice_date'].'</td>';
            $content .= '<td>'.$info7['supplier_name'].'</td>';
            $content .= '<td>'.$info7['invoice_price'].'</td>';
            $content .= '</tr>';
            $total_cash_out7 += $info7['invoice_price'];
        }
        $content .= '<tr><td colspan="3" class="text-center">Total</td><td><b>'. $total_cash_out7.'</b></td></tr>';

        }else{
            $content .= '<tr><td colspan="4" class="text-center" style="background-color:#f2b5b478">no data found</td></tr>';
    
            }


    $content .= '</table>';
     // FG Purchase  ends

     $total_cash_out8 = 0;
     // RAW Purchase 
     $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
     $content .= '<tr><td colspan="4" class="text-center" style="background-color:#f2b5b478">RAW Purchases</td></tr>';
     $content .= '<tr>';
     $content .= '<td>Invoice</td>';
     $content .= '<td>Date</td>';
     $content .= '<td>Supplier</td>';
     $content .= '<td>Invoice Price</td>';
     $content .= '</tr>';
 
     $query8 = $conn_me->prepare("SELECT `code` FROM `raw_local_purches` where  (`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."')   "); 
     $query8->execute();
     $count8 = $query8->rowCount();
     if($count8 > 0 ){
         $fetch_list8 = $query8->fetchAll(PDO::FETCH_ASSOC);
         foreach($fetch_list8 AS $fetch8){ 
 
             $info8 = SETUP::SETUP_RAW_LOCAL_PURCHASE_HISTORY($fetch8['code']);
 
             $content .= '<tr>';
             $content .= '<td>'.$info8['invoice_no'].'</td>';
             $content .= '<td>'.$info8['invoice_date'].'</td>';
             $content .= '<td>'.$info8['supplier_name'].'</td>';
             $content .= '<td>'.$info8['invoice_price'].'</td>';
             $content .= '</tr>';
             $total_cash_out8 += $info8['invoice_price'];
         }
         $content .= '<tr><td colspan="3" class="text-center">Total</td><td><b>'. $total_cash_out8.'</b></td></tr>';
 
         }else{
             $content .= '<tr><td colspan="4" class="text-center" style="background-color:#f2b5b478">no data found</td></tr>';
     
             }
 
 
     $content .= '</table>';
      // RRaw Purchase  ends
 

      $total_cash_out9 = 0;
      // Paid to Customers
      $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
      $content .= '<tr><td colspan="4" class="text-center" style="background-color:#f2b5b478">Paid to Customers</td></tr>';
      $content .= '<tr>';
      $content .= '<td>Invoice</td>';
      $content .= '<td>Date</td>';
      $content .= '<td>Customer</td>';
      $content .= '<td>Paid</td>';
      $content .= '</tr>';
  
      $query9 = $conn_me->prepare("SELECT `id`,`transection_type` FROM `account_transection` where  (( `transection_to` = 'Customer' AND `transection_type` = 'EXPENSE' ) OR ( `transection_to` = 'Customer' AND `transection_type` = 'INCOME' AND `in_amount` < 0  ))  AND (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."') $BRUNCH_QUERY  "); 
      $query9->execute();
      $count9 = $query9->rowCount();
      if($count9 > 0 ){
          $fetch_list9 = $query9->fetchAll(PDO::FETCH_ASSOC);
          foreach($fetch_list9 AS $fetch9){ 
              $info9 = SETUP::ACCOUNT_TRANSECTION($fetch9['id']);
  
              if($info9['transection_type'] == 'EXPENSE' ){
                $target_amount = abs($info9['out_amount']);
              }else{
                $target_amount = abs($info9['in_amount']);
              }
              $content .= '<tr>';
              $content .= '<td>'.$info9['invoice_no'].'</td>';
              $content .= '<td>'.$info9['transectiondate'].'</td>';
              $content .= '<td>'.$info9['details_of_transection_to'].'</td>';
              $content .= '<td>'.$target_amount.'</td>';
              $content .= '</tr>';
              $total_cash_out9 += $target_amount;
          }
          $content .= '<tr><td colspan="3" class="text-center">Total</td><td><b>'. $total_cash_out9.'</b></td></tr>';
  
          }else{
              $content .= '<tr><td colspan="4" class="text-center" style="background-color:#f2b5b478">no data found</td></tr>';
      
              }
  
  
      $content .= '</table>';
       // Paid Customers ends
  
  
  
       
       $total_cash_out10 = 0;
      // Paid to Suppliers
      $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
      $content .= '<tr><td colspan="4" class="text-center" style="background-color:#f2b5b478">Paid To Supplier</td></tr>';
      $content .= '<tr>';
      $content .= '<td>Invoice</td>';
      $content .= '<td>Date</td>';
      $content .= '<td>Supplier</td>';
      $content .= '<td>Paid</td>';
      $content .= '</tr>';
  
      $query10 = $conn_me->prepare("SELECT `id` FROM `account_transection` where  `transection_to` = 'Supplier' AND  `transection_type` = 'EXPENSE' AND (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."')  $BRUNCH_QUERY "); 
      $query10->execute();
      $count10 = $query10->rowCount();
      if($count10 > 0 ){
          $fetch_list10 = $query10->fetchAll(PDO::FETCH_ASSOC);
          foreach($fetch_list10 AS $fetch10){ 
              $info10 = SETUP::ACCOUNT_TRANSECTION($fetch10['id']);
  
              $content .= '<tr>';
              $content .= '<td>'.$info10['invoice_no'].'</td>';
              $content .= '<td>'.$info10['transectiondate'].'</td>';
              $content .= '<td>'.$info10['details_of_transection_to'].'</td>';
              $content .= '<td>'.$info10['out_amount'].'</td>';
              $content .= '</tr>';
              $total_cash_out10 += $info10['out_amount'];
          }
          $content .= '<tr><td colspan="3" class="text-center">Total</td><td><b>'. $total_cash_out10.'</b></td></tr>';
  
          }else{
              $content .= '<tr><td colspan="4" class="text-center" style="background-color:#f2b5b478">no data found</td></tr>';
      
              }
  
  
      $content .= '</table>';
       // Paid to  supplier ends
         

       $total_cash_out11 = 0;
      // Paid to Employee
      $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
      $content .= '<tr><td colspan="4" class="text-center" style="background-color:#f2b5b478">Paid To Employee</td></tr>';
      $content .= '<tr>';
      $content .= '<td>Invoice</td>';
      $content .= '<td>Date</td>';
      $content .= '<td>Employee</td>';
      $content .= '<td>Paid</td>';
      $content .= '</tr>';
  
      $query11 = $conn_me->prepare("SELECT `id` FROM `account_transection` where  `transection_to` = 'Employee' AND  `transection_type` = 'EXPENSE' AND (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."') $BRUNCH_QUERY  "); 
      $query11->execute();
      $count11 = $query11->rowCount();
      if($count11 > 0 ){
          $fetch_list11 = $query11->fetchAll(PDO::FETCH_ASSOC);
          foreach($fetch_list11 AS $fetch11){ 
              $info11 = SETUP::ACCOUNT_TRANSECTION($fetch11['id']);
  
              $content .= '<tr>';
              $content .= '<td>'.$info11['invoice_no'].'</td>';
              $content .= '<td>'.$info11['transectiondate'].'</td>';
              $content .= '<td>'.$info11['details_of_transection_to'].'</td>';
              $content .= '<td>'.$info11['out_amount'].'</td>';
              $content .= '</tr>';
              $total_cash_out11 += $info11['out_amount'];
          }
          $content .= '<tr><td colspan="3" class="text-center">Total</td><td><b>'. $total_cash_out11.'</b></td></tr>';
  
          }else{
              $content .= '<tr><td colspan="4" class="text-center" style="background-color:#f2b5b478">no data found</td></tr>';
      
              }
  
  
      $content .= '</table>';
       // Paid to  Employee ends


       
$total_cash_out12 = 0;
// Cash Paid
$content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
$content .= '<tr><td colspan="4" class="text-center" style="background-color:#f2b5b478">Cash Paid</td></tr>';
$content .= '<tr>';
$content .= '<td>Transaction Id</td>';
$content .= '<td>Date</td>';
$content .= '<td>Account Name</td>';
$content .= '<td>Paid</td>';
$content .= '</tr>';

$query12 = $conn_me->prepare("SELECT `id`,`transection_head_id`,`ledger_id` FROM `account_transection` where  `transection_to` = 'Account Head' AND `transection_by` = 'Cash' AND `transection_type` = 'EXPENSE' AND  (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."')  $BRUNCH_QUERY "); 
$query12->execute();
$count12 = $query12->rowCount();
if($count12 > 0 ){
    $fetch_list12 = $query12->fetchAll(PDO::FETCH_ASSOC);
    foreach($fetch_list12 AS $fetch12){ 
        $info12 = SETUP::ACCOUNT_TRANSECTION($fetch12['id']);
        $head_info = SETUP::ACCOUNT_HEAD_SETUP($fetch12['transection_head_id']);
        $led_info = SETUP::SETUP_LEDGER($fetch12['ledger_id']);

        $content .= '<tr>';
        $content .= '<td>'.$info12['invoice_no'].'</td>';
        $content .= '<td>'.$info12['transectiondate'].'</td>';
        $content .= '<td>'.$led_info['fetch']['name'].' > '.$head_info['account_head'].'</td>';
        $content .= '<td>'.$info12['out_amount'].'</td>';
        $content .= '</tr>';
        $total_cash_out12 += $info12['out_amount'];
    }
    $content .= '<tr><td colspan="3" class="text-center">Total</td><td><b>'. $total_cash_out12.'</b></td></tr>';

    }else{
        $content .= '<tr><td colspan="4" class="text-center" style="background-color:#f2b5b478">no data found</td></tr>';

        }


$content .= '</table>';
 // Paid ends


 $total_cash_out13 = 0;
 //Bank Withdraw

 $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
 $content .= '<tr><td colspan="6" class="text-center" style="background-color:#f2b5b478">Bank Withdraws</td></tr>';
 $content .= '<tr>';
 $content .= '<td>Sl</td>';
 $content .= '<td>Account Name	</td>';
 $content .= '<td>Account Number</td>';
 $content .= '<td>Bank Name</td>';
 $content .= '<td>Date</td>';
 $content .= '<td>Withdraw</td>';

 $content .= '</tr>';
 
 $sl13 =1;
 $query13 = $conn_me->prepare("SELECT `id`,`out_amount`,`transection_by_id`,date_format(transection_date, '%d-%m-%Y') AS `transection_date` FROM `account_transection` where  `transection_to` = 'Account Head' AND `transection_by` = 'Bank' AND `transection_type` = 'EXPENSE' AND  (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."')  $BRUNCH_QUERY "); 
 $query13->execute();
 $count13 = $query13->rowCount();
 if($count13 > 0 ){
     $fetch_list13 = $query13->fetchAll(PDO::FETCH_ASSOC);
     foreach($fetch_list13 AS $fetch13){ 
         $bank_info = SETUP::BANK_SETUP($fetch13['transection_by_id']);
 
         $content .= '<tr>';
         $content .= '<td>'.$sl13++.'</td>';
         $content .= '<td>'.$bank_info['account_name'].'</td>';
         $content .= '<td>'.$bank_info['account_number'].'</td>';
         $content .= '<td>'.$bank_info['bank_name'].'</td>';
         $content .= '<td>'.$fetch13['transection_date'].'</td>';
         $content .= '<td>'.$fetch13['out_amount'].'</td>';
         $content .= '</tr>';
         $total_cash_out13 += $fetch13['out_amount'];
     }
     $content .= '<tr><td colspan="5" class="text-center">Total</td><td><b>'. $total_cash_out13.'</b></td></tr>';
 
     }else{
         $content .= '<tr><td colspan="6" class="text-center" style="background-color:#f2b5b478">no data found</td></tr>';
 
         }
 
 
 $content .= '</table>';
  // Bank withdraw ends
 

  $total_cash_out14 = 0;
  //Mobile Deposits

  $content .= '<table class="table table-hover table-condensed table-striped table-bordered">';
  $content .= '<tr><td colspan="5" class="text-center" style="background-color:#f2b5b478">Mobile Withdraws</td></tr>';
  $content .= '<tr>';
  $content .= '<td>Sl</td>';
  $content .= '<td>Operator	</td>';
  $content .= '<td>Mobile Number</td>';
  $content .= '<td>Date</td>';
  $content .= '<td>Withdraw</td>';

  $content .= '</tr>';
  
  $sl14 =1;
  $query14 = $conn_me->prepare("SELECT `id`,`out_amount`,`transection_by_id`,date_format(transection_date, '%d-%m-%Y') AS `transection_date` FROM `account_transection` where  `transection_to` = 'Account Head' AND `transection_by` = 'Mobile' AND  `transection_type` = 'EXPENSE' AND  (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."')  $BRUNCH_QUERY "); 
  $query14->execute();
  $count14 = $query14->rowCount();
  if($count14 > 0 ){
      $fetch_list14 = $query14->fetchAll(PDO::FETCH_ASSOC);
      foreach($fetch_list14 AS $fetch14){ 
          $bank_info = SETUP::BANK_MOBILE_BANKING($fetch14['transection_by_id']);
  
          $content .= '<tr>';
          $content .= '<td>'.$sl14++.'</td>';
          $content .= '<td>'.$bank_info['mobile_bank_name'].'</td>';
          $content .= '<td>'.$bank_info['mobile_number'].'</td>';
          $content .= '<td>'.$fetch14['transection_date'].'</td>';
          $content .= '<td>'.$fetch14['out_amount'].'</td>';
          $content .= '</tr>';
          $total_cash_out14 += $fetch14['out_amount'];
      }
      $content .= '<tr><td colspan="5" class="text-center">Total</td><td><b>'. $total_cash_out14.'</b></td></tr>';
  
      }else{
          $content .= '<tr><td colspan="5" class="text-center" style="background-color:#f2b5b478">no data found</td></tr>';
  
          }
  
  
  $content .= '</table>';
   // Mobile Deposits ends
  



    $content .= '</div>';
    $content .= '</div>';


    $total_in = $total_cash_in1+$total_cash_in2+$total_cash_in3+$total_cash_in4+$total_cash_in5+$total_cash_in6;
    $total_out = $total_cash_out7+$total_cash_out8+$total_cash_out9+$total_cash_out10+$total_cash_out11+$total_cash_out12+$total_cash_out13+$total_cash_out14;

    $content .= '<div class="row">';
    $content .= '<div class="col-sm-6">';
    $content .= '<input type="button" value="'.$total_in.'" class="form-control block">';
    $content .= '</div>';

    $content .= '<div class="col-sm-6">';
    $content .= '<input type="button" value="'.$total_out.'" class="form-control block">';

    $content .= '</div>';

    $content .= '</div>';


    $content .= '<div class="row">';
    $content .= '<div class="col-sm-12">';
    $content .= '<input type="button" value="'.number_format((float)($total_in-$total_out), 2, '.', '') .'" class="form-control block">';

    $content .= '</div>';

    $content .= '</div>';
    print   $content ;

}else if ( $_POST['action'] == 'Stock-With-Price'){


    $date_from = date("Y-m-d", strtotime($_POST['date_from']));

     if($_POST['branch_id'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " where `id` = '".$_POST['branch_id']."' ";
     }


  $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` $BRUNCH_QUERY ");
        $qry->execute();
        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
        $a = $fetch_list['related_warehouse'];
        $b = str_replace("[","",$a);
        $c = str_replace("]","",$b);
        
        
        if($_POST['branch_id'] == 'All' ){
          $QUERYTAG = " WHERE  `date` = '".$date_from."'  GROUP BY  `product_id` ";
        }else {
          $QUERYTAG = " WHERE `warehouse_id` IN ($c)  AND `date` = '".$date_from."'  GROUP BY `product_id` ";
        }
      
        
        

 $content = '';

    $content = '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
        <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">
            <thead>';

    $content .='<tr>
    <td>Sl</td>
    <td>Item</td>
    <td>RC Price</td>
    <td>Stock</td>
    ';



    $content .='</thead>
    <tbody>
    ';
$sl=1;
    $query1 = $conn_me->prepare("SELECT sum(`stock_in`) AS `stockin`,sum(`stock_out`) AS `stockout`,`product_id`  FROM `balance_product`   $QUERYTAG  "); 
    $query1->execute();
    $count = $query1->rowCount();
    if($count > 0 ){
        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){ 
            $info_product = SETUP::SETUP_PRODUCT($fetch1['product_id']);
            $stock =  number_format((float)($fetch1['stockin']- $fetch1['stockout']), 2, '.', '');
            
        $content .='<tr>
        <td>'.$sl++.'</td>
        <td>'.$info_product['product_name'].'</td>
        <td>'.$info_product['sales_rate'].'</td>
        <td>'.$stock.'</td>';

     

        $content .=' </tr>';
    }

       

        
    }else{
        $content .='<tr><td>No Repord Found</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
    }
    


    $content .= '</tbody> 
    </table>
    </div>
    </div>      
  </div>

</div>
</div>';


    print   $content;
    
    
}else if ( $_POST['action'] == 'Product Damage Report'){



    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));
    
    $movement_array = [];
    $damage_array = [];
    $count = 0;

    $ck131 = $conn_me->prepare("SELECT A.*,C.name  FROM `fg_damage_store` A 
JOIN 
admin B on (A.poster = B.id)
join
setup_employee C on (B.employee_id = C.id)
        where A.`status` = 'Done' AND  ( A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' )   ");
    $ck131->execute();
    if($ck131->rowCount()>0){
    
        $fe_ck31 = $ck131->fetchAll(PDO::FETCH_ASSOC);
        foreach($fe_ck31 AS $fetch31){
        if(!empty($fetch31['quantity'])){
              $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch31['warehouse_id']);
              $info_product = SETUP::SETUP_PRODUCT($fetch31['product_id']);

              
        if( is_null($fetch31['quantity']) || $fetch31['quantity'] == 0 ){
            $in_carton = 0;
        }else{
            $in_carton = $fetch31['quantity']/$info_product['pcs_in_cartoon'];

        }

if($fetch31['quantity'] > 0 ){
    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch31['invoice_date'];
    $movement_array[$count]['time'] = $fetch31['time'];
    $movement_array[$count]['qty'] = $fetch31['quantity'];
    $movement_array[$count]['carton'] = $in_carton;
    $movement_array[$count]['product_name'] = $info_product['product_name'];
    $movement_array[$count]['product_category'] = $info_product['category'];
    $movement_array[$count]['warehouse_name'] = $info_warehouse['name'];
    $movement_array[$count]['employee_name'] = $fetch31['name'];
    $movement_array[$count]['note'] = $fetch31['notes'];
    $movement_array[$count]['source'] = 'Store Damage';
}
          
        }    
       
    }
    
    }



$ck7 = $conn_me->prepare("SELECT A.*,C.name  FROM `damage_invoice_item` A 
JOIN 
admin B on (A.poster = B.id)
join
setup_employee C on (B.employee_id = C.id)
        where ( A.`warehouse_receive_date` BETWEEN '".$date_from."' AND '".$date_to."' )");
    $ck7->execute();
    
    $fe_ck7 = $ck7->fetchAll(PDO::FETCH_ASSOC);
    foreach($fe_ck7 AS $fetch7){
    
            $count  = $count+1;

            $info_product = SETUP::SETUP_PRODUCT($fetch7['product_id']);
            $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch7['warehouse_id']);


            if( is_null($fetch7['damage_quantity']) || $fetch7['damage_quantity'] == 0 ){
                $in_carton = 0;
            }else{
                $in_carton = $fetch7['damage_quantity']/$info_product['pcs_in_cartoon'];
    
            }

            if($fetch7['damage_quantity'] > 0 ){
            $movement_array[$count]['serial'] = $count;
            $movement_array[$count]['date'] = $fetch7['warehouse_receive_date'];
            $movement_array[$count]['time'] = $fetch7['time'];
            $movement_array[$count]['qty'] = $fetch7['damage_quantity'];
            $movement_array[$count]['carton'] = $in_carton;
            $movement_array[$count]['product_name'] = $info_product['product_name'];
            $movement_array[$count]['product_category'] = $info_product['category'];
            $movement_array[$count]['warehouse_name'] = $info_warehouse['name'];
            $movement_array[$count]['employee_name'] = $fetch7['name'];
            $movement_array[$count]['note'] = 'Invoice Wise Damage';
            $movement_array[$count]['source'] = 'Invoice Damage';
            }
    }
    
    

     $content = '';

     $content = '<div class="row mydivclass">
     <div class="col-md-12">
     
         <div class="panel panel-default">
             <div class="panel-body" id="load_table">
            <div class="table-responsive">
         <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">
             <thead>';
 
     $content .='<tr>
     <td>Sl</td>
     <td>Date</td>
     <td>Product Name</td>
     <td>Category</td>
     <td> Warehouse </td>
     <td>Total Pcs</td>
     <td>Total Carton</td>
     <td>Note</td>
     <td>Source</td>     <td>User</td>

  ';

 
     $content .='</thead>
     <tbody>
     ';
 $sl=1;

$date_asc = array_column($movement_array, 'date');
array_multisort($date_asc, SORT_ASC, $movement_array);


 $total_qty = 0 ;


         foreach($movement_array as $item) {

 

         $content .='<tr>
         <td>'.$sl++.'</td>
         <td>'.$item['date'].'</td>
         <td>'.$item['product_name'].'</td>
         <td>'.$item['product_category'].'</td>
         <td>'.$item['warehouse_name'].'</td>
         <td>'.$item['qty'].'</td>
         <td>'.$item['carton'].'</td>
         <td style="
        white-space: normal;
        word-wrap: break-word;
        max-width: 200px;">'.$item['note'].'</td>
         <td>'.$item['source'].'</td>
                  <td>'.$item['employee_name'].'</td>';
         $total_qty += $item['qty'];
       
     
     }
 
 
 
         $content .=' </tr>';
 
         
     
 
 
     $content .= '</tbody><tfoot> <tr><th colspan="5" style="text-align:right">Total</th><th>'.$total_qty.'</th></tr></tfoot>  
     </table>
     </div>
     </div>      
   </div>
 
 </div>
 </div>';

 print $content;

}else if ( $_POST['action'] == 'Product Transfer Report'){


    
    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


    $from_warehouse_id = implode(',', $_POST['from_warehouse_id']);
    $to_warehouse_id = implode(',', $_POST['to_warehouse_id']);



    $content = '';
     $content = '<div class="row mydivclass">
     <div class="col-md-12">
         <div class="panel panel-default">
             <div class="panel-body" id="load_table">
            <div class="table-responsive">
         <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">
             <thead>';
     $content .='<tr>
     <td>Sl</td>
     <td>Date</td>
     <td>Product Name</td>
     <td>Category</td>
    
     <td>Total Pcs</td>
     <td>Total Carton</td>
     <td>Product Price</td>
     <td>Total Price</td>
     <td>Saved By</td>
     <td>Dispatcher</td>
     <td>From (Warehouse)</td>
     <td>To (Warehouse)</td>
     <td>Status</td><td>Action</td>';

 
     $content .='</thead>
     <tbody>
     ';
 $sl=1;
 $total_price  = 0 ;
 $count = 0;
 $total_pcs  = 0; 
 $total_ctn  = 0; 
 $query1 = $conn_me->prepare("SELECT
 T.poster,T.id,
 A.product_name,A.pcs_in_cartoon,
 B.category,
 C.name as to_warehouse_name,
 D.name as from_warehouse_name,
 T.status,
 T.code,
 T.`id` AS transfer_id,
 T.`product_id`,
 date_format(T.invoice_date, '%d-%m-%Y') AS `invoicedate`,
 T.`invoice_date` AS transfer_date,
 T.`quantity` AS transfer_quantity,
 T.`FROM_warehouse_id` AS from_warehouse,
 T.`TO_warehouse_id` AS to_warehouse

FROM
 `fg_warehouse_to_warehouse_transfer` T

 JOIN
 `setup_product` A ON T.`product_id` = A.`id`

 JOIN
 `setup_category` B ON A.`category_id` = B.`id`

 JOIN
 `setup_warehouse` C ON T.`TO_warehouse_id` = C.`id`

 JOIN
 `setup_warehouse` D ON T.`FROM_warehouse_id` = D.`id`

WHERE
 ( T.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) 
 AND T.`FROM_warehouse_id` IN ({$from_warehouse_id})
 AND T.`TO_warehouse_id` IN ({$to_warehouse_id})
 "); 
 $query1->execute();
 
         $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
         foreach($fetch_list1 AS $fetch1){ 
         $count = $count + 1;

         $product_price = SETUP::getProductPriceOnTransferDate($fetch1['transfer_date'],$fetch1['product_id']);

         $product_value = $fetch1['transfer_quantity']*$product_price;

         $poster_info = SETUP::ADMIN_SETUP($fetch1['poster']);

         if( is_null($fetch1['transfer_quantity']) || $fetch1['transfer_quantity'] == 0 ){
            $in_carton = 0;
        }else{
            $in_carton = $fetch1['transfer_quantity']/$fetch1['pcs_in_cartoon'];

        }

         
 
         $content .= ' <input value="'.$fetch1['id'].'"  type="hidden" name="tr_id" id="tr_id'.$count.'"  class="form-control tr_id"/>';
 
     
         $content .='<tr>
         <td>'.$sl++.'</td>
         <td>'.$fetch1['invoicedate'].'</td>
         <td>'.$fetch1['product_name'].'</td>
         <td>'.$fetch1['category'].'</td>
        
         <td>'.$fetch1['transfer_quantity'].'</td>
         <td>'.$in_carton.'</td>
         <td>'.$product_price.'</td>
         <td>'.$product_value.'</td>
         <td>'.$poster_info['hr_name'].'</td>
         <td></td>
         <td>'.$fetch1['from_warehouse_name'].'</td>
         <td>'.$fetch1['to_warehouse_name'].'</td>';
       
         $content .='<td>'.$fetch1['status'].'</td>';
         $content .='<td><a href="print.php?print=FG-WAREHOUSE-TO-WAREHOUSE-TRANSFER-RECEIPT&code='.$fetch1['code'].'" target="_BLINK">Copy<a>   </td>';
         $total_price  += $product_value; 
         $total_pcs  += $fetch1['transfer_quantity']; 
         $total_ctn  += $in_carton; 


     }

 
 
         $content .=' </tr>';

     
 
 
     $content .= '</tbody> 

     <tfoot>
     <tr>
         <th colspan="4" style="text-align:right"><b>Total</b></th>
         <th>' . $total_pcs . '</th>
         <th>' . number_format((float)( $total_ctn ), 2, '.', '') . '</th>
         <th></th>

         <th>' . $total_price . '</th>
         <th></th>   <th></th>
     </tr>
 </tfoot>
     </table>
     </div>
     </div>      
   </div>
 
 </div>
 </div>';

 print $content;


}else if ( $_POST['action'] == 'find_discount_adjustment'){


    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


     if($_POST['branch_id'] == 'All' ){
        $brunch_name = "All";
     }else {
       $BRUNCH_QUERY = " AND A.`brunch_id` = '".$_POST['branch_id']."' ";
         $DATA = SETUP::SETUP_BRUNCH($_POST['branch_id']);
         $brunch_name = $DATA['brunch'];
     }



 $content = '';

$content .= '<h3 class="text-center">Discount Adjustment Report</h3>
<p class="text-center">Date From: '.date("d-m-Y", strtotime($_POST['date_from'])).' | Date To: '.date("d-m-Y", strtotime($_POST['date_to'])).'</p>
<p class="text-center">Branch: '.$brunch_name.'</p>';



    $content .= '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
        <table id="example" class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" >
            <thead>';

    $content .='<tr>
    <td>Date</td>
    <td>Code</td>
    <td>Customer Name</td>
    <td>Shop Name</td>
        <td>Entry By</td>
    <td>Notes</td>

    <td>Amount</td>
    
    <td>Action</td>';
  


    $content .='</tr></thead>
    <tbody>
    ';
$sl=1;
$adcount = 0;
$total_taka  = 0 ;




    $query2 = $conn_me->prepare("SELECT A.*, 
       CASE 
           WHEN A.poster IS NULL THEN ''
           ELSE C.name  -- Fetching real name from setup_employee
       END AS posterName
FROM balance_customer A
LEFT JOIN admin B ON A.poster = B.id
LEFT JOIN setup_employee C ON B.employee_id = C.id
WHERE A.note = 'DISCOUNT' AND (A.`date` BETWEEN  '".$date_from."' AND  '".$date_to."' ) $BRUNCH_QUERY "); 
    $query2->execute();
        $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list2 AS $fetch2){ 
            $adcount = $adcount + 1;
           $info = SETUP::SETUP_CUSTOMER($fetch2['customer_id']);

        $content .= ' <input value="'.$fetch2['id'].'"  type="text" style="display:none" name="tr_id" id="tr_id'.$adcount.'"  class="form-control tr_id"/>';

        $content .='<tr>
        <td>'.$fetch2['date'].'</td>
        <td>'.$info['customer_code'].'</td>
        <td>'.$info['customer_name'].'</td>
        <td>'.$info['shop_name'].'</td>
                <td>'.$fetch2['posterName'].'</td>
<td style="word-break: break-word; white-space: normal;"> ' .$fetch2['actual_note'].'
</td>
        <td>'.$fetch2['return_amount'].'</td>';

        $content .='<td><input type="button" class="btn btn-danger" value="x" onclick="delete_cart_row(\'Account/CUSTOMER-TRANSACTION/New\',\'balance_customer\',\''.$fetch2['id'].'\',\'Yes\');"></td>';


$total_taka  += $fetch2['return_amount'] ;

    }



        $content .=' </tr>';

        
      
     $content .='<tfoot>
     <tr>
         <th colspan="6" style="text-align:right"><b>Total</b></th>
         <th>' . number_format((float)( $total_taka ), 2, '.', '') . '</th>
         <th></th>

     </tr>
 </tfoot>';

    $content .= '</tbody> 


    </table>
    </div>
    </div>      
  </div>

</div>
</div>';



    print   $content;

}else if ( $_POST['action'] == 'find_due_adjustment'){


    
    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


     if($_POST['branch_id'] == 'All' ){
        $brunch_name = "All";
     }else {
       $BRUNCH_QUERY = " AND A.`brunch_id` = '".$_POST['branch_id']."' ";
         $DATA = SETUP::SETUP_BRUNCH($_POST['branch_id']);
         $brunch_name = $DATA['brunch'];
     }



 $content = '';

$content .= '<h3 class="text-center">Due Adjustment Report</h3>
<p class="text-center">Date From: '.date("d-m-Y", strtotime($_POST['date_from'])).' | Date To: '.date("d-m-Y", strtotime($_POST['date_to'])).'</p>
<p class="text-center">Branch: '.$brunch_name.'</p>';




    $content .= '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
        <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="example">
            <thead>';

    $content .='<tr>
    <td>Date</td>
    <td>Code</td>
    <td>Customer Name</td>
    <td>Shop Name</td>
    <td>Entry By</td>
        <td>Note</td>

    <td>Amount</td>

    <td>Action</td>';
  


    $content .='</thead>
    <tbody>
    ';
$sl=1;
$adcount = 0;$total_taka  = 0 ;


    $query2 = $conn_me->prepare("SELECT A.*, 
       CASE 
           WHEN A.poster IS NULL THEN ''
           ELSE C.name  -- Fetching real name from setup_employee
       END AS posterName
FROM balance_customer A
LEFT JOIN admin B ON A.poster = B.id
LEFT JOIN setup_employee C ON B.employee_id = C.id
WHERE A.note = 'LAST DUE' AND (A.`date` BETWEEN  '".$date_from."' AND  '".$date_to."' ) $BRUNCH_QUERY "); 
    $query2->execute();
        $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list2 AS $fetch2){ 
            $adcount = $adcount + 1;
           $info = SETUP::SETUP_CUSTOMER($fetch2['customer_id']);

        $date = date("d-m-Y", strtotime($fetch2['date']));


        $content .= ' <input value="'.$fetch2['id'].'"  type="text" style="display:none" name="tr_id" id="tr_id'.$adcount.'"  class="form-control tr_id"/>';

        $content .='<tr>
        <td>'.$date.'</td>
        <td>'.$info['customer_code'].'</td>
        <td>'.$info['customer_name'].'</td>
        <td>'.$info['shop_name'].'</td>
        <td>'.$fetch2['posterName'].'</td>
<td style="word-break: break-word; white-space: normal;"> ' .$fetch2['actual_note'].'
</td>

        <td>'.$fetch2['invoice_amount'].'</td>';

        $content .='<td><input type="button" class="btn btn-danger" value="x" onclick="delete_cart_row(\'Account/CUSTOMER-TRANSACTION/New\',\'balance_customer\',\''.$fetch2['id'].'\',\'Yes\');"></td>';

$total_taka  += $fetch2['invoice_amount'] ;


    }



        $content .=' </tr>';

        
            
     $content .='<tfoot>
     <tr>
         <th colspan="6" style="text-align:right"><b>Total</b></th>
         <th>' . number_format((float)( $total_taka ), 2, '.', '') . '</th>
         <th></th>

     </tr>
 </tfoot>';


    $content .= '</tbody> 


    </table>
    </div>
    </div>      
  </div>

</div>
</div>';



    print   $content;
    



}else if ( $_POST['action'] == 'Transaction Summary'){


    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


     if($_POST['branch_id'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND A.`brunch_id` = '".$_POST['branch_id']."' ";
     }



   if($_POST['report_type'] == 'MONEY-TRANSFER'){

    $content = '';

    $content = '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
        <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">
            <thead>';

    $content .='<tr>
    <td>Sl</td>
    <td>Date</td>
    <td>Saved By</td>
    <td>Transfer Type</td>
    <td>From Branch</td>
    <td>Amount</td>
    <td>From Account</td>
    <td>To Branch</td>
    <td>To Account</td>
    <td>Narration</td>';
    if ( $_SESSION['USER_TYPE'] == 'Admin') {
            $content .='<td>Action</td>';

    }else{
        $content .='<td></td>';

    }


    $content .='</thead>
    <tbody>
    ';
$sl=1;
$count = 0;
$out_amount = 0;
    $query1 = $conn_me->prepare("SELECT A.`id`,A.`transection_id`  FROM `account_transection` A

    where  A.`data_inserted_from` = 'MONEY-TRANSFER-TO' AND (A.`transection_date` BETWEEN  '".$date_from."' AND  '".$date_to."' ) $BRUNCH_QUERY "); 
    $query1->execute();
    $count = $query1->rowCount();
        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){ 
        $count = $count + 1;
        $tr_to = SETUP::ACCOUNT_TRANSECTION($fetch1['id']);
        $tr_from = SETUP::ACCOUNT_TRANSECTION($fetch1['transection_id']);
            
        $poster_info = SETUP::ADMIN_SETUP($tr_from['poster']);

        $content .= ' <input value="'.$fetch1['id'].'" style="display:none"  type="number" name="tr_id" id="tr_id'.$count.'"  class="form-control tr_id"/>';

            $transection_date = date("d-m-Y", strtotime($tr_from['transection_date']));

        $content .='<tr>
        <td>'.$sl++.'</td>
        <td>'.$transection_date.'  '.$tr_from['time'].'</td>
        <td>'.$poster_info['hr_name']. '</td>
        <td>'.$tr_from['transection_by']. '  <span class="glyphicon glyphicon-random text-danger"></span>  '.$tr_to['transection_by']. '  </td>
        <td>'.$tr_from['brunch_name'].'</td>
        <td>'.$tr_from['out_amount'].'</td>';
        if($tr_from['transection_by'] == 'Cash' ){ $content .='<td>Cash</td>'; }else{
            $content .='  <td>'.$tr_from['bank_name'].' '.$tr_from['check_no'].' '.$tr_from['check_date'].' '.$tr_from['mobile_banking_no'].'</td>';
        }
        $content .='<td>'.$tr_to['brunch_name'].'</td>';
        if($tr_to['transection_by'] == 'Cash' ){ $content .='<td>Cash</td>'; }else{
        $content .=' <td>'.$tr_to['bank_name'].' '.$tr_to['check_no'].' '.$tr_to['check_date'].' '.$tr_to['mobile_banking_no'].'</td>';
        }
        $content .='<td>'.$tr_from['note'].'</td>';
        
    
  if ( $_SESSION['USER_TYPE'] == 'Admin') {

    $content .='<td><input type="button" class="btn btn-danger" value="x" onclick="delete_money_transfer(\'tr_id'.$count.'\');"></td>';

}else{
    $content .='<td></td>';

}
$out_amount += $tr_from['out_amount'];
    }



        $content .=' </tr>';

        

    


    $content .= '</tbody> 

    <tfoot>
    <tr>
        <th colspan="5" style="text-align:right"><b>Total</b></th>
        <th>' . $out_amount . '</th>
        <th></th>
        <th></th>   <th></th>
    </tr>
</tfoot>


    </table>
    </div>
    </div>      
  </div>

</div>
</div>';


}else{

    if($_POST['report_type'] == 'EXPENSE' || $_POST['report_type'] == 'INCOME' ){

        $QUERY =   " A.`transection_type` = '".$_POST['report_type']."' AND B.`special_id` =  'NO'  $BRUNCH_QUERY  ";

    }else{

        $QUERY =   " A.`ledger_id` = '".$_POST['ledger_id']."' $BRUNCH_QUERY ";

    }
 $content = '';

    $content = '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
        <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">
            <thead>';

    $content .='<tr>
    <td>Tr.Id</td>
    <td>Tr. Date</td>
    <td>Tr. Type</td>
    <td>Led. Head</td>
    <td>Acc. Head</td>
    <td>Note</td>
    <td>Receive</td>
    <td>Payment</td>
    <td>Sales By</td>';
    if ( $_SESSION['USER_TYPE'] == 'Admin') {
            $content .='<td>Action</td>';

    }else{
        $content .='<td></td>';

    }


    $content .='</thead>
    <tbody>
    ';
$sl=1;
$count = 0;
$total_in = 0 ;
$total_out = 0;
    $query1 = $conn_me->prepare("SELECT A.`poster`,A.`id`,B.`account_head` FROM `account_transection` A 
    JOIN `setup_ac_head` B ON (A.`transection_head_id` = B.`id`)
    where $QUERY AND (A.`transection_date` BETWEEN  '".$date_from."' AND  '".$date_to."' ) "); 
    $query1->execute();
    $count = $query1->rowCount();
        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){ 
            $count = $count + 1;
        $info_head = SETUP::ACCOUNT_TRANSECTION($fetch1['id']);
        $poster_info = SETUP::ADMIN_SETUP($fetch1['poster']);

        $content .= ' <input value="'.$fetch1['id'].'"  type="text" style="display:none" name="tr_id" id="tr_id'.$count.'"  class="form-control tr_id"/>';

            $transection_date = date("d-m-Y", strtotime($info_head['transection_date']));


        $content .='<tr>
        <td>'.$info_head['invoice_no'].'</td>
        <td>'.$transection_date.'  '.$info_head['time'].'</td>
        <td>'.$info_head['transection_by']. ' ' . $info_head['bank_name'] .' ' .  $info_head['check_no'] . ' ' . $info_head['check_date'] . ' '. $info_head['mobile_banking_no'] . '</td>
        <td>'.$info_head['ladger_name'].'</td>
        <td>'.$fetch1['account_head'].'<br>'.$info_head['details_of_transection_to'].'</td>
        <td>'.$info_head['note'].'</td>';
  
  if($_POST['report_type'] == 'CUSTOMER-TRANSACTION' ){

    if($info_head['in_amount'] > 0 ){
        $content .='<td>'.$info_head['in_amount'].'</td>';
        $content .='<td>0.00</td>';
    
    }else{
        $content .='<td>0.00</td>';
        $content .='<td>'.abs($info_head['in_amount']).'</td>';
    
    }
         
  } else{

        $content .='<td>'.abs($info_head['in_amount']).'</td>';
        $content .='<td>'.abs($info_head['out_amount']).'</td>';

  }      
     
      
  $content .='<td>'.$poster_info['hr_name'].'</td>';

  if ( $_SESSION['USER_TYPE'] == 'Admin') {

    $content .='<td><input type="button" class="btn btn-danger" value="x" onclick="delete_total_transection(\'tr_id'.$count.'\');"></td>';

}else{
    $content .='<td></td>';

}

$total_in += $info_head['in_amount'] ;
$total_out += $info_head['out_amount'];


    }



        $content .=' </tr>';

        
      


    $content .= '</tbody> 
    <tfoot>
    <tr>
        <th colspan="6" style="text-align:right"><b>Total</b></th>
        <th>' . $total_in . '</th>
        <th>' . $total_out . '</th>
        <th></th>   <th></th>
    </tr>
</tfoot>

    </table>
    </div>
    </div>      
  </div>

</div>
</div>';



}

   

    print   $content;
    


}else if ( $_POST['action'] == 'Cash Transaction Report'){


    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));

     if($_POST['brunch_id'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND `brunch_id` = '".$_POST['brunch_id']."' ";
     }



    $content = '';

    $content .= ' <div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Cash Transaction Report\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Cash Transaction Report\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>';


    $content .= '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
        <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">
            <thead>';

    $content .='<tr>
        <td>Sl</td>

    <td>Tr. Id</td>
    <td>Transaction Date	</td>
    <td>Transaction Type	</td>
    <td>Account Name	</td>
    <td>Note</td>';

    if($_POST['report_type'] == 'Receive-Account' ){

        $content .=' <td>Receive</td>
        <td  style="display:none">Payment</td>';

        $QUERY = " `transection_head_id` = '".$_POST['EXTRAFILED']."' AND  ";

    }else if ($_POST['report_type'] == 'Payment-Account'){

        $content .=' <td  style="display:none">Receive</td>
        <td>Payment</td>';

        $QUERY = " `transection_head_id` = '".$_POST['EXTRAFILED']."' AND  ";

    }else{

        $content .=' <td>Receive</td>
        <td>Payment</td>';
        $QUERY = "  ";

    }



    $content .='</thead>
    <tbody>
    ';
$sl=1;
 $totalDeposit = 0; 
       $totalWithdraw = 0; 
    $query1 = $conn_me->prepare("SELECT `id` FROM `account_transection` where $QUERY  `transection_by` = 'Cash' AND (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."')   $BRUNCH_QUERY "); 
    $query1->execute();
    $count = $query1->rowCount();
        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){ 
        
        $info_head = SETUP::ACCOUNT_TRANSECTION($fetch1['id']);
    
        $content .='<tr>
                <td>'.$sl++.'</td>

        <td>'.$info_head['invoice_no'].'</td>
        <td>'.$info_head['transection_date'].'</td>
        <td>Cash Payment</td>
        <td>'.$info_head['transection_head_name'].'</td>
        <td>'.$info_head['details_of_transection_to'].'</td>';

        if($_POST['report_type'] == 'Receive-Account' ){

            $content .='<td>'.$info_head['in_amount'].'</td>
            <td style="display:none">'.$info_head['out_amount'].'</td>';

        }else if ($_POST['report_type'] == 'Payment-Account'){

            $content .='<td style="display:none">'.$info_head['in_amount'].'</td>
            <td>'.$info_head['out_amount'].'</td>';

        }else{

            $content .='<td>'.$info_head['in_amount'].'</td>
            <td>'.$info_head['out_amount'].'</td>';
        }
      
       $totalDeposit += $info_head['in_amount']; 
       $totalWithdraw += $info_head['out_amount']; 
       
    }

        $content .=' </tr>';




       $content .= '<tfoot>
         <tr>
             <th colspan="6" style="text-align:right"><b>Total</b></th>
             <th>' . number_format((float)( $totalDeposit), 2, '.', '') . '</th>
              <th>' . number_format((float)( $totalWithdraw), 2, '.', '') . '</th>
         </tr>
         </tfoot>';
         
         
         
    $content .= '</tbody> 
    </table>
    </div>
    </div>      
  </div>

</div>
</div>';


    print   $content;
    



}else if ( $_POST['action'] == 'Mobile Banking Transaction Report'){

    

    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


    $content = '';


    $content .= ' <div class="panel-heading">
    <div class="btn-group pull-right">
        <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
        <ul class="dropdown-menu">
            <li><a  onclick="printButtn(\' :: Mobile Transaction Report\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
            <li><a onclick="exportToExcel(\'Mobile Transaction Report\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
        </ul>
    </div>                                    
    
    </div>';


    $content .= '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
           <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">
           <thead>';


            $content .='<tr>
            <td>Tr. Id</td>
            <td>Transaction Date	</td>
            <td>Bank Details	</td>
            <td>Account Name	</td>
            <td>Note</td>';

    if($_POST['report_type'] == 'Deposit' ){

        $content .=' <td>Deposit</td>
        <td  style="display:none">Withdraw</td>';

    }else if ($_POST['report_type'] == 'Withdraw'){

        $content .=' <td  style="display:none">Deposit</td>
        <td>Withdraw</td>';

    }else{

        $content .=' <td>Deposit</td>
        <td>Withdraw</td>';
    }



    $content .='</thead>
    <tbody>
    ';
$sl=1;
    $query1 = $conn_me->prepare("SELECT `id` FROM `account_transection` where  `transection_by` = 'Mobile' AND  `transection_by_id` = '".$_POST['EXTRAFILED']."' AND (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."') "); 
    $query1->execute();

        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){         
            
            $info_head = SETUP::ACCOUNT_TRANSECTION($fetch1['id']);
    
            $content .='<tr>
            <td>'.$info_head['invoice_no'].'</td>
            <td>'.$info_head['transection_date'].'</td>
            <td>'.$info_head['mobile_banking_no'].'</td>
            <td>'.$info_head['transection_head_name'].'</td>
            <td>'.$info_head['details_of_transection_to'].'</td>';
    
            if($_POST['report_type'] == 'Deposit' ){
    
                $content .='<td>'.$info_head['in_amount'].'</td>
                <td style="display:none">'.$info_head['out_amount'].'</td>';
    
            }else if ($_POST['report_type'] == 'Withdraw'){
    
                $content .='<td style="display:none">'.$info_head['in_amount'].'</td>
                <td>'.$info_head['out_amount'].'</td>';
    
            }else{
    
                $content .='<td>'.$info_head['in_amount'].'</td>
                <td>'.$info_head['out_amount'].'</td>';
            }
          

       
    }

        $content .=' </tr>';

        
 


    $content .= '</tbody> 
    </table>
    </div>
    </div>      
  </div>

</div>
</div>';


    print   $content;


}else if ( $_POST['action'] == 'Bank Transaction Report'){

    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


     if($_POST['brunch_id'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND `brunch_id` = '".$_POST['brunch_id']."' ";
     }


    $bank_id = implode(',', $_POST['EXTRAFILED']);      


    $content = '';

    $content .= ' <div class="panel-heading">
    <div class="btn-group pull-right">
        <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
        <ul class="dropdown-menu">
            <li><a  onclick="printButtn(\' :: Bank Transaction Report\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
            <li><a onclick="exportToExcel(\'Bank Transaction Report\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
        </ul>
    </div>                                    
    
    </div>';


    $content .= '<div class="row mydivclass">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           <div class="table-responsive">
           <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="MSalary">
           <thead>';


            


            $content .='<tr>
            <td>Tr. Id</td>
            <td>Transaction Date	</td>
            <td>Bank Details	</td>
            <td>Account Name	</td>
            <td>Note</td>';

    if($_POST['report_type'] == 'Deposit' ){

        $content .=' <td>Deposit</td>
        <td  style="display:none">Withdraw</td>';

    }else if ($_POST['report_type'] == 'Withdraw'){

        $content .=' <td  style="display:none">Deposit</td>
        <td>Withdraw</td>';

    }else{

        $content .=' <td>Deposit</td>
        <td>Withdraw</td>';
    }



    $content .='</thead>
    <tbody>
    ';
$sl=1;

 $totalDeposit = 0; 
       $totalWithdraw = 0; 
       
    $query1 = $conn_me->prepare("SELECT `id` FROM `account_transection` where  `transection_by` = 'Bank'  $BRUNCH_QUERY  AND  `transection_by_id` IN  ({$bank_id})  AND (`transection_date` BETWEEN '".$date_from."' AND '".$date_to."') "); 
    $query1->execute();
    $count = $query1->rowCount();
    if($count > 0 ){
        
        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){         
            
            $info_head = SETUP::ACCOUNT_TRANSECTION($fetch1['id']);
    
            $content .='<tr>
            <td>'.$info_head['invoice_no'].'</td>
            <td>'.$info_head['transection_date'].'</td>
            <td>'.$info_head['bank_name'].' '.$info_head['check_no'].' '.$info_head['check_date'].'</td>
            <td>'.$info_head['transection_head_name'].'</td>
            <td>'.$info_head['details_of_transection_to'].'</td>';
    
            if($_POST['report_type'] == 'Deposit' ){
    
                $content .='<td>'.$info_head['in_amount'].'</td>
                <td style="display:none">'.$info_head['out_amount'].'</td>';
    
            }else if ($_POST['report_type'] == 'Withdraw'){
    
                $content .='<td style="display:none">'.$info_head['in_amount'].'</td>
                <td>'.$info_head['out_amount'].'</td>';
    
            }else{
    
                $content .='<td>'.$info_head['in_amount'].'</td>
                <td>'.$info_head['out_amount'].'</td>';
            }
          

       $totalDeposit += $info_head['in_amount']; 
       $totalWithdraw += $info_head['out_amount']; 
    }

        $content .=' </tr>';

        
    }else{
        $content .='<tr><td colspan="7">No Repord Found</td></tr>';
    }
    


       $content .= '<tfoot>
         <tr>
             <th colspan="5" style="text-align:right"><b>Total</b></th>
             <th>' . number_format((float)( $totalDeposit), 2, '.', '') . '</th>
              <th>' . number_format((float)( $totalWithdraw), 2, '.', '') . '</th>
         </tr>
         </tfoot>';
         
    $content .= '</tbody> 
    </table>
    </div>
    </div>      
  </div>

</div>
</div>';


    print   $content;
}else if ( $_POST['action'] == 'Daily-Attendance-Report'){

    $date_from = date("Y-m-d", strtotime($_POST['date_from']));

if($_POST['report_type'] == 'All'){ 

$QUERY = " where `attandance_date` = '".$date_from."'  order by `date`,`time` ";

}else{

$QUERY = " where `attandance_date` = '".$date_from."' AND `department_id`  = '".$_POST['report_type']."' order by `date`,`time` ";

}

    $content = '';

    $content = '<div class="row" style="background-color:white">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" >
           
        <table class="table  table-hover table-condensed table-striped table-bordered">
            <thead>';

    $content .='<tr>
    <td>SL</td>
    <td>EMP ID</td>
    <td>NAME</td>
    <td>DATE OF JOING</td>
    <td>DESIGNATION</td>
    <td>DEPARTMENT</td>
    <td>ATTENDANCE</td></thead>
    <tbody>
    ';
$sl=1;
    $query1 = $conn_me->prepare("SELECT * FROM `take_attandance` $QUERY  "); 
    $query1->execute();
    $count = $query1->rowCount();
    if($count > 0 ){
        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch1){ 
        
        $info_employee = SETUP::SETUP_EMPLOYEEY($fetch1['employee_id']);
        $info_attan = FIND::checkAttendanceStatus($fetch1['employee_id'],$date_from);

        $content .='<tr>
        <td>'.$sl++.'</td>
        <td>'.$info_employee['employee_code'].'</td>
        <td>'.$info_employee['name'].'</td>
        <td>'.$info_employee['join_d'].'</td>
        <td>'.$info_employee['designation_text'].'</td>
        <td>'.$info_employee['present_department_name'].'</td>
        <td>'.$info_attan.'</td>
        </tr>';

        }
    }else{
        $content .='<tr><td colspan="7">No Repord Found</td></tr>';
    }
    


    $content .= '</tbody> 
    </table>
    </div>
          
  </div>

</div>
</div>';



print   $content;

}else if ( $_POST['action'] == 'Overall RAW Inventory'){



    

    $content = '';
    $query1 = $conn_me->prepare("SELECT * FROM `setup_warehouse`  ORDER BY `name` ASC "); 
    $query1->execute();
    $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);

 
    $sl =1;
    $query2 = $conn_me->prepare("SELECT `id` FROM `setup_raw_material`  ORDER BY `category_id` ASC LIMIT 30 "); 
    $query2->execute();
    $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    foreach($fetch_list2 AS $fetch2){ 
        $total_cartoon = 0;
        $total_qty = 0;
    $product_data = SETUP::SETUP_RAW_MATERIAL($fetch2['id']);


    $content .='<tr>
    <td data-toggle="tooltip" title="Data Serial">'.$sl++.'</td>
    <td data-toggle="tooltip" title="Product Name" >'.$product_data['product_name'].'</td>
    <td data-toggle="tooltip" title="Category">'.$product_data['category'].'</td>';



    foreach($fetch_list1 AS $fetch1){ 

        $stock_data = STOCK::RAW_ITEM_WISE_STOCK($fetch1['id'],$fetch2['id'],'warehouse_wise');
        $in_cartoon = $stock_data['ITEM_STOCK']/$product_data['pcs_in_cartoon'];

        if($stock_data['ITEM_STOCK'] > 0 ){ $stock_qty =  $stock_data['ITEM_STOCK'];  }else{  $stock_qty = '';  }
        if($in_cartoon > 0 ){ $in_cartoon_qty =  $in_cartoon;  }else{  $in_cartoon_qty = '';  }

        $content .='<td   data-toggle="tooltip" title="Cartoon :: '.$fetch1['name'].' :: '.$product_data['product_name'].'">'.$in_cartoon_qty.'</td>
                    <td   data-toggle="tooltip"  title="Pieces :: '.$fetch1['name'].' :: '.$product_data['product_name'].'">'.$stock_qty.'</td>';

                    $total_qty += $stock_data['ITEM_STOCK'];
                    $total_cartoon += $in_cartoon;           
    }



    $content .='<td data-toggle="tooltip" title="Cartoon :: Total">';
    if($total_cartoon > 0 ){ $content .= $total_cartoon; } else { } $content .='</td>';
    $content .='<td data-toggle="tooltip" title="Pieces :: Total">';
    if($total_qty > 0 ){ $content .= $total_qty; } else { } $content .='</td>';

    
    $content .='</tr>';

  

    }
  



print $content ;



}else if ( $_POST['action'] == 'Employee List'){

$company_info = SETUP::SETUP_COMPANY('Active');

    
    $report = '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
    <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$_POST['action'].'</th></tr> ';
    
    $report .= '</table></div>';



    $report .= '<div class="container"><div class="row"><table class="table table-hover table-condensed table-striped table-bordered">
    <thead>
    <th>Sl</th>
    <th>Code</th>
    <th>Name</th>
    <th>Designation</th>
    <th>Contact</th>
    <th>Status</th>

    </thead>
        <tbody>'; 
$sl =1;
$qry = $conn_me->prepare("SELECT `id` FROM `setup_employee`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

foreach($fetch_list AS $fetch) {

$employee_info = SETUP::SETUP_EMPLOYEEY($fetch['id']);

    $report .= '<tr>
    <th>'.$sl++.'</th>
    <th>'.$employee_info['employee_code'].'</th>
    <th>'.$employee_info['name'].'</th>
    <th>'.$employee_info['designation_text'].'</th>
    <th>'.$employee_info['mob_no'].'</th>

    <th>'.$employee_info['status'].'</th>

   </tr>';
}


            
              
$report .= '</tbody> 
  </table></div></div>';

  print $company_info['header_content'] . $report ;


}else if ( $_POST['action'] == 'Complete Price List'){

    if( $_POST['EXTRAFILED'] == NULL  ){
        $QUERY = " ";
     }else {
        $selectedValues = implode(',', $_POST['EXTRAFILED']);
        $QUERY = "  where sp.`category_id` IN  ({$selectedValues}) ";

     }

 
    $report = '';

    $report .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           
            <table class="table datatable" id="MSalary">
            <thead>
            <th>Sl</th>
            <th>Code</th>
            <th>Product</th>
            <th>Category</th>
            <th>Purchase  Price</th>
            <th>Selling Price</th>
            <th>VAT % </th>
            </thead>
                <tbody>'; 
    $sl =1;
    $qry = $conn_me->prepare("SELECT sp.id ,sp.category_id,
    (
      SELECT fg.purches_price 
      FROM fg_local_purches fg 
      WHERE fg.product_id = sp.id 
      ORDER BY fg.invoice_date DESC 
      LIMIT 1
    ) AS `PurchasePrice`
  FROM setup_product sp
   $QUERY ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
 
        foreach($fetch_list AS $fetch) {
    
            $category_info = SETUP::SETUP_CATEGORY($fetch['category_id']);
            $product_info = SETUP::SETUP_PRODUCT($fetch['id']);

    
            $report .= '<tr id="tr_'.$fetch['id'].'">
            <td>'.$sl++.'</td>
            <td>'.$product_info['code'].'</td>
            <td>'.$product_info['product_name'].'</td>
            <td>'.$category_info['category'].' </td>
            <td>'.$fetch['PurchasePrice'].' </td>

            <td>'.$product_info['sales_rate'].'</td>
            <td>'.$product_info['vat_percentage'].'</td>
           </tr>';
        }
    

                    
                      
    $report .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';

  print  $report ;



}else if ( $_POST['action'] == 'Price List'){


    if( $_POST['EXTRAFILED'] == NULL  ){
        $QUERY = " WHERE  `in_service` = 'checked' ";
     }else {
        $selectedValues = implode(',', $_POST['EXTRAFILED']);
        $QUERY = "  where `category_id` IN  ({$selectedValues}) ";

     }

 
    $report = '';


    $report .= ' <div class="panel-heading">
    <div class="btn-group pull-right">
        <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
        <ul class="dropdown-menu">
            <li><a  onclick="printButtn(\' :: Price List Report\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
            <li><a onclick="exportToExcel(\'Price List Report\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
        </ul>
    </div>                                    
    
    </div>';


    $report .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           
            <table class="table datatable" id="MSalary">
            <thead>
            <th>Sl</th>
            <th>Code</th>
            <th>Product</th>
            <th>Category</th>
            <th>Price</th>
            <th>VAT % </th>
            </thead>
                <tbody>'; 
    $sl =1;
    $qry = $conn_me->prepare("SELECT * FROM `setup_product` $QUERY ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

        foreach($fetch_list AS $fetch) {
    
            $category_info = SETUP::SETUP_CATEGORY($fetch['category_id']);
    
    
            $report .= '<tr id="tr_'.$fetch['id'].'">
            <td>'.$sl++.'</td>
            <td>'.$fetch['code'].'</td>
            <td>'.$fetch['product_name'].'</td>
            <td>'.$category_info['category'].' </td>

            <td>'.$fetch['sales_rate'].'</td>
            <td>'.$fetch['vat_percentage'].'</td>
           </tr>';
        }
    
    
                    
                      
    $report .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';

  print  $report ;

}else if ( $_POST['action'] == 'Supplier Due Report'){

    


    if($_POST['report_type'] == 'All' ){
        $QUERY = '';
    }else if ($_POST['report_type'] == 'Supplier-Wise' ){
        $QUERY = " where `id` = '".$_POST['EXTRAFILED']."' ";
    }else{
        $QUERY = '';
    }


      
    $report = '<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Supplier Due Report\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Supplier Due Report\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div> ';


    $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
    <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$_POST['action'].'</th></tr> ';
    
    $report .= '</table></div>';
    

    $report .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           
            <table class="table datatable" id="MSalary">
            <thead>
            <th>Sl</th>
            <th>Supplier Id	</th>
            <th>Supplier Name	</th>
            <th>Bill Amount	</th>
            <th>Paid Amount	</th>
            <th>Returned Amount		</th>
            <th>Due</th>

            </thead>
                <tbody>'; 
    $sl =1;
    $qry = $conn_me->prepare("SELECT `id` FROM `setup_supplier` $QUERY ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    if ($qry->rowCount() > 0)
    {
        foreach($fetch_list AS $fetch) {
    
            $supplier_info = SETUP::SETUP_SUPPLIER($fetch['id']);
    
    
            $report .= '<tr>
            <td>'.$sl++.'</td>
            <td>'.$supplier_info['supplier_code'].'</td>
            <td>'.$supplier_info['supplier_name'].'</td>
            <td>'.$supplier_info['total_invoice_price'].'</td>
            <td>'.$supplier_info['total_paid'].'</td>
            <td>'.$supplier_info['total_return'].'</td>
            <td>'.$supplier_info['supplier_due'].'</td>

           </tr>';
        }
    
    }
                    
                      
    $report .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';
    

    print  $report ;


}else if ( $_POST['action'] == 'Salesman Performance Report'){



    if($_POST['branch_id'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND  si2.`brunch_id` = '".$_POST['branch_id']."' ";
     }

     $date_from = date("Y-m-d", strtotime($_POST['date_from']));
     $date_to = date("Y-m-d", strtotime($_POST['date_to']));

     $selectedValues = implode(',', $_POST['related_id']);





     
     $report = '<div class="row">
     <div class="col-md-12">
     
         <div class="panel panel-default">
             <div class="panel-body" id="load_table">
            
             <table class="table datatable" id="MSalary">
             <thead>
             <th>Sl</th>
             <th>Date</th>
             <th>Salesman Name</th>
             <th>Branch</th>
             <th>Contact No</th>
             <th>Total Sale</th>
             <th>Total Collection (Amount Received)</th>

             </thead>
                 <tbody>'; 
$sum_total_sales_quantity=0;
$sum_total_in_amount=0;

$sl =1;

/* 
$qry = $conn_me->prepare("SELECT 
COALESCE(si.invoice_date, at.transection_date) AS date,
COALESCE(si.sales_person, at.poster) AS sales_person,
SUM(COALESCE(sii.sales_rate, 0) * COALESCE(sii.sales_quantity, 0)) AS total_sale,
SUM(COALESCE(at.in_amount, 0)) AS total_receive
FROM 
sales_invoice si 
LEFT JOIN sales_invoice_item sii ON si.id = sii.sales_invoice_id 
LEFT JOIN account_transection at ON si.transection_id = at.id 
WHERE 
(si.invoice_date BETWEEN '$date_from' AND '$date_to' OR at.transection_date BETWEEN '$date_from' AND '$date_to') 
AND COALESCE(si.sales_person, at.poster) IN ($selectedValues)
GROUP BY 
COALESCE(si.invoice_date, at.transection_date), COALESCE(si.sales_person, at.poster)
ORDER BY 
date ASC
");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

*/ 

$movement_array = [];
$count = 0;



$ck1 = $conn_me->prepare("SELECT A.`code`,A.`id`,A.`sales_person`,A.`invoice_date`  FROM `sales_invoice`  A where A.`sales_person` IN  ({$selectedValues}) AND  A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND A.`generate_challan` = 'Done' ORDER BY A.`id` ASC");
$ck1->execute();

$fe_ck1 = $ck1->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck1 AS $fetch1){

    $invoice_info = SETUP::SETUP_SALES_INVOICE($fetch1['code']);

$count  = $count+1;
$movement_array[$count]['serial'] = $count;
$movement_array[$count]['date'] = $fetch1['invoice_date'];
$movement_array[$count]['sales_person'] = $fetch1['sales_person'];
$movement_array[$count]['sale_amount'] =  $invoice_info['total_invoice_price'];
$movement_array[$count]['receive_amount'] =  0;


}


$ck2 = $conn_me->prepare("SELECT A.`in_amount`,A.`poster`, A.`transection_date`  FROM `account_transection`  A where A.`poster` IN  ({$selectedValues}) AND  A.`transection_date` BETWEEN '".$date_from."' AND '".$date_to."'  ORDER BY A.`id` ASC");
$ck2->execute();
$fe_ck2 = $ck2->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck2 AS $fetch2){

$count  = $count+1;
$movement_array[$count]['serial'] = $count;
$movement_array[$count]['date'] = $fetch2['transection_date'];
$movement_array[$count]['sales_person'] = $fetch2['poster'];
$movement_array[$count]['sale_amount'] =  0;
$movement_array[$count]['receive_amount'] =  $fetch2['in_amount'];


}



$grouped = array();
foreach ($movement_array as $row) {
    $date = $row['date'];
    if (!isset($grouped[$date])) {
        $grouped[$date] = array(
            'date' => $date,
            'sales_person' => $row['sales_person'],
            'sale_amount' => 0,
            'receive_amount' => 0
        );
    }
    $grouped[$date]['sale_amount'] += $row['sale_amount'];
    $grouped[$date]['receive_amount'] += $row['receive_amount'];
}
$serial = 0;
$final_array = array();
foreach ($grouped as $row) {
    $serial++;
    $row['serial'] = $serial;
    $final_array[] = $row;
}



foreach($final_array as $item) {

    $poster_info = SETUP::ADMIN_SETUP($item['sales_person']);

       
             $report .= '<tr>
             <td>'.$sl++.'</td>
             <td>'.$item['date'].'</td>
             <td>'.$poster_info['hr_name'].'</td>
             <td>'.$poster_info['brunch_name'].'</td>
             <td>'.$poster_info['mob_no'].'</td>
             <td>'. number_format((float)( $item['sale_amount']), 2, '.', '').' </td>
             <td>'. number_format((float)( $item['receive_amount']), 2, '.', '').' </td>
  

 
       
            </tr>';
 
            $sum_total_sales_quantity += number_format((float)( $item['sale_amount']), 2, '.', '');
            $sum_total_in_amount +=number_format((float)( $item['receive_amount']), 2, '.', '');

         }
 
         $report .= '<tfoot>
         <tr>
             <th colspan="5" style="text-align:right"><b>Total</b></th>
             <th>' . number_format((float)( $sum_total_sales_quantity), 2, '.', '') . '</th>
             <th>' . number_format((float)( $sum_total_in_amount), 2, '.', '') . '</th>
         </tr>
         </tfoot>';
                       
     $report .= '</tbody> 
           </table>
           </div>
                 
         </div>
     
     </div>
     </div>';
 
 
     print  $report ;


}else if ( $_POST['action'] == 'Purchase Return Record'){




    if($_POST['branch_id'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND  sri.`brunch_id` = '".$_POST['branch_id']."' ";
     }

     $date_from = date("Y-m-d", strtotime($_POST['date_from']));
     $date_to = date("Y-m-d", strtotime($_POST['date_to']));

     $selectedValues = implode(',', $_POST['related_id']);



     if($_POST['report_type'] == 'Multipal-Product-Wise' ){
        $QUERY = "  sri.product_id IN  ({$selectedValues})  $BRUNCH_QUERY ";

     }else if ($_POST['report_type'] == 'Multipal-Supplier-Wise'){
        $QUERY = "   sri2.supplier_id IN  ({$selectedValues})  $BRUNCH_QUERY ";

     }else{

        $QUERY = "";
     } 


     $movement_array = [];
     $count = 0;



    $ck1 = $conn_me->prepare("SELECT sri.*,sri2.supplier_name,sri2.mobile FROM history_local_fg_purches sri
    INNER JOIN setup_supplier sri2 ON sri.supplier_id = sri2.id
    WHERE  $QUERY  AND sri.reject_quantity > 0 
    AND sri.invoice_date BETWEEN '".$date_from."' AND '".$date_to."';
    ");
    $ck1->execute();

    $fe_ck1 = $ck1->fetchAll(PDO::FETCH_ASSOC);
    foreach($fe_ck1 AS $fetch1){

    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch1['invoice_date'];
    $movement_array[$count]['product_id'] = $fetch1['product_id'];
    $movement_array[$count]['supplier_name'] = $fetch1['supplier_name'];
    $movement_array[$count]['mobile'] = $fetch1['mobile'];
    $movement_array[$count]['qty'] =  $fetch1['reject_quantity'];

    }



     $report = '<div class="row">
     <div class="col-md-12">
     
         <div class="panel panel-default">
             <div class="panel-body" id="load_table">
            
             <table class="table datatable" id="MSalary">
             <thead>
             <th>Sl</th>
             <th>Supplier Name</th>
             <th>Contact No</th>
             <th>Product Name</th>
             <th>Quantity</th>
             <th>Unit</th>
             <th>Quantity (CTN)</th>
             <th>Return Date </th>

             </thead>
                 <tbody>'; 
$sl=1;
foreach($movement_array as $item) {

     
             $info_product = SETUP::SETUP_PRODUCT($item['product_id']);

 
             if($item['qty'] > 0){
             $total_ctn = $item['qty']/($info_product['pcs_in_cartoon'] ?? 0 );
             }else{
             $total_ctn = 0.00;
             }
 
        
 
          
             $report .= '<tr>
             <th>'.$sl++.'</th>
             <th>'.$item['supplier_name'].'</th>
             <th>'.$item['mobile'].'</th>
             <th>'.$info_product['product_name'].'</th>
             <th>'. number_format((float)( $item['qty']), 2, '.', '').' </th>
             <th>'.$info_product['unit'].'</th>
             <th>'. number_format((float)( $total_ctn), 2, '.', '').'</th>
 
             <th>'.$item['date'].'</th>
 

 
       
            </tr>';
 
     
         }
 
   
                       
     $report .= '</tbody> 
           </table>
           </div>
                 
         </div>
     
     </div>
     </div>';
 
 
     print  $report ;




}else if ( $_POST['action'] == 'Sale Return Record'){


    if($_POST['branch_id'] == 'All' ){
        $BRUNCH_QUERY = " ";
     }else {
       $BRUNCH_QUERY = " AND  sri.`brunch_id` = '".$_POST['branch_id']."' ";
     }

     $date_from = date("Y-m-d", strtotime($_POST['date_from']));
     $date_to = date("Y-m-d", strtotime($_POST['date_to']));

     $selectedValues = implode(',', $_POST['related_id']);



     if($_POST['report_type'] == 'Multipal-Product-Wise' ){
        $QUERY = "  sri.product_id IN  ({$selectedValues})  $BRUNCH_QUERY ";

     }else if ($_POST['report_type'] == 'Multipal-Customer-Wise'){
        $QUERY = "   sri2.customer_id IN  ({$selectedValues})  $BRUNCH_QUERY ";

     }else{

        $QUERY = "";
     } 


     $movement_array = [];
     $count = 0;



    $ck1 = $conn_me->prepare("SELECT sri.*,sri2.invoice_date,sri2.customer_id FROM sales_return_invoice_item sri
    INNER JOIN sales_return_invoice sri2 ON sri.return_invoice_id = sri2.id
    WHERE  $QUERY
    AND sri2.invoice_date BETWEEN '".$date_from."' AND '".$date_to."';
    ");
    $ck1->execute();

    $fe_ck1 = $ck1->fetchAll(PDO::FETCH_ASSOC);
    foreach($fe_ck1 AS $fetch1){

    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch1['invoice_date'];
    $movement_array[$count]['product_id'] = $fetch1['product_id'];
    $movement_array[$count]['customer_id'] = $fetch1['customer_id'];
    $movement_array[$count]['qty'] = $fetch1['return_quantity'];
    $movement_array[$count]['rate'] =  $fetch1['sales_rate'];
    $movement_array[$count]['description'] = "Sales Return";

    }

    $ck2 = $conn_me->prepare("SELECT sri.*,sri2.invoice_date,sri2.customer_id  FROM damage_invoice_item  sri
    INNER JOIN damage_invoice  sri2 ON sri.damage_invoice_id  = sri2.id
    WHERE  $QUERY
    AND sri2.invoice_date BETWEEN '".$date_from."' AND '".$date_to."';
    
    ");
    $ck2->execute();

    $fe_ck2 = $ck2->fetchAll(PDO::FETCH_ASSOC);
    foreach($fe_ck2 AS $fetch2){

    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch1['invoice_date'];
    $movement_array[$count]['product_id'] = $fetch1['product_id'];
    $movement_array[$count]['customer_id'] = $fetch1['customer_id'];
    $movement_array[$count]['qty'] = $fetch1['damage_quantity'];
    $movement_array[$count]['rate'] =  $fetch1['sales_rate'];
    $movement_array[$count]['description'] = "Damage Return";

    }




     $report = '<div class="row">
     <div class="col-md-12">
     
         <div class="panel panel-default">
             <div class="panel-body" id="load_table">
            
             <table class="table datatable" id="MSalary">
             <thead>
             <th>Sl</th>
             <th>Shop Name</th>
                          <th>Address</th>

             <th>Contact No</th>
             <th>Product Name</th>
             <th>Quantity</th>
             <th>Unit</th>
             <th>Quantity (CTN)</th>
             <th>Return Date </th>
             <th>Return Price</th>
             <th>Note</th>

             </thead>
                 <tbody>'; 
$sl=1;
$total_qty = 0 ;
$total_return_price = 0 ;
foreach($movement_array as $item) {

     
             $info_product = SETUP::SETUP_PRODUCT($item['product_id']);
             $info_customer = SETUP::SETUP_CUSTOMER($item['customer_id']);

 
             if($item['qty'] > 0){
             $total_ctn = $item['qty']/($info_product['pcs_in_cartoon'] ?? 0 );
             }else{
             $total_ctn = 0.00;
             }
 
        
 
          
             $report .= '<tr>
             <th>'.$sl++.'</th>
             <th>'.$info_customer['shop_name'].'</th>
               <th>'.$info_customer['address'].'</th>
             <th>'.$info_customer['mobile'].'</th>
             <th>'.$info_product['product_name'].'</th>
             <th>'. number_format((float)( $item['qty']), 2, '.', '').' </th>
             <th>'.$info_product['unit'].'</th>
             <th>'. number_format((float)( $total_ctn), 2, '.', '').'</th>
 
             <th>'.$item['date'].'</th>
 
             <th>'.$item['rate'].'</th>
             <th>'.$item['description'].'</th>

 
       
            </tr>';
 
 
 $total_qty += $item['qty'] ;
$total_return_price += $item['rate'] ;
     
         }
 
   
                       
     $report .= '<tfoot><tr><th colspan="4">Total</th><th>'. $total_qty.'</th><th colspan="3"></th><th>'.$total_return_price.'</th><th></th></tr> </tfoot> </tbody> 
           </table>
           </div>
                 
         </div>
     
     </div>
     </div>';
 
 
     print  $report ;




}else if ( $_POST['action'] == 'Sale Summary'){
    
if ($_POST['branch_id'] == 'All') { 
    $BRUNCH_QUERY = "";
} else {
    $BRUNCH_QUERY = " AND si.`brunch_id` = '".$_POST['branch_id']."' ";
}

$date_from = date("Y-m-d", strtotime($_POST['date_from']));
$date_to   = date("Y-m-d", strtotime($_POST['date_to']));

$selectedValues = implode(',', $_POST['related_id']);

if ($_POST['report_type'] == 'Multipal-Product-Wise') {
    $QUERY = " si.product_id IN ({$selectedValues}) $BRUNCH_QUERY ";
} else if ($_POST['report_type'] == 'Multipal-Category-Wise') {
    $QUERY = " p.category_id IN ({$selectedValues}) $BRUNCH_QUERY ";
} else {
    $QUERY = " 1=1 $BRUNCH_QUERY ";
}

$report = '<div class="row">
<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
            <table class="table table-hover table-condensed table-striped table-bordered" id="example" style="width:100%">
                <thead>
                    <th>Sl</th>
                    <th>Product Name</th>
                    <th>Number of Product Sold</th>
                    <th>Unit</th>
                    <th>Number of Product Sold (CTN)</th>
                    <th>Opening Stock</th>
                    <th>Opening Stock (CTN)</th>
                    <th>Closing Stock</th>
                    <th>Closing Stock (CTN)</th>
                </thead>
                <tbody>'; 

$sl = 1;
$total_no_of_sold = 0;
$total_no_of_sold_ctn = 0;
$total_os = 0;
$total_os_ctn = 0;
$total_c_os = 0;
$total_c_os_ctn = 0;

// 🔹 Optimized query with stock joins
$qry = $conn_me->prepare("
    SELECT 
        si.product_id, 
        SUM(si.sales_quantity) AS total_sales_quantity,
        os.opening_stock,
        cs.current_stock
    FROM sales_invoice_item AS si
    INNER JOIN sales_invoice AS s 
        ON si.sales_invoice_id = s.id
    INNER JOIN setup_product AS p 
        ON si.product_id = p.id
    LEFT JOIN (
        SELECT 
            bp.product_id,
            SUM(bp.stock_in - bp.stock_out) AS opening_stock
        FROM balance_product bp
        WHERE bp.date <= :date_from
        GROUP BY bp.product_id
    ) os ON os.product_id = si.product_id
    LEFT JOIN (
        SELECT 
            bp.product_id,
            SUM(bp.stock_in - bp.stock_out) AS current_stock
        FROM balance_product bp
        WHERE bp.date <= :date_to
        GROUP BY bp.product_id
    ) cs ON cs.product_id = si.product_id
    WHERE $QUERY
      AND s.invoice_date BETWEEN :date_from AND :date_to
      AND s.generate_challan = 'Done'
    GROUP BY si.product_id, os.opening_stock, cs.current_stock
");
$qry->execute([
    ':date_from' => $date_from,
    ':date_to'   => $date_to
]); 

$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

foreach ($fetch_list as $fetch) {
    $info_product = SETUP::SETUP_PRODUCT($fetch['product_id']);

    // Sales Quantity (CTN)
    $total_sales_quantity_ctn = 0;
    if ($fetch['total_sales_quantity'] > 0 && !empty($info_product['pcs_in_cartoon'])) {
        $total_sales_quantity_ctn = $fetch['total_sales_quantity'] / $info_product['pcs_in_cartoon'];
    }

    // Opening Stock (CTN)
    $opening_balance_ctn = 0;
    if ($fetch['opening_stock'] > 0 && !empty($info_product['pcs_in_cartoon'])) {
        $opening_balance_ctn = $fetch['opening_stock'] / $info_product['pcs_in_cartoon'];
    }

    // Closing Stock (already pre-calculated as current_stock)
    $closing_stock = $fetch['current_stock'] ?? 0;
    $closing_stock_ctn = 0;
    if ($closing_stock > 0 && !empty($info_product['pcs_in_cartoon'])) {
        $closing_stock_ctn = $closing_stock / $info_product['pcs_in_cartoon'];
    }

    $report .= '<tr>
        <th>'.$sl++.'</th>
        <th>'.$info_product['product_name'].'</th>
        <th>'.number_format((float)$fetch['total_sales_quantity'], 2, '.', '').'</th>
        <th>'.$info_product['unit'].'</th>
        <th>'.number_format((float)$total_sales_quantity_ctn, 2, '.', '').'</th>
        <th>'.number_format((float)$fetch['opening_stock'], 2, '.', '').'</th>
        <th>'.number_format((float)$opening_balance_ctn, 2, '.', '').'</th>
        <th>'.number_format((float)$closing_stock, 2, '.', '').'</th>
        <th>'.number_format((float)$closing_stock_ctn, 2, '.', '').'</th>
    </tr>';

    // Totals
    $total_no_of_sold    += $fetch['total_sales_quantity'];
    $total_no_of_sold_ctn += $total_sales_quantity_ctn;
    $total_os            += $fetch['opening_stock'];
    $total_os_ctn        += $opening_balance_ctn;
    $total_c_os          += $closing_stock;
    $total_c_os_ctn      += $closing_stock_ctn;
}

$report .= '<tfoot>';  
$report .= '<tr>';        
$report .= '<th colspan="2" style="text-align:right">TOTAL :: </th>';
$report .= '<th>'.$total_no_of_sold.'</th>';
$report .= '<th></th>';
$report .= '<th>'.$total_no_of_sold_ctn.'</th>';
$report .= '<th>'.$total_os.'</th>';
$report .= '<th>'.$total_os_ctn.'</th>';
$report .= '<th>'.$total_c_os.'</th>';
$report .= '<th>'.$total_c_os_ctn.'</th>';
$report .= '</tr>';
$report .= '</tfoot>';  
$report .= '</tbody> 
      </table>
      </div>
    </div>
</div>
</div>';

print $report;

}else if ( $_POST['action'] == 'Customer List'){

     
    $report = '<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Customer List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Customer-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div> ';
$report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
<tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$_POST['action'].'</th></tr> ';

$report .= '</table></div>';




        if($_POST['report_type'] == 'All'){
        $QUERY = " where  A.`in_service` = 'checked' ";

       }else{
        $selectedid = implode(',', $_POST['EXTRAFILED']);

        if($_POST['report_type'] == 'Multiple-Division-Wise'){

            $QUERY = " WHERE A.`division_id` IN  ({$selectedid})  AND A.`in_service` = 'checked' ";
    
          }else if ($_POST['report_type'] == 'Multiple-District-Wise'){
    
            $QUERY = " WHERE A.`district_id` IN  ({$selectedid}) AND A.`in_service` = 'checked' ";
    
          }else if ($_POST['report_type'] == 'Multiple-Upazila-Wise'){
    
            $QUERY = " WHERE A.`upazila_id` IN  ({$selectedid}) AND A.`in_service` = 'checked' ";
    
          }else{
    
            $QUERY = " ";
    
          }
           

       }
     





    $report .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           
            <table class="table datatable" id="MSalary">
            <thead>
            <th>Sl</th>
            <th>Code</th>
            <th>Name</th>
            <th>Shop Name</th>
            <th>Contact Number</th>
            <th>Address</th>
            <th>Divisions</th>
            <th>Districts</th>
            <th>Upazila</th>
            <th>Union</th>
    
            </thead>
                <tbody>'; 
    $sl =1;
    $total_invoice = 0;
    $total_receive = 0;
    $total_return = 0;
    $total_due = 0;
    $qry = $conn_me->prepare("SELECT A.`id` FROM `setup_customer` A  $QUERY ");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

        foreach($fetch_list AS $fetch) {
    
            $info_customer = SETUP::SETUP_CUSTOMER($fetch['id']);


            $report .= '<tr>
            <th>'.$sl++.'</th>
            <th>'.$info_customer['code'].'</th>
            <th>'.$info_customer['customer_name'].'</th>
            <th>'.$info_customer['shop_name'].'</th>
            <th>'.$info_customer['mobile'].'</th>
            <th>'.$info_customer['address'].'</th>
            <th>'.$info_customer['division'].'</th>
            <th>'.$info_customer['district'].'</th>
            <th>'.$info_customer['upazila'].'</th>
            <th>'.$info_customer['union'].'</th>
           </tr>';

    
        }

  
                      
    $report .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';


    print  $report ;



}else if ( $_POST['action'] == 'Customer Due Report'){

    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));

  


    $report = '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
    <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$_POST['action'].' ( Upto '.$_POST['date_to'].')</th></tr> ';
    
    $report .= '</table></div>';


    $report .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
            <div class="table-responsive">
                                      <table class="table table-hover table-condensed table-striped table-bordered" id="example"  style="width:100%">

            <thead>
            <th>Sl</th>
            <th>Customer Name	</th>
            <th>Shop Name</th>
            <th>Address</th>
            <th>Invoice Amount	</th>
            <th>Transaction Amount	</th>
            <th>Product Retr. Amount </th> 
            <th>Due Adjustment </th> 
            <th>Discount Adjustment </th> 
            <th>Due</th>
                        <th>Due Limit</th>

   <th>Status</th>
            </thead>
                <tbody>'; 
    $sl =1;


    $total_invoice = 0;
    $transection_amount = 0;
    $product_return_amount = 0;
    $dis_adjustement = 0;
    $due_adjustement = 0;
    $total_due = 0;

$report_type = (isset($_POST['report_type']))  ? $_POST['report_type'] : '';

$data = FIND::getAllCustomerDues($report_type,$_POST['related_id'], $date_to);

    foreach ($data as $fetch) {


        $customerInfo  = SETUP::findCustomerById($fetch['customer_id']);

         $customer_name = ( !empty($customerInfo['customer_name']))? $customerInfo['customer_name'] : '' ; 
         $shop_name = ( !empty($customerInfo['shop_name']))? $customerInfo['shop_name'] : '' ; 
         $address = ( !empty($customerInfo['address']))? $customerInfo['address'] : '' ; 

       
       $status = ($fetch['customer_due'] > $customerInfo['creadit_limit'] ) ? ' NOT OK  ' : ' OK ' ;
       $color = ($fetch['customer_due'] > $customerInfo['creadit_limit'] ) ? ' red ' : ' green ' ;

            $report .=  '<tr>
            <td>'.$sl++.'</td>
            <td>'.$customer_name.'</td>
            <td>'.$shop_name.'</td>
            <td>'.$address.'</td>
            <td>'.$fetch['invoice_amount'].'</td>
            <td>'.$fetch['transection_amount'].'</td>
            <td>'.$fetch['product_return_amount'].'</td>
            <td>'.$fetch['due_adjustement'].'</td>
            <td>'.$fetch['dis_adjustement'].'</td>
            <td>'.$fetch['customer_due'].'</td>

            <td>'.$customerInfo['creadit_limit'].'</td>
            <td style="background-color:'.$color.'" >'.$status.'</td>

           </tr>';

           $total_invoice += $fetch['invoice_amount'];
           $transection_amount += $fetch['transection_amount'];
           $product_return_amount += $fetch['product_return_amount'];
           $due_adjustement += $fetch['due_adjustement'];
           $dis_adjustement += $fetch['dis_adjustement'];
           $total_due += $fetch['customer_due'];

    

        }
    
        $report .= '<tfoot>';  
    $report .= '<tr>';        
    $report .= '<th colspan="4" style="text-align:right">TOTAL :: </th>';
    $report .= '<th>'.$total_invoice.'</th>';
    $report .= '<th>'.$transection_amount.'</th>';
    $report .= '<th>'.$product_return_amount.'</th>';
    $report .= '<th>'.$due_adjustement.'</th>';
    $report .= '<th>'.$dis_adjustement.'</th>';
    $report .= '<th>'.$total_due.'</th>';

    $report .= '</tr>';
                      
        
    $report .= '</tfoot>';  
    $report .= '</tbody> 
          </table>
          </div>
          </div>  
        </div>
    
    </div>
    </div>';


    print  $report ;

}else if ( $_POST['action'] == 'Raw Goods Purchase Record With Details'){


    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


    if($_POST['report_type'] == 'All' ){

        $QUERY = " where `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND `warehouse_receive` = 'Done' GROUP BY `code`" ;

    }else if ($_POST['report_type'] == 'Supplier-Wise' ){

        $QUERY = " where `supplier_id` = '".$_POST['EXTRAFILED']."' AND   `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."'  AND `warehouse_receive` = 'Done' GROUP BY `code`" ;

    }else if ( $_POST['report_type'] == 'Employee-Wise' ){

        $QUERY = " where `poster` = '".$_POST['EXTRAFILED']."' AND  `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND `warehouse_receive` = 'Done' GROUP BY `code`" ;

    }else if ( $_POST['report_type'] == 'By-Purchase-Invoice' ){

        $QUERY = " where `invoice_no` REGEXP '".$_POST['EXTRAFILED']."'  AND `warehouse_receive` = 'Done' GROUP BY `code` " ;


    }else{
        
        $QUERY = " WHERE `warehouse_receive` = 'Done' GROUP BY `code`";
    }


    $report = '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
    <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$_POST['action'].'</th></tr> ';
    
    $report .= '</table></div>';

    $report .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           
            <table class="table table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" >
<thead>
<tr>
<th>Invoice No.</th> 
<th>Date</th> 
<th>Supplier Name</th> 
<th>Employee Name</th> 
<th>Product Name</th> 
<th>VAT</th> 
<th>Transport Cost</th> 
<th>Price</th> 
<th>Quantity</th> 
<th>Total</th> 
<th>Action</th>
</tr>
</thead>
<tbody>
                
            '; 
    $sl =1;
  
 

    $qry = $conn_me->prepare("SELECT `code`   FROM `raw_local_purches`  $QUERY ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

        foreach($fetch_list AS $fetch) {
    
                $invoice_info = SETUP::SETUP_RAW_LOCAL_PURCHASE_HISTORY($fetch['code']);
                 $info_customer_due = FIND::SUPPLIER_DUE($invoice_info['supplier_id']);

            

                $report .= '<tr>
                <td>'.$invoice_info['invoice_no'].'</td>
                <td>'.$invoice_info['invoice_date'].'</td>
                <td>'.$invoice_info['supplier_name'].'</td>
                <td>'.$invoice_info['emp_name'].'</td>
                <td></td>
                <td>'.$invoice_info['vat_cost'].'</td>
                <td>'.$invoice_info['transport_cost'].'</td>
                <td></td>
                <td></td>
                <td></td>
                <td> 

                <a href="purchase_invoice_copy.php?purches_type=raw_local_purches&code='.$fetch['code'].'" target="_BLINK"><span class="fa fa-file-text"></span></a>

           
                </td>
              </tr>';

              $total_qty =0;
              $qry2 = $conn_me->prepare("SELECT *  FROM `raw_local_purches` A  where `code` = '".$invoice_info['code']."' ");
              $qry2->execute();
              $count =   $qry2->rowCount();
              $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
              foreach($fetch_list2 AS $fetch2) {
                $total_price = $fetch2['purches_price']*$fetch2['quantity'];
                $count_sl = $sl++;
                if($count_sl == 1 ){ $rowspan= "rowspan='.$count.'"; } else { $rowspan= '' ; }
                $product_data = SETUP::SETUP_RAW_MATERIAL($fetch2['product_id']);

                    $report .= '<tr>
                    <td colspan="4" '.$rowspan.'></td> 
                    <td>'.$product_data['product_name'].'</td> 
                    <td colspan="2"></td> 
                    <td style="text-align: right;">'.$fetch2['purches_price'].'</td> 
                    <td style="text-align: center;">'.$fetch2['quantity'].'</td> 
                    <td style="text-align: right;">'.$total_price.'</td>
                    <td></td>
                </tr>';
                $total_qty +=  $fetch2['quantity'];
              }
             
              $report .= ' <tr style="font-weight: bold;"><td colspan="8"></td> <td style="text-align: center;">Total Quantity<br>'.$total_qty.'</td> <td style="text-align: right;">
              Total: '.$invoice_info['invoice_price'].'<br>
              Total Paid: '.$info_customer_due['total_paid'].'<br>
              Total Due: '.$info_customer_due['supplier_due'].'
          </td> <td></td></tr> ';
            
       

        }
        $report .= '</tbody></table>';   

                    
    $report .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';

    print $report;

}else if ( $_POST['action'] == 'SALARY-PAYMERT'){

    $explode = explode("-",$_POST['date_from']);
    $number_of_days = cal_days_in_month(CAL_GREGORIAN, $explode['0'], $explode['1']);


    $report = '<div class="row">
    <div class="col-md-12">
   												 											
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
            <div class="table-responsive">
            <table class="table table-hover table-condensed table-striped table-bordered datatable" style="white-space:nowrap;">
            <thead>
            <th>Code</th>
            <th>Name</th>
            <th>Department</th>
            <th>Medical	</th>
            <th>House Rent</th>
            <th>Basic</th>
            <th>Over Time Bill</th>
            <th>Monthly Drawing</th>
            <th>Per day Salary</th>
            <th>Total Absent</th>
            <th>Net Absent (Total Absent-Grace 1 day absent)</th>
            <th>Absent Deduction</th>
            <th>Payable Salary after absent deduction</th>
            <th>Deductuon From Advance</th>
            <th>To be Paid/Paid Salary</th>
            <th>New Salary Due</th>
            <th>Previos Advance</th>
            <th>Last Month Advance</th>
            <th>Advance Paid</th>
            <th>New Total Due</th>

            </thead>
                <tbody>'; 

$query =$conn_me->prepare("SELECT `id` FROM `setup_employee` order by `id`");
    $query->execute();

$fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach($fetch_list AS $fetch) {

            $info_emp = SETUP::SETUP_EMPLOYEEY($fetch['id']);


            $perday_sallery = $info_emp['present_salary']/$number_of_days;
            $total_absent = FIND::TOTAL_ACTIVITY_GIVEN_MONTH($_POST['date_from'],'absent',$fetch['id']);
            $advance_transection = FIND::ADVANCE_TRANSECTION($_POST['date_from'],$fetch['id']);


            $net_absemt = $total_absent['total_days']-1;
            $absent_deducation = $net_absemt*$perday_sallery;
            $after_deducation = $info_emp['present_salary']-$absent_deducation;
            $to_be_paid =  $after_deducation - $advance_transection['this_month_advance'];
            $new_salry_due =  $to_be_paid -  $after_deducation;
            $new_total_due = $new_salry_due+$advance_transection['total_advance_paid'];


            $report .= '<tr>';
       
            $report .= '<td>'.$info_emp['employee_code'].'</td>';
            $report .= '<td>'.$info_emp['name'].'</td>';
            $report .= '<td>'.$info_emp['present_department_name'].'</td>';
            $report .= '<td>'.$info_emp['medical'].'</td>';
            $report .= '<td>'.$info_emp['house_rent'].'</td>';
            $report .= '<td>'.$info_emp['basic'].'</td>';
            $report .= '<td>'.$info_emp['over_time_bill'].'</td>';
            $report .= '<td>'.$info_emp['present_salary'].'</td>';
            $report .= '<td>'.$perday_sallery.'</td>';
            $report .= '<td>'.$total_absent['total_days'].'</td>';
            $report .= '<td>'.$net_absemt.'</td>';
            $report .= '<td>'.$absent_deducation.'</td>';
            $report .= '<td>'.$after_deducation.'</td>';
            $report .= '<td>'.$advance_transection['this_month_advance'].'</td>';
            $report .= '<td>'.$to_be_paid.'</td>';
            $report .= '<td>'.$new_salry_due.'</td>';
            $report .= '<td>'.$advance_transection['previous_advance'].'</td>';
            $report .= '<td>'.$advance_transection['last_month_advance'].'</td>';
            $report .= '<td>'.$advance_transection['total_advance_paid'].'</td>';
            $report .= '<td>'.$new_total_due.'</td>';

            
            $report .= '</tr>';

        
        }
    
    
                    
              
    $report .= '</tbody> 
          </table>
          </div>
          </div> 
        </div>
    
    </div>
    </div>';


    print  $report ;



}else if ( $_POST['action'] == 'Finishied Goods Purchase Record With Details'){



    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


    if($_POST['report_type'] == 'All' ){

        $QUERY = " where `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND `warehouse_receive` = 'Done' GROUP BY `code`" ;

    }else if ($_POST['report_type'] == 'Supplier-Wise' ){

        $QUERY = " where `supplier_id` = '".$_POST['EXTRAFILED']."' AND   `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."'  AND `warehouse_receive` = 'Done' GROUP BY `code`" ;

    }else if ( $_POST['report_type'] == 'Employee-Wise' ){

        $QUERY = " where `poster` = '".$_POST['EXTRAFILED']."' AND  `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND `warehouse_receive` = 'Done' GROUP BY `code`" ;

    }else if ( $_POST['report_type'] == 'By-Purchase-Invoice' ){

        $QUERY = " where `invoice_no` REGEXP '".$_POST['EXTRAFILED']."'  AND `warehouse_receive` = 'Done' GROUP BY `code` " ;

      }else if ( $_POST['report_type'] == 'Finished-Goods' ){
  
          $QUERY = " where `product_id` = '".$_POST['EXTRAFILED']."'  AND `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."'  AND `warehouse_receive` = 'Done'  " ;
    }else{
        
        $QUERY = "where `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND `warehouse_receive` = 'Done' GROUP BY `code` ";
    }


    $report = '<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Finishied Goods Purchase Record With Details\',\'withdetails\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Finishied Goods Purchase Record With Details\',\'withdetails\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>';


    $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
    <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$_POST['action'].'</th></tr> ';
    
    $report .= '</table></div>';

    $report .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           
            <table class="table table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" id="withdetails" >
<thead>
<tr>
<th>Invoice No.</th> 
<th>Date</th> 
<th>Supplier Name</th> 
<th>Employee Name</th> 
<th>Product Name</th> 
<th>VAT</th> 
<th>Transport Cost</th> 
<th>Price</th> 
<th>Quantity</th> 
<th>Total</th> 
<th>Action</th>
</tr>
</thead>
<tbody>
                
            '; 
    $sl =1;
  
 

    $qry = $conn_me->prepare("SELECT `code`   FROM `fg_local_purches`  $QUERY ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

        foreach($fetch_list AS $fetch) {
    
                $invoice_info = SETUP::SETUP_FG_LOCAL_PURCHASE_HISTORY($fetch['code']);
                 $info_supplier_due = FIND::SUPPLIER_DUE($invoice_info['supplier_id']);

            

                $report .= '<tr>
                <td>'.$invoice_info['invoice_no'].'</td>
                <td>'.$invoice_info['invoice_date'].'</td>
                <td>'.$invoice_info['supplier_name'].'</td>
                <td>'.$invoice_info['emp_name'].'</td>
                <td></td>
                <td>'.$invoice_info['vat_cost'].'</td>
                <td>'.$invoice_info['transport_cost'].'</td>
                <td></td>
                <td></td>
                <td></td>
                <td> 

                <a href="purchase_invoice_copy.php?purches_type=fg_local_purches&code='.$fetch['code'].'" target="_BLINK"><span class="fa fa-file-text"></span></a>

           
                </td>
              </tr>';

              $total_qty =0;
              $qry2 = $conn_me->prepare("SELECT *  FROM `fg_local_purches` A  where `code` = '".$invoice_info['code']."' ");
              $qry2->execute();
              $count =   $qry2->rowCount();
              $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
              foreach($fetch_list2 AS $fetch2) {
                $total_price = $fetch2['purches_price']*$fetch2['quantity'];
                $count_sl = $sl++;
                if($count_sl == 1 ){ $rowspan= "rowspan='.$count.'"; } else { $rowspan= '' ; }
                $product_data = SETUP::SETUP_PRODUCT($fetch2['product_id']);

                    $report .= '<tr>
                    <td colspan="4" '.$rowspan.'></td> 
                    <td>'.$product_data['product_name'].'</td> 
                    <td colspan="2"></td> 
                    <td style="text-align: right;">'.$fetch2['purches_price'].'</td> 
                    <td style="text-align: center;">'.$fetch2['quantity'].'</td> 
                    <td style="text-align: right;">'.$total_price.'</td>
                    <td></td>
                </tr>';
                $total_qty +=  $fetch2['quantity'];
              }
             
              $report .= ' <tr style="font-weight: bold;"><td colspan="8"></td> <td style="text-align: center;">Total Quantity<br>'.$total_qty.'</td> <td style="text-align: right;">
              Total: '.$invoice_info['invoice_price'].'<br>
              Total Paid: '.$info_supplier_due['total_paid'].'<br>
              Total Due: '.$info_supplier_due['supplier_due'].'
          </td> <td></td></tr> ';
            
       

        }
        $report .= '</tbody></table>';   

                    
    $report .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';

    print $report;


}else if ( $_POST['action'] == 'Demand Record'){

   

    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


    if($_POST['report_type'] == 'All' ){

        $QUERY = "  AND A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' " ;

    }else{
        
        $QUERY = "  AND A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' " ;
    }


    $report = '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
    <tr><th style="text-align:center;font-size:18px;color:red">'.$_POST['action'].'</th></tr> ';
    
    $report .= '</table></div>';


    $report .= '';


    $report .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
            <div class="table-responsive">
            <table class="table table-hover table-condensed table-striped table-bordered"  id="MSalary2">
<thead>
<tr>
<th>Invoice No.</th> 
<th>Demand Date</th> 
<th>Demand From </th> 
<th>Demand To </th> 
<th>Notes</th> 
<th>Product Name</th> 
<th>Demand Quantity</th> 
<th>Approved Quantity</th> 
<th>Price</th> 
<th>Total</th> 
<th>Action</th>
</tr>
</thead>
<tbody>
                
            '; 
    $sl =1;

    $TotalDemand = 0.00;
    $TotalApproved = 0.00;
    $grand_total_demand = 0 ;
    $qry = $conn_me->prepare("SELECT A.code,A.id,
    A.notes,A.`invoice_no`,A.`invoice_date`,B.`brunch` AS DEMAND_FROM ,C.`brunch` AS DEMAND_TO,
    F.`name` as `poster_name`

    FROM `demand` A  
    
    JOIN `setup_brunch` B ON (A.`demand_created_from` = B.`id`)
    JOIN `setup_brunch` C ON (A.`demand_created_to` = C.`id`)
    JOIN `admin` E ON (A.`poster` = E.`id`)
    JOIN `setup_employee` F ON (E.`employee_id` = F.`id`)

WHERE
     (A.demand_created_from = '".$_SESSION['USER_BRUNCH']."'  OR A.demand_created_to = '".$_SESSION['USER_BRUNCH']."' )AND A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."'  $QUERY
     ORDER BY A.`id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

        foreach($fetch_list AS $fetch) {
    
              
                $report .= '<tr>
                <th>'.$fetch['invoice_no'].'</th>
                <th>'.$fetch['invoice_date'].'</th>
                <th>'.$fetch['DEMAND_FROM'].'</th>
                <th>'.$fetch['DEMAND_TO'].'</th>
                <th>'.$fetch['notes'].'</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
        
                <td class="hidden-print"> 

            <button class="btn btn-danger btn-rounded btn-sm" onClick="deleteFullDemand('.$fetch['id'].');">DELETE FULL DEMAND</button>';

        
                              $report .= '<a class="btn btn-danger btn-rounded btn-sm" href="demand_record_print.php?copy=Approved-Copy&demand_id='.$fetch['id'].'" target="_BLINK"> Print 1 </a>';
                              
      
       
               
                              $report .= '<a class="btn btn-danger btn-rounded btn-sm" href="demand_record_print.php?copy=Print-Copy&demand_id='.$fetch['id'].'" target="_BLINK"> Print 2</a>';
        


                $report .= '</td>
              </tr>';

              $total_demand =0;
              $total_approved = 0;
            
              $qry2 = $conn_me->prepare("SELECT
              A.id,
              A.demand_id,
              A.product_id,
              A.quantity,
              B.product_name
            FROM
            demand_item A
            JOIN
            setup_product B ON A.product_id = B.id
            WHERE
              A.demand_id = '".$fetch['id']."';
            ");
              $qry2->execute();
              $count =   $qry2->rowCount();
              $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
              foreach($fetch_list2 AS $fetch2) {
                $count_sl = $sl++;
                if($count_sl == 1 ){ $rowspan= "rowspan='.$count.'"; } else { $rowspan= '' ; }

                $delivery_done  = DEMAND::TotalDelivery($fetch2['demand_id'],$fetch2['product_id']) ; 
                 
                $total = $delivery_done['total_item']*$delivery_done['productprice'] ; 

                    $report .= '<tr>
                    <td '.$rowspan.'></td> 
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.$fetch2['product_name'].'</td> 
                    <td style="text-align: right;">'.$fetch2['quantity'].'</td> 
      
                    <td style="text-align: right;">'.$delivery_done['total_item'].'</td> 
                    <td style="text-align: right;">'.$delivery_done['productprice'].'</td> 
                    <td>'.$total.'</td>';
                    $report .= '<td>';
                    if($delivery_done['total_item'] > 0 ){

                    }else{
                        $report .= '<button class="btn btn-danger btn-rounded btn-sm" onClick="deleteDemand('.$fetch2['id'].');"><span class="fa fa-times"></span></button>';
                    }
                    $report .= '</td>';
                    $report .= '</tr>';


                    $total_demand +=$total;
        $TotalDemand += $fetch2['quantity'];
        $TotalApproved += $delivery_done['total_item'];


              }
             
              $report .= '<tr>
              <th colspan="9" style="text-align:right"><b>Sub Total</b></th>
              <th colspan="2">'.$total_demand.'</th> 
  
          </tr>';



          $grand_total_demand += $total_demand ; 

        }

        $report .= '<tfoot>
        <tr>
            <th colspan="6" style="text-align:right;color:red"><b>Total</b></th>
            <th style="color:red">'.$TotalDemand .'</th>
            <th style="color:red">'.$TotalApproved .'</th><th></th>
            <th  style="color:red">'.$grand_total_demand.'</th> <th></th>

        </tr>
        </tfoot>';

        $report .= '</tbody></table></div>';   

                    
    $report .= '
          </div>
                
        </div>
    
    </div>
    </div>';

    print $report;

}else if ( $_POST['action'] == 'Sales Record With Details'){
    
    
    

    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));


    if($_POST['report_type'] == 'All' ){

        $QUERY = " WHERE  A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND A.`generate_challan` = 'Done' " ;

    }else if ($_POST['report_type'] == 'Multipal-Customer-Wise' ){

        $customer_id = implode(',', $_POST['EXTRAFILED']);      

        $QUERY = "  WHERE A.`customer_id` IN  ({$customer_id})  AND   A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."'  AND A.`generate_challan` = 'Done'" ;

    }else if ( $_POST['report_type'] == 'Multipal-Sales-By' ){

        $sales_by = implode(',', $_POST['EXTRAFILED']);      
        $QUERY = "  WHERE A.`sales_by` IN  ({$sales_by}) AND  A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND A.`generate_challan` = 'Done' " ;

    }else if (  $_POST['report_type'] == 'By-Sales-Invoice'){
        
        $QUERY = " WHERE A.`invoice_no` REGEXP '".$_POST['EXTRAFILED']."'  AND `generate_challan` = 'Done'" ;

    }else if (  $_POST['report_type'] == 'Multipal-Sales-Person'){
        
        $sales_person = implode(',', $_POST['EXTRAFILED']);      
        $QUERY = "  WHERE A.`sales_person` IN  ({$sales_person}) AND  A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND A.`generate_challan` = 'Done' " ;


    }else if (  $_POST['report_type'] == 'Branch-Wise'){

        $QUERY = " WHERE A.`brunch_id` = '".$_POST['EXTRAFILED']."' " ;


    }else{
        
        $QUERY = "  ";
    }


    $report = '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
    <tr><th style="text-align:center;font-size:18px;color:red">'.$_POST['action'].'</th></tr> ';
    
    $report .= '</table></div>';





    $report .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
            <div class="table-responsive">
            <table class="table table-hover table-condensed table-striped table-bordered"  id="MSalary2">

<thead>
<tr>
<th>Invoice No.</th> 
<th>Date</th> 
<th>Customer Name</th> 
<th>Employee Name</th> 
<th>Sales Person</th> 
<th>Product Name</th> 
<th>VAT</th> 
<th>Discount</th> 
<th>Transport Cost</th> 
<th>Price</th> 
<th>Quantity</th> 
<th>Total</th> 
<th>Action</th>
</tr>
</thead>
<tbody>
                
            '; 
    $sl =1;

    $total_of_total_qty = 0.00;
    $total_of_total_invoice_price = 0.00;
    $total_of_total_receive = 0.00;
    $total_of_total_due = 0.00;

    $qry = $conn_me->prepare("SELECT 
    A.`invoice_no`,A.`invoice_date`,A.`discount`,A.`transport_cost`,A.`total_vat_cost`,A.`transection_id`,A.`code`,A.`id`,
    B.`shop_name`,
    D.`name` as `sales_person_name`,
    F.`name` as `sales_by_name`,   
    IF(A.`transection_id` IS NOT NULL, G.`in_amount`, 0) AS in_amount,
    GROUP_CONCAT(P.product_name,'SAJIDSEPERATOR1',si.sales_quantity,'SAJIDSEPERATOR1',si.sales_rate,'SAJIDSEPERATOR2') AS product_details

    FROM `sales_invoice` A  
    INNER JOIN 
    sales_invoice_item si ON A.id = si.sales_invoice_id
    JOIN `setup_customer` B ON (A.`customer_id` = B.`id`)
    JOIN `admin` C ON (A.`sales_person` = C.`id`)
    JOIN `setup_employee` D ON (C.`employee_id` = D.`id`)
    JOIN `admin` E ON (A.`sales_by` = E.`id`)
    JOIN `setup_employee` F ON (E.`employee_id` = F.`id`)
    LEFT JOIN `account_transection` G ON (A.`transection_id` = G.`id`)
    JOIN 
    setup_product P ON si.product_id = P.id
    
    $QUERY
    GROUP BY A.id
    ORDER BY A.`id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

        foreach($fetch_list AS $fetch) {
    
            $receive = number_format((float)( $fetch['in_amount']), 2, '.', '');

            $product_items = explode('SAJIDSEPERATOR2', $fetch['product_details']);



                $report .= '<tr>
                <th>'.$fetch['invoice_no'].'</th>
                <th>'.$fetch['invoice_date'].'</th>
                <th>'.$fetch['shop_name'].'</th>
                <th>'.$fetch['sales_person_name'].'</th>
                <th>'.$fetch['sales_by_name'].'</td>
                <td></td>
                <td>'.$fetch['total_vat_cost'].'</td>
                <td>'.$fetch['discount'].'</td>
                <td>'.$fetch['transport_cost'].'</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="hidden-print"> 

                <div class="btn-group">
                <a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle">Action <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="invoice_copy.php?code='.$fetch['code'].'" target="_BLINK" >Invoice Copy</a></li>
                    <li><a href="print.php?print=Delivery Challan&code='.$fetch['code'].'" target="_BLINK">Challan Copy</a></li>
                    <li><a href="print.php?print=Godown Copy&code='.$fetch['code'].'" target="_BLINK">Godown Copy</a></li>';
    
    
                    if($_SESSION['NEWERP_SESS_MEMBER_ID'] == '149' ){ // only for linkon vai
                    $report .= ' <li><a onclick="HardDelete(\'INVOICE\',\''.$fetch['id'].'\')">Delete</a></li>';
                }
                    $report .= '</ul>
            </div>

                </td>
              </tr>';

              $total_qty =0;
              $actul_total_price = 0;
  $sll = 1;
              foreach ($product_items as $item) {

                $item_details = explode('SAJIDSEPERATOR1', $item);


                if (count($item_details) === 3) {

                    $product_name = ltrim($item_details[0], ',');

 
                    
                    $sales_quantity = is_numeric($item_details[1]) ? (float)$item_details[1] : 0;
$sales_rate     = is_numeric($item_details[2]) ? (float)$item_details[2] : 0;

$total_price = $sales_quantity * $sales_rate;


                    $count_sl = $sl++;
                    if($count_sl == 1 ){ $rowspan= ''; } else { $rowspan= '' ; }

        $report .= '<tr>
        <td></td> 
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>'. $sll++.'. '. $product_name.'</td> 
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align: right;">'.$sales_rate.'</td>
        <td style="text-align: center;">'.$sales_quantity.'</td> 
        <td style="text-align: right;">'.$total_price.'</td> 
        <td></td>
    </tr>';

    


    $total_qty +=  $sales_quantity;
    $actul_total_price += $total_price;


                }


    




              }

          
             
              $invoice_price = ($actul_total_price + $fetch['transport_cost'] + $fetch['total_vat_cost'] ) - $fetch['discount'] ;

              $invoice_due = number_format((float)( $invoice_price - $receive), 2, '.', '');



              $report .= ' <tr style="font-weight: bold;"> 
 
              <th colspan="10"  style="text-align: right;color:red">T O T A L</th> 
              <td style="text-align: center;color:red"> '.$total_qty.'</td> 
              
              <td style="text-align: right;color:red">

              <table class="table table-hover table-condensed table-striped table-bordered">
              <tr>
              <td>Invoice</td>
              <td>'.$invoice_price.'</td>
              </tr>
              <tr>
              <td>Paid</td>
              <td>'.$receive.'</td>
              </tr>
              <tr>
              <td>Due</td>
              <td>'.$invoice_due.'</td>
              </tr>
              </table>


          </td> 
          
          <td></td></tr> ';
            
       $total_of_total_qty +=  $total_qty;
       $total_of_total_invoice_price +=  $invoice_price;
       $total_of_total_receive +=  $receive;
       $total_of_total_due +=  $invoice_due;

        }

        $report .= ' <tr style="font-weight: bold;">
        <th colspan="10"  style="text-align: right;color:green">S U M M E R Y</th> 

        <td style="text-align: center;color:green">'.$total_of_total_qty.'</td>
        <td style="text-align: right;color:green">
        <table class="table table-hover table-condensed table-striped table-bordered">
        <tr>
        <td>Invoice</td>
        <td>'.$total_of_total_invoice_price.'</td>
        </tr>
        <tr>
        <td>Paid</td>
        <td>'.$total_of_total_receive.'</td>
        </tr>
        <tr>
        <td>Due</td>
        <td>'.$total_of_total_due.'</td>
        </tr>
        </table>
       
    </td> <td></td></tr> ';


        $report .= '</tbody></table></div>';   

                    
    $report .= '
          </div>
                
        </div>
    
    </div>
    </div>';

    print $report;

}else if ( $_POST['action'] == 'Raw Goods Purchase Record'){

    

    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));
      
      if($_POST['report_type'] == 'All' ){
  
          $QUERY = " where A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND A.`warehouse_receive` = 'Done'  GROUP BY A.`code` " ;
  
      }else if ($_POST['report_type'] == 'Supplier-Wise' ){
  
          $QUERY = " where A.`supplier_id` = '".$_POST['EXTRAFILED']."' AND  A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."'   AND A.`warehouse_receive` = 'Done'  GROUP BY A.`code` " ;


      }else if ( $_POST['report_type'] == 'Raw-Material' ){
  
          $QUERY = " where A.`product_id` = '".$_POST['EXTRAFILED']."'  AND A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."'  AND A.`warehouse_receive` = 'Done'  " ;
  
      }else if (  $_POST['report_type'] == 'Raw-Category'){
  
          $QUERY = " JOIN `setup_product` B ON (A.`product_id` = B.`id`)  where   B.`category_id` = '".$_POST['EXTRAFILED']."' AND  A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND  A.`warehouse_receive` = 'Done' GROUP BY A.`code` " ;
  
      }else if (  $_POST['report_type'] == 'By-Purchase-Invoice'){
          
          $QUERY = " where A.`invoice_no` REGEXP '".$_POST['EXTRAFILED']."'  AND A.`warehouse_receive` = 'Done' GROUP BY `code`" ;
  
      }else if (  $_POST['report_type'] == 'Employee-Wise'){
          
          $QUERY = " where A.`poster` = '".$_POST['EXTRAFILED']."' AND A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND A.`warehouse_receive` = 'Done'  GROUP BY A.`code`" ;
  
      }else{
          
          $QUERY = " ";
      }
  
  
      $report = '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
      <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$_POST['action'].'</th></tr> ';
      
      $report .= '</table></div>';
      
  if( $_POST['report_type'] == 'Raw-Material' ||  $_POST['report_type'] == 'Raw-Category' ){
      $report .= '<div class="row">
      <div class="col-md-12">
      
          <div class="panel panel-default">
              <div class="panel-body" id="load_table">
             
              <table class="table datatable">
              <thead>
              <th>Sl</th>
              <th>Material Id</th>
              <th>Material Name</th>
              <th>Category</th>
              <th> Quantity</th>
              <th>Unit</th>
             
  
              </thead>
                  <tbody>'; 
      $sl =1;
    
      $total_sold =0;
      $qry = $conn_me->prepare("SELECT  A.`quantity` ,A.`product_id`  FROM `raw_local_purches` A  $QUERY ORDER BY A.`id` ASC");
      $qry->execute();
      $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
      if ($qry->rowCount() > 0)
      {
          foreach($fetch_list AS $fetch) {
      
      
                  $product_info = SETUP::SETUP_RAW_MATERIAL($fetch['product_id']);
                  $report .= '<tr>
                  <td>'.$sl++.'</td>
                  <td>'.$product_info['material_code'].'</td>
                  <td>'.$product_info['product_name'].'</td>
                  <td>'.$product_info['category'].'</td>
                  <td>'.$fetch['quantity'].'</td>
                  <td>'.$product_info['unit'].'</td>
                
                 </tr>';
      
                 $total_sold += $fetch['quantity'];
              
   
             
            
      
             
  
          }
          $report .= '<tr><th colspan="4" class="text-pull-right">Total</th><th>'.$total_sold.'</th><th></th></tr>';   
      }else{
          $report .= '<tr><th colspan="4" class="text-pull-right"></th><th>No record</th></tr>';         
  
      }
                      
      $report .= '</tbody> 
            </table>
            </div>
                  
          </div>
      
      </div>
      </div>';
  
    
      
  }else{
      $report .= '<div class="row">
      <div class="col-md-12">
      
          <div class="panel panel-default">
              <div class="panel-body" id="load_table">
             
              <table class="table datatable">
              <thead>
              <th>Sl</th>
              <th>Invoice No.</th>
              <th>Date</th>
              <th>Supplier Name</th>
              <th>Employee Name</th>
              <th>Sub Total</th>
              <th>Vat</th>
              <th>Transport Cost</th>
              <th>Total</th>
  
              </thead>
                  <tbody>'; 
      $sl =1;
      $total_sub_total =0;
      $total_vat =0;
      $total_transport =0;
      $total_discount =0;
      $total_invoice =0;
      $qry = $conn_me->prepare("SELECT A.`code` FROM `raw_local_purches`  A $QUERY ORDER BY A.`id` ASC");
      $qry->execute();
      $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
      if ($qry->rowCount() > 0)
      {
          foreach($fetch_list AS $fetch) {
      
            $invoice_info = SETUP::SETUP_RAW_LOCAL_PURCHASE_HISTORY($fetch['code']);

      
            $report .= '<tr>
            <td>'.$sl++.'</td>
            <td>'.$invoice_info['invoice_no'].'</td>
            <td>'.$invoice_info['invoice_date'].'</td>
            <td>'.$invoice_info['supplier_name'].'</td>
            <td>'.$invoice_info['emp_name'].'</td>
            <td>'.$invoice_info['sub_total'].'</td>
            <td>'.$invoice_info['vat_cost'].'</td>
            <td>'.$invoice_info['transport_cost'].'</td>
            <td>'.$invoice_info['invoice_price'].'</td>

            </tr> ';

  
            
          }
      
      }
                      
             
      $report .= '</tbody> 
            </table>
            </div>
                  
          </div>
      
      </div>
      </div>';
  }
     
      
  
      print  $report ;

}else if ( $_POST['action'] == 'Finishied Goods Purchase Record'){

    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));
      
      if($_POST['report_type'] == 'All' ){
  
          $QUERY = " where A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND A.`warehouse_receive` = 'Done'  GROUP BY A.`code` " ;
  
      }else if ($_POST['report_type'] == 'Supplier-Wise' ){
  
          $QUERY = " where A.`supplier_id` = '".$_POST['EXTRAFILED']."' AND  A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."'   AND A.`warehouse_receive` = 'Done'  GROUP BY A.`code` " ;


      }else if ( $_POST['report_type'] == 'Finished-Goods' ){
  
          $QUERY = " where A.`product_id` = '".$_POST['EXTRAFILED']."'  AND A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."'  AND A.`warehouse_receive` = 'Done'  " ;
  
      }else if (  $_POST['report_type'] == 'FG-Category'){
  
          $QUERY = " JOIN `setup_product` B ON (A.`product_id` = B.`id`)  where   B.`category_id` = '".$_POST['EXTRAFILED']."' AND  A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND  A.`warehouse_receive` = 'Done' GROUP BY A.`code` " ;
  
      }else if (  $_POST['report_type'] == 'By-Purchase-Invoice'){
          
          $QUERY = " where A.`invoice_no` REGEXP '".$_POST['EXTRAFILED']."'  AND A.`warehouse_receive` = 'Done' GROUP BY `code`" ;
  
      }else if (  $_POST['report_type'] == 'Employee-Wise'){
          
          $QUERY = " where A.`poster` = '".$_POST['EXTRAFILED']."' AND A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND A.`warehouse_receive` = 'Done'  GROUP BY A.`code`" ;
  
      }else{
          
          $QUERY = " ";
      }
  
  
      $report = '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
      <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$_POST['action'].'</th></tr> ';
      
      $report .= '</table></div>';
      
  if( $_POST['report_type'] == 'Finished-Goods' ||  $_POST['report_type'] == 'FG-Category' ){
      $report .= '<div class="row">
      <div class="col-md-12">';
      
      $report .= '<div class="panel-heading">
      <div class="btn-group pull-right">
          <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
          <ul class="dropdown-menu">
              <li><a  onclick="printButtn(\' :: Finishied Goods Purchase Record\',\'adas\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
              <li><a onclick="exportToExcel(\'Finishied Goods Purchase Record\',\'adas\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
          </ul>
      </div>                                    
      
      </div>';


      $report .= '<div class="panel panel-default">
              <div class="panel-body" id="load_table">
             
              <table class="table datatable" id="adas">
              <thead>
              <th>Sl</th>
              <th>Product Id</th>
              <th>Invoice Date</th>
              <th>Product Name</th>
              <th>Category</th>
              <th>Purchase Quantity</th>
              <th>Unit</th>
             
  
              </thead>
                  <tbody>'; 
      $sl =1;
    
      $total_sold =0;
      $qry = $conn_me->prepare("SELECT  date_format(A.invoice_date, '%d-%m-%Y') AS `invoice_date`,A.`quantity` ,A.`product_id`  FROM `fg_local_purches` A  $QUERY ORDER BY A.`id` ASC");
      $qry->execute();
      $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
      if ($qry->rowCount() > 0)
      {
          foreach($fetch_list AS $fetch) {
      
      
                  $product_info = SETUP::SETUP_PRODUCT($fetch['product_id']);
                  $report .= '<tr>
                  <td>'.$sl++.'</td>
                  <td>'.$product_info['product_code'].'</td>
                  <td>'.$fetch['invoice_date'].'</td>
                  <td>'.$product_info['product_name'].'</td>
                  <td>'.$product_info['category'].'</td>
                  <td>'.$fetch['quantity'].'</td>
                  <td>'.$product_info['unit'].'</td>
                
                 </tr>';
      
                 $total_sold += $fetch['quantity'];
              
   
             
            
      
             
  
          }
          $report .= '<tr><th colspan="4" class="text-pull-right">Total</th><th>'.$total_sold.'</th><th></th></tr>';   
      }else{
          $report .= '<tr><th colspan="4" class="text-pull-right"></th><th>No record</th></tr>';         
  
      }
                      
      $report .= '</tbody> 
            </table>
            </div>
                  
          </div>
      
      </div>
      </div>';
  
    
      
  }else{
      $report .= '<div class="row">
      <div class="col-md-12">';
      $report .= '<div class="panel-heading">
      <div class="btn-group pull-right">
          <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
          <ul class="dropdown-menu">
              <li><a  onclick="printButtn(\' :: Finishied Goods Purchase Record\',\'adas\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
              <li><a onclick="exportToExcel(\'Finishied Goods Purchase Record\',\'adas\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
          </ul>
      </div>                                    
      
      </div>';
      $report .= ' <div class="panel panel-default">
              <div class="panel-body" id="load_table">
             
              <table class="table datatable" id="adas">
              <thead>
              <th>Sl</th>
              <th>Invoice No.</th>
              <th>Date</th>
              <th>Supplier Name</th>
              <th>Employee Name</th>
              <th>Sub Total</th>
              <th>Vat</th>
              <th>Transport Cost</th>
              <th>Total</th>
  
              </thead>
                  <tbody>'; 
      $sl =1;
      $total_sub_total =0;
      $total_vat =0;
      $total_transport =0;
      $total_discount =0;
      $total_invoice =0;
      $qry = $conn_me->prepare("SELECT A.`code` FROM `fg_local_purches`  A $QUERY ORDER BY A.`id` ASC");
      $qry->execute();
      $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
      if ($qry->rowCount() > 0)
      {
          foreach($fetch_list AS $fetch) {
      
            $invoice_info = SETUP::SETUP_FG_LOCAL_PURCHASE_HISTORY($fetch['code']);

      
            $report .= '<tr>
            <td>'.$sl++.'</td>
            <td>'.$invoice_info['invoice_no'].'</td>
            <td>'.$invoice_info['invoice_date'].'</td>
            <td>'.$invoice_info['supplier_name'].'</td>
            <td>'.$invoice_info['emp_name'].'</td>
            <td>'.$invoice_info['sub_total'].'</td>
            <td>'.$invoice_info['vat_cost'].'</td>
            <td>'.$invoice_info['transport_cost'].'</td>
            <td>'.$invoice_info['invoice_price'].'</td>

            </tr> ';

  
            
          }
      
      }
                      
             
      $report .= '</tbody> 
            </table>
            </div>
                  
          </div>
      
      </div>
      </div>';
  }
     
      
  
      print  $report ;
  
      
    }else if ( $_POST['action'] == 'PRODUCT-PRICE-HISTORY'){


        $date_from = date("Y-m-d", strtotime($_POST['date_from']));
        $date_to = date("Y-m-d", strtotime($_POST['date_to']));

        $content = '' ;
        if($_POST['report_type'] == 'Finished-Goods' ){

            $info_product =  SETUP::SETUP_PRODUCT($_POST['EXTRAFILED']);

        

              
    $content .= '<div class="btn-group pull-right hidden-print">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="SimplePrint()" ><img src="img/icons/json.png" width="24"/>Print</a></li>
    </ul>
</div> ';



            $content .= '<div class="row mydivclass">
            <div class="col-md-12">
            
                <div class="panel panel-default">
                    <div class="panel-body" id="load_table">
                   <div class="table-responsive">
                   <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="MSalary">
                   <thead>';
        
            $content .='
            <tr>
            <th colspan="3" style="text-align:center;color:red">'.$info_product['code'].' '.$info_product['product_name'].' </th>
            </tr> 
            <tr>
            <th>Sl	</th>
            <th>Date</th>
            <th> Price</td>
            </tr> </thead>';
        
        
        $sl =1;
            $query1 = $conn_me->prepare("SELECT  date ,price
            FROM history_change_product_price_vat A
            WHERE A.product_id = '".$_POST['EXTRAFILED']."' and A.date BETWEEN '".$date_from."'  AND '".$date_to."' 
            ORDER BY date DESC
        "); 
            $query1->execute();
       
       
            $content .= '<tr>';
            $content .= '<td style="text-align:center" colspan="2">RECENT PRICE</td>';
            $content .= '<td>'.$info_product['sales_rate'].'</td>';
       
            $content .= '</tr>';
       
       
            $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
       
       
               foreach($fetch_list1 AS $fetch1){ 
                   $date = date("d-m-Y", strtotime($fetch1['date']));
       
                   $content .= '<tr>';
                   $content .= '<td>'.$sl++.'</td>';
                   $content .= '<td>'.$date.'</td>';
                   $content .= '<td>'.$fetch1['price'].'</td>';
           
                   $content .= '</tr>';
           
               }
           
        
            
            
          
        
        
        
            $content .= '</tbody> 
            </table>
            </div>
            </div>      
          </div>
        
        </div>
        </div>';
        



        }else{
            $content .= '<div class="row mydivclass">
     <div class="col-md-12">
     
         <div class="panel panel-default">
             <div class="panel-body" id="load_table">
            <div class="table-responsive">
            <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="example">
            <thead>';
 
     $content .='<tr>
     <th>Sl	</th>
     <th>Product Code</th>
     <th>Product Name</th>
     <th>Date</th>
     <th>Previous Price</td>
     <th>Modified Price </th>
     <th> Price Difference</th>
   
     </tr> </thead>';
 
 
 $sl =1;
     $query1 = $conn_me->prepare("SELECT
     h.product_id,
     MAX(h.date) AS last_changed_date,
     h.price AS last_changed_price,
     p.sales_rate AS original_price,
     p.code,
     p.product_name,
     h.price - p.sales_rate AS price_difference
   FROM
     history_change_product_price_vat h
   JOIN
     setup_product p ON h.product_id = p.id
   GROUP BY
     h.product_id;
 "); 
     $query1->execute();
     $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
         foreach($fetch_list1 AS $fetch1){ 
     
        
                 $content .= '<tr>';
                 $content .= '<td>'.$sl++.'</td>';
                 $content .= '<td>'.$fetch1['code'].'</td>';
                 $content .= '<td>'.$fetch1['product_name'].'</td>';
                 $content .= '<td>'.$fetch1['last_changed_date'].'</td>';
                 $content .= '<td>'.$fetch1['last_changed_price'].'</td>';
 
                 $content .= '<td>'.$fetch1['original_price'].'</td>';
                 $content .= '<td>'.$fetch1['price_difference'].'</td>';

                 $content .= '</tr>';
         
            
     
         }
     
   
 
 
 
     $content .= '</tbody> 
     </table>
     </div>
     </div>      
   </div>
 
 </div>
 </div>';
 
        }
        
     
 
 
 print   $content;


    }else if ( $_POST['action'] == 'PREORDER-PRODUCT-LIST'){


    
    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));

    if($_POST['EXTRAFILED'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND  `brunch_id` = '".$_POST['EXTRAFILED']."' ";
     }



     $content = '<div class="row mydivclass">
     <div class="col-md-12">
     
         <div class="panel panel-default">
             <div class="panel-body" id="load_table">
            <div class="table-responsive">
            <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="MSalary">
            <thead>';
 
     $content .='<tr>
     <th>Sl	</th>
     <th>Product Name</th>
     <th>Current Price     </th>
     <th>QTY</td>
     <th>Unit</th>
     <th> QTY (CTN)  </th>
     <th> Stock </th>
     <th> Stock (CTN) </th>

     </tr> </thead>';
 
 
 $sl =1;
     $query1 = $conn_me->prepare("SELECT *  FROM `pre_order_invoice_item`  WHERE  `converat_to_invoice` = 'Pending'  $BRUNCH_QUERY   "); 
     $query1->execute();
     $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
         foreach($fetch_list1 AS $fetch1){ 
     
             $info_product = SETUP::SETUP_PRODUCT($fetch1['product_id']);
             $stock_info = STOCK::FG_ITEM_WISE_STOCK($fetch1['brunch_id'],$fetch1['product_id'],'unique_brunch_wise');

             

            if($info_product['pcs_in_cartoon'] > 0 ){
                $carton = round($fetch1['quantity']/ ($info_product['pcs_in_cartoon'] ?? 0));
                $stock_carton = round($stock_info['ITEM_STOCK']/ ($info_product['pcs_in_cartoon'] ?? 0));

            }else{
                $carton = 0.00;
                $stock_carton = 0.00;
            }
        
                 $content .= '<tr>';
                 $content .= '<td>'.$sl++.'</td>';
                 $content .= '<td>'.$info_product['product_name'].'</td>';
                 $content .= '<td>'.$info_product['sales_rate'].'</td>';
                 $content .= '<td>'.$fetch1['quantity'].'</td>';
                 $content .= '<td>'.$info_product['unit'].'</td>';
 
                 $content .= '<td>'.$carton.'</td>';
                 $content .= '<td>'.$stock_info['ITEM_STOCK'].'</td>'; 
                 $content .= '<td>'.$stock_carton.'</td>';

                 $content .= '</tr>';
         
            
     
         }
     
   
 
 
 
     $content .= '</tbody> 
     </table>
     </div>
     </div>      
   </div>
 
 </div>
 </div>';
 
 
 
 print   $content;



    }else if ( $_POST['action'] == 'PREORDER-RECORD'){


    
    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));

    if($_POST['EXTRAFILED'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND  `brunch_id` = '".$_POST['EXTRAFILED']."' ";
     }



     $content = '<div class="row mydivclass">
     <div class="col-md-12">
     
         <div class="panel panel-default">
             <div class="panel-body" id="load_table">
            <div class="table-responsive">
            <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="MSalary">
            <thead>';
 
     $content .='<tr>
     <th>Sl	</th>
     <th>Date</th>
     <th>Invoice No    </th>
     <th>Shop Name</td>
     <th>Address</th>
     <th> Total  </th>
     <th> Invoice Conversation  </th>
     </tr> </thead>';
 
 
 $sl =1;
     $query1 = $conn_me->prepare("SELECT *  FROM `preorder_invoice`  WHERE  ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."')   $BRUNCH_QUERY   "); 
     $query1->execute();
     $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
         foreach($fetch_list1 AS $fetch1){ 
     
             $info_customer = SETUP::SETUP_CUSTOMER($fetch1['customer_id']);
             $info_quatation_price = FIND::TOTAL_PREORDER_INVOICE_PRICE($fetch1['id']);

        
                 $content .= '<tr>';
                 $content .= '<td>'.$sl++.'</td>';
                 $content .= '<td>'.$fetch1['invoice_date'].'</td>';
                 $content .= '<td>'.$fetch1['invoice_no'].'</td>';
                 $content .= '<td>'.$info_customer['shop_name'].'</td>';
                 $content .= '<td>'.$info_customer['address'].'</td>';
 
                 $content .= '<td>'.$info_quatation_price['invoice_price'].'</td>';
                 $content .= '<td>'.$fetch1['converat_to_invoice'].'</td>'; 
                 $content .= '</tr>';
         
            
     
         }
     
   
 
 
 
     $content .= '</tbody> 
     </table>
     </div>
     </div>      
   </div>
 
 </div>
 </div>';
 
 
 
 print   $content;




}else if ( $_POST['action'] == 'QUOTATION-RECORD'){

    $date_from = date("Y-m-d", strtotime($_POST['date_from']));
    $date_to = date("Y-m-d", strtotime($_POST['date_to']));

    if($_POST['EXTRAFILED'] == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND  `brunch_id` = '".$_POST['EXTRAFILED']."' ";
     }



     $content = '<div class="row mydivclass">
     <div class="col-md-12">
     
         <div class="panel panel-default">
             <div class="panel-body" id="load_table">
            <div class="table-responsive">
            <table class="table table-hover table-condensed table-striped table-bordered datatable"  id="MSalary">
            <thead>';
 
     $content .='<tr>
     <th>Sl	</th>
     <th>Date</th>
     <th>Invoice No    </th>
     <th>Shop Name</td>
     <th>Address</th>
     <th> Total  </th>
     <th> Invoice Conversation  </th>
     <th> Action  </th>
     </tr> </thead>';
 
 
 $sl =1;
     $query1 = $conn_me->prepare("SELECT *  FROM `quotation_invoice`  WHERE  ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."')   $BRUNCH_QUERY   "); 
     $query1->execute();
     $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
         foreach($fetch_list1 AS $fetch1){ 
     
             $info_customer = SETUP::SETUP_CUSTOMER($fetch1['customer_id']);
             $info_quatation_price = FIND::TOTAL_QUATATION_INVOICE_PRICE($fetch1['id']);

        
                 $content .= '<tr>';
                 $content .= '<td>'.$sl++.'</td>';
                 $content .= '<td>'.$fetch1['invoice_date'].'</td>';
                 $content .= '<td>'.$fetch1['invoice_no'].'</td>';
                 $content .= '<td>'.$info_customer['shop_name'].'</td>';
                 $content .= '<td>'.$info_customer['address'].'</td>';
 
                 $content .= '<td>'.$info_quatation_price['invoice_price'].'</td>';
                 $content .= '<td>'.$fetch1['converat_to_invoice'].'</td>';

                 $content .= '<td><a target="_BLINK" href="quatation_copy.php?id='.$fetch1['id'].'">Quotation Copy </a></td>';
 
                 $content .= '</tr>';
         
            
     
         }
     
   
 
 
 
     $content .= '</tbody> 
     </table>
     </div>
     </div>      
   </div>
 
 </div>
 </div>';
 
 
 
 print   $content;



}else if ( $_POST['action'] == 'Sales Record'){

  $date_from = date("Y-m-d", strtotime($_POST['date_from']));
  $date_to = date("Y-m-d", strtotime($_POST['date_to']));
    
    if($_POST['report_type'] == 'All' ){

        $QUERY = " where A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND A.`generate_challan` = 'Done' " ;

    }else if ($_POST['report_type'] == 'Multipal-Customer-Wise' ){

        $customer_id = implode(',', $_POST['EXTRAFILED']);      


        $QUERY = " where A.`customer_id` IN  ({$customer_id})  AND   A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."'  AND A.`generate_challan` = 'Done'" ;

    }else if ( $_POST['report_type'] == 'Multipal-Sales-By' ){

         $sales_by = implode(',', $_POST['EXTRAFILED']);      
        $QUERY = " where A.`sales_by` IN  ({$sales_by}) AND  A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND A.`generate_challan` = 'Done' " ;

    }else if ( $_POST['report_type'] == 'Multipal-Product-Wise' ){

        $product_id = implode(',', $_POST['EXTRAFILED']);      

        $QUERY = " 
        JOIN
        sales_invoice B ON A.sales_invoice_id = B.id
        JOIN
        setup_customer J ON B.customer_id = J.id
      JOIN
        setup_product C ON A.product_id = C.id
      JOIN
        setup_brunch D ON A.brunch_id = D.id
      JOIN
        setup_brunch E ON B.dispatch_from_which_brunch = E.id
      JOIN
        setup_unit F ON C.unit_id = F.id
      JOIN
        admin G ON A.sales_person = G.id
      JOIN
        setup_employee H ON G.employee_id = H.id
       JOIN
        setup_category I ON C.category_id = I.id
      
      INNER JOIN
        (SELECT DISTINCT product_id
         FROM sales_invoice_item
         WHERE product_id IN ({$product_id})) AS subquery ON A.product_id = subquery.product_id
      WHERE
        A.sales_manager_confirm_date BETWEEN '".$date_from."' AND '".$date_to."'
        AND B.generate_challan = 'Done'
      ORDER BY
        A.id ASC  " ;



    }else if (  $_POST['report_type'] == 'Multipal-Category-Wise'){


        $category_id = implode(',', $_POST['EXTRAFILED']);      

         
        $qry1 = $conn_me->prepare("SELECT group_concat(`id`) as `PID` FROM `setup_product` where category_id IN  ({$category_id}) ");
        $qry1->execute();
        $fetch_list = $qry1->fetch(PDO::FETCH_ASSOC);
        $product_id = $fetch_list['PID'];


        $QUERY = " 
        JOIN
        sales_invoice B ON A.sales_invoice_id = B.id
        JOIN
        setup_customer J ON B.customer_id = J.id
      JOIN
        setup_product C ON A.product_id = C.id
      JOIN
        setup_brunch D ON A.brunch_id = D.id
      JOIN
        setup_brunch E ON B.dispatch_from_which_brunch = E.id
      JOIN
        setup_unit F ON C.unit_id = F.id
      JOIN
        admin G ON A.sales_person = G.id
      JOIN
        setup_employee H ON G.employee_id = H.id
      JOIN
        setup_category I ON C.category_id = I.id

      INNER JOIN
        (SELECT DISTINCT product_id
         FROM sales_invoice_item
         WHERE product_id IN ({$product_id})) AS subquery ON A.product_id = subquery.product_id
      WHERE
        A.sales_manager_confirm_date BETWEEN '".$date_from."' AND '".$date_to."'
        AND B.generate_challan = 'Done'
      ORDER BY
        A.id ASC  " ;


    }else if (  $_POST['report_type'] == 'By-Sales-Invoice'){
        
        $QUERY = " where A.`invoice_no` REGEXP '".$_POST['EXTRAFILED']."'  AND `generate_challan` = 'Done'" ;

    }else if (  $_POST['report_type'] == 'Multipal-Sales-Person'){
        
        $sales_person = implode(',', $_POST['EXTRAFILED']);      

        $QUERY = " where A.`sales_person` IN  ({$sales_person}) AND  A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND A.`generate_challan` = 'Done' " ;



    }else if (  $_POST['report_type'] == 'Branch-Wise'){

        $QUERY = " where A.`brunch_id` = '".$_POST['EXTRAFILED']."' AND A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND A.`generate_challan` = 'Done'" ;

    }else{
        
        $QUERY = "  where A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' AND A.`generate_challan` = 'Done' ";
    }


    $report = '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
    <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$_POST['action'].'</th></tr> ';
    
    $report .= '</table></div>';
    


if( $_POST['report_type'] == 'Multipal-Product-Wise' ||  $_POST['report_type'] == 'Multipal-Category-Wise' ){
    $report .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
            <div class="table-responsive">
            <table id="example" class="table table-hover table-condensed table-striped table-bordered" >

<thead>
            <th>Sl</th>
            <th>Invoice No</th>
            <th>Shop Name</th>
            <th>Invoice Date</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Sales By</th>
            <th>Brunch</th>
            <th>Warehouse Branch</th>
            <th>Qty</th>
            <th>Rate</th>


            </thead>
                <tbody>'; 
    $sl =1;
  
    



    $total_sold =0;
    $qry = $conn_me->prepare("SELECT
    A.sales_quantity,
    A.sales_rate,
    B.invoice_no,
    B.invoice_date,
    C.product_name,
    I.category,
    F.unit,
    H.name AS SalesPerson,
    D.brunch AS sales_brunch,
    E.brunch AS dispatcher_brunch,
    J.shop_name
  FROM
    sales_invoice_item A $QUERY
  ");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

        foreach($fetch_list AS $fetch) {
    
 
                    $report .= '<tr>
                    <td>'.$sl++.'</td>
                    <td>'.$fetch['invoice_no'].'</td>
                    <td>'.$fetch['shop_name'].'</td>
                    <td>'.date("Y/m/d", strtotime($fetch['invoice_date'])) .'</td>
                    <td>'.$fetch['product_name'].'</td>
                    <td>'.$fetch['category'].'</td>
                    <td>'.$fetch['SalesPerson'].'</td>
                    <td>'.$fetch['sales_brunch'].'</td>
                    <td>'.$fetch['dispatcher_brunch'].'</td>
                    <td>'.$fetch['sales_quantity']. ' ' .   $fetch['unit'] .'</td>
                    <td>'.$fetch['sales_rate'].'</td>
    
                   </tr>';

                   $total_sold += $fetch['sales_quantity'];

          
               
    
             
            
            
        
           

        }

        $report .= '<tfoot>
<tr>
    <th colspan="8" style="text-align:right"><b>Total</b></th>
    <th colspan="2">' . $total_sold . '</th>
</tr>
</tfoot>';
 
                    
    $report .= '</tbody> 
          </table>
          </div>
          </div>
                
        </div>
    
    </div>
    </div>';

    
}else{


    $report .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
            <div class="table-responsive">
            <table id="example" class="table table-hover table-condensed table-striped table-bordered" >
            <thead>
            <th>Invoice No.</th>
            <th>Date</th>
            <th>Shop Name</th>
            <th>Address</th>
            <th>Sales By</th>
            <th>Sales Person</th>
            <th>Brunch</th>
            <th>Warehouse Branch</th>
            <th>Pcs</th>

            <th>Sub Total</th>
            <th>Vat</th>
            <th>Transport Cost</th>
            <th>Discount</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Due</th>
            <th>Action</th>
            </thead>
                <tbody>'; 
    $sl =0;
    $total_sub_total =0;
    $total_vat =0;
    $total_transport =0;
    $total_discount =0;
    $total_invoice =0;
    $total_receive =0;
    $total_invoice_due =0;
    $total_qty = 0 ;
    $qry = $conn_me->prepare("
    
    SELECT A.invoice_date,A.invoice_no,A.`code`,A.`id`,A.`brunch_id` ,B.brunch ,C.customer_name,C.shop_name,C.address,I.brunch as BrunchName,
    F.name AS sales_person_name,
    H.name AS sales_by_name,
    COALESCE(D.in_amount, 0) AS in_amount,
    ROUND(COALESCE(SUM(si.sales_quantity * si.sales_rate), 0), 2) AS invoice_price,
    ROUND(COALESCE(SUM(si.sales_quantity), 0), 2) AS QTY,
    A.discount,
    A.transport_cost,
    A.total_vat_cost
    
    FROM `sales_invoice`  A 

    INNER JOIN 
    sales_invoice_item si ON A.id = si.sales_invoice_id


    JOIN 
    setup_brunch B ON (A.brunch_id = B.id)
    JOIN 
    setup_customer C ON (A.customer_id = C.id)
    LEFT JOIN 
    account_transection D ON (A.transection_id = D.id)


    JOIN 
    admin E ON A.sales_person = E.id
    JOIN 
    setup_employee F ON E.employee_id = F.id
    JOIN 
    admin G ON A.sales_by = G.id
    JOIN 
    setup_employee H ON G.employee_id = H.id
    JOIN 
    setup_brunch I ON A.dispatch_from_which_brunch = I.id



    $QUERY 
    
    GROUP BY 
    A.invoice_no

    ORDER BY A.`id` ASC
    ");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
   
        foreach($fetch_list AS $fetch) {
            $sl++;

            $total_invoice_price =  number_format((float)( ($fetch['invoice_price'] + $fetch['transport_cost'] + $fetch['total_vat_cost'] ) -  $fetch['discount']  ), 2, '.', ''); ; 
            $receive = number_format((float)( $fetch['in_amount']), 2, '.', '');
            $invoice_due = number_format((float)( $total_invoice_price - $fetch['in_amount']), 2, '.', '');

            $report .= '<tr>
            <td>'.$fetch['invoice_no'].'</td>
            <td>'.date("d/m/Y", strtotime($fetch['invoice_date'])) .'</td>
            <td>'.$fetch['shop_name'].'</td>
            <td>'.$fetch['address'].'</td>
            <td>'.$fetch['sales_by_name'].'</td>
            <td>'.$fetch['sales_person_name'].'</td>
            <td>'.$fetch['brunch'].'</td>
            <td>'.$fetch['BrunchName'].'</td>
            <td>'.$fetch['QTY'].'</td>
            <td>'.$fetch['invoice_price'].'</td>
            <td>'.$fetch['total_vat_cost'].'</td>
            <td>'.$fetch['transport_cost'].'</td>
            <td>'.$fetch['discount'].'</td>
            <td>'.$total_invoice_price.'</td>
            <td>'.$receive.'</td>
            <td>'.$invoice_due.'</td>
            <td>

            <div class="btn-group">
            <a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle">Action <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="invoice_copy.php?code='.$fetch['code'].'" target="_BLINK" >Invoice Copy</a></li>
                <li><a href="print.php?print=Delivery Challan&code='.$fetch['code'].'" target="_BLINK">Challan Copy</a></li>
                <li><a href="print.php?print=Godown Copy&code='.$fetch['code'].'" target="_BLINK">Godown Copy</a></li>';


                if($_SESSION['NEWERP_SESS_MEMBER_ID'] == '149' ){ // only for linkon vai
                $report .= ' <li><a onclick="HardDelete(\'INVOICE\',\''.$fetch['id'].'\')">Delete</a></li>';
            }
                $report .= '</ul>
        </div>';

             $report .= '</td>

           </tr>';

           $total_sub_total += $fetch['invoice_price'];
           $total_vat += $fetch['total_vat_cost'];
           $total_transport += $fetch['transport_cost'];
           $total_discount += $fetch['discount'];
           $total_invoice += $total_invoice_price;
           $total_receive += $receive;
           $total_invoice_due += $invoice_due;
           $total_qty += $fetch['QTY'];

        }
    

      
     
        $report .= '<tfoot>
        <tr>
        <th class="text-pull-right;" colspan="8">Total Invoice: '.$sl.'</td>
              <th class="text-pull-right"><b>'.$total_qty.'</b></th>

        <th class="text-pull-right"><b>'.$total_sub_total.'</b></th>
        <th class="text-pull-right"><b>'.$total_vat.'</b></th>
        <th class="text-pull-right"><b>'.$total_transport.'</b></th>
        <th class="text-pull-right"><b>'.$total_discount.'</b></th>
        <th class="text-pull-right"><b>'.$total_invoice.'</b></th>
        <th class="text-pull-right"><b>'.$total_receive.'</b></th>
        <th class="text-pull-right"><b>'.$total_invoice_due.'</b></th>
        <th></th>
        </tr>
        </tfoot>';


            
    $report .= '</tbody> 
          </table>';
          $report .= '
          </div>
          </div>
        </div>
    
    </div>
    </div>';
}
   
    

    print  $report ;


}else if( $_POST['action'] == 'RAW Goods Movement' ){

    
    $FROMDATE = date("Y-m-d", strtotime($_POST['date_from']));
    $TODATE = date("Y-m-d", strtotime($_POST['date_to']));

    $create_movement  = FIND::RAW_MOVEMENT($_POST['EXTRAFILED'],$FROMDATE,$TODATE);

    $INFOPRODUCT = SETUP::SETUP_RAW_MATERIAL($_POST['EXTRAFILED']);



    $report = '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body">
           
            <table class="table datatable" id="MSalary">
            <thead>
            <th>Date</th>
            <th>Description</th>
            <th>In Quantity	</th>
            <th>Out Quantity</th>
            <th>Closing Stock</th>
          

            </thead>
                <tbody>'; 

$query =$conn_me->prepare("
    SELECT `movment_date`,`time`,
           `note`,
           `in_amount`,
           `out_amount`,
           (@rt:=@rt + (`in_amount` - `out_amount`)) AS runningbalance
    FROM `tempTable_raw_movement`, (SELECT @rt:=0) rt
   
   ");
    $query->execute();

$fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach($fetch_list AS $fetch) {

            $report .= '<tr>
            <td>'.date("d-m-Y", strtotime($fetch['movment_date'])).'</td>
            <td>'.$fetch['note'].'</td>';
            
            if($fetch['note'] == 'Closing Balance' ){
                $report .= '<td><p style="display:none">'.$fetch['in_amount'].' ' . $INFOPRODUCT['unit'] .'</p></td>';
                $report .= '<td><p style="display:none">'.$fetch['out_amount'].' '. $INFOPRODUCT['unit'] . '</p></td>';

            }else{
                $report .= '<td>'.$fetch['in_amount'].' ' . $INFOPRODUCT['unit'] .'</td>';
                $report .= '<td>'.$fetch['out_amount'].' ' . $INFOPRODUCT['unit'] . '</td>';

            }


            $report .= '
            <td>'.$fetch['runningbalance'].' ' . $INFOPRODUCT['unit'] .'</td>
           

           </tr>';

        
        }
    
    
                    
              
    $report .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';

        
        print  $report ;

}else if( $_POST['action'] == 'Opening Stock Report' ){





    $date_to = date("Y-m-d", strtotime($_POST['date_to']));
    $date_from = date("Y-m-d", strtotime($_POST['date_from']));

     $ids = implode(',', $_POST['warehouse_id']);      


    $content = '';


    $content .= '<div class="row">
    <div class="col-md-12"><h3 style="color:red;align:center">Opening Stock Report From '.$_POST['date_from'].' To '.$_POST['date_to'].'</h3>
    
        <div class="panel panel-default">
            <div class="panel-body">
            <div class="table-responsive"><table style="width:100%;" class="table datatable" id="example"><thead>
    <tr>
        <th >Sl</th>
        <th >Date</th>
        <th >Category</th>
        <th>Product</th>
        <th>Warehouse</th>
        <th>Qty</th>
        <th>Sales Price</th>
        <th >Total</th>
        <th >Notes</th>
        <th >User</th>

    </tr></thead> <tbody>';




       $sl=1;

    $report_qry = $conn_me->prepare("SELECT A.notes,A.product_id,A.quantity,D.name as EmployeeName,
    DATE_FORMAT(A.invoice_date, '%d/%m/%Y') AS date
    ,B.name FROM `fg_opening_stock` A 
    JOIN setup_warehouse B ON (A.warehouse_id = B.id)
    JOIN admin C ON (A.poster = C.id)
    JOIN setup_employee D ON (C.employee_id = D.id)
    where   A.`warehouse_id` IN  ({$ids})  AND A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."'   ORDER BY A.`invoice_date` ASC");
    $report_qry->execute();
    $fetch_report = $report_qry->fetchAll(PDO::FETCH_ASSOC);
 $grandTotal = 0 ;

$total_qty = 0;
                    foreach($fetch_report AS $fetch_R) { 
                        $notes = ($fetch_R['notes'] == 'no notes' ) ? ' ' : $fetch_R['notes'] ;
                        

                        $product_info = SETUP::SETUP_PRODUCT($fetch_R['product_id']);
                
                          $total = number_format((float)($fetch_R['quantity']*$product_info['sales_rate']), 2, '.', '');

                        $content .='<tr>
                        <td >'.$sl++.'</td>
                        <td >'.$fetch_R['date'].'</td>
                         <td>'.$product_info['category'].'</td>
                        <td>'.$product_info['product_name'].'</td>
                        <td >'.$fetch_R['name'].'</td>
                        <td >'.$fetch_R['quantity'].'</td>
                        <td >'.$product_info['sales_rate'].'</td>
                        <td >'.$total.'</td>
                        <td style="word-break: break-word; white-space: normal;">'.$notes.' </td>
                        <td >'.$fetch_R['EmployeeName'].'</td>

                        </tr>';
                        
                        $grandTotal += $total ;
                        $total_qty += $fetch_R['quantity'];
                    }
        
                    
        


    $content .= ' </tbody>';
         $content .= '<tfoot>
<tr>
    <th></th> 
    <th></th> 
    <th></th> 
      <th></th>   <th></th> 
    <th>Total</th>
    <th>' . $total_qty . '</th>
    <th>' . $grandTotal . '</th>
    <th></th>
</tr>
</tfoot>
</table></div></div></div></div></div>';





       print  $content;




}else if( $_POST['action'] == 'Invoice Wise Raw Opening Stock' ){

    $FROMDATE = date("Y-m-d", strtotime($_POST['date_from']));
    $TODATE = date("Y-m-d", strtotime($_POST['date_to']));

    $company_info = SETUP::SETUP_COMPANY('Active');

    $content = '';


    $content .= '<table style="width:100%;" class="table datatable_simple">
    <tr><th colspan="6" style="text-align:center">Invoice Wise Raw Opening Stock</th></tr>        
    <tr>
        <th style="text-align:center;">Sl</th>
        <th style="text-align:center;">Invoice No</th>
        <th style="text-align:center;">Product</th>
        <th style="text-align:center;">Quantity</th>
        <th style="text-align:center;">Poster</th>
        <th style="text-align:center;">Approve By</th>

    </tr>';


if($_POST['report_type'] == 'Invoice Wise' ){
$QUERY = " where `code` = '".$_POST['EXTRAFILED']."' ";
}else if ($_POST['report_type'] == 'Date Wise'){
    $QUERY = " where `code` = '".$_POST['EXTRAFILED']."' ";

}else{
    $QUERY = "";

}

       $sl=1;

    $report_qry = $conn_me->prepare("SELECT * FROM `raw_opening_stock` $QUERY  ORDER BY `date` ASC");
    $report_qry->execute();
    $fetch_report = $report_qry->fetchAll(PDO::FETCH_ASSOC);
    if ($report_qry->rowCount() > 0)
         {
                    foreach($fetch_report AS $fetch_R) { 

                        $product_info = SETUP::SETUP_PRODUCT($fetch_R['product_id']);
                        $poster_info = SETUP::ADMIN_SETUP($fetch_R['poster']);

                        if(!empty($fetch_R['approve_by'] )){
                            $approve_by_info = SETUP::ADMIN_SETUP($fetch_R['approve_by']);

                            $approve_by = $approve_by_info['hr_name']; }else{ $approve_by = 'Not Approved Yet';}


                        $content .='<tr>
                        <td style="text-align:center">'.$sl++.'</td>
                        <td style="text-align:center">'.$fetch_R['invoice_no'].'</td>
                        <td style="text-align:center">'.$product_info['product_name'].'</td>
                        <td style="text-align:center">'.$fetch_R['quantity'].'</td>
                        <td style="text-align:center" >'.$poster_info['hr_name'].'</td>
                        <td style="text-align:center">'.$approve_by.'</td>

                        </tr>';
        
        
                     }
        
    } else {
        $content .='<tr>
            <th style="text-align:center;color:red" colspan="2">No Record Found</th></tr>';
        
        }

    $content .= '</table>';





       print $com_profile['header_content'] .  $content;




}else if( $_POST['action'] == 'Finished Goods Movement' ){

    $FROMDATE = date("Y-m-d", strtotime($_POST['date_from']));
    $TODATE = date("Y-m-d", strtotime($_POST['date_to']));

    

    $create_movement  = FIND::FG_MOVEMENT($_POST['product_id'],$_POST['related_id'],$FROMDATE,$TODATE,$_POST['report_type']);
    $INFOPRODUCT = SETUP::SETUP_PRODUCT($_POST['product_id']);

    
 
    $report = '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body">
            <div class="table-responsive">

            <table class="table datatable" id="MSalary">
            <thead>
            <th>Sl</th>

            <th>Date</th>
            <th>User</th>
            <th>Description</th>

            <th>In Quantity	</th>
            <th>Out Quantity</th>
            <th>Closing Stock</th>
                        <th>ACTION</th>

            </thead>
                <tbody>'; 

$query =$conn_me->prepare("
    SELECT A.section,A.related_id,A.`movment_date`,A.`time`,A.`link`,A.`poster`,D.`name`,
           A.`note`,
           A.`in_amount`,
           A.`out_amount`,
           (@rt:=@rt + (A.`in_amount` - A.`out_amount`)) AS runningbalance
    FROM `tempTable_product_movement` A 
    JOIN `admin` B ON (A.`poster` = B.`id`)
    JOIN `setup_employee` D ON (B.`employee_id` = D.`id`)
    , (SELECT @rt:=0) rt
    ORDER BY A.`movment_date` ASC");
    $query->execute();
$sl = 1;
$fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

$total_in = 0 ; 
$total_out = 0 ; 
$opening = 0 ;

        foreach($fetch_list AS $fetch) {

            $report .= '<tr>
            <td>'.$sl++.'</td>
            <td>'.date("d-m-Y", strtotime($fetch['movment_date'])).' ' . $fetch['time'] .'</td>';

            if($fetch['note'] == 'Closing Balance' ){
                $report .= '<td></td>';

            }else{
                $report .= '<td>'.$fetch['name'].'</td>';

            }

            if($fetch['link'] === 'No LInk' ){
                $report .= '<td>'.$fetch['note'].'</td>';
            }else{
                $report .= '<td><a href="'.$fetch['link'].'">'.$fetch['note'].'</a></td>';
            }
            
            if($fetch['note'] == 'Closing Balance' ){
                $report .= '<td><p style="display:none">'.$fetch['in_amount'].' '.$INFOPRODUCT['unit'].'</p></td>';
                $report .= '<td><p style="display:none">'.$fetch['out_amount'].' '.$INFOPRODUCT['unit'].'</p></td>';
$opening += $fetch['in_amount'] ;
            }else{
                $report .= '<td>'.$fetch['in_amount'].' '.$INFOPRODUCT['unit'].'</td>';
                $report .= '<td>'.$fetch['out_amount'].' '.$INFOPRODUCT['unit'].'</td>';

            }


            

            $report .= '
            <td>'.$fetch['runningbalance'].' '.$INFOPRODUCT['unit'].'</td>';
           
             $report .= '<td>';

             if($fetch['related_id'] != 0 ){
            $report .= '<input type="button" onclick="DeleteFGMovement(\''.$fetch['related_id'].'\',\''.$fetch['section'].'\',\''.$fetch['in_amount'].'\',\''.$fetch['out_amount'].'\');" class="btn btn-danger" value="DELETE">';

             }
               $report .= '</td>';

            $report .= '</tr>';

$total_in += $fetch['in_amount'] ; 
$total_out += $fetch['out_amount'] ; 
        
        }
    
     $report .= '<tfoot>
<tr>
    <th></th> 
    <th></th> 
    <th></th> 
    <th>Total</th>
    <th>' . ($total_in - $opening) . ' ' . $INFOPRODUCT['unit'] . '</th>
    <th>' . $total_out . ' ' . $INFOPRODUCT['unit'] . '</th>
    <th></th>
</tr>
</tfoot>';
                    
              
    $report .= '</tbody> 
          </table>
          </div>
          </div>   
        </div>
    
    </div>
    </div>';

        
        print  $report ;

}else if( $_POST['action'] == 'Salary-Report' ){


    if($_POST['report_type'] == 'Monthly-Salery'){

    $explode = explode("-",$_POST['date_from']);
    $number_of_days = cal_days_in_month(CAL_GREGORIAN, $explode['0'], $explode['1']);


    $report = '<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' '.$_POST['report_type'].'\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\''.$_POST['report_type'].'\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div> ';


    $report .= '<div class="row">
    <div class="col-md-12">
   												 											
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
            <div class="table-responsive">
            <table class="table table-hover table-condensed table-striped table-bordered datatble" id="MSalary" style="white-space:nowrap;" id="MSalary" >
            <thead>
            <th>Sl</th>
            <th>Name</th>
            <th>Department</th>
            <th>Medical	</th>
            <th>House Rent</th>
            <th>Basic</th>
            <th>Over Time Bill</th>
            <th>Monthly Drawing</th>
            <th>Per day Salary</th>
            <th>Total Absent</th>
            <th>Net Absent (Total Absent-Grace 1 day absent)</th>
            <th>Absent Deduction</th>
            <th>Payable Salary after absent deduction</th>
            <th>Deductuon From Advance</th>
            <th>To be Paid/Paid Salary</th>
            <th>New Salary Due</th>
            <th>Previos Advance</th>
            <th>Last Month Advance</th>
            <th>Advance Paid</th>
            <th>New Total Due</th>

            </thead>
                <tbody>'; 
$sl=1;
$query =$conn_me->prepare("SELECT A.*,  C.department AS present_department_name FROM `setup_employee` A         
LEFT JOIN setup_department C ON (A.present_department = C.id )
 where A.hr_status = 'Active' order by A.`id` ");
    $query->execute();

$fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach($fetch_list AS $fetch) {




 $perday_sallery = number_format((float)($fetch['present_salary']/$number_of_days), 2, '.', '') ;
            
            $total_absent = FIND::TOTAL_ACTIVITY_GIVEN_MONTH($_POST['date_from'],'absent',$fetch['id']);
            $advance_transection = FIND::ADVANCE_TRANSECTION($_POST['date_from'],$fetch['id']);


            $net_absemt = $total_absent['total_days']-1;
            $absent_deducation = $net_absemt*$perday_sallery;
            $after_deducation = $fetch['present_salary']-$absent_deducation;
            $deducation_from_advanve = 0.00;
            $to_be_paid =  $after_deducation -  $deducation_from_advanve;
            $new_salry_due =  $to_be_paid -  $after_deducation;
            $new_total_due = $new_salry_due+$advance_transection['total_advance_paid'];


            $report .= '<tr>';
       
            $report .= '<td>'.$sl++.'</td>';
            $report .= '<td>'.$fetch['name'].'</td>';
            $report .= '<td>'.$fetch['present_department_name'].'</td>';
            $report .= '<td>'.$fetch['medical'].'</td>';
            $report .= '<td>'.$fetch['house_rent'].'</td>';
            $report .= '<td>'.$fetch['basic'].'</td>';
            $report .= '<td>'.$fetch['over_time_bill'].'</td>';
            $report .= '<td>'.$fetch['present_salary'].'</td>';
            $report .= '<td>'.$perday_sallery.'</td>';
            $report .= '<td>'.$total_absent['total_days'].'</td>';
            $report .= '<td>'.$net_absemt.'</td>';
            $report .= '<td>'.$absent_deducation.'</td>';
            $report .= '<td>'.$after_deducation.'</td>';
            $report .= '<td>'.$deducation_from_advanve.'</td>';
            $report .= '<td>'.$to_be_paid.'</td>';
            $report .= '<td>'.$new_salry_due.'</td>';
            $report .= '<td>'.$advance_transection['previous_advance'].'</td>';
            $report .= '<td>'.$advance_transection['last_month_advance'].'</td>';
            $report .= '<td>'.$advance_transection['total_advance_paid'].'</td>';
            $report .= '<td>'.$new_total_due.'</td>';

            
            $report .= '</tr>';

        
        }
    
    
                    
              
    $report .= '</tbody> 
          </table>
          </div>
          </div> 
        </div>
    
    </div>
    </div>';


    print  $report ;



    }else if ($_POST['report_type'] == 'Payment-Record'){
        print  $report ;
    }else{
        print  $report ;
    }

    



}else{

    print '505 error';
}
?>


