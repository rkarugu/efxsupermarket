
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                               <form action="{!! route($model.'.update',['id'=>$slot->branch_id ,'order_delivery_slot'=>$slot->id])!!}" method="post" class="submitMe">
                                    {{ method_field('PUT') }}
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <select name="day" id="day" class="form-control">
                                            <option value="" selected disabled>-- Select Slot Title --</option>
                                            @foreach ($days as $day)
                                                <option value="{{$day}}" {{$day == $slot->day ? 'selected' : ''}}>{{$day}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="slots">No of Delivery Points</label>
                                        <input type="number" min="0" max="50" name="no_of_delivery_points" value="{{$slot->no_of_delivery_points}}" class="form-control" placeholder="Allowed no of trucks" >
                                    </div>
                                    <div class="form-group">
                                        <label for="slots">No of Slots</label>
                                        <input type="number" min="0" max="24" name="slots" value="{{$slot->slots}}" class="form-control" placeholder="Slots" >
                                    </div>
                                    <div class="form-group">
                                        <label for="slots">Slot Start Time</label>
                                        <input type="time" min="0" max="24" name="start_time" value="{{date('H:i',strtotime($slot->start_time))}}" id="start_time" class="form-control" placeholder="delivery start time" >
                                    </div>
                                    <div class="form-group">
                                        <label for="slots">Slot Latest By</label>
                                        <input type="time" min="0" max="24" name="end_time" value="{{date('H:i',strtotime($slot->end_time))}}"  id="end_time" class="form-control" placeholder="delivery latest by time" >
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                               </form>
                            </div>
                        </div>
                    </div>


    </section>
   
@endsection
@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
@endsection
