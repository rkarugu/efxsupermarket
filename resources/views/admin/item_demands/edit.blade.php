@extends('layouts.admin.admin')

@php
    $user = getLoggeduserProfile();
    $redirectRoute = '"' . route('demands.item-demands.new') . '"';
@endphp

<script>
    window.user = {!! $user !!}
    window.page = '{!! $page !!}'
    window.demand = {!! $demand !!}
    window.redirectRoute = {!! $redirectRoute !!}
</script>

@section('content')
    <div id="app" v-cloak>
        <section class="content" id="return-from-store">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> {{ $page }} Price Demand </h3>
                </div>
                <div class="box-body">
                    <form action="" method="post" v-cloak>
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Document No.</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" value="{{ $demand->demand_no }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Employee name</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Document Date</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" readonly value="{{ $demand->created_at->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Supplier Reference</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" v-model="form.supplier_reference">
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">CU Invoice No</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" v-model="form.cu_invoice_no">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Note</label>
                                        <div class="col-sm-7">
                                            <textarea class="form-control" rows="3" v-model="form.note"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Branch</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" value="{{ $demand->user->userRestaurent->name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Store Location</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" value="{{ $demand->user->location_stores->location_name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Supplier Name</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" value="{{ $demand->supplier->name }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-5"></div>
                                    <div class="col-sm-6" style="text-align: right">
                                        <button type="button" class="btn btn-primary btn-sm" :disabled="processing" @click="convertDemand">
                                            Process
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        
        <section class="content">

            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">
                    <div class="col-md-12 no-padding-h ">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title">Price Demand Line</h3>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th style="min-width: 120px">Demand Qty</th>
                                    <th>Current Cost</th>
                                    <th>New Cost</th>
                                    <th>Delta</th>
                                    <th>VAT Type</th>
                                    <th>Exclusive</th>
                                    <th>VAT</th>
                                    <th>Demand Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(lineItem, index) in form.lineItems" :key="index">
                                    <td>@{{ form.lineItems[index].item_code }}</td>
                                    <td>@{{ form.lineItems[index].item_description }}</td>
                                    <td>@{{ form.lineItems[index].demand_quantity }}</td>
                                    <td>@{{ numberWithCommas(form.lineItems[index].current_cost) }}</td>
                                    <td>@{{ numberWithCommas(form.lineItems[index].new_cost) }}</td>
                                    <td>@{{ numberWithCommas(form.lineItems[index].delta) }}</td>
                                    <td>@{{ form.lineItems[index].vat_type }}</td>
                                    <td>@{{ numberWithCommas(form.lineItems[index].exclusive) }}</td>
                                    <td>@{{ numberWithCommas(form.lineItems[index].vat) }}</td>
                                    <td>@{{ numberWithCommas(form.lineItems[index].demand_amount) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="10"></td>
                                </tr>
                                <tr>
                                    <th colspan="9" style="text-align:right">
                                        Total Exclusive (KES)
                                    </th>
                                    <td>@{{ numberWithCommas(totalExclusive) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="9" style="text-align:right">
                                        Total VAT (KES)
                                    </th>
                                    <td>@{{ numberWithCommas(totalVat) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="9" style="text-align:right">
                                        Total Demand Amount (KES)
                                    </th>
                                    <td>@{{ numberWithCommas(totalDemandAmount) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="10"></td>
                                </tr>
                                <tr>
                                    <th colspan="9" style="text-align:right">
                                        Approved Amount (KES)
                                    </th>
                                    <td>@{{ numberWithCommas(demand.edited_demand_amount) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="9" style="text-align:right">
                                        VAT (KES)
                                    </th>
                                    <td>@{{ numberWithCommas(demand.vat_amount) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script type="importmap">
        {
            "imports": {
                "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
            }
        }
    </script>

    <script type="module">
        import {createApp, ref, computed, onMounted } from 'vue';

        createApp({
            setup() {
                const user = window.user
                const page = window.page
                const demand = window.demand

                const formUtil = new Form()
                
                const form = ref({
                    user_id: user.id,
                    demand_id: demand.id,
                    supplier_reference: demand.supplier_reference ?? '',
                    cu_invoice_no: demand.cu_invoice_no ?? '',
                    note: demand.note ?? '',

                    lineItems: []
                })

                window.demand.demand_items.forEach(demandItem => {
                    let quantity = demandItem.demand_quantity
                    let currentCost = demandItem.current_cost
                    let newCost = demandItem.new_cost
                    let delta = currentCost - newCost
                    let demandAmount = quantity * delta
                    let vatRate = parseFloat(demandItem.inventory_item.tax_manager.tax_value)
                    let vat = (vatRate * demandAmount) / (100 + vatRate)

                    form.value.lineItems.push({
                        id: demandItem.id,
                        item_code: demandItem.inventory_item.stock_id_code,
                        item_description: demandItem.inventory_item.description,
                        vat_type: demandItem.inventory_item.tax_manager.title,
                        demand_quantity: numberWithCommas(demandItem.demand_quantity),
                        current_cost: currentCost.toFixed(2),
                        new_cost: numberWithCommas(newCost.toFixed(2)),
                        delta: delta.toFixed(2),
                        vat_rate: vatRate,
                        exclusive: (demandAmount - vat).toFixed(2),
                        vat: vat.toFixed(2),
                        demand_amount: demandAmount.toFixed(2),
                    })
                })

                const totalExclusive = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.exclusive), 0).toFixed(2)
                })

                const totalVat = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.vat), 0).toFixed(2)
                })

                const totalDemandAmount = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.demand_amount), 0).toFixed(2)
                })

                const originalAmount = ref(0)
                const needsApproval = computed(() => {
                    return Math.abs(originalAmount.value - totalDemandAmount.value) > 1000
                })

                const processing = ref(false)

                const convertDemand = () => {
                    if (!form.value.supplier_reference) {
                        formUtil.errorMessage('Enter Supplier Reference')
                        return
                    }

                    if (!form.value.cu_invoice_no) {
                        formUtil.errorMessage('Enter CU Invoice No')
                        return
                    }

                    if (!form.value.note) {
                        formUtil.errorMessage('Enter note')
                        return
                    }
                    
                    Swal.fire({
                        title: 'Convert demand to credit note?',
                        showCancelButton: true,
                        confirmButtonText: `Convert`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            processing.value = true

                            axios.post(`/api/convert-price-demand/${demand.id}`, form.value)
                                .then(response => {
                                    formUtil.successMessage(response.data.message)

                                    processing.value = false

                                    window.location = window.redirectRoute
                                })
                                .catch(error => {
                                    formUtil.errorMessage(error.response.data.error)
                                    processing.value = false
                                })
                        }
                    }) 

                }

                onMounted(() => {
                    originalAmount.value = totalDemandAmount.value
                })

                return {
                    user,
                    form,
                    totalExclusive,
                    totalVat,
                    totalDemandAmount,
                    numberWithCommas,
                    processing,
                    convertDemand,
                    page, 
                    demand
                }
            }
        }).mount('#app')
    </script>
@endsection

