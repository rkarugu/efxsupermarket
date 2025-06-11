@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <a href="{{ route('delivery-center.create') }}" class="btn btn-primary btn-sm">Add Center</a>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <th width="5%">S.No.</th>

                                <th width="10%">Name</th>


                                <th width="15%" class="noneedtoshort">Action</th>

                                <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($centres) && !empty($centres))
                                <?php $b = 1; ?>
                                @foreach ($centres as $centre)
                                    <tr>
                                        <td>{!! $b !!}</td>

                                        <td>{!! $centre->name !!}</td>


                                        <td class="action_crud">


                                            <span>
                                                <a title="View" href="{{ route($model . '.edit', $centre->id) }}"><i
                                                        class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                            </span>

                                            <span>
                                                <form title="Trash"
                                                    action="{{ URL::route($model . '.destroy', $centre->id) }}"
                                                    method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button style="float:left"><i class="fa fa-trash"
                                                            aria-hidden="true"></i>
                                                    </button>
                                                </form>
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
