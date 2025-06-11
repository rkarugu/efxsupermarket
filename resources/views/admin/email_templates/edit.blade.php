
@extends('layouts.admin.admin')

@section('content')
<style>
    .span-action {

    display: inline-block;
    margin: 0 3px;

}
</style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                           
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <h3>Edit Email Template: {{$email_template->name}}</h3>
                            </div>
                            <div class="col-md-12 no-padding-h table-responsive">
                                <form action="{{ route('admin.email_templates.update', $key) }}" method="POST" class="submitMe mt-4">
                                    @csrf
                                    @method('PUT')
                            
                                    <div class="form-group mb-3">
                                        <label for="subject" class="form-label">Subject:</label>
                                        <input type="text" name="subject" class="form-control" id="subject" value="{{ @$template->subject ?? $email_template->subject }}" required>
                                        <small class="">Required Variable Names: {{$email_template->subject_variables}}</small>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="subscribers" class="form-label">Subscriber Emails:</label>
                                        <input type="text" name="subscribers" class="form-control tags" id="subscribers" 
                                            value="{{ @$template->subscribers ?? '' }}" 
                                            placeholder="Enter emails separated by commas" required>
                                        <small class="form-text text-muted">Add multiple emails separated by commas (e.g., email1@example.com, email2@example.com)</small>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="body" class="form-label">Body:</label>
                                        <textarea name="body" class="form-control" id="body" rows="10" required>
                                            {!! @$template->body ?? $email_template->template !!}
                                        </textarea>
                                        <small class="">Required Variable Names: {{$email_template->body_variables}}</small>
                                    </div>
                            
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                    <a href="{{ route('admin.email_templates.index') }}" class="btn btn-warning">Cancel</a>
                                </form>
                            </div>
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
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="{{ asset('assets/admin/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#body').wysihtml5();
            $(".tags").tagsinput({
                allowDuplicates: false
            });

            $(".tags").on('beforeItemAdd', function(event) {
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                event.cancel = !emailRegex.test(event.item);
            });
        })
    </script>
@endpush
