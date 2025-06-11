@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Payment Providers </h3>
                    <a href="{{ route("$base_route.index") }}" class="btn btn-primary"> Back </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route("$base_route.store") }}" class="form-horizontal" novalidate method="post" enctype="multipart/form-data">
                    {{ @csrf_field() }}

                    <div class="form-group">
                        <label for="name" class="control-label col-md-2">Provider Name</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image" class="control-label col-md-2"> Image </label>
                        <div class="col-md-10">
                            <input type="file" name="image" id="image" class="form-control">
                            <img id="preview-image" src="#" alt="Provider image" style="display: none; margin-top: 20px;" width="200" height="200"/>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"> SUBMIT </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>

    <script>
        $('#image').change(function () {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#preview-image').attr('src', e.target.result);
                $('#preview-image').css('display', 'block');
            }

            reader.readAsDataURL(this.files[0]);
        });
    </script>
@endsection
