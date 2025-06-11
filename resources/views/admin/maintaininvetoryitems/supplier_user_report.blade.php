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
                    <div class="col-md-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" name="manage" value="pdf">PDF</button>


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
                                <th>User</th>
                                <th>No of Suppliers</th>
                                <th>Suppliers</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suppliers as $userName => $userSuppliers)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $userName }}</td>

                                    <td>{{ $userSuppliers->count() }}</td>

                                    <td>

                                        <button onclick="toggleSuppliers({{ $loop->index }})" class="btn-primary btn-sm">
                                            <i id="icon{{ $loop->index }}" class="fas fa-eye  "></i>
                                        </button>
                                        <div id="suppliers{{ $loop->index }}" style="display: none;">
                                            <ul>
                                                @foreach ($userSuppliers as $supplier)
                                                    <li>{{ $supplier->suppname }}</li>
                                                @endforeach
                                            </ul>
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

        function approveShop() {
            let subjectShopId = $("#subject-shop").val();
            $(`#source-${subjectShopId}`).val('approval_requests');

            $(`#approve-shop-form-${subjectShopId}`).submit();
        }

        function approveAllShops() {
            $("#approve-all-shops-form").submit();
        }

        $('#view-issue-modal').on('show.bs.modal', function(event) {
            let triggeringButton = $(event.relatedTarget);
            let dataValue = triggeringButton.data('issue');

            let date = new Date();
            date.setTime(date.getTime() + (2 * 60 * 1000));
            let expires = "; expires=" + date.toGMTString();

            document.cookie = 'issue' + "=" + dataValue + expires + "; path=/";
        })

        function toggleSuppliers(index) {
            var icon = document.getElementById('icon' + index);
            var element = document.getElementById('suppliers' + index);
            if (element.style.display === 'none') {
                element.style.display = 'block';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                element.style.display = 'none';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
@endsection
