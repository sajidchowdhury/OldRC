$(document).ready(function () {
    $(".panel-collapse").click(function () {
      // Toggle the panel's visibility here
      // Example using Bootstrap's collapse class
      $(this).closest(".panel").find(".panel-body").collapse("toggle");
    });
  });

var title_tag = $('#title_tag_text').val();
$('.title_name').html(title_tag); 	
const regex = new RegExp('foo*');
$('.selectpicker').selectpicker('refresh');

function checkValue(funcName, value,mess) {

    switch (funcName) {
    case 'isEmpty':
    return isEmpty(value,mess);
    case 'isLessThanZero':
    return isLessThanZero(value,mess);
    case 'isZero':
    return isZero(value,mess);
    case 'isGreater':
    return isGreater(value,mess);
    default:
    return false;
    }
    }
    
     function isEmpty(value,mess) {
    if (value === undefined || value === null || value === '') {
    alert(mess + ' is empty');
    return true;
    }
    return false;
    }
    
     function isLessThanZero(value,mess) {
    if (value <= 0) {
    alert(mess + ' is less than zero');
    return true;
    }
    return false;
    }
    
     function isZero(value,mess) {
    if (value == 0) {
    alert(mess + ' is zero');
    return true;
    }
    return false;
    }

     function isGreater(value,mess) {

        var value = value.split('@');
        var mess = mess.split('@');

        var num1 = parseInt(value[0]);
        var num2 = parseInt(value[1]);

    if (num1 > num2) {
    alert(mess[0] + ' is greater then ' + mess[1]);
    return true;
    }
    return false;
    }


     function ConvertNum(val,mess) {
    var given_number = parseFloat(val).toFixed(2);
    if (isNaN(given_number)) {
    return 0.00;
    }
    return given_number;
    }




function DeleteFGMovement(ID,section,in_amount,out_amount){

  var x = window.confirm("Are you sure to Delete?");
  if(x){
  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      ID: ID,
      section: section,
      in_amount: in_amount,
      out_amount: out_amount,
      action: 'Delete_FG_Movement'
    },
    cache: false,
    success: function(html){
      alert(html);      
    }
  });
  }
}


function printDiv(divName) {
  var printContents = document.getElementById(divName).innerHTML;
  var originalContents = document.body.innerHTML;

  document.body.innerHTML = printContents;

  window.print();

  document.body.innerHTML = originalContents;
  location.reload();
}

function block_salles(PRODUCTID){


  var check_status =document.getElementById('user_sales'+PRODUCTID).checked;

  if(check_status == false){
   mess = 'Block';
  }else{
   mess = 'checked';
  }



 
  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      PRODUCTID: PRODUCTID,
      check_status: check_status,
      action: 'BLOCK_SALE',
      mess: mess
    },
    cache: false,
    success: function(html){
      alert(html);    
      location.reload();  
    }

  });
 

}

function PushToSalesReport(get_related_id){
var related_id = $('#'+get_related_id).val();
      $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      related_id: related_id,
      action: 'pursh_to_sale_report'
    },
    cache: false,
    success: function(html){

        location.reload();
    }

  });
}


function findUserToBlock(value){

  if(value == '' ){
    value = 'New';
  }else{
    value = value;
  }
  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      value: value,
      action: 'GET_RELATED_ADMIN_NAME'
    },
    cache: false,
    success: function(html){

      $('#change_load_content').html(html); 	

    }

  });

  
}

function block_user(ADMINID){

  var check_status =document.getElementById('user_block'+ADMINID).checked;

   if(check_status == false){
    mess = 'Block';
   }else{
    mess = 'Unblock';
   }



  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      ADMINID: ADMINID,
      check_status: check_status,
      action: 'BLOCK_USER',
      mess: mess
    },
    cache: false,
    success: function(html){
      alert(html);      
      location.reload();
    }

  });

}

function change_prefix(ID){

  var prefix = $('#prefix'+ID).val();

  var x = window.confirm("Are you sure to Update Prefix?");
  if(x){
  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      ID: ID,
      prefix: prefix,
      action: 'UPDATE_PREFIX'
    },
    cache: false,
    success: function(html){
      alert(html);   
      location.reload();   
    }

  });
  }
}



function change_priceANDvat(ID){

  var price = $('#product_price'+ID).val();
  var vat_percentage = $('#vat_percentage'+ID).val();
  var discount = $('#discount'+ID).val();

  var x = window.confirm("Are you sure to Update?");
  if(x){
  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      ID: ID,
      price: price,
      vat_percentage:vat_percentage,
      discount: discount,
      action: 'UPDATE_PRICE'
    },
    cache: false,
    success: function(html){
      alert(html);      
    }

  });
  }
}





$('#create_user').on('click', function() {


  $("#create_user").attr("disabled", "disabled");
  var related_id = $('#related_id').val();

  var user_name = $('#username').val();


  var user_password = $('#user_password').val();
var employee_id = $('#employee_id').val();
var brunch_id =$('#brunch_id').val();
var user_type =$('#user_type').val();



  if(user_name!="" && user_password!="" ){
    $.ajax({
      url: "form_action.php",
      type: "POST",
      data: {
        user_name: user_name,
        user_password: user_password,
        action: 'create_user',
        related_id: related_id,
        employee_id: employee_id,
        brunch_id: brunch_id,
        user_type: user_type
      },
      cache: false,
      success: function(dataResult){
          $("#create_user").removeAttr("disabled");	
          if(dataResult == '1'){
              alert('User Create Success');
              window.location.replace("Setup/User-Management/" + employee_id );

          }else if(dataResult == '2'){
            alert('User Update Success');
            window.location.replace("Setup/User-Management/" + employee_id );
          }else{
alert(dataResult);
          }
        
      }
    });
    }
    else{
      alert('Please fill all the field !');
      $("#create_user").removeAttr("disabled");

    }
  });


  
function givesingleaccess(MENU_ID){


  var lfckv = document.getElementById("give_single_access"+MENU_ID).checked;

  if(lfckv == true){
    view_check = 'Checked';
   }else{
    view_check = '';
   }

   var employee_id = $('#employee_id').val();
   var menu_id = $('#menu_id'+MENU_ID).val();




   $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      menu_id: menu_id,
      employee_id: employee_id,
      view_check: view_check,
      action: 'give_single_menu_access_to_user'
    },
    cache: false,
    success: function(dataResult){
      alert(dataResult); 
      // window.location.replace("Setup/User-Management/" + employee_id );

    }

  });


}


function todayBalance(transection_by,transection_by_id){


  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      transection_by: transection_by,
      transection_by_id: transection_by_id,
      action: 'find_today_balance'
    },
    cache: false,
    success: function(dataResult){
      document.getElementById('todaybalance').innerHTML = dataResult;
    }

  });



}



function give_main_access(section){


  document.getElementById('title'+section).innerHTML = 'working on it..... ';

  var employee_id = $('#employee_id').val();

  var check_status =document.getElementById('give_main_access'+section ).checked;



  if(check_status == true){
    view_check = 'Checked';
   }else{
    view_check = '';
   }

   $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        employee_id: employee_id,
        section: section,
        view_check: view_check,
        action: 'give_main_menu_access_to_user'
      },
      cache: false,
      success: function(dataResult){
        alert(dataResult); 
        window.location.replace("Setup/User-Management/" + employee_id );

      }
  
    });

}



function calculate_actual_qty(){

  var ess_fg_quantity = $('#ess_fg_quantity').val();
  var ess_raw_quantity = $('#ess_raw_quantity').val();

  document.getElementById('quantity').value = ess_raw_quantity/ess_fg_quantity;


  
}


function get_employee_details(ID){

  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      ID: ID,
      action: 'fatch_employee_details'
    },
    cache: false,
    success: function(dataResult){
      $('#load_emailoyee_details').html(dataResult); 	
    }

  });
}



$(function() {
          $('marquee').mouseover(function() {
              $(this).attr('scrollamount',0);
          }).mouseout(function() {
               $(this).attr('scrollamount',5);
          });
      });

      $(document).ready(function(){
        $("#search_table_row").on("keyup", function() {
          var value = $(this).val().toLowerCase();
          $("#report_table tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
          });
        });
      });

      $('#modal_large').on('show.bs.modal', function(event) {


        var button = $(event.relatedTarget) // Button that triggered the modal
        var section = button.data('whatever1') // Extract info from data-* attributes
        var page_name = button.data('whatever2') // Extract info from data-* attributes
        var related_id = button.data('whatever3') // Extract info from data-* attributes
        $('#modal_large .modal-title').html(section)
      
        var modal = $(this);
        $.ajax({
            type: "GET",
            url: page_name+".php",
            
            data: {
              related_id: related_id,
              section: section
            },
            cache: false,
            success: function(data) {
                console.log(data);
                modal.find('.dash').html(data);
                $('select').selectpicker();
            },
            error: function(err) {
                console.log(err);
            }
      
        });
      });



      function LedgerWiseData(type,ledger_id){

      
        $.ajax({
          url: "function_tem.php", 
          type: "POST",
          data: {
              ledger_id: ledger_id,
              type: type,
                action: 'ledger_wise_accounts'
          },
          cache: false,
          success: function(dataResult){
          
              document.getElementById('load_subhead').innerHTML = dataResult
              $('select').selectpicker();
      
          }
          });
      
      
      
      
      }


      $('#modal_large_no_need_refresh').on('show.bs.modal', function(event) {

        var button = $(event.relatedTarget) // Button that triggered the modal
        var section = button.data('whatever1') // Extract info from data-* attributes
        var page_name = button.data('whatever2') // Extract info from data-* attributes
        var related_id = button.data('whatever3') // Extract info from data-* attributes
        $('#modal_large_no_need_refresh .modal-title').html(section)
       
        var modal = $(this);        
          $.ajax({
            type: "GET",
            url: page_name+".php",
            
            data: {
              related_id: related_id,
              section: section
            },
            cache: false,
            success: function(data) {
                console.log(data);
                modal.find('.dash2').html(data);
                $('select').selectpicker();
            },
            error: function(err) {
                console.log(err);
            }
      
        });


      });



      function find_unit(VALUE,DIV){

        if(VALUE != '' || VALUE != 'no data' ){

          var purches_type = $('#purches_type').val();

          $.ajax({
            url: "function_tem.php",
            type: "POST",
            data: {
              VALUE: VALUE,
              purches_type: purches_type,
              action: 'find_unit'
            },
            cache: false,
            success: function(dataResult){
  
              document.getElementById(DIV).innerHTML = dataResult;
      
            }
  
          });
        }else{
          document.getElementById(DIV).innerHTML = '...';
        }


      }

      

      function find_supplier_spray_pending(ID){


        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {
            ID: ID,
            action: 'SUPPLIER_WISE_PENDING_SPRAY'
          },
          cache: false,
          success: function(dataResult){
            document.getElementById('pending_spray').innerHTML = dataResult;

    
          }

        });

      }

      function find_supplier_print_pending(ID){


        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {
            ID: ID,
            action: 'SUPPLIER_WISE_PENDING_PRINT'
          },
          cache: false,
          success: function(dataResult){
            document.getElementById('pending_print').innerHTML = dataResult;

    
          }

        });

      }


      function find_supplier_molding_pending(ID){


        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {
            ID: ID,
            action: 'SUPPLIER_WISE_PENDING_MOLDING'
          },
          cache: false,
          success: function(dataResult){
            document.getElementById('pending_molding').innerHTML = dataResult;

    
          }

        });

      }



      function find_production_demand(ID,SECTION){


        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {
            ID: ID,
            action: 'SUPPORTING_MATERIAL_DEMAND_FROM_PRODUCTION'
          },
          cache: false,
          success: function(dataResult){
            find_raw_material_unit(ID,'unit_name');
            document.getElementById('production_demnad').innerHTML = dataResult;

    
          }

        });

      }




      function find_raw_material_unit(VALUE,DIV){

        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {
            VALUE: VALUE,
            action: 'find_raw_material_unit'
          },
          cache: false,
          success: function(dataResult){

            document.getElementById(DIV).innerHTML = dataResult;
    
          }

        });

      }


      
function delete_mold_recipe_wise_demand_and_item(ID){


  var x = window.confirm("Are you sure to delete this data?");
  if(x){
    $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        ID: ID,
        action: 'delete_mold_recipe_wise_demand_and_item'
      },
      cache: false,
      success: function(dataResult){
   
        $("#refresh_cart2").load('cart_request_raw_material_mold.php');

    document.getElementById('mess_box').innerHTML = '<strong style="color:red"> ' + dataResult + '</strong>';

      setTimeout(function(){
    document.getElementById('mess_box').innerHTML = '';
    }, 2000);


    
      }

    });
}

}


function delete_print_recipe_wise_demand_and_item(ID){


  var x = window.confirm("Are you sure to delete this data?");
  if(x){
    $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        ID: ID,
        action: 'delete_print_recipe_wise_demand_and_item'
      },
      cache: false,
      success: function(dataResult){
   
        $("#refresh_cart2").load('cart_request_raw_material_print.php');

    document.getElementById('mess_box').innerHTML = '<strong style="color:red"> ' + dataResult + '</strong>';

      setTimeout(function(){
    document.getElementById('mess_box').innerHTML = '';
    }, 2000);


    
      }

    });
}

}

function delete_spray_recipe_wise_demand_and_item(ID){


  var x = window.confirm("Are you sure to delete this data?");
  if(x){
    $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        ID: ID,
        action: 'delete_spray_recipe_wise_demand_and_item'
      },
      cache: false,
      success: function(dataResult){
   
        $("#refresh_cart2").load('cart_request_raw_material_spray.php');

    document.getElementById('mess_box').innerHTML = '<strong style="color:red"> ' + dataResult + '</strong>';

      setTimeout(function(){
    document.getElementById('mess_box').innerHTML = '';
    }, 2000);


    
      }

    });
}

}
function delete_recipe_wise_item(ID){


  var x = window.confirm("Are you sure to delete this data?");
  if(x){
    $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        ID: ID,
        action: 'delete_recipe_wise_demand_and_item'
      },
      cache: false,
      success: function(dataResult){
   
        $("#refresh_cart2").load('cart_request_raw_material_batch.php');

    document.getElementById('mess_box').innerHTML = '<strong style="color:red"> ' + dataResult + '</strong>';

      setTimeout(function(){
    document.getElementById('mess_box').innerHTML = '';
    }, 2000);


    
      }

    });
}

}


function delete_receipe_cart(REFRESH,TABLENAME,ID,PRODUCTID){

  var x = window.confirm("Are you sure to delete this data?");
  if(x){
        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {
            TABLENAME: TABLENAME,
            ID: ID,
            action: 'delete_receipe_table_row'
          },
          cache: false,
          success: function(dataResult){

              $("#refresh_cart").load(REFRESH+'.php?related_id=New&product_id='+PRODUCTID);

            document.getElementById('mess_box').innerHTML = '<strong style="color:red"> ' + dataResult + '</strong>';

               setTimeout(function(){
          document.getElementById('mess_box').innerHTML = '';
        }, 2000);


        
          }

        });
      }

}


function delete_sales_row(ID,SALES_ID){

  var x = window.confirm("Are you sure to delete this data?");
  if(x){

    var related_id = $('#related_id').val();
    var draft_code = $('#draft_code').val();

    $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        ID: ID,
        SALES_ID: SALES_ID,
        related_id:related_id,
        action: 'delete_sales_table_row'
      },
      cache: false,
      success: function(dataResult){
     
     
        $("#refresh_cart").load('cart_sales_entry.php?related_id=' + related_id);
      
        document.getElementById('mess_box').innerHTML = '<strong style="color:red"> ' + dataResult + '</strong>';
        setTimeout(function(){
        document.getElementById('mess_box').innerHTML = '';
        var cart_sub_total = $('#cart_sub_total').val();
        $('#sub_total').val(cart_sub_total);
        sale_calculator();
        getDraftCode(draft_code);

        }, 1000);

       
    
      }

    });

    }

}


function delete_total_invoice(CODE){

  var x = window.confirm("Are you sure to delete this Invoice?");
  if(x){
        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {

            CODE: CODE,
            action: 'delete_total_invoice'
          },
          cache: false,
          success: function(dataResult){
            alert(dataResult);
                 location.reload();
          }

        });
      }


}


function delete_return_invoice(id){

  var x = window.confirm("Are you sure to delete this Invoice?");
  if(x){
        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {

            id: id,
            action: 'delete_return_invoice'
          },
          cache: false,
          success: function(dataResult){
            alert(dataResult);
                 location.reload();
          }

        });
      }


}



function delete_preorder_invoice(id){

  var x = window.confirm("Are you sure to delete this Invoice?");
  if(x){
        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {

            id: id,
            action: 'delete_preorder_invoice'
          },
          cache: false,
          success: function(dataResult){
            alert(dataResult);
                 location.reload();
          }

        });
      }


}



function delete_total_transection(id_placement){

  var id = $('#'+id_placement).val();

  var x = window.confirm("Are you sure to delete this Transaction?");
  if(x){
        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {

            id: id,
            action: 'delete_total_transection'
          },
          cache: false,
          success: function(dataResult){
            alert(dataResult);
                 location.reload();
          }

        });
      }


}

function updateDates(dateFrom, dateTo) {
  var dateFromInput = document.getElementById('date_from');
  var dateToInput = document.getElementById('date_to');
  
  // Format the dates as dd-mm-yyyy
  var formattedDateFrom = formatDate(dateFrom);
  var formattedDateTo = formatDate(dateTo);
  
  // Update the input values
  dateFromInput.value = formattedDateFrom;
  dateToInput.value = formattedDateTo;
}

function addDay(REPORT) {
  var dateFromInput = document.getElementById('date_from');
  var dateToInput = document.getElementById('date_to');
  
  // Get the current date values
  var dateFromValue = dateFromInput.value;
  var dateToValue = dateToInput.value;
  
  // Parse the dates using the desired format (dd-mm-yyyy)
  var dateFrom = parseDate(dateFromValue);
  var dateTo = parseDate(dateToValue);
  
  // Add 1 day to the dates
  dateFrom.setDate(dateFrom.getDate() + 1);
  dateTo.setDate(dateTo.getDate() + 1);
  
  // Update the input values
  updateDates(dateFrom, dateTo);

  if(REPORT == 'Day Book Report' ){
    generateReport('Day Book Report','branch_id')
  }
}

function subtractDay(REPORT) {
  var dateFromInput = document.getElementById('date_from');
  var dateToInput = document.getElementById('date_to');
  
  // Get the current date values
  var dateFromValue = dateFromInput.value;
  var dateToValue = dateToInput.value;
  
  // Parse the dates using the desired format (dd-mm-yyyy)
  var dateFrom = parseDate(dateFromValue);
  var dateTo = parseDate(dateToValue);
  
  // Subtract 1 day from the dates


  
  dateFrom.setDate(dateFrom.getDate() - 1);
  dateTo.setDate(dateTo.getDate() - 1);
  
  // Update the input values
  updateDates(dateFrom, dateTo);

  if(REPORT == 'Day Book Report' ){
    generateReport('Day Book Report','branch_id')
  }

  
}

function parseDate(dateString) {
  var parts = dateString.split('-');
  return new Date(parts[2], parts[1] - 1, parts[0]);
}

function formatDate(date) {
  var day = date.getDate();
  var month = date.getMonth() + 1;
  var year = date.getFullYear();
  return (day < 10 ? '0' + day : day) + '-' + (month < 10 ? '0' + month : month) + '-' + year;
}


function delete_money_transfer(id_placement){

  var id = $('#'+id_placement).val();


  var x = window.confirm("Are you sure to delete this Money Transfer?");
  if(x){
        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {

            id: id,
            action: 'delete_money_transfer'
          },
          cache: false,
          success: function(dataResult){
            alert(dataResult);
               
            transectionSummery('32','MONEY-TRANSFER');

          }

        });
      }


}



      function delete_cart_row(REFRESH,TABLENAME,ID,PAGE_REFRESH){

         var x = window.confirm("Are you sure to delete this data?");
  if(x){
        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {
            TABLENAME: TABLENAME,
            ID: ID,
            action: 'delete_table_row'
          },
          cache: false,
          success: function(dataResult){
         
            if(PAGE_REFRESH == 'Yes'){
              window.location.replace(REFRESH);
            }else if(PAGE_REFRESH == 'No'){
              $("#refresh_cart").load(REFRESH+'.php?related_id=New');            
            }else{
              PAGE_REFRESH ;  
            }
         

            document.getElementById('mess_box').innerHTML = '<strong style="color:red"> ' + dataResult + '</strong>';

               setTimeout(function(){
          document.getElementById('mess_box').innerHTML = '';
        }, 2000);


        
          }

        });
      }

      }



function TransferType(type){

  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      action: 'transfer_type',
      type: type
    },
    cache: false,
    dataType: 'json',
    success: function(dataResult){
      $('#level_data').html(dataResult.content); 	
      $('select').selectpicker();
    }
  });

}


function transfer_transaction(){

  var report_type = $('#report_type').val();
  var tr_type = $('#tr_type').val();
  var tr_from = $('#tr_from').val();
  var tr_to = $('#tr_to').val();
  var amount = $('#amount').val();
  var note = $('#note').val();
  var transection_by_from = $('#transection_by_from').val();
  var transection_by_to = $('#transection_by_to').val();
  var to_brunch_id = $('#brunch_id').val();
  var transection_date = $('#transection_date').val();

  if (report_type == '' ) { alert("Select Section"); $('#report_type').focus(); return false;}
  if (to_brunch_id == '' ) { alert("Select Brunch"); $('#tr_to').focus(); return false;}
  if (tr_type == '' ) { alert("Select Transfer Type"); $('#tr_to').focus(); return false;}

  if (transection_by_from == '' ) { alert("Select Transaction From"); $('#transection_by_from').focus(); return false;}
  if (transection_by_to == '' ) { alert("Select Transaction To"); $('#transection_by_to').focus(); return false;}

  if (tr_from == '' ) { alert("Select Transaction From"); $('#tr_from').focus(); return false;}
  if (tr_to == '' ) { alert("Select Transaction To"); $('#tr_to').focus(); return false;}

    if (amount < 0 || amount == '' ) { alert("amount is not valid"); $('#tr_to').focus(); return false;}


  var x = window.confirm("Are you sure to make the transfer ?");
  if(x){
  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      report_type: report_type,
      tr_type: tr_type,
      tr_from: tr_from,
      tr_to: tr_to,
      to_brunch_id: to_brunch_id,
      amount: amount,
      transection_by_from: transection_by_from,
      transection_by_to: transection_by_to,
      note: note,
      transection_date: transection_date,
      action: 'transfer_transaction'
    },
    cache: false,
    success: function(dataResult){
      alert(dataResult);
      location.reload();
    }

  });
}



}




function transectionSummeryPending(ledger_id,report_type){

  document.getElementById("search_data").disabled = true;            

  alert(ledger_id);alert(report_type);
  var date_from = $('#date_from').val();
  var date_to = $('#date_to').val();

  var branch_id = $('#branch_id').val();


  var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
  $('#laod_report').html(spinner); 	


  $.ajax({
    url: "generate_report.php",
    type: "POST",
    data: {
  
      action: 'Pending Transaction Summary',
      date_from: date_from,
      date_to: date_to,
      branch_id: branch_id,
      ledger_id:ledger_id,
      report_type: report_type
    },
    cache: false,
    success: function(html){

      console.log(html);
     document.getElementById("laod_report").innerHTML = html;
     document.getElementById("search_data").disabled = false;            
     $('#MSalary').DataTable();

    }

  });


}

function find_due_adjustment(){

  document.getElementById("search_data2").disabled = true;            

  var date_from = $('#date_from2').val();
  var date_to = $('#date_to2').val();

  var branch_id = $('#branch_id2').val();

  var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
  $('#laod_report2').html(spinner); 	


  $.ajax({
    url: "generate_report.php",
    type: "POST",
    data: {
  
      action: 'find_due_adjustment',
      date_from: date_from,
      date_to: date_to,
      branch_id: branch_id
        },
    cache: false,
    success: function(html){

     document.getElementById("laod_report2").innerHTML = html;
     document.getElementById("search_data2").disabled = false;            
           $('#example').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                lengthMenu: [10, 20, 50, 100], // Dropdown options for page length
                pageLength: 2000 // Default number of rows per page
            });


    }

  });

}

function find_discount_adjustment() {

    document.getElementById("search_data3").disabled = true;

    var date_from   = $('#date_from3').val();
    var date_to     = $('#date_to3').val();
    var branch_id   = $('#branch_id3').val();
    var branch_name = $("#branch_id3 option:selected").text(); // <-- GET TEXT

    var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif' style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
    $('#laod_report3').html(spinner);

    $.ajax({
        url: "generate_report.php",
        type: "POST",
        data: {
            action: 'find_discount_adjustment',
            date_from: date_from,
            date_to: date_to,
            branch_id: branch_id
        },
        cache: false,
        success: function (html) {

            document.getElementById("laod_report3").innerHTML = html;
            document.getElementById("search_data3").disabled = false;

            $('#example').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'print',
                        title: '',
                        messageTop: function () {
                            return `
                                <h3 style="text-align:center;margin:0;">
                                    Discount Adjustment Report
                                </h3>
                                <p style="text-align:center;margin:0;">
                                    Date From: ${date_from}  |  Date To: ${date_to}
                                </p>
                                <p style="text-align:center;margin:0;">
                                    Branch: ${branch_name}
                                </p>
                                <br>
                            `;
                        }
                    },
                    {
                        extend: 'pdf',
                        title: 'Discount Adjustment Report',
                        messageTop: `Date From: ${date_from} | Date To: ${date_to} | Branch: ${branch_name}`
                    },
                    {
                        extend: 'excel',
                        title: `Discount Adjustment Report (${branch_name})`
                    },
                    {
                        extend: 'csv',
                        title: `Discount Adjustment Report (${branch_name})`
                    },
                    'copy'
                ],
                lengthMenu: [10, 20, 50, 100],
                pageLength: 2000
            });

        }
    });
}



      function transectionSummery(ledger_id,report_type){

        document.getElementById("search_data").disabled = true;            

        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();

        var branch_id = $('#branch_id').val();

        var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
        $('#laod_report').html(spinner); 	
      
      
        $.ajax({
          url: "generate_report.php",
          type: "POST",
          data: {
        
            action: 'Transaction Summary',
            date_from: date_from,
            date_to: date_to,
            branch_id: branch_id,
            ledger_id:ledger_id,
            report_type: report_type
          },
          cache: false,
          success: function(html){
      
           document.getElementById("laod_report").innerHTML = html;
           document.getElementById("search_data").disabled = false;            
           $('#MSalary').DataTable();
      
          }
      
        });
      

      }

      

      
      function product_damage(report_type){

        document.getElementById("search_data").disabled = true;            

        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();



        var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
        $('#laod_report').html(spinner); 	
      
      
        $.ajax({
          url: "generate_report.php",
          type: "POST",
          data: {
        
            action: 'Product Damage Report',
            date_from: date_from,
            date_to: date_to,
            report_type: report_type
          },
          cache: false,
          success: function(html){
      
           document.getElementById("laod_report").innerHTML = html;
           document.getElementById("search_data").disabled = false;            
           $('#MSalary').DataTable();
      
          }
      
        });
      

      }

      
      function product_transfer(report_type){


        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();

        var from_warehouse_id = $('#from_warehouse_id').val();
        var to_warehouse_id = $('#to_warehouse_id').val();

if(from_warehouse_id === null){
  alert('Please select FROM warehouse');
  return false; 

}
if(to_warehouse_id === null){
  alert('Please select TO warehouse');
  return false; 

}
document.getElementById("search_data").disabled = true;            

        var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
        $('#laod_report').html(spinner); 	
      
      
        $.ajax({
          url: "generate_report.php",
          type: "POST",
          data: {
        
            action: 'Product Transfer Report',
            date_from: date_from,
            date_to: date_to,
            from_warehouse_id: from_warehouse_id,
            to_warehouse_id: to_warehouse_id,
            report_type: report_type
          },
          cache: false,
          success: function(html){
      
           document.getElementById("laod_report").innerHTML = html;
           document.getElementById("search_data").disabled = false;            
           $('#MSalary').DataTable();
      
          }
      
        });
      

      }

      
      function StockWithPrice(){

        document.getElementById("search_data").disabled = true;            

        var date_from = $('#date_from').val();
        var branch_id = $('#branch_id').val();


        var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
        $('#laod_report').html(spinner); 	
      
      
        $.ajax({
          url: "generate_report.php",
          type: "POST",
          data: {
        
            action: 'Stock-With-Price',
            date_from: date_from,
            branch_id: branch_id
          
          },
          cache: false,
          success: function(html){
      
           document.getElementById("laod_report").innerHTML = html;
           document.getElementById("search_data").disabled = false;            
           $('#MSalary').DataTable();
      
          }
      
        });
      

      }




      
      function filter_data(){
        var spinner = "<table><tr colspan='4'><td><img src='report.gif' style='height:350px' alt='loading...' /></td></tr></table>";
        $('#load_report').html(spinner); 	
          $.ajax({
            url: "generate_report.php",
            type: "POST",
            data: {
              action: 'Overall FG Inventory'
            },
            cache: false,
            success: function(dataResult){
              var progress = 0;
              var interval = setInterval(function(){
                if(progress === 100){
                  clearInterval(interval);
                  $('#load_report').html(dataResult); 	
                  $('#ATable').DataTable();
                  $("#submit-search").removeAttr("disabled");
                  $('.progress-bar').css('width', '0' + '%').html('0' + '%');

                } else {
                  progress += 20;
                  $('.progress-bar').css('width', progress + '%').html(progress + '%');
                }
              }, 500);
            }
          });
      }

function put_value_in_search_bar(VALUE){
  $("#search_table_row").val(VALUE);
  document.getElementById("submit-search").click();

}


function supplier_or_factory(VALUE){

    $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        action: 'supplier_or_factory',
        VALUE: VALUE
      
      
      },
      cache: false,
      success: function(dataResult){
        $('#level_name').html('Select '+VALUE); 	
        $('#level_data').html(dataResult); 	
        $('select').selectpicker();
      }
    });
  
 

}



function calculate_supporting_batch(){


  var supporting_id = $('#supporting_id').val();
  var batch_quantity = $('#batch_quantity').val();

  if(supporting_id != '' || batch_quantity != ''){
    $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        action: 'SUPPORTING_Recipe_Load',
        supporting_id: supporting_id,
        batch_quantity: batch_quantity
      
      
      },
      cache: false,
      success: function(dataResult){
        $('#refresh_cart').html(dataResult); 	

      }
    });
  
  }else{
   alert('Select a Product and Give Batch QTY');

  }
 



}

function spray_batch_calculation(){

  var spray_material_id = $('#spray_material_id').val();
  var batch_quantity = $('#batch_quantity').val();


  if(spray_material_id != ''){
    $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        action: 'SPREY_Recipe_Load',
        spray_material_id: spray_material_id,
        batch_quantity: batch_quantity
      
      
      },
      cache: false,
      success: function(dataResult){
        $('#refresh_cart').html(dataResult); 	
        $("#spray_batch_calculation").removeAttr("disabled");

      }
    });
  
  }else{
    $('#refresh_cart').html('Select a product'); 	
    $("#spray_batch_calculation").removeAttr("disabled");

  }
 



}



function print_batch_calculation(){

  
  var print_material_id = $('#print_material_id').val();
  var batch_quantity = $('#batch_quantity').val();


  if(print_material_id != ''){
    $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        action: 'PRINT_Recipe_Load',
        print_material_id: print_material_id,
        batch_quantity: batch_quantity
      
      
      },
      cache: false,
      success: function(dataResult){
        $('#refresh_cart').html(dataResult); 	
        $("#print_batch_calculation").removeAttr("disabled");

      }
    });
  
  }else{
    $('#refresh_cart').html('Select a product'); 	
    $("#print_batch_calculation").removeAttr("disabled");

  }
 

}


function calculate_batch(){
  var PRODUCT_ID = $('#product_id').val();
  var batch_quantity = $('#batch_quantity').val();

  if (batch_quantity == '' ) { alert("Batch qty Cant empty"); $('#batch_quantity').focus(); return false;}
  if (PRODUCT_ID == '' ) { alert("Select a product"); $('#PRODUCT_ID').focus(); return false;}

    $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        action: 'FG_Recipe_Load',
        PRODUCT_ID: PRODUCT_ID,
        batch_quantity: batch_quantity
      },
      cache: false,
      success: function(dataResult){
        $('#refresh_cart').html(dataResult); 	

      }
    });
  
 
 

}



      $('#submit-search').on('click', function() {
        $("#submit-search").attr("disabled", "disabled");
  
        var spinner = "<img src='report.gif' style='height:350px' alt='loading...' />";
        $('#load_report').html(spinner); 	

          $.ajax({
            url: "generate_report.php",
            type: "POST",
            data: {
              action: 'Overall FG Inventory'
            },
            cache: false,
            success: function(dataResult){
              $('#load_report').html(dataResult); 	
              filter_data();
              $('#ATable').DataTable();
              $("#submit-search").removeAttr("disabled");
            }
          });
        
        });


        $('#submit-search_raw').on('click', function() {
          $("#submit-search_raw").attr("disabled", "disabled");
    
          var spinner = "<img src='report.gif' style='height:350px' alt='loading...' />";
          $('#load_report2').html(spinner); 	
  
            $.ajax({
              url: "generate_report.php",
              type: "POST",
              data: {
                action: 'Overall RAW Inventory'
              },
              cache: false,
              success: function(dataResult){
                $('#load_report2').html(dataResult); 	
                filter_data();
  
                $("#submit-search_raw").removeAttr("disabled");
              }
            });
          
          });
  
          

          
function find_purchase_invoice_by_no(TYPE){

  var INVOICENO = $('#invoice_no').val();
  var purches_type = $('#purches_type').val();

  if(purches_type == 'raw_local_purches' ){
    var open_modal = 'Return Raw Local Purches';
  }else if(purches_type == 'fg_local_purches'){
    var open_modal = 'Return FG Local Purches';
  }else{
  var open_modal = '';
  }

  if(INVOICENO === '' ){ alert('Plsease write invoice no'); return false; }
  if(purches_type === '' ){ alert('Plsease select Purchase Type'); return false; }


  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      INVOICENO: INVOICENO,
      TYPE: TYPE,
      open_modal: open_modal,
      purches_type: purches_type,
      action: 'find_purchase_invoice_by_no'
    },
    cache: false,
    dataType: 'json',
    success: function(dataResult){
      document.getElementById('invoice_load').innerHTML = dataResult.content;
      
    }

  });


}



function find_sales_invoice_by_details(TYPE){

  var INVOICENO = $('#invoice_no').val();
  
  if(INVOICENO === '' ){ alert('Plsease write invoice no'); return false; }
  

  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      INVOICENO: INVOICENO,
      TYPE: TYPE,
      action: 'find_invoice_by_invoice_no'
    },
    cache: false,
    dataType: 'json',
    success: function(dataResult){
      document.getElementById('invoice_load').innerHTML = dataResult.content;
      $('#ATable').DataTable();
    }

  });


}


function find_sales_invoice_by_no(TYPE){

  var INVOICENO = $('#invoice_no').val();
  
  if(INVOICENO === '' ){ alert('Plsease write invoice no'); return false; }
  

  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      INVOICENO: INVOICENO,
      TYPE: TYPE,
      action: 'find_invoice_by_invoice_no'
    },
    cache: false,
    dataType: 'json',
    success: function(dataResult){
      document.getElementById('invoice_load').innerHTML = dataResult.content;
      
    }

  });


}



  


          function final_damage_receiv_by_warehouse(){

            
  cart2 = [];
  var code = $('#code').val();
  var total_item = $('#total_item').val();

  for(var i = 1; i <= total_item; i++) {
    var element2 = {};

    var id =  $('#id'+[i]).val();
    var damage_quantity =  $('#damage_quantity'+[i]).val();
    var warehouse_id =  $('#warehouse_id'+[i]).val();
    var product_id =  $('#product_id'+[i]).val();

    element2.id = id;
    element2.warehouse_id = warehouse_id;
    element2.damage_quantity = damage_quantity;
    element2.product_id = product_id;

    cart2.push({element2: element2});

  }

  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      today_data: JSON.stringify(cart2),
      code: code,
      total_item: total_item,
      action: 'final_damage_receiv_by_warehouse'
    },
    cache: false,
    success: function(dataResult){

    alert(dataResult);     
    location.reload();
    
    }
  });
          }
 


        function approve_single_field(FIELD1,ID,TABLE){


          $.ajax({
            url: "function_tem.php",
            type: "POST",
            data: {
              TABLE: TABLE,
              FIELD1: FIELD1,
              ID: ID,
              action: 'approve_single_pending_data'
            },
            cache: false,
            success: function(dataResult){
              alert(dataResult);
            location.reload();
            }
  
          });
        }


       
        


        function allowDrop(ev) {
          ev.preventDefault();
        }
        
        function drag(ev) {
          ev.dataTransfer.setData("text", ev.target.id);
        }
        
        function drop(ev) {
          ev.preventDefault();
          var data = ev.dataTransfer.getData("text");
          ev.target.appendChild(document.getElementById(data));
        }


        function getDepartmentWiseEmployee(VALUE){

          $.ajax({
            url: "function_tem.php",
            type: "POST",
            data: {
              VALUE: VALUE,
              action: 'get_department_wise_value'
            },
            cache: false,
            success: function(dataResult){
             
              document.getElementById('employee_list').innerHTML = dataResult;
              $('.selectpicker').selectpicker('refresh');

            }
  
          });

        }


function importFromMachine(){

  var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='finger_print.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";

  $('#employee_data').html(spinner); 	


  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      action: 'get_data_from_machine'

    },
    cache: false,
    success: function(dataResult){
      alert(dataResult);
      getEmloyeeForattanance();
    }

  });



}

function stock_adjustment_in_bulk() {
  var file_data = $('#fileToUpload').prop('files')[0];
  var warehouse_id = $('#warehouse_id').val();
  var adjustment_date = $('#adjustment_date').val();

  // Check if file_data, warehouse_id, or adjustment_date are empty
  if (!file_data) {
      alert("Please select a file to upload.");
      return;
  }
  if (!warehouse_id) {
      alert("Please select a warehouse.");
      return;
  }
  if (!adjustment_date) {
      alert("Please select an adjustment date.");
      return;
  }

  var form_data = new FormData();
  form_data.append('fileToUpload', file_data);
  form_data.append('warehouse_id', warehouse_id);
  form_data.append('adjustment_date', adjustment_date);

  document.getElementById("load_msg").innerHTML = '<img style="height:200px;" src="img/data_import.gif">';

  $.ajax({
      url: "stock_adjustment_in_bulk.php",
      type: "POST",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
      success: function(data) {
          location.reload();
      }
  });
}




        function getEmloyeeForattanance(){

          var attendance_date = $('#attendance_date').val();

          var spinner = "<img src='report.gif' style='height:350px' alt='loading...' />";
          $('#employee_data').html(spinner); 	
          $.ajax({
            url: "function_tem.php",
            type: "POST",
            data: {

              action: 'employee_data_for_attendance',
              attendance_date: attendance_date
            },
            cache: false,
            dataType: 'json',
            success: function(dataResult){

              document.getElementById('employee_data').innerHTML = dataResult.content;
             // $('#ATable').DataTable();

            } 
  
          });

        }


      function final_this_task(FIELD1,FIELD2,FIELD3,TABLE,CODE){
        var x = window.confirm("Are you sure to Final ?");
        if(x){
        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {
            TABLE: TABLE,
            FIELD1: FIELD1,
            FIELD2: FIELD2,
            FIELD3: FIELD3,
            CODE: CODE,
            action: 'approve_pending_data'
          },
          cache: false,
          success: function(dataResult){
            alert(dataResult);
          location.reload();
          }

        });
      }
      }


       
      function send_for_fitting(CODE,STATUS){

        var x = window.confirm("Are you sure to send for fitting ?");
        if(x){
        $.ajax({
          url: "function_tem.php",
          type: "POST",
          data: {
            CODE: CODE,
            action: 'send_for_fitting',
            STATUS:STATUS
          },
          cache: false,
          success: function(dataResult){
            alert(dataResult);
          location.reload();
          }

        });
      }


      }



      function takeAttandance() {
  var attendance_date = $('#attendance_date').val();
  var cart2 = [];

  // Loop through each row of the table
  $('#ATable tbody tr').each(function (index, row) {
    var element2 = {};

    // Find the employee ID from the hidden input in this row
    var employee_id = $(row).find('input[id^="employee_id"]').val();

    // Get selected attendance value
    var selectedValue = $(row).find('input[type="radio"]:checked').attr('id') || '';

    element2.present = selectedValue.includes("present") ? 1 : 0;
    element2.late = selectedValue.includes("late") ? 1 : 0;
    element2.absent = selectedValue.includes("absent") ? 1 : 0;
    element2.leave = selectedValue.includes("leave") ? 1 : 0;
    element2.employee_id = employee_id;

    cart2.push({ element2: element2 });
  });

  var count_item = cart2.length;

  // Send data to backend
  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      action: 'take_attandance_action',
      today_attendance: JSON.stringify(cart2),
      count_item: count_item,
      attendance_date: attendance_date
    },
    cache: false,
    success: function (dataResult) {
      alert(dataResult);
      getEmloyeeForattanance(); // reload updated data
    }
  });
}




    
      $('#create_molding_recipe_wise_demand').on('click', function() {


        var total_item = $('#total_item').val();
        var molding_type = $('#molding_type').val();
        var send_to_id = $('#send_to_id').val();
        var batch_quantity = $('#batch_quantity').val();
        var supporting_id = $('#supporting_id').val();
        var accepting_delivery_date = $('#accepting_delivery_date').val();
        var note = $('#note').val();
        var material_id = $("[name^='material_id']").map(function() { return $(this).val() }).get();
        var demand_qty = $("[name^='demand_qty']").map(function() { return $(this).val() }).get();

        if (total_item ==  0) { alert("No Item Added"); $('#molding_type').focus(); return false; }
        if (molding_type == '' ) { alert("Select Molding Type"); $('#molding_type').focus(); return false; }
        if (send_to_id == '' ) { alert("Select One Supplier or Factory Name"); $('#send_to_id').focus(); return false; }
        if (batch_quantity == '' ) { alert("Batch Cant not empty"); $('#batch_quantity').focus(); return false; }
        if (accepting_delivery_date == '' ) { alert("Deadline Cant not empty"); $('#accepting_delivery_date').focus(); return false; }
        if (supporting_id == '' ) { alert("Select a Product"); $('#supporting_id').focus(); return false; }


        if (total_item ==  0) {
          alert("No Item Added");
          $('#model').focus();
          return false;
          }


        for (var no = 1; no <= total_item; no++) {
          var tds = document.getElementsByClassName("tr" + no );
          for(var i = 0, j = tds.length; i < j; ++i)

          if ($.trim($('#demand_qty' + no).val()).length == 0 || $.trim($('#demand_qty' + no).val()) < 0) {
            
              tds[i].style.color = "red";
              alert("Please Enter valid number");
              $('#demand_qty' + no).focus();
              return false;
          }else{  
            
            tds[i].style.color = "black"; 
          
          }
               
      
      }




  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      total_item: total_item,
      supporting_id: supporting_id,
      action: 'create_molding_recipe_wise_demand',
      demand_qty: demand_qty,
      material_id:material_id,
      batch_quantity: batch_quantity,
      accepting_delivery_date: accepting_delivery_date,
      molding_type: molding_type,
      send_to_id: send_to_id,
      note: note
    },
    cache: false,
    success: function(dataResult){

      $("#refresh_cart2").load('cart_request_raw_material_mold.php' );

    document.getElementById('mess_box').innerHTML = '<strong style="color:red"> ' + dataResult + '</strong>';

    setTimeout(function(){
    document.getElementById('mess_box').innerHTML = '';
    }, 2000);


    
    }
  });




        });
      


        $('#create_signle_demand').on('click', function() {


          var total_item = $('#total_item').val();
          var send_to = $('#send_to').val();
          var send_to_id = $('#send_to_id').val();
          var material_id = $('#material_id').val();
          var accepting_delivery_date = $('#accepting_delivery_date').val();
          var pi_no = $('#pi_no').val();
          var demand_qty = $("[name^='demand_qty']").map(function() { return $(this).val() }).get();
      
          if (total_item ==  0) { alert("No Item Added"); $('#send_to').focus(); return false; }
          if (send_to == '' ) { alert("Select Molding Type"); $('#send_to').focus(); return false; }
          if (send_to_id == '' ) { alert("Select One Supplier or Factory Name"); $('#send_to_id').focus(); return false; }
          if (demand_qty == '' ) { alert("Demand Cant not empty"); $('#demand_qty').focus(); return false; }
          if (accepting_delivery_date == '' ) { alert("Date Cant not empty"); $('#accepting_delivery_date').focus(); return false; }
          if (material_id == '' ) { alert("Select a Product"); $('#material_id').focus(); return false; }
      
      
      
      
      
      $.ajax({
      url: "form_action.php",
      type: "POST",
      data: {
        total_item: total_item,
        material_id: material_id,
        action: 'create_signle_demand',
        demand_qty: demand_qty,
        material_id:material_id,
        accepting_delivery_date: accepting_delivery_date,
        send_to: send_to,
        send_to_id: send_to_id,
        pi_no: pi_no
      },
      cache: false,
      success: function(dataResult){
      
      
        $("#refresh_cart").load('cart_single_demand.php' );
      
        document.getElementById('mess_box').innerHTML = '<strong style="color:red"> ' + dataResult + '</strong>';
      
        setTimeout(function(){
        document.getElementById('mess_box').innerHTML = '';
        }, 2000);
      
      
      
      }
      });
      
          });
      



        $('#create_spray_recipe_wise_demand').on('click', function() {


          var total_item = $('#total_item').val();
          var send_to = $('#send_to').val();
          var send_to_id = $('#send_to_id').val();
          var batch_quantity = $('#batch_quantity').val();
          var spray_material_id = $('#spray_material_id').val();
          var accepting_delivery_date = $('#accepting_delivery_date').val();
          var note = $('#note').val();
          var material_id = $("[name^='material_id']").map(function() { return $(this).val() }).get();
          var demand_qty = $("[name^='demand_qty']").map(function() { return $(this).val() }).get();
  
          if (total_item ==  0) { alert("No Item Added"); $('#send_to').focus(); return false; }
          if (send_to == '' ) { alert("Select Molding Type"); $('#send_to').focus(); return false; }
          if (send_to_id == '' ) { alert("Select One Supplier or Factory Name"); $('#send_to_id').focus(); return false; }
          if (batch_quantity == '' ) { alert("Batch Cant not empty"); $('#batch_quantity').focus(); return false; }
          if (accepting_delivery_date == '' ) { alert("Deadline Cant not empty"); $('#accepting_delivery_date').focus(); return false; }
          if (spray_material_id == '' ) { alert("Select a Product"); $('#spray_material_id').focus(); return false; }
  

  
          if (total_item ==  0) {
            alert("No Item Added");
            $('#model').focus();
            return false;
            }
  
  
          for (var no = 1; no <= total_item; no++) {
            var tds = document.getElementsByClassName("tr" + no );
            for(var i = 0, j = tds.length; i < j; ++i)
  
            if ($.trim($('#demand_qty' + no).val()).length == 0 || $.trim($('#demand_qty' + no).val()) < 0) {
              
                tds[i].style.color = "red";
                alert("Please Enter valid number");
                $('#demand_qty' + no).focus();
                return false;
            }else{  
              
              tds[i].style.color = "black"; 
            
            }
                 
        
        }
  

      $.ajax({
      url: "form_action.php",
      type: "POST",
      data: {
        total_item: total_item,
        spray_material_id: spray_material_id,
        action: 'create_spray_recipe_wise_demand',
        demand_qty: demand_qty,
        material_id:material_id,
        batch_quantity: batch_quantity,
        accepting_delivery_date: accepting_delivery_date,
        send_to: send_to,
        send_to_id: send_to_id,
        note: note
      },
      cache: false,
      success: function(dataResult){
      

        $("#refresh_cart2").load('cart_request_raw_material_spray.php' );

        document.getElementById('mess_box').innerHTML = '<strong style="color:red"> ' + dataResult + '</strong>';
    
        setTimeout(function(){
        document.getElementById('mess_box').innerHTML = '';
        }, 2000);
    
    
      
      }
      });
      
      
  
      
      
          });



          
          $('#create_print_recipe_wise_demand').on('click', function() {

            var total_item = $('#total_item').val();
            var send_to = $('#send_to').val();
            var send_to_id = $('#send_to_id').val();
            var batch_quantity = $('#batch_quantity').val();
            var print_material_id = $('#print_material_id').val();
            var accepting_delivery_date = $('#accepting_delivery_date').val();
            var note = $('#note').val();
            var material_id = $("[name^='material_id']").map(function() { return $(this).val() }).get();
            var demand_qty = $("[name^='demand_qty']").map(function() { return $(this).val() }).get();
    
            if (total_item ==  0) { alert("No Item Added"); $('#send_to').focus(); return false; }
            if (send_to == '' ) { alert("Select Molding Type"); $('#send_to').focus(); return false; }
            if (send_to_id == '' ) { alert("Select One Supplier or Factory Name"); $('#send_to_id').focus(); return false; }
            if (batch_quantity == '' ) { alert("Batch Cant not empty"); $('#batch_quantity').focus(); return false; }
            if (accepting_delivery_date == '' ) { alert("Deadline Cant not empty"); $('#accepting_delivery_date').focus(); return false; }
            if (print_material_id == '' ) { alert("Select a Product"); $('#print_material_id').focus(); return false; }
    
  
    
            if (total_item ==  0) {
              alert("No Item Added");
              $('#model').focus();
              return false;
              }
    
    
            for (var no = 1; no <= total_item; no++) {
              var tds = document.getElementsByClassName("tr" + no );
              for(var i = 0, j = tds.length; i < j; ++i)
    
              if ($.trim($('#demand_qty' + no).val()).length == 0 || $.trim($('#demand_qty' + no).val()) < 0) {
                
                  tds[i].style.color = "red";
                  alert("Please Enter valid number");
                  $('#demand_qty' + no).focus();
                  return false;
              }else{  
                
                tds[i].style.color = "black"; 
              
              }
                   
          
          }
                
        $.ajax({
        url: "form_action.php",
        type: "POST",
        data: {
          total_item: total_item,
          print_material_id: print_material_id,
          action: 'create_print_recipe_wise_demand',
          demand_qty: demand_qty,
          material_id:material_id,
          batch_quantity: batch_quantity,
          accepting_delivery_date: accepting_delivery_date,
          send_to: send_to,
          send_to_id: send_to_id,
          note: note
        },
        cache: false,
        success: function(dataResult){
        
          $("#refresh_cart2").load('cart_request_raw_material_print.php' );

          document.getElementById('mess_box').innerHTML = '<strong style="color:red"> ' + dataResult + '</strong>';
      
          setTimeout(function(){
          document.getElementById('mess_box').innerHTML = '';
          }, 2000);
      
      
        
        }
        });
        
        

        
            });





      $('#create_recipe_wise_demand').on('click', function() {


        var total_item = $('#total_item').val();
        var accepting_delivery_date = $('#accepting_delivery_date').val();
        var batch_quantity = $('#batch_quantity').val();
        var product_id = $('#product_id').val();
        var send_to = $('#send_to').val();
        var send_to_id = $('#send_to_id').val();
        var pi_no = $('#pi_no').val();
        var note = $('#note').val();



          var material_id = $("[name^='material_id']").map(function() { return $(this).val() }).get();
          var demand_qty = $("[name^='demand_qty']").map(function() { return $(this).val() }).get();
  
          if (send_to ==  '') {alert("Select Assembling From"); $('#send_to').focus(); return false; }
          if (send_to_id ==  '') {alert("Select Assembling From"); $('#send_to_id').focus(); return false; }
          if (pi_no ==  '') {alert("give a pi no"); $('#pi_no').focus(); return false; }
  
          if (product_id ==  '') {alert("Select a poduct first"); $('#product_id').focus(); return false; }
          if (batch_quantity ==  '') {alert("Batch qty cant be empty"); $('#batch_quantity').focus(); return false; }

          if (batch_quantity <=  0 ) {alert("Batch qty cant be empty"); $('#batch_quantity').focus(); return false; }


          if (accepting_delivery_date ==  '') {alert("Give a Deadline"); $('#accepting_delivery_date').focus(); return false; }
  
  
          if (total_item ==  0) {alert("No Item Added"); $('#model').focus(); return false; }
  
          for (var no = 1; no <= total_item; no++) {
            var tds = document.getElementsByClassName("tr" + no );
            for(var i = 0, j = tds.length; i < j; ++i)
  
            if ($.trim($('#demand_qty' + no).val()).length == 0 || $.trim($('#demand_qty' + no).val()) < 0) {
              
                tds[i].style.color = "red";
                alert("Please Enter valid number");
                $('#demand_qty' + no).focus();
                return false;
            }else{  
              
              tds[i].style.color = "black"; 
            }
                 
        
        }
  
        
        
       


  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      total_item: total_item,
      product_id: product_id,
      pi_no: pi_no,
      action: 'create_recipe_wise_demand',
      demand_qty: demand_qty,
      material_id:material_id,
      accepting_delivery_date: accepting_delivery_date,
      batch_quantity: batch_quantity,
      send_to_id: send_to_id,
      send_to: send_to,
      note:note
    },
    cache: false,
    success: function(dataResult){

      $("#refresh_cart2").load('cart_request_raw_material_batch.php' );

    document.getElementById('mess_box').innerHTML = '<strong style="color:red"> ' + dataResult + '</strong>';

    setTimeout(function(){
    document.getElementById('mess_box').innerHTML = '';
    }, 2000);


    
    }
  });


  

        });



async function findTransectionDue(ID,TYPE,SECTION) {

   $('#amount_due').val('000000');
   
   if(ID == '' ){
          $('#amount_due').val('000000');
   }else{
      try {

    const dataResult = await $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
      action: 'find_transection_due',
      ID: ID,
      TYPE:TYPE,
      SECTION:SECTION
      },
      cache: false,
      dataType: 'json',
       success: function(dataResult){
          $('#amount_due').val(dataResult.due);
          
        }
    });

  } catch (error) {
    console.error('An error occurred:', error);
    alert('An error occurred while processing your request. Please try again later.');
  }
  
  
   }

}

   

async function findCustomerDue(ID) {
document.getElementById('CustomerDue' + ID ).innerHTML = 'Loading...';

try {
  const dataResult = await $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
    action: 'find_transection_due',
    ID: ID,
    TYPE: 'Customer',
    SECTION: 'Brunch_Wise'
    },
    cache: false,
    dataType: 'json',
     success: function(dataResult){
        document.getElementById('CustomerDue' + ID).innerHTML = dataResult.due;
      }
  });

} catch (error) {
  console.error('An error occurred:', error);
  alert('An error occurred while processing your request. Please try again later.');
}
}


        function transection_to_details(TYPE){
          transection_to_id = $('#get_transection_to_id').val();

          if(TYPE == ''){
            document.getElementById('transection_to_data').innerHTML = '';
          }else{
        
            $.ajax({
              url: "function_tem.php", 
              type: "POST",
              data: {
                      TYPE: TYPE,
                      action: 'find_transection_to_details',
                      transection_to_id: transection_to_id
              },
              cache: false,
              dataType: 'json',
              success: function(dataResult){
          
                document.getElementById('transection_to_data').innerHTML = dataResult.content;
             

                $('select').selectpicker();
              }
            });
          }
        

         

        }
      

        function convert_to_identified_customer(){

           posting_transection_id = $('#posting_transection_id').val();
           transection_id = $('#transection_id').val();

           
          transection_to_id = $('#identified_transection_to_id').val();

          alert(posting_transection_id);  alert(transection_id);  alert(transection_to_id);

           if(transection_id == '' ){
             alert('The Posting is not done properly , Delete the Transaction from admin panel then try again');
             return false;
           }

           if(transection_to_id == '' ){
            alert('Please select a customer');
            return false;
          }



           $.ajax({
            url: "function_tem.php", 
            type: "POST",
            data: {
                    action: 'convert_to_identified_customer',
                    transection_id: transection_id,
                    posting_transection_id: posting_transection_id,
                    transection_to_id: transection_to_id

            },
            cache: false,
            success: function(dataResult){
            location.reload();
            }
          });


        }




        function transection_by_details(TYPE){


          transection_by_id = $('#get_transection_by_id').val();
          check_number = $('#get_check_number').val();
          check_date = $('#get_check_date').val();

          if(TYPE == ''){
            document.getElementById('transection_by_data').innerHTML = '';
          }else{
        
            $.ajax({
              url: "function_tem.php", 
              type: "POST",
              data: {
                      TYPE: TYPE,
                      action: 'find_transection_by_details',
                      transection_by_id: transection_by_id,
                      check_number: check_number,
                      check_date: check_date

              },
              cache: false,
              dataType: 'json',
              success: function(dataResult){
          
                document.getElementById('transection_by_data').innerHTML = dataResult.content;
                document.getElementById('transection_by_data2').innerHTML = dataResult.content2;

                $('select').selectpicker();
              }
            });
          }
        
        }





        function newLadgerHead(TYPE){

          if(TYPE == 'YES' ){
              document.getElementById("parent_id").disabled = true;  
              document.getElementById('new_ladger_div').style.display = 'block';
              document.getElementById('section_head').style.display = 'none';
              document.getElementById('cHbutton').innerHTML = '<a onclick="newLadgerHead(\'NO\');" target="_blink" class="btn btn-danger" > <span class="fa fa-minus"> </span></a>';
          
          }else{
              document.getElementById("parent_id").disabled = false; 
              document.getElementById('new_ladger_div').style.display = 'none';
              document.getElementById('section_head').style.display = 'block';
              document.getElementById('cHbutton').innerHTML = '<a onclick="newLadgerHead(\'YES\');" target="_blink" class="btn btn-danger" > <span class="fa fa-plus-circle"> </span></a>';
          
          }
          
          
          }


function expense_transection(){
  
  var related_id = $('#related_id').val();
  var transection_to = $('#transection_to').val();
  var transection_head_id = $('#transection_head_id').val();
  var receive_now = getNum($('#amount').val());
  var transection_by = $('#transection_by').val();
  var transection_by_id = $('#transection_by_id').val();
  var check_number = $('#check_number').val();
  var check_date = $('#check_date').val();
  var transection_to_id = $('#transection_to_id').val();
  var note = $('#note').val();
  var code = $('#code').val();
  var data_inserted_from = $('#data_inserted_from').val();
  var ledger_id = $('#ledger_id').val();
  var transection_date = $('#transection_date').val();

  if(transection_to == 'Supplier' ){
    var transaction_type = $('#transaction_type').val();

  }else{
    var transaction_type = 'PAYMENT';

  }


  if(transection_by == 'Bank'){

    var check_number = $('#check_number').val();
    var check_date = $('#check_date').val();
      if (transection_by_id ==  '') {alert("select Bank"); $('#transection_by_id').focus(); return false; }

  }else if (transection_by == 'Mobile-Banking'){

    var check_number = '';
    var check_date = '';
      if (transection_by_id ==  '') {alert("select Mobile No"); $('#transection_by_id').focus(); return false; }


  }else if(transection_by == 'Cash'){

    var check_number = '';
    var check_date = '';

  }else{

    var check_number = '';
    var check_date = '';
  }

  if (transection_to ==  '') {alert("Select Transaction To"); $('#transection_to').focus(); return false; }
  if (transection_to_id ==  '') {alert("Select Transaction To"); $('#transection_to_id').focus(); return false; }
  if (transection_head_id ==  '') {alert("Select Head"); $('#transection_head_id').focus(); return false; }
  if (transection_by ==  '') {alert("Select Transaction BY"); $('#transection_by').focus(); return false; }

  if (receive_now ==  '') {alert("Amount is empty"); $('#amount').focus(); return false; }
  if (receive_now < 0) {alert("Amount can not nagative"); $('#amount').focus(); return false; }
  if (receive_now == 0) {alert("Amount can not zero"); $('#amount').focus(); return false; }

  if (transection_date == '') {alert("Date can not empty"); $('#transection_date').focus(); return false; }


  $.ajax({
    url: "form_action.php", 
    type: "POST",
    data: {
          related_id: related_id,
          ledger_id: ledger_id,
          transection_head_id: transection_head_id,
          receive_now: receive_now,
          transection_by: transection_by,
          transection_by_id: transection_by_id,
          check_number: check_number,
          check_date: check_date,
          transection_to: transection_to,
          transection_to_id: transection_to_id,
          transection_date: transection_date,
          transaction_type: transaction_type,
          note: note,
          action: 'save_expense_transection',
          code: code,
          data_inserted_from: data_inserted_from
    },
    cache: false,
    dataType: 'json',
    success: function(dataResult){
    
    alert(dataResult.mess);
    var x = window.confirm("Do you want to Print ?");
    if(x){
      window.open("money_recipt.php?status=Done&id="+dataResult.transection_id  );

    }
    location.reload();
    }
    });


}




function find_area_data(TABLE,FIELDNAME,id,value){

  var selectedValues = [];
  var selectElement = document.getElementById(id);
  for (var i = 0; i < selectElement.options.length; i++) {
    if (selectElement.options[i].selected) {
      selectedValues.push(selectElement.options[i].value);
    }
  }

  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      TABLE: TABLE,
      FIELD: id,
      selectedValues: selectedValues,
      action: 'find_area_data'
    },
    cache: false,
    success: function(dataResult){
        $('#Load_'+TABLE+'_level').html(FIELDNAME); 	
        $('#Load_'+TABLE+'_value').html(dataResult); 	
        $('select').selectpicker();

    }

  });



}
  

function call_it_a_day(){
  var count_total_invoice = $('#count_total_invoice').val();
  if(count_total_invoice <=  0 ){
    alert('No Invoice Created');
    return false;
  }else{

    
  var x = window.confirm("Are you sure to call it a day ?");
  if(x){
    cart = [];
  
for(var i = 1; i <= count_total_invoice; i++) {
  var element = {};
  var inid =  $('#inid'+[i]).val();
    element.inid = inid;
    cart.push({element: element});
}



$.ajax({
  url: "form_action.php",
  type: "POST",
  data: {
    action: 'CALL_IT_A_DAY',
    all_data: JSON.stringify(cart),
    count_total_invoice: count_total_invoice
    
  },
  cache: false,
  success: function(dataResult){

    alert(dataResult);
    location.reload();
  
  }
});



  }

  }
}




function income_transection(){

 
  var related_id = $('#related_id').val();
  var transection_head_id = $('#transection_head_id').val();
  var receive_now = getNum($('#amount').val());
  var transection_by = $('#transection_by').val();
  var transection_by_id = $('#transection_by_id').val();
  var check_number = $('#check_number').val();
  var check_date = $('#check_date').val();
  var transection_to_id = $('#transection_to_id').val();
  var transection_to = $('#transection_to').val();
  var ledger_id = $('#ledger_id').val();
  var extra_field = $('#extra_field').val();
  var transection_date = $('#transection_date').val();


  var note = $('#note').val();
  var code = $('#code').val();
  var data_inserted_from = $('#data_inserted_from').val();


  if (data_inserted_from == 'Invoice Wise Payment' ) { 
    var previous_discount = getNum($('#previous_discount').val());
    var discount = getNum($('#discount').val());

    
  }else{
      var previous_discount = 0.00 ; 
      var discount = 0.00 ; 
  }


if(transection_to == 'Customer' ){
  var transaction_type = $('#transaction_type').val();

}else{
  var transaction_type = 'RECEIVE';

}
  if(transection_by == 'Bank'){

    var check_number = $('#check_number').val();
    var check_date = $('#check_date').val();
    if (transection_by_id ==  '') {alert("select Bank"); $('#transection_by_id').focus(); return false; }

  }else if (transection_by == 'Mobile-Banking'){

    var check_number = '';
    var check_date = '';
    if (transection_by_id ==  '') {alert("select Mobile No"); $('#transection_by_id').focus(); return false; }


  }else if(transection_by == 'Cash'){

    var check_number = '';
    var check_date = '';

  }else{

    var check_number = '';
    var check_date = '';
  }


  if (transection_to_id ==  '') {alert("select Transaction To"); $('#transection_to_id').focus(); return false; }
  if (transection_head_id ==  '') {alert("Select Head"); $('#transection_head_id').focus(); return false; }
  if (transection_by ==  '') {alert("Select Transaction By"); $('#transection_by').focus(); return false; }

  if (receive_now ==  '') {alert("Amount is empty"); $('#amount').focus(); return false; }
  if (receive_now < 0) {alert("Amount can not nagative"); $('#amount').focus(); return false; }
  if (receive_now == 0) {alert("Amount can not zero"); $('#amount').focus(); return false; }

  if (transection_date == '') {alert("Date can not empty"); $('#transection_date').focus(); return false; }



  $.ajax({
    url: "form_action.php", 
    type: "POST",
    data: {
          related_id: related_id,
          transection_date:transection_date,
          ledger_id: ledger_id,
          transection_head_id: transection_head_id,
          receive_now: receive_now,
          transection_by: transection_by,
          transection_by_id: transection_by_id,
          check_number: check_number,
          transection_to: transection_to,
          check_date: check_date,
          transection_to_id: transection_to_id,
          note: note,
          extra_field: extra_field,
          discount: discount,
          previous_discount: previous_discount,
          transaction_type: transaction_type,
          action: 'save_income_transection',
          code: code,
          data_inserted_from: data_inserted_from
    },
    cache: false,
    dataType: 'json',
    success: function(dataResult){
      alert(dataResult.mess);
      if(dataResult.transection_id == 'NOID' ){

      }else{
        var x = window.confirm("Do you want to Print ?");
        if(x){
        window.open("money_recipt.php?status=Done&id="+dataResult.transection_id  );

        }
      }

      location.reload();
    
    }
    });




}


function pending_income_transection(){

 
    var related_id = $('#related_id').val();
    var transection_head_id = $('#transection_head_id').val();
    var receive_now = getNum($('#amount').val());
    var transection_by = $('#transection_by').val();
    var transection_by_id = $('#transection_by_id').val();
    var check_number = $('#check_number').val();
    var check_date = $('#check_date').val();
    var transection_to_id = $('#transection_to_id').val();
    var transection_to = $('#transection_to').val();
    var ledger_id = $('#ledger_id').val();
    var extra_field = $('#extra_field').val();
    var transection_date = $('#transection_date').val();
    var collect_by = $('#collect_by').val();


    var note = $('#note').val();
    var code = $('#code').val();
    var data_inserted_from = $('#data_inserted_from').val();


  if(transection_to == 'Customer' ){
    var tr_type = $('#tr_type').val();
  }else{
    var tr_type = 'RECEIVE';

  }


 
    if(transection_by == 'Bank'){

      var check_number = $('#check_number').val();
      var check_date = $('#check_date').val();
      if (transection_by_id ==  '') {alert("select Bank"); $('#transection_by_id').focus(); return false; }

    }else if (transection_by == 'Mobile-Banking'){

      var check_number = '';
      var check_date = '';
      if (transection_by_id ==  '') {alert("select Mobile No"); $('#transection_by_id').focus(); return false; }


    }else if(transection_by == 'Cash'){

      var check_number = '';
      var check_date = '';

    }else{

      var check_number = '';
      var check_date = '';
    }

    if (collect_by ==  '') {alert("Collected by is empty"); $('#collect_by').focus(); return false; }
    
    

    if (transection_to_id ==  '') {alert("select Transaction To"); $('#transection_to_id').focus(); return false; }
    if (transection_head_id ==  '') {alert("Select Head"); $('#transection_head_id').focus(); return false; }
    if (transection_by ==  '') {alert("Select Transaction By"); $('#transection_by').focus(); return false; }

    if (receive_now ==  '') {alert("Amount is empty"); $('#amount').focus(); return false; }
    if (receive_now < 0) {alert("Amount can not nagative"); $('#amount').focus(); return false; }
    if (receive_now == 0) {alert("Amount can not zero"); $('#amount').focus(); return false; }

    if (transection_date == '') {alert("Date can not empty"); $('#transection_date').focus(); return false; }

    $.ajax({
      url: "form_action.php", 
      type: "POST",
      data: {
            related_id: related_id,
            transection_date:transection_date,
            ledger_id: ledger_id,
            transection_head_id: transection_head_id,
            receive_now: receive_now,
            transection_by: transection_by,
            transection_by_id: transection_by_id,
            check_number: check_number,
            transection_to: transection_to,
            check_date: check_date,
            transection_to_id: transection_to_id,
            note: note,
            collect_by: collect_by,
            extra_field: extra_field,
            tr_type: tr_type,
            action: 'save_pending_income_transection',
            code: code,
            data_inserted_from: data_inserted_from
      },
      cache: false,
      dataType: 'json',
      success: function(dataResult){
        alert(dataResult.mess);

        var x = window.confirm("Do you want to Print ?");
        if(x){
        window.open("money_recipt.php?status=Pending&id="+dataResult.transection_id  );
        }

        location.reload();
      
      }
      });




  }


  
function pending_expense_transection(){

 
  var related_id = $('#related_id').val();
  var transection_head_id = $('#transection_head_id').val();
  var receive_now = getNum($('#amount').val());
  var transection_by = $('#transection_by').val();
  var transection_by_id = $('#transection_by_id').val();
  var check_number = $('#check_number').val();
  var check_date = $('#check_date').val();
  var transection_to_id = $('#transection_to_id').val();
  var transection_to = $('#transection_to').val();
  var ledger_id = $('#ledger_id').val();
  var extra_field = $('#extra_field').val();
  var transection_date = $('#transection_date').val();


  var note = $('#note').val();
  var code = $('#code').val();
  var data_inserted_from = $('#data_inserted_from').val();


if(transection_to == 'Supplier' ){
  var tr_type = $('#tr_type').val();

}else{
  var tr_type = 'PAYMENT';

}
  if(transection_by == 'Bank'){

    var check_number = $('#check_number').val();
    var check_date = $('#check_date').val();
    if (transection_by_id ==  '') {alert("select Bank"); $('#transection_by_id').focus(); return false; }

  }else if (transection_by == 'Mobile-Banking'){

    var check_number = '';
    var check_date = '';
    if (transection_by_id ==  '') {alert("select Mobile No"); $('#transection_by_id').focus(); return false; }


  }else if(transection_by == 'Cash'){

    var check_number = '';
    var check_date = '';

  }else{

    var check_number = '';
    var check_date = '';
  }


  if (transection_to_id ==  '') {alert("select Transaction To"); $('#transection_to_id').focus(); return false; }
  if (transection_head_id ==  '') {alert("Select Head"); $('#transection_head_id').focus(); return false; }
  if (transection_by ==  '') {alert("Select Transaction By"); $('#transection_by').focus(); return false; }

  if (receive_now ==  '') {alert("Amount is empty"); $('#amount').focus(); return false; }
  if (receive_now < 0) {alert("Amount can not nagative"); $('#amount').focus(); return false; }
  if (receive_now == 0) {alert("Amount can not zero"); $('#amount').focus(); return false; }

  if (transection_date == '') {alert("Date can not empty"); $('#transection_date').focus(); return false; }



  $.ajax({
    url: "form_action.php", 
    type: "POST",
    data: {
          related_id: related_id,
          transection_date:transection_date,
          ledger_id: ledger_id,
          transection_head_id: transection_head_id,
          receive_now: receive_now,
          transection_by: transection_by,
          transection_by_id: transection_by_id,
          check_number: check_number,
          transection_to: transection_to,
          check_date: check_date,
          transection_to_id: transection_to_id,
          note: note,
          extra_field: extra_field,
          tr_type: tr_type,
          action: 'save_pending_expense_transection',
          code: code,
          data_inserted_from: data_inserted_from
    },
    cache: false,
    dataType: 'json',
    success: function(dataResult){
      alert(dataResult.mess);
      if(dataResult.transection_id == 'NOID' ){

      }else{
        var x = window.confirm("Do you want to Print ?");
        if(x){
        window.open("money_recipt.php?status=Done&id="+dataResult.transection_id  );

        }
      }

      location.reload();
    
    }
    });




}


function leaveType(){

  var leave_type_id = $('#leave_type_id').val();

  $.ajax({
    url: "search_by_type.php",
    type: "POST",
    data: {
      SEARCH_BY: 'LEAVE_TYPE_WISE_DATA',
      leave_type_id: leave_type_id
    },
    cache: false,
    dataType: 'json',
    success: function(dataResult){


      if(leave_type_id == '1' || leave_type_id == '2'){

        document.getElementById('level1').style.display = 'none'; 
        document.getElementById('leave_end').style.display = 'none'; 
    
      }else{


        document.getElementById('level1').style.display = 'block'; 
        document.getElementById('leave_end').style.display = 'block'; 
      }




      $('#listType' ).html(dataResult.level); 	
      $('#employee_list' ).html(dataResult.value); 	
      $('#select_clear_toggole' ).html(dataResult.value2); 	
      $('select').selectpicker();

    }
  });


}


function edit_ac_head(){

  var related_id = $('#related_id').val();
  var new_ledger_id = $('#new_ledger_id').val();
  var new_account_head = $('#new_account_head').val();
  var new_account_type =  $('#new_account_type').val();

  if (new_account_head ==  '') {alert("New Ac Head Empty"); $('#new_account_head').focus(); return false; }

  $.ajax({
    url: "form_action.php", 
    type: "POST",
    data: {
          related_id: related_id,
          new_ledger_id: new_ledger_id,
          new_account_head: new_account_head,
          new_account_type: new_account_type,
          action: 'edit_ac_head'
    },
    cache: false,
    success: function(dataResult){
    
    alert(dataResult);
    location.reload();
    
    
    }
    });




}



function edit_ledger(){

  var related_id = $('#related_id').val();
  var new_ladger_head = $('#new_ladger_head').val();


  if (new_ladger_head ==  '') {alert("New Ladger Empty"); $('#ladger_head').focus(); return false; }

  $.ajax({
    url: "form_action.php", 
    type: "POST",
    data: {
          related_id: related_id,
          new_ladger_head: new_ladger_head,
          action: 'edit_ledger'
    },
    cache: false,
    success: function(dataResult){
    
    alert(dataResult);
    location.reload();
    
    
    }
    });


}



  $('#create_ac_head').on('click', function() {


    var related_id = $('#related_id').val();
    var description = $('#description').val();


    if($('#parent_id').is(':disabled')){
        
        var parent_id = 'New_Parent';
        var ladger_head = $('#new_ladger_head').val();
        var account_head = '';
        var account_type = 'All';

        if (ladger_head ==  '') {alert("New Ladger Empty"); $('#ladger_head').focus(); return false; }

    }else{
        var parent_id = $('#parent_id').val();
        var ladger_head = '';
        var account_head = $('#account_head').val();
        var account_type = $('#account_type').val();

        if (parent_id ==  '') {alert("Ledger Head Empty"); $('#parent_id').focus(); return false; }
        if (account_head ==  '') {alert("AC Head is Empty"); $('#account_head').focus(); return false; }

    }

    
  
  $.ajax({
  url: "form_action.php", 
  type: "POST",
  data: {
        related_id: related_id,
        account_head: account_head,
        account_type: account_type,
        action: 'save_ac_head',
        description: description,
        ladger_head: ladger_head,
        parent_id:parent_id
  },
  cache: false,
  success: function(dataResult){
  
  alert(dataResult);
  location.reload();
  
  
  }
  });
  
    });
  
  

  $('#create_mobile_bank').on('click', function() {

    var related_id = $('#related_id').val();
    var mobile_bank_name = $('#mobile_bank_name').val();
    var mobile_number = $('#mobile_number').val();
    var description = $('#description').val();
    var status = $('#status').val();


      if (mobile_bank_name ==  '') {alert("Bank Name is Empty"); $('#mobile_bank_name').focus(); return false; }
      if (mobile_number ==  '') {alert("Number Empty"); $('#mobile_number').focus(); return false; }


$.ajax({
url: "form_action.php", 
type: "POST",
data: {
        related_id: related_id,
        mobile_bank_name: mobile_bank_name,
        action: 'save_mobile_bank',
        description: description,
        mobile_number: mobile_number,
        status: status
},
cache: false,
success: function(dataResult){

  alert(dataResult);
location.reload();


}
});




    });




      $('#create_bank').on('click', function() {

        var related_id = $('#related_id').val();
        var bank_name = $('#bank_name').val();
        var brunch_name = $('#brunch_name').val();
        var account_number = $('#account_number').val();
        var account_name = $('#account_name').val();
        var description = $('#description').val();
        var status = $('#status').val();

  
          if (bank_name ==  '') {alert("Bank Name is Empty"); $('#bank_name').focus(); return false; }
          if (brunch_name ==  '') {alert("Brunch Name Empty"); $('#brunch_name').focus(); return false; }
          if (account_number ==  '') {alert("A/c Number Empty"); $('#account_number').focus(); return false; }
  
          if (account_name ==  '') {alert("A/C Name empty"); $('#account_name').focus(); return false; }
        

  $.ajax({
    url: "form_action.php", 
    type: "POST",
    data: {
            related_id: related_id,
            bank_name: bank_name,
            brunch_name: brunch_name,
            action: 'save_bank',
            account_number: account_number,
            account_name: account_name,
            description: description,
            status: status
    },
    cache: false,
    success: function(dataResult){

      alert(dataResult);
location.reload();

    
    }
  });


  

        });

        function printMe() {
          var frame = document.getElementsByClassName('mydivclass').item(0);
          var data = frame.innerHTML;
          var win = window.open('', '', 'height=500,width=900');
          win.document.write('<style>@page{size:landscape;}</style><html><head><title></title>');
          win.document.write('</head><body >');
          win.document.write(data);
          win.document.write('</body></html>');
          win.print();
          win.close();
          return true;
      }
      

      function Change_Type(TYPE,LEVELDIV,VALUEDIV){

        var search_type = $('#' + TYPE ).val();
        $.ajax({
          url: "search_by_type.php",
          type: "POST",
          data: {
            SEARCH_BY: search_type
          },
          cache: false,
          dataType: 'json',
          success: function(dataResult){
      
            $('#' + LEVELDIV ).html(dataResult.level); 	
            $('#' + VALUEDIV ).html(dataResult.value); 	
            $('select').selectpicker();
      
          }
        });
      
      
      }
      
function sales_record_report(Details){
  var check_status =document.getElementById(Details).checked;

  var report_wise_code = $('#report_wise_code').val();

  var EXTRAFILED = $('#'+ report_wise_code).val();
  if( report_wise_code == undefined ){

  }else{
    if( EXTRAFILED == null ){
      alert('Please Select Related Data');
      return false; 
    }
  }

    if(check_status === false){
      reportType = 'Sales Record';
    }else{
      reportType = 'Sales Record With Details';
    }
  
   generateReport(reportType,report_wise_code)
  
  



}



function PRODUCT_MOVEMENT_REPORT(REPORTNAME){

  document.getElementById("search_data").disabled = true;            

  var product_id = $('#product_id').val();
  var report_type = $('#report_type').val();
  var report_wise_code = $('#report_wise_code').val();
  var date_from = $('#date_from').val();
  var date_to = $('#date_to').val();


  if (checkValue('isEmpty', product_id,'Product Name')) { $("#search_data").removeAttr("disabled");return false; }

  if(report_type != ''){
    var related_id = $('#'+report_wise_code).val();

    if (checkValue('isEmpty', related_id,'' + report_type + ' Name')) { $("#search_data").removeAttr("disabled");return false; }
  }else{
    var related_id = '';

  }
  

  var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
  $('#laod_report').html(spinner); 	


  $.ajax({
    url: "generate_report.php",
    type: "POST",
    data: {
  
      action: REPORTNAME,
      date_from: date_from,
      date_to: date_to,
      report_type:report_type,
      product_id: product_id,
      related_id: related_id
    },
    cache: false,
    success: function(html){
     document.getElementById("laod_report").innerHTML = html;
     document.getElementById("search_data").disabled = false;            
     $('#MSalary').DataTable();

    }

  });

}

 var start = 1;
 var is_running = false;



function SHORT_STOCK_REPORT(){
  start = 1;
  document.getElementById('laod_stock_report').innerHTML = '';

  SHORT_STOCK_DATA();
  

}





function modifyreport(TYPE){

var FILED = $('#report_wise_code').val();


 generateReport(TYPE,FILED);
 
}



function SHORT_STOCK_DATA(){


  var report_type = $('#report_type').val();


  
  if(report_type == 'FG-Category'  ){
    var FILED = $('#category_id').val();
  }else if (report_type == 'Finished-Goods' ){
    var FILED = $('#product_id').val();
  }else{
    var FILED = '';
  }


  is_running = true;
  $("#laoding").show();

  jQuery.ajax({
    
    url: 'generate_report.php',
    data: {
      start: start,
      report_type: report_type,
      EXTRAFILED: FILED,
      action: 'Product-Hot-List'
    },
    type: "POST",
    
    success:function(result){  
     
      split = result.split('_SAJID_');
      if(split[1] == 0 ){
        $("#laoding").hide();

      }else{
        $("#laod_stock_report").append(split[0]);
        $("#laoding").hide();
        is_running = false;
        start++;
    
      }

    }

  });
}

/*
jQuery(window).scroll(function(){

  if(jQuery(window).scrollTop() >= jQuery(document).height() -  jQuery(window).height() ){
    if(!is_running){ SHORT_STOCK_DATA() ;}

  
  }

});

*/ 

function exportToExcel(FILENAME,TABLEID) {
  var tableData = $('#'+TABLEID).DataTable().data();
  var theadContent = $("#"+TABLEID + " thead").html();
  var printContent = "";
  for (var i = 0; i < tableData.length; i++) {
    printContent += "<tr>";
    for (var key in tableData[i]) {
      printContent += "<td>" + tableData[i][key] + "</td>";
    }
    printContent += "</tr>";
  }

  // create a new table element and populate it with the data to export
  var exportTable = $("<table>").append("<thead>" + theadContent + "</thead>").append("<tbody>" + printContent + "</tbody>");

  var wb = XLSX.utils.table_to_book(exportTable[0], {sheet:"Sheet JS"});
  XLSX.writeFile(wb, FILENAME+'.xlsx');
}


// Helper function to convert string to ArrayBuffer
function s2ab(s) {
  var buf = new ArrayBuffer(s.length);
  var view = new Uint8Array(buf);
  for (var i = 0; i < s.length; i++) {
    view[i] = s.charCodeAt(i) & 0xFF;
  }
  return buf;
}

  


function STOCK_REPORT(){





  var report_type = $('#report_type').val();
  var section = $('#section').val();
  var date_to = $('#date_to').val();

  if(section == 'FG-STOCK' ){
    action = 'Finished Goods Stock Report';
  }else if (section == 'RAW-STOCK'){
    action = 'Raw Goods Stock Report';
  }else{
    action = '';

  }
  

  if(date_to == '' ){
   
    alert('date is empty');
    return false ;
  }
  if(report_type == 'FG-Category' || report_type == 'Raw-Category' ){
    var FILED = $('#category_id').val();

    
  }else if (report_type == 'Finished-Goods' || report_type == 'Raw-Material'){
    var FILED = $('#product_id').val();


  }else if (report_type == 'Multipal-Warehouse-Wise'){
    var FILED = $('#warehouse_id').val();

  }else{
    var FILED = '';
  }

  var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
  $('#laod_report').html(spinner); 	

  jQuery.ajax({
    
    url: 'generate_report.php',
    data: {
      start: start,
      report_type: report_type,
      EXTRAFILED: FILED,
      date_to: date_to,
      action: action,
    },
    type: "POST",
    
    success:function(result){  
      document.getElementById("laod_report").innerHTML = result;
      $('#MSalary').DataTable();
                        
    }

  });
}





function OpeningStockReport(){


  var date_to = $('#date_to').val();
  var date_from = $('#date_from').val();

  var report_type = $('#report_type').val();
   

  
  
  if(report_type == ''){
   
    alert('Select report type');
    return false ;
  }

 var warehouse_id = $('#warehouse_id').val();
 
  if(date_to == '' || date_from == ''){
   
    alert('date is empty');
    return false ;
  }

  var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
  $('#laod_report').html(spinner); 	

  jQuery.ajax({
    
    url: 'generate_report.php',
    data: {
      date_to: date_to,  
      date_from: date_from,
      report_type: report_type,
      warehouse_id: warehouse_id,
      action: 'Opening Stock Report',
    },
    type: "POST",
    
    success:function(result){  
      document.getElementById("laod_report").innerHTML = result;
      $('#example').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                lengthMenu: [10, 20, 50, 100], // Dropdown options for page length
                pageLength: 2000 // Default number of rows per page
            });
                        
    }

  });
}




function purchase_record_report(Details,Section){
  var check_status =document.getElementById(Details).checked;

  var report_wise_code = $('#report_wise_code').val();

  if(Section == 'FG'){

    if(check_status === false){
      reportType = 'Finishied Goods Purchase Record';
    }else{
      reportType = 'Finishied Goods Purchase Record With Details';
    }


  }else if (Section == 'RAW'){

    if(check_status === false){
      reportType = 'Raw Goods Purchase Record';
    }else{
      reportType = 'Raw Goods Purchase Record With Details';
    }

  }else{

  }


  generateReport(reportType,report_wise_code)



}


function SectionWiseReport(MODULE){


  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
  
      action: 'Module_wise_report',
      MODULE: MODULE
    },
    cache: false,
    success: function(html){

     document.getElementById("reportList").innerHTML = html;

    }

  });


}


function serachreport(){

  var search_report = $('#search_report').val();

  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
  
      action: 'Search_Report',
      search_report: search_report
    },
    cache: false,
    success: function(html){

     document.getElementById("reportList").innerHTML = html;

    }

  });


}


function selectAllOptions(ID) {
  var select = document.getElementById(ID);
  for(var i=0; i<select.options.length; i++) {
    select.options[i].selected = true;
  }
  select.setAttribute('data-selected-text-format', 'count');
  $(select).selectpicker('refresh');
}



function unselectAllOptions(ID) {
  var select = document.getElementById(ID);
  for(var i=0; i<select.options.length; i++) {
    select.options[i].selected = false;
  }
  select.setAttribute('data-selected-text-format', 'count>2');
  $(select).selectpicker('refresh');
}



function updateSaftystock(ID,SECTION){

  var safty_stock = $('#update_safty_stock').val();



  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
  
      action: 'update_safty_stock',
      ID: ID,
      SECTION: SECTION,
      safty_stock:safty_stock

    },
    cache: false,
    success: function(html){
alert(html);
      modifyreport('Short-List',''+SECTION+'');

    }

  });



}


function CommonReportGenerator(REPORTNAME){

  var date_from = $('#date_from').val();
  var date_to = $('#date_to').val();
  var report_type = $('#report_type').val();
  var report_wise_code = $('#report_wise_code').val();
  var branch_id = $('#branch_id').val();

  var related_id = $('#'+ report_wise_code).val();
  if(report_type == '' ){ alert('Please select a report type');return false; }
      if(related_id == '' ){ alert('Please select a customer');return false; }

  var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
  $('#laod_report').html(spinner); 	

  document.getElementById("search_data").disabled = true;            

  $.ajax({
    url: "generate_report.php",
    type: "POST",
    data: {
  
      action: REPORTNAME,
      date_from: date_from,
      date_to: date_to,
      related_id:related_id,
      branch_id: branch_id,
      report_type: report_type
    },
    cache: false,
    success: function(html){

     document.getElementById("laod_report").innerHTML = html;

           $('#example').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                lengthMenu: [10, 20, 50, 100], // Dropdown options for page length
                pageLength: 2000 // Default number of rows per page
            });



     document.getElementById("search_data").disabled = false;            
     $('#MSalary').DataTable();

    }
  });
}






function deleteFullDemand(id){
  
  var x = window.confirm("Are you sure to delete this data?");
  if(x){
    $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        id: id,
        action: 'delete_Full_Demand'
      },
      cache: false,
      success: function(dataResult){
        
        alert(dataResult);
        generateReport('Demand Record','');
      }

    });
}



}



function deleteDemand(id){
  
  var x = window.confirm("Are you sure to delete this data?");
  if(x){
    $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        id: id,
        action: 'delete_demand'
      },
      cache: false,
      success: function(dataResult){
        
        alert(dataResult);
        generateReport('Demand Record','');
      }

    });
}



}


function BankTrReport(REPORTNAME,EXTRAFILED){


  document.getElementById("search_data").disabled = true;            
  var date_from = $('#date_from').val();
  var date_to = $('#date_to').val();
  var report_type = $('#report_type').val();
  var EXTRAFILED = $('#'+ EXTRAFILED).val();
  var brunch_id = $('#brunch_id').val();

  var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
  $('#laod_report').html(spinner); 	
  
  $.ajax({
    url: "generate_report.php",
    type: "POST",
    data: {
  
      action: REPORTNAME,
      date_from: date_from,
      date_to: date_to,
      report_type:report_type,
      EXTRAFILED: EXTRAFILED,
      brunch_id: brunch_id
    },
    cache: false,
    success: function(html){



     document.getElementById("laod_report").innerHTML = html;

          $('#example').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                lengthMenu: [10, 20, 50, 100], // Dropdown options for page length
                pageLength: 2000 // Default number of rows per page
            });

     document.getElementById("search_data").disabled = false;            
   

    }

  });



      
}




function generateReport(REPORTNAME,EXTRAFILED){


  document.getElementById("search_data").disabled = true;            
  var date_from = $('#date_from').val();
  var date_to = $('#date_to').val();
  var report_type = $('#report_type').val();
  var EXTRAFILED = $('#'+ EXTRAFILED).val();


  
  var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
  $('#laod_report').html(spinner); 	
  
  $.ajax({
    url: "generate_report.php",
    type: "POST",
    data: {
  
      action: REPORTNAME,
      date_from: date_from,
      date_to: date_to,
      report_type:report_type,
      EXTRAFILED: EXTRAFILED
    },
    cache: false,
    success: function(html){



     document.getElementById("laod_report").innerHTML = html;

          $('#example').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                lengthMenu: [10, 20, 50, 100], // Dropdown options for page length
                pageLength: 2000 // Default number of rows per page
            });

     document.getElementById("search_data").disabled = false;            
   


        if(REPORTNAME   == 'Day Book Report'){
                $(".panel-collapse").click(function() {
      $(this).closest(".panel").find(".panel-body").collapse("toggle");
    });
    $(".panel-body").addClass("collapse");
        }


    }

  });



      
}


function printButtn(NAME,TABLEID){

  var report_type = $('#report_type').val();
  var date_from = $('#date_from').val();
  var date_to = $('#date_to').val();
  if(date_from == '' || date_from == 'undefined' ){ var date1 =  '';  }else{ var date1 = 'From: ' + date_from ;}
  if(date_to == '' || date_to == 'undefined' ){ var date2 =  ''; }else{ var date2 = 'To: ' + date_to ; }

  var title_company_name = $('#title_company_name').val();

  var tableData = $('#'+TABLEID).DataTable().data();
  var theadContent = $("#"+TABLEID + " thead").html();
  var printContent = "";
  for (var i = 0; i < tableData.length; i++) {
    printContent += "<tr>";
    for (var key in tableData[i]) {
      printContent += "<td>" + tableData[i][key] + "</td>";
    }
    printContent += "</tr>";
  }


  var printWindow = window.open('', '', 'height=800,width=1200');
  printWindow.document.write('<html><head><style>a {text-decoration: none;}table{width: 100%;border-spacing: 0px;} th{ border: 1px solid black; } td{border: 1px solid black;}</style></head>');
  printWindow.document.write('<body><table><tr><th style="text-align:center">'+title_company_name+'</th></tr><tr><th style="text-align:center">'+report_type+' '+NAME+'</th></tr><tr><th style="text-align:center">'+date1+'  '+date2+'</th></tr></table><table><thead>' + theadContent + '</thead>' + printContent + '</table></body></html>');
  printWindow.document.close();
  printWindow.focus();
  printWindow.print();
  // printWindow.close();

}


function SimplePrint(){

  print();
}


function printButtn2(NAME){

  var report_type = $('#report_type').val();
  var date_from = $('#date_from').val();
  var date_to = $('#date_to').val();

  if(date_from != '' ){ var date1 = 'From: ' + date_from ; }else{ var date1 =  '';}
  if(date_to != '' ){ var date2 = 'To: ' + date_to ; }else{ var date2 =  '';}
  var title_company_name = $('#title_company_name').val();
  var tableData = $('#MSalary2 tr').map(function() {
    return [$('td', this).map(function() {
        return $(this).text();
    }).get()];
}).get();

  var theadContent = $("#MSalary2 thead").html();
  var printContent = "";
  for (var i = 0; i < tableData.length; i++) {
    printContent += "<tr>";
    for (var key in tableData[i]) {
      printContent += "<td>" + tableData[i][key] + "</td>";
    }
    printContent += "</tr>";
  }

  var printWindow = window.open('', '', 'height=800,width=1200');
  printWindow.document.write('<html><head><style>a {text-decoration: none;}table{width: 100%;border-spacing: 0px;} th{ border: 1px solid black; } td{border: 1px solid black;}</style></head>');
  printWindow.document.write('<body><table><tr><th style="text-align:center">'+title_company_name+'</th></tr><tr><th style="text-align:center">'+report_type+' '+NAME+'</th></tr><tr><th style="text-align:center">'+date1+'  '+date2+'</th></tr></table><table><thead>' + theadContent + '</thead>' + printContent + '</table></body></html>');
  printWindow.document.close();
  printWindow.focus();
  printWindow.print();
  //printWindow.close();

}



        function importAttandanceCsv(){

          var file_data = $('#fileToUpload').prop('files')[0];
          
          var form_data = new FormData();
            
            form_data.append('fileToUpload', file_data);
            
         

            document.getElementById("load_msg").innerHTML = '<img style="height:200px;" src="img/data_import.gif">';
            
            $.ajax({
                
            url: "bulk_attandance_import_action.php",
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
           
              if (data > 1) {
            
                document.getElementById("load_msg").innerHTML = '<font color=green>Uplaod Success</font>';
                makeAttandanceFromCSV();

                
              }else {

                document.getElementById("load_msg").innerHTML = '<font color=red>'+data+'</font>';

               }
              
            }
            });

        }


function makeAttandanceFromCSV(){


  
  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {

      action: 'MAKE_ATTANDANCE_FROM_FROM_CSV'
    },
    cache: false,
    success: function(html){

   document.getElementById("take_att").click();

    }

  });

}



        function importCsv(){

          var file_data = $('#fileToUpload').prop('files')[0];
          
          var form_data = new FormData();
            
            form_data.append('fileToUpload', file_data);
            
         

            document.getElementById("load_msg").innerHTML = '<img style="height:200px;" src="img/data_import.gif">';
            
            $.ajax({
                
            url: "bulk_import_action.php",
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
           
              if (data > 1) {
            
                document.getElementById("load_msg").innerHTML = '<font color=green>Uplaod Success</font>';
                $("#refresh_cart2").load('cart_request_raw_material_batch.php');
                document.getElementById("for_bulk_uplaod").innerHTML = '';
                document.getElementById("final_bulk_upload").value = 'Yes';

                
              }else {

                document.getElementById("load_msg").innerHTML = '<font color=red>'+data+'</font>';

               }
              
            }
            });

        }


function final_pending_demand(){



  var invoice_code = $('#invoice_code').val();
  var pi_no = $('#pi_no').val();
  var accepting_delivery_date = $('#accepting_delivery_date').val();
  var poino = $('#poino').val();
  var total_added_item = $('#total_added_item').val();

  if(poino === ''){

    
  if (pi_no ==  '') { alert("Give a PI No"); $('#pi_no').focus(); return false; }
  if (accepting_delivery_date ==  '') { alert("Give a date"); $('#accepting_delivery_date').focus(); return false; }


  }
  if (total_added_item ==  0) {alert("No Item Added"); $('#pi_no').focus(); return false; }
  var x = window.confirm("Are you sure to Final Invocie?");
  if(x){
  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
  
      action: 'FINAL_RECIPE_WISE_DEMAND',
      invoice_code: invoice_code
    },
    cache: false,
    success: function(html){
      alert(html);     
      $("#refresh_cart2").load('cart_request_raw_material_batch.php');
      window.open('Production/Manufacture-Product/New');
    }

  });
  }
  
}


function final_pending_print_demand(){

  var invoice_code = $('#invoice_code').val();
  var send_to = $('#send_to').val();
  var send_to_id = $('#send_to_id').val();
  var note = $('#note').val();
  var accepting_delivery_date = $('#accepting_delivery_date').val();




  if (send_to ==  '') { alert("Select a Type"); $('#send_to').focus(); return false; }
  if (send_to_id ==  '') { alert("Select a supplier or factory"); $('#send_to_id').focus(); return false; }
  if (accepting_delivery_date ==  '') { alert("Give a date"); $('#accepting_delivery_date').focus(); return false; }




  var x = window.confirm("Are you sure to Final Print?");
  if(x){
  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
  
      action: 'FINAL_RECIPE_WISE_PRINT',
      invoice_code: invoice_code,
      note: note,
      send_to: send_to,
      send_to_id: send_to_id,
      accepting_delivery_date: accepting_delivery_date



    },
    dataType: 'json',
    cache: false,
    success: function(dataResult){
      alert(dataResult.mess);     
      window.open("print.php?print=Report Send For Print&code=" +dataResult.code );
      window.location.replace("Production/Send-For-Print/New");
    }
  });
  

}
}

function final_pending_spray_demand(){



  var invoice_code = $('#invoice_code').val();
  var send_to = $('#send_to').val();
  var send_to_id = $('#send_to_id').val();
  var note = $('#note').val();
  var accepting_delivery_date = $('#accepting_delivery_date').val();




  if (send_to ==  '') { alert("Select a Type"); $('#send_to').focus(); return false; }
  if (send_to_id ==  '') { alert("Select a supplier or factory"); $('#send_to_id').focus(); return false; }
  if (accepting_delivery_date ==  '') { alert("Give a date"); $('#accepting_delivery_date').focus(); return false; }




  var x = window.confirm("Are you sure to Final Spray?");
  if(x){
  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
  
      action: 'FINAL_RECIPE_WISE_SPRAY',
      invoice_code: invoice_code,
      note: note,
      send_to: send_to,
      send_to_id: send_to_id,
      accepting_delivery_date: accepting_delivery_date



    },
    dataType: 'json',
    cache: false,
    success: function(dataResult){
      alert(dataResult.mess);     
      window.open("print.php?print=Report Send For Spray&code=" +dataResult.code );
      window.location.replace("Production/Send-For-Spray/New");
    }
  });
  }


}



function final_pending_mold_demand(){


  var invoice_code = $('#invoice_code').val();
  var molding_type = $('#molding_type').val();
  var send_to_id = $('#send_to_id').val();
  var note = $('#note').val();
  var accepting_delivery_date = $('#accepting_delivery_date').val();
  var total_added_item = $('#total_added_item').val();




  if (molding_type ==  '') { alert("Select a molding From"); $('#molding_type').focus(); return false; }
  if (send_to_id ==  '') { alert("Select a supplier or factory"); $('#send_to_id').focus(); return false; }
  if (accepting_delivery_date ==  '') { alert("Give a date"); $('#accepting_delivery_date').focus(); return false; }

  if (total_added_item ==  0) {alert("No Item Added"); $('#molding_type').focus(); return false; }



  var x = window.confirm("Are you sure to Final Mold?");
  if(x){
  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
  
      action: 'FINAL_RECIPE_WISE_MOLD',
      invoice_code: invoice_code,
      note: note,
      molding_type: molding_type,
      send_to_id: send_to_id,
      accepting_delivery_date: accepting_delivery_date



    },
    dataType: 'json',
    cache: false,
    success: function(dataResult){
      alert(dataResult.mess);     
      window.open("print.php?print=Report Send For Molding&code=" +dataResult.code );
      window.location.replace("Production/Send-For-Molding/New");
    }

  });
  }
  
}


function convertQuotation(){

  

  var x = window.confirm("Are you sure Convert to Invoice?");
  if(x){

    cart = [];

var checksomthing = $('input[type=checkbox]:checked').map(function(){
    return this.value;
}).get().join(",");

var main_invoice_id = $('#main_invoice_id').val();
var dispatch_from_which_brunch = $('#dispatch_from_which_brunch').val();


var str_array = checksomthing.split(',');
var count_item =  str_array.length-1;
if (count_item <  1) { alert("No Item Added"); return false;  }



for(var i = 1; i <= count_item; i++) {

  var element = {};
  var total_demand = getNum($('#total_demand'+str_array[i]).val());
  var product_id =  $('#product_id'+str_array[i]).val();
  var quotation_id =  $('#quotation_id'+str_array[i]).val();
  var stock =  getNum($('#total_stock'+str_array[i]).val());
  var recommended_price =  getNum($('#recommended_price'+str_array[i]).val());


  if(+total_demand > +stock ){
    alert('Not Enough in stockasas');
    return false;  

}else{
    element.product_id = product_id;
    element.total_demand = total_demand;
    element.quotation_id = quotation_id;
    element.stock = stock;
    element.recommended_price = recommended_price;
  
    cart.push({element: element});

}



}

let data_cart = JSON.parse(JSON.stringify(cart));

for (let i = 0; i < data_cart.length; i++) {
  let element = data_cart[i].element;
  if (element.total_demand === 0 && element.stock === 0 && element.recommended_price === 0 ) {
    delete data_cart[i];
  }
}

data_cart = data_cart.filter(item => item !== null);





$.ajax({
  url: "form_action.php",
  type: "POST",
  data: {
    action: 'quotation_action_convert_to_invoice',
    all_data: JSON.stringify(data_cart),
    count_item: count_item,
    main_invoice_id: main_invoice_id,
    dispatch_from_which_brunch: dispatch_from_which_brunch
    

    
  },
  cache: false,
  success: function(dataResult){

      alert(dataResult);
    location.reload();
    


   
  }
});




  }


}


function convertPreOrderInvoice(){

  var x = window.confirm("Are you sure Convert to Invoice?");
  if(x){

    cart = [];

var checksomthing = $('input[type=checkbox]:checked').map(function(){
    return this.value;
}).get().join(",");

var main_invoice_id = $('#main_invoice_id').val();
var dispatch_from_which_brunch = $('#dispatch_from_which_brunch').val();

var str_array = checksomthing.split(',');
var count_item =  str_array.length-1;
if (count_item <  1) { alert("No Item Added"); return false;  }


for(var i = 1; i <= count_item; i++) {

  var element = {};
  var total_demand = getNum($('#total_demand'+str_array[i]).val());
  var product_id =  $('#product_id'+str_array[i]).val();
  var pre_order_item_id =  $('#pre_order_item_id'+str_array[i]).val();
  var stock =  getNum($('#total_stock'+str_array[i]).val());
  var recommended_price =  getNum($('#recommended_price'+str_array[i]).val());


    if(+total_demand > +stock ){
      alert('Not Enough in stockasas');
      return false;  
  
  }else{
    element.product_id = product_id;
    element.total_demand = total_demand;
    element.pre_order_item_id = pre_order_item_id;
    element.stock = stock;
    element.recommended_price = recommended_price;
  
    cart.push({element: element});
  
  }
  




}


let data_cart = JSON.parse(JSON.stringify(cart));

for (let i = 0; i < data_cart.length; i++) {
  let element = data_cart[i].element;
  if (element.total_demand === 0 && element.stock === 0 && element.recommended_price === 0 ) {
    delete data_cart[i];
  }
}

data_cart = data_cart.filter(item => item !== null);




$.ajax({
  url: "form_action.php",
  type: "POST",
  data: {
    action: 'action_convert_to_invoice',
    all_data: JSON.stringify(data_cart),
    count_item: count_item,
    dispatch_from_which_brunch: dispatch_from_which_brunch,
    main_invoice_id: main_invoice_id
    
  },
  cache: false,
  success: function(dataResult){

      alert(dataResult);
    location.reload();
    


   
  }
});




  }

}

function toggleCheckboxes() {
  var checkboxes = document.getElementsByClassName('icheckbox');
  for (var i = 0; i < checkboxes.length; i++) {
    checkboxes[i].checked = document.getElementById('select-all').checked;
  }
}

function edit_purches(id,database,supplier_id,note,product_id,supplier_bill_no,supplier_bill_date,invoice_no,date,price,qty){


  $('#local_related_id').val(id);
  if(supplier_id == ''){}else{  $("#supplier_id").val(supplier_id).change(); }
 
  $('#purches_type').val(database);
  if(supplier_bill_date == '' ){}else{$('#supplier_bill_date').val(supplier_bill_date);}
  
  if(note == ''){}else{  $('#note').val(note);}

  $("#material_id").val(product_id).change();
  $('#purches_price').val(price);
  $('#quantity').val(qty);
  if(supplier_bill_no == '' ){}else{   $('#supplier_bill_no').val(supplier_bill_no); }

  $('#invoice').val(invoice_no);
  $('#invoice_date').val(date);
  document.getElementById("purches_type").disabled = true;            



} 



function search_related(WHAT,WHERE,DIV){

  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      action: 'search_task',
      WHAT: WHAT,
      WHERE: WHERE
      
    },
    cache: false,
    success: function(dataResult){
     
      document.getElementById(DIV).innerHTML = dataResult;            

    }
  });


}

function add_cart_demand() {
    $("#add_cart").attr("disabled", true);

    let related_id = $('#related_id').val().trim();
    let invoice_id = $('#invoice_id').val().trim();
    let product_id = $('#product_id').val().trim();
    let quantity = $('#quantity').val().trim();
    let spinner = "<img src='../img/owl/AjaxLoader.gif' alt='loading...' />";

    if (!product_id || !quantity) {
        alert('Please fill all the fields!');
        $("#add_cart").removeAttr("disabled");
        return;
    }

    $.ajax({
        url: "form_action.php",
        type: "POST",
        data: {
            product_id,
            invoice_id,
            quantity,
            related_id,
            action: 'add_cart_demand'
        },
        cache: false,
        success: function (response) {
            if (response === 'Added to cart successfully') {
                $("#mess_box").html('<strong style="color:green">' + response + '</strong>');
                $('#quantity').val('');
                
                updateCart(related_id, invoice_id, spinner);

                setTimeout(() => { $("#mess_box").html(''); }, 2000);
            } else {
                alert(response);
                $('#quantity').val('');
                updateCart(related_id, invoice_id, spinner);
            }

            $("#add_cart").removeAttr("disabled");
        }
    });
}

function updateCart(related_id, invoice_id, spinner) {
    $("#refresh_cart").html(spinner).load(`demand-cart.php?edit&related_id=${related_id}&invoice_id=${invoice_id}`);
}


  function final_demand() {


  $("#fianl_data").attr("disabled", "disabled");



  var brunch_id = $('#brunch_id').val();
  var notes = $('#notes').val();

  var count_cart = $('#count_cart').val();



  if(count_cart > 0  &&   brunch_id != ""  ){
    $.ajax({
      url: "form_action.php",
      type: "POST",
      data: {

        brunch_id: brunch_id,
        notes: notes,
        action: 'final_demand'
      },
      cache: false,
      success: function(dataResult){
        alert(dataResult);
          $("#fianl_data").removeAttr("disabled");
          window.location.replace("sales/Create-Demand/New");
      }
    });
    }
    else{
      alert('Please add product and fill all the field !');
      $("#fianl_data").removeAttr("disabled");
    }
  }





function done_receive_raw_local_purches(){
  

  var code = $('#code').val();


  var x = window.confirm("Are you sure to done with this invoice?");

  if(x){


  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      action: 'done_receive_raw_local_purches',
      code: code
      
    },
    cache: false,
    success: function(dataResult){
        alert(dataResult);
        location.reload();
    }
  });
}


}


function update_receive_raw_local_purches(){

  var total_item = $('#total_item').val();
  var code = $('#code').val();

  var receive_now = $("[name^='receive_now']").map(function() { return $(this).val() }).get();
  var product_id = $("[name^='product_id']").map(function() { return $(this).val() }).get();
  var supplier_id = $("[name^='supplier_id']").map(function() { return $(this).val() }).get();
  var warehouse_id = $("[name^='warehouse_id']").map(function() { return $(this).val() }).get();

  if (total_item ==  0) {
  alert("No Item Added");
  return false;
  }
  for (var no = 1; no <= total_item; no++) {
   
    var tds = document.getElementsByClassName("tr" + no );
    for(var i = 0, j = tds.length; i < j; ++i)
   


    if ($.trim($('#receive_now' + no).val()).length == 0   ) {
      tds[i].style.color = "red";
      alert("can not empty");
      $('#receive_now' + no).focus();
      return false;
    }else {

      tds[i].style.color = "green";
      if( $.trim($('#receive_now' + no).val()) < 0 ){
          tds[i].style.color = "red";
        alert("Please Enter possitive number");
        $('#receive_now' + no).focus();
        return false;

      }else{
          tds[i].style.color = "red";
        if( (Number($.trim($('#actual_quantity' + no).val()))  < (Number($.trim($('#receive_now' + no).val()))) + Number($.trim($('#actual_receive' + no).val())))   ) {
          alert("Cant receive more then Batch Qty");
          $('#receive_now' + no).focus();
          return false;
      }

      if( (Number($.trim($('#receive_now' + no).val()))  >  Number($.trim($('#actual_quantity' + no).val())))  ) {
          tds[i].style.color = "red";
        alert("Cant receive more then Batch Qty");
        $('#receive_now' + no).focus();
        return false;
    }

    if( $.trim($('#receive_now' + no).val()) > 0 && $.trim($('#warehouse_id' + no).val()) == '' ){

      tds[i].style.color = "red";
      alert("Select a warehouse");
      $('#warehouse_id' + no).focus();
      return false;

    }

    

      }



  }
   
  
}

$('#action_bar').html('Working on it..... '); 	


$.ajax({
  url: "form_action.php",
  type: "POST",
  data: {
    total_item: total_item,
    receive_now: receive_now,
    action: 'update_receive_raw_local_purches',
    warehouse_id: warehouse_id,
    product_id: product_id,
    supplier_id: supplier_id,
    code: code
  },
  cache: false,
  success: function(dataResult){

  alert(dataResult);     
  location.reload();
  
  }
});

}


function update_return_raw_local_purches(){

  var total_item = $('#total_item').val();
  var code = $('#code').val();

  var reject_now = $("[name^='reject_now']").map(function() { return $(this).val() }).get();
  var product_id = $("[name^='product_id']").map(function() { return $(this).val() }).get();
  var supplier_id = $("[name^='supplier_id']").map(function() { return $(this).val() }).get();
  var warehouse_id = $("[name^='warehouse_id']").map(function() { return $(this).val() }).get();






  if (total_item ==  0) {
  alert("No Item Added");
  return false;
  }
  for (var no = 1; no <= total_item; no++) {
   
    var tds = document.getElementsByClassName("tr" + no );
    for(var i = 0, j = tds.length; i < j; ++i)
   


    if ( $.trim($('#reject_now' + no).val()).length == 0 ) {
     
      tds[i].style.color = "red";
      alert("can not empty");
      $('#reject_now' + no).focus();
      return false;
    }else {
     
      tds[i].style.color = "green";
      if(  $.trim($('#reject_now' + no).val()) < 0 ){
       
        tds[i].style.color = "red";
        alert("Please Enter possitive number");
        $('#reject_now' + no).focus();
        return false;

      }else{
        
        tds[i].style.color = "green";
        if(  Number($.trim($('#reject_now' + no).val()))  >  Number($.trim($('#actual_quantity' + no).val()))  ) {
          tds[i].style.color = "red";
          alert("Receive Qty can not more then Ordr Qty");
          $('#reject_now' + no).focus();
          return false;
      }
      }


    }
   
}

$('#action_bar').html('Working on it..... '); 	


$.ajax({
  url: "form_action.php",
  type: "POST",
  data: {
    total_item: total_item,
    action: 'update_return_raw_local_purches',
    reject_now: reject_now,
    product_id: product_id,
    supplier_id: supplier_id,
    code: code,
    warehouse_id: warehouse_id
  },
  cache: false,
  success: function(dataResult){

  alert(dataResult);     
  location.reload();
  
  }
});

}


function done_receive_fg_local_purches(){
  

  var code = $('#code').val();


  var x = window.confirm("Are you sure to done with this invoice?");

  if(x){


  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      action: 'done_receive_fg_local_purches',
      code: code
      
    },
    cache: false,
    success: function(dataResult){
        alert(dataResult);
        location.reload();
    }
  });
}


}


function update_receive_fg_local_purches(){

  var total_item = $('#total_item').val();
  var code = $('#code').val();

  var receive_now = $("[name^='receive_now']").map(function() { return $(this).val() }).get();
  var product_id = $("[name^='product_id']").map(function() { return $(this).val() }).get();
  var supplier_id = $("[name^='supplier_id']").map(function() { return $(this).val() }).get();
  var warehouse_id = $("[name^='warehouse_id']").map(function() { return $(this).val() }).get();



  if (total_item ==  0) {
  alert("No Item Added");
  return false;
  }
  for (var no = 1; no <= total_item; no++) {
   
    var tds = document.getElementsByClassName("tr" + no );
    for(var i = 0, j = tds.length; i < j; ++i)
   


    if ($.trim($('#receive_now' + no).val()).length == 0 ) {
     
      tds[i].style.color = "red";
      alert("can not empty");
      $('#receive_now' + no).focus();
      return false;
    }else {
     
      tds[i].style.color = "green";
      if( $.trim($('#receive_now' + no).val()) < 0 ){
       
        tds[i].style.color = "red";
        alert("Please Enter possitive number");
        $('#receive_now' + no).focus();
        return false;

      }else{
        
        tds[i].style.color = "green";
        if(  Number($.trim($('#receive_now' + no).val()))  >  Number($.trim($('#actual_quantity' + no).val()))  ) {
          tds[i].style.color = "red";
          alert("Receive Qty can not more then Ordr Qty");
          $('#receive_now' + no).focus();
          return false;
      }
      }


    }
   
}

$('#action_bar').html('Working on it..... '); 	


$.ajax({
  url: "form_action.php",
  type: "POST",
  data: {
    total_item: total_item,
    receive_now: receive_now,
    action: 'update_receive_fg_local_purches',
    product_id: product_id,
    supplier_id: supplier_id,
    code: code,
    warehouse_id: warehouse_id
  },
  cache: false,
  success: function(dataResult){

  alert(dataResult);     
  location.reload();
  
  }
});

}


function update_return_fg_local_purches(){

  var total_item = $('#total_item').val();
  var code = $('#code').val();

  var return_now = $("[name^='return_now']").map(function() { return $(this).val() }).get();
  var product_id = $("[name^='product_id']").map(function() { return $(this).val() }).get();
  var supplier_id = $("[name^='supplier_id']").map(function() { return $(this).val() }).get();

  var warehouse_id = $("[name^='warehouse_id']").map(function() { return $(this).val() }).get();


  if (total_item ==  0) {
  alert("No Item Added");
  return false;
  }
  for (var no = 1; no <= total_item; no++) {
   
    if ($.trim($('#return_now' + no).val()).length == 0 || $.trim($('#return_now' + no).val()) < 0) {
      alert("Enter Possitive number");
      $('#return_now' + no).focus();
      return false;
    }

  if(  Number($.trim($('#return_now' + no).val()))  >  Number($.trim($('#total_receive' + no).val()))  ) {
    alert("Return Qty can not more then Receive Qty");
    $('#return_now' + no).focus();
    return false;
}


  if ($.trim($('#warehouse_id' + no).val()) == '') {
    alert("Select a warehouse");
    $('#warehouse_id' + no).focus();
    return false;
  }

  if ($.trim($('#warehouse_id' + no).val()) == '') {
    alert("Select a warehouse");
    $('#warehouse_id' + no).focus();
    return false;
  }

}

$('#action_bar').html('Working on it..... '); 	


$.ajax({
  url: "form_action.php",
  type: "POST",
  data: {
    total_item: total_item,
    return_now: return_now,
    action: 'update_return_fg_local_purches',
    product_id: product_id,
    supplier_id: supplier_id,
    code: code,
    warehouse_id: warehouse_id

  },
  cache: false,
  success: function(dataResult){

  alert(dataResult);     
  location.reload();
  
  }
});

}

  

        function receive_after_batch_done(){
      
          var total_item = $('#total_item').val();
          var code = $('#code').val();
          var send_to_id = $('#send_to_id').val();
          var send_to = $('#send_to').val();


          var receive_now = $("[name^='receive_now']").map(function() { return $(this).val() }).get();
          var product_id = $("[name^='product_id']").map(function() { return $(this).val() }).get();
          var warehouse_id = $("[name^='warehouse_id']").map(function() { return $(this).val() }).get();

          if (total_item ==  0) { alert("No Item Added");return false;}
  
          for (var no = 1; no <= total_item; no++) {
 
              if ($.trim($('#receive_now' + no).val()).length == 0   ) {
                alert("can not empty");
                $('#receive_now' + no).focus();
                return false;
              }else {
  
                if( $.trim($('#receive_now' + no).val()) < 0 ){
  
                  alert("Please Enter possitive number");
                  $('#receive_now' + no).focus();
                  return false;
    
                }else{
                  if( (Number($.trim($('#total_batch_qty' + no).val()))  < (Number($.trim($('#receive_now' + no).val()))) + Number($.trim($('#actual_receive' + no).val())))   ) {
                    alert("Cant receive more then Batch Qty");
                    $('#receive_now' + no).focus();
                    return false;
                }
  
                if( (Number($.trim($('#receive_now' + no).val()))  >  Number($.trim($('#total_batch_qty' + no).val())))  ) {
                  alert("Cant receive more then Batch Qty");
                  $('#receive_now' + no).focus();
                  return false;
              }
  
              if( $.trim($('#receive_now' + no).val()) > 0 && $.trim($('#warehouse_id' + no).val()) == '' ){

              
                alert("Select a warehouse");
                $('#warehouse_id' + no).focus();
                return false;
  
              }

              
  
                }
  
  

            }
    
        
        }
  
        $('#action_bar').html('Working on it..... '); 	



        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            total_item: total_item,
            code: code,
            send_to_id: send_to_id,
            send_to: send_to,
            action: 'receive_after_batch_done',
            receive_now: receive_now,
            product_id: product_id,
            warehouse_id: warehouse_id
          },
          cache: false,
          success: function(dataResult){
      
          alert(dataResult);     
          location.reload();
          
          }
        });
        }

        function receive_after_print(){


      
          var total_item = $('#total_item').val();
          var code = $('#code').val();
          var supplier_or_factory_id = $('#supplier_or_factory_id').val();
          var send_to = $('#send_to').val();


          var receive_now = $("[name^='receive_now']").map(function() { return $(this).val() }).get();
          var material_id = $("[name^='material_id']").map(function() { return $(this).val() }).get();
          var warehouse_id = $("[name^='warehouse_id']").map(function() { return $(this).val() }).get();

          if (total_item ==  0) { alert("No Item Added");return false;}
  
          for (var no = 1; no <= total_item; no++) {
 
              if ($.trim($('#receive_now' + no).val()).length == 0   ) {
                alert("can not empty");
                $('#receive_now' + no).focus();
                return false;
              }else {
  
                if( $.trim($('#receive_now' + no).val()) < 0 ){
  
                  alert("Please Enter possitive number");
                  $('#receive_now' + no).focus();
                  return false;
    
                }else{
                  if( (Number($.trim($('#total_batch_qty' + no).val()))  < (Number($.trim($('#receive_now' + no).val()))) + Number($.trim($('#actual_receive' + no).val())))   ) {
                    alert("Cant receive more then Batch Qty");
                    $('#receive_now' + no).focus();
                    return false;
                }
  
                if( (Number($.trim($('#receive_now' + no).val()))  >  Number($.trim($('#total_batch_qty' + no).val())))  ) {
                  alert("Cant receive more then Batch Qty");
                  $('#receive_now' + no).focus();
                  return false;
              }
  
              if( $.trim($('#receive_now' + no).val()) > 0 && $.trim($('#warehouse_id' + no).val()) == '' ){

              
                alert("Select a warehouse");
                $('#warehouse_id' + no).focus();
                return false;
  
              }

              
  
                }
  
  

            }
    
        
        }
  
        $('#action_bar').html('Working on it..... '); 	



        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            total_item: total_item,
            code: code,
            supplier_or_factory_id: supplier_or_factory_id,
            send_to: send_to,
            action: 'receive_after_print',
            receive_now: receive_now,
            material_id: material_id,
            warehouse_id: warehouse_id
          },
          cache: false,
          success: function(dataResult){
      
          alert(dataResult);     
          location.reload();
          
          }
        });
        }



        function receive_after_spray(){

          var total_item = $('#total_item').val();
          var code = $('#code').val();
          var supplier_or_factory_id = $('#supplier_or_factory_id').val();
          var send_to = $('#send_to').val();


          var receive_now = $("[name^='receive_now']").map(function() { return $(this).val() }).get();
          var material_id = $("[name^='material_id']").map(function() { return $(this).val() }).get();
          var warehouse_id = $("[name^='warehouse_id']").map(function() { return $(this).val() }).get();

          if (total_item ==  0) {
          alert("No Item Added");
          return false;
          }
  
          for (var no = 1; no <= total_item; no++) {
 
              if ($.trim($('#receive_now' + no).val()).length == 0   ) {
                alert("can not empty");
                $('#receive_now' + no).focus();
                return false;
              }else {
  
                if( $.trim($('#receive_now' + no).val()) < 0 ){
  
                  alert("Please Enter possitive number");
                  $('#receive_now' + no).focus();
                  return false;
    
                }else{
                  if( (Number($.trim($('#total_batch_qty' + no).val()))  < (Number($.trim($('#receive_now' + no).val()))) + Number($.trim($('#actual_receive' + no).val())))   ) {
                    alert("Cant receive more then Batch Qty");
                    $('#receive_now' + no).focus();
                    return false;
                }
  
                if( (Number($.trim($('#receive_now' + no).val()))  >  Number($.trim($('#total_batch_qty' + no).val())))  ) {
                  alert("Cant receive more then Batch Qty");
                  $('#receive_now' + no).focus();
                  return false;
              }
  
              if( $.trim($('#receive_now' + no).val()) > 0 && $.trim($('#warehouse_id' + no).val()) == '' ){

              
                alert("Select a warehouse");
                $('#warehouse_id' + no).focus();
                return false;
  
              }

              
  
                }
  
  

            }
    
        
        }
  
        $('#action_bar').html('Working on it..... '); 	



        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            total_item: total_item,
            code: code,
            supplier_or_factory_id: supplier_or_factory_id,
            send_to: send_to,
            action: 'receive_after_spray',
            receive_now: receive_now,
            material_id: material_id,
            warehouse_id: warehouse_id
          },
          cache: false,
          success: function(dataResult){
      
          alert(dataResult);     
          location.reload();
          
          }
        });



        }

        function receive_after_molding(){

          var total_item = $('#total_item').val();
          var code = $('#code').val();
          var supplier_or_factory_id = $('#supplier_or_factory_id').val();
          var send_to = $('#send_to').val();


          var receive_now = $("[name^='receive_now']").map(function() { return $(this).val() }).get();
          var supporting_id = $("[name^='supporting_id']").map(function() { return $(this).val() }).get();
          var warehouse_id = $("[name^='warehouse_id']").map(function() { return $(this).val() }).get();

          if (total_item ==  0) {
          alert("No Item Added");
          return false;
          }
  
          for (var no = 1; no <= total_item; no++) {
 
              if ($.trim($('#receive_now' + no).val()).length == 0   ) {
                alert("can not empty");
                $('#receive_now' + no).focus();
                return false;
              }else {
  
                if( $.trim($('#receive_now' + no).val()) < 0 ){
  
                  alert("Please Enter possitive number");
                  $('#receive_now' + no).focus();
                  return false;
    
                }else{
                  if( (Number($.trim($('#total_batch_qty' + no).val()))  < (Number($.trim($('#receive_now' + no).val()))) + Number($.trim($('#actual_receive' + no).val())))   ) {
                    alert("Cant receive more then Batch Qty");
                    $('#receive_now' + no).focus();
                    return false;
                }
  
                if( (Number($.trim($('#receive_now' + no).val()))  >  Number($.trim($('#total_batch_qty' + no).val())))  ) {
                  alert("Cant receive more then Batch Qty");
                  $('#receive_now' + no).focus();
                  return false;
              }
  
              if( $.trim($('#receive_now' + no).val()) > 0 && $.trim($('#warehouse_id' + no).val()) == '' ){

              
                alert("Select a warehouse");
                $('#warehouse_id' + no).focus();
                return false;
  
              }

              
  
                }
  
  

            }
    
        
        }
  
        $('#action_bar').html('Working on it..... '); 	



        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            total_item: total_item,
            code: code,
            supplier_or_factory_id: supplier_or_factory_id,
            send_to: send_to,
            action: 'receive_after_molding',
            receive_now: receive_now,
            supporting_id: supporting_id,
            warehouse_id: warehouse_id
          },
          cache: false,
          success: function(dataResult){
      
          alert(dataResult);     
          location.reload();
          
          }
        });


        }

function final_batch_dispatch(CODE){

  var x = window.confirm("Are you sure to Final ?");
  if(x){
  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      CODE: CODE,
      action: 'final_recipe_wise_dispatch_from_warehouse'
    },
    cache: false,
    success: function(dataResult){
      alert(dataResult);
    location.reload();
    }

  });
}
  
}




        function dispatch_receipe_wise_item_demand(){

          var total_item = $('#total_item').val();
          var code = $('#code').val();
          var supplier_or_factory_id = $('#send_to_id').val();
          var send_to = $('#send_to').val();


          var dispatch_now = $("[name^='dispatch_now']").map(function() { return $(this).val() }).get();
          var product_id = $("[name^='product_id']").map(function() { return $(this).val() }).get();
          var warehouse_id = $("[name^='warehouse_id']").map(function() { return $(this).val() }).get();

          if (total_item ==  0) {
          alert("No Item Added");
          return false;
          }
  
          for (var no = 1; no <= total_item; no++) {


              if ($.trim($('#dispatch_now' + no).val()).length == 0   ) {
                alert("can not empty");
                $('#receive_now' + no).focus();
                return false;
              }else {
  
                if( $.trim($('#dispatch_now' + no).val()) < 0 ){
  
                  alert("Please Enter possitive number");
                  $('#dispatch_now' + no).focus();
                  return false;
    
                }else{
                  if( (Number($.trim($('#actual_quantity' + no).val()))  < (Number($.trim($('#dispatch_now' + no).val()))) + Number($.trim($('#total_dispatch' + no).val())))   ) {
                    alert("Cant provide more then Demand Qty");
                    $('#dispatch_now' + no).focus();
                    return false;
                }
  
                if( (Number($.trim($('#dispatch_now' + no).val()))  >  Number($.trim($('#item_stock' + no).val())))  ) {
                  alert("Cant provide more then Stock Qty");
                  $('#dispatch_now' + no).focus();
                  return false;
              }
  
              if( $.trim($('#dispatch_now' + no).val()) > 0 && $.trim($('#warehouse_id' + no).val()) == '' ){

              
                alert("Select a warehouse");
                $('#warehouse_id' + no).focus();
                return false;
  
              }

              
  
                }
  
  

            }
    
        
        }
  
        $('#action_bar').html('Working on it..... '); 	



        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            total_item: total_item,
            code: code,
            supplier_or_factory_id: supplier_or_factory_id,
            send_to: send_to,
            action: 'dispatch_receipe_wise_item_demand',
            dispatch_now: dispatch_now,
            product_id: product_id,
            warehouse_id: warehouse_id
          },
          cache: false,
          success: function(dataResult){
      
          alert(dataResult);     
          location.reload();
          
          }
        });


        }



        function dispatch_raw_material_for_print(){
        
      
          var total_item = $('#total_item').val();
          var total_print_item = $('#total_print_item').val();

          var code = $('#code').val();
          var supplier_or_factory_id = $('#supplier_or_factory_id').val();
          var send_to = $('#send_to').val();


          var dispatch_now = $("[name^='dispatch_now']").map(function() { return $(this).val() }).get();
          var product_id = $("[name^='product_id']").map(function() { return $(this).val() }).get();
          var warehouse_id = $("[name^='warehouse_id']").map(function() { return $(this).val() }).get();


          var dispatch_now_item = $("[name^='dispatch_now_item']").map(function() { return $(this).val() }).get();
          var print_product_id = $("[name^='print_product_id']").map(function() { return $(this).val() }).get();
          var print_warehouse_id = $("[name^='print_warehouse_id']").map(function() { return $(this).val() }).get();



          if (total_item ==  0 || total_print_item ==  0) {
          alert("No Item Added");
          return false;
          }
  
          for (var no = 1; no <= total_item; no++) {
 

    

              if ($.trim($('#dispatch_now' + no).val()).length == 0   ) {
                alert("can not empty");
                $('#receive_now' + no).focus();
                return false;
              }else {
  
                if( $.trim($('#dispatch_now' + no).val()) < 0 ){
  
                  alert("Please Enter possitive number");
                  $('#dispatch_now' + no).focus();
                  return false;
    
                }else{
                  if( (Number($.trim($('#actual_quantity' + no).val()))  < (Number($.trim($('#dispatch_now' + no).val()))) + Number($.trim($('#total_dispatch' + no).val())))   ) {
                    alert("Cant provide more then Demand Qty");
                    $('#dispatch_now' + no).focus();
                    return false;
                }
  
                if( (Number($.trim($('#dispatch_now' + no).val()))  >  Number($.trim($('#item_stock' + no).val())))  ) {
                  alert("Cant provide more then Stock Qty");
                  $('#dispatch_now' + no).focus();
                  return false;
              }
  
              if( $.trim($('#dispatch_now' + no).val()) > 0 && $.trim($('#warehouse_id' + no).val()) == '' ){

              
                alert("Select a warehouse");
                $('#warehouse_id' + no).focus();
                return false;
  
              }

  
                }
  
  

            }
    
        
        }

        for (var no = 1; no <= total_print_item; no++) {

          
          if ($.trim($('#dispatch_now_item' + no).val()).length == 0   ) {
            alert("can not empty");
            $('#dispatch_now_item' + no).focus();
            return false;
          }else {
      
            if( $.trim($('#dispatch_now_item' + no).val()) < 0 ){
      
              alert("Please Enter possitive number");
              $('#dispatch_now_item' + no).focus();
              return false;
      
            }else{
              if( (Number($.trim($('#print_actual_quantity' + no).val()))  < (Number($.trim($('#dispatch_now_item' + no).val()))) + Number($.trim($('#print_total_dispatch' + no).val())))   ) {
                alert("Cant provide more then Demand Qty");
                $('#dispatch_now_item' + no).focus();
                return false;
            }
      
            if( (Number($.trim($('#dispatch_now_item' + no).val()))  >  Number($.trim($('#item_stock' + no).val())))  ) {
              alert("Cant provide more then Stock Qty");
              $('#dispatch_now_item' + no).focus();
              return false;
          }
      
          if( $.trim($('#dispatch_now_item' + no).val()) > 0 && $.trim($('#print_warehouse_id' + no).val()) == '' ){
      
          
            alert("Select a warehouse");
            $('#print_warehouse_id' + no).focus();
            return false;
      
          }
      
          
      
            }
      
      
      
        }
        }
  
        $('#action_bar').html('Working on it..... '); 	



        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            total_item: total_item,
            code: code,
            total_print_item: total_print_item,
            supplier_or_factory_id: supplier_or_factory_id,
            send_to: send_to,
            action: 'dispatch_raw_material_for_print',
            dispatch_now: dispatch_now,
            product_id: product_id,
            warehouse_id: warehouse_id,
            dispatch_now_item: dispatch_now_item,
            print_product_id: print_product_id,
            print_warehouse_id: print_warehouse_id

          },
          cache: false,
          success: function(dataResult){
      
          alert(dataResult);     
          location.reload();
          
          }
        });


        }

        function dispatch_raw_material_for_spray(){

        
          var total_item = $('#total_item').val();
          var total_spray_item = $('#total_spray_item').val();

          var code = $('#code').val();
          var supplier_or_factory_id = $('#supplier_or_factory_id').val();
          var send_to = $('#send_to').val();

          var dispatch_now_item = $("[name^='dispatch_now_item']").map(function() { return $(this).val() }).get();
          var spray_product_id = $("[name^='spray_product_id']").map(function() { return $(this).val() }).get();
          var spray_warehouse_id = $("[name^='spray_warehouse_id']").map(function() { return $(this).val() }).get();


          var dispatch_now = $("[name^='dispatch_now']").map(function() { return $(this).val() }).get();
          var product_id = $("[name^='product_id']").map(function() { return $(this).val() }).get();
          var warehouse_id = $("[name^='warehouse_id']").map(function() { return $(this).val() }).get();

          if (total_item ==  0 || total_spray_item ==  0) {
          alert("No Item Added");
          return false;
          }
  
          for (var no = 1; no <= total_spray_item; no++) {

          
            if ($.trim($('#dispatch_now_item' + no).val()).length == 0   ) {
              alert("can not empty");
              $('#dispatch_now_item' + no).focus();
              return false;
            }else {

              if( $.trim($('#dispatch_now_item' + no).val()) < 0 ){

                alert("Please Enter possitive number");
                $('#dispatch_now_item' + no).focus();
                return false;
  
              }else{
                if( (Number($.trim($('#spray_actual_quantity' + no).val()))  < (Number($.trim($('#dispatch_now_item' + no).val()))) + Number($.trim($('#spray_total_dispatch' + no).val())))   ) {
                  alert("Cant provide more then Demand Qty");
                  $('#dispatch_now_item' + no).focus();
                  return false;
              }

              if( (Number($.trim($('#dispatch_now_item' + no).val()))  >  Number($.trim($('#item_stock' + no).val())))  ) {
                alert("Cant provide more then Stock Qty");
                $('#dispatch_now_item' + no).focus();
                return false;
            }

            if( $.trim($('#dispatch_now_item' + no).val()) > 0 && $.trim($('#spray_warehouse_id' + no).val()) == '' ){

            
              alert("Select a warehouse");
              $('#spray_warehouse_id' + no).focus();
              return false;

            }

            

              }



          }
          }


          for (var no = 1; no <= total_item; no++) {
 

    

              if ($.trim($('#dispatch_now' + no).val()).length == 0   ) {
                alert("can not empty");
                $('#receive_now' + no).focus();
                return false;
              }else {
  
                if( $.trim($('#dispatch_now' + no).val()) < 0 ){
  
                  alert("Please Enter possitive number");
                  $('#dispatch_now' + no).focus();
                  return false;
    
                }else{
                  if( (Number($.trim($('#actual_quantity' + no).val()))  < (Number($.trim($('#dispatch_now' + no).val()))) + Number($.trim($('#total_dispatch' + no).val())))   ) {
                    alert("Cant provide more then Demand Qty");
                    $('#dispatch_now' + no).focus();
                    return false;
                }
  
                if( (Number($.trim($('#dispatch_now' + no).val()))  >  Number($.trim($('#item_stock' + no).val())))  ) {
                  alert("Cant provide more then Stock Qty");
                  $('#dispatch_now' + no).focus();
                  return false;
              }
  
              if( $.trim($('#dispatch_now' + no).val()) > 0 && $.trim($('#warehouse_id' + no).val()) == '' ){

              
                alert("Select a warehouse");
                $('#warehouse_id' + no).focus();
                return false;
  
              }

              
  
                }
  
  

            }
    
        
        }
  

        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            total_item: total_item,
            total_spray_item: total_spray_item,
            code: code,
            supplier_or_factory_id: supplier_or_factory_id,
            send_to: send_to,
            action: 'dispatch_raw_material_for_spray',
            dispatch_now: dispatch_now,
            product_id: product_id,
            warehouse_id: warehouse_id,
            dispatch_now_item: dispatch_now_item,
            spray_product_id: spray_product_id,
            spray_warehouse_id: spray_warehouse_id


          },
          cache: false,
          success: function(dataResult){
      if(dataResult == 'Data Updated' ){
        alert(dataResult);     
        location.reload();
      }else{
        alert(dataResult);    
      }
         
          
          }
        });




        }



        function dispatch_raw_material_for_molding(){

          
          var total_item = $('#total_item').val();
          var code = $('#code').val();
          var supplier_or_factory_id = $('#supplier_or_factory_id').val();
          var send_to = $('#send_to').val();


          var dispatch_now = $("[name^='dispatch_now']").map(function() { return $(this).val() }).get();
          var product_id = $("[name^='product_id']").map(function() { return $(this).val() }).get();
          var warehouse_id = $("[name^='warehouse_id']").map(function() { return $(this).val() }).get();

          if (total_item ==  0) {
          alert("No Item Added");
          return false;
          }
  
          for (var no = 1; no <= total_item; no++) {
 

              if ($.trim($('#dispatch_now' + no).val()).length == 0   ) {
                alert("can not empty");
                $('#receive_now' + no).focus();
                return false;
              }else {
  
                if( $.trim($('#dispatch_now' + no).val()) < 0 ){
  
                  alert("Please Enter possitive number");
                  $('#dispatch_now' + no).focus();
                  return false;
    
                }else{
                  if( (Number($.trim($('#actual_quantity' + no).val()))  < (Number($.trim($('#dispatch_now' + no).val()))) + Number($.trim($('#total_dispatch' + no).val())))   ) {
                    alert("Cant provide more then Demand Qty");
                    $('#dispatch_now' + no).focus();
                    return false;
                }
  
                if( (Number($.trim($('#dispatch_now' + no).val()))  >  Number($.trim($('#item_stock' + no).val())))  ) {
                  alert("Cant provide more then Stock Qty");
                  $('#dispatch_now' + no).focus();
                  return false;
              }

    

  
              if( $.trim($('#dispatch_now' + no).val()) > 0 && $.trim($('#warehouse_id' + no).val()) == '' ){

              
                alert("Select a warehouse");
                $('#warehouse_id' + no).focus();
                return false;
  
              }

              
  
                }
  
  

            }
    
        
        }
  



        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            total_item: total_item,
            code: code,
            supplier_or_factory_id: supplier_or_factory_id,
            send_to: send_to,
            action: 'dispatch_raw_material_for_molding',
            dispatch_now: dispatch_now,
            product_id: product_id,
            warehouse_id: warehouse_id
          },
          cache: false,
          success: function(dataResult){
      if(dataResult == 'Data Updated' ){
        alert(dataResult);    
       location.reload();
      }else{
        alert(dataResult);
      }
              
          
          }
        });

        }



      $('#save_notification').on('click', function() {
        $("#save_notification").attr("disabled", "disabled");
        var notification = $('#notification').val();

        var related_id = $('#related_id').val();
        var status=document.getElementById("status").checked;

        
        if(notification!="" ){
          $.ajax({
            url: "form_action.php",
            type: "POST",
            data: {
              notification: notification,
              status: status,
              action: 'save_notification',
              related_id: related_id
            },
            cache: false,
            success: function(dataResult){
                $("#save_notification").removeAttr("disabled");
                $('#myform').find('input:text').val('');
                $("#success").show();
                $('#success').html(dataResult); 	
                window.location.replace("Notification/New");
            
              
              
            }
          });
          }
          else{
            alert('Please fill all the field !');
            $("#save_notification").removeAttr("disabled");
      
          }
        });
      

        

        
$('#save_timetable').on('click', function() {
  $("#save_timetable").attr("disabled", "disabled");
  var on_duty_time = $('#on_duty_time').val();
  var related_id = $('#related_id').val();
  var off_duty_time = $('#off_duty_time').val();
  var late_time = $('#late_time').val();
  var leave_early = $('#leave_early').val();

  
  if(on_duty_time!="" || off_duty_time!="" || late_time!=""  || leave_early!="" ){
    $.ajax({
      url: "form_action.php",
      type: "POST",
      data: {
        on_duty_time: on_duty_time,
        action: 'save_timetable',
        off_duty_time: off_duty_time,
        late_time:late_time,
        related_id: related_id,
        leave_early: leave_early
      },
      cache: false,
      success: function(dataResult){
          $("#save_timetable").removeAttr("disabled");
          $('#myform').find('input:text').val('');
          $("#success").show();
          $('#success').html(dataResult); 	
           window.location.replace("HRM/Time-Table/New");
      
        
        
      }
    });
    }
    else{
      alert('Please fill all the field !');
      $("#save_timetable").removeAttr("disabled");

    }
  });


$('#save_department').on('click', function() {
  $("#save_department").attr("disabled", "disabled");
  var department_name = $('#department_name').val();
  var related_id = $('#related_id').val();

  
  if(department_name!="" ){
    $.ajax({
      url: "form_action.php",
      type: "POST",
      data: {
        department_name: department_name,
        action: 'save_department',
        related_id: related_id
      },
      cache: false,
      success: function(dataResult){
          $("#save_department").removeAttr("disabled");
          $('#myform').find('input:text').val('');
          $("#success").show();
          $('#success').html(dataResult); 	
          window.location.replace("Setup/Department-Setup/New");
      
        
        
      }
    });
    }
    else{
      alert('Please fill all the field !');
      $("#save_department").removeAttr("disabled");

    }
  });



  $('#save_section').on('click', function() {
    $("#save_section").attr("disabled", "disabled");
    var section_name = $('#section_name').val();
    var related_id = $('#related_id').val();
  
    
    if(section_name!="" ){
      $.ajax({
        url: "form_action.php",
        type: "POST",
        data: {
          section_name: section_name,
          action: 'save_section',
          related_id: related_id
        },
        cache: false,
        success: function(dataResult){
            $("#save_section").removeAttr("disabled");
            $('#myform').find('input:text').val('');
            $("#success").show();
            $('#success').html(dataResult); 	
            window.location.replace("Setup/Section-Setup/New");
        
          
          
        }
      });
      }
      else{
        alert('Please fill all the field !');
        $("#save_section").removeAttr("disabled");

      }
    });


    $('#save_designation').on('click', function() {
      $("#save_designation").attr("disabled", "disabled");
      var designation_name = $('#designation_name').val();
      var related_id = $('#related_id').val();
    
      
      if(designation_name!="" ){
        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            designation_name: designation_name,
            action: 'save_designation',
            related_id: related_id
          },
          cache: false,
          success: function(dataResult){
              $("#save_designation").removeAttr("disabled");
              $('#myform').find('input:text').val('');
              $("#success").show();
              $('#success').html(dataResult); 	
              window.location.replace("Setup/Designation-Setup/New");
          
            
            
          }
        });
        }
        else{
          alert('Please fill all the field !');
          $("#save_designation").removeAttr("disabled");
  
        }
      });




$('#save_transport_cost').on('click', function() {
  $("#save_transport_cost").attr("disabled", "disabled");
  
  var district_id = $('#district_id').val();
  var related_id = $('#related_id').val();
  var nogot_cost = getNum($('#nogot_cost').val());
  var vaki_cost = getNum($('#vaki_cost').val());

  
  if(district_id!="" ){
    $.ajax({
      url: "form_action.php",
      type: "POST",
      data: {
        district_id: district_id,
        nogot_cost: nogot_cost,
        vaki_cost: vaki_cost,
        action: 'save_transport_cost',
        related_id: related_id
      },
      cache: false,
      success: function(dataResult){
          $("#save_transport_cost").removeAttr("disabled");
          $('#myform').find('input:text').val('');
          $("#success").show();
          $('#success').html(dataResult); 	
          window.location.replace("Setup/Transport-Cost-Setup/New");
      
        
        
      }
    });
    }
    else{
      alert('Please fill all the field !');
      $("#save_transport_cost").removeAttr("disabled");

    }
  });



  
  $('#leave_reason_note').on('click', function() {


    var leave_reason_note = $('#leave_reason_note').val();

    if(leave_reason_note == ''){
      var employee = $( "#employee_id option:selected" ).text();
      var leave_type = $( "#leave_type_id option:selected" ).text();
      var leave = $( "#leave option:selected" ).text();
  
      document.getElementById('leave_reason_note').innerHTML = 'জনাব, ' + employee + ' ' + leave_type + ' এ ' + leave +' ছুটি নিচ্ছেন ' ;
    }
  
  });
  

  

function findLeaveLimit(){

  var leave_type_id = $('#leave_type_id').val();

  if(leave_type_id == '5'  || leave_type_id == '6'){
    var employee_id = $('#employee_id').val();
    var year = $('#year').val();
    var month = $('#month').val();

    var leave_type = $( "#leave_type_id option:selected" ).text();
    if(employee_id == '' ){ alert('Please select an employee'); return false;}

    $.ajax({
      url: "function_tem.php",
      type: "POST",
      data: {
        employee_id: employee_id,
        action: 'find_leave_limit',
        leave_type_id: leave_type_id,
        year: year,
        month: month
  
      },
      dataType: 'json',
      cache: false,
      success: function(dataResult){

        document.getElementById('leave_left').value  = dataResult.type_wise_leave_left;
        document.getElementById('leave_history').innerHTML =  '<b class="text-danger"> ' + leave_type + ' :: Left ' +  dataResult.type_wise_leave_left + ' Days </b> | <b class="text-info"> Taken ' + dataResult.total_leave_taken + ' Days </b>'
     }
    });


  }  




}



function saveAdvanceSetup(){


  var less_then_1_year = $('#less_then_1_year').val();
  var more_then_1_less_then_2 = $('#more_then_1_less_then_2').val();
  var more_then_2_less_then_3 = $('#more_then_2_less_then_3').val();
  var more_then_3_less_then_4 = $('#more_then_3_less_then_4').val();
  var more_then_4_less_then_5 = $('#more_then_4_less_then_5').val();
  var more_then_5 = $('#more_then_5').val();

  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {

      action: 'save_advance',
      less_then_1_year: less_then_1_year,
      more_then_1_less_then_2: more_then_1_less_then_2,
      more_then_2_less_then_3: more_then_2_less_then_3,
      more_then_3_less_then_4: more_then_3_less_then_4,
      more_then_4_less_then_5: more_then_4_less_then_5,
      more_then_5: more_then_5

            },
            
    cache: false,
    success: function(dataResult){
      alert(dataResult)
       window.location.replace("Setup/Advance-Setup/New");
    
      
      
    }
  });
  




}


function paymentterm(term){

  if(term == 'Monthly' ){
    document.getElementById('payment_term_details').innerHTML = '<table class="table " style="background-color:antiquewhite;"><tr><td>Cut Per Month</td><td><input type="number" class="form-control" id="condition1" value=""></td></tr><tr><td>Duration</td><td><select class="form-control" id="condition2" ><option value="1_Month">1 Month </option><option value="3_Month">3 months</option><option value="6_Month">6 months</option><option value="12_Month">12 months</option><option value="18_Month">18 months</option><option value="24_Month">24 months</option></select></td></tr></table>';

  }else if (term == 'Dead-Line' ){
    document.getElementById('payment_term_details').innerHTML = '<table class="table " style="background-color:antiquewhite;"><tr><td>Date</td><td><input type="date" class="form-control" id="condition1" value=""></td></tr><tr><td><input type="hidden"  id="condition2" value=""></td><td></td></tr></table>';

  }else if (term == 'Bonus-Deduction' ){
    document.getElementById('payment_term_details').innerHTML = '<table class="table "><tr><td></td><td><input type="hidden"  id="condition1" value=""></td></tr><tr><td><input type="hidden"  id="condition2" value=""></td><td></td></tr></table>';
  }else{
    document.getElementById('payment_term_details').innerHTML = '';
  }

}


function advanceStatus(employee_id){

  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      employee_id: employee_id,
      action: 'advance_status'
    },
    cache: false,
    success: function(dataResult){
      document.getElementById('employee_details').innerHTML = dataResult;


    }
  });



}

    function clearSelected(UNSELECTID) {

      var elements = document.getElementById(UNSELECTID).options;

      for (var i = 0; i < elements.length; i++) {
          elements[i].selected = false;
      }
      $('.selectpicker').selectpicker('refresh');
      document.getElementById('select_clear_toggole').innerHTML = '<input type="button" class="btn btn-info" onclick="SelecteAll(\''+UNSELECTID+'\');" value="Select All">';   

  }

  function SelecteAll(SELECTID) {

    var elements = document.getElementById(SELECTID).options;

      for (var i = 0; i < elements.length; i++) {
          elements[i].selected = true;
      }
      $('.selectpicker').selectpicker('refresh');


      document.getElementById('select_clear_toggole').innerHTML = '<input type="button" class="btn btn-danger" onclick="clearSelected(\''+SELECTID+'\');" value="Clear All">';     
  }

  $('#save_define_leave').on('click', function() {


    var leave_type_id = $('#leave_type_id').val();
    var related_id = $('#related_id').val();
    var leave_start = $('#leave_start').val();
    var leave_end = $('#leave_end').val();
    var leave_name = $('#leave_name').val();
    var leave_left = $('#leave_left').val();

    var employee_id = $("[name^='employee_id']").map(function() { return $(this).val() }).get();
    var num_item = employee_id.length;

    if(leave_type_id == '' ){ alert('Leave Type cant empty'); return false;}

    if(leave_type_id == '1' || leave_type_id == '2' || leave_type_id == '3' || leave_type_id == '4'  ){

    }else{
      if(leave_left < 1 ){ alert('You dont have enough leave'); return false;}

    }

    $("#save_define_leave").attr("disabled", "disabled");

      $.ajax({
        url: "form_action.php",
        type: "POST",
        data: {
          leave_type_id: leave_type_id,
          action: 'save_define_leave',
          related_id: related_id,
          employee_id:employee_id,
          leave_name: leave_name,
          leave_end: leave_end,
          leave_left: leave_left,
          leave_start: leave_start,
          num_item:num_item
                },
                
        cache: false,
        success: function(dataResult){
          alert(dataResult)
           window.location.replace("HRM/Leave-Define/New");
        
          
          
        }
      });
    
    });


    
    $('#save_file_path').on('click', function() {
      var file_path = $('#file_path').val();
    
      
      if(file_path!="" ){
        $("#save_file_path").attr("disabled", "disabled");

        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            file_path: file_path,
            action: 'save_machine_lcoation'

          },
          cache: false,
          success: function(dataResult){
            alert(dataResult);
              $("#save_area").removeAttr("disabled");
           location.reload();
          
            
            
          }
        });
        }
        else{
          alert('Please fill all the field !');
          $("#save_area").removeAttr("disabled");
  
        }
      });


    $('#save_area').on('click', function() {
      $("#save_area").attr("disabled", "disabled");
      var area_name = $('#area_name').val();
      var related_id = $('#related_id').val();
    
      
      if(area_name!="" ){
        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            area_name: area_name,
            action: 'save_area',
            related_id: related_id
          },
          cache: false,
          success: function(dataResult){
              $("#save_area").removeAttr("disabled");
              $('#myform').find('input:text').val('');
              $("#success").show();
              $('#success').html(dataResult); 	
              window.location.replace("Setup/Area-Setup/New");
          
            
            
          }
        });
        }
        else{
          alert('Please fill all the field !');
          $("#save_area").removeAttr("disabled");
  
        }
      });
  
      $('#save_unit').on('click', function() {
        $("#save_unit").attr("disabled", "disabled");
        var unit_name = $('#unit_name').val();
        var related_id = $('#related_id').val();
      
        
        if(unit_name!="" ){
          $.ajax({
            url: "form_action.php",
            type: "POST",
            data: {
              unit_name: unit_name,
              action: 'save_unit',
              related_id: related_id
            },
            cache: false,
            success: function(dataResult){
                
              window.location.replace("Setup/Unit-Setup/New");
            
              
              
            }
          });
          }
          else{
            alert('Please fill all the field !');
            $("#save_unit").removeAttr("disabled");
    
          }
        });

      $('#save_profile').on('click', function() {
        $("#save_profile").attr("disabled", "disabled");
        var company_name = $('#company_name').val();
        var related_id = $('#related_id').val();
        var company_short_name = $('#company_short_name').val();
        var company_address = $('#company_address').val();
        var company_phone = $('#company_phone').val();
        var company_email = $('#company_email').val();

      
        
        if(company_short_name!="" ){
          $.ajax({
            url: "form_action.php",
            type: "POST",
            data: {
              company_phone: company_phone,
              company_address: company_address,
              company_email: company_email,
              company_name: company_name,
              action: 'save_company_profile',
              related_id: related_id,
              company_short_name: company_short_name
            },
            cache: false,
            success: function(dataResult){
              alert(dataResult);
                window.location.replace("Setup/Company-Profile/New");
            
              
              
            }
          });
          }
          else{
            alert('Please fill all the field !');
            $("#save_profile").removeAttr("disabled");
    
          }
        });



        
        $('#update_personal_profile').on('click', function() {
          $("#save_profile").attr("disabled", "disabled");
          
          var hr_name = $('#hr_name').val();
          var admin_id = $('#admin_id').val();
          var employee_id = $('#employee_id').val();
          var old_pass = $('#old_pass').val();
          var fa_name = $('#fa_name').val();
          var mo_name = $('#mo_name').val();
          var username = $('#username').val();
          var user_password = $('#user_password').val();
          var birth_date = $('#birth_date').val();
          var mob_no = $('#mob_no').val();
          var email = $('#email').val();
          var ad_id = $('#ad_id').val();

                      $.ajax({
              url: "form_action.php",
              type: "POST",
              data: {
                hr_name: hr_name,
                fa_name: fa_name,
                username: username,
                user_password: user_password,
                mo_name: mo_name,
                birth_date: birth_date,
                action: 'update_personal_profile',
                employee_id: employee_id,
                admin_id: admin_id,
                mob_no: mob_no,
                email:email
              },
              cache: false,
              success: function(dataResult){
                alert(dataResult);

                if(old_pass == user_password ){
                  window.location.replace("Setup/Employee-Personal-Profile/"+admin_id);
                }else{
                  window.location.replace("logout.php");

                }
              
                
              }
            });
       
  
          });


          function uploadPic(){

            
  // Get the file input element
  var fileInput = document.getElementById('fileInput');

  // Get the file
  var file = fileInput.files[0];


  var employee_id = document.getElementById('employee_id').value;

  if (fileInput.files.length == 0) {
    alert('Please select a file to upload');
    return;
}

  // Create a new FormData object
  var formData = new FormData();

  // Append the file to the FormData object
  formData.append('file', file);
  formData.append('employee_id', employee_id);

  // Create an XMLHttpRequest object
  var xhr = new XMLHttpRequest();

  // Open the connection to the server
  xhr.open('POST', 'upload-file.php', true);

  // Set the onprogress event handler to update the progress bar
  xhr.upload.onprogress = function(e) {
      if (e.lengthComputable) {
          var percentComplete = (e.loaded / e.total) * 100;
          document.getElementById('crate').innerHTML = (percentComplete + '% uploaded');
          
          // Update the progress bar
          var progressBar = document.getElementById('progressBar');
          progressBar.style.width = percentComplete + '%';
          location.reload();
      }
  };

  // Set the onload event handler to display the success message
  xhr.onload = function() {
      if (this.status == 200) {
        document.getElementById('textUpload').innerHTML = 'File uploaded successfully';
      } else {
        document.getElementById('textUpload').innerHTML = 'Error uploading file';
      }
  };

  // Send the request
  xhr.send(formData);


}

      $('#save_brunch').on('click', function() {
        $("#save_brunch").attr("disabled", "disabled");
        var brunch_name = $('#brunch_name').val();
        var related_id = $('#related_id').val();
        var address1 = $('#address1').val();
        var address2 = $('#address2').val();
        var phone = $('#phone').val();
        var related_warehouse = $("[name^='related_warehouse']").map(function() { return $(this).val() }).get();


        if(brunch_name!="" ){
          $.ajax({
            url: "form_action.php",
            type: "POST",
            data: {
              brunch_name: brunch_name,
              action: 'save_brunch',
              related_id: related_id,
              address1: address1,
              address2: address2,
              phone: phone,
              related_warehouse:related_warehouse
            },
            cache: false,
            success: function(dataResult){
              alert(dataResult);
                window.location.replace("Setup/Brunch-Setup/New");
            
              
              
            }
          });
          }
          else{
            alert('Please fill all the field !');
            $("#save_brunch").removeAttr("disabled");
    
          }
        });


      $('#create_raw_category').on('click', function() {
        $("#create_raw_category").attr("disabled", "disabled");
        var category_name = $('#category_name').val();
        var related_id = $('#related_id').val();
      
        
        if(category_name!="" ){
          $.ajax({
            url: "form_action.php",
            type: "POST",
            data: {
              category_name: category_name,
              action: 'create_raw_category',
              related_id: related_id
            },
            cache: false,
            success: function(dataResult){
              const myArr = dataResult.split("_SAJID_");
                $("#create_raw_category").removeAttr("disabled");
                $('#myform').find('input:text').val('');
                $("#success").show();
                $('#success').html(myArr[0]); 	
                window.location.replace("Recipe/Raw-Material-Category-Setup/New/New");
            
              
              
            }
          });
          }
          else{
            alert('Please fill all the field !');
            $("#create_raw_category").removeAttr("disabled");
    
          }
        });


        $('#save_category').on('click', function() {
          $("#save_category").attr("disabled", "disabled");
          var category_name = $('#category_name').val();
          var related_id = $('#related_id').val();
        
          
          if(category_name!="" ){
            $.ajax({
              url: "form_action.php",
              type: "POST",
              data: {
                category_name: category_name,
                action: 'save_category',
                related_id: related_id
              },
              cache: false,
              success: function(dataResult){
                const myArr = dataResult.split("_SAJID_");
                  $("#save_category").removeAttr("disabled");
                  $('#myform').find('input:text').val('');
                  $("#success").show();
                  $('#success').html(myArr[0]); 	
                  location.reload();              
                
                
              }
            });
            }
            else{
              alert('Please fill all the field !');
              $("#save_category").removeAttr("disabled");
      
            }
          });



          function showPurchasePrice(){


            var product_id = $('#product_id').val();
            var checkBox = document.getElementById("checkValue");

            if(product_id == ''){
        
              alert('Please select a product first');
              document.getElementById("checkValue").checked = false;

            }else{
              if(checkBox.checked == true){

                $.ajax({
                  url: "function_tem.php",
                  type: "POST",
                  data: {
                    product_id: product_id,
                    action: 'find_purchase_price'
                  },
                  dataType: 'json',
                  cache: false,
                  success: function(dataResult){
                    document.getElementById('show_purchase_price').value = dataResult.price;


                  }
                });


              }else{
                document.getElementById('show_purchase_price').value = '********';

              }

        
            
            }
           

          }



          
function calculate_purches_price(){

var purches_price = getNum($('#purches_price').val());
var quantity = getNum($('#quantity').val());
$('#price_tag').html(purches_price*quantity); 


}


function getNum(val) {
  var given_number = parseFloat(val).toFixed(2);
if (isNaN(given_number)) {
return 0.00;
}
return given_number;
}




  function calculation_of_two_number(TYPE,NUMBER1,NUMBER2,RESULT_ID){


    
    var NUMBER1 = getNum($('#' + NUMBER1).val());
    var NUMBER2 = getNum($('#' + NUMBER2).val());
    if(TYPE == 'DIFFREANCE'){
      document.getElementById(RESULT_ID).value = getNum(NUMBER1-NUMBER2) ;
      document.getElementById(RESULT_ID).innerHTML = getNum(NUMBER1-NUMBER2) ;

    }else if(TYPE == 'MULTIFICATION'){
      document.getElementById(RESULT_ID).value = getNum(NUMBER1*NUMBER2) ;
      document.getElementById(RESULT_ID).innerHTML = getNum(NUMBER1*NUMBER2) ;

    }else if(TYPE == 'DIVISION'){
      document.getElementById(RESULT_ID).value = getNum(NUMBER1/NUMBER2) ;
      document.getElementById(RESULT_ID).innerHTML = getNum(NUMBER1/NUMBER2) ;

    }else{
      document.getElementById(RESULT_ID).value = '';
      document.getElementById(RESULT_ID).innerHTML = '';

    }

    
    
            }
    


function receive_sales_retuen_by_warehouse(){
  cart2 = [];
  var code = $('#code').val();
  var total_item = $('#total_item').val();

  for(var i = 1; i <= total_item; i++) {
    var element2 = {};

    var id =  $('#id'+[i]).val();
    var return_quantity =  $('#return_quantity'+[i]).val();
    var warehouse_id =  $('#warehouse_id'+[i]).val();
    var product_id =  $('#product_id'+[i]).val();

    element2.id = id;
    element2.return_quantity = return_quantity;
    element2.warehouse_id = warehouse_id;
    element2.product_id = product_id;


    cart2.push({element2: element2});

  }

  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      today_data: JSON.stringify(cart2),
      code: code,
      total_item: total_item,
      action: 'receive_sales_retuen_by_warehouse'
    },
    cache: false,
    success: function(dataResult){

    alert(dataResult);     
    location.reload();
    
    }
  });


 }



 
function DamageProduct(){

  cart2 = [];

  var total_item = $('#total_item').val();
  var main_invoice_id = $('#main_invoice_id').val();
  var customer_id = $('#customer_id').val();


  if (total_item ==  0) { alert("No Item Added"); return false; }

  $("#action_bar").attr("disabled", "disabled");


  for(var i = 1; i <= total_item; i++) {
    var element2 = {};

    var product_id =  $('#product_id'+[i]).val();
    var return_qty =  $('#return_qty'+[i]).val();
    var note =  $('#note'+[i]).val();
    var sales_quantity =  $('#sales_quantity'+[i]).val();
    var sales_rate =  $('#sales_rate'+[i]).val();
    var previous_damage_qty =  $('#previous_damage_qty'+[i]).val();
    var damage_carton =  $('#damage_carton'+[i]).val();
    var damage_qty =  $('#damage_qty'+[i]).val();


    if(damage_qty < 0 || damage_qty < 0 ){ alert('cant not nagative'); $("#action_bar").removeAttr("disabled"); return false; }

    if((+return_qty  + +damage_qty + +previous_damage_qty) > sales_quantity){ alert('cant Return more then invoice QTY'); $("#action_bar").removeAttr("disabled"); return false; }
    


    element2.product_id = product_id;
    element2.damage_qty = damage_qty;
    element2.damage_carton = damage_carton;
    element2.note = note;
    element2.sales_rate = sales_rate;

    cart2.push({element2: element2});

  }
  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      today_data: JSON.stringify(cart2),
      main_invoice_id: main_invoice_id,
      total_item: total_item,
      customer_id:customer_id,
      action: 'return_damage_product_from_customer'
    },
    cache: false,
    success: function(dataResult){

    alert(dataResult);     
    location.reload();
    
    }
  });


}


function ReturnProduct(){

  cart2 = [];

  var total_item = $('#total_item').val();
  var main_invoice_id = $('#main_invoice_id').val();
  var customer_id = $('#customer_id').val();

  if (total_item ==  0) { alert("No Item Added"); return false; }

  $("#action_bar").attr("disabled", "disabled");




  for(var i = 1; i <= total_item; i++) {
    var element2 = {};

    var product_id =  $('#product_id'+[i]).val();
    var return_qty =  $('#return_qty'+[i]).val();
    var return_carton =  $('#return_carton'+[i]).val();
    var damage_qty =  $('#damage_qty'+[i]).val();
    var previous_return_qty =  $('#previous_return_qty'+[i]).val();
    var note =  $('#note'+[i]).val();
    var sales_rate =  $('#sales_rate'+[i]).val();
    var sales_quantity =  $('#sales_quantity'+[i]).val();

     
if(return_qty < 0 || return_carton < 0 ){ alert('cant not nagative'); $("#action_bar").removeAttr("disabled"); return false; }

if((+return_qty  + +damage_qty + +previous_return_qty) > sales_quantity){ alert('cant Return more then invoice QTY'); $("#action_bar").removeAttr("disabled"); return false; }

 
    element2.product_id = product_id;
    element2.return_qty = return_qty;
    element2.return_carton = return_carton;
    element2.sales_rate = sales_rate;
    element2.note = note;

    cart2.push({element2: element2});

  }
  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      today_data: JSON.stringify(cart2),
      main_invoice_id: main_invoice_id,
      total_item: total_item,
      customer_id:customer_id,
      action: 'return_product_from_customer'
    },
    cache: false,
    success: function(dataResult){

    alert(dataResult);     
    location.reload();
    
    }
  });


}


function saveWarehouseLivedata(count){

  var warehouse_id = $('#warehouse_id'+count).val();
  var product_id = $('#product_id'+count).val();
  var sales_id = $('#sales_id'+count).val();
  var demand_qty = $('#demand_qty'+count).val();

  if(warehouse_id == ''){

  }else{

    
    $.ajax({
      url: "form_action.php",
      type: "POST",
      data: {
        warehouse_id: warehouse_id,
        product_id: product_id,
        sales_id: sales_id,
        demand_qty: demand_qty,
        action: 'check_and_insert_warehouse_live_data'
      },
      cache: false,
      success: function(dataResult){
  if(dataResult == 'DONE' ){

  }else{
    alert(dataResult);
    $('#warehouse_id'+count).val('').change();
    
  }
    
      }
    });
  }



}


function GodowncopyCopySave(){
  cart2 = [];
  var total_item = $('#total_item').val();
  var main_invoice_id = $('#main_invoice_id').val();
  var dispatcher_id = $("[name^='Seletdispatcher_id']").map(function() { return $(this).val() }).get();
  var dispatcher_name = $('#dispatcherNname').val();



  if(dispatcher_id == '' ){
    alert('Dispatcher Cant not empty'); 
    return false;
  }  


  if (total_item ==  0) {
  alert("No Item Added");
  return false;
  }

  for(var i = 1; i <= total_item; i++) {


    var row = document.getElementById("tr_no" + i);
    var cells = row.getElementsByTagName("td");
    for (var y = 0; y < cells.length; y++) {
    cells[y].style.color = "black";
    }

    var element2 = {};

    var warehouse_id =  $('#warehouse_id'+[i]).val();
    var product_id =  $('#product_id'+[i]).val();
    var sales_id =  $('#sales_id'+[i]).val();
    var sales_quantity_stock_check =  $('#sales_quantity_stock_check'+[i]).val();

    if(warehouse_id == '' ){ alert('Warehouse Cant empty'); return false;}else{}

    element2.warehouse_id = warehouse_id;
    element2.sales_id = sales_id;
    element2.product_id = product_id;
    element2.sales_quantity_stock_check = sales_quantity_stock_check;

    
    cart2.push({element2: element2});

  }

  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      today_data: JSON.stringify(cart2),
      total_item: total_item,
      dispatcher_id: dispatcher_id,
      main_invoice_id: main_invoice_id,
      dispatcher_name: dispatcher_name,
      action: 'godowncopy_copy_by_warehouse_manager'
    },
    cache: false,
    success: function(dataResult){

    if(dataResult == 'Success' ){

      alert(dataResult);     
      location.reload();
    }else{
      alert(dataResult);
      
      var check_tr = dataResult.split(",");
      check_tr.forEach(function(TR) {
        var row = document.getElementById("tr_no" + TR);
        var cells = row.getElementsByTagName("td");
        for (var i = 0; i < cells.length; i++) {
        cells[i].style.color = "red";
        }

      });
    }

    
    }
  });

}

 function ChallanCopySave(){



  var x = window.confirm("Are you sure to create Challan Copy?");
  if(x){
      
      cart2 = [];

  var total_item = $('#total_item').val();
  var code = $('#code').val();
  var customer_id =  $('#customer_id').val();
  var main_invoice_id = $('#main_invoice_id').val();
  var dispatcher_id = $("[name^='Seletdispatcher_id']").map(function() { return $(this).val() }).get();
  var dispatcher_name = $('#dispatcherNname').val();
  var invoice_date = $('#invoice_date').val();
  var total_transport_cost = $('#total_transport_cost').val();
  var brunch_id = $('#brunch_id').val();



  if(dispatcher_id == '' ){
    alert('Dispatcher Cant not empty'); 
    return false;
  }  

  if (total_transport_cost ==  '' ) {
    alert("Transport Cost Empty");
    return false;
    }

    

  if (total_item ==  0) {
  alert("No Item Added");
  return false;
  }



  for(var i = 1; i <= total_item; i++) {


    var row = document.getElementById("tr_no" + i);
    var cells = row.getElementsByTagName("td");
    for (var y = 0; y < cells.length; y++) {
    cells[y].style.color = "black";
    }



    var element2 = {};

    var warehouse_id =  $('#warehouse_id'+[i]).val();
    var product_id =  $('#product_id'+[i]).val();

    
    var sales_id =  $('#sales_id'+[i]).val();
    var sales_quantity =  $('#sales_quantity'+[i]).val();
    var carton_receive =  $('#carton_receive'+[i]).val();
    var sales_rate = $('#sales_rate'+[i]).val();

    if(warehouse_id == '' ){ alert('Warehouse Cant empty'); return false;}else{}

    element2.warehouse_id = warehouse_id;
    element2.sales_id = sales_id;
    element2.sales_quantity = sales_quantity;
    element2.carton_receive = carton_receive;
    element2.product_id = product_id;
    element2.sales_rate = sales_rate;

    
    cart2.push({element2: element2});




  }

  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      today_data: JSON.stringify(cart2),
      code: code,
      customer_id: customer_id,
      total_item: total_item,
      total_transport_cost: total_transport_cost,
      dispatcher_id: dispatcher_id,
      brunch_id: brunch_id,
      invoice_date: invoice_date,
      main_invoice_id: main_invoice_id,
      dispatcher_name: dispatcher_name,
      action: 'challan_copy_by_warehouse_manager'
    },
    cache: false,
    success: function(dataResult){

      if(dataResult == 'Success' ){
    
      location.reload();
    }else{
      
      var check_tr = dataResult.split(",");
      check_tr.forEach(function(TR) {
        var row = document.getElementById("tr_no" + TR);
        var cells = row.getElementsByTagName("td");
        for (var i = 0; i < cells.length; i++) {
        cells[i].style.color = "red";
        }

      });
    }

    
    }
  });
  
      
  }
  
     }


function HardDelete(WHAT,ID){

 var x = window.confirm("Are you sure to create?");
  if(x){
  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      WHAT: WHAT,
      ID: ID,
      action: 'HARD_DELETE'
    },
    cache: false,
    success: function(dataResult){

    alert(dataResult);     
sales_record_report('target1');
    
    }
  });

}
}




            function confirmBySalesManager(){

              cart2 = [];

  var total_item = $('#total_item').val();
  var code = $('#code').val();
  var invoice_date = $('#invoice_date').val();

  
  var sales_price = $("[name^='sales_price']").map(function() { return $(this).val() }).get();
  var sales_id = $("[name^='sales_id']").map(function() { return $(this).val() }).get();
  
  if (total_item ==  0) {
  alert("No Item Added");
  return false;
  }


  $('#action_bar').html('Working on it..... '); 	


  for(var i = 1; i <= total_item; i++) {
    var element2 = {};

    var sales_price =  $('#sales_price'+[i]).val();
    var sales_id =  $('#sales_id'+[i]).val();

    element2.sales_price = sales_price;
    element2.sales_id = sales_id;

    cart2.push({element2: element2});

  }


  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {
      today_data: JSON.stringify(cart2),
      code: code,
      total_item: total_item,
      invoice_date: invoice_date,
      action: 'confirm_invoice_by_sales_manager'
    },
    cache: false,
    success: function(dataResult){

    alert(dataResult);     
    location.reload();
    
    }
  });

}





        function fg_or_raw(TYPE){
          var spinner = "<img src='img/owl/AjaxLoader.gif' alt='loading...' />";

          if(TYPE == ''){
            alert('Select One');
          }else{ 

          $.ajax({
            url: "function_tem.php",
            type: "POST",
            data: {
              TYPE: TYPE,
              action: 'find_type_wise_data'
            },
            cache: false,
            success: function(dataResult){
                $('#load_selct_box').html(dataResult); 	
                $('select').selectpicker();
                $("#refresh_cart").html(spinner).load('cart_local_purches.php?purches_type=' + TYPE );

                
            }

          });
            
          }

            

        }

        

        function fatch_supplier_info(SUPPLIERID){

          $.ajax({
            url: "function_tem.php",
            type: "POST",
            data: {
              SUPPLIERID: SUPPLIERID,
              action: 'get_supplier_info'
            },
            dataType: 'json',
            cache: false,
            success: function(dataResult){
   

              document.getElementById('mobile').value = dataResult.mobile ;
              document.getElementById('address').innerHTML = dataResult.address ;

            }
          });

        }


        function fatch_customer_info(WHERE){

  var customer_id = $('#customer_id').val();
          var transport_cost_type = $('#transport_cost_type').val();


if(customer_id == '' ){
    
    
            if(WHERE == 'SALES_ENTRY' ){
                document.getElementById('mobile').value = '' ;
                document.getElementById('address').value = '' ;
                document.getElementById('creadit_limit').value = 0 ;
                document.getElementById('customer_due').value = 0 ;
                document.getElementById('transport_cost').value = 0 ;
                document.getElementById('estamated_cost').innerHTML = 0 ;
                calculation_of_two_number('MULTIFICATION','total_carton','transport_cost','total_transport_cost');
                sale_calculator();
  
              }else if (WHERE == 'CHLLAN_COPY'){
                document.getElementById('transport_cost').value = 0;
                document.getElementById('estamated_cost').innerHTML = 0 ;
              }else{

              }
              
              
}else{
    
     $.ajax({
            url: "function_tem.php",
            type: "POST",
            data: {
              transport_cost_type: transport_cost_type,
              customer_id: customer_id,
              action: 'get_customer_info'
            },
            dataType: 'json',
            cache: false,
            success: function(dataResult){


              if(WHERE == 'SALES_ENTRY' ){
                document.getElementById('mobile').value = dataResult.mobile ;
                document.getElementById('address').value = dataResult.address ;
                document.getElementById('creadit_limit').value = dataResult.creadit_limit ;
                document.getElementById('customer_due').value = dataResult.due ;

                                document.getElementById('transport_cost').value = dataResult.transport_cost ;

                document.getElementById('estamated_cost').innerHTML = dataResult.transport_cost ;
                calculation_of_two_number('MULTIFICATION','total_carton','transport_cost','total_transport_cost');
                sale_calculator();
  
              }else if (WHERE == 'CHLLAN_COPY'){
                document.getElementById('transport_cost').value = dataResult.transport_cost ;
                document.getElementById('estamated_cost').innerHTML = dataResult.transport_cost ;
              }else{

              }
            

            }
          });
     
}
    
         if(WHERE == 'SALES_ENTRY' ){
          findTransectionDue(customer_id,'Customer','Brunch_Wise');
          sale_calculator();
          }
          
          
}
        
         
        





  $('#add_local_purches').on('click', function() {



    var related_id = $('#local_related_id').val();
    var supplier_id = $('#supplier_id').val();
    var material_id = $('#material_id').val();
    var purches_type = $('#purches_type').val(); 
    var note = $('#note').val(); 
    var supplier_bill_date = $('#supplier_bill_date').val();
    var supplier_bill_no = $('#supplier_bill_no').val();

    var purches_price = getNum($('#purches_price').val());
    var quantity = getNum($('#quantity').val());
    var invoice_date = $('#invoice_date').val();

    if(supplier_id == '' ){ alert('Supplier cant blank');return false;}else{}
    if(material_id == '' ){ alert('Material cant blank');return false;}else{}
    if(purches_type == '' ){ alert('Purchase Type blank');return false;}else{}
    if(supplier_bill_date == '' ){ alert('Bill Date cant blank');return false;}else{}
    if(quantity == '' ){ alert('Quantity cant blank');return false;}else{}

    $("#add_local_purches").attr("disabled", "disabled");

      $.ajax({
        url: "form_action.php",
        type: "POST",
        data: {
          related_id: related_id,
          supplier_id: supplier_id,
          material_id: material_id,
          purches_type: purches_type,
          supplier_bill_date: supplier_bill_date,
          quantity: quantity,
          purches_price: purches_price,
          action: 'add_local_purches',
          supplier_bill_no: supplier_bill_no,
          invoice_date: invoice_date,
          note: note
        },
        cache: false,
        success: function(dataResult){

          document.getElementById('mess_box').innerHTML = '<strong style="color:green"> ' + dataResult + '</strong>';

          $("#add_local_purches").removeAttr("disabled");
          $('#quantity').val('');
          $("#refresh_cart").load('cart_local_purches.php?purches_type=' + purches_type );
        

          setTimeout(function (){
            document.getElementById('mess_box').innerHTML = '';
            var cart_sub_total = $('#cart_sub_total').val();
            $('#sub_total').val(cart_sub_total);
            $("#add_cart_sale").removeAttr("disabled");
            purchase_calculation();

          }, 1000); 

          $('#add_local_purches').val('Add');
          $('#local_related_id').val('new_id');
          $("#material_id").val('').change();
          $('#purches_price').val(0.00);
          $('#quantity').val(0.00);
        
        }
      });
   
    });


    function calculation_of_three_number(NUMBER1,NUMBER2,NUMBER3,RESULT_ID){


      var NUMBER1 = getNum($('#' + NUMBER1).val());
      var NUMBER2 = getNum($('#' + NUMBER2).val());
      var NUMBER3 = getNum($('#' + NUMBER3).val());

    
      document.getElementById(RESULT_ID).value = (+NUMBER1 + +NUMBER2 + +NUMBER3) ;
      
      
              }


                  



    function sale_calculator()
{


  
 
  calculation_of_three_number('sub_total','total_transport_cost','total_vat_cost','invoice_total');
  calculation_of_two_number('MULTIFICATION','quantity','recommended_price','each_item_total');
  calculation_of_two_number('DIFFREANCE','invoice_total','discount','invoice_payable');
}          

    function purchase_calculation(){
      
    var sub_total = getNum($('#sub_total').val());
    var total_transport_cost = getNum($('#total_transport_cost').val());
    var total_vat_cost = getNum($('#total_vat_cost').val());

    invoice_total = getNum(+sub_total+ +total_transport_cost+ +total_vat_cost) ;
    document.getElementById('invoice_total').value = invoice_total;
  
    }

  $('#save_warehouse').on('click', function() {
    $("#save_warehouse").attr("disabled", "disabled");
    var related_id = $('#related_id').val();
    var warehouse_name = $('#warehouse_name').val();
    var warehouse_address = $('#warehouse_address').val();
    var warehouse_phone = $('#warehouse_phone').val(); 
    var warehouse_height = $('#warehouse_height').val();
    var warehouse_width = $('#warehouse_width').val();
    var warehouse_length = $('#warehouse_length').val();

    
    if(warehouse_name!="" && warehouse_height!="" && warehouse_phone!="" && warehouse_address!="" && warehouse_width!="" && warehouse_length!="" ){
      $.ajax({
        url: "form_action.php",
        type: "POST",
        data: {
          warehouse_name: warehouse_name,
          warehouse_address: warehouse_address,
          warehouse_phone: warehouse_phone,
          warehouse_height: warehouse_height,
          warehouse_width: warehouse_width,
          warehouse_length: warehouse_length,
          action: 'save_warehouse',
          related_id: related_id
        },
        cache: false,
        success: function(dataResult){
            $("#save_warehouse").removeAttr("disabled");
            $('#myform').find('input:text').val('');
            $("#success").show();
            $('#success').html(dataResult); 	
            window.location.replace("Setup/Warehouse-Setup/New");
        
        }
      });
      }
      else{
        alert('Please fill all the field !');
        $("#save_warehouse").removeAttr("disabled");
      }
    });


    $('#save_customer').on('click', function() {
   

      var related_id = $('#related_id').val();
      var customer_type= 'Wholesale';
      var customer_name = $('#customer_name').val();
      var address = $('#address').val();
      var mobile = $('#mobile').val();
      var email = $('#email').val();
      var shop_name = $('#shop_name').val();
      var sales_person = $('#sales_person').val();


      if(document.getElementById("in_service").checked){
        var in_service = 'checked';
        }else{
          var in_service = '';
        }
      



      var creadit_limit = getNum($('#creadit_limit').val());
      var division_id = $('#division_id').val();
      var district_id = $('#district_id').val();
      var upazila_id = $('#upazilla_id').val();
      var union_id = $('#union_id').val();


if(mobile == '' ){ alert('Mobile number can not Blank'); return false; }
if(shop_name == '' ){ alert('Shop Name number can not Blank'); return false; }
if(customer_name == '' ){ alert('Customer Name number can not Blank'); return false; }

if(division_id == '' ){ alert('Division can not Blank'); return false; }
if(district_id == '' ){ alert('District Name number can not Blank'); return false; }
if(upazila_id == '' ){ alert('Upazila Name number can not Blank'); return false; }

if(creadit_limit == '' || creadit_limit ==  0 || creadit_limit < 0  ){ alert('Credit Limit can not empty or nagative'); return false; }

if(sales_person == '' ){ alert('Select Sales person'); return false; }

$("#save_customer").attr("disabled", "disabled");

  
      
        
        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            customer_name: customer_name,
            address: address,
            mobile: mobile,
            email: email,
            customer_type: customer_type,
            action: 'save_customer',
            related_id: related_id,
            in_service: in_service,
            division_id: division_id,
            district_id: district_id,
            upazila_id: upazila_id,
            union_id: union_id,
            sales_person: sales_person,
            creadit_limit : creadit_limit ,
            shop_name:shop_name
          },
          cache: false,
          success: function(dataResult){
            if(dataResult == 'Update Success' || dataResult == 'Insert Success'){
              
              $("#success").show();
              $('#success').html(dataResult); 	
              window.location.replace("sales/Customer-Setup/New");
          
            }else{
              $("#save_customer").removeAttr("disabled");
              $("#success").show();
              $('#success').html(dataResult); 	
            }
              
          }
        });



    });





  $('#save_supplier').on('click', function() {
    $("#save_supplier").attr("disabled", "disabled");
    var related_id = $('#related_id').val();
    var supplier_name = $('#supplier_name').val();
    var address = $('#address').val();
    var mobile = $('#mobile').val();
    var owner_name = $('#owner_name').val();
    var email = $('#email').val();
    var description = $('#description').val();

    if(supplier_name!="" && mobile!="" && owner_name!="" && email!="" ){

    
      
      $.ajax({
        url: "form_action.php",
        type: "POST",
        data: {
          supplier_name: supplier_name,
          address: address,
          mobile: mobile,
          owner_name: owner_name,
          email: email,
          action: 'save_supplier',
          related_id: related_id,
          description: description
        },
        cache: false,
        success: function(dataResult){
          if(dataResult == 'Update Success' || dataResult == 'Insert Success'){
            
            $("#success").show();
            $('#success').html(dataResult); 	
            location.reload(); 
          }else{
            $("#save_supplier").removeAttr("disabled");
            $('#myform').find('input:text').val('');
            $("#success").show();
            $('#success').html(dataResult); 	
            location.reload();
          }
            
        }
      });
      }
      else{
        alert('Please fill all the field !');
        $("#save_supplier").removeAttr("disabled");
      }
   
    });

    

  $('#save_factory').on('click', function() {
    $("#save_factory").attr("disabled", "disabled");
    var related_id = $('#related_id').val();
    var factory_name = $('#factory_name').val();
    var address = $('#address').val();
    var mobile = $('#mobile').val();
    var owner_name = $('#owner_name').val();
    var email = $('#email').val();

    if(factory_name!="" && mobile!="" && owner_name!="" && email!="" ){

    
      
      $.ajax({
        url: "form_action.php",
        type: "POST",
        data: {
          factory_name: factory_name,
          address: address,
          mobile: mobile,
          owner_name: owner_name,
          email: email,
          action: 'save_factory',
          related_id: related_id
        },
        cache: false,
        success: function(dataResult){
          if(dataResult == 'Update Success' || dataResult == 'Insert Success'){
            
            $("#success").show();
            $('#success').html(dataResult); 	
            location.reload(); 
          }else{
            $("#save_factory").removeAttr("disabled");
            $('#myform').find('input:text').val('');
            $("#success").show();
            $('#success').html(dataResult); 	
            location.reload();
          }
            
        }
      });
      }
      else{
        alert('Please fill all the field !');
        $("#save_factory").removeAttr("disabled");
      }
   
    });


    

    
    $('#add_cart_damage').on('click', function() {
      $("#add_cart_damage").attr("disabled", "disabled");

      var related_id = $('#related_id').val();
      var product_id = $('#product_id').val();
      var quantity = $('#quantity').val();
      var notes = $('#notes').val();
      var warehouse_id = $('#from_warehouse_id').val();
      var spinner = "<img src='img/owl/AjaxLoader.gif' alt='loading...' />";
      

      if(product_id!="" && quantity!=""  && warehouse_id!="" ){
        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            product_id: product_id,
            quantity: quantity,
            related_id: related_id,
            notes: notes,
            warehouse_id: warehouse_id,
            action: 'add_cart_damage'
          },
          cache: false,
          success: function(dataResult){

              document.getElementById('mess_box').innerHTML = '<strong style="color:green"> ' + dataResult + '</strong>';

              $("#add_cart_damage").removeAttr("disabled");
              $('#quantity').val('');
              $("#refresh_cart").html(spinner).load('damage-cart.php');
            
              setTimeout(function(){
                document.getElementById('mess_box').innerHTML = '';
             }, 2000);

          
          }
        });
        }
        else{
          alert('Please fill all the field !');
          $("#add_cart_damage").removeAttr("disabled");
        }
      });

      

    $('#add_cart_writeoff').on('click', function() {
      $("#add_cart_writeoff").attr("disabled", "disabled");

      var related_id = $('#related_id').val();
      var product_id = $('#product_id').val();
      var quantity = $('#quantity').val();
      var notes = $('#notes').val();
      var warehouse_id = $('#warehouse_id').val();
      var spinner = "<img src='img/owl/AjaxLoader.gif' alt='loading...' />";

      
      if(product_id!="" && quantity!=""  && warehouse_id!="" ){
        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            product_id: product_id,
            quantity: quantity,
            related_id: related_id,
            notes: notes,
            warehouse_id: warehouse_id,
            action: 'add_cart_writeoff'
          },
          cache: false,
          success: function(dataResult){

              document.getElementById('mess_box').innerHTML = '<strong style="color:green"> ' + dataResult + '</strong>';

              $("#add_cart_writeoff").removeAttr("disabled");
              $('#quantity').val('');
              $("#refresh_cart").html(spinner).load('opening-stock-cart.php');
            
              setTimeout(function(){
                document.getElementById('mess_box').innerHTML = '';
             }, 2000);

          
          }
        });
        }
        else{
          alert('Please fill all the field !');
          $("#add_cart_writeoff").removeAttr("disabled");
        }
      });


      $('#add_cart_raw_writeoff').on('click', function() {
        $("#add_cart_raw_writeoff").attr("disabled", "disabled");
  
        var related_id = $('#related_id').val();
        var product_id = $('#product_id').val();
        var quantity = $('#quantity').val();
        var notes = $('#notes').val();
        var warehouse_id = $('#warehouse_id').val();
        var spinner = "<img src='img/owl/AjaxLoader.gif' alt='loading...' />";
  
        
        if(product_id!="" && quantity!=""  && warehouse_id!="" ){
          $.ajax({
            url: "form_action.php",
            type: "POST",
            data: {
              product_id: product_id,
              quantity: quantity,
              related_id: related_id,
              notes: notes,
              warehouse_id: warehouse_id,
              action: 'add_cart_raw_writeoff'
            },
            cache: false,
            success: function(dataResult){
  
                document.getElementById('mess_box').innerHTML = '<strong style="color:green"> ' + dataResult + '</strong>';
  
                $("#add_cart_raw_writeoff").removeAttr("disabled");
                $('#quantity').val('');
                $("#refresh_cart").html(spinner).load('raw-opening-stock-cart.php');
              
                setTimeout(function(){
                  document.getElementById('mess_box').innerHTML = '';
               }, 2000);
  
            
            }
          });
          }
          else{
            alert('Please fill all the field !');
            $("#add_cart_raw_writeoff").removeAttr("disabled");
          }
        });


      $('#add_cart_fg_recipe').on('click', function() {
  
        var related_id = $('#related_id').val();
        var previous_recipe = $('#previous_recipe').val();
        var add_cart_fg_recipe = $('#add_cart_fg_recipe').val();

        
        var product_id = $('#product_id').val();
        var quantity = $('#quantity').val();
        var raw_material_id = $('#raw_material_id').val();
        var spinner = "<img src='img/owl/AjaxLoader.gif' alt='loading...' />";
  
if(add_cart_fg_recipe == 'Update' && related_id == 'new_id' ){ alert('Select a product from cart to update'); return false;}else{ }
if(previous_recipe == 1 ){ alert('Recipe alredy setup for this product'); return false;}else{ }
if(product_id == '' ){ alert('Select a product'); return false;}else{ }
if(quantity == '' ){ alert('Actual Quantity cant empty'); return false;}else{ }
if(raw_material_id == '' ){ alert('RAW MATERIAL cant empty'); return false;}else{ }

        $("#add_cart_fg_recipe").attr("disabled", "disabled");

          $.ajax({
            url: "form_action.php",
            type: "POST",
            data: {
              product_id: product_id,
              quantity: quantity,
              related_id: related_id,
              raw_material_id: raw_material_id,
              action: 'add_cart_fg_recipe'
            },
            cache: false,
            success: function(dataResult){
  
                document.getElementById('mess_box').innerHTML = '<strong style="color:green"> ' + dataResult + '</strong>';
  
                $("#add_cart_fg_recipe").removeAttr("disabled");
                $('#quantity').val('');
                $("#refresh_cart").html(spinner).load('fg-recipe-cart.php?related_id=' + related_id + '&product_id=' + product_id);
              
                setTimeout(function(){
                  document.getElementById('mess_box').innerHTML = '';
               }, 2000);
               $("#add_cart_fg_recipe").removeAttr("disabled");

            
            }
          });
         
        });



        $('#add_cart_supporting_recipe').on('click', function() {
   
          var previous_recipe = $('#previous_recipe').val();
          var add_cart_supporting_recipe = $('#add_cart_supporting_recipe').val();

          var related_id = $('#related_id').val();
          var supporting_id = $('#supporting_id').val();
          var quantity = getNum($('#quantity').val());
          var raw_material_id = $('#raw_material_id').val();
          var spinner = "<img src='img/owl/AjaxLoader.gif' alt='loading...' />";
    
          if(add_cart_supporting_recipe == 'Update' && related_id == 'new_id' ){ alert('Select a product from cart to update'); return false;}else{ }



          if(previous_recipe == 1 ){ alert('Recipe alredy setup for this product'); return false;}else{ }
          if(supporting_id == '' ){ alert('Select a product'); return false;}else{ }
          if(quantity == '' ){ alert('Actual Quantity cant empty'); return false;}else{ }
          if(raw_material_id == '' ){ alert('RAW MATERIAL cant empty'); return false;}else{ }

          $("#add_cart_supporting_recipe").attr("disabled", "disabled");

            $.ajax({
              url: "form_action.php",
              type: "POST",
              data: {
                supporting_id: supporting_id,
                quantity: quantity,
                related_id: related_id,
                raw_material_id: raw_material_id,
                action: 'add_cart_supporting_recipe'
              },
              cache: false,
              success: function(dataResult){
    
                  document.getElementById('mess_box').innerHTML = '<strong style="color:green"> ' + dataResult + '</strong>';
    
                  $("#add_cart_supporting_recipe").removeAttr("disabled");
                  $('#quantity').val('');
                  $("#refresh_cart").html(spinner).load('molding-recipe-cart.php?related_id=' + related_id + '&product_id=' + supporting_id);
                
                  setTimeout(function(){
                    document.getElementById('mess_box').innerHTML = '';
                 }, 2000);
                 $("#add_cart_supporting_recipe").removeAttr("disabled");
              
              }
            });
        
          });

                 
          $('#add_cart_spray_recipe').on('click', function() {
      
            var previous_recipe = $('#previous_recipe').val();

            var related_id = $('#related_id').val();
            var spray_material_id = $('#spray_material_id').val();
            var quantity = getNum($('#quantity').val());
            var raw_material_id = $('#raw_material_id').val();
            var spinner = "<img src='img/owl/AjaxLoader.gif' alt='loading...' />";
      
            var add_cart_spray_recipe = $('#add_cart_spray_recipe').val();
            if(add_cart_spray_recipe == 'Update' && related_id == 'new_id' ){ alert('Select a product from cart to update'); return false;}else{ }



if(previous_recipe == 1 ){ alert('Recipe alredy setup for this product'); return false;}else{ }
if(spray_material_id == '' ){ alert('Select a product'); return false;}else{ }
if(quantity == '' ){ alert('Actual Quantity cant empty'); return false;}else{ }
if(raw_material_id == '' ){ alert('RAW MATERIAL cant empty'); return false;}else{ }

            $("#add_cart_spray_recipe").attr("disabled", "disabled");

              $.ajax({
                url: "form_action.php",
                type: "POST",
                data: {
                  spray_material_id: spray_material_id,
                  quantity: quantity,
                  related_id: related_id,
                  raw_material_id: raw_material_id,
                  action: 'add_cart_spray_recipe'
                },
                cache: false,
                success: function(dataResult){
      
                    document.getElementById('mess_box').innerHTML = '<strong style="color:green"> ' + dataResult + '</strong>';
      
                    $("#add_cart_spray_recipe").removeAttr("disabled");
                    $('#quantity').val('');
                    $("#refresh_cart").html(spinner).load('spray-recipe-cart.php?related_id=' + related_id + '&product_id=' + spray_material_id);
                  
                    setTimeout(function(){
                      document.getElementById('mess_box').innerHTML = '';
                   }, 2000);
      
                
                }
              });
          
            });



            $('#final_spray_recipe').on('click', function() {
              $("#final_spray_recipe").attr("disabled", "disabled");
        
              var count_cart = $('#count_cart').val();
      
              
              
              if(count_cart > 0  ){
                $.ajax({
                  url: "form_action.php",
                  type: "POST",
                  data: {
                    action: 'final_spray_recipe'
                  },
                  cache: false,
                  success: function(dataResult){
                    alert(dataResult);
                      $("#final_spray_recipe").removeAttr("disabled");
                      window.location.replace("Recipe/Spray-Recipe-Setup/New/New");
                  }
                });
                }
                else{
                  alert('No product added');
                  $("#final_spray_recipe").removeAttr("disabled");
                }
              });
    

              $('#add_cart_print_recipe').on('click', function() {
              
                var previous_recipe = $('#previous_recipe').val();
                var add_cart_print_recipe = $('#add_cart_print_recipe').val();

                var related_id = $('#related_id').val();
                var print_material_id = $('#print_material_id').val();
                var quantity = getNum($('#quantity').val());
                var raw_material_id = $('#raw_material_id').val();
                var spinner = "<img src='img/owl/AjaxLoader.gif' alt='loading...' />";
            
                if(add_cart_print_recipe == 'Update' && related_id == 'new_id' ){ alert('Select a product from cart to update'); return false;}else{ }


                if(previous_recipe == 1 ){ alert('Recipe alredy setup for this product'); return false;}else{ }
if(print_material_id == '' ){ alert('Select a product'); return false;}else{ }
if(quantity == '' ){ alert('Actual Quantity cant empty'); return false;}else{ }
if(raw_material_id == '' ){ alert('RAW MATERIAL cant empty'); return false;}else{ }


                
                $("#add_cart_print_recipe").attr("disabled", "disabled");


                  $.ajax({
                    url: "form_action.php",
                    type: "POST",
                    data: {
                      print_material_id: print_material_id,
                      quantity: quantity,
                      related_id: related_id,
                      raw_material_id: raw_material_id,
                      action: 'add_cart_print_recipe'
                    },
                    cache: false,
                    success: function(dataResult){
            
                        document.getElementById('mess_box').innerHTML = '<strong style="color:green"> ' + dataResult + '</strong>';
            
                        $("#add_cart_print_recipe").removeAttr("disabled");
                        $('#quantity').val('');
                        $("#refresh_cart").html(spinner).load('print-recipe-cart.php?related_id=' + related_id + '&product_id=' + print_material_id);
                      
                        setTimeout(function(){
                          document.getElementById('mess_box').innerHTML = '';
                       }, 2000);
            
                    
                    }
                  });
            
                });
            
            
            
                $('#final_print_recipe').on('click', function() {
                  $("#final_print_recipe").attr("disabled", "disabled");
            
                  var count_cart = $('#count_cart').val();
            
                  
                  
                  if(count_cart > 0  ){
                    $.ajax({
                      url: "form_action.php",
                      type: "POST",
                      data: {
                        action: 'final_print_recipe'
                      },
                      cache: false,
                      success: function(dataResult){
                        alert(dataResult);
                          $("#final_print_recipe").removeAttr("disabled");
                          window.location.replace("Recipe/Print-Recipe-Setup/New/New");
                      }
                    });
                    }
                    else{
                      alert('No product added');
                      $("#final_print_recipe").removeAttr("disabled");
                    }
                  });

                  
                  
function go_purches(){
  

  var x = window.confirm("Are you sure to create P.O?");
  if(x){
  
    cart = [];

    var checksomthing = $('input[type=checkbox]:checked').map(function(){
        return this.value;
    }).get().join(",");
    
    var str_array = checksomthing.split(',');
    var count_item =  str_array.length;
    
    
    if (checksomthing.length <  1) { alert("No Item Added"); return false;  }
    
    for(var i = 0; i < count_item; i++) {
      var element = {};
      var total_demand =  getNum($('#total_demand'+str_array[i]).val());
      var material_id =  $('#material_id'+str_array[i]).val();
      var action_status =  $('#action_status'+str_array[i]).val();

   
        element.material_id = material_id;
        element.total_demand = total_demand;
      
        cart.push({element: element});
    
     
    
    }
    
    var user_given_pi_no = $('#usergivenpino').val();
    var code = $('#code').val();

    
      
        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            action: 'action_go_purches',
            purches_me: JSON.stringify(cart),
            count_item: count_item,
            code: code
            
          },
          cache: false,
          success: function(dataResult){
              alert(dataResult);
            window.location.replace("Sales/Local-Purchase/_ID_New_ID_raw_local_purches_ID_" +user_given_pi_no );
          }
        });
  
  }
  }


                


function go_requasation(){
  

var code =  $('#code').val();


  var x = window.confirm("Are you sure to send Requasation?");
  if(x){
  
    cart_reqo = [];

    var checksomthing = $('input[type=checkbox]:checked').map(function(){
        return this.value;
    }).get().join(",");
    
    var str_array = checksomthing.split(',');
    var count_item =  str_array.length;
    
   
    if (checksomthing.length <  1) { alert("No Item Added"); return false;  }
    
    for(var i = 0; i < count_item; i++) {

      var element_reco = {};
      var total_demand =  getNum($('#total_demand'+str_array[i]).val());
      var material_id =  $('#material_id'+str_array[i]).val();
      var stock =  getNum($('#total_stock'+str_array[i]).val());
      var action_status =  $('#action_status'+str_array[i]).val();


        if(+total_demand > +stock ){
          alert('Not Enough in stock3');
          return false; 
      }else{
        element_reco.material_id = material_id;
        element_reco.total_demand = total_demand;
      
        cart_reqo.push({element_reco: element_reco});
      }
      

    
    }
    
    
      
        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            action: 'action_go_requasation',
            purches_me: JSON.stringify(cart_reqo),
            count_item: count_item,
            code: code
            
          },
          cache: false,
          success: function(dataResult){ 
            alert('Action Success');
   
       location.reload();
  
        }
        });
  
  }



} 


function go_print(){

  
  var x = window.confirm("Are you sure to Send for Print?");
  if(x){
  

  cart = [];

var checksomthing = $('input[type=checkbox]:checked').map(function(){
    return this.value;
}).get().join(",");

var str_array = checksomthing.split(',');
var count_item =  str_array.length;

if (checksomthing.length <  1) { alert("No Item Added"); return false;  }

for(var i = 0; i < count_item; i++) {
  var element = {};
  var total_demand = getNum($('#total_demand'+str_array[i]).val());
  var material_id =  $('#material_id'+str_array[i]).val();
  var stock =  getNum($('#total_stock'+str_array[i]).val());
  var action_status =  $('#action_status'+str_array[i]).val();

  if(+total_demand > +stock ){
      alert('Not Enough in stock1');
      return false; 
  }else{
    element.material_id = material_id;
    element.total_demand = total_demand;
  
    cart.push({element: element});
  


}
}

var user_given_pi_no = $('#usergivenpino').val();
var code = $('#code').val();

    $.ajax({
      url: "form_action.php",
      type: "POST",
      data: {
        action: 'action_go_print',
        purches_me: JSON.stringify(cart),
        count_item: count_item,
        code: code
        
      },
      cache: false,
      success: function(dataResult){

          alert(dataResult);
          window.location.replace("Production/Send-For-Print/_ID_New_ID_" +user_given_pi_no );
        


       
      }
    });

  }

}

function go_sprey(){



  var x = window.confirm("Are you sure to Send for Sprey?");
  if(x){
  cart = [];

var checksomthing = $('input[type=checkbox]:checked').map(function(){
    return this.value;
}).get().join(",");

var str_array = checksomthing.split(',');
var count_item =  str_array.length;

if (checksomthing.length <  1) { alert("No Item Added"); return false;  }

for(var i = 0; i < count_item; i++) {
  
  var element = {};
  var total_demand =  getNum($('#total_demand'+str_array[i]).val());
  var material_id =  $('#material_id'+str_array[i]).val();
  var stock =  getNum($('#total_stock'+str_array[i]).val());
  var action_status =  $('#action_status'+str_array[i]).val();



  if(+total_demand > +stock ){
    alert('Not Enough in stock2');
    return false; 
}else{
  element.material_id = material_id;
  element.total_demand = total_demand;

  cart.push({element: element});
}


}
var user_given_pi_no = $('#usergivenpino').val();
var code = $('#code').val();

  
    $.ajax({
      url: "form_action.php",
      type: "POST",
      data: {
        action: 'action_go_spray',
        purches_me: JSON.stringify(cart),
        count_item: count_item,
        code: code
        
      },
      cache: false,
      success: function(dataResult){  
        alert(dataResult);
        window.location.replace("Production/Send-For-Spray/_ID_New_ID_" +user_given_pi_no );
       
      }
    });

  }

}




function go_mold(){


  var x = window.confirm("Are you sure to Send for Molding?");
  if(x){
  cart = [];

var checksomthing = $('input[type=checkbox]:checked').map(function(){
    return this.value;
}).get().join(",");

var str_array = checksomthing.split(',');
var count_item =  str_array.length;

if (checksomthing.length <  1) { alert("No Item Added"); return false;  }

for(var i = 0; i < count_item; i++) {
  var element = {};
  var total_demand =  getNum($('#total_demand'+str_array[i]).val());
  var material_id =  $('#material_id'+str_array[i]).val();
  var action_status =  $('#action_status'+str_array[i]).val();

  element.material_id = material_id;
  element.total_demand = total_demand;

  cart.push({element: element});
  
}
var user_given_pi_no = $('#usergivenpino').val();
var code = $('#code').val();

    $.ajax({
      url: "form_action.php",
      type: "POST",
      data: {
        action: 'action_go_mold',
        purches_me: JSON.stringify(cart),
        count_item: count_item,
        code: code
        
      },
      cache: false,
      success: function(dataResult){

          alert(dataResult);
         
          window.location.replace("Production/Send-For-Molding/_ID_New_ID_" +user_given_pi_no );
       
        

      }
    });


  }


    }



          $('#final_local_purches').on('click', function() {

            var count_cart = $('#count_cart').val();
            var purches_type = $('#purches_type').val(); 
            var note = $('#note').val(); 
            var invoice_total = getNum($('#invoice_total').val());
            var supplier_bill_date = $('#supplier_bill_date').val(); 
            var total_transport_cost = getNum($('#total_transport_cost').val());
            var total_vat_cost = getNum($('#total_vat_cost').val());

          
            if(purches_type == '' ){ alert('Please select purches type'); return false ; }else{}
            if(count_cart == 0  ){ alert('No Item Added'); return false ; }else{}

         


               $("#final_local_purches").attr("disabled", "disabled");
               $("#add_local_purches").attr("disabled", "disabled");


                $.ajax({
                  url: "form_action.php",
                  type: "POST",
                  data: {
                    action: 'final_local_purches',
                    purches_type: purches_type,
                    note: note,
                    invoice_total: invoice_total,
                    supplier_bill_date: supplier_bill_date,
                    total_vat_cost: total_vat_cost,
                    total_transport_cost: total_transport_cost
                    
                  },
                  dataType: 'json',
                  cache: false,
                  success: function(dataResult){

                     alert(dataResult.mess);
                    
                     $("#final_local_purches").removeAttr("disabled");
                     $("#add_local_purches").removeAttr("disabled");                    
                     
                       document.getElementById('transection_modal').innerHTML =   '<a id="test2" href="#modal_large" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-whatever1="LOCAL PURCHASE EXPENSE" data-whatever2="LOCAL-PURCHASE-EXPENSE" data-whatever3="'+dataResult.transection_id+'"><span>TEST2</span></a>';
                      $('#test2').find('span').trigger('click');
                       window.open("purchase_invoice_copy.php?purches_type="+purches_type+"&code=" +dataResult.relared_code );
                     
                  }
                });
           
            
         
            });
  


          
        $('#final_supporting_recipe').on('click', function() {
          $("#final_fg_recipe").attr("disabled", "disabled");
    
          var count_cart = $('#count_cart').val();
  
          
          
          if(count_cart > 0  ){
            $.ajax({
              url: "form_action.php",
              type: "POST",
              data: {
                action: 'final_supporting_recipe'
              },
              cache: false,
              success: function(dataResult){
                alert(dataResult);
                  $("#final_supporting_recipe").removeAttr("disabled");
                  window.location.replace("Recipe/Molding-Recipe-Setup/New/New");
              }
            });
            }
            else{
              alert('No product added');
              $("#final_supporting_recipe").removeAttr("disabled");
            }
          });



        

        $('#final_fg_recipe').on('click', function() {
          $("#final_fg_recipe").attr("disabled", "disabled");
    
          var count_cart = $('#count_cart').val();
  
          
          
          if(count_cart > 0  ){
            $.ajax({
              url: "form_action.php",
              type: "POST",
              data: {
                action: 'final_fg_recipe'
              },
              cache: false,
              success: function(dataResult){
                alert(dataResult);
                  $("#final_fg_recipe").removeAttr("disabled");
                  window.location.replace("Recipe/FG-Recipe-Setup/New/New");
              }
            });
            }
            else{
              alert('No product added');
              $("#final_fg_recipe").removeAttr("disabled");
            }
          });

          
          

          
      $('#final_damage').on('click', function() {
        $("#final_damage").attr("disabled", "disabled");
  
        var invoice_date = $('#invoice_date').val();
        var count_cart = $('#count_cart').val();
        var dispatcher_id = $('#dispatcher_id').val();

        
        
        if(count_cart > 0  ){
          $.ajax({
            url: "form_action.php",
            type: "POST",
            data: {
              invoice_date: invoice_date,
              dispatcher_id: dispatcher_id,
              action: 'final_damage'
            },
            cache: false,
            dataType: 'json',
            success: function(dataResult){
                $("#final_damage").removeAttr("disabled");
                window.open("print.php?print=FG-STORE-DAMAGE-RECEIPT&code=" +dataResult.code );
                window.location.replace("Inventory/Finished-Goods-Damage-Receive/New");
            }
          });
          }
          else{
            alert('No product added');
            $("#final_damage").removeAttr("disabled");
          }
        });


      $('#final_writeoff').on('click', function() {
        $("#final_writeoff").attr("disabled", "disabled");
  
        var writeoff_date = $('#writeoff_date').val();
        var count_cart = $('#count_cart').val();

        
        
        if(count_cart > 0  ){
          $.ajax({
            url: "form_action.php",
            type: "POST",
            data: {
              writeoff_date: writeoff_date,
              action: 'final_writeoff'
            },
            cache: false,
            success: function(dataResult){
                $("#final_writeoff").removeAttr("disabled");
                window.location.replace("Inventory/Finished-Goods-Opening-Stock/New");
            }
          });
          }
          else{
            alert('No product added');
            $("#final_writeoff").removeAttr("disabled");
          }
        });


        
        
        $('#final_sales').on('click', function() {

          $("#final_sales").attr("disabled", "disabled");

          var invice_or_quotation = $('#invice_or_quotation').val();
          var related_id = $('#related_id').val();
          var related_code = $('#related_code').val();
          var brunch_id = $('#brunch_id').val();
          var narration = $('#narration').val();
          var creadit_limit = getNum($('#creadit_limit').val());
          var customer_due = getNum($('#customer_due').val());
                    var draft_code = $('#draft_code').val();
          var customer_id = $('#customer_id').val();
          var count_cart = $('#count_cart').val();
          var dispatch_from_which_brunch = $('#dispatch_from_which_brunch').val();
          var sales_person = $('#sales_person').val();
          var sales_by = $('#sales_by').val();

          var invoice_date = $('#invoice_date').val();
          var discount = getNum($('#discount').val());
          var transport_cost = getNum($('#total_transport_cost').val());
          var total_vat_cost = getNum($('#total_vat_cost').val());
          var invoice_payable = getNum($('#invoice_payable').val());
          var previous_discount = getNum($('#previous_discount').val());
          var previous_vat = getNum($('#previous_vat').val());


    if (checkValue('isEmpty', invice_or_quotation,'Invoice Type')) { $("#final_sales").removeAttr("disabled");
    return false; }

    if (checkValue('isEmpty', count_cart,'Cart')) { $("#final_sales").removeAttr("disabled"); return false; }

    if (checkValue('isEmpty', customer_id,'Customer Name')) { $("#final_sales").removeAttr("disabled"); return false; }

    if (checkValue('isEmpty', sales_by,'Sales By')) { $("#final_sales").removeAttr("disabled"); return false; }
    
        if (checkValue('isEmpty', sales_person,'Sales Person')) { $("#final_sales").removeAttr("disabled"); return false; }
        
        
    if (checkValue('isEmpty', invoice_date,'Invoice Date')) { $("#final_sales").removeAttr("disabled"); return false; }

  if(count_cart < 1 ){
 
 alert("Empty Cart");
 $("#final_sales").removeAttr("disabled"); return false;
  }



  if(invoice_payable > (creadit_limit - customer_due) ){
 
 alert("Please pay due");
 $("#final_sales").removeAttr("disabled"); return false;
  }


          $("#final_sales").attr("disabled", "disabled");

          
          $.ajax({
            url: "form_action.php",
            type: "POST",
            data: {
              customer_id: customer_id,
              brunch_id: brunch_id,
              related_id: related_id,
              narration: narration, 
              dispatch_from_which_brunch: dispatch_from_which_brunch,
              discount: discount,
              related_code: related_code,
              transport_cost: transport_cost,
              total_vat_cost: total_vat_cost,
              invoice_payable: invoice_payable,
              invice_or_quotation: invice_or_quotation,
              sales_by: sales_by,
              sales_person: sales_person,
              draft_code: draft_code,
              previous_discount: previous_discount,
              previous_vat: previous_vat,
              invoice_date: invoice_date,
              action: 'final_sales_invocie'
            },
            cache: false,
            dataType: 'json',
            success: function(dataResult){
                
              alert(dataResult.mess);



            if(invice_or_quotation == 'Invoice'){
          
             window.location.replace("sales/Sales-Entry/New");

            
               }else  if(invice_or_quotation == 'Quatation'){
                window.open("print.php?print=Quatation&code=" +dataResult.code );
                location.reload();
              }else{

                location.reload();


              }
            

            }

          });

          $("#final_sales").removeAttr("disabled");

        });





function invoiceOrQuat (Type){

  var related_id = $('#related_id').val();

  if(Type == 'Invoice'){
    $("#refresh_cart").load('cart_sales_entry.php?related_id=' + related_id);

  }else if(Type == 'Quotation'){
    $("#refresh_cart").load('cart_quatation_entry.php');

  }else if(Type == 'Preorder'){
    $("#refresh_cart").load('cart_preorder_invoice_entry.php');


  }else{
    $("#refresh_cart").load('cart_sales_entry.php');

  }



}





$('#recommended_price').on('keypress',function(e){

  if(e.which == 13) {
    document.getElementById("add_cart_sale").click();
  }
})





function validateCartInput(invice_or_quotation, product_id, quantity, recommended_price) {
    if (!invice_or_quotation) {
        alert('Please select invoice type');
        return false;
    }
    if (!product_id) {
        alert('একটি পণ্য  সিলেক্ট করুন');
        return false;
    }
    if (recommended_price <= 0) {
        alert('প্রস্তাবিত মূল্য খালি বা 0.00 হতে পারে না');
        return false;
    }
    if (quantity <= 0) {
        alert('Quantity cannot be empty or negative');
        return false;
    }

    return true;
}


function resetCartFields() {
    $('#product_name, #product_id, #quantity, #recommended_price, #note').val('');
    $('#stock_in_pcs, #saleable').val(0.00);
    $('#add_cart_sale').val('Add++');
    $('#related_sales_id').val('new_id');
    document.getElementById('product_name').focus();
}



function getDraftCode(CODE) {
    let customerId = ''; // Default empty value

    if (CODE !== '') {
        // Extracting the two parts
        let parts = CODE.match(/(\d+)([A-Za-z]+)(\d+)/);
        if (parts) {
            customerId = parts[3]; // The last numeric part (customer ID)
        }


        $('[id^="CurrentCart"]').removeClass('active');
        $('#CurrentCart' + CODE).addClass('active');
        $('.tab-pane').removeClass('active');
        $('#tab-second' + CODE).addClass('active');
    } else {
        customerId =  $('#customer_id').val();
    }

        // Set the value of the draft_code input field
        $('#draft_code').val(CODE);
        $('#sub_total').val($('#cart_sub_total' + CODE).val());



    // Select customer_id in dropdown
    $('#customer_id').val(customerId).trigger('change');

    sale_calculator();
}



function saveDraft() {

  var count_cart = $('#count_cart').val();
  var customer_id = $('#customer_id').val();

if(count_cart < 1 ){
 
 alert("Please add product on cart");
 return false;
  }


 $.ajax({
        url: "form_action.php",
        type: "POST",
        data: {
                action: 'save_to_draft',
            customer_id: customer_id
        },
        cache: false,
        success: function(dataResult) {
         location.reload();

        },
        error: function(xhr, status, error) {
            alert('An error occurred: ' + error);

        }
    });


}





$('#add_cart_sale').on('click', function() {
    var invice_or_quotation = $('#invice_or_quotation').val();
    var relatedId = $('#related_id').val();
    var customerId = $('#customer_id').val();
    var productId = $('#product_id').val();
    var draft_code = $('#draft_code').val();

    var note = $('#note').val();
    var quantity = getNum($('#quantity').val());
    var recommendedPrice = getNum($('#recommended_price').val());
    var saleable = getNum($('#saleable').val());
    var stock_in_pcs = getNum($('#stock_in_pcs').val());




    if (!function validateCartInput(invice_or_quotation, product_id, quantity, recommended_price) {
    if (!invice_or_quotation) {
        alert('Please select invoice type');
        return false;
    }
    if (!product_id) {
        alert('একটি পণ্য  সিলেক্ট করুন');
        return false;
    }
    if (recommended_price <= 0) {
        alert('প্রস্তাবিত মূল্য খালি বা 0.00 হতে পারে না');
        return false;
    }
    if (quantity <= 0) {
        alert('Quantity cannot be empty or negative');
        return false;
    }

    return true;
}


(invice_or_quotation, productId, quantity, recommendedPrice)) {
        return false;
    }

        if (+stock_in_pcs > 0 && (+quantity > +saleable)) {
            alert('এই পণ্যটি স্টকে পর্যাপ্ত  নেই !');
            return false;
        } else if (stock_in_pcs <= 0 && quantity > saleable) {
            return confirm("এই পণ্যটি স্টকে নেই ! আপনি কি প্রি-অর্ডার করতে চান ?");
        }
    
    
    
    $("#add_cart_sale").attr("disabled", "disabled").text("Processing...");

    $.ajax({
        url: "form_action.php",
        type: "POST",
        data: {
            invice_or_quotation: invice_or_quotation,
            related_id: relatedId,
            customer_id: customerId,
            product_id: productId,
            note: note,
            draft_code: draft_code,
            quantity: quantity,
            recommended_price: recommendedPrice,
            action: 'add_cart_sales_invocie'
        },
        cache: false,
        success: function(dataResult) {
            invoiceOrQuat(invice_or_quotation);
            resetCartFields();

            $('#mess_box').html('<strong style="color:green">' + dataResult + '</strong>');

            if (invice_or_quotation === 'Invoice') {
                setTimeout(function() {
                    var draft_code = $('#draft_code').val();
                    $('#sub_total').val($('#cart_sub_total' + draft_code).val());
                    sale_calculator();
                    $("#add_cart_sale").removeAttr("disabled").text("Add++");


getDraftCode(draft_code);
                }, 1000);





            } else {
                $("#add_cart_sale").removeAttr("disabled").text("Add++");
            }
        },
        error: function(xhr, status, error) {
            alert('An error occurred: ' + error);
            $("#add_cart_sale").removeAttr("disabled").text("Add++");
        }
    });
});

 
        
        var currentActiveIndex = 0;
        function navigate(value,event) {
     
          var items = document.getElementsByClassName("list-group-item");

          if (event.keyCode == 40) {

              // Down arrow key is pressed
              items[currentActiveIndex].classList.remove("active");
              if(currentActiveIndex == 0){
                currentActiveIndex = currentActiveIndex + 1;
             

              }else{
                currentActiveIndex = currentActiveIndex + 1;
             

              }


              items = document.getElementsByClassName("list-group-item");
              currentActiveIndex  = Math.min(Math.max(currentActiveIndex, 0), items.length - 1);
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



        function WarehouseWiseProductStockInPreinvoice(){

          
          var main_invoice_id = $('#main_invoice_id').val();
          var brunch_id = $('#dispatch_from_which_brunch').val();
          var selectedText = $('#dispatch_from_which_brunch option:selected').text();
          document.getElementById('bName').innerHTML = selectedText;

  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {

      main_invoice_id: main_invoice_id,
      brunch_id: brunch_id,
      action: 'WarehouseWiseProductStockInPreinvoice'
    },
    cache: false,
    dataType: 'json',
    success: function(dataResult){

      var responseObj = dataResult;
      var responseArray = Object.entries(responseObj.item_stock);
      responseArray.forEach(function(pair) {
        
        var sl = pair[1].sl;
        var stock = pair[1].stock;
        var row = document.getElementById("tr_no" + sl);
        var stock_cell = row.getElementsByTagName("td")[3];
        var action_cell = row.getElementsByTagName("td")[5];

        var qty = $('#total_demand' + sl).val();
     
        stock_cell.innerHTML = stock;

          if(stock > qty ){
            action_cell.innerHTML = '<label class="check"><input name="checkvalues[]" type="checkbox" class="icheckbox" value="'+sl+'" /></label>';
            $('#total_stock' + sl).val(stock);
          }else{
            action_cell.innerHTML = 'Stock Not Enough';
            $('#total_stock' + sl).val(0.00);
          }

      });
      

    }
  });


        }




        function BrunchWiseDue(){

        
          var spinner = "<div class='col-md-4'></div><div class='col-md-4'><img src='report.gif'  style='height:350px;' alt='loading...' /></div><div class='col-md-4'></div>";
          $('#laod_report').html(spinner); 	

          

          document.getElementById("search_data").disabled = true;            

          var brunch_id = $('#brunch_id').val();
          var date_to = $('#date_to').val();

  $.ajax({
    url: "brunch_report.php",
    type: "POST",
    data: {

      brunch_id: brunch_id,
      date_to: date_to,
      action: 'Brunch-Wise-Due'
    },
    cache: false,
    success: function(dataResult){

      document.getElementById("laod_report").innerHTML = dataResult;
      document.getElementById("search_data").disabled = false;            
      $('#MSalary2').DataTable();


    }
  });


        }




        function WarehouseWiseProductStockInQuatation(){

          
          var main_invoice_id = $('#main_invoice_id').val();
          var brunch_id = $('#dispatch_from_which_brunch').val();
          var selectedText = $('#dispatch_from_which_brunch option:selected').text();
          document.getElementById('bName').innerHTML = selectedText;

  $.ajax({
    url: "form_action.php",
    type: "POST",
    data: {

      main_invoice_id: main_invoice_id,
      brunch_id: brunch_id,
      action: 'WarehouseWiseProductStockInQuatation'
    },
    cache: false,
    dataType: 'json',
    success: function(dataResult){

      var responseObj = dataResult;
      var responseArray = Object.entries(responseObj.item_stock);
      responseArray.forEach(function(pair) {
        
        var sl = pair[1].sl;
        var stock = pair[1].stock;
        var row = document.getElementById("tr_no" + sl);
        var stock_cell = row.getElementsByTagName("td")[3];
        var action_cell = row.getElementsByTagName("td")[5];

        var qty = $('#total_demand' + sl).val();
     
        stock_cell.innerHTML = stock;

          if(stock > qty ){
            action_cell.innerHTML = '<label class="check"><input name="checkvalues[]" type="checkbox" class="icheckbox" value="'+sl+'" /></label>';
            $('#total_stock' + sl).val(stock);
          }else{
            action_cell.innerHTML = 'Stock Not Enough';
            $('#total_stock' + sl).val(0.00);
          }

      });
      

    }
  });


        }




        function WarehouseWiseProductStock(){

          var product_id = $('#product_id').val();
          var brunch_id = $('#dispatch_from_which_brunch').val();

          if(product_id == '' ){

          }else{
            itemstock(product_id,brunch_id,'FG','YES','NO','NO','YES','NO','NO','YES','YES');

          }

        }



        function putonsearchbar(id,name){

          var product_id = $('#'+id).val();
          var product_name = $('#'+name).val();
          var brunch_id = $('#dispatch_from_which_brunch').val();

          document.getElementById('product_name').value = product_name;
          document.getElementById('product_id').value = product_id;
        
          itemstock(product_id,brunch_id,'FG','YES','NO','NO','YES','NO','NO','YES','YES');
          document.getElementById('search-listing').innerHTML = '';
          document.getElementById("quantity").focus();

        }
        
        
  

function getEachWarehouseStock(Type,Materialid,Warehouseid,COUNT){

  var warehouse_id = $('#'+Warehouseid+''+COUNT).val();
  var material_id = $('#'+Materialid+''+COUNT).val();

  
  $.ajax({
    url: "function_tem.php",
    type: "POST",
    data: {
      Type: Type,
      warehouse_id: warehouse_id,
      material_id: material_id,
      action: 'get_Each_Warehouse_Stock'
    },
    cache: false,
    dataType:"json",
    success: function(dataResult){

      document.getElementById('warehouse_stock_pcs'+COUNT).value = dataResult.stock_pcs;

    }
  });


}

        $('#final_raw_writeoff').on('click', function() {
          $("#final_raw_writeoff").attr("disabled", "disabled");
    
          var writeoff_date = $('#writeoff_date').val();
          var count_cart = $('#count_cart').val();
  
          
          
          if(count_cart > 0  ){
            $.ajax({
              url: "form_action.php",
              type: "POST",
              data: {
                writeoff_date: writeoff_date,
                action: 'final_raw_writeoff'
              },
              cache: false,
              success: function(dataResult){
                  $("#final_raw_writeoff").removeAttr("disabled");
                  window.location.replace("Production/RAW-Opening-Stock/New");
              }
            });
            }
            else{
              alert('No product added');
              $("#final_raw_writeoff").removeAttr("disabled");
            }
          });


          function modal_wihout_refresh(page_name,get_related_id,section) {

            var related_id = $('#'+get_related_id).val();


            if(related_id == '' ){ alert('can not find data for empty data'); return false;}
            $('#exampleModalCenter').modal('show')
            document.getElementById('#exampleModalCenter')
            $('#exampleModalCenter .modal-title').html(section)
            var modal = $(this);        
            document.getElementById('dash22').innerHTML = 'Loading data...';

               $.ajax({
            type: "GET",
            url: page_name+".php",
            
            data: {
              related_id: related_id,
              section: section
            },
            cache: false,
            success: function(data) {
                console.log(data);
                document.getElementById('dash22').innerHTML = data;

                $('select').selectpicker();
            },
            error: function(err) {
                console.log(err);
            }
      
        });
        }



        function pipline_modal(page_name,product_id,brunch_id) {

          var product_id = $('#'+product_id).val();
          var brunch_id = $('#'+brunch_id).val();

          if(product_id == '' ){ alert('can not find data for empty data'); return false;}
          $('#exampleModalCenter').modal('show')
          document.getElementById('#exampleModalCenter')
          $('#exampleModalCenter .modal-title').html('Pipeline Details')
          var modal = $(this);        
          document.getElementById('dash22').innerHTML = 'Loading data...';

             $.ajax({
          type: "GET",
          url: page_name+".php",
          
          data: {
            product_id: product_id,
            brunch_id: brunch_id,
            section  : 'Pipeline Details' 
          },
          cache: false,
          success: function(data) {
              console.log(data);
              document.getElementById('dash22').innerHTML = data;

              $('select').selectpicker();
          },
          error: function(err) {
              console.log(err);
          }
    
      });
      }


        function second_modal_wihout_refresh(page_name,get_related_id,section) {

          var related_id = $('#'+get_related_id).val();

          if(related_id == '' ){ alert('can not find data for empty data'); return false;}
          $('#second_exampleModalCenter').modal('show')
          document.getElementById('#exampleModalCenter')
          $('#second_exampleModalCenter .modal-title').html(section)
          var modal = $(this);        
          document.getElementById('dash222').innerHTML = 'Loading data...';

             $.ajax({
          type: "GET",
          url: page_name+".php",
          
          data: {
            related_id: related_id,
            section: section
          },
          cache: false,
          success: function(data) {
              console.log(data);
              document.getElementById('dash222').innerHTML = data;

              $('select').selectpicker();
          },
          error: function(err) {
              console.log(err);
          }
    
      });
      }



          function itemstock(ID,BRUNCH_ID,TYPE,stock_in_pcs,stock_in_carton,warehouse_list,product_retaile_price,product_wholesale_price,stock_list,recomaned_price,saleableqty){

            $.ajax({
              url: "function_tem.php",
              type: "POST",
              data: {
                ID: ID,
                BRUNCH_ID: BRUNCH_ID,
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
        if(saleableqty == 'YES'){ 
          $('#pipe_line_stock').val( dataResult.pipe_line_stock);
          $('#saleable').val( dataResult.saleable);
        
        }

        $('select').selectpicker();


              }
            });
          }




          $('#add_cart_raw_wTow').on('click', function() {
            $("#add_cart_raw_wTow").attr("disabled", "disabled");
        
            var related_id = $('#related_id').val();
            var product_id = $('#product_id').val();
            var quantity = getNum($('#quantity').val());
            var stock_in_pcs = getNum($('#stock_in_pcs').val());
            var notes = $('#notes').val();
            var to_warehouse_id = $('#to_warehouse_id').val();
            var from_warehouse_id = $('#from_warehouse_id').val();
            var spinner = "<img src='img/owl/AjaxLoader.gif' alt='loading...' />";
        
            
        if(quantity > stock_in_pcs ){  
          alert('Can not Transfer more then Stock');
        return false; 
      }

            if(product_id!="" && quantity!="" && to_warehouse_id!="" && from_warehouse_id!="" ){
              $.ajax({
                url: "form_action.php",
                type: "POST",
                data: {
                  product_id: product_id,
                  quantity: quantity,
                  related_id: related_id,
                  notes: notes,
                  to_warehouse_id: to_warehouse_id,
                  from_warehouse_id: from_warehouse_id,
                  action: 'add_cart_raw_wTow'
                },
                cache: false,
                success: function(dataResult){
  
                  document.getElementById('mess_box').innerHTML = '<strong style="color:green"> ' + dataResult + '</strong>';
    
                  $("#add_cart_raw_wTow").removeAttr("disabled");
                  $('#quantity').val('');
                  $("#refresh_cart").html(spinner).load('raw_warehouse_to_warehouse_cart.php');
                
                  setTimeout(function(){
                    document.getElementById('mess_box').innerHTML = '';
                 }, 2000);
    
                
                }
              });
              }
              else{
                alert('Please fill all the field !');
                $("#add_cart_raw_wTow").removeAttr("disabled");
              }
            });



           


            function add_cart_fg_wTow(){

              $("#addcartfgwTow").attr("disabled", "disabled");
              var related_id = $('#related_id').val();
              var product_id = $('#product_id').val();
              var quantity = $('#quantity').val();
              var notes = $('#notes').val();
              var to_warehouse_id = $('#to_warehouse_id').val();
              var from_warehouse_id = $('#from_warehouse_id').val();

              var spinner = "<img src='img/owl/AjaxLoader.gif' alt='loading...' />";
          
              
              if(product_id!="" && quantity!="" && to_warehouse_id!="" && from_warehouse_id!="" ){
                $.ajax({
                  url: "form_action.php",
                  type: "POST",
                  data: {
                    product_id: product_id,
                    quantity: quantity,
                    related_id: related_id,
                    notes: notes,
                    to_warehouse_id: to_warehouse_id,
                    from_warehouse_id: from_warehouse_id,
                    action: 'add_cart_fg_wTow'
                  },
                  cache: false,
                  success: function(dataResult){
    
                    document.getElementById('mess_box').innerHTML = '<strong style="color:green"> ' + dataResult + '</strong>';
      
                    $("#addcartfgwTow").removeAttr("disabled");
                    $('#quantity').val('');
                    $("#refresh_cart").html(spinner).load('warehouse_to_warehouse_cart.php');
                  
                    setTimeout(function(){
                      document.getElementById('mess_box').innerHTML = '';
                   }, 2000);
      
                  
                   $('#product_id').focus();
                   $('#product_id').select('open'); // Assuming you are using a select plugin like Select2
                   $('[tabindex="1"]').focus();

                  }
                });
                }
                else{
                  alert('Please fill all the field !');
                  $("#addcartfgwTow").removeAttr("disabled");
                }

            }



                

                  $('#final_wTow').on('click', function() {
            $("#final_wTow").attr("disabled", "disabled");
      
            var send_date = $('#send_date').val();
            var count_cart = $('#count_cart').val();
            var dispatcher_id = $('#dispatcher_id').val();
            var related_invoice_id = $('#related_invoice_id').val();
            
            if(count_cart > 0 ){
              $.ajax({
                url: "form_action.php",
                type: "POST",
                data: {
                  send_date: send_date,
                  dispatcher_id: dispatcher_id,
                  related_invoice_id: related_invoice_id,
                  action: 'final_wTow'
                },
                cache: false,
                dataType: 'json',
                success: function(dataResult){
                    $("#final_wTow").removeAttr("disabled");
                    alert(dataResult.mess);
                    window.open("print.php?print=FG-WAREHOUSE-TO-WAREHOUSE-TRANSFER-RECEIPT&code=" +dataResult.code );
                    window.location.replace("Inventory/Finished-Goods-Warehouse-To-Warehouse/New");
                }
              });
              }
              else{
                alert('No product added');
                $("#final_wTow").removeAttr("disabled");
              }
            });



            $('#final_raw_wTow').on('click', function() {
              $("#final_raw_wTow").attr("disabled", "disabled");
        
              var send_date = $('#send_date').val();
              var count_cart = $('#count_cart').val();
  
              
              if(count_cart > 0 ){
                $.ajax({
                  url: "form_action.php",
                  type: "POST",
                  data: {
                    send_date: send_date,
                    action: 'final_raw_wTow'
                  },
                  cache: false,
                  success: function(dataResult){
                      $("#final_raw_wTow").removeAttr("disabled");
                      window.location.replace("Inventory/Raw-Material-Warehouse-To-Warehouse/New");
                  }
                });
                }
                else{
                  alert('No product added');
                  $("#final_raw_wTow").removeAttr("disabled");
                }
              });


    $('#save_product').on('click', function() {

  
      $("#save_product").attr("disabled", "disabled");

      var related_id = $('#related_id').val();
      var product_name = $('#product_name').val();
      var new_category_name = $('#new_category_name').val();
      var get_category_id = $('#category_id').val();
      var unit_id = $('#unit_id').val();
      var pcs_in_cartoon = $('#pcs_in_cartoon').val();
      var sales_rate = $('#sales_rate').val();
      var wholesale_rate = $('#wholesale_rate').val();
      var product_code = $('#product_code').val();
      var safety_stock = $('#safety_stock').val();

      if(document.getElementById("in_service").checked){
        var in_service = 'checked';
        }else{
          var in_service = '';
        }
      

      if(product_name!="" && unit_id!="" && pcs_in_cartoon!="" && safety_stock!=""  ){


      if(document.getElementById('target3').checked){
    

        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            category_name: new_category_name,
            action: 'save_category',
            related_id: 'new_id'
          },
          cache: false,
          success: function(dataResult){
            
            const myArr = dataResult.split("_SAJID_");
            var category_id = myArr[1];
          }
        });
      }else{
      
        var category_id = get_category_id;
      }

      
        $.ajax({
          url: "form_action.php",
          type: "POST",
          data: {
            product_name: product_name,
            category_id: category_id,
            unit_id: unit_id,
            safety_stock: safety_stock,
            pcs_in_cartoon: pcs_in_cartoon,
            action: 'save_product',
            related_id: related_id,
            sales_rate: sales_rate,
            wholesale_rate: wholesale_rate,
            in_service: in_service,
            product_code: product_code


          },
          cache: false,
          success: function(dataResult){
             
             window.location.replace("Setup/Product-Setup/New");
          
          }
        });
        }
        else{
          alert('Please fill all the field !');
          $("#save_product").removeAttr("disabled");
        }
      });
  


      $('#save_raw_material').on('click', function() {

  
        $("#save_raw_material").attr("disabled", "disabled");
        
        var related_id = $('#related_id').val();
        var product_name = $('#product_name').val();
        var weight = $('#weight').val();
        var new_category_name = $('#new_category_name').val();
        var get_category_id = $('#category_id').val();
        var unit_id = $('#unit_id').val();
        var pcs_in_cartoon = $('#pcs_in_cartoon').val();
        var product_code = $('#product_code').val();
        var supporting_product = $('#supporting_product').val();
        var spray_product = $('#spray_product').val();
        var print_product = $('#print_product').val();
        var mold_product = $('#mold_product').val();
        var minimum_stock_qty = $('#minimum_stock_qty').val();

        
       
        if(product_name!="" && unit_id!="" && pcs_in_cartoon!=""  ){
        
        
        if(document.getElementById('target3').checked){
        
          $.ajax({
            url: "form_action.php",
            type: "POST",
            data: {
              category_name: new_category_name,
              action: 'create_raw_category',
              related_id: 'new_id'

            },
            cache: false,
            success: function(dataResult){
              
              const myArr = dataResult.split("_SAJID_");
              var category_id = myArr[1];
            }
          });
        }else{
        
          var category_id = get_category_id;
        }
        
        
          $.ajax({
            url: "form_action.php",
            type: "POST",
            data: {
              product_name: product_name,
              category_id: category_id,
              unit_id: unit_id,
              pcs_in_cartoon: pcs_in_cartoon,
              action: 'save_raw_material',
              related_id: related_id,
              mold_product: mold_product,
              weight: weight,
              product_code: product_code,
              supporting_product:supporting_product,
              spray_product: spray_product,
              print_product: print_product,
              minimum_stock_qty: minimum_stock_qty
        
        
            },
            cache: false,
            success: function(dataResult){
               alert(dataResult);
               window.location.replace("Recipe/Raw-Material-Setup/New/New");
            
            }
          });
          }
          else{
            alert('Please fill all the field !');
            $("#save_raw_material").removeAttr("disabled");
          }
        });


        
    $('#company_logo').change(function() {


      var file_data = $('#company_logo').prop('files')[0];
      var form_data = new FormData();
      
      
      form_data.append('fileToUpload', file_data);
      form_data.append('file_section', 'Company_Logo');
      document.getElementById("load_msg_company_logo").innerHTML = '<font color=red>loading...</font>';
      
      $.ajax({
      
      url: "upload_file.php",
      type: "POST",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
      success: function(data) {
        alert(data);
      
        document.getElementById("load_msg_company_logo").innerHTML = '<font color=red>File save failed</font>';
        if (data == 'Uplaod Success') {
      
        document.getElementById("load_msg_company_logo").innerHTML = '<font color=green>Upload Success</font>';
        document.getElementById("company_logo").value = "";
      
      }
        
      }
      });
      });




    $('#invoice_header').change(function() {


      var file_data = $('#invoice_header').prop('files')[0];
      var form_data = new FormData();
      
      
      form_data.append('fileToUpload', file_data);
      form_data.append('file_section', 'Invoice_Header');
      document.getElementById("load_msg_invoice_header").innerHTML = '<font color=red>loading...</font>';
      
      $.ajax({
      
      url: "upload_file.php",
      type: "POST",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
      success: function(data) {
        alert(data);
      
        document.getElementById("load_msg_invoice_header").innerHTML = '<font color=red>File save failed</font>';
        if (data == 'Uplaod Success') {
      
        document.getElementById("load_msg_invoice_header").innerHTML = '<font color=green>Upload Success</font>';
        document.getElementById("invoice_header").value = "";
      
      }
        
      }
      });
      });

      

function convert_customer_demand_to_invoice(){
    
    
    

  cart2 = [];

  var total_item = $('#total_item').val();
  var demand_from_brunch_id = $('#demand_from_brunch_id').val();
  var demand_to_brunch_id = $('#demand_to_brunch_id').val();

    var demand_id = $('#demand_id').val();

  var dispatcher_id = $("[name^='Seletdispatcher_id']").map(function() { return $(this).val() }).get();

  if (dispatcher_id ==  '') { alert("Select Dispatcher "); return false; }

  if (total_item ==  0) { alert("No Item Added"); return false; }


  for (var i = 0; i < total_item; i++) {
    var element2 = {};

    var product_id = $('#product_id' + [i]).val();
    var demand_qty = getNum($('#demand_quantity' + [i]).val());
    var sales_rate = getNum($('#sales_rate' + [i]).val());
    var dispatch_from_warehouse = $('#dispatch_from_warehouse' + [i]).val();
    var received_warehouse = $('#received_warehouse' + [i]).val();
    var related_id = $('#related_id' + [i]).val();

    

 
    var selectedOption = document.getElementById("dispatch_from_warehouse" + [i] );
    var stockValue = getNum(selectedOption.options[selectedOption.selectedIndex].getAttribute("data-stock"));
  
if( dispatch_from_warehouse == 'DONT' ){



}else{

  
  if (( received_warehouse == 'DONT' )) {
    alert('Select a to Warehouse');
    $("#action_bar").removeAttr("disabled");
    return false;
    }


  if (( +demand_qty > 0 ) && ( +demand_qty > +stockValue )) {
    alert('Not Enough in warehouse');
    $("#action_bar").removeAttr("disabled");
    return false;
}

}



    element2.product_id = product_id;
    element2.demand_qty = demand_qty;
    element2.sales_rate = sales_rate;
    element2.dispatch_from_warehouse = dispatch_from_warehouse;
    element2.received_warehouse = received_warehouse;
    element2.related_id = related_id;

    cart2.push({ element2: element2 });
}



console.log(JSON.stringify(cart2));
$.ajax({
  url: "form_action.php",
  type: "POST",
  data: {
    today_data: JSON.stringify(cart2),
    demand_id: demand_id,
    total_item: total_item,
    demand_from_brunch_id: demand_from_brunch_id,
    demand_to_brunch_id: demand_to_brunch_id,
    dispatcher_id: dispatcher_id,
    action: 'Save Demand Delivery Copy'
  },
  cache: false,
  success: function(dataResult){

  alert(dataResult);     
  location.reload();
  
  }
});


}



      $('#invoice_footer').change(function() {


        var file_data = $('#invoice_footer').prop('files')[0];
        var form_data = new FormData();
        
        
        form_data.append('fileToUpload', file_data);
        form_data.append('file_section', 'Invoice_Footer');
        document.getElementById("load_msg_invoice_footer").innerHTML = '<font color=red>loading...</font>';
        
        $.ajax({
        
        url: "upload_file.php",
        type: "POST",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
          alert(data);
        
          document.getElementById("load_msg_invoice_footer").innerHTML = '<font color=red>File save failed</font>';
          if (data == 'Uplaod Success') {
        
          document.getElementById("load_msg_invoice_footer").innerHTML = '<font color=green>Upload Success</font>';
          document.getElementById("invoice_footer").value = "";
        
        }
          
        }
        });
        });
  
        


        function doneFinalAction(id){

          var x = window.confirm("Are you sure to Close the Demand?");
          if(x){
          $.ajax({
            url: "function_tem.php",
            type: "POST",
            data: {
              id: id,
              action: 'DONE_DEMAND'
            },
            cache: false,
            success: function(html){
              location.reload();   
            }
        
          });
          }



        }




        function find_related_data(TABLE,FIELDNAME,FIELD,VALUE){

          $.ajax({
            url: "function_tem.php",
            type: "POST",
            data: {
              TABLE: TABLE,
              FIELD: FIELD,
              VALUE: VALUE,
              action: 'find_related_data'
            },
            cache: false,
            success: function(dataResult){
                $('#Load_'+TABLE+'_level').html(FIELDNAME); 	
                $('#Load_'+TABLE+'_value').html(dataResult); 	

                $('select').selectpicker();

            }

          });
        }


function calTada(){

  var present_salary = getNum($('#present_salary').val());
  var basic = 0.5*present_salary;
  var medical = 0.16*basic;
  var house_rent = 0.34*basic;
  var over_time_bill = 0.5*basic;


  document.getElementById("basic").value=basic;
  document.getElementById("medical").value=medical;
  document.getElementById("house_rent").value=house_rent;
  document.getElementById("over_time_bill").value=over_time_bill;



}

        $('#save_employee_data').on('click', function() {

  var related_id = $('#related_id').val();
  var name =$('#name').val();
  var joining_department = $('#joining_department').val();
  var present_department = $('#present_department').val();
  var present_section = $('#present_section').val();
  var joining_section = $('#joining_section').val();
  var designation = $('#designation').val();
  var joining_designation = $('#joining_designation').val();
  var fa_name = $('#fa_name').val();
  var mo_name = $('#mo_name').val();
  var mob_no = $('#mob_no').val();
  var nationality = $('#nationality').val();
  var division_id = $('#division_id').val();
  var district_id = $('#district_id').val();
  var upazila_id = $('#upazilla_id').val();
  var union_id = $('#union_id').val();
  var show_code = $('#show_code').val();

var gender = $('#gender').val();
var matrial_status = $('#matrial_status').val();
var referrer = $('#referrer').val();
var supervisor = $('#supervisor').val();
var nominee_information = $('#nominee_information').val();
var bank_account = $('#bank_account').val();



  var village = $('#village').val();
  var house = $('#house').val();
  var nid = $('#nid').val();
  var religion = $('#religion').val();
  var email = $('#email').val();
  var edu_qul = $('#edu_qul').val();
  var previous_company = $('#previous_company').val();
  var joining_salary = $('#joining_salary').val();
  var present_salary = $('#present_salary').val();
  var house_rent = $('#house_rent').val();
  var da = $('#da').val();
  var ta = $('#ta').val();
  var provident_fund = $('#provident_fund').val();

  var basic = $('#basic').val();
  var over_time_bill = $('#over_time_bill').val();
  var join_d = $('#join_d').val();
  var medical = $('#medical').val();
  var po_office = $('#po_office').val();
  var birth_date = $('#birth_date').val();
  
  
  var conf_d = $('#conf_d').val();
  var generate_salary_sheet = $('#generate_salary_sheet').val();
 
  if( name == ""){ alert('Name cant not empty'); return false ;}
  if( joining_department == ""){ alert('joining department cant not empty'); return false ;}
  if( present_department == ""){ alert('Present department cant not empty'); return false ;}
  if( mob_no == ""){ alert('Mobile cant not empty'); return false ;}
  if( joining_designation == ""){ alert('joining designation cant not empty'); return false ;}
  if( designation == ""){ alert('Present designation cant not empty'); return false ;}
  if( nid == ""){ alert('NID cant not empty'); return false ;}
  if( medical == ""){ alert('medical cant not empty'); return false ;}
  if( house_rent == ""){ alert('House Rent cant not empty'); return false ;}
  if( basic == ""){ alert('night hold cant not empty'); return false ;}
  if( joining_salary == ""){ alert('joining salary cant not empty'); return false ;}
  if( present_salary == ""){ alert('present salary cant not empty'); return false ;}
  

 
  $.ajax({
              url: "form_action.php",
              type: "POST",
              data: { 
          
                action: 'save_employee_data',
                related_id : related_id ,
                show_code: show_code,
                birth_date: birth_date,
                name : name ,
                division_id: division_id,
                district_id: district_id,
                upazila_id: upazila_id,
                union_id: union_id,
                joining_department : joining_department ,
                present_department : present_department ,
                present_section : present_section ,
                joining_section : joining_section ,
                designation : designation ,
                joining_designation : joining_designation ,
                fa_name : fa_name ,
                mo_name : mo_name ,
                mob_no : mob_no ,
                nationality : nationality ,
                village : village ,
                po_office : po_office ,
                house : house ,
                nid : nid ,
                religion : religion ,
                email : email ,
                edu_qul : edu_qul ,
                previous_company : previous_company ,
                joining_salary : joining_salary ,
                present_salary : present_salary ,
                house_rent : house_rent ,
                medical : medical ,
                da : da ,
                ta: ta,
                provident_fund: provident_fund,
                basic : basic ,
                gender: gender,
                matrial_status: matrial_status,
                referrer: referrer,
                supervisor: supervisor,
                nominee_information: nominee_information,
                bank_account: bank_account,
                over_time_bill : over_time_bill ,
                join_d : join_d ,
                conf_d: conf_d,
                generate_salary_sheet: generate_salary_sheet

              },
              cache: false,
              success: function(dataResult){
                alert(dataResult);
                  $("#save_employee_data").removeAttr("disabled");
	
                window.location.replace("HRM/Employee-Profile/New");
              
                
                
              }
            });

          });
        

function pokeMess(){

  var report_wise_code = $('#report_wise_code').val();
  var related_id = $('#' + report_wise_code).val();
  var poke_mess = $('#poke_mess').val();

  if( report_wise_code === undefined && related_id === undefined ){ alert('Select Type'); return false ;}

  if( report_wise_code == "All" && related_id === undefined ){ }else{

    if(related_id == ''){
      alert('Select Whome to send mess'); return false ;

    }
  }

  if( poke_mess == ""){ alert('Mess cant empty'); return false ;}


  $.ajax({
    url: "notification/poke.php",
    type: "POST",
    data: {
      report_wise_code: report_wise_code,
      related_id: related_id,
      poke_mess: poke_mess
      
    },
    cache: false,
    dataType: 'json',
    success: function(dataResult){
      

    }

  });



  
}



        function get_report(REPORTNAME,FROM_F,TO_F,ID){

          var FROM = $('#' + FROM_F + '').val();
          var TO = $('#' + TO_F + '').val();


           $.ajax({
            url: "function_tem.php",
            type: "POST",
            data: {
              ID: ID,
              FROM: FROM,
              TO: TO,
              action: REPORTNAME
            },
            cache: false,
            success: function(dataResult){
                $('#Load_'+REPORTNAME+'_div').html(dataResult); 	
            }

          });


        }


      function HIDE_AND_SHOW(DIV,IDNAME,DISABLEID){

      var check_status =document.getElementById(IDNAME).checked;

      if(check_status == true){
      document.getElementById(DIV).style.display = 'block';
      document. getElementById(DISABLEID). setAttribute("disabled", "disabled");

      }else{
      document.getElementById(DIV).style.display = 'none';
      document. getElementById(DISABLEID). removeAttribute("disabled");

      }
      }


      

        function search_bar(str,COUNT) {



  if (str.length==0) {
    document.getElementById('livesearch'+COUNT).innerHTML="";
    document.getElementById('livesearch'+COUNT).style.border="0px";
    return;
  }
  var xmlhttp=new XMLHttpRequest();
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {

      document.getElementById('livesearch'+COUNT).innerHTML=this.responseText;
      document.getElementById('livesearch'+COUNT).style.border="1px solid black";
   
      
}else{

    }
  }


  xmlhttp.open("GET","livesearch_product.php?rowcount="+COUNT+"&q="+str,true);
  xmlhttp.send();



    
}
      

function putValueInSerachBar(COUNT,code,product_id,product_name,category,wholesale_rate,sales_rate,pcs_in_cartoon,unit){


  $('#written_value').val(product_name);
  var related_id  = $('#related_id').val();
  var purches_date  = $('#purches_date').val();
  var invoice_no  = $('#invoice_no').val();


  $.ajax({
    type: "POST",
    url: 'function/function_tem.php',
    data:{
      action:'add_cart_purches',
      product_id: product_id,
      related_id: related_id,
      purches_date: purches_date,
      invoice_no: invoice_no
     
     },
    success:function(html) {
  
      $('#written_value').val('');
  
      if(myArr[1] == 'add cart success' ){
  
      }else{
        
  
        setTimeout(function(){
          document.getElementById('cart_delete_box').innerHTML = '';
        }, 2000);
      }
     
    }
  
  });

  document.getElementById('livesearch'+COUNT).innerHTML = '';


 }
 


function get_placement(EID,placement){

  var column_no = $('#column_no').val();

  for(var i = 1; i <= column_no; i++) {
    
    $('#employee_id'+placement+'_'+i).val(EID);

  }



}

function putValueonPlacement(NOTES,placement){

  var column_no = $('#column_no').val();

  for(var i = 1; i <= column_no; i++) {
    
    $('#note'+placement+'_'+i).val(NOTES);

  }



}




function addField(){
   var column_no = $('#column_no').val();
   var count = $('#row_count').val();

  count++;
  $('#row_count').val(count);
  var html_code = '';

  html_code += '<tr  class="tr'+ count +'" id="row_id_' + count + '">';

  $.ajax({
    type: "POST",
    url: 'function_tem.php',
    data:{
      action:'get_employee_data',
      count: count
     },
    success:function(html) {
     
      document.getElementById('em_list_'+count).innerHTML = html;
      $('select').selectpicker();
    }
  
  });

  html_code += '<td id="em_list_'+count+'">';
  html_code += '</td>';

html_code += '<td><input type="text" name="get_note[]"  onkeyup="putValueonPlacement(this.value,\''+count+'\');" id="note' + count + '" class="form-control input-sm note" value="" />';
html_code += '</td>';

for(var i = 0; i < column_no; i++) {


  placement = i+1;
  var pi = $('#get_product_id'+placement).val();
  var fl = $('#get_ftting_left'+placement).val();

   

html_code += '<td>';
html_code += '<input type="number" name="done_qty[]" id="done_qty'+count+'_'+placement+'" data-srno="'+count+'_'+placement+'" value="" class="form-control  done_qty"/>';

html_code += '<input type="hidden" name="product_id[]" id="product_id'+count+'" data-srno="'+count+'" value="'+pi+'" class="form-control number_only product_id"/>';

html_code += '<input type="hidden" name="employee_id[]" id="employee_id'+count+'_'+placement+'" data-srno="'+count+'" value="" class="form-control number_only employee_id'+count+'_'+placement+'"/>';

html_code += '<input type="hidden" name="ftting_left[]" id="ftting_left'+count+'_'+placement+'"  data-srno="'+count+'" value="'+fl+'" class="form-control number_only ftting_left"/>';

html_code += '<input type="hidden" name="note[]" id="note'+count+'_'+placement+'" data-srno="'+count+'" value="" class="form-control number_only note'+count+'_'+placement+'"/>'; 
html_code += '</td>';


}



html_code += '<td><button type="button" name="remove_row" id="' + count + '" class="btn btn-danger btn-xs remove_row"> X </button></td>';
html_code += '</tr>';
$('#invoice-item-table').append(html_code);

}


$(document).on('click', '.remove_row', function () {
  var row_id = $(this).attr("id");
  $('#row_id_' + row_id).remove();
  row_id--;
  $('#row_count').val(row_id);
});





function findRecipeSetupOrnot(WHICH,FIELD,VALUE){

  $.ajax({
    type: "POST",
    url: 'function_tem.php',
    data:{
      action:'find_recipe',
      VALUE: VALUE,
      WHICH: WHICH,
      FIELD: FIELD
     },
     dataType: 'json',
              cache: false,
    success:function(html) {
     

      document.getElementById('previous_recipe').value = html.value;
      document.getElementById('mess_load').innerHTML = html.mess; 
    }
  
  });


}



function saveFittingData(){

var code = $('#code').val();
var row_count = $('#row_count').val();
var column_no = $('#column_no').val();

var employee_id = $("[name^='employee_id']").map(function() { return $(this).val() }).get();
var product_id = $("[name^='product_id']").map(function() { return $(this).val() }).get();
var note = $("[name^='note']").map(function() { return $(this).val() }).get();
var done_qty = $("[name^='done_qty']").map(function() { return $(this).val() }).get();
var total_loop = employee_id.length;

if (row_count ==  0) { alert("No Item Added"); return false; }

for (var no1 = 0; no1 < column_no; no1++) {
  sum = 0;
  placement1 = no1+1;


  for (var no2 = 0; no2 < row_count; no2++) {


      placement2 = no2+1;
      each_done_qty = $('#done_qty'+placement2+'_'+placement1).val();
      sum = +each_done_qty + +sum ;

      if(sum > $('#ftting_left'+placement2+'_'+placement1).val() ){
                alert('Done Quantity must be less then Total Fitting');
                return false;
      }

      if(each_done_qty === 0 ||  each_done_qty === ''){
        alert('Done Quantity cant 0');
        return false;
}



      if(  $.trim($('#employee_id' + placement2 + '_' + placement1 ).val()) == '' ){
       
        alert("Please Select Employee");
        $('#employee_id' + placement2 + '_' + placement1 ).val().focus();
        return false;
  
      }
  }



}








$.post('form_action.php',{

  action: 'fitting_action',
  code: code,
  total_loop: total_loop,
  employee_id: employee_id,
  note: note,
  product_id:product_id,
  done_qty: done_qty


},function(result){
alert(result);
location.reload();
});

}



function get_pending_post(transection_type,data_inserted_from,link){

  $.ajax({
    url: "function_tem.php", 
    type: "POST",
    data: {
            action: 'get_pending_post',
            transection_type: transection_type,
            link: link,
            data_inserted_from: data_inserted_from

    },
    cache: false,
    dataType: 'json',
    success: function(dataResult){

      document.getElementById('load_pending_post').innerHTML = dataResult.content;
      $('#myDatatable').DataTable();

    }
  });



}


function POST_DATA(ID){


  transaction_type = $('#transaction_type').val();
  data_inserted_from = $('#data_inserted_from').val();

  $.ajax({
    url: "function_tem.php", 
    type: "POST",
    data: {
            action: 'POST_DATA',
            ID: ID,
            transaction_type: transaction_type,
            data_inserted_from: data_inserted_from


    },
    cache: false,
    dataType: 'json',
    success: function(dataResult){
       alert( dataResult.content1);
      get_pending_post('BOTH',data_inserted_from,'');
      document.getElementById('pending_data_cal_id').value = dataResult.content2;


    }
  });





}