@extends('layouts.admin.admin')

@section('content')
    <section class="content" id="create-work-order-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Add Work Order </h3>

                    <a href="{{ route("$base_route_name.index") }}" class="btn btn-default" role="button">
                        << Back to Work Orders
                    </a>
                </div>
            </div>

            <div class="box-body">
                @php
                    $formAttributes = [
                        'route' => "$base_route_name.store",
                        'method' => 'post',
                        'id' => 'work-order-form',
                    ];
                @endphp

                {{ Form::open($formAttributes) }}
                <div class="row">
                    <div class="form-group col-md-3">
                        {{ Form::label('order_number', 'Order Reference', ['class' => 'control-label']) }}
                        {{ Form::text('order_number', $order_number, ['class' => 'form-control', 'disabled' => true]) }}
                    </div>

                    <div class="form-group col-md-3">
                        {{ Form::label('production_plant_id', 'Production Plant', ['class' => 'control-label']) }}
                        <select id="production_plant_id" name="production_plant_id" class="form-control" required v-model="selectedLocationStoreId">
                            <option :value="location.id" v-for="location in locationStores" :key="location.id"
                                    :label="location.location_name"></option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        {{ Form::label('wa_inventory_item_id', 'Production Item', ['class' => 'control-label']) }}
                        <select id="wa_inventory_item_id" name="wa_inventory_item_id" class="form-control" required v-model="selectedProductId">
                            <option :value="product.id" v-for="product in producibleProducts" :key="product.id" :label="product.title"></option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        {{ Form::label('production_quantity', 'Production Quantity', ['class' => 'control-label']) }}
                        <input type="number" v-model.number="productionQuantity" name="production_quantity" id="production_quantity"
                               class="form-control" min="1" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-3">
                        {{ Form::label('description', 'Notes', ['class' => 'control-label']) }}
                        <textarea name="description" id="description" class="form-control"></textarea>
                    </div>
                </div>

                <div style="margin-top: 15px;" v-if="selectedProductId">
                    <div class="box-header with-border">
                        <h3 class="box-title"> BOM </h3>
                    </div>

                    <div class="d-flex flex-column box-body" v-if="selectedItemBom.length === 0">
                        <p> The selected item does not have a BOM. </p>
                        {{--                            <a href="{{ route('maintain-items.show-bom') }}"> Manage Item BOM </a>--}}
                    </div>

                    <div v-else class="table-responsive box-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col"> #</th>
                                <th scope="col"> Raw Material</th>
                                <th scope="col"> Base Quantity</th>
                                <th scope="col"> Required Quantity</th>
                                <th scope="col"> Unit Cost</th>
                                <th scope="col"> Total Cost</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="(bomItem, index) in selectedItemBom" :key="bomItem.id">
                                <th scope="row"> @{{ index + 1 }}</th>
                                <td> @{{ bomItem.raw_material_name }}</td>
                                <td> @{{ bomItem.base_quantity }}</td>
                                <td> @{{ getBomItemRequiredQuantity(bomItem) }}</td>
                                <td> @{{ bomItem.unit_cost }}</td>
                                <td> @{{ getBomItemTotalCost(bomItem) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="margin-top: 15px;" v-if="selectedProductId">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Operation Steps </h3>
                    </div>

                    <div class="d-flex flex-column box-body" v-if="selectedItemOperationSteps.length === 0">
                        <p> The selected item does not have any operation steps. </p>
                        {{--                            <a href="{{ route('maintain-items.show-bom') }}"> Manage Item BOM </a>--}}
                    </div>

                    <div v-else class="table-responsive box-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col"> Step Number</th>
                                <th scope="col"> Operation</th>
                                <th scope="col"> Duration</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="(process, index) in selectedItemOperationSteps" :key="process.id">
                                <th scope="row"> @{{ process.step_number }}</th>
                                <td> @{{ process.operation }}</td>
                                <td> @{{ process.duration }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{ Form::close() }}

                <div style="margin-top: 15px;">
                    <button class="btn btn-primary" @click.prevent="saveWorkOrder" :disabled="!workOrderIsValid"> Save Work Order</button>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="module">
        import {createApp} from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js'

        const app = createApp({
            data() {
                return {
                    producibleProducts: [],
                    selectedProductId: null,
                    locationStores: [],
                    selectedLocationStoreId: null,
                    productionQuantity: 1
                }
            },

            computed: {
                selectedItem() {
                    return this.producibleProducts.find(item => item.id === this.selectedProductId)
                },

                selectedItemBom() {
                    return this.selectedItem?.bom ?? []
                },

                selectedItemOperationSteps() {
                    return this.selectedItem?.processes ?? []
                },

                workOrderIsValid() {
                    return (this.selectedItem !== undefined)
                        && this.productionQuantity
                        && (this.productionQuantity > 0)
                        && this.selectedLocationStoreId
                        && (this.selectedItemBom.length > 0) && (this.selectedItemOperationSteps.length > 0)
                }
            },

            created() {
                this.fetchFinishedProductList()
                this.fetchLocationStores()
            },

            methods: {
                fetchFinishedProductList() {
                    axios.get('/api/producible-products').then(response => {
                        this.producibleProducts = response.data
                    }).catch(error => {
                        // pass for now
                        // TODO: Handle exception
                    })
                },

                fetchLocationStores() {
                    axios.get('/api/location-stores').then(response => {
                        this.locationStores = response.data
                    }).catch(error => {
                        // pass for now
                        // TODO: Handle exception
                    })
                },

                getBomItemRequiredQuantity(item) {
                    return item.base_quantity * this.productionQuantity
                },

                getBomItemTotalCost(item) {
                    return item.unit_cost * this.getBomItemRequiredQuantity(item)
                },

                saveWorkOrder(e) {
                    e.preventDefault();

                    $("#work-order-form").submit()
                }
            },
        })

        app.mount('#create-work-order-page')
    </script>
@endsection
