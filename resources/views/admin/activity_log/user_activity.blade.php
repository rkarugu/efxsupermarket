@extends('layouts.admin.admin')

@section('content')
@php
    use Carbon\Carbon;
@endphp
<section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-6"> 
                        <h3 class="box-title">{{ $user->name }} Activity Log</h3>
                    </div>
                    <div class="col-sm-6 d-flex">
                        
                        <input type="date" id="date" class="form-control" placeholder="select Date" value="{{ Carbon::now()->format('Y-m-d') }}" style="margin:0 10px;">
                        <a id="userActivityBtn" href="{{route('activitylogs.index')}}" class="btn btn-primary"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                    </div>
                </div>
            </div>
        
            <div class="box-body">
                @include('message')
                
                @foreach($activities as $year => $months)
                    <div class="panel-group" id="accordionYear" role="tablist" aria-multiselectable="false">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="heading{{ $year }}">
                                <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordionYear" href="#collapse{{ $year }}" aria-expanded="false" aria-controls="{{ $year }}">
                                    Year: {{ $year }}
                                </a>
                                </h4>
                            </div>
                            <div id="collapse{{ $year }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{ $year }}">
                                <div class="panel-body">
                                    @foreach($months as $month => $days)
                                        <div class="panel-group" id="accordionMonth" role="tablist" aria-multiselectable="false">
                                            <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingMonth{{$month}}">
                                                <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordionMonth" href="#collapseMonth{{$month}}" aria-expanded="false" aria-controls="collapseMonth{{$month}}">
                                                    {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                                                </a>
                                                </h4>
                                            </div>
                                            <div id="collapseMonth{{$month}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingMonth{{$month}}">
                                                <div class="panel-body">
                                                        @foreach($days as $day => $activities)
                                                            <div class="panel-group" id="accordionDay" role="tablist" aria-multiselectable="false">
                                                                <div class="panel panel-default">
                                                                <div class="panel-heading" role="tab" id="headingDay{{$day}}">
                                                                    <h4 class="panel-title">
                                                                    <a role="button" data-toggle="collapse" data-parent="#accordionDay" href="#collapseDay{{$day}}" aria-expanded="false" aria-controls="collapseDay{{$day}}">
                                                                        {{ date('l jS', strtotime($day.'-'.$month.'-'.$year))  }} 
                                                                    </a>
                                                                    </h4>
                                                                </div>
                                                                <div id="collapseDay{{$day}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingDay{{$day}}">
                                                                    <div class="panel-body">
                                                                        <div class="row">

                                                                        </div>
                                                                        @foreach($activities as $activity)
                                                                        <div class="col-md-4">
                                                                            <div class="card">
                                                                                <h4 class="">{{$activity->created_at->format('H:i')}}  
                                                                                    @if ($activity->description != 'USER Logout')
                                                                                        <a href="{{ route('activitylogs.show',$activity->id) }}" class="text-primary" target="_blank" title="View"><i class="fa fa-eye"></i></a>
                                                                                    @endif
                                                                                
                                                                                </h4>
                                                                                <p class="event"> {{$activity->description}} </p>
                                                                                @if ($activity->subject_type)<p> {{$activity->subject_type}} </p>@endif
                                                                                @if ($activity->subject_id)<p> {{$activity->subject_id}} </p>@endif
                                                                                
                                                                                @php
                                                                                    $subjectTitle = '';
                                                                                     if($activity->subject){
                                                                                        if($activity->subject->title){
                                                                                            $subjectTitle = $activity->subject->title;
                                                                                        } elseif($activity->subject->name){
                                                                                            $subjectTitle = $activity->subject->name;
                                                                                        }
                                                                                        elseif($activity->subject->customer_name){
                                                                                            $subjectTitle = $activity->subject->customer_name;
                                                                                        }
                                                                                        // elseif($activity->subject->stock_id_code){
                                                                                        //     $subjectTitle = $activity->subject->stock_id_code;
                                                                                        // } 
                                                                                        elseif($activity->subject->document_no){
                                                                                            $subjectTitle = $activity->subject->document_no;
                                                                                        }  
                                                                                        elseif($activity->subject->transaction_no){
                                                                                            $subjectTitle = $activity->subject->transaction_no;
                                                                                        }   
                                                                                        elseif($activity->subject->grn_number){
                                                                                            $subjectTitle = $activity->subject->grn_number;
                                                                                        } else{
                                                                                            $subjectTitle = '';
                                                                                        }
                                                                                    }
                                                                                @endphp   
                                                                                @if ($subjectTitle != '')
                                                                                    <p> {{$subjectTitle}} </p>
                                                                                @endif      
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                @endforeach
            </div>
        </div>
    </section>
@endsection
@push('styles')
<style>
    .card{
        border: 1px solid #ddd;
        padding: 5px 10px;
        margin-bottom: 20px;
        height: 100%;
        border-radius: 5px;
        background-color: #ebebeb;
        position: relative;
    }
    .card h4{

    }
    .card h4 a{
        font-size: 15px;
        position: absolute;
        top: 20px;
        right: 15px;
    }
    .card p{
        margin: 0px;
        font-size: 13px;
    }
    .card p.event{
        font-weight: 600;
        text-transform: uppercase;
    }
</style>
@endpush
@push('scripts')
    <script>
        
    </script>
@endpush
