@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Route Delivery Centers </h3>

                    @if(request()->query('route_id'))
                        <a href="{{ route("$base_route.create", ['route_id' => request()->query('route_id')]) }}" role="button" class="btn btn-primary">
                            Add Delivery Center
                        </a>
                    @else
                        <a href="{{ route("$base_route.create") }}" role="button" class="btn btn-primary"> Add Delivery Center </a>
                    @endif
                </div>
            </div>

            <div class="box-body">
                @include('message')

                <div class="data-table-filters">
                    <form action="{{ route("$base_route.index") }}" id="filter-centers-form">
                        {{ @csrf_field() }}

                        <div class="row">
                            <div class="col-md-2 form-group">
                                <input type="text" class="form-control" name="name_filter" placeholder="Search by center name">
                            </div>

                            <div class="col-md-2 form-group">
                                <select name="route_id" id="route_id" class="form-control">
                                    <option value="" selected disabled> Search by route</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route['id'] }}" @if(request()->query('route_id') == $route['id']) selected @endif>
                                            {{ $route['route_name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <button class="btn btn-outline-primary"> Search</button>
                                <button class="btn btn-default" style="margin-left: 12px;" onclick="resetFilterForm(event)"> Reset</button>
                            </div>
                        </div>
                    </form>
                </div>

                @if(count($centers) == 0)
                    <p> No delivery centers found. </p>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col"> #</th>
                                <th scope="col"> Center Name</th>
                                <th scope="col"> Route</th>
                                <th scope="col"> Date Created</th>
                                <th scope="col"> Shop Count</th>
                                <th scope="col"> Actions</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($centers as $index => $center)
                                <tr>
                                    <th scope="row"> {{ $index + 1 }}</th>
                                    <td> {{ $center['name'] }} </td>
                                    <td> {{ $center['route']['route_name'] }} </td>
                                    <td> {{ $center['date_created'] }} </td>
                                    <td> {{ $center['shop_count'] }} </td>
                                    <td>
                                        <div class="action-button-div">
                                            <a href="{{ route("$base_route.edit", $center['id']) }}" title="Edit Delivery Center">
                                                <i class="fa fa-pencil-square text-primary fa-lg" aria-hidden="true"></i>
                                            </a>

                                            <a href="javascript:void(0);" title="Remove Delivery Center" onclick="confirmCenterDeletion()">
                                                <i class="fa fa-trash text-danger fa-lg" aria-hidden="true"></i>
                                                <form action="{{ route("$base_route.destroy", $center['id']) }}" method="post" id="delete-center-form"
                                                      style="display: inline-block;">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    {{ @csrf_field() }}
                                                </form>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="paginator d-flex justify-content-between align-items-center">
                        <div>
                            Showing 1 to {{ count($centers) }} of {{ $centers->count() }} entries.
                        </div>

                        <div>
                            {{ $centers->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script type="text/javascript">
        function resetFilterForm(e) {
            e.preventDefault();

            $("#name_filter").val('');
            $("#route_id").val('');

            $("#filter-centers-form").submit();
        }

        function confirmCenterDeletion() {
            let userHasConfirmed = confirm(`Are you sure you want to remove this center?`);
            if (userHasConfirmed) {
                $("#delete-center-form").submit();
            }
        }
    </script>
@endsection