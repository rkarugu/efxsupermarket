<div style="padding:10px" id="work-info-app">
    <div v-cloak>
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
            <div class="form-group col-md-4">
                <label for="contract-end-date">Contract End Date</label>
                <input type="date" class="form-control" id="contract-end-date" placeholder="Enter contract end date" v-model="form.contract_end_date">
            </div>
            <div class="form-group col-sm-4">
                <label for="employment-status">Employment Status <span style="color: red">*</span></label>
                <select id="employment-status" class="form-control" v-model="form.employment_status_id" data-key="employment_status_id" :onchange="setFormValue">
                    <option :value="employmentStatus.id" v-for="employmentStatus in employmentStatuses">@{{ employmentStatus.name }}</option>
                </select>
            </div>
            <div class="form-group col-md-4" v-if="selectedEmploymentStatus == 'Terminated'">
                <label for="terminated-date">Terminated Date <span style="color: red">*</span></label>
                <input type="date" class="form-control" id="terminated-date" placeholder="Enter terminated date" v-model="form.terminated_date">
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
        
        <hr>
    
        <div style="text-align: right">
            <button class="btn btn-primary" :disabled="processing" @click="submitForm">
                <i class="fa fa-floppy-disk"></i>
                Save
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <script type="module">
        import { createApp, onMounted, ref, watch, computed } from 'vue';

        createApp({
            setup() {
                const employee = {!! $employee !!}

                const formUtil = new Form()
                const baseUrl = 'hr'

                const form = ref({
                    id: employee.id,
                    branch_id: employee.branch_id ?? '',
                    department_id: employee.department_id ?? '',
                    employment_type_id: employee.employment_type_id ?? '',
                    job_title_id: employee.job_title_id ?? '',
                    job_grade_id: employee.job_grade_id ?? '',
                    employment_date: employee.employment_date ?? '',
                    terminated_date: employee.terminated_date ?? '',
                    contract_end_date: employee.current_contract?.end_date ?? '',
                    employment_status_id: employee.employment_status_id,
                    line_manager_id: employee.line_manager_id ?? '',
                    is_line_manager: employee.is_line_manager ?? '',
                    
                })

                const selectedEmploymentStatus = computed(() => {
                    return employmentStatuses.value.find(employmentStatus => employmentStatus.id == form.value.employment_status_id)?.name ?? ''
                })

                const branchDepartments = computed(() => {
                    return departments.value.filter(department => department.restaurant_id == form.value.branch_id)
                })

                const jobLevelGrades = computed(() => {
                    let jobTitle = jobTitles.value.find(jobTitle => jobTitle.id == form.value.job_title_id)

                    return jobGrades.value.filter(jobGrade => jobGrade.job_level_id == jobTitle?.job_level_id)
                })

                watch(() => form.value.branch_id, () => {
                    form.value.department_id = ''
                    
                    $('#department').val('').trigger('change')
                })

                watch(() => form.value.job_title_id, () => {
                    form.value.job_grade_id = ''
                    
                    $('#job-grade').val('').trigger('change')
                })

                watch(() => form.value.employment_status_id, () => {
                    form.value.terminated_date = ''
                })

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

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.branch_id) {
                        formUtil.errorMessage('Select branch')
                        return
                    }

                    if (!form.value.department_id) {
                        formUtil.errorMessage('Select department')
                        return
                    }

                    if (!form.value.employment_type_id) {
                        formUtil.errorMessage('Select employment type')
                        return
                    }

                    if (!form.value.job_title_id) {
                        formUtil.errorMessage('Select job title')
                        return
                    }

                    if (!form.value.job_grade_id) {
                        formUtil.errorMessage('Select job grade')
                        return
                    }

                    if (!form.value.job_title_id) {
                        formUtil.errorMessage('Select job title')
                        return
                    }

                    if (!form.value.job_grade_id) {
                        formUtil.errorMessage('Select job grade')
                        return
                    }

                    if (!form.value.employment_date) {
                        formUtil.errorMessage('Enter date employed')
                        return
                    }

                    if (!form.value.employment_status_id) {
                        formUtil.errorMessage('Select employment status')
                        return
                    }

                    if (!form.value.is_line_manager && lineManagers.value.length && !form.value.line_manager_id) {
                        formUtil.errorMessage('Select a line manager')
                        return true
                    }
                    
                    processing.value = true

                    axios.post(`${baseUrl}/management/employees-edit`, form.value)
                        .then(response => {
                            formUtil.successMessage(response.data.message)

                            processing.value = false

                            window.location.reload()
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
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

                const branches = ref([])
                const departments = ref([])
                const employmentTypes = ref([])
                const employmentStatuses = ref([])
                const jobTitles = ref([])
                const jobGrades = ref([])
                onMounted(() => {
                    fetchData('branches', branches)
                    
                    fetchData('departments', departments)

                    fetchData(`${baseUrl}/configurations/employment-types-list`, employmentTypes)

                    fetchData(`${baseUrl}/configurations/employment-statuses-list`, employmentStatuses)

                    fetchData(`${baseUrl}/configurations/job-titles-list`, jobTitles)
                    
                    fetchData(`${baseUrl}/configurations/job-grades-list`, jobGrades)

                    fetchLineManagers()

                    $('#work-info-app select').select2({
                        placeholder: 'Select...'
                    });
                })

                return {
                    processing,
                    form, 
                    submitForm,
                    setFormValue,
                    branches,
                    branchDepartments,
                    departments,
                    employmentTypes,
                    employmentStatuses,
                    jobTitles,
                    jobGrades,
                    jobLevelGrades,
                    lineManagers,
                    selectedEmploymentStatus,
                    branchChanged
                }
            }
        }).mount('#work-info-app')
    </script>
@endpush