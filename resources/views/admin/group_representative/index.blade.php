@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header">
                @include('message')
                <div class="d-flex">
                    <h4 class="flex-grow-1">Group Representatives</h4>
                    <div class="text-right">
                        
                    </div>
                </div>
            </div>
        
            <div class="box-body">
                <table class="table table-striped" id="groupRepsDataTable">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Group Rep.</th>
                            <th>Routes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedByUserAndName as $reps)
                            <tr>
                                <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                                <td> {{ $reps['user_name'] }} </td>
                                <td>
                                    @foreach ($reps['routes'] as $item)
                                        {{ $item->route->route_name }}, 
                                    @endforeach  </td>
                                <td> <a href="{{route('group-rep.view',$reps['user_id'])}}"><i class='fa fa-eye'></i></a>  </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
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
    </script>
@endpush