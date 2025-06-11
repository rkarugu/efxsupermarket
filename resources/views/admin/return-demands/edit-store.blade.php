@extends('layouts.admin.admin')

@php
    $user = getLoggeduserProfile();
    $redirectRoute = '"' . route('return-demands.index') . '"';
@endphp

<script>
    window.user = {!! $user !!}
    window.page = '{!! $page !!}'
    window.demand = {!! $demand !!}
    window.returns = {!! $returns !!}
    window.redirectRoute = {!! $redirectRoute !!}
</script>

@section('content')
    <div id="app" v-cloak>
        <section class="content" id="return-from-store">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> {{ $page }} Demand </h3>
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
                                            <input type="text" class="form-control" :value="branchName" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Store Location</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" :value="locationName" readonly>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Bin Location</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" :value="binName" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Supplier Name</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" :value="supplierName" readonly>
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
                            <h3 class="box-title">Demand Line</h3>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>QOH</th>
                                    <th>Quantity</th>
                                    <th>Weight (Kg)</th>
                                    <th>VAT Type</th>
                                    <th>Cost (KES)</th>
                                    <th>Exclusive</th>
                                    <th>VAT</th>
                                    <th>Total Cost (KES)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(lineItem, index) in form.lineItems" :key="index">
                                    <td>@{{ form.lineItems[index].item_code }}</td>
                                    <td>@{{ form.lineItems[index].item_description }}</td>
                                    <td>@{{ form.lineItems[index].qoh }}</td>
                                    <td>@{{ form.lineItems[index].quantity }}</td>
                                    <td>@{{ numberWithCommas(form.lineItems[index].weight) }}</td>
                                    <td>@{{ form.lineItems[index].vat_type }}</td>
                                    <td>@{{ numberWithCommas(form.lineItems[index].cost) }}</td>
                                    <td>@{{ numberWithCommas(form.lineItems[index].exclusive) }}</td>
                                    <td>@{{ numberWithCommas(form.lineItems[index].vat) }}</td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].total_cost) }}</span></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="10"></td>
                                </tr>
                                <tr>
                                    <th colspan="9" style="text-align:right">
                                        Total Weight (Kg)
                                    </th>
                                    <td>@{{ numberWithCommas(totalWeight) }}</td>
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
                                        Total Cost (KES)
                                    </th>
                                    <td>@{{ numberWithCommas(totalCost) }}</td>
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

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script type="importmap">
        {
            "imports": {
                "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
            }
        }
    </script>

    <script type="module">
        import {createApp, ref, computed } from 'vue';

        createApp({
            setup() {
                const user = window.user
                const page = window.page
                const demand = window.demand
                const returns = window.returns
                
                const formUtil = new Form()
                
                const form = ref({
                    user_id: user.id,
                    demand_id: demand.id,
                    supplier_reference: demand.supplier_reference ?? '',
                    cu_invoice_no: demand.cu_invoice_no ?? '',
                    note: demand.note ?? '',

                    lineItems: []
                })

                const branchName = returns[0].store_return.location.branch.name
                const locationName = returns[0].store_return.location.location_name
                const binName = returns[0].store_return.uom.title
                const supplierName = returns[0].store_return.supplier.name

                const numberWithCommas = (value) => {
                    if (value) {
                        let parts = value.toString().split(".");
                        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        return parts.join(".");
                    } else {
                        return "0";
                    }
                }
                
                returns.forEach(returnItem => {
                    let quantity = returnItem.quantity
                    let sellingPrice = returnItem.cost
                    let vatRate = parseFloat(returnItem.inventory_item.tax_manager.tax_value)
                    let totalCost = quantity * sellingPrice
                    let vat = (vatRate * totalCost) / (100 + vatRate)

                    form.value.lineItems.push({
                        id: returnItem.id,
                        supplier_id: returnItem.store_return.wa_supplier_id,
                        item_code: returnItem.inventory_item.stock_id_code,
                        item_description: returnItem.inventory_item.description,
                        qoh: returnItem.inventory_item.stock_moves_sum_qauntity ?? 0,
                        quantity: quantity,
                        weight: quantity * returnItem.inventory_item.net_weight,
                        vat_type: returnItem.inventory_item.tax_manager.title,
                        vat_rate: vatRate,
                        cost: numberWithCommas(sellingPrice),
                        total_cost: totalCost,
                        exclusive: (totalCost - vat).toFixed(2),
                        vat: vat.toFixed(2),
                    })
                })

                const totalWeight = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.weight), 0).toFixed(2)
                })

                const totalExclusive = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.exclusive), 0).toFixed(2)
                })

                const totalVat = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.vat), 0).toFixed(2)
                })

                const totalCost = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.total_cost), 0).toFixed(2)
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

                            axios.post(`/api/convert-return-demand/${demand.id}`, form.value)
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

                return {
                    form,
                    totalWeight,
                    totalExclusive,
                    totalVat,
                    totalCost,
                    numberWithCommas,
                    processing,
                    supplierName,
                    branchName,
                    locationName,
                    binName,
                    convertDemand,
                    page,
                    demand
                }
            }
        }).mount('#app')
    </script>
@endsection

