<?php
include_once("auth.php");
include('function_query.php');


$query = $conn_me->prepare("SELECT *  FROM `quick_pay_slip` where `id` = '".$_GET['id']."' ");
$query->execute();
$fetch = $query->fetch(PDO::FETCH_ASSOC);


$basic = number_format((float)(0.5*$fetch['Salary']), 2, '.', '');
$medical =  number_format((float)(0.16*$basic), 2, '.', '');
$house_rent =  number_format((float)(0.34*$basic), 2, '.', '');
$over_time_bill = number_format((float)(0.5*$basic), 2, '.', ''); 


$total_income  = number_format((float)($basic+$house_rent+$medical+$over_time_bill), 2, '.', '');
$total_expense = number_format((float)($fetch['ProvidentFund']+$fetch['AdvanceAdjustment']+$fetch['FebruaryAdvance']+$fetch['AbsentLateDeduction']), 2, '.', '');

$net_income = number_format((float)($total_income - $total_expense), 2, '.', '');
?>

<link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>



<table class="table table-condensed  table-bordered">
<tr>
    <td style="text-align:center" colspan="4"><b>REMOTE CENTER</b></td>
</tr>
<tr>
    <td style="text-align:center"  colspan="4"><i>A Sister Concern of RC Star Industry Ltd.</i></td>
</tr>

<tr>
    <td>Date of Payment</td>
    <td><?php print date('d-M-Y');?></td>
    <td>Employee Name</td>
    <td><?php print $fetch['name'];?></td>
</tr>

<tr>
    <td>Pay Period</td>
    <td><?php print $fetch['PayPeriod'];?></td>
    <td>Designation</td>
    <td><?php print $fetch['designation'];?></td>
</tr>

<tr>
    <td>Working Days</td>
    <td><?php print $fetch['WorkingDays'];?></td>
    <td>Department</td>
    <td><?php print $fetch['dep'];?></td>
</tr>
<tr><td colspan="4"></td></tr>
<tr><td colspan="4"></td></tr>
<tr>
    <th>Earnings</th>
    <th>Amount</th>
    <th>Deductions</th>
    <th>Amount</th>
</tr>




<tr>
    <td>Basic</td>
    <td><?php print $basic;?></td>
    <td>Provident Fund</td>
    <td><?php print number_format((float)($fetch['ProvidentFund']), 2, '.', '');?></td>
</tr>

<tr>
    <td>House Rent Allowance</td>
    <td><?php print $house_rent;?></td>
    <td>Advance Adjustment</td>
    <td><?php print number_format((float)($fetch['AdvanceAdjustment']), 2, '.', '');?></td>

</tr>

<tr>
    <td>Medical</td>
    <td><?php print $medical;?></td>
    <td>Loan/Salary Advance </td>
    <td><?php print number_format((float)($fetch['FebruaryAdvance']), 2, '.', '');?></td>

</tr>
<tr>
    <td>Transport Allowance</td>
    <td><?php print 0.00;?></td>
    <td>Absent</td>
    <td><?php print number_format((float)($fetch['AbsentLateDeduction']), 2, '.', '');?></td>

</tr>


<tr>
    <td>Overtime</td>
    <td><?php print $over_time_bill;?></td>
    <td>Late Attendance</td>
    <td><?php print 0.00;?></td>
</tr>

<tr>
    <td>Other</td>
    <td><?php print 0.00;?></td>
    <td></td>
    <td></td>
</tr>

<tr>
    <td>Total Earnings</td>
    <td><?php print number_format((float)($total_income), 2, '.', '');?></td>
    <td>Total Deductions</td>
    <td><?php print number_format((float)($total_expense), 2, '.', '');?></td>
</tr>

<tr>
    <td></td>
    <td></td>
    <th>Net Salary</th>
    <th><?php print ceil(number_format((float)($net_income), 2, '.', ''));?></th>
</tr>

<tr>
    <th>Bank (DBBL Payroll)</th>
    <th><?php print number_format((float)($total_income), 2, '.', '');?></th>
    <th>Cash</th>
    <th><?php print number_format((float)($total_expense), 2, '.', '');?></th>
</tr>


<tr><td colspan="4" style="text-align:center"><?php print strtoupper(FIND::numberTowords(ceil($net_income)));?></td></tr>

<tr><td colspan="4"></td></tr>
<tr><td colspan="4"></td></tr>
<tr><td colspan="4"></td></tr>

<tr>
    <td style="text-align:center">Authorised Signature</td>
    <td></td>
    <td></td>
    <td style="text-align:center">Employee Signature</td>

</tr>

</table>





<script type="text/javascript">

   // setTimeout(function (){
      //   window.print();
    //      }, 1000); 
  
  
    </script>

