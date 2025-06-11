@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header">
                
                <div class="d-flex">
                    <h4 class="flex-grow-1">Group Representative View, <small>{{ $rep->user->name }}</small></h4>
                    <div class="text-right">
                        @if (can('route-group-rep', 'reassign'))
                            <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#reassignAllModal" style="margin-top:0px; margin-right:10px;">
                                <i class="fa fa-solid fa-rotate"></i> Reassign All Routes
                            </button>
                        @endif
                        @if (can('route-group-rep', 'add'))
                            <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#addRouteModal" style="margin-top:0px; margin-right:10px;">
                                <i class="fa fa-plus"></i> Add Route
                            </button>
                        @endif
                        <a href="{{ route('group-rep.index') }}" class="btn btn-primary" style="margin-top:0px;"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                    </div>
                </div>
                <hr>
            </div>
        
            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <table class="table table-striped" id="groupRepsDataTable">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Routes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($routes as $item)
                            <tr>
                                <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                                <td>
                                    {{ $item->route->route_name }} 
                                </td>
                                <td>
                                    @if (can('route-group-rep', 'reassign'))
                                        <a onclick="reassignModal({{ $item->id }},'{{ $item->route->route_name }}')" text="Reassign Route">
                                            <i class="fa fa-solid fa-rotate"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reassignAllModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Reassign All Routes </h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('ressign-all-routes') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <form id="fetchPaymentForm" action="" method="post">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="new_rep" class="control-label"> Group Rep </label>
                                    <select name="new_rep" id="new_rep" class="form-control select22" required>
                                        <option value="">Choose Group Rep</option>
                                        @foreach ($groupReps as $item)
                                            <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" value="{{$rep->user_id}}" id="repId" name="repId">
                            <button type="submit" class="btn btn-primary" data-id="0">Re-assign All</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addRouteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Add Route </h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('group-rep.add-route') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <form id="fetchPaymentForm" action="" method="post">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="route" class="control-label"> Route </label>
                                    <select name="route" id="route" class="form-control select2" required>
                                        <option value="">Choose Route</option>
                                        @foreach ($allRoutes as $item)
                                            <option value="{{ $item->id }}">{{ $item->route_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" value="{{$rep->user_id}}" id="repId" name="repId">
                            <button type="submit" class="btn btn-primary" data-id="0">Allocate</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="reassignModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title" id="reassignTitle"> Reassign Route </h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('ressign-route') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <form id="fetchPaymentForm" action="" method="post">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="new_rep" class="control-label"> Group Rep </label>
                                    <select name="new_rep" id="new_rep" class="form-control select3" required>
                                        <option value="">Choose Group Rep</option>
                                        @foreach ($groupReps as $item)
                                            <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" id="routeId" name="routeId">
                            <input type="hidden" value="{{$rep->user_id}}" name="repId">
                            <button type="submit" class="btn btn-primary" data-id="0">Re-assign</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2.select2-container.select2-container--default {
            width: 100% !important;
        }
    </style>
@endsection

@section('uniquepagescript')
<script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                dropdownParent: $('#addRouteModal')
            });
            $('.select22').select2({
                dropdownParent: $('#reassignAllModal')
            });
            $('.select3').select2({
                dropdownParent: $('#reassignModal')
            });
            
            $('#groupRepsDataTable').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });

            
        });
        function reassignModal(id, name) {
                $('#routeId').val(id);
                $("#reassignTitle").text('Reassign Route '+name)
                $('#reassignModal').modal('show');
            }
    </script>
@endsection