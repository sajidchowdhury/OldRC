<ul class="breadcrumb">
    <li><a>HRM </a></li>           
    <li><a>Report </a></li>          
    <li class="active"><?php print $_GET['page_identity'];?></li>
</ul>

<div class="row animated bounceIn">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-body">
            <form id="myform">
            
            <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
          </div>

          <div class="col-md-4"></div>
            <div class="col-md-4">
            <div class="table-responsive">

            <table class="table table-hover table-condensed" >

           
                    
                    <tr>
                  <th>Salary Month</th>

                        <td  colspan="2"><input  type="text" value="<?php print date('m-Y');?>" id="date_from" class="monthonly form-control text-danger" >
                        <input  type="hidden" value="" id="date_to" class="" >
</td>
</tr>
<tr>
                 

                        <td  colspan="3"><input  type="button" id="search_data" value="Search Data" onclick="generateReport('SALARY-PAYMERT','')" class="btn btn-info block" ></td>
</tr>
                
      
          </table>
</div>
</div>
          <div class="col-md-4"></div>

        </form>  
                
        </div>

    </div>
</div>
</div>


<div class="row" style="background-color: white;">
<div class="panel-heading">
                                                         
                                    
                                </div>
        <div class="panel-body"  id="laod_report">

       
      </div>
            

</div>
