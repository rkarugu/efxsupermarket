@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {{$route->route_name." ". $type_name." Returns"}} </h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary"> Back </a>
                </div>
            </div>

            <div class="box-body" style="padding:15px">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="" method="get">
                    <div class="row">
                       <div class="col-md-2">
                       <div class="form-group">
                        <label for="">From</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{$d1}}" readonly>
                       </div>
                        </div>
                        <div class="col-md-2">
                        <div class="form-group">
                       <label for="">To</label>
                      <input type="date" name="end_date" id="end_date" class="form-control" value="{{$d2}}" readonly>
                       </div>
                        </div>


                       
                    </div>
                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Return No</th>
                            <th>Return Date</th>
                            <th>Invoice No</th>
                            <th>Invoice Date</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Return Total</th>
                            
                        </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0;?>
                        @foreach($returns as $key => $return)
                            <tr>
                                <th style="width: 3%;">{{ $loop->index + 1 }}</th>
                                <td> {{ $return->route }} </td>
                                 <td style="text-align: right;"> {{ number_format((float)$return->total_returns, 2) }} </td>
                                 <td><span><a href=""><i class="fa fa-eye" title></i></a></span></td>
                                
                            </tr>
                             <?php $total += $return->total_returns;?>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <th colspan="2"></th>
                             <th style="text-align: right;"> {{ number_format((float)$total, 2) }}</th>
                             <th></th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    

    </section>
@endsection
@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection


@section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>
        function printgrn(transfer_no) {
            jQuery.ajax({
                url: '{{route('transfers.print-return')}}',
                async: false,   //NOTE THIS
                type: 'POST',
                data: {transfer_no: transfer_no},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var divContents = response;
                    var printWindow = window.open('', '', 'width=600');
                    printWindow.document.write(divContents);
                    printWindow.document.close();
                    printWindow.print();
                    printWindow.close();
                }
            });
        }

        $(function () {
            $("#route_id").select2();
            $(".mlselec6t").select2();
        });

    $(document).on("click", ".open-confirmDialog", function () {
     var return_number = $(this).data('id');
     $(".modal-body #return_number").val(return_number );
     // As pointed out in comments, 
     // it is unnecessary to have to manually call the modal.
      $('#approve').modal('show');
    });
    </script>
@endsection