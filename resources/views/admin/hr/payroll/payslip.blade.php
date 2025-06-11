@php
    $payrollMonth = $payrollMonthDetail->payrollMonth->name;

    $employee = $payrollMonthDetail->employee;

    $taxRelief = $payrollMonthDetail->tax_relief;
    
    $insuranceRelief = $payrollMonthDetail->insurance_relief;
    
    $housingLevyRelief = $payrollMonthDetail->housing_levy_relief;

@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ "$payrollMonth $employee->full_ame Payslip" }}</title>

    <style>
        table {
            width: 100%;
        }

        table td {
            width: 50%
        }

        p {
            margin: 0;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table>
        <tbody>
            <tr>
                <td style="padding: 10px; border: 1px solid black;">
                    <div>
                        <p class="bold">KANINI HARAKA ENTERPRISES</p>
                        <p class="bold">PAYSLIP FOR {{ strtoupper($payrollMonth) }}</p>
                        
                        <hr>

                        <p class="bold">{{ strtoupper($employee->full_name) }}</p>
                        <p>EMPLOYEE NO.: {{ $employee->employee_no }}</p>
                        <p>PIN NO.: {{ $employee->pin_no }}</p>
                        <p>ID NO.: {{ $employee->id_no }}</p>
                        <p>BRANCH: {{ $employee->branch->name }}</p>
                        <p>DESIGNATION: {{ strtoupper($employee->jobTitle->name) }}</p>

                        <hr>

                        <p class="bold">EARNINGS</p>
                        <table style="margin-bottom: 30px; font-size: 15px;">
                            <tbody>
                                <tr>
                                    <td>BASIC PAY</td>
                                    <td class="text-right">{{ number_format($payrollMonthDetail->basic_pay, 2) }}</td>
                                </tr>
                                @foreach ($payrollMonthDetail->earnings as $earning)
                                    <tr>
                                        <td>{{ strtoupper($earning->earning->name) }}</td>
                                        <td class="text-right">{{ number_format($earning->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="bold">TOTAL EARNINGS</td>
                                    <td class="text-right bold">{{ number_format($payrollMonthDetail->basic_pay + $payrollMonthDetail->earnings->sum('amount'), 2) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <P class="bold">P.A.Y.E</P>
                        <table style="margin-bottom: 30px; font-size: 15px;">
                            <tbody>
                                <tr>
                                    <td>LIABLE PAY</td>
                                    <td class="text-right">{{ number_format($payrollMonthDetail->gross_pay, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>LESS PENSION/NSSF</td>
                                    <td class="text-right">{{ number_format($payrollMonthDetail->nssf, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>CHARGEABLE PAY</td>
                                    <td class="text-right">{{ number_format($payrollMonthDetail->taxable_pay, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>TAX CHARGED</td>
                                    <td class="text-right">{{ number_format($payrollMonthDetail->paye + $taxRelief + $insuranceRelief + $housingLevyRelief, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>PERSONAL RELIEF</td>
                                    <td class="text-right">{{ number_format($taxRelief, 2) }}</td>
                                </tr>
                                @if ($insuranceRelief)
                                    <tr>
                                        <td>INSURANCE RELIEF</td>
                                        <td class="text-right">{{ number_format($insuranceRelief, 2) }}</td>
                                    </tr>                                    
                                @endif
                                <tr>
                                    <td>HOUSING RELIEF</td>
                                    <td class="text-right">{{ number_format($housingLevyRelief, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <p class="bold">DEDUCTIONS</p>
                        <table style="margin-bottom: 30px; font-size: 15px;">
                            <tbody>
                                <tr>
                                    <td>PAYE</td>
                                    <td class="text-right">{{ number_format($payrollMonthDetail->paye, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>NSSF</td>
                                    <td class="text-right">{{ number_format($payrollMonthDetail->nssf, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>SHIF</td>
                                    <td class="text-right">{{ number_format($payrollMonthDetail->shif, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>HOUSING LEVY</td>
                                    <td class="text-right">{{ number_format($payrollMonthDetail->housing_levy, 2) }}</td>
                                </tr>
                                @foreach ($payrollMonthDetail->deductions as $deduction)
                                    <tr>
                                        <td>{{ strtoupper($deduction->deduction->name) }}</td>
                                        <td class="text-right">{{ number_format($deduction->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="bold">TOTAL DEDUCTIONS</td>
                                    <td class="text-right bold">{{ number_format($payrollMonthDetail->gross_pay - $payrollMonthDetail->net_pay, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <table style="margin-bottom: 30px; font-size: 15px;">
                            <tbody>
                                <tr>
                                    <td class="bold">NET PAY</td>
                                    <td class="text-right bold">{{ number_format($payrollMonthDetail->net_pay, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <p>Paid By {{ $employee->paymentMode->name }}</p>
                        <p>{{ $employee->primaryBankAccount?->bank->name }} {{ $employee->primaryBankAccount?->bankBranch->name }}</p>
                        <p>BANK A/C: {{ $employee->account_no }}</p>

                        <p style="margin-top: 30px">Signature:</p>
                    </div>
                </td>
                <td style="visibility: hidden">
                    Content
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>