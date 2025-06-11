@extends('layouts.admin.admin')

@section('content')
    <section class="content" id="app">
        <div class="box box-primary" v-cloak>
            <div class="box-header with-border">
                <h3 class="box-title"> Employees </h3>

                <button 
                    class="btn btn-info pull-right" 
                    style="margin-left: 10px"
                    @click="showUploadModal" 
                    v-if="(permissions.includes('hr-management-employees___bulk-upload') || user.role_id == '1')"
                >
                    <i class="fa fa-upload"></i>
                    Bulk Upload
                </button>

                <a 
                    href="{{ route('hr.management.employees-create') }}" 
                    class="btn btn-primary pull-right"
                    v-if="(permissions.includes('hr-management-employees___create') || user.role_id == '1')"
                >
                    <i class="fa fa-plus"></i>
                    Add Employee
                </a>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th>Employee No.</th>
                            <th>Name</th>
                            <th>Branch</th>
                            <th>Job Title</th>
                            <th>Employment Type</th>
                            <th>Employement Date</th>
                            <th style="width: 10%" v-if="canViewActions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(employee, index) in employees" :key="employee.id">
                            <td>@{{ ++index }}</td>
                            <td>@{{ employee.employee_no }}</td>
                            <td>@{{ employee.full_name }}</td>
                            <td>@{{ employee.branch.name }}</td>
                            <td>@{{ employee.job_title.name }}</td>
                            <td>@{{ employee.employment_type.name }}</td>
                            <td>@{{ employee.employment_date }}</td>
                            <td style="text-align: center" v-if="canViewActions">
                                <div class="action-button-div">                                         
                                    <a :href="`/admin/hr/management/employees/${employee.id}`" v-if="(permissions.includes('hr-management-employees___details') || user.role_id == '1')">
                                        <i class="fa fa-store" title='Details'></i>
                                    </a>                                           
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="modal fade" id="upload-modal" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title">Bulk Upload Employees</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="file" class="form-control" id="upload-file" accept=".xlsx" :onchange="documentChanged">
                                <span style="font-size: 12px; font-style: italic">No file selected</span>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('hr.management.bulk-upload-template') }}" class="btn btn-warning pull-left" download>Download Template</a>
                            <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled="processing" @click="submitBulkUpload">Upload</button>
                            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

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
                const user = {!! $user !!}
                const permissions = Object.keys(user.permissions)

                const formUtil = new Form()

                const canViewActions = computed(() => {
                    return permissions.includes('hr-management-employees___edit') || 
                        permissions.includes('hr-management-employees___details') ||
                        user.role_id == '1'
                })

                const showUploadModal = () => {
                    $('#upload-file').val(null)
                    
                    $('#upload-modal').modal('show')
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
                const submitBulkUpload = () => {
                    const file = document.getElementById('upload-file')

                    if (!file.files.length) {
                        formUtil.errorMessage('Select a file to upload')
                        return
                    }

                    processing.value = true

                    const formData = new FormData()

                    formData.append('uploaded_file', file.files[0])

                    axios.post('hr/management/employees-bulk-upload', formData, {
                        responseType: 'blob'
                    })
                        .then(response => {
                            fetchEmployees()

                            if (response.status == 201) {
                                
                                const url = window.URL.createObjectURL(new Blob([response.data]));
                                const link = document.createElement('a');
                                link.href = url;

                                link.setAttribute('download', 'employees_bulk_upload_errors.xlsx');
                                
                                document.body.appendChild(link);
                                link.click();

                                document.body.removeChild(link);
                                
                                formUtil.warningMessage("Some errors were encountered during upload.")
                            } else {
                                formUtil.successMessage('Employees uploaded successfully')
                            }
                            
                            $('#upload-modal').modal('hide')

                            file.value = ''
                            $(file).val('').trigger('change')

                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                    
                }

                const employees = ref([])
                const fetchEmployees = async () => {
                    try {
                        let response = await axios.get('hr/management/employees-list')

                        employees.value = response.data
                    } catch (error) {
                        formUtil.errorMessage(error.response.data.error)
                    }
                }

                onMounted(async () => {
                    
                    await fetchEmployees()

                    $('table').DataTable({
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'ordering': true,
                        'info': true,
                        'autoWidth': false,
                        'pageLength': 25,
                        'initComplete': function (settings, json) {
                            let info = this.api().page.info();
                            let total_record = info.recordsTotal;
                            if (total_record < 26) {
                                $('.dataTables_paginate').hide();
                            }
                        },
                        'aoColumnDefs': [{
                            'bSortable': false,
                            'aTargets': 'noneedtoshort'
                        }],
                    });

                })

                return {
                    user,
                    permissions,
                    employees,
                    canViewActions,
                    showUploadModal,
                    submitBulkUpload,
                    processing,
                    documentChanged
                }
            }
        }).mount('#app')
    </script>
@endsection
