import './bootstrap'
import { createApp } from "vue";
import VueApexCharts from "vue3-apexcharts";

import ChairmanDashboard from "./pages/ChairmanDashboard.vue";
import ChairmanGeneralDashboard from "./pages/ChairmanGeneralDashboard.vue";
import ChairmanGeneralDashboardSalesReport from "./pages/ChairmanGeneralDashboardSalesReport.vue";

import PayrollConfiguration from "./pages/hr/PayrollConfiguration.vue";
import PayrollMonths from "./pages/hr/PayrollMonths.vue";
import PayrollMonthDetails from "./pages/hr/PayrollMonthDetails.vue";
import Casuals from "./pages/hr/Casuals.vue";
import CasualsCreate from "./pages/hr/CasualsCreate.vue";
import CasualsEdit from "./pages/hr/CasualsEdit.vue";
import CasualsPayPeriods from "./pages/hr/CasualsPayPeriods.vue";
import CasualsPayperiodDetails from "./pages/hr/CasualsPayperiodDetails.vue";
import SuccessfulDisbursements from "./pages/hr/casual-pay/SuccessfulDisbursements.vue";
import FailedDisbursements from "./pages/hr/casual-pay/FailedDisbursements.vue";
import ExpungedDisbursements from "./pages/hr/casual-pay/ExpungedDisbursements.vue";
import PaymasterReport from "./pages/hr/payroll-reports/PaymasterReport.vue";
import PayrollSummaryReport from "./pages/hr/payroll-reports/PayrollSummaryReport.vue";
import EarningsReport from "./pages/hr/payroll-reports/EarningsReport.vue";
import DeductionsReport from "./pages/hr/payroll-reports/DeductionsReport.vue";
import ConsolidatedPayrollReport from "./pages/hr/payroll-reports/ConsolidatedPayrollReport.vue";
import PayeDeductionsReport from "./pages/hr/payroll-reports/PayeDeductionsReport.vue";
import NssfDeductionsReport from "./pages/hr/payroll-reports/NssfDeductionsReport.vue";
import ShifDeductionsReport from "./pages/hr/payroll-reports/ShifDeductionsReport.vue";
import HousingLevyDeductionsReport from "./pages/hr/payroll-reports/HousingLevyDeductionsReport.vue";
import OtherDeductionsReport from "./pages/hr/payroll-reports/OtherDeductionsReport.vue";
import DeviceCenter from "./pages/device_center/device-center.vue";

const app = createApp({});

app.use(VueApexCharts);

app.component('chairman-dashboard', ChairmanDashboard);
app.component('chairman-general-dashboard', ChairmanGeneralDashboard)
app.component('chairman-general-dashboard-sales-report', ChairmanGeneralDashboardSalesReport)

app.component('payroll-configuration', PayrollConfiguration);
app.component('payroll-months', PayrollMonths);
app.component('payroll-month-details', PayrollMonthDetails);
app.component('casuals', Casuals);
app.component('casuals-create', CasualsCreate);
app.component('casuals-edit', CasualsEdit);
app.component('casuals-pay-periods', CasualsPayPeriods);
app.component('casuals-pay-period-details', CasualsPayperiodDetails);
app.component('successful-disbursements', SuccessfulDisbursements);
app.component('failed-disbursements', FailedDisbursements);
app.component('expunged-disbursements', ExpungedDisbursements);
app.component('paymaster-report', PaymasterReport);
app.component('payroll-summary-report', PayrollSummaryReport);
app.component('earnings-report', EarningsReport);
app.component('deductions-report', DeductionsReport);
app.component('consolidated-payroll-report', ConsolidatedPayrollReport);
app.component('paye-deductions-report', PayeDeductionsReport);
app.component('nssf-deductions-report', NssfDeductionsReport);
app.component('shif-deductions-report', ShifDeductionsReport);
app.component('housing-levy-deductions-report', HousingLevyDeductionsReport);
app.component('other-deductions-report', OtherDeductionsReport);
app.component('device-center', DeviceCenter);

app.mount("#admin-app");

// Force rebuild: 2025-06-12T13:23:00+03:00
