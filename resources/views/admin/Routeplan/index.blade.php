@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <a href="{{ route('create-route-plan', $route_id) }}" class="btn btn-primary btn-sm">Add Route Plan</a>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <th width="5%">S.No.</th>

                                <th width="10%">Distance</th>
                                <th width="10%">Time</th>
                                <th width="10%">Fuel</th>
                                <th width="10%">Actions</th>
                                <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($plans) && !empty($plans))
                                <?php $b = 1; ?>
                                @foreach ($plans as $plan)
                                    <tr>
                                        <td>{!! $b !!}</td>

                                        <td>{!! $plan->total_distance !!}</td>

                                        <td>{!! $plan->total_time !!}</td>

                                        <td>{!! $plan->total_fuel !!}</td>


                                        <td class="action_crud">


                                            <span>
                                                <a title="View" href="#"><i
                                                        class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                            </span>

                                            <span>
                                                <a title="Edit" href="{{route('edit-route-plan',$plan)}}"><i
                                                        class="fa fa-edit" aria-hidden="true"></i>
                                                </a>
                                            </span>

                                        </td>


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
