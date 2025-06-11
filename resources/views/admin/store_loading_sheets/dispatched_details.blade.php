@extends('layouts.admin.admin')

@section('content')
    

    <section class="content" id="store-loading-sheets">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Dispatch  {{ $loadingSheet->bin?->title. '-' .$loadingSheet->id }} Items</h3>
                    <div>
                  

                         <a href="{{  route('store-loading-sheets.dispatched') }}" class="btn btn-primary   ">{{'<< '}}Back to Dispatches</a>
                    </div>
      
                 
                </div>
              
            </div>

            <div class="box-body">
                

                <div class="session-message-container"></div>


                <div class="col-md-12">

                <div class="table-responsive">
                    <table class="table list-table" id="create_datatable_25">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Stock Id Code</th>
                            <th>Item</th>
                            <th>Total Quantity</th>
                            <th>Dispatched Quantity</th>
                           
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($dispatchItems as $index => $dispatchItem)
                            <tr>
                                <th scope="row" style="width: 3%;">{{ $index + 1 }}</th>
                                <td>{{ $dispatchItem->item?->stock_id_code }}</td>
                                <td>{{ $dispatchItem->item?->title }}</td>
                                <td>{{ $dispatchItem->total_quantity }}</td>
                                <td>{{ $dispatchItem->dispatched_quantity }}</td>

                              
                               
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

  

@endsection
