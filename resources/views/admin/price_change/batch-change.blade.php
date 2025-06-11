@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp

    <script>
        window.inventoryItems = {!! $inventoryItems !!};
        window.user = {!! $user !!};
    </script>

    <section class="content" id="batch-price-change-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Batch Price Change </h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="filters">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="control-label">Select supplier</label>
                            <select id="selected-supplier-id" class="form-control">
                                <option value="" disabled selected></option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"> {{ $supplier->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="control-label">Select item</label>
                            <select id="selected-item-id" class="form-control">
                                <option value="" disabled selected>Select item</option>
                                @foreach($inventoryItems as $item)
                                    <option value="{{ $item->id }}"> ({{ $item->stock_id_code }}) {{ $item->title }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="table-responsive">
                    <p v-if="selectedItems.length === 0"> No items selected. </p>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th> Item </th>
                            <th> Supplier </th>
                            <th> QoH </th>
                            <th> Min Margin </th>
                            <th>Price List Cost</th>
                            <th>New Price List Cost</th>
                            <th> Current Cost </th>
                            <th> New Cost </th>
                            <th> Delta </th>
                            <th> Current Price </th>
                            <th> New Price </th>
                            <th>Price Change</th>
                            <th>  </th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-for="(item, index) in selectedItems" :key="index" v-cloak>
                            <th style="width: 3%;"> @{{ index + 1 }} </th>
                            <td>@{{ item.stock_id_code }} - @{{ item.title }}</td>
                            <td><p> @{{ item.supplier_names }}</p>
                                <span v-if="!validateSupplier()" style="color: red;">Cannot do a price change for multiple suppliers at once.</span>
                            
                            </td>
                            <td>@{{ item.qoh }}</td>
                            <td>@{{item.min_margin}}</td>
                            <td>@{{ item.price_list_cost }}</td>
                            <td>
                                <input type="text" class="form-control" v-model="item.new_price_list_cost" style="width: 100px;">
                                {{-- <span v-if="!validateMargin(item)" style="color: red;">
                                    <p v-if="item.margin_type === 1">New price should be greater than new cost by at least @{{ item.min_margin }}% margin.</p>
                                    <p v-else>New price should be greater than new cost by at least Kes @{{ item.min_margin }}. </p>
                                </span> --}}


                            </td>

                            <td>@{{ item.current_cost }}</td>
                            <td>
                                <input type="text" class="form-control" v-model="item.new_cost" style="width: 100px;">
                                <span v-if="!validateMargin(item)" style="color: red;">
                                    <p v-if="item.margin_type === 1">New price should be greater than new cost by at least @{{ item.min_margin }}% margin.</p>
                                    <p v-else>New price should be greater than new cost by at least Kes @{{ item.min_margin }}. </p>
                                </span>


                            </td>
                            
                            <td><span style="font-weight: bold;">@{{ getItemDemand(item).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span></td>
                            <td>@{{ item.current_price }}</td>
                            <td>
                                <input type="text" class="form-control" v-model="item.new_price" style="width: 100px;">
                                <span v-if="!validateMargin(item)" style="color: red;">
                                    <p v-if="item.margin_type === 1">New price should be greater than new cost by at least @{{ item.min_margin }}% margin.</p>
                                    <p v-else>New price should be greater than new cost by at least Kes @{{ item.min_margin }}.</p>
                                </span>

                            </td>
                            <td><span style="font-weight: bold;">@{{ getItemPriceChange(item).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span></td>

                            <td><i class="fas fa-trash-alt fa-lg text-danger" style="margin-top: 10px; cursor:pointer;" title="Discard" @click="removeItem(index)"></i></td>
                        </tr>

                        <tr v-if="selectedItems.length > 0" style="border-top: 2px solid black;">
                            <td style="width: 3%;"></td>
                            <td colspan="8"><span style="font-weight: bold; font-size: 18px;">TOTAL DEMAND TO SUPPLIER(S)</span></td>
                            <td colspan="4"><span style="font-weight: bold; font-size: 18px;" v-cloak>@{{ getDemandTotal().toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-end" v-if="selectedItems.length > 0">
                        <button class="btn btn-primary" @click="processPriceChange()" :disabled="!isFormValid || !isDocumentChanged"> Process </button>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>

    <div id="loader-on" style="position: fixed;top: 0;text-align: center;z-index: 999999;width: 100%;height: 100%;background: #000000b8;display:none;">
        <div class="loader" id="loader-1"></div>
    </div>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="importmap">
        {
          "imports": {
            "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
          }
        }
    </script>

    <script type="module">
        import {createApp} from 'vue';

        const app = createApp({
            data() {
                return {
                    selectedItems: [],
                    displayableItems: [],
                }
            },

            computed: {
                currentUser() {
                    return window.user
                },

                toaster() {
                    return new Form();
                },
                isFormValid() {
                for (let item of this.selectedItems) {
                    if (!this.validateMargin(item)) {
                        return false;
                    }
                }
                return true; 
                },
                isDocumentChanged() {
                    return this.selectedItems.some(item => item.new_cost !== item.current_cost || item.new_price !== item.current_price || item.price_list_cost !== item.new_price_list_cost);
                }
           
            },

            mounted() {
                $('body').addClass('sidebar-collapse');

                let selectedSupplierField = $("#selected-supplier-id");
                let selectedItemField = $("#selected-item-id");
                selectedSupplierField.select2({
                    placeholder: 'Select supplier',
                    allowClear: true
                });
                selectedItemField.select2();

                selectedSupplierField.change(() => {
                    this.selectedItems = [];

                    if (selectedItemField.data('select2')) {
                        selectedItemField.select2('destroy');
                        selectedItemField.attr('disabled', true);
                    }

                    let selectedSupplierId = selectedSupplierField.val();
                    if (selectedSupplierId) {
                        let items = window.inventoryItems.filter(_item => _item.supplier_ids.includes(parseInt(selectedSupplierId)));
                        items.forEach(item => {
                            console.log(item);
                            item.supplier_names = selectedSupplierField.find(':selected').text();

                            this.selectedItems.push({
                                id: item.id,
                                title: item.title,
                                stock_id_code: item.stock_id_code,
                                supplier_names: item.supplier_names,
                                qoh: item.qoh,
                                min_margin:item.percentage_margin,
                                price_list_cost: item.price_list_cost,
                                new_price_list_cost: item.price_list_cost,
                                current_cost: item.standard_cost,
                                current_valuation: item.standard_cost * item.qoh,
                                new_cost: item.standard_cost,
                                current_price: item.selling_price,
                                new_price: item.selling_price,
                                margin_type: item.margin_type,
                            });
                        })
                    } else {
                        selectedItemField.attr('disabled', false);
                        selectedItemField.select2();
                    }
                });

                selectedItemField.change(() => {
                   let selectedId = selectedItemField.val();
                   let item = window.inventoryItems.find(_item => _item.id === parseInt(selectedId));
                   //check if it exists
                   let isItemAlreadyAdded = this.selectedItems.some(selectedItem => selectedItem.id === item.id);
                   if (!isItemAlreadyAdded) {
                    this.selectedItems.push({
                       id: item.id,
                       title: item.title,
                       stock_id_code: item.stock_id_code,
                       supplier_names: item.supplier_names, 
                       qoh: item.qoh,
                       min_margin:item.percentage_margin,
                       price_list_cost:item.price_list_cost,
                       new_price_list_cost:item.price_list_cost,
                       current_cost: item.standard_cost,
                       current_valuation: item.standard_cost * item.qoh,
                       new_cost: item.standard_cost,
                       current_price: item.selling_price,
                       new_price: item.selling_price,
                       margin_type: item.margin_type,
                   });
                } else {
                    alert('Item has already been added.');
                }


                 
                });
            },

            methods: {
                getItemNewValuation(item) {
                    return item.new_cost * item.qoh;
                },

                getItemDemand(item) {
                    if (item.qoh === 0) {
                        return 0;
                    }

                    if (parseFloat(item.new_cost) >= item.current_cost) {
                        return 0;
                    }

                    return item.current_valuation - this.getItemNewValuation(item)
                },
                getItemPriceChange(item) {

                    return item.new_price - item.current_price;
                },
                validateMargin(item) {
                    if (item.margin_type === 0) {
                        if (parseFloat(item.new_price) < (parseFloat(item.new_cost) + parseFloat(item.min_margin))) {
                            return false;
                        }
                    } else  if (item.margin_type === 1) {
                        if (item.new_price < item.new_cost * (1 + item.min_margin / 100)) {
                            return false;
                        }
                    }
                    return true;
               
                },
                validateSupplier() {
                    const suppliers = new Set();
                    for (let item of this.selectedItems) {
                        suppliers.add(item.supplier_names);
                    }
                    return suppliers.size === 1; 
                },
                getDemandTotal() {
                    let total = 0;
                    this.selectedItems.forEach(item => {
                        total += this.getItemDemand(item);
                    });

                    return total;
                },

                removeItem(index) {
                    this.selectedItems.splice(index, 1);
                    let selectedItemField = $("#selected-item-id");
                    selectedItemField.val("").trigger("change"); // Clear the selected item and trigger the update
                    
                    // Ensure the removed item is selectable again in the dropdown
                    selectedItemField.select2('destroy'); // Reinitialize the select2 dropdown to reflect the updated list
                    selectedItemField.select2();
                },

                processPriceChange() {
                    let form = new Form();

                    let payload = {
                        items: this.selectedItems.map(item => ({
                            ...item,
                            new_valuation: this.getItemNewValuation(item),
                            demand: this.getItemDemand(item)
                        })),
                        demand_total: this.getDemandTotal(),
                        user_id: this.currentUser.id
                    };

                    $("#loader-on").show();

                    axios.post('/api/process-batch-price-change', payload)
                        .then(response => {
                            $("#loader-on").hide();

                            form.successMessage('Batch price change processed successfully.');
                            window.location.reload();

                        })
                        .catch(error => {
                            console.log(error);
                            form.errorMessage(error.response.data.message);   
                            $("#loader-on").hide();
                        });
                    

                },
             
            },
        })

        app.mount('#batch-price-change-page')
    </script>
@endsection
