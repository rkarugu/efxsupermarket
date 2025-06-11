@component('mail::message')
<p>Dear <strong>{{ $payrollMonthDetail->employee->full_name }}</strong>,</p>

<p>Please see the attached payslip for <strong>{{ $payrollMonthDetail->payrollMonth->name }}</strong></p>

<p>
Regards <br>
HR Department
</p>
@endcomponent