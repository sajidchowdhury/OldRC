<?php
include_once("auth.php");
include('function_query.php');

$conn_me = Database::getInstance();

?>
 <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>

 
<?php 
if($_POST['action'] == 'Brunch-Wise-Due' ){

    include('bangla-convert.php');


    $date_to = date("Y-m-d", strtotime($_POST['date_to']));
    $prev_date = date('Y-m-d', strtotime($date_to .' -1 day'));



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
        <table class="table  table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;" >
            <thead>';


            $sl=1;

    $content .='<tr>
    <td>বিবরণ</td>
    <td>Nowabpur </td>
    <td>Head Office</td> 
    ';


    $content .='</thead>
    <tbody>
    ';

    $yesterday_stock_value = STOCK::ALL_ITEM_STCOK_BY_DATE($date_to,'PREVIOUS');
    $endofday_stock_value = STOCK::ALL_ITEM_STCOK_BY_DATE($date_to,'TODAY');



    $product_in_value_headoffice_to_nowapur = STOCK::PRODUCT_IN_HEADOFFICE_TO_NOWABPUR($date_to);

    $product_out_value_headoffice_to_nowapur = ( $endofday_stock_value['total_stock_in_nowabpur'] + $product_in_value_headoffice_to_nowapur ) - $yesterday_stock_value['total_stock_in_nowabpur'] ; 

    $total_memo_today = STOCK::TOTAL_MEMO($date_to,$_POST['brunch_id']);
    $total_memo_value= STOCK::TOTAL_MEMO_VALUE($date_to,$_POST['brunch_id']);


    $brunch_wise_total_memo_value_nowapbur= STOCK::BRUNCH_WISE_TOTAL_MEMO_VALUE($date_to,$_POST['brunch_id'],'NOWAPBUR');
    $brunch_wise_total_memo_value_headoffice = STOCK::BRUNCH_WISE_TOTAL_MEMO_VALUE($date_to,$_POST['brunch_id'],'HEADOFFICE');

    $today_customer_due =  FIND::CUSTOMERDUEALLCLIENT($date_to,$_POST['brunch_id']);
    $closinng_customer_due =  FIND::CUSTOMERDUEALLCLIENT($prev_date,$_POST['brunch_id']);

    $today_invoice_by_clientpayment =  FIND::SALESRECORDBYDATEANDBRUNCH($date_to,$_POST['brunch_id']);
    $all_day_customer_due = $today_invoice_by_clientpayment['total_invoice_price'] - $today_invoice_by_clientpayment['total_paid'] ; 

    $saradin_sales_theke_nogod_bikri = $total_memo_value - $all_day_customer_due;

    $opening_cash = TEST_BOOK::calculateOpeningBalance($date_to,$_POST['brunch_id']);



    $cash_sales_data = TEST_BOOK::calculateSectionWiseTransection($date_to, 'INCOME', 'Invoice Wise Payment','Cash',$_POST['brunch_id']);
    $cash_customer_tr_data = TEST_BOOK::calculateSectionWiseTransection($date_to, 'INCOME', 'CUSTOMER-TRANSACTION', 'Cash', $_POST['brunch_id']);
    $total_cash_collection = $cash_sales_data['total_balance'] +  $cash_customer_tr_data['total_balance'];

    $bank_sales_data = TEST_BOOK::calculateSectionWiseTransection($date_to, 'INCOME', 'Invoice Wise Payment','Bank',$_POST['brunch_id']);
    $bank_customer_tr_data = TEST_BOOK::calculateSectionWiseTransection($date_to, 'INCOME', 'CUSTOMER-TRANSACTION','Bank',$_POST['brunch_id']);
    $total_bank_collection = $bank_sales_data['total_balance'] +  $bank_customer_tr_data['total_balance'];



    $mobile_customer_tr_data = TEST_BOOK::calculateSectionWiseTransection($date_to, 'INCOME', 'CUSTOMER-TRANSACTION','Mobile-Banking',$_POST['brunch_id']);
    $mobile_sales_data = TEST_BOOK::calculateSectionWiseTransection($date_to, 'INCOME', 'Invoice Wise Payment','Mobile-Banking',$_POST['brunch_id']);
    $total_mobile_collection = $mobile_customer_tr_data['total_balance'] +  $mobile_sales_data['total_balance'];

   $total_collection =  $total_cash_collection + $total_bank_collection + $total_mobile_collection ; 



   $cash_money_tranfer_from_data = TEST_BOOK::calculateSectionWiseTransection($date_to, 'EXPENSE', 'MONEY-TRANSFER-FROM', 'Cash', $_POST['brunch_id']);
   $bank_money_tranfer_from_data = TEST_BOOK::calculateSectionWiseTransection($date_to, 'EXPENSE', 'MONEY-TRANSFER-FROM','Bank',$_POST['brunch_id']);
   $mobile_money_tranfer_from_data = TEST_BOOK::calculateSectionWiseTransection($date_to, 'EXPENSE', 'MONEY-TRANSFER-FROM','Mobile-Banking',$_POST['brunch_id']);


   $cash_add_expense_data = TEST_BOOK::calculateSectionWiseTransection($date_to, 'EXPENSE', 'ADD EXPENSE', 'Cash', $_POST['brunch_id']);

   $closing_cash = ($opening_cash + $total_cash_collection - $cash_money_tranfer_from_data['total_balance'] - $cash_add_expense_data['total_balance'] ) ; 



   $total_memo_value_half_half= STOCK::TOTAL_MEMO_HALF_HEADOFFICE_HALF_NOWABUR($date_to,$_POST['brunch_id']);


   $total_memo_value_nowabpur_only= STOCK::TOTAL_MEMO_HEADOFFICE_AND_NOWABUR_SEPERATLY($date_to,$_POST['brunch_id']);
   $total_memo_value_headoffice_only= STOCK::TOTAL_MEMO_HEADOFFICE_AND_HEADOFFICE_SEPERATLY($date_to,$_POST['brunch_id']);


   $day_start_nowbpur_owe_headoffice = $yesterday_stock_value['total_stock_in_nowabpur'] + $today_customer_due['customer_due'] ; 

   $product_out_value = $product_in_value_headoffice_to_nowapur + $brunch_wise_total_memo_value_headoffice['total_invoice_price'] + $total_memo_value_headoffice_only ; 


   $day_end_nowbpur_owe_headoffice = $day_start_nowbpur_owe_headoffice + $product_out_value - $cash_money_tranfer_from_data['total_balance'] + $bank_money_tranfer_from_data['total_balance'] ; 


    $content .='<tr><td>দিনের শুরুতে প্রোডাক্ট ভ্যালু কত টাকা</td><td>'.$yesterday_stock_value['total_stock_in_nowabpur'].'</td><td>'.$yesterday_stock_value['total_stock_in_headoffice'].'</td></tr>';

    $content .='<tr><td>দিনশেষে প্রোডাক্ট ভ্যালু কত টাকা</td><td>'.$endofday_stock_value['total_value_in_nowabpur'].'</td><td>'.$endofday_stock_value['total_value_in_headoffice'].'</td></tr>';

    $content .='<tr><td>সারাদিনের প্রোডাক্ট ইন ভ্যালু কত টাকা (হেড অফিস গোডাউন থেকে ব্রাঞ্চ গোডাউন)</td><td>'.$product_in_value_headoffice_to_nowapur.'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের প্রোডাক্ট আউট ভ্যালু কত টাকা (ব্রাঞ্চ গোডাউন থেকে কাস্টোমার পর্যন্ত)</td><td>'.$product_out_value_headoffice_to_nowapur.'</td><td></td></tr>';




    $content .='<tr><td>সারাদিনের মোট মেমো সংখ্যা </td><td>'.$total_memo_today.'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের মোট মেমোতে সেলস ভ্যালু</td><td>'.$total_memo_value.'</td><td></td></tr>';


    
    
    
    
    $content .='<tr><td>সারাদিনের মোট মেমো সংখ্যা (ব্রাঞ্চ গোডাউন থেকে)</td><td>'.$brunch_wise_total_memo_value_nowapbur['invoic_count'].'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের মোট মেমো ভ্যালু (ব্রাঞ্চ গোডাউন থেকে)</td><td>'.$brunch_wise_total_memo_value_nowapbur['total_invoice_price'].'</td><td></td></tr>';

    $content .='<tr><td>সারাদিনের মোট মেমো সংখ্যা (হেড অফিস গোডাউন থেকে)</td><td>'.$brunch_wise_total_memo_value_headoffice['invoic_count'].'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের মোট মেমো ভ্যালু (হেড অফিস গোডাউন থেকে)</td><td>'.$brunch_wise_total_memo_value_headoffice['total_invoice_price'].'</td><td></td></tr>';


    $content .='<tr><td>সারাদিনের মোট মেমো সংখ্যা (আংশিক ব্রাঞ্চ ও আংশিক হেড অফিস গোডাউন থেকে)</td><td>'.$total_memo_value_half_half['invoic_count'].'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের মোট মেমো ভ্যালু (আংশিক ব্রাঞ্চ ও আংশিক হেড অফিস গোডাউন থেকে)</td><td>'.$total_memo_value_half_half['total_invoice_price'].'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের মোট মেমো ভ্যালু (আংশিক ব্রাঞ্চ থেকে)</td><td>'.$total_memo_value_nowabpur_only.'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের মোট মেমো ভ্যালু (আংশিক হেড অফিস গোডাউন থেকে)</td><td>'.$total_memo_value_headoffice_only.'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনে ব্রাঞ্চের জন্য প্রোডাক্ট আউট ভ্যালু কত টাকা (হেড অফিস গোডাউন থেকে)</td><td>'.$product_out_value .'</td><td></td></tr>';
    $content .='<tr><td>দিনের শুরুতে হেড অফিসের পাওনা ব্রাঞ্চের কাছে</td><td>'.$day_start_nowbpur_owe_headoffice.'</td><td></td></tr>';
    $content .='<tr><td>দিনশেষে হেড অফিসের পাওনা ব্রাঞ্চের কাছে</td><td>'.$day_end_nowbpur_owe_headoffice.'</td><td></td></tr>';
    $content .='<tr><td>দিনের শুরুতে কাস্টোমার ডিউ</td><td>'.$today_customer_due['customer_due'].'</td><td></td></tr>';
    $content .='<tr><td>দিনশেষে কাস্টোমার ডিউ</td><td>'.$closinng_customer_due['customer_due'].'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের সেলস মেমো থেকে কাস্টোমার ডিউ</td><td>'.$all_day_customer_due.'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের সেলস মেমো থেকে নগদ বিক্রি</td><td>'.$saradin_sales_theke_nogod_bikri.'</td><td></td></tr>';
    $content .='<tr><td>দিনের শুরুতে ক্যাশ</td><td>' . $opening_cash . '</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের মোট কালেকশন</td><td>'.$total_collection.'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের নগদ কালেশন </td><td>'.$total_cash_collection.'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের ব্যাংক কালেশন </td><td>'.$total_bank_collection.'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের মোবাইলে কালেশন </td><td>'.$total_mobile_collection.'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের হেড অফিস ট্রান্সফার বাই ক্যাশ</td><td>'.$cash_money_tranfer_from_data['total_balance'].'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের হেড অফিস ট্রান্সফার বাই ব্যাংক</td><td>'.$bank_money_tranfer_from_data['total_balance'].'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের হেড অফিস ট্রান্সফার বাই মোবাইল</td><td>'.$mobile_money_tranfer_from_data['total_balance'].'</td><td></td></tr>';
    $content .='<tr><td>সারাদিনের খরচ</td><td>'.$cash_add_expense_data['total_balance'].'</td><td></td></tr>';
    $content .='<tr><td>দিনশেষে ক্যাশ</td><td>'.$closing_cash.'</td><td></td></tr>';




    $content .= '</tbody> 
    </table>
    </div>
    </div>      
  </div>

</div>
</div>';





print  $content; 


    die();
}


