@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Price Update </h3>
            </div>

            <div class="box-body">
                @include('message')

                    <div class="row">
                        <form action="{{ route('price-update.download') }}" method="get">

                            <div class="form-group col-md-3">
                                <label for="category" class="control-label"> Select Category To Download</label>
                                <select name="category" id="category" class="form-control">
                                    <option value="" selected>Select Category </option>
                                    {{-- <option value="ALL">ALL</option> --}}
                                    @foreach ($categories as $key => $category)
                                        <option value="{{ $category->id }}"> {{ $category->category_description }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <button type="submit" class="btn btn-primary"  style=" margin-top:24px; " >Download</button>
                            
                            </div>
                    </form>
                    <form action="{{ route('price-update.upload') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group col-md-3">
                            <label for="upload_file" class="control-label"> Select File To Upload</label>

                           <input type="file" class="form-control" name="upload_file"  onchange="displaySelectedFile()" id="upload_file">
                           <div id="selected_file_display"></div>

                        </div>
                        <div class="form-group col-md-3">
                            <button type="submit" class="btn btn-primary" style=" margin-top:24px;">Upload</button>
                        
                        </div>
                </form>

                    </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection
@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#category').select2();
        
    });

    </script>
    <script>
        function displaySelectedFile() {
            var fileInput = document.getElementById('upload_file');
    
            var selectedFile = fileInput.files[0];
    
            var displayElement = document.getElementById('selected_file_display');
    
            if (selectedFile) {
                displayElement.innerText = 'Selected File: ' + selectedFile.name;
            } else {
                displayElement.innerText = 'No file selected';
            }
        }
        </script>

    
@endsection