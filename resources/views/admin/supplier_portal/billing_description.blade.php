@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-header-flex">
                    <div class="d-flex flex-column">
                        <h3 class="box-title"> {{$title}} </h3>
                       
                    </div>

                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="" method="post" class="submitMe">
                    @csrf
                    <div class="form-group">
                      <label for="">Description</label>
                      <textarea class="form-control" name="description" id="description" rows="20" ></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
                
            </div>
        </div>

    </section>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
    <style>
        .bootstrap-tagsinput {
            width: 100%;
        }

        .bootstrap-tagsinput .tag {
            font-size: 13px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('#description').wysihtml5();
            $("#description").data('wysihtml5').editor.setValue(renderdefaultMessage());

        })

        function renderdefaultMessage() {
            return `{!! $description ? $description->description : "" !!}`;
            }
    </script>
@endpush

@section('uniquepagescript')
<div id="loader-on" style="
        position: absolute;
        top: 0;
        text-align: center;
        display: block;
        z-index: 999999;
        width: 100%;
        height: 100%;
        background: #000000b8;
        display:none;
    "
    class="loder">
    <div class="loader" id="loader-1"></div>
</div> 

<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{ asset('js/form.js') }}"></script>
@endsection
