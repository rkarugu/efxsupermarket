@extends('layouts.admin.admin')

@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border ">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Order Details : {{$orderItems->requisition_no}}</h3>
                    
                    <a href="{{  route('salesman-shift-details', $orderItems->wa_shift_id) }}" class="btn btn-primary">{{'<< '}}Back to shift summary</a>
                    


                   
                   
                </div>


            </div>
        
            <div class="box-body">

                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>

                                <th>#</th>
                                <th>Item</th>
                                <th>Selling Price</th>
                                <th>Quantity Sold</th>
                                <th>Tonnage</th>



                                <th>Item Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $dataelement)
                                <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $dataelement['name'] }}</td>
                                    <td>{!! format_amount_with_currency( $dataelement['item_price']) !!}</td>


                                    <td>{{ $dataelement['quantity'] }}</td>
                                    <td>{{ $dataelement['tonnage'] }}</td>


                                    <td>{!! format_amount_with_currency( $dataelement['total_price'] ) !!}</td>
                                   
                                  
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
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            $(".mlselect").select2();
        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
   


    <script type="text/javascript" class="init">
        $(document).ready(function() {
            $('#create_datatable1').DataTable({
                pageLength: "100",
                "order": [
                    [0, "desc"]
                ]
            });
        });
     </script>
@endsection
