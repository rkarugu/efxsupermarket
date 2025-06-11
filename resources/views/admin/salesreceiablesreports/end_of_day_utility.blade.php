@extends('layouts.admin.admin')

<script>
    window.branches = @json($branches);
    window.branchescloseddata = @json($branchescloseddata);
</script>

@section('content')
    <div id="app" v-cloak>

        <section class="content">
            @include('message')
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="box-header-flex">
                        <h3 class="box-title"> End of Day </h3>
                        {{-- <button :loading="loading" type="button" class="btn btn-danger"
                            @click.prevent="fetchBranchAccountData">
                            @{{ loading ? 'Processing.....' : 'Close Day' }}
                        </button> --}}
                        <form action="{{ route('end_of_day_process.process') }}" method="get" v-cloak>
{{--                            @if(\Illuminate\Support\Facades\Auth::user()->is_hq_user)--}}
                                <div class="col-sm-6">
                                    <div class="form-group">
                                       <input name="date" type="date" class="form-control" id="date"  max="{{ date('Y-m-d') }}" >
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <select name="select_branch" id="select_branch"
                                                class="form-control" required>
                                            <option value="">Select...</option>
                                            <option v-for="(item, index) in branches" :value="item.id"
                                                    :key="index">@{{ item.location_name }}</option>
                                        </select>
                                    </div>
                                </div>
{{--                            @endif--}}

                            <button class="btn btn-danger" type="submit">
                                Close Day
                            </button>
                        </form>


                        {{-- <button type="button" class="btn btn-danger">
                            Close Day
                        </button> --}}
                    </div>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="show-eod-dates">
                                    <thead>
                                        <tr>
                                            <th>Branch</th>
                                            <th>Cash Banking Reference</th>
                                            <th>Date</th>
                                            <th>Open Time</th>
                                            <th>Close Time</th>
                                            <th>Opened By</th>
                                            <th>Closed By</th>
                                            <th>Status</th>
                                            <th>CBR</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="branchesclosed in branchescloseddata" :key="branchesclosed.id">
                                            <td>@{{ branchesclosed?.walocationandstore.location_name }}</td>
                                            <td>@{{ branchesclosed?.chiefcashierdeclaration?.reference }}</td>
                                            <td>@{{ formatDate(branchesclosed?.opened_date, 'DD-MM-YYYY') }}</td>
                                            <td>@{{ formatDate(branchesclosed?.opened_time, 'HH:MM:SS') }}</td>
                                            <td>@{{ formatDate(branchesclosed?.closed_time, 'HH:MM:SS') }}</td>
                                            <td>@{{ branchesclosed?.openedby?.name }}</td>
                                            <td>@{{ branchesclosed?.closedby?.name }}</td>
                                            <td>@{{ branchesclosed?.status }}</td>
                                            <td>
                                                <a :href="`chief-cashier-cash-pdf/${branchesclosed.chiefcashierdeclaration.id}`" class="btn btn-sm btn-primary" v-if="branchesclosed.chiefcashierdeclaration">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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

                const branches = ref(window.branches)
                const branchescloseddata = ref(window.branchescloseddata)
                const itemmoves = ref([])
                const disablebutton = ref(true)
                const step = ref(1)
                const showwizard = ref(false)
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

                const reloadDateInput = ref(0)
                const reloadBranchInput = ref(0)

                const formUtil = new Form()

                const loading = ref(false)
                const loading2 = ref(false)

                const form = ref({
                    branch_id: '',
                    selected_date: ''
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
                    if (step.value == 6) {
                        if (eodtotal.value == tilltotal.value) {
                            loading2.value = true
                            axios.post('/admin/close-branch', form.value)
                                .then(response => {
                                    if (response.status == 200) {
                                        formUtil.successMessage('Branch closed for date ' + form.value
                                            .selected_date)
                                        loading2.value = false
                                        showendofdaydata.value = true
                                        showwizard.value = false
                                        showclearbutton.value = false
                                        disablebutton.value = true
                                        form.value.branch_id = ''
                                        form.value.selected_date = ''
                                    }
                                })
                                .catch(error => {
                                    formUtil.errorMessage(error.response.data.error)
                                })
                        } else {
                            return
                        }
                    }
                    if (step.value < 6) {
                        // step.value++
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
                    if (branchdata.value.status == 'Closed') {
                        formUtil.infoMessage(branchdata.value.walocationandstore.location_name +
                            ' is already closed for date ' + formatDate(branchdata.value.opened_date,
                                'DD-MM-YYYY'))
                        return
                    }
                    loading.value = true
                    disablebutton.value = true
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

                const fetchPendingInterbranchTransfers = () => {

                    axios.post('/admin/process-inter-branch-transfer-details', form.value)
                        .then(response => {
                            if (response.status == 200) {
                                inwardspendinginterbranchtransfers.value = response.data
                                    .inwardspendinginterbranchtransfers
                                outwardspendinginterbranchtransfers.value = response.data
                                    .outwardspendinginterbranchtransfers
                                const uniqueDates = [...new Set([...inwardspendinginterbranchtransfers.value
                                    .map(item => item.transfer_date), ...
                                    outwardspendinginterbranchtransfers.value.map(item => item
                                        .transfer_date)
                                ])];
                                const pendingtransfersarray = uniqueDates.map(date => {
                                    const inwardTransfers = inwardspendinginterbranchtransfers.value
                                        .filter(item => item
                                            .transfer_date === date);
                                    const outwardTransfers = outwardspendinginterbranchtransfers
                                        .value
                                        .filter(item => item
                                            .transfer_date === date);
                                    const totalInwards = inwardTransfers.length;
                                    const totalOutwards = outwardTransfers.length;
                                    console.log(date, totalInwards, totalOutwards)
                                    return {
                                        date,
                                        totalInwards,
                                        totalOutwards,
                                        inwardTransfers,
                                        outwardTransfers
                                    };
                                });
                                pendingtransfers.value = pendingtransfersarray
                                showendofdaydata.value = false
                            }
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                        })

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
                    form.value.branch_id = ''
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
                        "searching": true,
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


                onMounted(() => {
                    destroyTable()
                    triggerDataTable()
                    // const savedStep = localStorage.getItem('wizard_step');
                    // if (savedStep) {
                    //     step.value = parseInt(savedStep);
                    // }
                })

                return {
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
                    branches,
                    branchescloseddata,
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
