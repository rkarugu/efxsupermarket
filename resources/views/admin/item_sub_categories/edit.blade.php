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
                            <input type="text" name="title" id="title" value="{{$pack->title}}" class="form-control" placeholder="Title">
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" name="description" value="{{$pack->description}}" id="description" class="form-control" placeholder="Description">
                        </div>

                        <div class="form-group">
                            <label for="image"> Image </label>
                            <input type="file" name="image" id="image" class="form-control">
                            <img id="preview-image" src="{{ $pack->image }}" alt="Sub category image" style="margin-top: 20px;" width="200" height="200"/>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </form>
                </div>
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
