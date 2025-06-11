@extends('layouts.admin.admin')

@section('content')
    {{--    @php--}}
    {{--        $user = getLoggeduserProfile();--}}
    {{--    @endphp--}}

    {{--    <script>--}}
    {{--        window.loadingSheets = {!! $loadingSheets !!};--}}
    {{--        window.user = {!! $user !!};--}}
    {{--    </script>--}}

    <section class="content" id="store-loading-sheets">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Dispatched Loading Sheets </h3>
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'store-loading-sheets.dispatched', 'method' => 'get']) !!}
                <div class="row">
                    @if($user->role_id == 1 || isset($my_permissions['dispatched-loading-sheets___view-all'])) 
                    <div class="col-md-3 form-group">
                        <select name="bin" id="bin" class="mlselect">
                            <option value="" selected disabled>Select Bin</option>
                            @foreach ($bins as $bin )
                            <option value="{{$bin->id}}" {{ $bin->id == $selectedBinId ? 'selected' : '' }}>{{$bin->title}}</option>
                                
                            @endforeach
                        </select>

                    </div>
                    @endif

                    <div class="col-md-2 form-group">
                        <input type="date" name="date" id="date" class="form-control" value="{{ request()->date ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    

                    <div class="col-md-2 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                        <a class="btn btn-primary ml-12" href="{!! route('store-loading-sheets.dispatched') !!}">Clear </a>
                    </div>
                </div>

                {!! Form::close(); !!}
                <hr>

                <div class="session-message-container"></div>


                <div class="col-md-12">

                <div class="table-responsive">
                    <table class="table list-table" id="create_datatable_25">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Dispatch Date</th>
                            <th>Dispatch Time</th>
                            <th> Shift Date</th>
                            <th>Dispatch No.</th>
                            <th>Dispatcher</th>
                            <th> Branch</th>
                            <th> Bin</th>
                            {{-- <th> Shift Date</th> --}}
                            <th> Route</th>
                            <th> Salesman</th>
                            <th> Item Count</th>
                            <th> Unfulfilled</th>
                            <th style="width: 10%;">Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($loadingSheets as $index => $loadingSheet)
                            <tr>
                                <th scope="row" style="width: 3%;">{{ $index + 1 }}</th>
                                <td>{{ \Carbon\Carbon::parse($loadingSheet->created_at)->toDateString() }}</td>
                                <td>{{ \Carbon\Carbon::parse($loadingSheet->created_at)->toTimeString() }}</td>
                                <td>{{ $loadingSheet->shift->created_at->format('Y-m-d') }}</td>
                                <td>{{ $loadingSheet->bin?->title. '-' .$loadingSheet->id }}</td>
                                <td>{{ $loadingSheet->dispatcher?->name }}</td>
                                <td> {{ $loadingSheet->branch }}</td>
                                <td> {{ $loadingSheet->bin?->title }}</td>
                                <td>{{ $loadingSheet->shift?->salesman_route?->route_name }}</td>
                                <td>{{ $loadingSheet->shift?->salesman?->name }}</td>
                                <td>{{ count($loadingSheet->items) }}</td>
                                <td>{{ count($loadingSheet->unfulfilled_items) }}</td>
                                <td style="width: 10%;">
                                  
                                    <div class="action-button-div">
                                        <a href="{{route('store-loading-sheets.dispatched-details', $loadingSheet->id)}}" title="view dispatched items"><i class="fa fa-eye text-primary fa-lg"></i> </a>
                                        @if(count($loadingSheet->unfulfilled_items) > 0)
                                            <button class="btn btn-primary" data-toggle="modal" data-target="#dispatch-modal" data-backdrop="static" data-id="{{ $loadingSheet->id }}">
                                                Process</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
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

    <script type="text/javascript">
        $(function () {

            $(".mlselect").select2();
        });
    </script>

    {{--    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>--}}
    {{--    <script type="importmap">--}}
    {{--        {--}}
    {{--          "imports": {--}}
    {{--            "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"--}}
    {{--          }--}}
    {{--        }--}}
    {{--    </script>--}}

    {{--    <script type="module">--}}
    {{--        import {createApp} from 'vue';--}}

    {{--        const app = createApp({--}}
    {{--            data() {--}}
    {{--                return {--}}
    {{--                    activeLoadingSheet: {},--}}
    {{--                }--}}
    {{--            },--}}

    {{--            computed: {--}}
    {{--                currentUser() {--}}
    {{--                    return window.user--}}
    {{--                },--}}

    {{--                toaster() {--}}
    {{--                    return new Form();--}}
    {{--                },--}}
    {{--            },--}}

    {{--            mounted() {--}}
    {{--                $('#dispatch-modal').on('show.bs.modal', (event) => {--}}
    {{--                    let triggeringButton = $(event.relatedTarget);--}}
    {{--                    let idValue = triggeringButton.data('id');--}}
    {{--                    this.activeLoadingSheet = window.loadingSheets.find(sheet => sheet.id === idValue);--}}

    {{--                    setTimeout(() => {--}}
    {{--                        if (this.table) {--}}
    {{--                            this.table.destroy();--}}
    {{--                        }--}}

    {{--                        this.table = $('#dispatch-items').DataTable({--}}
    {{--                            'paging': true,--}}
    {{--                            'lengthChange': true,--}}
    {{--                            'searching': true,--}}
    {{--                            'ordering': true,--}}
    {{--                            'info': true,--}}
    {{--                            'autoWidth': false,--}}
    {{--                            'pageLength': 10,--}}
    {{--                            'initComplete': function (settings, json) {--}}
    {{--                                let info = this.api().page.info();--}}
    {{--                                let total_record = info.recordsTotal;--}}
    {{--                                if (total_record < 11) {--}}
    {{--                                    $('.dataTables_paginate').hide();--}}
    {{--                                }--}}
    {{--                            },--}}
    {{--                            'aoColumnDefs': [{--}}
    {{--                                'bSortable': false,--}}
    {{--                                'aTargets': 'noneedtoshort'--}}
    {{--                            }],--}}
    {{--                        });--}}
    {{--                    }, 50)--}}
    {{--                })--}}
    {{--            },--}}

    {{--            methods: {--}}
    {{--                processDispatch() {--}}
    {{--                    let receivedQtiesAreOk = true--}}
    {{--                    this.activeLoadingSheet.items.forEach(item => {--}}
    {{--                        if (!item.qty_received || isNaN(parseFloat(item.qty_received)) ||  parseFloat(item.qty_received) === 0) {--}}
    {{--                            receivedQtiesAreOk = false--}}
    {{--                        }--}}
    {{--                    })--}}

    {{--                    if (!receivedQtiesAreOk) {--}}
    {{--                        return this.toaster.errorMessage('You have invalid dispatch quantities.');--}}
    {{--                    }--}}

    {{--                    let payload = {--}}
    {{--                        payload: JSON.stringify(this.activeLoadingSheet),--}}
    {{--                        user_id: this.currentUser.id--}}
    {{--                    }--}}

    {{--                    $("#loader-on").show();--}}
    {{--                    axios.post('/api/store-loading-sheets/dispatch', payload).then(response => {--}}
    {{--                        $("#loader-on").hide();--}}
    {{--                        this.toaster.successMessage('Loading sheet dispatched successfully');--}}

    {{--                        window.location.reload();--}}
    {{--                    }).catch(error => {--}}
    {{--                        $("#loader-on").hide();--}}
    {{--                        console.log(error);--}}
    {{--                        this.toaster.errorMessage(error.response?.data?.message ?? error.response)--}}
    {{--                    })--}}
    {{--                }--}}
    {{--            },--}}
    {{--        })--}}

    {{--        app.mount('#store-loading-sheets')--}}
    {{--    </script>--}}
@endsection
