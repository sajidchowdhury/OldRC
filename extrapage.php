<?php 
include('auth.php');
include('layout/head.php');
include('function_query.php');
$conn_me = Database::getInstance();

$topbar = SETUP::TOP_BAR($_GET['page_identity'],'');




?>

    <body> 

<div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
            <div class="page-sidebar">
            <?php include('layout/common_side_bar.php'); ?>

            </div>
            <!-- END PAGE SIDEBAR -->
            
            <!-- PAGE CONTENT -->
            <div class="page-content">
                
           <?php print $topbar['top_bar_content'];?> 
             
                
                                                   
                
                <!-- START CONTENT FRAME -->
                <div class="page-content-wrap">
              
            
                <div id="load_content">

<?php include("$_GET[page_identity].php");?>



                
                </div>

                </div>
      
                </div>
                <!-- END CONTENT FRAME -->
                
                
                
            </div>            
            <!-- END PAGE CONTENT -->
        </div>


<?php include('javascripts.php'); ?>
<script type="module" src="check_data.js"></script>
<script type="module" src="rc_center_script.js"></script>

<script>

function invoiceOrQuat (Type){
        if(Type == 'Invoice'){
          $("#refresh_cart").load('cart_sales_entry.php');
        }else if(Type == 'Quotation'){
          $("#refresh_cart").load('cart_quatation_entry.php');
      
        }else if(Type == 'Preorder'){
          $("#refresh_cart").load('cart_preorder_invoice_entry.php');
        }else{
          $("#refresh_cart").load('cart_sales_entry.php');
        }
      }


     function navigate(value,event) {
     
     var items = document.getElementsByClassName("list-group-item");
     if (event.keyCode == 40) {

         // Down arrow key is pressed
         items[currentActiveIndex].classList.remove("active");
         if(currentActiveIndex == 0){
           currentActiveIndex = currentActiveIndex + 4;
         }else{
           currentActiveIndex = currentActiveIndex + 1;
         }


         items = document.getElementsByClassName("list-group-item");
         currentActiveIndex = Math.min(Math.max(currentActiveIndex, 0), items.length - 1);
         items[currentActiveIndex].classList.add("active");
     } else if (event.keyCode == 38) {
         // Up arrow key is pressed
         items[currentActiveIndex].classList.remove("active");
         currentActiveIndex = currentActiveIndex - 1;

         items = document.getElementsByClassName("list-group-item");
         currentActiveIndex = Math.min(Math.max(currentActiveIndex, 0), items.length - 1);
         items[currentActiveIndex].classList.add("active");
     } else {
         search_product_List(value);
         // reset currentActiveIndex after search results are rendered
         items = document.getElementsByClassName("list-group-item");
         currentActiveIndex = 0;

     }
    


   }


   function search_product_List(value){

if(value == '' ){
  document.getElementById('search-listing').innerHTML = '';
}else{
  $.ajax({
      url: "function_tem.php", 
      type: "POST",
      data: {
          value: value,
            action: 'search_product_wise'
      },
      cache: false,
      success: function(dataResult){
      
          document.getElementById('search-listing').innerHTML = dataResult;
      }
      });
}
}


        $('#stop').on('click', function() {
        var value = $('#product_name').val();
        document.getElementById("product_name").focus();
        search_product_List(value);
        });


        $('#product_name').on('keypress',function(e){
            var activeItem = document.getElementsByClassName("list-group-item active")[0];
            if (activeItem) {
              if (e.keyCode === 13) {
                var placeholder = activeItem.getAttribute("data-placeholder");
                putonsearchbar('get_product_id'+placeholder,'get_product_name'+placeholder);
                $('#itemID'+placeholder).find('li').trigger('click');
               }
            }
        
        })



        function putonsearchbar(id,name){

          var product_id = $('#'+id).val();
          var product_name = $('#'+name).val();
          document.getElementById('product_name').value = product_name;
          document.getElementById('product_id').value = product_id;
        
          itemstock(product_id,'FG','YES','NO','NO','YES','NO','NO','YES');
          document.getElementById('search-listing').innerHTML = '';
          document.getElementById("quantity").focus();

        }
        

        function itemstock(ID,TYPE,stock_in_pcs,stock_in_carton,warehouse_list,product_retaile_price,product_wholesale_price,stock_list,recomaned_price){


$.ajax({
  url: "function_tem.php",
  type: "POST",
  data: {
    ID: ID,
    TYPE: TYPE,
    action: 'find_stock'
  },
  dataType: 'json',
  cache: false,
  success: function(dataResult){
    

if(stock_in_pcs == 'YES'){  $('#stock_in_pcs').val(dataResult.stock_pcs); }
if(stock_in_carton == 'YES'){   $('#stock_in_carton').val(dataResult.stock_carton);}
if(warehouse_list == 'YES'){    document.getElementById('warehouse_list').innerHTML =   dataResult.warehouse_list; }
if(product_retaile_price == 'YES'){  $('#product_retaile_price').val( dataResult.retail_price); }
if(product_wholesale_price == 'YES'){  $('#stock_in_pcs').val(dataResult.wholesale_price); }
if(stock_list == 'YES'){  document.getElementById('stock_list').innerHTML =   dataResult.stock_list; }
if(recomaned_price == 'YES'){ $('#recommended_price').val( dataResult.retail_price); }

$('select').selectpicker();


  }
});
}

function sale_calculator()
{

  calculation_of_three_number('sub_total','total_transport_cost','total_vat_cost','invoice_total');
  calculation_of_two_number('MULTIFICATION','quantity','recommended_price','each_item_total');
  calculation_of_two_number('DIFFREANCE','invoice_total','discount','invoice_payable');
  TransportCals()
}          

</script>

    </body>
</html>






