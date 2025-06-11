 <?php

use App\Model\Entitlements;
use App\Model\AssignLeave;

?>
  <!DOCTYPE html>
  <html>
  <head>
  <style>
  table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
  }
  </style>
  </head>
  <body>

  <h2>LEAVES BALANCE REPORT</h2> 
  <table style="width:100%;font-size: 13px;margin-top: 7px;">
    <tr>
      <th align="left" scope="col">Emp No</th>
            <th align="left" scope="col" style="padding: 3px;">Employee</th>
            <th align="left" scope="col" style="padding: 3px;">Leave Type</th>
            <th align="left" scope="col" style="padding: 3px;">Opening Bal.</th>
            <th align="left" scope="col" style="padding: 3px;">Accrued</th>
            <th align="left" scope="col" style="padding: 3px;">Days Taken</th>
            <th align="left" scope="col" style="padding: 3px;">Balance</th>
    </tr>
     @foreach($leaveDataAssignBalacePdf as $value)

      <?php
          $assignLeave = AssignLeave::where('emp_id',$value->employee_id)->groupBy('emp_id')->selectRaw('*,sum(day_taken) as total')->first();
          $val [] = $value->opening_balance +  $value->default_entitlement - @$assignLeave->total;

       ?>

        <tr>
          <td style="padding:3px;">{{$value->EmpDataGet ? $value->EmpDataGet->staff_number : 'NA'}}</td>
          <td style="padding:3px;">{{$value->EmpDataGet ? $value->EmpDataGet->first_name : 'NA'}} {{$value->EmpDataGet ? $value->EmpDataGet->middle_name : 'NA'}} {{$value->EmpDataGet ? $value->EmpDataGet->last_name : 'NA'}}</td>
          <td style="padding:3px;">{{$value->LeaveDataGet ? $value->LeaveDataGet->leave_type : 'NA'}}</td>
          <td style="padding:3px;">{{$value->opening_balance}}</td>
          <td style="padding:3px;">{{$value->default_entitlement}}</td>
          <td style="padding:3px;">{{@$assignLeave->total}}</td>
          <td style="padding:3px;">{{$value->opening_balance +  $value->default_entitlement - @$assignLeave->total }}</td>
        </tr>
        @endforeach
  </table>
  <h4 style="text-align: right;margin-top: 0px;">Grand Total : {{array_sum($val)}}</h4>

  </body>
  </html>
