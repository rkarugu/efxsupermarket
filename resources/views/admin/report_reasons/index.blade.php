@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div align="right"> <a href="{!! route($model . '.create') !!}" class="btn btn-success">Add
                        {!! $title !!}</a></div>
                <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Reason</th>
                                <th class="noneedtoshort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($reasons) && !empty($reasons))
                                <?php $b = 1; ?>
                                @foreach ($reasons as $reason)
                                    <tr>
                                        <td>{!! $b !!}</td>
                                        <td>{!! $reason->name ?? '' !!}</td>
                                        <td class="action_crud">
                                            <span>
                                                <a title="Edit" href="{{ route($model . '.edit', $reason->id) }}"><img
                                                        src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                </a>
                                            </span>
                                            <span>
                                                <form title="Trash" action="{{ route($model . '.destroy', $reason->id) }}"
                                                    method="POST" class="submitMe">
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
@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
@endsection
