@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp

    <script>
        window.grns = {!! $grns !!};
        window.user = {!! $user !!};
    </script>

    <section class="content" id="return-from-grn-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Return To Supplier From GRN </h3>
            </div>

            <div class="box-body">
                <div class="filters">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="control-label">Select GRN</label>
                            <select id="selected-grn-number" class="form-control">
                                <option value="" disabled selected>Select GRN</option>
                                @foreach($grnNames as $grn)
                                    <option value="{{ $grn }}"> {{ $grn }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="control-label">Select supplier</label>
                            <select id="selected-supplier-id" class="form-control">
                                <option value="" disabled selected></option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"> {{ $supplier->name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="table-responsive">
                    <p v-if="selectedItems.length === 0"> No items selected. </p>

                    <div class="alert alert-warning alert-dismissible" v-if="selectedItems.length > 0" v-cloak>
                        Items with 0 return quantities will be ignored

                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>GRN Number</th>
                            <th>PO Number</th>
                            <th>Item</th>
                            <th>Supplier</th>
                            <th>Qty Received</th>
                            <th>Already Returned</th>
                            <th>Return Qty</th>
                            <th>Reason</th>
                        </tr>
                        </thead>

                        <tr v-for="(item, index) in selectedItems" :key="index" v-cloak>
                            <th style="width: 3%;"> @{{ index + 1 }}</th>
                            <td>@{{ item.grn_number }}</td>
                            <td>@{{ item.po_number }}</td>
                            <td>@{{ item.item_code }} - @{{ item.item_description }}</td>
                            <td>@{{ item.supplier }}</td>
                            <td>@{{ item.qty_received }}</td>
                            <td>@{{ item.already_returned_qty }}</td>
                            <td>
                                <input type="text" class="form-control" v-model="item.return_quantity" style="width: 100px;" :id="`return_quantity-${index}`">
                            </td>
                            <td>
                                <input type="text" class="form-control" v-model="item.reason" style="width: 200px;" :id="`reason-${index}`">
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-end" v-if="selectedItems.length > 0">
                        <button class="btn btn-primary" @click="processReturns"> Process Returns</button>
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
                }
            },

            computed: {
                currentUser() {
                    return window.user
                },

                toaster() {
                    return new Form();
                },
            },

            mounted() {
                // $('body').addClass('sidebar-collapse');
                let selectedSupplierField = $("#selected-supplier-id");
                let selectedGrnField = $("#selected-grn-number");
                selectedSupplierField.select2({
                    placeholder: 'Select supplier',
                    allowClear: true
                });
                selectedGrnField.select2();

                selectedSupplierField.change(() => {
                    this.selectedItems = [];

                    if (selectedGrnField.data('select2')) {
                        selectedGrnField.select2('destroy');
                        selectedGrnField.attr('disabled', true);
                    }

                    let selectedSupplierId = selectedSupplierField.val();
                    if (selectedSupplierId) {
                        let items = window.grns.filter(_item => _item.wa_supplier_id === parseInt(selectedSupplierId));
                        items.forEach(item => {
                            item.return_quantity = 0;
                            item.initiated_by = this.currentUser.id;
                            this.selectedItems.push(item);
                        })
                    } else {
                        selectedGrnField.attr('disabled', false);
                        selectedGrnField.select2();
                    }
                });

                selectedGrnField.change(() => {
                    this.selectedItems = []
                    let selectedGrnNumber = selectedGrnField.val();
                    let items = window.grns.filter(grn => grn.grn_number === selectedGrnNumber);
                    items.forEach(item => {
                        item.return_quantity = 0;
                        item.initiated_by = this.currentUser.id;
                        this.selectedItems.push(item);
                    })
                });
            },

            methods: {
                removeItem(index) {
                    this.selectedItems.splice(index, 1);
                },

                processReturns() {
                    let quantitiesAreValid = true
                    this.selectedItems.every(item => {
                        if (isNaN(item.return_quantity) || parseFloat(item.return_quantity) < 0) {
                            quantitiesAreValid = false;
                            return false
                        }

                        return true
                    })

                    if (!quantitiesAreValid) {
                        return this.toaster.errorMessage('You have invalid return quantities');
                    }

                    $("#loader-on").show();
                    axios.post('/api/process-grn-return', {items: JSON.stringify(this.selectedItems)}).then(response => {
                        form.successMessage('Returns processed successfully.');
                        $("#loader-on").hide();

                        window.location.reload();
                    }).catch(error => {
                        $("#loader-on").hide();
                        form.errorMessage(error.response?.data?.message ?? error.response?.data ?? error);
                    });
                }
            },
        })

        app.mount('#return-from-grn-page')
    </script>
@endsection
