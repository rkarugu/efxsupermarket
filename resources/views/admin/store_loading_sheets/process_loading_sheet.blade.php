@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp

    <script>
        window.loadingSheets = {!! $loadingSheets !!};
        window.user = {!! $user !!};
        window.sheetId = {!! $id !!}
            window.activeLoadingSheet = {!! $activeLoadingSheet !!};
    </script>

    <section class="content" id="store-loading-sheets">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">Dispatch Loading Sheet </h3>
                    <a href="{{ route('store-loading-sheets.index') }}" class="btn btn-primary">Back</a>

                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container"></div>

                <div class="table-responsive">
                    <table class="table" id="dispatch-items">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Total Quantity</th>
                            <th>Quantity Dispatched</th>
                        </tr>
                        </thead>

                        <tbody id="dispatch-items-body">
                        <tr v-for="(item, index) in activeLoadingSheet.items" :key="item.id" v-cloak>
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ item.item_name }}</td>
                            <td> @{{ item.total_quantity }}</td>
                            <td>
                                <input type="text" class="form-control" placeholder="Qty received" v-model="item.qty_received" readonly>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box-footer">
                <div class="box-header-flex">
                    <a href="{{ route('store-loading-sheets.index') }}" class="btn btn-secondary" style="background-color:#f7f7f7 !important; border-color:#f7f7f7 !important; color:grey !important;">Cancel</a>
                    <button type="button" class="btn btn-primary" @click="processDispatch">Process Dispatch</button>
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
                    activeLoadingSheet: {},
                }
            },

            computed: {
                currentUser() {
                    return window.user
                },

                sheetId() {
                    return window.sheetId
                },

                toaster() {
                    return new Form();
                },
            },

            mounted() {
                this.activeLoadingSheet = window.loadingSheets.find(sheet => sheet.id === sheetId);
                this.activeLoadingSheet.items.forEach(item => {
                    item.qty_received = item.total_quantity
                })
            },

            methods: {
                processDispatch() {
                    let receivedQtiesAreOk = true
                    this.activeLoadingSheet.items.forEach(item => {

                        if (item.qty_received === null || item.qty_received === undefined || isNaN(parseFloat(item.qty_received)) || (parseFloat(item.qty_received) > item.total_quantity)) {
                            receivedQtiesAreOk = false
                        }
                    })

                    if (!receivedQtiesAreOk) {
                        return this.toaster.errorMessage('You have invalid dispatch quantities.');
                    }

                    let payload = {
                        payload: JSON.stringify(this.activeLoadingSheet),
                        user_id: this.currentUser.id
                    }

                    $("#loader-on").show();
                    axios.post('/api/store-loading-sheets/dispatch', payload).then(response => {
                        $("#loader-on").hide();
                        this.toaster.successMessage('Loading sheet dispatched successfully');

                        // window.location.reload();
                        window.location.href = '/admin/store-loading-sheets';
                    }).catch(error => {
                        $("#loader-on").hide();
                        console.log(error);
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response)
                    })
                }
            },
        })

        app.mount('#store-loading-sheets')
    </script>
@endsection
