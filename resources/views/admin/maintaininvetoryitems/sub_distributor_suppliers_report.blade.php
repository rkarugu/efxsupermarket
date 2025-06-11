@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">{!! $title !!}</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a>
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['method' => 'get']) !!}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <!--  <button type="submit" class="btn btn-primary">Filter</button> -->
                            <button type="submit" class="btn btn-primary" name="manage" value="excel">Excel</button>
                        </div>
                    </div>
                </div>
                </form>
                <hr>
                <div class="table-responsive">


                    <table class="table table-bordered" id="create_datatable_25">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Main Suppliers</th>
                                <th>Distributors Suppliers</th>

                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($suppliers as $mainsupplier => $subsuppliers)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $mainsupplier }}</td>
                                    <td>
                                        <ul>
                                            @foreach ($subsuppliers as $subsupplier)
                                                <li>{{ $subsupplier->subsupplier }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>

    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
@endsection

@section('uniquepagescript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js">
        < script src = "https://code.jquery.com/jquery-3.6.0.min.js" >
    </script>
    <script></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $('body').addClass('sidebar-collapse');
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
