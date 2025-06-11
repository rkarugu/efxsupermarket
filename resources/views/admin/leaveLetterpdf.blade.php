<!DOCTYPE html>
<html>
   <head>
      <title></title>
   </head>
   <body> 
  

      
      <table style="border-collapse: collapse;width: 100%">
         <tr>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; padding: 10px" rowspan="2"></td>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; padding: 10px"><b>Title</b>: Leave Form</td>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; padding: 10px"> {{$data->date}}</td>
         </tr>
         <tr>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; padding: 10px"><b>Issue Date</b>:
               {{$data->date}}
            </td>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; padding: 10px"><b>Review Date</b>:  {{$data->date}} <span style="float: right;">Page:1</span></td>
         </tr>
      </table>


      <table style="border-collapse: collapse;width: 100%">
         <tr>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; padding: 10px" colspan="3"><b>NB: To be submited one month in advance of leave request unless on emergency</td>
         </tr>
         <tr>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; padding: 10px"><b>Employee:</b>
             {{$empData->first_name}} {{$empData->middle_name}} {{$empData->last_name}}
            </td>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; padding: 10px"><b>Staf No:</b> {{$empData->staff_number}}</td>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; padding: 10px"><b>Date:</b> {{$empData->date_employed}}</td>
         </tr>
         <tr>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; padding: 10px"><b>Department:</b>{{$empData->DepData ? $empData->DepData->department_name : 'NA'}}
            </td>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; padding: 10px" colspan="2"><b>Duty Station:</b>
               WESTLANDS
            </td>
         </tr>
      </table>


      <table style="border-collapse: collapse;width: 100%">
         <tr>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; padding: 10px"><b>Leave requested </b> <br> <br>
               {{$data->LeaveType}}
            </td>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; padding: 10px" colspan="2"><b>Leave allowance If applicable</b>
            </td>
         </tr>
         <tr>
            <td style="border-right:1px solid gray;border-left: 1px solid gray; border-top:1px solid; border-bottom: 1px solid gray; padding: 10px" colspan="3"><b>Part 1 ( To be completed by the employee)</b></td>
         </tr>
      </table>


      <table style="border-collapse: collapse;width: 100%;border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray">
         <tr>
            <td style="padding: 10px" colspan="3"><b>To Head of Department</b></td>
         </tr>
         <tr>
            <td style="padding: 10px"><b>I wish to apply for :</b> {{$data->day_taken}}</td>
            <td style="padding: 10px"><b>days from :</b> {{$data->from}}
            </td>
            <td style="padding: 10px"><b>to :</b> {{$data->to}}
            </td>
         </tr>
         <tr>
            <td style="padding: 10px" colspan="3"><b>and report to work on:</b> _____________________<td>
         </tr>
         <tr>
            <td style="padding: 10px"><b>Sign</b>_____________________</td>
            <td style="padding: 10px" colspan="2"><b>Date</b> _____________________</td>
         </tr>
      </table>


      <table style="border-collapse: collapse;width: 100%;border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray">
         <tr>
            <td style="padding: 10px"><b>While away:</b> _____________________</td>
            <td style="padding: 10px"><b>will perform my dutes.</b> _____________________</td>
         </tr>
         <tr>
            <td style="padding: 10px"><b>Sign:</b> _____________________</td>
            <td style="padding: 10px"><b>Date:</b> _____________________</td>
         </tr>
         <tr>
            <td style="padding: 10px" colspan="2"><b>Tel::</b>............</td>
         </tr>
      </table>



      <table style="border-collapse: collapse;width: 100%;border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray">
         <tr>
            <td style="padding: 10px;border-bottom: 1px solid gray" colspan="3"><b>Part 2 (Ofcial use only)</b></td>
         </tr>
         <tr>
            <td style="padding: 10px 10px 0px 10px" colspan="3"><b>Head of Department Approval</b></td>
         </tr>
         <tr>
            <td style="padding: 10px"><b>Name:</b>Admin</td>
            <td style="padding: 10px"><b>Sign:</b> ------------
            </td>
            <td style="padding: 10px"><b>Date:</b> ------------
            </td>
         </tr>
         <tr>
            <td style="padding: 10px 10px 0px 10px" colspan="3"><b><u>HR Department</u></b></td>
         </tr>
         <tr>
            <td style="padding: 10px"><b>Leave Days Available:</b> 45.75</td>
            <td style="padding: 10px"><b>Days Requested:</b> {{$data->day_taken}} 
            </td>
            <td style="padding: 10px"><b>Balance:</b> {{$data->leave_balance}}
            </td>
         </tr>
      </table>


      <table style="border-collapse: collapse;width: 100%;border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray">
         <tr>
            <td style="padding: 10px;border-bottom: 1px solid gray" colspan="3"><b>Remarks</b>.............................</td>
         </tr>
         <tr>
            <td style="padding: 10px 10px 0px 10px" colspan="3"><b><u>Directors Approval</u></b></td>
         </tr>
         <tr>
            <td style="padding: 10px"><b>Approved:</b> Yes</td>
         </tr>
         <tr>
            <td style="padding: 10px"><b>Sign:</b>  -----------------</td>
            <td style="padding: 10px"><b>Date:</b>  ----------------- 
            </td>
         </tr>
         <tr>
            <td style="padding: 10px;border-bottom: 1px solid gray" colspan="3"><b>Remarks</b>.............................</td>
         </tr>
      </table>
      <br><br><br>
   </body>
</html>