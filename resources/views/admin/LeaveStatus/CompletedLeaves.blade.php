<!DOCTYPE html>
<html>
<head>
<style>
table {
  width:100%;
}
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
th, td {
  padding: 15px;
  text-align: left;
}
#t01 tr:nth-child(even) {
  background-color: #eee;
}
#t01 tr:nth-child(odd) {
 background-color: #fff;
}
#t01 th {
  background-color: black;
  color: white;
}
</style>
</head>
<body>
  <h2>Completed Leaves</h2>
<table>
  <tr>
    <th>Emp No</th>
    <th>Employee</th> 
    <th>Leave Type</th>
    <th>From</th>
    <th>To</th>
    <th>Total Days</th>
    <th>Date Approved</th>
    <th>Approved</th>
  </tr>

    @foreach($completedLeavesDataPdf2 as $value)
    <tr>
       <td align="left" scope="col">{{$value->EmpDataGet2 ? $value->EmpDataGet2->staff_number : 'NA'}}</td>
       <td align="left" scope="col">{{$value->EmpDataGet2 ? $value->EmpDataGet2->first_name : 'NA'}} {{$value->EmpDataGet2 ? $value->EmpDataGet2->middle_name : 'NA'}} {{$value->EmpDataGet2 ? $value->EmpDataGet2->last_name : 'NA'}} </td>
       <td align="left" scope="col">{{$value->LeaveDataGet2 ? $value->LeaveDataGet2->leave_type : 'NA'}}</td>
       <td align="left" scope="col">{{$value->from}}</td>
       <td align="left" scope="col">{{$value->to}}</td>
       <td align="left" scope="col">{{$value->day_taken}}</td>
       <td align="left" scope="col">{{$value->day_taken}}</td>
       <td align="left" scope="col">{{$value->UData ? $value->UData->name : 'NA'}}</td>
    </tr>@endforeach

</table>

</body>
</html>
