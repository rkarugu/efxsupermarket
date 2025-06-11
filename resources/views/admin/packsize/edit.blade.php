
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                               <form action="{!! route($model.'.update',$pack->id)!!}" method="post" class="submitMe">
                                    {{ method_field('PUT') }}
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input type="text" name="title" id="title" value="{{$pack->title}}" class="form-control" placeholder="Title" >
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" name="description" value="{{$pack->description}}" id="description" class="form-control" placeholder="Description" >
                                    </div>
                                    <div class="form-group">
                                        <label for="pack_size">Pack Size</label>
                                        <select name="pack_size" id="pack_size" class="form-control">
                                            <option value="" selected disabled>Select a pack size</option>
                                            <option value="FULL PACK" {{ $pack->pack_size === 'FULL PACK' ? 'selected' : '' }}>FULL PACK</option>
                                            <option value="SMALL PACK" {{ $pack->pack_size === 'SMALL PACK' ? 'selected' : '' }}>SMALL PACK</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox-inline">
                                            <input type="checkbox" name="canorder" id="canorder" class="" value="1" {{ $pack->can_order ? 'checked' : '' }}>
                                        <label for="can-order">Can Order</label>
                                        </div>
                                        <div class="checkbox-inline">
                                            <input type="checkbox" name="ctn" id="ctn" class="ctn-checkbox" value="1" {{ $pack->ctn ? 'checked' : '' }}>
                                            <label for="ctn">CTN</label>
                                        </div>
                                        <div class="checkbox-inline">
                                            <input type="checkbox" name="dzn" id="dzn" class="dzn-checkbox" value="1" {{ $pack->dzn ? 'checked' : '' }}>
                                            <label for="dzn">DZN</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                               </form>
                            </div>
                        </div>
                    </div>


    </section>
   
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
    <style>
        .checkbox-inline {
            display: inline-block;
            margin-right: 10px;
        }
    </style>
@endsection

@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script>
    $(document).ready(function () {
        $('.ctn-checkbox').click(function () {
            if ($(this).is(':checked')) {
                $('#dzn').prop('checked', false);
            }
        });

        $('.dzn-checkbox').click(function () {
            if ($(this).is(':checked')) {
                $('#ctn').prop('checked', false);
            }
        });
    });
</script>
@endsection
