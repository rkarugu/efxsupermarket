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

  <h2>LEAVES HISTORY REPORT</h2>

  <table style="border:0px !important;">
         <tr style="border:0px !important;">
            <td style="padding: 0px;border:0px !important;"><b>From:</b> {{$filerArray['from']}}
            </td>
            <td style="border:0px !important;"><b>To:</b> {{$filerArray['to']}}</td>
         </tr>
      </table>  
  <table style="width:100%;font-size: 13px;margin-top: 7px;">
    <tr>
      <th style="padding: 3px;">Emp No</th>
      <th style="padding: 3px;">Employee</th> 
      <th style="padding: 3px;">Leave Type</th>
      <th style="padding: 3px;">From</th>
      <th style="padding: 3px;">To</th>
      <th style="padding: 3px;">Total Days</th>
      <th style="padding: 3px;">Status</th>
    </tr>
     @foreach($leaveDataAssignPdf as $value)
        <tr>
          <td style="padding: 3px;">{{$value->EmpData ? $value->EmpData->staff_number : 'NA'}}</td>
          <td style="padding: 3px;">{{$value->EmpData ? $value->EmpData->first_name : 'NA'}} {{$value->EmpData ? $value->EmpData->middle_name : 'NA'}} {{$value->EmpData ? $value->EmpData->last_name : 'NA'}}</td>
          <td style="padding: 3px;">{{$value->LeaveDataGet2 ? $value->LeaveDataGet2->leave_type : 'NA'}}</td>
          <td style="padding: 3px;">{{$value->from}}</td>
          <td style="padding: 3px;">{{$value->to}}</td>
          <td style="padding: 3px;">{{$value->day_taken}}</td>
          <td style="padding: 3px;">{{$value->manager_status}}</td>
        </tr>
        @endforeach
  </table>

  </body>
  </html>
