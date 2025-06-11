@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">  Teams </h3>

                    @if(can('add', $permissions_module))
                        <a href="{{ route("$base_route.create") }}" class="btn btn-primary"> Add Team </a>
                    @endif
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Team Name</th>
                            <th>Branch </th>
                            <th>Team Leader</th>
                            <th>Members</th>
                            <th>Routes</th>
                            <th>Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($data as $index => $team)
                            <tr>
                                <th style="width: 3%;">{{ $index + 1 }}</th>
                                <td>{{ $team['team_name'] }}</td>
                                <td>{{ $team['branch_name'] }}</td>
                                <td>{{ $team['team_leader'] }}</td>
                                <td>
                                    @foreach ($team['members'] as $member)
                                    {{$member}}
                                    @if (!$loop->last)
                                    ,
                                        
                                    @endif
                                        
                                    @endforeach

                                </td>
                                <td>
                                    @foreach ($team['routes'] as $route )
                                    {{$route}}
                                    @if (!$loop->last)
                                    ,
                                        
                                    @endif
                                        
                                    @endforeach

                                </td>
                                <td>
                                    <div class="action-button-div">
                                        @if(can('edit', $permissions_module))
                                            <a href="{{ route("$base_route.edit", $team['id']) }}"> <i class="fas fa-edit"></i> </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
