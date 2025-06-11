@extends('layouts.admin.admin')

@php
    $user = getLoggeduserProfile();
@endphp

<script>
    window.user = {!! $user !!}
    window.branches = {!! json_encode(getBranchesDropdown()) !!}
    window.locations = {!! json_encode(getStoreLocationDropdownByBranch($user->restaurant_id)) !!}
    window.bins = {!! json_encode(getUnitOfMeasureList()) !!}
</script>

<style>
table .select2-selection__arrow b{
    display:none !important;
}
</style>

@section('content')
    <div id="app" v-cloak>
        <section class="content" id="return-from-store">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Create Return From Store </h3>
                </div>
                <div class="box-body">
                    <form action="" method="post" v-cloak>
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" :value="user.name" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Return Date</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" readonly value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Note</label>
                                        <div class="col-sm-7">
                                            <textarea class="form-control" rows="3" v-model="form.note"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" :disabled="!canChangeBranch" v-model="form.branch_id" :onchange="branchChanged">
                                                <option :value="index" :selected="index == user.restaurant_id" v-for="(branch, index) in branches" :key="index">
                                                    @{{ branch }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Store Location</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" id="location-select" :disabled="!canChangeStoreLocation || !Object.keys(locations).length" v-model="form.location_id" :onchange="locationChanged">
                                                <option :value="index" :selected="index == user.wa_location_and_store_id" v-for="(location, index) in locations" :key="index">
                                                    @{{ location }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Bin Location</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" id="uom-select" :disabled="!Object.keys(bins).length || !canChangeBin" v-model="form.uom_id" :onchange="uomChanged">
                                                <option :value="index" :selected="index == user.wa_unit_of_measures_id" v-for="(bin, index) in bins" :key="index">
                                                    @{{ bin }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Supplier Name</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" id="supplier-select" v-model="form.supplier_id" :onchange="supplierChanged" :disabled="!Object.keys(suppliers).length">
                                                <option :value="index" v-for="(supplier, index) in suppliers" :key="index">
                                                    @{{ supplier }}
                                                </option>
                                            </select>
                                        </div>
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
                        <h3 class="box-title">Return From Store Line</h3>
                        <button type="button" class="btn btn-danger btn-sm" style="position: fixed;bottom: 30%;left:4%;" @click="addItem" :disabled="!form.supplier_id">
                            <i class="fa fa-plus"></i>
                        </button>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>QOH</th>
                                    <th>Quantity</th>
                                    <th>Weight (Kg)</th>
                                    <th>Cost (KES)</th>
                                    <th>Exclusive</th>
                                    <th>VAT</th>
                                    <th>Total Cost (KES)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(lineItem, index) in form.lineItems" :key="index">
                                    <td style="width: 300px" @click="itemSelectClicked">
                                        <select 
                                            class="form-control" 
                                            v-model="form.lineItems[index].code" 
                                            :onchange="itemChanged"
                                            :disabled="!items.length"
                                            style="width: 100%"
                                            :data-index="index"
                                        >
                                            <option :value="item.stock_id_code" v-for="item in items" :key="item.id">
                                                @{{ `${item.stock_id_code} (${item.title})` }}
                                            </option>
                                        </select>

                                    </td>
                                    <td>@{{ form.lineItems[index].description }}</td>
                                    <td>@{{ form.lineItems[index].qoh }}</td>
                                    <td style="width: 100px">
                                        <input 
                                            class="form-control" 
                                            v-model="form.lineItems[index].quantity" 
                                            v-show="form.lineItems[index].code"
                                            @keyUp="quantityInput($event, index)"
                                        >
                                    </td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].weight) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].cost) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].exclusive) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].vat) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].total_cost) }}</span></td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-danger btn-sm" @click="removeItem(index)">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot v-if="form.lineItems.length">
                                <tr>
                                    <th colspan="4" style="text-align:right">
                                        Total Weight (Kg)
                                    </th>
                                    <td>@{{ numberWithCommas(totalWeight) }}</td>
                                    <th colspan="4" style="text-align:right">
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
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <div>
                            <button type="button" class="btn btn-primary btn-sm" :disabled="processing || !form.lineItems.length" @click="submitForm">
                                @{{ processing ? 'Processing...' : 'Process' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('select').select2({
                placeholder: 'Select...',
            });

            $('table select').select2({
                placeholder: '',
            });
        });
    </script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script type="importmap">
        {
        "imports": {
            "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.prod.js"
        }
        }
    </script>

    <script type="module">
        import {createApp, onMounted, ref, watch, computed } from 'vue';

        createApp({
            setup() {
                const user = ref(window.user)
                const branches = ref(window.branches)
                const locations = ref(window.locations)
                const bins = ref(window.bins)
                const suppliers = ref([])

                const permissions = Object.keys(user.value.permissions)

                const canChangeBranch = computed(() => {
                    return permissions.includes('return-to-supplier-from-store___change-branch') || user.value.role_id == 1
                })

                const canChangeStoreLocation = computed(() => {
                    return permissions.includes('return-to-supplier-from-store___change-store-location') || user.value.role_id == 1
                })

                const canChangeBin = computed(() => {
                    return permissions.includes('return-to-supplier-from-store___change-bin') || [1, 154].includes(user.value.role_id)
                })
                
                const formUtil = new Form()
                
                const form = ref({
                    user_id: user.value.id,
                    branch_id: user.value.restaurant_id ?? '',
                    location_id: user.value.wa_location_and_store_id ?? '',
                    uom_id: user.value.wa_unit_of_measures_id ?? '',
                    supplier_id: '',
                    note: '',

                    lineItems: []
                })

                const fetchSuppliers = () => {
                    if (form.value.uom_id) {
                        axios.get(`/api/suppliers-by-uom/${form.value.uom_id}`)
                            .then(response => suppliers.value = response.data)
                            .catch(error => formUtil.errorMessage(error.response.data.error))
                    }
                }

                const branchChanged = (event) => {
                    form.value.branch_id = $(event.target).val();

                    form.value.location_id = ''
                    $('#location-select').val(null).trigger('change')

                    locations.value = []

                    axios.get(`/api/location-and-stores/${form.value.branch_id}`)
                        .then(response => {
                            locations.value = response.data
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                        })
                }

                const locationChanged = (event) => {
                    form.value.location_id = $(event.target).val();

                    form.value.uom_id = ''
                    $('#uom-select').val(null).trigger('change')

                    bins.value = []

                    if (form.value.location_id) {
                        axios.get(`/api/location-store-uom/${form.value.location_id}`)
                            .then(response => {
                                bins.value = response.data
                            })
                            .catch(error => {
                                formUtil.errorMessage(error.response.data.error)
                            })
                    }
                }

                const uomChanged = (event) => {
                    form.value.uom_id = $(event.target).val();

                    suppliers.value = []
                    form.value.lineItems = []

                    fetchSuppliers()
                }
                
                const items = ref([])
                const supplierChanged = () => {
                    form.value.supplier_id = $('#supplier-select').val()

                    form.value.lineItems = [
                        {
                            item_id: '',
                            code: '',
                            description: '',
                            qoh: '',
                            quantity: 0,
                            weight: 0,
                            cost: 0,
                            exclusive: 0,
                            vat: 0,
                            total_cost: 0,
                        }
                    ]

                    if (form.value.supplier_id) {
                        axios.get(`/api/supplier-items/${form.value.supplier_id}`, {
                            params: {
                                location_id: form.value.location_id,
                                uom_id: form.value.uom_id
                            }
                        })
                            .then((response) => {
                                items.value = response.data
                            })
                            .catch((error) => {
                                formUtil.errorMessage(error.response.data.error);
                            })
                    } else {
                        items.value = []
                    }

                    setTimeout(() => {
                        $('table select').select2({
                            placeholder: '',
                        });
                    }, 100);
                }

                const itemChanged = (event) => {
                    let code = event.target.value
                    let index = event.target.dataset.index

                    let item = items.value.find(item => item.stock_id_code == code)

                    if (form.value.lineItems.find(lineItem => lineItem.item_id == item.id)) {
                        formUtil.errorMessage('Item already added')
                        $(event.target).val(null).trigger('change')
                        return
                    } 

                    form.value.lineItems[index].item_id = item.id
                    form.value.lineItems[index].code = item.stock_id_code
                    form.value.lineItems[index].description = item.description
                    form.value.lineItems[index].qoh = item.stock_moves_sum_qauntity ?? 0
                    form.value.lineItems[index].quantity = 0
                    form.value.lineItems[index].weight = 0
                    form.value.lineItems[index].cost = 0
                    form.value.lineItems[index].exclsuive = 0
                    form.value.lineItems[index].vat = 0
                    form.value.lineItems[index].total_cost = 0

                }

                const calculateWeightAndCost = (index) => {
                    let item = items.value.find(item => item.stock_id_code == form.value.lineItems[index].code)

                    let quantity = form.value.lineItems[index].quantity
                    let standardCost = parseFloat(item.standard_cost)
                    let vatRate = parseFloat(item.tax_manager.tax_value)
                    let totalCost = quantity * standardCost
                    let vat = (vatRate * totalCost) / (100 + vatRate)

                    form.value.lineItems[index].weight = (quantity * parseInt(item.net_weight)).toFixed(2)
                    form.value.lineItems[index].cost = standardCost
                    form.value.lineItems[index].exclusive = (totalCost - vat).toFixed(2)
                    form.value.lineItems[index].vat = vat.toFixed(2)
                    form.value.lineItems[index].total_cost = (quantity * standardCost).toFixed(2)
                }

                const quantityInput = (event, index) => {
                    if (event.key == 'Enter') {
                        if (form.value.lineItems[form.value.lineItems.length - 1].item_id) {
                            addItem()
                        } else {
                            formUtil.errorMessage('Fill in all details or previous item')
                        }
                    } else {
                        calculateWeightAndCost(index)
                    }
                }

                const itemSelectClicked = () => {
                    if (!form.value.supplier_id) {
                        formUtil.errorMessage('Select supplier to get returnable items')
                    }
                }

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

                const addItem = () => {
                    form.value.lineItems.push({
                        item_id: '',
                        code: '',
                        description: '',
                        quantity: 0,
                        weight: 0,
                        cost: 0,
                        exclusive: 0,
                        vat: 0,
                        total_cost: 0,
                    })

                    setTimeout(() => {
                        $('table select').select2({
                            placeholder: '',
                        });
                    }, 100);

                }

                const removeItem = (index) => {
                    form.value.lineItems.splice(index, 1)
                }

                const processing = ref(false)
                const submitForm = () => {

                    if (!form.value.note) {
                        formUtil.errorMessage('Enter note')
                        return
                    }

                    if (!form.value.branch_id) {
                        formUtil.errorMessage('Select a branch')
                        return
                    }

                    if (!form.value.location_id) {
                        formUtil.errorMessage('Select a store location')
                        return
                    }

                    if (!form.value.uom_id) {
                        formUtil.errorMessage('Select a bin location')
                        return
                    }

                    if (!form.value.supplier_id) {
                        formUtil.errorMessage('Select a supplier')
                        return
                    }

                    if (!form.value.lineItems.length) {
                        formUtil.errorMessage('Add an item')
                        return
                    } else {
                        let error = false

                        for (let i = 0; i < form.value.lineItems.length; i++) {
                            let lineItem = form.value.lineItems[i]

                            if (!lineItem.quantity) {
                                formUtil.errorMessage(`Enter quantity for ${lineItem.code}`)
                                error = true
                                break
                            }
    
                            if (lineItem.quantity == '0') {
                                formUtil.errorMessage(`Item ${lineItem.code} return quantity should be greater than 1`)
                                error = true
                                break
                            }
    
                            if (!/^\d+(\.\d+)?$/.test(lineItem.quantity) || parseFloat(lineItem.quantity) < 0) {
                                formUtil.errorMessage(`Enter a valid quantity for ${lineItem.code}`);
                                error = true;
                                break;
                            }
                            
    
                            if (lineItem.quantity > lineItem.qoh) {
                                formUtil.errorMessage(`Item ${lineItem.code} return quantity exceeds QOH`)
                                error = true
                                break
                            }
                        }

                        if (error) {
                            return
                        }
                    }

                    processing.value = true

                    axios.post('/api/process-return-from-store', form.value)
                        .then(response => {

                            formUtil.successMessage('Return processed successfully!')

                            $('#supplier-select').val(null).trigger('change')
                            form.value.supplier_id = ''
                            form.value.note = ''
                            form.value.lineItems = []
                            
                            processing.value = false
                        })
                        .catch(error => {
                            console.log(error);
                            if (error?.response?.data?.error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    html: error.response.data.error,
                                });
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    html: 'Something went wrong',
                                });
                            }
                            processing.value = false
                        })
                        
                }

                const numberWithCommas = (value) => {
                    if (value) {
                        let parts = value.toString().split(".");
                        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        return parts.join(".");
                    } else {
                        return "0";
                    }
                }

                onMounted(() => {
                    fetchSuppliers()
                    
                    $('body').addClass('sidebar-collapse');
                })
                
                return {
                    form,
                    user,
                    branches,
                    locations,
                    bins,
                    suppliers,
                    supplierChanged,
                    items,
                    itemChanged,
                    calculateWeightAndCost,
                    addItem,
                    removeItem,
                    totalWeight,
                    totalCost,
                    submitForm,
                    numberWithCommas,
                    processing,
                    quantityInput,
                    itemSelectClicked,
                    branchChanged,
                    locationChanged,
                    uomChanged,
                    totalExclusive,
                    totalVat,
                    canChangeBin,
                    canChangeBranch,
                    canChangeStoreLocation,
                }
            }
        }).mount('#app')
    </script>
@endsection

