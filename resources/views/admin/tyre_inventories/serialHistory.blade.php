
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
        <div style="float:right;"><a class="btn btn-danger" href="{{route('tyre.stockMovesSerials',$inventory_item_id)}}">Back</a></div>
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Serial No.</th>
                                        <th>Stock ID Code</th>
                                        <th>Vehicle</th>
                                        <th>User</th>
                                        <th>Tyre Position</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($history) && !empty($history))
                                        <?php $b = 1;?>
                                        @foreach($history as $list)                                         
                                            <tr>
                                                <td>{!! $b !!}</td>                                                
                                                <td>{!! $list->stock_move->stock_id_code !!}</td>
                                                <td>{!! $list->serial_no !!}</td>				
                                                <td>{!! @$list->vehicle->license_plate !!}</td>                                       
                                                <td>{!! @$list->user->name !!}</td> 
                                                <td>{!! @$list->tyre_position->title !!}</td>                                      
                                                <td>{!! ucwords(str_replace('_',' ',$list->status)) !!}</td>                                       
                                                <td>{!! $list->updated_at !!}</td>                                       
                                            </tr>
                                           <?php $b++; ?>
                                        @endforeach
                                    @endif


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
 
   
@endsection
