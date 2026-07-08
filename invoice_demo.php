<?php
include_once("auth.php");
include('function_query.php');
include('bangla-convert.php');

$invoice_info = SETUP::SETUP_SALES_INVOICE($_GET['code']);
$company_info = SETUP::SETUP_COMPANY('Active');
$obj = new BanglaNumberToWord();


?>


<!DOCTYPE html>
<html>


<head>
<script src="https://unpkg.com/pagedjs/dist/paged.polyfill.js"></script>

</head>
<style>


.pagedjs_pages > .pagedjs_page > .pagedjs_sheet > .pagedjs_pagebox > .pagedjs_area > div [data-split-from] {
    text-indent: unset;
    margin-top: auto;
    padding-top: 115px;

}

.title {
  position: running(titleRunning);
 
}



   @page {
    size: A4;
  bleed: 32mm 2mm ;
  margin: 0mm;


    @top-center {
        
        content: element(titleRunning);
        margin: 10mm 0mm 16mm 0mm;


    }
    @bottom-left {
    content: " " url("invoice_footer.svg");
    margin: 8mm 0mm 0mm 0mm;
    }

    
    @bottom-center {
        content: "নিজে নামাজ পড়ুন, অপরকে নামাজ পড়তে উৎসাহিত করুন";
        margin: 8mm 0mm 0mm 0mm;

    }
    
    @bottom-right {
        content: "Page " counter(page) " of " counter(pages);
        margin: 8mm 0mm 0mm 0mm;
    
    
    }

    .break-after:nth-child(18n) {
    break-after: page;
    }

}

@media print {
    @top-center {
        
        content: element(titleRunning);
        margin: 10mm 0mm 16mm 0mm;


    }
    @bottom-left {
    content: " " url("invoice_footer.svg");
    margin: 8mm 0mm 0mm 0mm;
    }

    
    @bottom-center {
        content: "নিজে নামাজ পড়ুন, অপরকে নামাজ পড়তে উৎসাহিত করুন";
        margin: 8mm 0mm 0mm 0mm;

    }
    
    @bottom-right {
        content: "Page " counter(page) " of " counter(pages);
        margin: 8mm 0mm 0mm 0mm;
    
    
    }

    .break-after:nth-child(18n) {
    break-after: page;
    }
  
}


.watermark {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: -1;
    }

    
</style>
<body id="printableArea">
<img src="img/trans_img.png" class="watermark">


<table> 

<tr>
<div class="pagedjs_margin pagedjs_margin-top-center hasContent">
  <div class="pagedjs_margin-content">

  
<table class="title">
<tr>
<td colspan="6"><img src="challan_copy.svg" alt=""></td>
</tr>
<tr>
    <td colspan="6" style="text-align:center">

    <text id="address1" transform="matrix(1 0 0 1 180.7775 77.9423)" style="font-size:14px;"> <?php echo $invoice_info['sales_person_brunch_address1'] ?> </text>
    <br>
    <text id="address2" transform="matrix(1 0 0 1 212.007 88.2365)" style="font-size:14px;"> <?php echo $invoice_info['sales_person_brunch_address2'] ?> </text>

    </td>
</tr>

<tr >
<td colspan="2" style="line-height:10px;text-align:left;padding: 10px 10px;border : 1px solid; ">চালান নং <?php print  $invoice_info['invoice_no'];?></td>
<td colspan="2"  style="line-height:10px;text-align:center;font-size:12px;"></td>

<td colspan="2"  style="line-height:10px;text-align:right;padding: 10px 10px;border: 1px solid ">তারিখঃ<?php print  $invoice_info['invoice_date'];?></td>
</tr>
<tr ><td colspan="6" style=""></td></tr>

<tr><td colspan="3" style="line-height:10px;text-align:left; padding: 10px 10px;border: 1px solid black">নামঃ <?php print $invoice_info['shop_name'];?></td>
<td colspan="3" style="line-height:10px;text-align:left; padding: 10px 10px;border: 1px solid black">মোবাইল নংঃ<?php print $invoice_info['mobile'];?></td>
</tr>
<tr><td colspan="6" style=""></td></tr>
<tr>
<td colspan="2"  style="line-height:10px;text-align:left; padding: 10px 10px;border: 1px solid black">ঠিকানাঃ <?php print $invoice_info['address'];?></td>
<td  colspan="2" style="line-height:10px;text-align:left; padding: 10px 10px;border: 1px solid black">উপঃ <?php print $invoice_info['upazila'];?></td>
<td colspan="2"  style="line-height:10px;text-align:left; padding: 10px 10px;border: 1px solid black">জেলাঃ <?php print $invoice_info['district'];?></td>
</tr>

    <tr>
    <td style="center;width:10%;padding: 10px 10px;"></td>
    <td style="center;width:60%;padding: 10px 10px;"></td>
    <td style="center;padding: 10px 10px;width:7.5%;"></td>
    <td style="center;padding: 10px 10px;width:7.5%;"></td>
    <td style="center;padding: 10px 10px;width:7.5%;"></td>
    <td style="center;padding: 10px 10px;width:7.5%;"></td>
    </tr>
</table>

</div>
</div>

</tr>
<tr>

<br>
<table  style="width:100%;border-spacing: 0px;" class="productTable" >
    
<tr>
    <td style="border: 1px solid black;text-align:center;width:10%;padding: 10px 10px;"><b>ক্র নং</b></td>
    <td style="border: 1px solid black;text-align:center;width:67.5%;padding: 10px 10px;"><b>পণ্যের নাম</b></td>
    <td style="border: 1px solid black;text-align:center;padding: 10px 10px;width:7.5%;"><b>পরিমান</b></td>
    <td style="border: 1px solid black;text-align:center;padding: 10px 10px;width:7.5%;"><b>একক</b></td>
    <td style="border: 1px solid black;text-align:center;padding: 10px 10px;width:7.5%;"><b>কার্টুন</b></td>
    </tr>
    
    
    <?php 
$product_count = 0;
 $sl =1;
 $qry2 = $conn_me->prepare("SELECT *  FROM `sales_invoice_item` A  where `sales_invoice_id` = '".$invoice_info['id']."' ");
 $qry2->execute();
 $count =   $qry2->rowCount();
 $total_page = ceil($count/17);
 $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
 foreach($fetch_list2 AS $fetch2) { 
    $product_data = SETUP::SETUP_PRODUCT($fetch2['product_id']);     
    if($fetch2['sales_quantity'] > 0 ){
        $qty_in_carton = $fetch2['sales_quantity']/$product_data['pcs_in_cartoon'];

    }else{
        $qty_in_carton = 0.00;   
    }
 
    ?>
    <tr class="break-after">
    <td  style="text-align:left;width:10%;padding: 0.5em;border: 0.5px solid black !important;"><?php print $obj->engToBn($sl++);?></td>
    <td style="text-align:center;font-size:20px;width:67.5%;padding: 0.5em;border: 0.5px solid black !important;"><?php print $product_data['product_name'];?></td>
    <td style="text-align:center;font-size:20px;padding: 0.5em;border: 0.5px solid black !important;width:7.5%"><?php print round($fetch2['sales_quantity'],0);?></td>
    <td style="text-align:center;font-size:20px;padding: 0.5em;border: 0.5px solid black !important;width:7.5%"><?php print $product_data['unit'];?></td>
    <td style="text-align:right;font-size:20px;padding: 0.5em;border: 0.5px solid black !important;width:7.5%"><?php print number_format((float)$qty_in_carton, 2, '.', '');?></td>

    </tr>
    <?php

$product_count++; 
} ?>


<tr>
    <th colspan="5" style="border: 1px solid black;text-align:right;padding: 10px 10px;">মোট</th>
    <td style="border: 1px solid black;text-align:right;padding: 10px 10px;"><?php print number_format((float)$invoice_info['sub_total'], 2, '.', '') ;?></td>
 </tr>
 <tr>
  <th colspan="5" style="border: 1px solid black;text-align:right;padding: 10px 10px;">ভ্যাট</th>
<td style="border: 1px solid black;text-align:right;padding: 10px 10px;"><?php print number_format((float)$invoice_info['total_vat_cost'], 2, '.', '');?></td>
 </tr>
 <tr>
  <th colspan="5" style="border: 1px solid black;text-align:right;padding: 10px 10px;">ডিসকাউন্ট</th>
<td style="border: 1px solid black;text-align:right;padding: 10px 10px;"><?php print number_format((float)$invoice_info['discount'], 2, '.', '') ;?></td>
 </tr>
 <tr>
  <th colspan="5" style="border: 1px solid black;text-align:right;padding: 10px 10px;">ট্রান্সপোর্ট খরচ</th>
<td style="border: 1px solid black;text-align:right;padding: 10px 10px;"><?php print number_format((float)$invoice_info['transport_cost'], 2, '.', '') ;?></td>
 </tr>
 <tr>
  <th colspan="5" style="border: 1px solid black;text-align:right;padding: 10px 10px;">সর্বমোট</th>
  <td style="border: 1px solid black;text-align:right;padding: 10px 10px;"><?php print number_format((float)$invoice_info['total_invoice_price'], 2, '.', '') ;?></td>
 </tr>
 <tr>
  <th colspan="5" style="border: 1px solid black;text-align:right;padding: 10px 10px;">কথায়</th>
  <td style="border: 1px solid black;text-align:right;padding: 10px 10px;"><?php print $obj->numToWord($invoice_info['total_invoice_price']);?> টাকা মাত্র</td>
 </tr>


 <tr>
  <td colspan="6" style="text-align:left;line-height:18px;border: 0px solid !important;font-size:12px;">
  দৃষ্টি আকর্ষণঃ<br>
১। আপনার ক্রয় কৃত মাল নিজ দায়িত্বে চেক করে নিন, পরবর্তীতে কোন অভিযোগ গ্রহণযোগ্য নহে।<br>
২। এখানে ইলেকট্রনিক্স মালের কোন প্রকার গ্যারান্টি বা ওয়ারেন্টি দেয়া হয় না।<br>
৩। দয়া করে আপনার ক্রয়কৃত মাল ভালো কাজের জন্য ব্যবহার করুন।<br>

 </td>
 </tr>

 <tr>
 <th colspan="6" style="padding-top:35px;"></th>
 </tr>


 <tr>
    <th colspan="2" style="text-align:center">....................................</th>
    <th colspan="2"></th>
    <th colspan="2" style="text-align:center">....................................</th>
 </tr>
 <tr>
    <th colspan="2" style="text-align:center">ক্রেতার স্বাক্ষর</th>
    <th colspan="2"></th>
    <th colspan="2" style="text-align:center">বিক্রেতার স্বাক্ষর</th>

 </tr>

</table>
</tr>
</table>





</body>
</html>



<script type="text/javascript">

    setTimeout(function (){
          // window.print();
          }, 1000); 
  
  
    </script>

