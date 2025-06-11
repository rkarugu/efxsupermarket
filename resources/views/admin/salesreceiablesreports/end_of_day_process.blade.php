@extends('layouts.admin.admin')

@section('content')
    <div id="app" v-cloak>
        <section class="content">
            <div class="box box-primary">
                <div class="box-header">
                    <div class="box-header-flex">

                    </div>
                </div>

                <div class="box-body">
                    <div class="multistep" v-show="showwizard">
                        <div class="container-fluid">

                            <h4 class="text-center" style="margin-bottom:30px;margin-top:-20px;font-weight:bolder">
                                END OF DAY PROCESS
                            </h4>

                            <div class="stepwizard" style="width:100%">
                                <div class="stepwizard-row setup-panel" style="width:100%">
                                    <div class="stepwizard-step col-xs-3" style="width:16%">
                                        <a href="javascript:void(0)" type="button"
                                            class="btn btn-default btn-circle step-button"
                                            :class="{ 'active': isStepActive(1) }" :disabled="step !== 1">1</a>
                                        <p><b>Till Tender Entry Total vs EOD Total</b></p>
                                    </div>
                                    <div class="stepwizard-step col-xs-3" style="width:16%">
                                        <a href="javascript:void(0)" type="button"
                                            class="btn btn-default btn-circle step-button"
                                            :class="{ 'active': isStepActive(2) }" :disabled="step !== 2">2</a>
                                        <p><b>Pending Interbranch Transfers</b></p>
                                    </div>
                                    <div class="stepwizard-step col-xs-3" style="width:16%">
                                        <a href="javascript:void(0)" type="button"
                                            class="btn btn-default btn-circle step-button"
                                            :class="{ 'active': isStepActive(3) }" :disabled="step !== 3">3</a>
                                        <p><b>Pending Split</b></p>
                                    </div>
                                    <div class="stepwizard-step col-xs-3" style="width:16%">
                                        <a href="javascript:void(0)" type="button"
                                            class="btn btn-default btn-circle step-button"
                                            :class="{ 'active': isStepActive(4) }" :disabled="step !== 4">4</a>
                                        <p><b>Incomplete Transactions</b></p>
                                    </div>
                                    <div class="stepwizard-step col-xs-3" style="width:16%">
                                        <a href="javascript:void(0)" type="button"
                                            class="btn btn-default btn-circle step-button"
                                            :class="{ 'active': isStepActive(5) }" :disabled="step !== 5">5</a>
                                        <p><b>Sales vs Stock Movement</b></p>
                                    </div>
                                    <div class="stepwizard-step col-xs-3" style="width:16%">
                                        <a href="javascript:void(0)" type="button"
                                            class="btn btn-default btn-circle step-button"
                                            :class="{ 'active': isStepActive(6) }" :disabled="step !== 6">6</a>
                                        <p><b>Pending Post Dated Cheques</b></p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <section class="content">

                            <div class="box box-primary" v-if="step === 1">
                                <div class="box-header with-border">
                                    <div class="d-flex justify-content-between">
                                        <div class="justify-content-start">
                                            <p style="font-weight:bolder">
                                                Till Tender Entry Total vs EOD Total
                                            </p>
                                        </div>
                                        <div class="justify-content-end">
                                            <span v-if="eodtotal != tilltotal" style="color:red;font-weight:bolder">
                                                Not Matching
                                            </span>
                                            <span v-else style="color: green;font-weight:bolder">
                                                {{-- Okay --}}
                                                Passed
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="item-moves-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Till Tender Entry Total </th>
                                                            <th>End of Day Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>@{{ form.selected_date }}</td>
                                                            <td>@{{ numberWithCommas(eodtotal) }}</td>
                                                            <td>@{{ numberWithCommas(tilltotal) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="box box-primary" v-if="step === 2">
                                <div class="box-header with-border">
                                    <div class="d-flex justify-content-between">
                                        <div class="justify-content-start">
                                            <p style="font-weight:bolder">
                                                Pending Interbranch Transfers
                                            </p>
                                        </div>
                                        <div class="justify-content-end">
                                            <span
                                                v-if="pendingtransfers[0]?.totalInwards === 0 && pendingtransfers[0]?.totalOutwards === 0"
                                                style="color:green;font-weight:bolder">
                                                {{-- Okay --}}
                                                Passed
                                            </span>
                                            <span v-else style="color: red;font-weight:bolder">
                                                Not Matching
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="item-moves-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Inwards</th>
                                                            <th>Outwards</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(pendingtransfer, index) in pendingtransfers"
                                                            :key="index">
                                                            <td>@{{ formatDate(pendingtransfer.date, 'DD-MM-YYYY') }}</td>
                                                            <td style="cursor: pointer"
                                                                @click="toggleDetails(pendingtransfer, 'inwards')">
                                                                <a
                                                                    v-if="showInwardsDetails[index]">@{{ pendingtransfer.totalInwards }}</a>
                                                                <a v-else>@{{ pendingtransfer.totalInwards }}</a>
                                                            </td>
                                                            <td style="cursor: pointer"
                                                                @click="toggleDetails(pendingtransfer, 'outwards')">
                                                                <a
                                                                    v-if="showOutwardsDetails[index]">@{{ pendingtransfer.totalOutwards }}</a>
                                                                <a v-else>@{{ pendingtransfer.totalOutwards }}</a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <div class="box box-primary" v-if="step === 3">
                                <div class="box-header with-border">
                                    <p style="font-weight:bolder">
                                        Pending Split
                                    </p>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="item-moves-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Inwards </th>
                                                            <th>Outwards</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>12-04-2024</td>
                                                            <td>2,000</td>
                                                            <td>1,000</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="box box-primary" v-if="step === 4">
                                <div class="box-header with-border">
                                    <p style="font-weight:bolder">
                                        Incomplete Transactions
                                    </p>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="item-moves-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Vooma </th>
                                                            <th>Eazzy</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>12-04-2024</td>
                                                            <td>2,000</td>
                                                            <td>1,000</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="box box-primary" v-if="step === 5">
                                <div class="box-header with-border">
                                    <p style="font-weight:bolder">
                                        Sales vs Stock Movement
                                    </p>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="item-moves-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Vooma </th>
                                                            <th>Eazzy</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>12-04-2024</td>
                                                            <td>2,000</td>
                                                            <td>1,000</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="box box-primary" v-if="step === 6">
                                <div class="box-header with-border">
                                    <p style="font-weight:bolder">
                                        Pending Post Dated Cheques
                                    </p>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="item-moves-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Vooma </th>
                                                            <th>Eazzy</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>12-04-2024</td>
                                                            <td>2,000</td>
                                                            <td>1,000</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary" style="float:left" :disabled="step === 1"
                                @click="prevStep">
                                Previous
                            </button>

                            <button :disabled="disabledbuttondata" type="button" class="btn btn-primary"
                                style="float:right" v-if="step !== 6" @click="nextStep">
                                Next
                            </button>

                            <button type="button" :loading="loading2" class="btn btn-primary" style="float:right"
                                v-if="step === 6" @click="nextStep">
                                @{{ loading2 ? 'Processing.....' : 'Finish and Close' }}
                            </button>

                        </section>

                    </div>

                </div>
            </div>

        </section>

    </div>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('css/multistep-form.css') }}">
    <div id="loader-on"
        style="
            position: fixed;
            top: 0;
            text-align: center;
            display: block;
            z-index: 999999;
            width: 100%;
            height: 100%;
            background: #000000b8;
            display:none;
            "
        class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        a.btn.btn-default.btn-circle.step-button.active {
            background: #ff0000 !important;
            border: none;
            color: white;
            font-weight: bolder;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $('body').addClass('sidebar-collapse');

        $(document).ready(function() {
            $('select_branch').select2();
        });
    </script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script type="importmap">
        {
        "imports": {
            "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
        }
        }
    </script>

    <script type="module">
        import {
            createApp,
            ref,
            watch,
            computed,
            onMounted
        } from 'vue';

        createApp({
            setup() {
                const itemmoves = ref([])
                const disablebutton = ref(true)
                const step = ref(1)
                const showwizard = ref(true)
                const showendofdaydata = ref(true)
                const showclearbutton = ref(false)
                const inwardspendinginterbranchtransfers = ref([])
                const outwardspendinginterbranchtransfers = ref([])
                const pendingtransfers = ref([])
                const showInwardsDetails = ref(Array(pendingtransfers.value.length).fill(false));
                const showOutwardsDetails = ref(Array(pendingtransfers.value.length).fill(false));
                const wagltranssum = ref([])
                const watenderentriessum = ref([])
                const branchdata = ref({})
                const eodtotal = ref(0)
                const tilltotal = ref(0)
                const endofdayarray = ref([])
                const disabledbuttondata = ref(false)
                const salesvsstocks = ref([])

                const reloadDateInput = ref(0)
                const reloadBranchInput = ref(0)

                const formUtil = new Form()
                const print  =  function printBill(slug) {
                    jQuery.ajax({
                        url: slug,
                        type: 'GET',
                        async: false,   //NOTE THIS
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            var divContents = response;
                            var printWindow = window.open('', '', 'width=600');
                            printWindow.document.write(divContents);
                            printWindow.document.close();
                            printWindow.print();
                            printWindow.close();
                        }
                    });
                }

                const loading = ref(false)
                const loading2 = ref(false)

                const form = ref({
                    branch_id: @json($branch),
                    selected_date: @json(today()),
                })

                const filteredClosedDate = computed(() => {
                    const currentDate = new Date()
                    const closedDate = new Date(branchdata.value.closed_date)

                    currentDate.setHours(0, 0, 0, 0)

                    return closedDate.getTime() === currentDate.getTime() ? closedDate
                        .toLocaleDateString() : null
                })

                const formatDate = (date, format) => {
                    if (!date) return "";

                    const parsedDate = new Date(date);
                    const day = parsedDate.getDate().toString().padStart(2, '0');
                    const month = (parsedDate.getMonth() + 1).toString().padStart(2, '0');
                    const year = parsedDate.getFullYear();
                    const hours = parsedDate.getHours().toString().padStart(2, '0');
                    const minutes = parsedDate.getMinutes().toString().padStart(2, '0');
                    const seconds = parsedDate.getSeconds().toString().padStart(2, '0');
                    const formattedDate = format
                        .replace(/DD/, day)
                        .replace(/MM/, month)
                        .replace(/YYYY/, year)
                        .replace(/HH/, hours)
                        .replace(/MM/, minutes)
                        .replace(/SS/, seconds);

                    return formattedDate;
                }

                const getYesterday = () => {
                    const today = new Date();
                    const yesterday = new Date(today);
                    yesterday.setDate(today.getDate() - 1);
                    return yesterday.toISOString().split('T')[0];
                }

                const isStepActive = (stepNumber) => {
                    return step.value === stepNumber;
                }

                const nextStep = () => {
                    if (step.value == 1) {
                        fetchPendingInterbranchTransfers()
                    }
                    if (step.value == 3) {
                        fetchIncompleteTransactions()
                    }
                    if (step.value == 4) {
                        fetchSalesVsStocks()
                    }
                    if (step.value == 6) {
                        if (eodtotal.value == tilltotal.value) {
                            loading2.value = true
                            var VForm = new Form();
                            jQuery.ajax({
                                url: "close-branch",
                                type: 'POST',
                                data: form.value,
                                headers: {
                                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {
                                    if (response.status == 200) {
                                        formUtil.successMessage('Branch closed for date ' + form.value
                                            .selected_date)
                                        loading2.value = false
                                        showendofdaydata.value = true
                                        showwizard.value = false
                                        showclearbutton.value = false
                                        disablebutton.value = true
                                        form.value.branch_id = @json($branch);
                                        form.value.selected_date = '';
                                        print(response.data.cash_receipt_url)
                                        window.location.href='{{ url('admin/get-end-of-day-veiw') }}'
                                    }

                                },
                                error: function(error) {
                                    VForm.errorMessage(error.responseJSON.message);
                                }
                            });
                        } else {
                            return
                        }
                    }
                    if (step.value < 6) {
                        step.value++
                    }
                }

                const prevStep = () => {
                    if (step.value > 1) {
                        step.value--
                    }
                }

                const selectBranch = (event) => {
                    form.value.branch_id = event.target.value
                    if (form.value.branch_id != '' || form.value.branch_id != null || form.value.branch_id !=
                        undefined) {
                        disablebutton.value = false
                        showwizard.value = false
                        showendofdaydata.value = true
                        axios.post('/admin/process-branch-details', form.value)
                            .then(response => {
                                endofdayarray.value = response.data.endofdayarray
                                branchdata.value = response.data.branchdata
                            })
                            .catch(error => {
                                formUtil.errorMessage(error.response.data.error)
                            })
                    } else {
                        disablebutton.value = true
                        showwizard.value = false
                        showendofdaydata.value = true
                    }
                }

                const fetchBranchAccountData = () => {
                    // if (branchdata.value.status == 'Closed') {
                    //     formUtil.infoMessage(branchdata.value.walocationandstore.location_name +
                    //         ' is already closed for date ' + formatDate(branchdata.value.opened_date,
                    //             'DD-MM-YYYY'))
                    //     return
                    // }
                    loading.value = true
                    disablebutton.value = true
                    console.log(form.value)
                    axios.post('/admin/process-branch-accounts-details', form.value)
                        .then(response => {
                            if (response.status == 200) {
                                loading.value = false
                                showendofdaydata.value = false
                                showwizard.value = true
                                showclearbutton.value = true
                                disablebutton.value = true
                                const transactions = response.data.totalsPerDate;

                                eodtotal.value = response.data.totaleodsums
                                tilltotal.value = response.data.tenderentriestotals
                                if (step.value === 1 && eodtotal.value !== tilltotal.value) {
                                    disabledbuttondata.value = true
                                }

                                const currentDate = new Date();
                                currentDate.setHours(0, 0, 0, 0);
                                let closestDate = null;
                                for (const date in response.data.totalsPerDate) {
                                    const dateObject = new Date(date);
                                    dateObject.setHours(0, 0, 0, 0);
                                    if (dateObject < currentDate && (closestDate === null || dateObject >
                                            closestDate)) {
                                        closestDate = dateObject;
                                    }
                                }
                                if (closestDate !== null) {
                                    const closestDateString = closestDate.toISOString().split('T')[0];
                                    const closestDayData = response.data.totalsPerDate[closestDateString];
                                    console.log("Closest available day:", closestDateString);
                                    console.log(closestDayData);
                                } else {
                                    console.log("No data available before the current date");
                                }

                            }
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                            loading.value = false
                            showendofdaydata.value = false
                            showwizard.value = true
                            showclearbutton.value = true
                            disablebutton.value = true
                        })
                }

                // const fetchPendingInterbranchTransfers = () => {

                //     axios.post('/admin/process-inter-branch-transfer-details', form.value)
                //         .then(response => {
                //             if (response.status == 200) {
                //                 inwardspendinginterbranchtransfers.value = response.data
                //                     .inwardspendinginterbranchtransfers
                //                 outwardspendinginterbranchtransfers.value = response.data
                //                     .outwardspendinginterbranchtransfers
                //                 const uniqueDates = [...new Set([...inwardspendinginterbranchtransfers.value
                //                     .map(item => item.transfer_date), ...
                //                     outwardspendinginterbranchtransfers.value.map(item => item
                //                         .transfer_date)
                //                 ])];
                //                 const pendingtransfersarray = uniqueDates.map(date => {
                //                     const inwardTransfers = inwardspendinginterbranchtransfers.value
                //                         .filter(item => item
                //                             .transfer_date === date);
                //                     const outwardTransfers = outwardspendinginterbranchtransfers
                //                         .value
                //                         .filter(item => item
                //                             .transfer_date === date);
                //                     const totalInwards = inwardTransfers.length;
                //                     const totalOutwards = outwardTransfers.length;
                //                     console.log(date, totalInwards, totalOutwards)
                //                     return {
                //                         date,
                //                         totalInwards,
                //                         totalOutwards,
                //                         inwardTransfers,
                //                         outwardTransfers
                //                     };
                //                 });
                //                 pendingtransfers.value = pendingtransfersarray
                //                 showendofdaydata.value = false
                //             }
                //         })
                //         .catch(error => {
                //             formUtil.errorMessage(error.response.data.error)
                //         })

                // }

                const fetchSalesVsStocks = () => {
                    axios.post('/admin/process-sales-vs-stock-movement', form.value)
                        .then(response => {
                            salesvsstocks.value = response.data.salesvsstocks
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error);
                        })
                }

                const fetchPendingInterbranchTransfers = () => {
                    axios.post('/admin/process-inter-branch-transfer-details', form.value)
                        .then(response => {
                            if (response.status == 200) {
                                inwardspendinginterbranchtransfers.value = response.data
                                    .inwardspendinginterbranchtransfers;
                                outwardspendinginterbranchtransfers.value = response.data
                                    .outwardspendinginterbranchtransfers;

                                const allDates = [
                                    ...inwardspendinginterbranchtransfers.value.map(item => item
                                        .transfer_date),
                                    ...outwardspendinginterbranchtransfers.value.map(item => item
                                        .transfer_date)
                                ];

                                const uniqueDates = [...new Set(allDates)];

                                const sortedDates = uniqueDates.sort((a, b) => new Date(b) - new Date(a));

                                const latestDate = sortedDates.length > 0 ? sortedDates[0] : null;

                                if (latestDate) {
                                    const inwardTransfers = inwardspendinginterbranchtransfers.value.filter(
                                        item => item.transfer_date === latestDate);
                                    const outwardTransfers = outwardspendinginterbranchtransfers.value
                                        .filter(item => item.transfer_date === latestDate);

                                    const totalInwards = inwardTransfers.length;
                                    const totalOutwards = outwardTransfers.length;

                                    console.log(latestDate, totalInwards, totalOutwards);

                                    pendingtransfers.value = [{
                                        date: latestDate,
                                        totalInwards,
                                        totalOutwards,
                                        inwardTransfers,
                                        outwardTransfers
                                    }];

                                    showendofdaydata.value = false;
                                }
                            }
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error);
                        });
                }

                const fetchIncompleteTransactions = () => {
                    axios.post('/admin/process-incomplete-transactions-branch-details', form.value)
                        .then(response => {
                            console.log(response.data)
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                        })
                }

                const groupTransactionsByDate = (transactions) => {
                    const groupedTransactions = {};
                    transactions.forEach(transaction => {
                        const date = new Date(transaction.trans_date).toLocaleDateString();
                        if (!groupedTransactions[date]) {
                            groupedTransactions[date] = [];
                        }
                        groupedTransactions[date].push(transaction);
                    });
                    return groupedTransactions;
                }

                const displayTotalAmountPerDate = (groupedByDate) => {
                    Object.entries(groupedByDate).forEach(([date, transactions]) => {
                        let totalAmount = 0;
                        transactions.forEach(transaction => {
                            totalAmount += parseFloat(transaction.amount);
                        });
                    });
                }

                const closeBranchData = () => {
                    showwizard.value = false
                    showclearbutton.value = false
                    showendofdaydata.value = true
                    disablebutton.value = false
                }

                const toggleDetails = (pendingtransfer, type) => {
                    const index = pendingtransfers.value.indexOf(pendingtransfer);
                    if (type === 'inwards') {
                        showInwardsDetails.value[index] = !showInwardsDetails.value[index];
                        showOutwardsDetails.value[index] = false;
                    } else {
                        showOutwardsDetails.value[index] = !showOutwardsDetails.value[index];
                        showInwardsDetails.value[index] = false;
                    }
                };

                const handleDateChange = (event) => {
                    reloadDateInput.value += 1
                    reloadBranchInput.value += 1
                    form.value.selected_date = ''
                    form.value.branch_id = @json($branch);
                    form.value.selected_date = event.target.value;
                }

                const numberWithCommas = (number) => {
                    return number.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
                }

                const destroyTable = () => {
                    $('#show-eod-dates').DataTable().destroy()
                }

                const triggerDataTable = () => {
                    $('#show-eod-dates').DataTable({
                        "paging": true,
                        "pageLength": 10,
                        "lengthChange": true,
                        "lengthMenu": [10, 20, 50, 100],
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
                        "order": [
                            [0, "asc"]
                        ]
                    });
                };

                const formatDateData = () => {
                    let dateObj = new Date();
                    dateObj.setDate(dateObj.getDate() - 1);
                    form.value.selected_date = formatDate(dateObj, 'DD-MM-YYYY')
                    form.value.branch_id = @json($branch);
                }

                onMounted(() => {
                    formatDateData()
                    fetchBranchAccountData()
                    destroyTable()
                    triggerDataTable()
                    // const savedStep = localStorage.getItem('wizard_step');
                    // if (savedStep) {
                    //     step.value = parseInt(savedStep);
                    // }
                })

                return {
                    salesvsstocks,
                    formatDateData,
                    numberWithCommas,
                    reloadDateInput,
                    reloadBranchInput,
                    handleDateChange,
                    disabledbuttondata,
                    formatDate,
                    fetchBranchAccountData,
                    fetchPendingInterbranchTransfers,
                    getYesterday,
                    step,
                    showwizard,
                    showendofdaydata,
                    disablebutton,
                    form,
                    selectBranch,
                    destroyTable,
                    triggerDataTable,
                    itemmoves,
                    inwardspendinginterbranchtransfers,
                    outwardspendinginterbranchtransfers,
                    pendingtransfers,
                    loading,
                    nextStep,
                    prevStep,
                    isStepActive,
                    showInwardsDetails,
                    showOutwardsDetails,
                    toggleDetails,
                    closeBranchData,
                    showclearbutton,
                    branchdata,
                    filteredClosedDate,
                    eodtotal,
                    tilltotal
                }
            }
        }).mount('#app')
    </script>
@endsection
