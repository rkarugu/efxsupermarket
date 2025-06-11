@extends('layouts.admin.admin')

@push('styles')
    <style>
        .col-xs-2 {
            width: 14%
        }
    </style>
@endpush

@section('content')
    <section class="content" id="app">
        <div class="box box-primary" v-cloak>
            <div class="box-header with-border">
                <h3 class="box-title"> Create Employee </h3>
                
            </div>
            
            <div class="box-body" style="min-height: 75vh; display: flex; flex-direction: column;">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div style="flex-grow: 1">
                    <div class="stepwizard" style="margin-bottom: 20px; padding-bottom: 10px;">
                        <div class="stepwizard-row setup-panel">
                            <div class="stepwizard-step col-xs-2" v-for="(tab, index) in tabs">
                                <a href="#"
                                    class="btn btn-circle step-buttons"
                                    :class="{'btn-success': isCurrentTab(index), 'btn-default': !isCurrentTab(index)}"
                                    @click.prevent=""
                                    style="cursor: default"
                                >
                                    @{{ index + 1 }}
                                </a>
                                <p><b>@{{ tab }}</b></p>
                            </div>
                        </div>
                    </div>
                    <div class="setup-content" :class="{'d-none': !isCurrentTab(0)}">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="first-name">First Name <span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="first-name" placeholder="Enter first name" v-model="form.first_name">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="middle-name">Middle Name</label>
                                <input type="text" class="form-control" id="middle-name" placeholder="Enter middle name" v-model="form.middle_name">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="last-name">Last Name <span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="last-name" placeholder="Enter last name" v-model="form.last_name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="dob">Date of Birth <span style="color: red">*</span></label>
                                <input type="date" class="form-control" id="dob" v-model="form.date_of_birth">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="id-number">ID No. <span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="id-number" placeholder="Enter ID number" v-model="form.id_no">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="passport-number">Passport No.</label>
                                <input type="text" class="form-control" id="passport-number" placeholder="Enter passport no." v-model="form.passport_no">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="gender">Gender <span style="color: red">*</span></label>
                                <select class="form-control" id="gender" v-model="form.gender_id" data-key="gender_id" :onchange="setFormValue">
                                    <option :value="gender.id" v-for="gender in genders">@{{ gender.name }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="salutation">Salutation <span style="color: red">*</span></label>
                                <select class="form-control" id="salutation" v-model="form.salutation_id"  data-key="salutation_id" :onchange="setFormValue">
                                    <option :value="salutation.id" v-for="salutation in salutations">@{{ salutation.name }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="marital-status">Marital Status <span style="color: red">*</span></label>
                                <select class="form-control" id="marital-status" v-model="form.marital_status_id" data-key="marital_status_id" :onchange="setFormValue">
                                    <option :value="maritalStatus.id" v-for="maritalStatus in maritalStatuses">@{{ maritalStatus.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="nationality">Nationality <span style="color: red">*</span></label>
                                <select class="form-control" id="nationality" v-model="form.nationality_id" data-key="nationality_id" :onchange="setFormValue">
                                    <option :value="nationality.id" v-for="nationality in nationalities">@{{ nationality.name }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="education-level">Education Level <span style="color: red">*</span></label>
                                <select class="form-control" id="education-level" v-model="form.education_level_id" data-key="education_level_id" :onchange="setFormValue">
                                    <option :value="educationLevel.id" v-for="educationLevel in educationLevels">@{{ educationLevel.name }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="image">
                                    Image
                                    <span v-if="draft && employee?.image">
                                        <a :href="`/storage/${employee.image}`" target="_blank">(Current Image)</a>
                                    </span>
                                </label>
                                <input type="file" id="image" accept="image/*" :onchange="fileChanged">
                                <span style="font-size: 12px; font-style: italic">@{{ filename }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="setup-content" :class="{'d-none': !isCurrentTab(1)}">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="email">Email <span style="color: red">*</span></label>
                                <input type="email" class="form-control" id="email" placeholder="Enter email" v-model="form.email">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="work-email">Work Email</label>
                                <input type="email" class="form-control" id="work-email" placeholder="Enter work email" v-model="form.work_email">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="phone-number">Phone No. <span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="phone-number" placeholder="Enter phone no." v-model="form.phone_no">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="home-phone">Alternative Phone No. <span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="home-phone" placeholder="Enter alternative phone no." v-model="form.alternative_phone_no">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="residential-address">Residential Address <span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="residential-address" placeholder="Enter residential address" v-model="form.residential_address">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="postal-address">Postal Address</label>
                                <input type="text" class="form-control" id="postal-address" placeholder="Enter postal address" v-model="form.postal_address">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="postal-code">Postal Code</label>
                                <input type="text" class="form-control" id="postal-code" placeholder="Enter postal code" v-model="form.postal_code">
                            </div>
                        </div>
                    </div>
                    <div class="setup-content" :class="{'d-none': !isCurrentTab(2)}">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="branch">Branch <span style="color: red">*</span></label>
                                <select class="form-control" id="branch" v-model="form.branch_id"  data-key="branch_id" :onchange="branchChanged">
                                    <option :value="branch.id" v-for="branch in branches">@{{ branch.name }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="department">Department <span style="color: red">*</span></label>
                                <select class="form-control" id="department" v-model="form.department_id" data-key="department_id" :onchange="setFormValue">
                                    <option :value="department.id" v-for="department in branchDepartments" :key="department.id">@{{ department.department_name }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="employment-type">Employment Type <span style="color: red">*</span></label>
                                <select class="form-control" id="employment-type" v-model="form.employment_type_id" data-key="employment_type_id" :onchange="setFormValue">
                                    <option :value="employmentType.id" v-for="employmentType in employmentTypes">@{{ employmentType.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="job-title">Job Title <span style="color: red">*</span></label>
                                <select class="form-control" id="job-title" v-model="form.job_title_id" data-key="job_title_id" :onchange="setFormValue">
                                    <option :value="jobTitle.id" v-for="jobTitle in jobTitles">@{{ jobTitle.name }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="job-grade">Job Grade <span style="color: red">*</span></label>
                                <select class="form-control" id="job-grade" v-model="form.job_grade_id" data-key="job_grade_id" :onchange="setFormValue">
                                    <option :value="jobGrade.id" v-for="jobGrade in jobLevelGrades" :key="jobGrade.id">@{{ jobGrade.name }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="employment-date">Employment Date <span style="color: red">*</span></label>
                                <input type="date" class="form-control" id="employment-date" placeholder="Enter employment date" v-model="form.employment_date">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="contract-end-date">Contract End Date <span style="color: red">*</span></label>
                                <input type="date" class="form-control" id="contract-end-date" placeholder="Enter contract end date" v-model="form.contract_end_date">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="line-manager">Line Manager <span style="color: red" v-if="lineManagers.length">*</span></label>
                                <select class="form-control" id="line-manager" v-model="form.line_manager_id" data-key="line_manager_id" :onchange="setFormValue">
                                    <option :value="lineManager.id" v-for="lineManager in lineManagers">@{{ lineManager.full_name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" v-model="form.is_line_manager">
                                        Is Line Manager
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="setup-content" :class="{'d-none': !isCurrentTab(3)}">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="pin-no">Pin No. <span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="pin-no" placeholder="Enter pin no" v-model="form.pin_no">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="nssf-no">NSSF No. <span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="nssf-no" placeholder="Enter NSSF no." v-model="form.nssf_no">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="nhif-no">NHIF No. <span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="nhif-no" placeholder="Enter NHIF no." v-model="form.nhif_no">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="helb-no">HELB No.</label>
                                <input type="text" class="form-control" id="helb-no" placeholder="Enter HELB no." v-model="form.helb_no">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="basic-pay">Basic Pay <span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="basic-pay" placeholder="Enter basic pay" v-model="form.basic_pay" @keyUp="formatAmount($event)">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="payment-mode">Payment Mode <span style="color: red">*</span></label>
                                <select class="form-control" id="payment-mode" v-model="form.payment_mode_id" data-key="payment_mode_id" :onchange="setFormValue">
                                    <option :value="paymentMode.id" v-for="paymentMode in paymentModes">@{{ paymentMode.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" v-show="selectedPaymentMode == 'bank'">
                            <div class="form-group col-md-4">
                                <label for="bank">Bank <span style="color: red">*</span></label>
                                <select class="form-control" id="bank" v-model="form.bank_id" data-key="bank_id" :onchange="setFormValue">
                                    <option :value="bank.id" v-for="bank in banks">@{{ bank.name }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="bank-branch">Bank Branch <span style="color: red">*</span></label>
                                <select class="form-control" id="bank-branch" v-model="form.bank_branch_id" data-key="bank_branch_id" :onchange="setFormValue">
                                    <option :value="bankBranch.id" v-for="bankBranch in bankBankBranches" :key="bankBranch.id">@{{ bankBranch.name }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="account-name">Account Name <span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="account-name" placeholder="Enter account name" v-model="form.account_name">
                            </div>
                        </div>
                        <div class="row" v-show="selectedPaymentMode == 'bank'">
                            <div class="form-group col-md-4">
                                <label for="account-number">Account No. <span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="account-number" placeholder="Enter account number" v-model="form.account_no">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" v-model="form.inclusive_of_house_allowance">
                                        Basic pay inclusive of house allowance
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="setup-content" :class="{'d-none': !isCurrentTab(4)}">
                        <hr style="margin-top: 0">

                        <div style="margin-bottom: 10px; text-align: right">
                            <button class="btn btn-sm btn-primary" @click="addEmergencyContact">Add Emergency Contact</button>
                        </div>

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Relationship</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone No.</th>
                                    <th>Place of Work</th>
                                    <th>ID No.</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(emergencyContact, index) in form.emergency_contacts" :key="index">
                                    <td>@{{ index + 1 }}</td>
                                    <td>
                                        <select :id="`relationship-select-${index}`" v-model="form.emergency_contacts[index].relationship_id" :data-index="index" :onchange="emergencyContactRelationshipChanged">
                                            <option :value="relationship.id" v-for="relationship in relationships" :key="relationship.id">@{{ relationship.name }}</option>
                                        </select>
                                        <input type="text" class="form-control" placeholder="Enter relationship" v-model="form.emergency_contacts[index].custom_relationship" v-if="selectedRelationship(form.emergency_contacts[index].relationship_id) == 'Other'">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Enter full name" v-model="form.emergency_contacts[index].full_name">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Enter email" v-model="form.emergency_contacts[index].email">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Enter phone no" v-model="form.emergency_contacts[index].phone_no">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Enter place of work" v-model="form.emergency_contacts[index].place_of_work">
                                    </td>
                                    <td style="width: 120px">
                                        <input type="text" class="form-control" placeholder="Enter id no" v-model="form.emergency_contacts[index].id_no">
                                    </td>
                                    <td style="text-align: center">
                                        <div class="action-button-div">
                                            <button type="button" class="btn btn-danger btn-sm" @click="removeEmergencyContact(index)">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>                                          
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="setup-content" :class="{'d-none': !isCurrentTab(5)}">
                        <hr style="margin-top: 0">
                        
                        <div style="margin-bottom: 10px;text-align: right">
                            <button class="btn btn-sm btn-primary" @click="addBeneficiary">Add Beneficiary</button>
                        </div>
                        
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Relationship</th>
                                    <th>Is Minor</th>
                                    <th>Full Name</th>
                                    <th>Guardian Name</th>
                                    <th>Email/Guardian Email</th>
                                    <th>Phone No./Guardian Phone No.</th>
                                    <th>Place of Work</th>
                                    <th>ID No.</th>
                                    <th>Percentage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(beneficiary, index) in form.beneficiaries" :key="beneficiary.id">
                                    <td>@{{ index + 1 }}</td>
                                    <td>
                                        <select :id="`beneficiary-relationship-select-${index}`" v-model="form.beneficiaries[index].relationship_id" :data-index="index" :onchange="beneficiaryRelationshipChanged">
                                            <option :value="relationship.id" v-for="relationship in relationships" :key="relationship.id">@{{ relationship.name }}</option>
                                        </select>
                                        <input type="text" class="form-control" placeholder="Enter relationship" v-model="form.beneficiaries[index].custom_relationship" v-if="selectedRelationship(form.beneficiaries[index].relationship_id) == 'Other'">
                                    </td>
                                    <td style="text-align: center">
                                        <input type="checkbox" v-model="form.beneficiaries[index].is_minor" :data-index="index" :onchange="isMinorChanged">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Enter full name" v-model="form.beneficiaries[index].full_name">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Enter guardian name" v-model="form.beneficiaries[index].guardian_name" v-if="form.beneficiaries[index].is_minor">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Enter email" v-model="form.beneficiaries[index].email">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Enter phone no" v-model="form.beneficiaries[index].phone_no">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Enter place of work" v-model="form.beneficiaries[index].place_of_work" v-if="!form.beneficiaries[index].is_minor">
                                    </td>
                                    <td style="width: 120px">
                                        <input type="text" class="form-control" placeholder="Enter id no" v-model="form.beneficiaries[index].id_no" v-if="!form.beneficiaries[index].is_minor">
                                    </td>
                                    <td style="width: 80px">
                                        <input type="number" class="form-control"  v-model="form.beneficiaries[index].percentage">
                                    </td>
                                    <td style="text-align: center">
                                        <div class="action-button-div">
                                            <button type="button" class="btn btn-danger btn-sm" @click="removeBeneficiary(index)">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>                                          
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                
                        <div class="row" v-if="form.beneficiaries.length">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <table class="table table-bordered" style="margin-bottom: 10px">
                                    <tr>
                                        <th>Percentage Allocated:</th>
                                        <td>@{{ totalPercentage }}%</td>
                                    </tr>
                                </table>
                                <p style="font-style: italic; text-align: right; color: red" v-if="!totalPercentageAllocated">Percentage allocated should equal 100%</p>
                            </div>
                        </div>
                    </div>
                    <div class="setup-content" :class="{'d-none': !isCurrentTab(6)}">
                        <hr style="margin-top: 0">
                        
                        <div class="row">
                            <div v-for="documentType in documentTypes" :key="documentType.id">
                                <div class="form-group col-md-4" v-if="documentType.required_during_onboarding">
                                    <label :for="`document-${documentType.id}`">
                                        @{{ documentType.name }} <span style="color: red">*</span>
                                        <span v-if="draft && inEmployeeDocuments(documentType.id)">
                                            <a :href="`/storage/${documentUrl(documentType.id)}`" target="_blank">(Current Image)</a>
                                        </span>
                                    </label>
                                    <input type="file" :id="`document-${documentType.id}`" :data-id="documentType.id" :onchange="documentChanged">
                                    <span style="font-size: 12px; font-style: italic">No file selected</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <div>
                        <button type="button" 
                            class="btn btn-primary pull-right" 
                            style="margin-left: 10px" 
                            :disabled="processing || currentTab == tabs.length - 1" 
                            @click="nextTab"
                        >
                            Next
                            <i class="fa fa-arrow-right"></i>
                        </button>
                        <button type="button" 
                            class="btn btn-primary pull-right" 
                            style="margin-left: 10px" 
                            :disabled="processing || currentTab == '0'" 
                            @click="previousTab"
                        >
                            <i class="fa fa-arrow-left"></i>
                            Previous
                        </button>
                    </div>

                    <div>
                        <button type="button" class="btn btn-primary" @click="saveAsDraft">
                            <i class="fa fa-bookmark"></i>
                            Save as Draft
                        </button>
                    </div>

                    <div>
                        <button type="button" 
                            class="btn btn-primary pull-right" 
                            style="margin-left: 10px" 
                            :disabled="processing || currentTab < tabs.length - 1"
                            @click="submitForm"
                        >
                            <i class="fa fa-floppy-disk"></i>
                           Save
                        </button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal" @click="clearForm">
                            <i class="fa fa-ban"></i>
                            Reset
                        </button>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('css/multistep-form.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>

    <style>
        .d-none {
            display: none
        }
    </style>
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
                "vue": "{{ config('app.env') == 'local' ?  asset('js/vue.esm-browser.min.js') : asset('js/vue.esm-browser.prod.min.js') }}"
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
        
    </script>

    <script type="module">
        import { createApp, onMounted, ref, computed, watch } from 'vue';

        createApp({
            setup() {
                const draft = {!! $draft !!}
                const employee = {!! $employee !!}
                
                const formUtil = new Form()

                const tabs = [
                    'Personal Details',
                    'Contact and Address Information',
                    'Employee Information',
                    'Payroll Information',
                    'Emergency Contacts',
                    'Dependents and Beneficiaries',
                    'Documents',
                ]
                
                const currentTab = ref(0)

                const isCurrentTab = (tab) => {
                    return currentTab.value == tab
                }

                const nextTab = () => {
                    if (currentTab.value < tabs.length - 1) {
                        let error = false
                        if (currentTab.value == 0) {
                            error = validatePersonalDetails()

                        } else if (currentTab.value == 1) {
                            error = validateContactDetails()
                            
                        } else if (currentTab.value == 2) {
                            error = validateEmploymentDetails()

                        }  else if (currentTab.value == 3) {
                            error = validatePayrollDetails()
                        } else if (currentTab.value == 4) {
                            error = validateEmergencyContacts()
                        } else if (currentTab.value == 5) {
                            error = validateBeneficiaries()
                        }

                        if (!error) {
                            currentTab.value++ 
                        }
                    }
                }

                const previousTab = () => {
                    if (currentTab.value > 0) {
                        currentTab.value--
                    }
                }

                const branchDepartments = computed(() => {
                    return departments.value.filter(department => department.restaurant_id == form.value.branch_id)
                })

                const jobLevelGrades = computed(() => {
                    let jobTitle = jobTitles.value.find(jobTitle => jobTitle.id == form.value.job_title_id)

                    return jobGrades.value.filter(jobGrade => jobGrade.job_level_id == jobTitle?.job_level_id)
                })

                const bankBankBranches = computed(() => {
                    return bankBranches.value.filter(bankBranch => bankBranch.bank_id == form.value.bank_id)
                })

                const documentTypes = ref([])

                const requiredDocuments = computed(() => {
                    return documentTypes.value.filter(documentType => documentType.required_during_onboarding == true)
                })
                
                const form = ref({
                    id: '',
                    first_name: '',
                    middle_name: '',
                    last_name: '',
                    date_of_birth: '',
                    id_no: '',
                    passport_no: '',
                    gender_id: '',
                    salutation_id: '',
                    marital_status_id: '',
                    nationality_id: '',
                    education_level_id: '',
                    email: '',
                    phone_no: '',
                    alternative_phone_no: '',
                    residential_address: '',
                    postal_address: '',
                    postal_code: '',
                    branch_id: '',
                    department_id: '',
                    employment_type_id: '',
                    job_title_id: '',
                    job_grade_id: '',
                    work_email: '',
                    employment_date: '',
                    contract_end_date: '',
                    employment_status_id: '',
                    line_manager_id: '',
                    pin_no: '',
                    nssf_no: '',
                    nhif_no: '',
                    helb_no: '',
                    basic_pay: '',
                    payment_mode_id: '',
                    bank_id: '',
                    bank_branch_id: '',
                    account_name: '',
                    account_no: '',
                    inclusive_of_house_allowance: true,

                    emergency_contacts: [],
                    deleted_emergency_contacts: [],

                    beneficiaries: [],
                    deleted_beneficiaries: []
                })

                const clearForm = () => {
                    form.value.id = ''
                    form.value.first_name = ''
                    form.value.middle_name = ''
                    form.value.last_name = ''
                    form.value.date_of_birth = ''
                    form.value.id_no = ''
                    form.value.passport_no = ''
                    form.value.gender_id = ''
                    form.value.salutation_id = ''
                    form.value.marital_status_id = ''
                    form.value.nationality_id = ''
                    form.value.education_level_id = ''
                    form.value.email = ''
                    form.value.phone_no = ''
                    form.value.alternative_phone_no = ''
                    form.value.residential_address = ''
                    form.value.postal_address = ''
                    form.value.postal_code = ''
                    form.value.branch_id = ''
                    form.value.department_id = ''
                    form.value.employment_type_id = ''
                    form.value.job_title_id = ''
                    form.value.job_grade_id = ''
                    form.value.work_email = ''
                    form.value.employment_date = ''
                    form.value.contract_end_date = ''
                    form.value.employment_status_id = ''
                    form.value.line_manager_id = ''
                    form.value.pin_no = ''
                    form.value.nssf_no = ''
                    form.value.nhif_no = ''
                    form.value.helb_no = ''
                    form.value.basic_pay = ''
                    form.value.payment_mode_id = ''
                    form.value.bank_id = ''
                    form.value.bank_branch_id = ''
                    form.value.account_name = ''
                    form.value.account_no = ''
                    form.value.inclusive_of_house_allowance = true,
                    form.value.is_line_manager = false,
                    form.value.is_draft = false,

                    form.value.emergency_contacts = [],
                    form.value.deleted_emergency_contacts = [],

                    form.value.beneficiaries = [],
                    form.value.deleted_beneficiaries = []

                    currentTab.value = '0'

                    $('#gender').val('').trigger('change')
                    $('#salutation').val('').trigger('change')
                    $('#marital-status').val('').trigger('change')
                    $('#nationality').val('').trigger('change')
                    $('#education-level').val('').trigger('change')
                    $('#branch').val('').trigger('change')
                    $('#department').val('').trigger('change')
                    $('#employment-type').val('').trigger('change')
                    $('#job-title').val('').trigger('change')
                    $('#job-grade').val('').trigger('change')
                    $('#employment-status').val('').trigger('change')
                    $('#line-manager').val('').trigger('change')
                    $('#bank').val('').trigger('change')
                    $('#bank-branch').val('').trigger('change')
                }

                const employeeDocuments = ref([])

                const findEmployeeDocument = (id) => {
                    return employeeDocuments.value.find(employeeDocument => employeeDocument.document_type_id == id)
                }
                
                const inEmployeeDocuments = (id) => {
                    return findEmployeeDocument(id) ? true : false
                }

                const documentUrl = (id) => {
                    return findEmployeeDocument(id)?.file_path
                }
                const loadDraft = () => {
                    form.value.id = employee.id ?? ''
                    form.value.first_name = employee.first_name ?? ''
                    form.value.middle_name = employee.middle_name ?? ''
                    form.value.last_name = employee.last_name ?? ''
                    form.value.date_of_birth = employee.date_of_birth ?? ''
                    form.value.id_no = employee.id_no ?? ''
                    form.value.passport_no = employee.passport_no ?? ''
                    form.value.gender_id = employee.gender_id ?? ''
                    form.value.salutation_id = employee.salutation_id ?? ''
                    form.value.marital_status_id = employee.marital_status_id ?? ''
                    form.value.nationality_id = employee.nationality_id ?? ''
                    form.value.education_level_id = employee.education_level_id ?? ''
                    form.value.email = employee.email ?? ''
                    form.value.phone_no = employee.phone_no ?? ''
                    form.value.alternative_phone_no = employee.alternative_phone_no ?? ''
                    form.value.residential_address = employee.residential_address ?? ''
                    form.value.postal_address = employee.postal_address ?? ''
                    form.value.postal_code = employee.postal_code ?? ''
                    form.value.branch_id = employee.branch_id ?? ''
                    form.value.department_id = employee.department_id ?? ''
                    form.value.employment_type_id = employee.employment_type_id ?? ''
                    form.value.job_title_id = employee.job_title_id ?? ''
                    form.value.job_grade_id = employee.job_grade_id ?? ''
                    form.value.work_email = employee.work_email ?? ''
                    form.value.employment_date = employee.employment_date ?? ''
                    form.value.contract_end_date = employee.current_contract?.end_date ?? ''
                    form.value.line_manager_id = employee.line_manager_id ?? ''
                    form.value.pin_no = employee.pin_no ?? ''
                    form.value.nssf_no = employee.nssf_no ?? ''
                    form.value.nhif_no = employee.nhif_no ?? ''
                    form.value.helb_no = employee.helb_no ?? ''
                    form.value.basic_pay = numberWithCommas(employee.basic_pay) ?? ''
                    form.value.payment_mode_id = employee.payment_mode_id ?? ''
                    form.value.bank_id = employee.primary_bank_account?.bank_id ?? ''
                    form.value.bank_branch_id = employee.primary_bank_account?.bank_branch_id ?? ''
                    form.value.account_name = employee.primary_bank_account?.account_name ?? ''
                    form.value.account_no = employee.primary_bank_account?.account_no ?? ''
                    form.value.inclusive_of_house_allowance = true,
                    form.value.is_line_manager = employee.is_line_manager,

                    form.value.emergency_contacts = employee.emergency_contacts,

                    form.value.beneficiaries = employee.beneficiaries

                    employeeDocuments.value = employee.documents
                }

                if (draft) {
                    loadDraft()
                }

                const initSelect2 = () => {
                    $('select').each(function() {
                        if (!$(this).data('select2')) {
                            $(this).select2({
                                placeholder: "Select..."
                            })
                        }
                    })
                }
                
                const addEmergencyContact = () => {
                    form.value.emergency_contacts.push({
                        id: '',
                        relationship_id: '',
                        custom_relationship: '',
                        full_name: '',
                        email: '',
                        phone_no: '',
                        place_of_work: '',
                        id_no: '',
                    })

                    setTimeout(() => {
                        initSelect2()
                    }, 100)
                }

                const removeEmergencyContact = (index) => {
                    if (form.value.emergency_contacts[index].id) {
                        form.value.deleted_emergency_contacts.push(form.value.emergency_contacts[index].id)
                    }
                    form.value.emergency_contacts.splice(index, 1)
                }

                const emergencyContactRelationshipChanged = (event) => {
                    let index = event.target.dataset.index
                    let relationship_id = $(event.target).val()
                    
                    form.value.emergency_contacts[index].relationship_id = relationship_id
                    form.value.emergency_contacts[index].custom_relationship = ''
                }

                const addBeneficiary = () => {
                    form.value.beneficiaries.push({
                        id: '',
                        relationship_id: '',
                        custom_relationship: '',
                        is_minor: false,
                        full_name: '',
                        email: '',
                        phone_no: '',
                        place_of_work: '',
                        id_no: '',
                        guardian_name: '',
                        percentage: 0,
                    })

                    setTimeout(() => {
                        initSelect2()
                    }, 100)
                }

                const removeBeneficiary = (index) => {
                    if (form.value.beneficiaries[index].id) {
                        form.value.deleted_beneficiaries.push(form.value.beneficiaries[index].id)
                    }
                    form.value.beneficiaries.splice(index, 1)
                }

                const beneficiaryRelationshipChanged = (event) => {
                    let index = event.target.dataset.index
                    let relationship_id = $(event.target).val()
                    
                    form.value.beneficiaries[index].relationship_id = relationship_id
                    form.value.beneficiaries[index].custom_relationship = ''
                }

                const isMinorChanged = (event) => {
                    let index = event.target.dataset.index
                    
                    form.value.beneficiaries[index].guardian_name = ''
                    form.value.beneficiaries[index].place_of_work = ''
                    form.value.beneficiaries[index].id_no = ''
                }
                
                const totalPercentage = computed(() => {
                    return form.value.beneficiaries.reduce((acc, beneficiary) => acc + parseFloat(beneficiary.percentage ?? 0), 0).toFixed(2)
                })

                const totalPercentageAllocated = computed(() => {
                    return totalPercentage.value == 100
                })

                const validateBasicPersonalDetails = () => {
                    if (!form.value.first_name) {
                        formUtil.errorMessage('Enter first name')
                        return true
                    }

                    if (!form.value.last_name) {
                        formUtil.errorMessage('Enter last name')
                        return true
                    }

                    if (!form.value.date_of_birth) {
                        formUtil.errorMessage('Enter date of birth')
                        return true
                    }

                    if (!form.value.id_no) {
                        formUtil.errorMessage('Enter ID no.')
                        return true
                    }

                    if (!form.value.gender_id) {
                        formUtil.errorMessage('Select gender')
                        return true
                    }
                }
                
                const validatePersonalDetails = () => {
                    let error = false
                    
                    error = validateBasicPersonalDetails()

                    if (error) {
                        return true
                    }

                    if (!form.value.salutation_id) {
                        formUtil.errorMessage('Select salutation')
                        return true
                    }

                    if (!form.value.marital_status_id) {
                        formUtil.errorMessage('Select marital status')
                        return true
                    }

                    if (!form.value.nationality_id) {
                        formUtil.errorMessage('Select nationality')
                        return true
                    }

                    if (!form.value.education_level_id) {
                        formUtil.errorMessage('Select education level')
                        return true
                    }
                }

                const validateContactDetails = () => {
                    if (!form.value.email) {
                        formUtil.errorMessage('Enter email')
                        return true
                    }

                    if (!form.value.phone_no) {
                        formUtil.errorMessage('Enter phone no.')
                        return true
                    }

                    if (!form.value.alternative_phone_no) {
                        formUtil.errorMessage('Enter alternative phone no.')
                        return true
                    }

                    if (!form.value.residential_address) {
                        formUtil.errorMessage('Enter residential address')
                        return true
                    }
                }

                const validateEmploymentDetails = () => {
                    if (!form.value.branch_id) {
                        formUtil.errorMessage('Select branch')
                        return true
                    }

                    if (!form.value.department_id) {
                        formUtil.errorMessage('Select department')
                        return true
                    }

                    if (!form.value.employment_type_id) {
                        formUtil.errorMessage('Select employment type')
                        return true
                    }

                    if (!form.value.job_title_id) {
                        formUtil.errorMessage('Select job title')
                        return true
                    }

                    if (!form.value.job_grade_id) {
                        formUtil.errorMessage('Select job grade')
                        return true
                    }

                    if (!form.value.job_title_id) {
                        formUtil.errorMessage('Select job title')
                        return true
                    }

                    if (!form.value.job_grade_id) {
                        formUtil.errorMessage('Select job grade')
                        return true
                    }

                    if (!form.value.employment_date) {
                        formUtil.errorMessage('Enter date employed')
                        return true
                    }

                    if (!form.value.contract_end_date) {
                        formUtil.errorMessage('Enter contract end date')
                        return true
                    }

                    if (!form.value.is_line_manager && lineManagers.value.length && !form.value.line_manager_id) {
                        formUtil.errorMessage('Select a line manager')
                        return true
                    }
                }

                const validatePayrollDetails = () => {
                    if (!form.value.pin_no) {
                        formUtil.errorMessage('Enter PIN no.')
                        return true
                    }
                    
                    if (!form.value.nssf_no) {
                        formUtil.errorMessage('Enter NSSF no.')
                        return true
                    }

                    if (!form.value.nhif_no) {
                        formUtil.errorMessage('Enter NHIF no.')
                        return true
                    }

                    if (!form.value.basic_pay) {
                        formUtil.errorMessage('Enter basic pay')
                        return true
                    }

                    if (!form.value.payment_mode_id) {
                        formUtil.errorMessage('Select a payment mode')
                        return true
                    }

                    if (selectedPaymentMode.value == 'bank') {
                        if (!form.value.bank_id) {
                            formUtil.errorMessage('Select bank')
                            return true
                        }
    
                        if (!form.value.bank_branch_id) {
                            formUtil.errorMessage('Select bank branch')
                            return true
                        }
                        
                        if (!form.value.account_name) {
                            formUtil.errorMessage('Enter account name')
                            return true
                        }
    
                        if (!form.value.account_no) {
                            formUtil.errorMessage('Enter account no.')
                            return true
                        }
                    }
                    
                }

                const validateEmergencyContacts = () => {
                    if (!form.value.emergency_contacts.length) {
                        formUtil.errorMessage('Add at least one emergency contact')
                        return true
                    }

                    let error = false
                    form.value.emergency_contacts.forEach((emergencyContact, index) => {
                        if (!emergencyContact.relationship_id) {
                            formUtil.errorMessage(`Select relationship for emergency contact #${index + 1}`)
                            error = true
                            return
                        }

                        if (!emergencyContact.full_name) {
                            formUtil.errorMessage(`Enter full name for emergency contact #${index + 1}`)
                            error = true
                            return
                        }

                        if (!emergencyContact.email) {
                            formUtil.errorMessage(`Enter email for emergency contact #${index + 1}`)
                            error = true
                            return
                        }

                        if (!emergencyContact.phone_no) {
                            formUtil.errorMessage(`Enter phone no. for emergency contact #${index + 1}`)
                            error = true
                            return
                        }

                        if (!emergencyContact.place_of_work) {
                            formUtil.errorMessage(`Enter place of work for emergency contact #${index + 1}`)
                            error = true
                            return
                        }

                        if (!emergencyContact.id_no) {
                            formUtil.errorMessage(`Enter id no. for emergency contact #${index + 1}`)
                            error = true
                            return
                        }
                    })

                    return error
                }

                const validateBeneficiaries = () => {
                    if (!form.value.beneficiaries.length) {
                        formUtil.errorMessage('Add at least one dependent/beneficiary')
                        return true
                    }
                    
                    let error = false
                    
                    form.value.beneficiaries.forEach((beneficiary, index) => {
                        if (!beneficiary.relationship_id) {
                            formUtil.errorMessage(`Select relationship for beneficiary #${index + 1}`)
                            error = true
                            return
                        }

                        if (!beneficiary.full_name) {
                            formUtil.errorMessage(`Enter full name for beneficiary #${index + 1}`)
                            error = true
                            return
                        }

                        if (!beneficiary.guardian_name && beneficiary.is_minor) {
                            formUtil.errorMessage(`Enter guardian name for beneficiary #${index + 1}`)
                            error = true
                            return
                        }

                        if (!beneficiary.email) {
                            formUtil.errorMessage(`Enter email for beneficiary #${index + 1}`)
                            error = true
                            return
                        }

                        if (!beneficiary.phone_no) {
                            formUtil.errorMessage(`Enter phone no. for beneficiary #${index + 1}`)
                            error = true
                            return
                        }

                        if (!beneficiary.is_minor) {
                            if (!beneficiary.place_of_work) {
                                formUtil.errorMessage(`Enter place of work for beneficiary #${index + 1}`)
                                error = true
                                return
                            }

                            if (!beneficiary.id_no) {
                                formUtil.errorMessage(`Enter ID no. for beneficiary #${index + 1}`)
                                error = true
                                return
                            }
                        }

                        if (!beneficiary.percentage) {
                            formUtil.errorMessage(`Enter percentage for beneficiary #${index + 1}`)
                            error = true
                            return
                        }
                        
                    })
                    
                    if (!totalPercentageAllocated.value) {
                        formUtil.errorMessage('Percentage allocated should equal 100%')
                        return true
                    }

                    return error
                }

                const validateDocuments = () => {
                    let error = false

                    requiredDocuments.value.forEach(requiredDocument => {
                        let documentElement = document.getElementById(`document-${requiredDocument.id}`)

                        if (!documentElement.files.length && !inEmployeeDocuments(requiredDocument.id)) {
                            formUtil.errorMessage(`Select a file for ${requiredDocument.name}`)
                            error = true
                            return
                        }
                    })

                    return error
                }

                watch(() => form.value.branch_id, () => {
                    form.value.department_id = ''
                    
                    $('#department').val('').trigger('change')
                })

                watch(() => form.value.job_title_id, () => {
                    form.value.job_grade_id = ''
                    
                    $('#job-grade').val('').trigger('change')
                })

                watch(() => form.value.payment_mode_id, () => {
                    form.value.bank_id = ''
                    form.value.bank_branch_id = ''
                    form.value.account_name = ''
                    form.value.account_no = ''

                    $('#bank').val('').trigger('change')
                    $('#bank-branch').val('').trigger('change')
                })

                const paymentModes = ref([])
                const selectedPaymentMode = computed(() => {
                    return paymentModes.value.find(paymentMode => paymentMode.id == form.value.payment_mode_id)?.slug ?? ''
                })

                const formatAmount = (event) => {
                    let value = event.target.value.replace(/,/g, '')
                    
                    form.value.basic_pay = numberWithCommas(value)
                }

                const setFormValue = (event) => {
                    let key = event.target.dataset.key

                    form.value[key] = $(event.target).val()
                }

                const branchChanged = (event) => {
                    setFormValue(event)

                    form.value.line_manager_id = ''
                    $('#line-manager').val('').trigger('change')

                    fetchLineManagers()
                }

                const filename = ref('No file selected')
                const fileChanged = (event) => {
                    if (event.target.files.length) {
                        filename.value = event.target.files[0].name
                    } else {
                        filename.value = 'No file selected'
                    }
                }

                const documentChanged = (event) => {
                    let fileInput = event.target

                    if (fileInput.files.length) {
                        fileInput.nextElementSibling.innerText = fileInput.files[0].name
                    } else {
                        fileInput.nextElementSibling.innerText = 'No file selected'
                    }
                }
                
                const processing = ref(false)

                const saveAsDraft = () => {
                    let error = false
                    
                    error = validateBasicPersonalDetails()

                    if (error) {
                        return true
                    }
                    
                   form.value.is_draft = true
                   
                   submitForm()
                }
                
                const submitForm = () => {

                    let error = false

                    if (!form.value.is_draft) {
                        error = validateDocuments()
                    }

                    if (error) {
                        return
                    }
                    
                    processing.value = true

                    const formData = new FormData()
                    
                    for (const key in form.value) {
                        if (['emergency_contacts', 'beneficiaries', 'deleted_emergency_contacts', 'deleted_beneficiaries'].includes(key)) {
                            for (let i = 0; i < form.value[key].length; i++) {
                                formData.append(`${key}[${i}]`, JSON.stringify(form.value[key][i]))
                            }

                        } else {
                            formData.append(key, form.value[key] ?? '');
    
                            if (key == 'basic_pay') {
                                formData.append(key, form.value[key].replace(/,/g, ''))
                            }
                        }
                        
                    }

                    let image = document.getElementById(`image`)

                    if (image.files[0]) {
                        formData.append('image', image.files[0])
                    }

                    requiredDocuments.value.forEach((requiredDocument, index) => {
                        let documentElement = document.getElementById(`document-${requiredDocument.id}`)

                        if ( documentElement.files.length) {
                            formData.append(`document-${requiredDocument.id}`, documentElement.files[0])
                        }
                    })

                    let uri = ''
                    if (form.value.id) {
                        uri = 'hr/management/employees-draft-edit'
                    } else {
                        uri = 'hr/management/employees-create'
                    }
                    
                    axios.post(uri, formData)
                        .then(response => {
                            formUtil.successMessage(response.data.message)

                            // clearForm()
                            
                            processing.value = false

                            setTimeout(() => {
                                window.location = "{!! route('hr.management.employees') !!}"
                            }, 1000)
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                            form.value.is_draft = false
                        })
                }

                const fetchData = (uri, referenceVariable) => {
                    axios.get(uri)
                        .then(response => referenceVariable.value = response.data.data ?? response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.message))
                }

                const lineManagers = ref([])
                const fetchLineManagers = () => {
                    fetchData(`hr/management/line-managers-by-branch/${form.value.branch_id}`, lineManagers)
                }

                const genders = ref([])
                const salutations = ref([])
                const maritalStatuses = ref([])
                const nationalities = ref([])
                const educationLevels = ref([])
                const employmentTypes = ref([])
                const employmentStatuses = ref([])
                const branches = ref([])
                const departments = ref([])
                const jobTitles = ref([])
                const jobGrades = ref([])
                const banks = ref([])
                const bankBranches = ref([])
                const relationships = ref([])
                onMounted(async () => {
                    $('body').addClass('sidebar-collapse');
                    
                    // Fetch genders list
                    fetchData(`hr/configurations/gender-list`, genders)

                    // Fetch salutations list
                    fetchData(`hr/configurations/salutations-list`, salutations)

                    // Fetch marital status list
                    fetchData(`hr/configurations/marital-statuses-list`, maritalStatuses)

                    // Fetch nationalities
                    fetchData(`hr/configurations/nationalities-list`, nationalities)

                    // Fetch education levels
                    fetchData(`hr/configurations/education-levels-list`, educationLevels)
                    
                    // Fetch employment types list
                    fetchData(`hr/configurations/employment-types-list`, employmentTypes)
                    
                    // Fetch employment statuses list
                    fetchData(`hr/configurations/employment-statuses-list`, employmentStatuses)
                    
                    // Fetch branches list
                    fetchData('branches', branches)
                    
                    // Fetch departments list
                    fetchData('departments', departments)

                    // Fetch job titles list
                    fetchData(`hr/configurations/job-titles-list`, jobTitles)

                    // Fetch job grades list
                    fetchData(`hr/configurations/job-grades-list`, jobGrades)

                    if (draft) {
                        fetchLineManagers()
                    }

                    // Fetch banks list
                    fetchData(`hr/configurations/bank-list`, banks)

                    // Fetch bank branches list
                    fetchData(`hr/configurations/bank-branch-list`, bankBranches)

                    // Fetch payment modes list
                    fetchData(`hr/configurations/payment-mode-list`, paymentModes)

                    // Fetch relationships list
                    fetchData('hr/configurations/relationships', relationships)

                    // Fetch document type list
                    fetchData('hr/configurations/document-types', documentTypes)

                    $('select').select2({
                        placeholder: 'Select...'
                    });
                })

                watch(() => employmentStatuses, () => {
                    form.value.employment_status_id = employmentStatuses.value.find(employmentStatus => employmentStatus.name == 'Active')?.id ?? ''
                }, {
                    deep: true
                })

                const selectedRelationship = (id) => {
                    return relationships.value.find(relationship => relationship.id == id)?.name ?? ''
                } 

                return {
                    processing,
                    form, 
                    submitForm,
                    currentTab,
                    isCurrentTab,
                    tabs,
                    nextTab,
                    previousTab,
                    genders,
                    salutations,
                    maritalStatuses,
                    nationalities,
                    educationLevels,
                    employmentTypes,
                    branches,
                    branchDepartments,
                    jobTitles,
                    jobLevelGrades,
                    formatAmount,
                    banks,
                    bankBankBranches,
                    setFormValue,
                    clearForm,
                    filename,
                    fileChanged,
                    paymentModes,
                    selectedPaymentMode,
                    relationships,
                    addEmergencyContact,
                    emergencyContactRelationshipChanged,
                    removeEmergencyContact,
                    addBeneficiary,
                    removeBeneficiary,
                    beneficiaryRelationshipChanged,
                    isMinorChanged,
                    totalPercentage,
                    totalPercentageAllocated,
                    documentTypes,
                    documentChanged,
                    lineManagers,
                    saveAsDraft,
                    draft,
                    employee,
                    inEmployeeDocuments,
                    documentUrl,
                    branchChanged,
                    selectedRelationship
                }
            }
        }).mount('#app')
    </script>
@endsection