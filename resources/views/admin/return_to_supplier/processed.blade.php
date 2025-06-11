@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp

    <section class="content" id="return-from-grn-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Processed Returns </h3>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Return No</th>
                            <th>Return Date</th>
                            <th>Returned By</th>
                            <th>Item</th>
                            <th>GRN No</th>
                            <th>Supplier</th>
                            <th>Date Received</th>
                            <th>Qty Received</th>
                            <th>Qty Returned</th>
                            <th>Reason</th>
                        </tr>
                        </thead>

                       <tbody>
                       @foreach($returns as $index => $return)
                           <tr>
                               <th style="width: 3%;"> {{ $index + 1 }}</th>
                               <td>{{ $return->return_number }}</td>
                               <td>{{ $return->date }}</td>
                               <td>{{ $return->user }}</td>
                               <td>{{ $return->grn->item_code }} - {{ $return->grn->item_description }}</td>
                               <td>{{ $return->grn->grn_number }}</td>
                               <td>{{ $return->supplier }}</td>
                               <td>{{ $return->grn->date }}</td>
                               <td>{{ $return->grn->qty_received }}</td>
                               <td>{{ $return->returned_quantity }}</td>
                               <td>{{ $return->reason }}</td>
                           </tr>
                       @endforeach
                       </tbody>
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
@endsection
