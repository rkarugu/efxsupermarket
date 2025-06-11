@extends('layouts.admin.admin')
@section('content')
<a href="{{ route('supreme-store-receive.index') }}" class="btn btn-primary">Back</a>
<br>
<section class="content"  style="min-height: 10px">    
    <div class="box box-primary" style="margin-bottom: 0px">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
            <div class = "row">
                <div class = "col-sm-4">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="">Date</label>
                                <span class="form-control">{{$data->date}}</span>
                            </div>
                        </div>
                </div>

                <div class = "col-sm-4">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Time</label>
                            <span class="form-control">{{$data->time}}</span>
                        </div>
                    </div>
                </div>

                <div class = "col-sm-4">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">User</label>
                            <span class="form-control">{{@$data->user->name}}</span>
                        </div>
                    </div>
                </div>
            </div>       
            
    </div>
</section>

       <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             
                          
                            <div class="col-md-12 no-padding-h ">
                           <h3 class="box-title"> Items </h3>

                            <span id = "requisitionitemtable">
                                <table class="table table-bordered table-hover" id="mainItemTable">
                                    <thead>
                                    <tr>
                                      <th>Item</th>
                                      <th>Description</th>
                                      <th>Unit</th>
                                      <th>QTY</th>
                                      <th>Location</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $total =0;
                                    @endphp
                                    @foreach ($data->items as $item)
                                        @php
                                            $total +=$item->qty;
                                        @endphp
                                       <tr>
                                           <td>{{@$item->item->stock_id_code}}</td>
                                           <td>{{@$item->item->description}}</td>
                                           <td>{{@$item->item->pack_size->title}}</td>
                                           <td>{{$item->qty}}</td>
                                           <td>{{@$item->location->location_name}}</td>
                                       </tr>
                                    @endforeach


                                    </tbody>
                                    <tfoot>
                                      <tr>
                                        <th colspan="3" style="text-align:right">
                                        Total Qty
                                        </th>
                                        <td colspan="2"><span id="total_exclusive">{{$total}}</span></td>
                                      </tr>
                                    
                                    </tfoot>
                                </table>
                                </span>
                            </div>
                       


                        </div>
                    </div>


    </section>
@endsection