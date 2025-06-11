@extends('layouts.admin.admin')

@section('content')
    <section class="content" id="bom-container">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Add BOM Item For {{ $inventoryItem->title }} </h3>

                    <a href="{{ route("$model.show-bom", $inventoryItem->id) }}" class="btn btn-outline-primary"> << Back to BOM Items </a>
                </div>
            </div>

            <div class="alert-message">
                @include('message')
            </div>

            <div class="box-body">
                <form class="validate form-horizontal" role="form" method="POST" action="{{ route($model.'.store-bom-item', $inventoryItem->id) }}">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="raw_material_id">Item</label>
                        <div class="col-sm-10">
                            <select name="raw_material_id" id="raw_material_id" class="form-control" required>
                                <option value="" selected disabled> Please Select</option>
                                @foreach($rawMaterials as $rawMaterial)
                                    <option value="{{ $rawMaterial->id }}"> {{ $rawMaterial->title }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="quantity">Quantity</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" name="quantity" id="quantity" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="notes">Notes</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="notes" id="notes"></textarea>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
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
        $("#raw_material_id").select2();
    </script>
@endsection
