<?php 
include('function_query.php');
$content = '';


if ($_GET['section'] == 'Supplier-Setup' || $_GET['section'] == 'Warehouse-Setup' 
|| $_GET['section'] == 'Raw-Material-Setup' || $_GET['section'] == 'Raw-Material-Setup' || $_GET['section'] == 'Raw-Material-Setup'){

    $templet_content = TEMPLET::TEMPLET_CONTENT($_GET['section'],$_GET['related_id']);
    $content =  $templet_content['content'] ;

}else if($_GET['section'] == 'Mold Receive From Supplier' || $_GET['section'] == 'Mold Receive From Factory' || $_GET['section'] == 'Pending Warehouse Dispatch For Molding'){   

    $inventory_info = WORKFLOW::PRODUCTION('molding',$_GET['section'],$_GET['related_id'],'','');
    $content =  $inventory_info['report'] ;

}else if($_GET['section'] == 'Spray Receive From Supplier' || $_GET['section'] == 'Pending Warehouse Dispatch For Spray' || $_GET['section'] == 'Spray Receive From Factory'){   

    $inventory_info = WORKFLOW::PRODUCTION('spray',$_GET['section'],$_GET['related_id'],'','');
    $content =  $inventory_info['report'] ;

}else if($_GET['section'] == 'Print Receive From Supplier' || $_GET['section'] == 'Print Receive From Factory' || $_GET['section'] == 'Pending Warehouse Dispatch For Print'){   

    $inventory_info = WORKFLOW::PRODUCTION('print',$_GET['section'],$_GET['related_id'],'','');
    $content =  $inventory_info['report'] ;

   
}else if($_GET['section'] == 'Batch Fitting'){  

    $molding_info = WORKFLOW::PRODUCTION('receipe_wise_demand',$_GET['section'],$_GET['related_id'],'','');
    $content =  $molding_info['report'] ;



}else if($_GET['section'] == 'Batch Receive:: From Supplier' || $_GET['section'] == 'Batch Receive:: From Factory' || $_GET['section'] == 'Batch Receive ::: From Supplier' || $_GET['section'] == 'Batch Receive ::: From Factory' || $_GET['section'] == 'Report Recipe Wise Requisition' || $_GET['section'] == 'Pending Receipe wise Demand'){   

    $molding_info = WORKFLOW::PRODUCTION('receipe_wise_demand',$_GET['section'],$_GET['related_id'],'','');
    $content =  $molding_info['report'] ;


}else if($_GET['section'] == 'Return FG Local Purches' || $_GET['section'] == 'Pending Receive FG Local Purchase'){   

    $inventory_info = WORKFLOW::INVENTORY('fg_local_purches',$_GET['section'],$_GET['related_id'],'','');
    $content =  $inventory_info['report'] ;


}else if( $_GET['section'] == 'Return Raw Local Purches' || $_GET['section'] == 'Pending Receive Raw Local Purches' || $_GET['section'] == 'Pending Receive Sales Return' || $_GET['section'] == 'Pending Damage Receive'){   

    $inventory_info = WORKFLOW::INVENTORY('raw_local_purches',$_GET['section'],$_GET['related_id'],'','');
    $content =  $inventory_info['report'] ;

}else if($_GET['section'] == 'Invoice Wise Batch Status' || $_GET['section'] == 'Pending Requisition'){   


    $molding_info = REPORT::BATCH_STATUS('Batch Status',$_GET['section'],$_GET['related_id']);
    $content =  $molding_info['print_report'] ;    

}else if($_GET['section'] == 'Raw Warehouse List'){   

    $a = FIND::WAREHOUSE_LIST('RAW',$_GET['related_id'],'');
    $content = $a['warehouse_content'];


}else if($_GET['section'] == 'Product Transfer'){   

    $content = '';
    include('Finished-Goods-Warehouse-To-Warehouse-From_Godown-Copy.php');

}else if($_GET['section'] == 'Central Warehouse List'){   

    $a = FIND::CENTRAL_WAREHOUSE_LIST('FG',$_GET['related_id'],'');
    $content = $a['warehouse_content'];

}else if($_GET['section'] == 'Transaction History'){   

        $a = FIND::TransactionHistory($_GET['related_id']);
       $content = $a;



}else if($_GET['section'] == 'Warehouse List'){   

    $a = FIND::WAREHOUSE_LIST('FG',$_GET['related_id'],'');
    $content = $a['warehouse_content'];
    
}else if($_GET['section'] == 'Pending Pre Order Invoice'){   

    $pre_invoice = WORKFLOW::PREINVOICE('preinvoice',$_GET['section'],$_GET['related_id'],'','');
    $content = $pre_invoice['report'];

}else if($_GET['section'] == 'Pending Quotation'){   

    $pre_invoice = WORKFLOW::QUOTATION('quotation',$_GET['section'],$_GET['related_id'],'','');
    $content = $pre_invoice['report'];

}else if($_GET['section'] == 'Pending Demand'){   

    $content = DEMAND::PENDING_DEMAND($_GET['related_id']);

}else if( $_GET['section'] == 'Challan Copy' || $_GET['section'] == 'Generate Godown Copy' || $_GET['section'] == 'Pending Sales Invoice' || $_GET['section'] == 'Sales Return' || $_GET['section'] == 'Damage Return' ){   

    $saling_info = WORKFLOW::SALES('sales',$_GET['section'],$_GET['related_id'],'','');
    $content =  $saling_info['report'] ;

}else if($_GET['section'] == 'Pipeline Details'){   

    $a = FIND::PIPELINE_DETAILS($_GET['product_id'],$_GET['brunch_id']);
    $content = $a;

}else if($_GET['section'] == 'Stock Adjustment'){   

    $inventory_info = WORKFLOW::STOCK_ADJUSTMENT_REPORT($_GET['related_id']);
    $content =  $inventory_info['report'] ;

    
}else if($_GET['section'] == 'Identify Customer'){   

    $content =  '' ;
}else{
    $content = 'processing...';
}

?>



<div id="printableArea"><?php print $content;?></div>


<script type="text/javascript" src="my_sz_script2.js">
