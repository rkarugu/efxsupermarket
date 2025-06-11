@extends('layouts.admin.admin')
@section('content')
<a href="{{ route($model.'.index') }}" class="btn btn-primary">Back</a>
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
                                      <th>Source Item</th>
                                      <th>Description</th>
                                      <th>Bal Stock</th>
                                      <th>Destination Code</th>
                                      <th>Destination Item</th>
                                      <th>Source Qty</th>
                                      <th>Conversion Factor</th>
                                      <th>Destination Qty</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($data->items as $item)                                       
                                       <tr>
                                           <td>{{@$item->source_item->stock_id_code}}</td>
                                           <td>{{@$item->source_item->description}}</td>
                                           <td>{{@$item->source_item_bal_stock}}</td>
                                           <td>{{@$item->destination_item->stock_id_code}}</td>
                                           <td>{{@$item->destination_item->description}}</td>
                                           <td>{{@$item->source_qty}}</td>
                                           <td>{{@$item->conversion_factor}}</td>
                                           <td>{{@$item->destination_qty}}</td>
                                       </tr>
                                    @endforeach
                                    </tbody>
                                    
                                </table>
                                </span>
                            </div>
                       


                        </div>
                    </div>


    </section>
@endsection