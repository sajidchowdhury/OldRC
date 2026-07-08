<?php 
include('function_query.php');
?>

 <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
 <input type="hidden" id="page_name" value="<?php print $_GET['print']?>">
 <input type="hidden" id="code" value="<?php print $_GET['code']?>">

 <?php 


if($_GET['print'] == 'Report Raw Local Purchase' || $_GET['print'] == 'Batch Wise Raw Local Purches' ){

  
$inventory_info = WORKFLOW::INVENTORY('raw_local_purches',$_GET['print'],$_GET['code'],'','');
$content =  $inventory_info['report'] ;

}else if($_GET['print'] == 'Report FG Local Purchase' ){

  $inventory_info = WORKFLOW::INVENTORY('fg_local_purches',$_GET['print'],$_GET['code'],'','');
  $content =  $inventory_info['report'] ;

  

}else if ($_GET['print'] == 'Batch Wise Send For Molding' || $_GET['print'] == 'Report Send For Molding' ){
  $inventory_info = WORKFLOW::PRODUCTION('molding',$_GET['print'],$_GET['code'],'','');
  $content =  $inventory_info['report'] ;

}else if ($_GET['print'] == 'Report Send For Print' || $_GET['print'] == 'Batch Wise Send For Print' ){
    $inventory_info = WORKFLOW::PRODUCTION('print',$_GET['print'],$_GET['code'],'','');
    $content =  $inventory_info['report'] ;
 

  }else if ($_GET['print'] == 'Report Send For Spray' || $_GET['print'] == 'Batch Wise Send For Spray' ){
    $inventory_info = WORKFLOW::PRODUCTION('spray',$_GET['print'],$_GET['code'],'','');
    $content =  $inventory_info['report'] ;
 
  }else if ($_GET['print'] == 'Report Recipe Wise Requisition' ){
    $inventory_info = WORKFLOW::PRODUCTION('receipe_wise_demand',$_GET['print'],$_GET['code'],'','');
    $content =  $inventory_info['report'] ;


    
  }else if ( $_GET['print'] == 'Challan Copy For Sales Person Approval' || $_GET['print'] == 'Godown Copy'  || $_GET['print'] == 'Sales Invoice' || $_GET['print'] == 'Delivery Challan' ){

    $inventory_info = WORKFLOW::SALES('sales',$_GET['print'],$_GET['code'],'','');
    $content =  $inventory_info['report'] ;

  }else if ($_GET['print'] == 'FG-STORE-DAMAGE-RECEIPT' ){

    $inventory_info = WORKFLOW::SALES('sales',$_GET['print'],$_GET['code'],'','');
    $content =  $inventory_info['report'] ;


  }else if ($_GET['print'] == 'FG-WAREHOUSE-TO-WAREHOUSE-TRANSFER-RECEIPT' ){

    $inventory_info = WORKFLOW::FGTRANSFER($_GET['print'],$_GET['code']);
    $content =  $inventory_info['report'] ;

    
  }else if ($_GET['print'] == 'Quatation' ){
    $inventory_info = WORKFLOW::Quatation('quatation',$_GET['print'],$_GET['code'],'','');
    $content =  $inventory_info['report'] ;
    
}else{
  $content = 'NO REPORZT' ;
}

?>

<div id="printableArea">
  <div class="container">
  <div class="row">

  <div class="panel-heading hidden-print">
    <div class="btn-group pull-right">
        <button onclick="printNow()" class="btn btn-danger" ><i class="fa fa-print"></i> Print</button>

    </div>                                    
                                    
  </div>
  <div class="row">
  <div class="col-md-12">
  <?php print $content;?>

</div>
  </div>
  </div>


</div>

<script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>

<script type="text/javascript" >
  function printNow(){
    var page_name = document.getElementById('page_name').value;
    var code = document.getElementById('code').value;

      $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      page_name: page_name,
      code: code,
      action: 'UPDATE_PRINT_STATUS'
    },
    cache: false,
    success: function(html){
    
      var printContents = document.getElementById('printableArea').innerHTML;
  var originalContents = document.body.innerHTML;
  document.body.innerHTML = printContents;
  window.print();
  document.body.innerHTML = originalContents;

    }

  });
    

    
  }

    </script>
