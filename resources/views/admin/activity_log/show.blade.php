@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                @include('message')
                <div class="row">
                    <div class="col-sm-9">
                        <div class="form-group">
                            <label for="balance">Activity Log</label>
                        </div>
                    </div>
                    <div class="col-sm-3 text-right">
                        <a href="{{ route('activitylogs.index') }}" class="btn btn-primary">Back</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="card">
                    <div class="card-header">
                        Activity Log #{{ $activity->id }}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <p><strong>Description:</strong> <br> {{ $activity->description }}</p>
                            </div>
                            <div class="col-sm-3">
                                <p><strong>Subject:</strong> <br> {{ $activity->subject ? $activity->subject->name : 'N/A' }}</p>
                            </div>
                            <div class="col-sm-3">
                                <p><strong>Causer:</strong> <br> {{ $activity->causer ? $activity->causer->name : 'N/A' }}</p>
                            </div>
                            <div class="col-sm-3">
                                <p><strong>IP Address:</strong> <br> {{ $activity->properties['ip'] ?? 'N/A' }}</p>
                            </div>
                            <div class="col-sm-3">
                                <p><strong>User Agent:</strong>  <br>{{ $activity->properties['user_agent'] ?? 'N/A' }}</p>
                            </div>
                            <div class="col-sm-3">
                                <p><strong>Created At:</strong> <br> {{ $activity->created_at }}</p>
                            </div>
                        </div>
                        <p><strong>Properties:</strong> </p>
                        @php
                            $data = json_decode($activity->properties, true);
                        @endphp
                        @if ($activity->description == 'updated')
                        <div class="row">
                            <div class="col-sm-6">
                                <h4>Old</h4>
                                <table class="table table-striped">
                                    @foreach ($data['old'] as $key => $value)
                                        <tr>
                                            <td><b>{{$key}} ::</b> {{$value}}</td>   
                                        </tr>    
                                    @endforeach
                                </table>  
                            </div>
                            <div class="col-sm-6">
                                <h4>New</h4>
                                <table class="table table-striped">
                                    @foreach ($data['attributes'] as $key => $value)
                                        <tr>
                                            <td><b>{{$key}} ::</b> {{$value}}</td>   
                                        </tr>    
                                    @endforeach
                                </table>  
                            </div>
                        </div>
                        @elseif ($activity->description == 'update Permission')
                        <div class="row">
                            <div class="col-sm-6">
                                <h4>Old</h4>
                                <table class="table table-striped">
                                    @foreach (json_decode($data['old']) as $key => $value)
                                        <tr>
                                            <td><b>Module Name ::</b> {{$value->module_name}}</td>   
                                            <td><b>Module Action ::</b> {{$value->module_action}}</td>   
                                        </tr>    
                                    @endforeach
                                </table>  
                            </div>
                            <div class="col-sm-6">
                                <h4>New</h4>
                                <table class="table table-striped">
                                    @foreach (json_decode($data['attributes']) as $key => $value)
                                        <tr>
                                            <td><b>Module Name ::</b> {{$value->module_name}}</td>   
                                            <td><b>Module Action ::</b> {{$value->module_action}}</td>    
                                        </tr>    
                                    @endforeach
                                </table>  
                            </div>
                        </div>
                        @else
                        <div class="">
                            <table class="table table-striped">
                                @foreach ($data['attributes'] as $key => $value)
                                    <tr>
                                        <td><b>{{$key}} ::</b> {{$value}}</td>   
                                    </tr>    
                                @endforeach
                            </table>  
                        </div>
                        @endif
                        
                    </div>
                </div>
                
            </div>
        </div>
    </section>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
                      

        })
    </script>
@endpush
