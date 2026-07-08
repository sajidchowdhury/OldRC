<?php 

$content = '<input type="hidden" id="report_type" value="All" ><input type="hidden" id="date_from" value=""><input type="hidden" id="date_to" value="">';

$content .= '<div class="row">
<div class="col-md-12">

<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Supplier List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Supplier-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>
    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
        <div class="table-responsive ">

        <table class="table table-hover table-condensed table-striped table-bordered datatable" id="MSalary">
        <thead>
        <th>Sl</th>
        <th>Product Name</th>
        <th>Category</th>
        <th>Unit</th>
        <th>Stock Limit </th>
        <th>Recent Stock (Pcs)</th>
        <th>Recent Stock (CTN)</th>
     

        </thead>
            <tbody>'; 
$sl =1;
$qry = $conn_me->prepare("SELECT `id` FROM `setup_product`  WHERE  `in_service` = 'checked' ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

    foreach($fetch_list AS $fetch) {

        $stock_info = STOCK::FG_ITEM_WISE_STOCK('',$fetch['id'],'product_wise');
        $product_info = SETUP::SETUP_PRODUCT($fetch['id']);

if($stock_info['ITEM_STOCK'] < $product_info['safty_stock']){

    
    if($product_info['pcs_in_cartoon'] > 0 ){
        $in_carton = $stock_info['ITEM_STOCK']/($product_info['pcs_in_cartoon'] ?? 0 );
        }else{
        $in_carton = 0.00;
        }


    $content .= '<tr>
    <th>'.$sl++.'</th>
    <th>'.$product_info['product_name'].'</th>
    <th>'.$product_info['category'].'</th>
    <th>'.$product_info['unit'].'</th>
    <th>'.$product_info['safty_stock'].'</th>
    <th>'.$stock_info['ITEM_STOCK'].'</th>
    <th>'.$in_carton.'</th>

   </tr>';


}
   
    }


                
                  
$content .= '</tbody> 
      </table>
      </div>
      </div>
            
    </div>

</div>
</div>';

print $content ;




?>

