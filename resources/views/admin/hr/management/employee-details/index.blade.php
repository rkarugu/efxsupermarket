@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="session-message-container">
            @include('message')
        </div>
        
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Employee Details </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-3" style="text-align: center">
                        @if ($employee->image)
                            <img 
                                src="{{ asset('storage/' . $employee->image) }}" 
                                class="img-thumbnail img-circle img-responsive" 
                                style="height:220px;width:220px;"
                            >
                        @else
                            <img 
                                src="{{ asset('images/user-default.png') }}" 
                                class="img-thumbnail img-circle img-responsive" 
                                style="height:220px;width:220px;"
                            >
                        @endif
                    </div>
                    <div class="col-sm-4">
                        <table class="table table-bordered">
                            <tr>
                                <th>Name</th>
                                <td>{{ $employee->full_name }}</td>
                            </tr>
                            <tr>
                                <th>Gender</th>
                                <td>{{ $employee->gender->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $employee->email }}</td>
                            </tr>
                            <tr>
                                <th>Phone No.</th>
                                <td>{{ $employee->phone_no }}</td>
                            </tr>
                            <tr>
                                <th>ID No.</th>
                                <td>{{ $employee->id_no }}</td>
                            </tr>
                            <tr>
                                <th>PIN No.</th>
                                <td>{{ $employee->pin_no }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <table class="table table-bordered">
                            <tr>
                                <th>Employee No.</th>
                                <td>{{ $employee->employee_no }}</td>
                            </tr>
                            <tr>
                                <th>Branch</th>
                                <td>{{ $employee->branch->name }}</td>
                            </tr>
                            <tr>
                                <th>Department</th>
                                <td>{{ $employee->department->department_name }}</td>
                            </tr>
                            <tr>
                                <th>Job Title</th>
                                <td>{{ $employee->jobTitle->name }}</td>
                            </tr>
                            <tr>
                                <th>Employment Type</th>
                                <td>{{ $employee->employmentType->name }}</td>
                            </tr>
                            <tr>
                                <th>Employment Date</th>
                                <td>{{ $employee->employment_date }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#bio-data" data-toggle="tab">Personal Details</a></li>
                    <li><a href="#work-info" data-toggle="tab">Employment Details</a></li>
                    <li><a href="#payroll-details" data-toggle="tab">Payroll Details</a></li>
                    <li><a href="#emergency-contacts" data-toggle="tab">Emergency Contacts</a></li>
                    <li><a href="#beneficiaries" data-toggle="tab">Dependents/Beneficiaries</a></li>
                    <li><a href="#educational-information" data-toggle="tab">Educational Information</a></li>
                    <li><a href="#professional-information" data-toggle="tab">Professional Information</a></li>
                    <li><a href="#documents" data-toggle="tab">Documents</a></li>
                </ul>
    
                <div class="tab-content">
                    <div class="tab-pane active" id="bio-data">
                        @include('admin.hr.management.employee-details.partials.bio-data')
                    </div>
                    <div class="tab-pane" id="work-info">
                        @include('admin.hr.management.employee-details.partials.work-info')
                    </div>
                    <div class="tab-pane" id="payroll-details">
                        @include('admin.hr.management.employee-details.partials.payroll-details')
                    </div>
                    <div class="tab-pane" id="emergency-contacts">
                        @include('admin.hr.management.employee-details.partials.emergency-contacts')
                    </div>
                    <div class="tab-pane" id="beneficiaries">
                        @include('admin.hr.management.employee-details.partials.beneficiaries')
                    </div>
                    <div class="tab-pane" id="educational-information">
                        @include('admin.hr.management.employee-details.partials.educational-information')
                    </div>
                    <div class="tab-pane" id="professional-information">
                        @include('admin.hr.management.employee-details.partials.professional-information')
                    </div>
                    <div class="tab-pane" id="documents">
                        @include('admin.hr.management.employee-details.partials.documents')
                    </div>
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
    <script src="{{ asset('js/axios.min.js') }}"></script>

    <script type="importmap">
        {
            "imports": {
                "vue": "{{ config('app.env') == 'local' ? asset('js/vue.esm-browser.min.js') : asset('js/vue.esm-browser.prod.min.js') }}"
            }
        }
    </script>


    <script>
        axios.defaults.baseURL = '/api'
        
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response && error.response.status === 401) {
                    window.location = '/'
                }
                return Promise.reject(error);
            }
        );

        $(document).ready(function() {
            $('body').addClass('sidebar-collapse');
        });
    </script>
@endsection
