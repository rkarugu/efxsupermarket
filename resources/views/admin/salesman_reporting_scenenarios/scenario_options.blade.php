@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title">{{ $scenario->name }} Reporting Options </h3>

                    <a href="{{ route("salesman-reporting.all-reporting-scenarios") }}" role="button"
                       class="btn btn-primary">
                        << Back To Salesman Reporting Options </a>
                </div>
            </div>
            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <form method="POST" action="{{  route('salesman-reporting.create-reporting-scenarios-options') }}">
                    @csrf
                    <input type="hidden" name="scenario_id" value="{{  $scenario->id }}">
                    <div class="row manage_scenario_options" id="delivery_centers">
                        <div class="col-12 all_centers_array">

                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
                        <div class="form-group" style="margin-top:25px;float:right;">
                            <button class="btn btn-success btn_scenario_option btn-sm" type="button"
                            >+ Scenario Option
                            </button>

                            <button class="btn btn-success btn-sm" type="submit" id="saveCentersBtn"
                                    style="margin-right:30px;">Save
                                Options
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">

                        </div>
                    </div>

                </form>

                @if($scenario->salesReportingReasons->count() >=1)
                    <form action="{{ route('salesman-reporting.update-reporting-scenarios-options') }}"  method="post">

                        {{ csrf_field() }}
                         @foreach($scenario->salesReportingReasons as  $index=>$option)

                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-2 form-group">
                                        <label for="distance_estimate" class="control-label"> Field Label </label>
                                        <input type="text" class="form-control" name="field_label[]" id="field_label[]" value="{{ $option->form_label }}" required>
                                    </div>

                                    <div class="col-md-2 form-group">
                                        <label for="field_data_type" class="control-label"> Field Data Type</label>
                                        <select class="form-control field_data_type" name="field_data_type[]"
                                                id="field_data_type" required>
                                            <option value="">Click to select</option>
                                            <option value="picture" {{ $option->data_type === 'picture' ? 'selected' : '' }}>Picture</option>
                                            <option value="string" {{ $option->data_type === 'string' ? 'selected' : '' }}> Text </option>
                                            <option value="inventory_item" {{ $option->data_type === 'inventory_item' ? 'selected' : '' }}> Inventory Item </option>
                                            <option value="number" {{ $option->data_type === 'number' ? 'selected' : '' }}> Number/Integer </option>

                                        </select>

                                    </div>

                                    <div class="col-md-2 form-group">
                                        <label for="field_key_name" class="control-label">Field Key Name </label>
                                        <input type="text" class="form-control" name="field_key_name[]" required
                                               id="field_key_name" value="{{ $option->field_name }}">
                                    </div>
                                    <input type="hidden" class="form-control" name="field_key_id[]" required
                                           id="field_key_id" value="{{ $option->option_id }}">
                                    <div class="col-md-2 form-group">
                                        <a href="{{ route('salesman-reporting.delete-reporting-scenarios-options', $option->option_id) }}" class="btn btn-danger text-danger" style="margin-top:20px;" title="Delete Record"><i
                                                    class="fa fa-remove fa-lg"></i></a>
                                    </div>


                                </div>
                                <br>


                            </div>

                        @endforeach

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"> Update Reporting Options</button>
                        </div>
                    </form>
                @else
                    <center><h5>No linked options for this salesman reporting issue.</h5></center>
                @endif
            </div>

        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection
@section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    <script type="text/javascript">
        document.getElementById('saveCentersBtn').style.display = 'none';
        $(document).ready(function () {
            $(function () {
                $('.field_data_type').select2();
            });
            var max_fields = 5000;
            var addButton = $('.btn_scenario_option');
            var wrapper = $('.manage_scenario_options');
            var centersForm =
                ' <div class="col-lg-12 all_centers_array">' +
                '<div class="col-lg-2 col-md-2 col-xs-12 col-sm-6">' +
                '<div class="form-group">' +
                '<label for="name" class="control-label"> Field Name</label>' +
                '<input type="text" class="form-control" name="field_name[]" id="name"  placeholder="Field Name"  required>' +
                '</div>' +
                '</div>' +
                '<div class="col-lg-3 col-md-3 col-xs-12 col-sm-6">' +
                '<div class="form-group">' +
                '<label for="center_location_name" class="control-label"> Field Data Type </label>' +
                '<select name="field_data_type[]" class="form-control" required>' +
                '<option value="">click to select</option>' +
                '<option value="picture">Picture</option>' +
                '<option value="string"> Text </option>' +
                '<option value="number"> Number </option>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="col-lg-2 col-md-2 col-xs-12 col-sm-4">' +
                '<div class="form-group">' +
                '<label for="center-latitude" class="control-label"> Field Key Name </label>' +
                '<input type="text" class="form-control latitude" name="field_key_name[]"  id="center-latitude"   placeholder="field_name" required>' +
                '</div>' +
                '</div>' +
                '<div class="col-lg-1 col-md-1 col-xs-12 col-sm-4">' +
                '<div class="form-group add_centers" style="margin-top:25px;">' +
                '<button class="btn btn-danger remove_btn_scenario_option" type="button">-</button>' +
                '</div>' +
                '</div>' +
                '</div>';

            var x = 1;
            $(addButton).click(function () {
                if (x < max_fields) {
                    x++;
                    let newForm = $(centersForm).clone();
                    wrapper.append(newForm);


                    var saveCentersBtn = document.getElementById('saveCentersBtn');
                    if (document.querySelector('.all_centers_array').childElementCount >= 1) {
                        saveCentersBtn.style.display = 'none';
                    } else {
                        saveCentersBtn.style.display = 'inline-block';
                    }
                } else {
                    alert('Maximum number of ' + max_fields + 'is reached');
                }
            });
            $(wrapper).on('click', '.remove_btn_scenario_option', function (e) {
                e.preventDefault();
                $(this).closest('.all_centers_array').remove();
                if (document.querySelector('.all_centers_array').childElementCount >= 1) {
                    saveCentersBtn.style.display = 'inline-block';
                } else {
                    saveCentersBtn.style.display = 'none';
                }
            });
            if (document.querySelector('.all_centers_array').childElementCount == 1) {
                saveCentersBtn.style.display = 'inline-block';
            } else {
                saveCentersBtn.style.display = 'none';
            }
        });
    </script>

@endsection
