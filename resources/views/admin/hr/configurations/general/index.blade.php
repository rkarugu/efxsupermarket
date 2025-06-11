@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> HR and Payroll Configurations - General </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

            </div>
        </div>

        <div class="box box-primary">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#gender" data-toggle="tab">Gender</a></li>
                <li><a href="#salutation" data-toggle="tab">Salutation</a></li>
                <li><a href="#marital-status" data-toggle="tab">Marital Status</a></li>
                <li><a href="#education-level" data-toggle="tab">Education Level</a></li>
                <li><a href="#nationality" data-toggle="tab">Nationality</a></li>
                <li><a href="#relationship" data-toggle="tab">Relationship</a></li>
                <li><a href="#document-type" data-toggle="tab">Document Type</a></li>
                <li><a href="#employment-type" data-toggle="tab">Employment Type</a></li>
                <li><a href="#employment-status" data-toggle="tab">Employment Status</a></li>
                <li><a href="#job-group" data-toggle="tab">Job Group</a></li>
                <li><a href="#job-level" data-toggle="tab">Job Level</a></li>
                <li><a href="#job-grade" data-toggle="tab">Job Grade</a></li>
                <li><a href="#job-title" data-toggle="tab">Job Title</a></li>
                <li><a href="#discipline-category" data-toggle="tab">Discipline Category</a></li>
                <li><a href="#discipline-action" data-toggle="tab">Discipline Action</a></li>
                <li><a href="#termination-type" data-toggle="tab">Termination Type</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="gender">
                    @include('admin.hr.configurations.general.partials.gender')
                </div>
                <div class="tab-pane" id="salutation">
                    @include('admin.hr.configurations.general.partials.salutation')
                </div>
                <div class="tab-pane" id="marital-status">
                    @include('admin.hr.configurations.general.partials.marital-status')
                </div>
                <div class="tab-pane" id="education-level">
                    @include('admin.hr.configurations.general.partials.education-level')
                </div>
                <div class="tab-pane" id="nationality">
                    @include('admin.hr.configurations.general.partials.nationality')
                </div>
                <div class="tab-pane" id="relationship">
                    @include('admin.hr.configurations.general.partials.relationship')
                </div>
                <div class="tab-pane" id="document-type">
                    @include('admin.hr.configurations.general.partials.document-type')
                </div>
                <div class="tab-pane" id="employment-type">
                    @include('admin.hr.configurations.general.partials.employment-type')
                </div>
                <div class="tab-pane" id="employment-status">
                    @include('admin.hr.configurations.general.partials.employment-status')
                </div>
                <div class="tab-pane" id="job-group">
                    @include('admin.hr.configurations.general.partials.job-group')
                </div>
                <div class="tab-pane" id="job-level">
                    @include('admin.hr.configurations.general.partials.job-level')
                </div>
                <div class="tab-pane" id="job-grade">
                    @include('admin.hr.configurations.general.partials.job-grade')
                </div>
                <div class="tab-pane" id="job-title">
                    @include('admin.hr.configurations.general.partials.job-title')
                </div>
                <div class="tab-pane" id="discipline-category">
                    @include('admin.hr.configurations.general.partials.discipline-category')
                </div>
                <div class="tab-pane" id="discipline-action">
                    @include('admin.hr.configurations.general.partials.discipline-action')
                </div>
                <div class="tab-pane" id="termination-type">
                    @include('admin.hr.configurations.general.partials.termination-type')
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}"></script>

    <script src="{{ asset('js/lodash.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>

    <script type="importmap">
        {
            "imports": {
                "vue": "{{ config('app.env') == 'local' ? asset('js/vue.esm-browser.min.js') : asset('js/vue.esm-browser.prod.min.js') }}"
            }
        }
    </script>
@endsection
