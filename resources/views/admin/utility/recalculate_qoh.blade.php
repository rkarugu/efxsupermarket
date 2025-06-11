@extends('layouts.admin.admin')

<script>
    window.items = @json($items);
    window.locations = @json($locations);
</script>

@section('content')
    <div id="app" v-cloak>
        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="box-header-flex">
                        <h3 class="box-title"> Recalculate New QOH </h3>
                    </div>
                    <hr>

                    <div class="box-body">
                        <form action="" method="post" v-cloak>
                            @csrf
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="inputEmail3" class="control-label">Location</label>
                                                <select class="form-control" v-model="form.location_id"
                                                    :onchange="selectLocation">
                                                    <option value="">Select...</option>
                                                    <option v-for="(location, index) in locations" :value="location.id"
                                                        :key="index">@{{ location.location_name }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="inputEmail3" class="control-label">Item Description</label>
                                                <select class="form-control" v-model="form.item_id"
                                                    :disabled="disableitemdropdown" :onchange="selectItem">
                                                    <option value="">Select...</option>
                                                    <option v-for="(item, index) in items" :value="item.stock_id_code"
                                                        :key="index">@{{ item.title }}</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <button :disabled="disableitemdropdown" type="button" class="btn btn-danger btn-sm"
                                            style="margin-top: 25px" @click.prevent="updateQoh">
                                            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                                            <i v-else class="fa-solid fa-pen-to-square"></i>
                                            @{{ loading ? ' Processing...' : ' Update New QOH' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </section>

        <section class="content">
            <div class="box box-primary">

                <div class="box-header with-border ">
                    <div class="d-flex justify-content-between align-items-center">

                        <h3 class="box-title">Stock Movements</h3>

                    </div>
                </div>

                <div class="box-body">
                    <div class="col-md-12 no-padding-h">

                        <table class="table table-bordered table-hover" id="item-moves-table">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">Date</th>
                                    <th>User Name</th>
                                    <th>Store Location</th>
                                    <th>Qty In</th>
                                    <th>Qty Out</th>
                                    <th>New QOH</th>
                                    <th>Selling Price</th>
                                    <th>Reference</th>
                                    <th>Document No</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="list in lists" :key="list.id">
                                    <td style="width: 15%;">@{{ formatDate(list.created_at) }}</td>
                                    <td style="width: 15%;">@{{ list.get_related_user.name }}</td>
                                    <td>@{{ list.get_location_of_store.location_name }}</td>
                                    <td>@{{ list.qauntity >= 0 ? +list.qauntity : NULL }}</td>
                                    <td>@{{ list.qauntity < 0 ? -list.qauntity : NULL }}</td>
                                    <td>@{{ list.new_qoh }}</td>
                                    <td>@{{ list.selling_price }}</td>
                                    <td>@{{ list.refrence }}</td>
                                    <td>@{{ list.document_no }}</td>
                                    <td>@{{ getStockMoveType(list) }}</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </section>

    </div>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>

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

                const items = ref(window.items)
                const locations = ref(window.locations)
                const lists = ref([])
                const itemmoves = ref([])
                const formUtil = new Form()
                const loading = ref(false)
                const disableitemdropdown = ref(true)

                const form = ref({
                    item_code: '',
                    item_id: '',
                    location_id: ''
                })

                const getStockMoveType = (list) => {
                    let type = '';
                    let inv = [];

                    if (list.document_no !== undefined && list.document_no !== null && list.document_no !==
                        '') {
                        inv = list.document_no.split('-');
                    }

                    switch (true) {
                        case !!list.wa_purchase_order_id:
                            type = "Purchase";
                            break;
                        case !!list.wa_internal_requisition_id:
                            type = "Sales Invoice";
                            break;
                        case !!list.stock_adjustment_id:
                            type = "Adjustment";
                            break;
                        case !!list.wa_inventory_location_transfer_id:
                            type = "Delivery Note";
                            break;
                        case !!list.ordered_item_id:
                            type = "Ingredients booking";
                            break;
                        case !!list.document_no && inv.includes("INV"):
                            type = "Sales Invoice";
                            break;
                        case !!list.document_no && inv.includes("CS"):
                            type = "Cash Sales";
                            break;
                        case !!list.document_no && inv.includes("RTN"):
                            type = "Return";
                            break;
                        case !!list.document_no && inv.includes("RSSC"):
                            type = "Receive Stock store-C";
                            break;
                        case !!list.document_no && inv.includes("IRSC"):
                            type = "Internal Requisition Store-C";
                            break;
                        case !!list.document_no && inv.includes("STB"):
                            type = "Stock Break";
                            break;
                        case !!list.document_no && inv.includes("MARCH24"):
                            type = "Transfer";
                            break;
                        case !!list.document_no && inv.includes("RFS"):
                            type = "Return From Store";
                            break;
                        default:
                            type = "";
                    }

                    return type;
                }

                const selectLocation = (event) => {
                    form.value.location_id = event.target.value
                    disableitemdropdown.value = false
                }

                const formatDate = (date) => {
                    const formattedDate = new Date(date);
                    const day = String(formattedDate.getDate()).padStart(2, '0');
                    const month = String(formattedDate.getMonth() + 1).padStart(2, '0');
                    const year = formattedDate.getFullYear();
                    const hours = String(formattedDate.getHours()).padStart(2, '0');
                    const minutes = String(formattedDate.getMinutes()).padStart(2, '0');
                    const seconds = String(formattedDate.getSeconds()).padStart(2, '0');

                    return `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
                }

                const selectItem = (event) => {
                    form.value.item_id = event.target.value

                    axios.get(
                            `/admin/process-item-stock-moves-data/${form.value.item_id}/${form.value.location_id}`
                            )
                        .then(response => {
                            console.log(response.data)
                            itemmoves.value = response.data.lists
                            lists.value = response.data.lists
                            if (lists.value != []) {
                                setTimeout(() => {
                                    $('#item-moves-table').DataTable().destroy()
                                    $('#item-moves-table').DataTable({
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
                                }, 20)
                            }
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                        })
                }

                const updateQoh = () => {
                    loading.value = true
                    axios.post(`/admin/recalculate-new-qoh-data/${form.value.item_id}`)
                        .then(response => {
                            itemmoves.value = []
                            formUtil.successMessage('Item QOH Updated')
                            loading.value = false
                            window.location.href = 'maintain-items'
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                        })
                }

                const destroyTable = () => {
                    $('#item-moves-table').DataTable().destroy()
                }

                const triggerDataTable = () => {
                    $('#item-moves-table').DataTable({
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
                    // triggerDataTable()
                })

                computed(() => {

                })

                return {
                    items,
                    locations,
                    lists,
                    form,
                    selectItem,
                    selectLocation,
                    updateQoh,
                    itemmoves,
                    loading,
                    disableitemdropdown,
                    formatDate,
                    getStockMoveType
                }
            }
        }).mount('#app')
    </script>
@endsection
