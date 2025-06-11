
@extends('layouts.admin.admin') 

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">

                                                       <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                            <i class="fa fa-filter"></i> Filter
                            </div><br>
                            {!! Form::open(['route' => 'feedback.restro','method'=>'get']) !!}

                            <div>
                            <div class="col-md-12 no-padding-h">
                            <div class="col-sm-3">
                            <div class="form-group">
                            {!! Form::text('start-date', null, [
                            'class'=>'datepicker form-control',
                            'placeholder'=>'Start Date' ,'readonly'=>true]) !!}
                            </div>
                            </div>

                            <div class="col-sm-3">
                            <div class="form-group">
                            {!! Form::text('end-date', null, [
                            'class'=>'datepicker form-control',
                            'placeholder'=>'End Date','readonly'=>true]) !!}
                            </div>
                            </div>

                             <div class="col-sm-3">
                            <div class="form-group">
                            {!!Form::select('restaurant', $restro, null, ['placeholder'=>'Branch', 'class' => 'form-control'  ])!!}
                            </div>
                            </div>

                           
                            </div>

                            <div class="col-md-12 no-padding-h">
                                <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>

                               

                               
                                <div class="col-sm-1">
                                <a class="btn btn-info" href="{!! route('feedback.restro') !!}"  >Clear</a>
                                </div>
                            </div>
                            </div>

                            </form>
                        </div>
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                       <th width="5%">S.No.</th>
                                       
                                        <th width="12%">Name</th>
                                        <th width="15%">Phone</th>
                                        <th width="15%">Restaurant</th>
                                        <th width="10%">Ratings</th>
                                         <th width="35%">Message</th>
                                       
                                        <th width="13%">Created</th>
                                       
                                         
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                               
                                                <td>{!! ucfirst($list->getAssociateUserForFeedback->name) !!}</td>
                                                 <td>{!! $list->getAssociateUserForFeedback->phone_number !!}</td>

                                                   <td>{!! ucfirst($list->getAssociateRestroForFeedback->name) !!}</td>

                                                   <td>
                                                    @if(count($list->getAssociateFeedbackRetingWithFeedback)>0)
                                                    {!! $list->getAssociateFeedbackRetingWithFeedback->sum('rating')/count($list->getAssociateFeedbackRetingWithFeedback)!!}

                                                    @else
                                                      0

                                                    @endif


                                                   </td>
                                                   <td>
                                                     {!! $list->feedback !!}
                                                   </td>


                                                    <td>{!! date('Y-m-d h:i A',strtotime($list->created_at)) !!}</td>
                                                 
                                       
                                                
                      
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


@section('uniquepagestyle')
 <link rel="stylesheet" href="{{asset('assets/admin/dist/bootstrap-datetimepicker.min.css')}}">
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datetimepicker.js')}}"></script>
<script>
               

                  $('.datepicker').datetimepicker({
                  format: 'yyyy-mm-dd hh:ii:00',
                  minuteStep:1,
                 });




            </script>

           


@endsection