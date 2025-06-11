  <!DOCTYPE html>
  <html>
  <head>
  <style>
  .table2, th, td {
    border: 1px solid black;
    border-collapse: collapse;
  }
  </style>
  </head>
  <body>

  <h2>LEAVES REVERSAL REPORT</h2>
<table style="border:0px !important;">
         <tr style="border:0px !important;">
            <td style="padding: 0px;border:0px !important;"><b>From:</b> {{$filerArray['from']}}
            </td>
            <td style="border:0px !important;"><b>To:</b> {{$filerArray['to']}}</td>
         </tr>
      </table>
  <table class="table2" style="width:100%;font-size: 13px;margin-top: 7px;">
    <tr>
            <th align="left" scope="col" style="padding: 3px;">Emp No</th>
            <th align="left" scope="col" style="padding: 3px;">Employee</th>
            <th align="left" scope="col" style="padding: 3px;">Leave Type</th>
            <th align="left" scope="col" style="padding: 3px;">From</th>
            <th align="left" scope="col" style="padding: 3px;">To</th>
            <th align="left" scope="col" style="padding: 3px;">Total Days</th>
            <th align="left" scope="col" style="padding: 3px;">Reversal Date</th>
            <th align="left" scope="col" style="padding: 3px;">Reversal By</th>
    </tr>
     @foreach($leaveReversalDataPdf as $value)
       <tr>
          <td style="padding: 3px;">{{$value->EmpData ? $value->EmpData->staff_number : 'NA'}}</td>
          <td style="padding: 3px;">{{$value->EmpData ? $value->EmpData->first_name : 'NA'}} {{$value->EmpData ? $value->EmpData->middle_name : 'NA'}} {{$value->EmpData ? $value->EmpData->last_name : 'NA'}}</td>
          <td style="padding: 3px;">{{$value->LeaveDataGet2 ? $value->LeaveDataGet2->leave_type : 'NA'}}</td>
          <td style="padding: 3px;">{{$value->AssignLeave ? $value->AssignLeave->from : 'NA'}}</td>
          <td style="padding: 3px;">{{$value->AssignLeave ? $value->AssignLeave->to : 'NA'}}</td>
          <td style="padding: 3px;">{{$value->AssignLeave ? $value->AssignLeave->day_taken : 'NA'}}</td>
          <td style="padding: 3px;">{{$value->date_reversal}}</td>
          <td style="padding: 3px;">{{$value->ReCallsLeave ? $value->ReCallsLeave->name : 'NA'}}</td>
        </tr>
        @endforeach
  </table>

  </body>
  </html>