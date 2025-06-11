@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                        <h3 class="box-title"> Group <strong>{{$groupdata}}</strong> Route
                            @if(isset($mainUnmetShops) && !empty($mainUnmetShops))
                                Unmet Shops
                            @elseif(isset($ctnsItems) && !empty($ctnsItems))
                                CTNS
                            @elseif(isset($dznsItems) && !empty($dznsItems))
                                DZNS
                            @endif
                                Report 
                        </h3>

                        <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                            << Back to Group Perfomance Report
                        </a>

                        {{-- <a href="{{ url()->previous() }}" class="btn btn-primary"> <i class="fas fa-arrow-left"></i> Back</a> --}}
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form id="filterForm" action="{{ route('sales-and-receivables-reports.group-data-filter-route-item-report', ['ctns-dzns' => 'ctns', 'ctns-dzns' => 'dzns', 'route_id' => request()->route_id, 'start_date' => request()->start_date, 'end_date' => request()->end_date]) }}" method="get">

                    <input type="hidden" name="ctns_dzns" value="">

                    {{-- <input type="hidden" id="route_id" name="route_id" value=""> --}}

                    <div class="row">
                        @if((isset($ctnsItems) && !empty($ctnsItems)) || (isset($dznsItems) && !empty($dznsItems)))
                            <div class="form-group col-md-2">
                                <label for="" class="control-label"> Start Date </label>
                                <input type="date" name="start_date" value="{{ request()->start_date ?? \Carbon\Carbon::now()->toDateString() }}" class="form-control"/>
                            </div>

                            <div class="form-group col-md-2">
                                <label for="" class="control-label"> End Date </label>
                                <input type="date" name="end_date" value="{{ request()->end_date ?? \Carbon\Carbon::now()->toDateString() }}" class="form-control"/>
                            </div>
                        @endif

                        <div class="form-group col-md-3">
                            <label for="route" class="control-label"> Select Route </label>
                            <select name="route" id="route" class="form-control">
                                <option value="" selected disabled> Select a route</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}" @if(request()->route == $route->id) selected @endif> {{ $route->route_name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" id="route_id" name="route_id" value="{{ request()->route_id }}">

                        <div class="form-group col-md-3">
                            <label class="text-white" style="display: block; color: white!important;"> Action </label>
                            <input type="submit" name="intent" value="FILTER" class="btn btn-primary"/>
                            <input type="submit" name="intent" value="EXCEL" class="btn btn-primary ml-12"/>
                            <input type="submit" name="intent" value="CLEAR" class="btn btn-primary ml-12" onclick="clearForm()"/>
                            
                        </div>
                    </div>
                </form>

                <hr>

                <div class="table-responsive">

                    <table class="table table-bordered table-hover" id="group-performance-table">
                        <thead>
                            @if(isset($mainUnmetShops) && !empty($mainUnmetShops))
                                <tr>
                                    <th>SHOP ID</th>
                                    <th>UNMET SHOP NAME</th>
                                </tr>
                            @else
                                <tr>
                                    <th>ITEM ID</th>
                                    <th>ITEM STOCK ID</th>
                                    <th>DESCRIPTION</th>
                                    <th>PACK SIZE</th>
                                    <th>QUANTITY</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @if(isset($ctnsItems) && !empty($ctnsItems))
                                @foreach($ctnsItems as $item)
                                    <tr>
                                        <td>{{$item['id']}}</td>
                                        <td>{{$item['stock_id_code']}}</td>
                                        <td>{{$item['title']}}</td>
                                        <td>{{$item['pack_size']}}</td>
                                        <td>{{manageAmountFormat($item['quantity'])}}</td>
                                    </tr>
                                @endforeach
                            @endif

                            @if(isset($dznsItems) && !empty($dznsItems))
                                @foreach($dznsItems as $item)
                                    <tr>
                                        <td>{{$item['id']}}</td>
                                        <td>{{$item['stock_id_code']}}</td>
                                        <td>{{$item['title']}}</td>
                                        <td>{{$item['pack_size']}}</td>
                                        <td>{{manageAmountFormat($item['quantity'])}}</td>
                                    </tr>
                                @endforeach
                            @endif

                            @if(isset($mainUnmetShops) && !empty($mainUnmetShops))
                                @foreach ($mainUnmetShops as $mainUnmetShop)
                                    <tr>
                                        <td>{{ $mainUnmetShop['id']}}</td>
                                        <td>{{ $mainUnmetShop['name'] }}</td>
                                    </tr>
                                @endforeach
                            @endif

                        </tbody>
                        <tfoot>
                            @if((isset($ctnsItems) && !empty($ctnsItems)) || (isset($dznsItems) && !empty($dznsItems)))
                                <tr style="border-top: 2px solid black !important; font-weight: bold;">
                                    <td colspan="4" style="text-align: center;">TOTALS</td>
                                    <td>
                                        @if(isset($ctnsItems) && !empty($ctnsItems))
                                            {{number_format($totalCtnsQuantity,2)}}
                                        @elseif(isset($dznsItems) && !empty($dznsItems))
                                            {{number_format($totalDznsQuantity,2)}}
                                        
                                        @endif
                                    </td>
                                </tr>
                            @elseif(isset($mainUnmetShops) && !empty($mainUnmetShops))
                                <tr style="border-top: 2px solid black !important; font-weight: bold;">
                                    <td style="text-align: center;">TOTALS</td>
                                    <td>
                                        {{number_format($unmetShopCount,2)}}
                                    </td>
                                </tr>
                            @else
                            @endif
                        </tfoot>
                    </table>
                    

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

    <script type="text/javascript">
        $(function () {
            $('body').addClass('sidebar-collapse');
            $("#route").select2();
            $("#filter").select2();
            $("#group").select2();
        });

        $(document).ready(function() {
            var ctnsDznsValue = '{{ request()->input('ctns_dzns') }}';
            $('input[name="ctns_dzns"]').val(ctnsDznsValue);
            var routeId = '{{ request()->input('route_id') }}';
            $('input[name="route_id"]').val(routeId);
        });

        function clearForm() {
        document.getElementsByName('start_date')[0].valueAsDate = new Date();
        document.getElementsByName('end_date')[0].valueAsDate = new Date();

        document.getElementById('route').value = '';

        document.getElementsByName('ctns_dzns')[0].value = ctnsDznsValue;
        document.getElementsByName('route_id')[0].value = routeId;

        document.getElementById('filterForm').submit();
    }
        
        $(document).ready(function () {
            $('#group-performance-table').DataTable({
                "paging": true,
                "pageLength": 10,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [[0, "asc"]]
            });
        });
    </script>
    
@endsection

