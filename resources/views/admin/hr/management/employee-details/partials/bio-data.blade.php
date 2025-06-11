<div style="padding:10px" id="bio-data-app">
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
        <div class="form-group col-md-4">
            <label for="home-phone">Alternative Phone No. <span style="color: red">*</span></label>
            <input type="text" class="form-control" id="home-phone" placeholder="Enter alternative phone no." v-model="form.alternative_phone_no">
        </div>
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
            <label for="residential-address">Residential Address <span style="color: red">*</span></label>
            <input type="text" class="form-control" id="residential-address" placeholder="Enter residential address" v-model="form.residential_address">
        </div>
        <div class="form-group col-md-4">
            <label for="postal-address">Postal Address</label>
            <input type="text" class="form-control" id="postal-address" placeholder="Enter postal address" v-model="form.postal_address">
        </div>
        <div class="form-group col-md-4">
            <label for="postal-code">Postal Code</label>
            <input type="text" class="form-control" id="postal-code" placeholder="Enter postal code" v-model="form.postal_code">
        </div>
        <div class="form-group col-md-4">
            <label for="image">Image</label>
            <input type="file" id="image" accept="image/*" :onchange="fileChanged">
            <span style="font-size: 12px; font-style: italic">@{{ filename }}</span>
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

@push('scripts')
    <script type="module">
        import { createApp, ref, onMounted } from 'vue';

        createApp({
            setup() {
                const employee = {!! $employee !!}
                const user = {!! $user !!}
                const permissions = Object.keys(user.permissions)

                const formUtil = new Form()
                const baseUrl = 'hr'

                const form = ref({
                    id: employee.id,
                    first_name: employee.first_name ?? '',
                    middle_name: employee.middle_name ?? '',
                    last_name: employee.last_name ?? '',
                    date_of_birth: employee.date_of_birth ?? '',
                    id_no: employee.id_no ?? '',
                    passport_no: employee.passport_no ?? '',
                    gender_id: employee.gender_id ?? '',
                    salutation_id: employee.salutation_id ?? '',
                    marital_status_id: employee.marital_status_id ?? '',
                    nationality_id: employee.nationality_id ?? '',
                    education_level_id: employee.education_level_id ?? '',
                    email: employee.email ?? '',
                    work_email: employee.work_email ?? '',
                    phone_no: employee.phone_no ?? '',
                    alternative_phone_no: employee.alternative_phone_no ?? '',
                    residential_address: employee.residential_address ?? '',
                    postal_address: employee.postal_address ?? '',
                    postal_code: employee.postal_code ?? '',
                })

                const setFormValue = (event) => {
                    let key = event.target.dataset.key

                    form.value[key] = $(event.target).val()
                }

                const filename = ref('No file selected')
                const fileChanged = (event) => {
                    if (event.target.files.length) {
                        filename.value = event.target.files[0].name
                    } else {
                        filename.value = 'No file selected'
                    }
                }
                
                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.first_name) {
                        formUtil.errorMessage('Enter first name')
                        return
                    }

                    if (!form.value.last_name) {
                        formUtil.errorMessage('Enter last name')
                        return
                    }

                    if (!form.value.date_of_birth) {
                        formUtil.errorMessage('Enter date of birth')
                        return
                    }

                    if (!form.value.id_no) {
                        formUtil.errorMessage('Enter ID no.')
                        return
                    }

                    if (!form.value.email) {
                        formUtil.errorMessage('Enter email')
                        return
                    }

                    if (!form.value.phone_no) {
                        formUtil.errorMessage('Enter phone no.')
                        return
                    }

                    if (!form.value.alternative_phone_no) {
                        formUtil.errorMessage('Enter alternative phone no.')
                        return
                    }

                    if (!form.value.gender_id) {
                        formUtil.errorMessage('Select gender')
                        return
                    }

                    if (!form.value.salutation_id) {
                        formUtil.errorMessage('Select salutation')
                        return
                    }

                    if (!form.value.marital_status_id) {
                        formUtil.errorMessage('Select marital status')
                        return
                    }

                    if (!form.value.nationality_id) {
                        formUtil.errorMessage('Select nationality')
                        return
                    }

                    if (!form.value.education_level_id) {
                        formUtil.errorMessage('Select education level')
                        return
                    }

                    if (!form.value.residential_address) {
                        formUtil.errorMessage('Enter residential address')
                        return
                    }
                    
                    processing.value = true

                    const formData = new FormData()
                    
                    for (const key in form.value) {
                        formData.append(key, form.value[key]);
                    }

                    let image = document.getElementById(`image`)

                    if (image.files[0]) {
                        formData.append('image', image.files[0])
                    }

                    axios.post(`${baseUrl}/management/employees-edit`, formData)
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
                        .then(response => referenceVariable.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.message))
                }

                const genders = ref([])
                const salutations = ref([])
                const maritalStatuses = ref([])
                const nationalities = ref([])
                const educationLevels = ref([])
                onMounted(() => {
                    fetchData(`${baseUrl}/configurations/gender-list`, genders)
                    
                    fetchData(`${baseUrl}/configurations/salutations-list`, salutations)
                    
                    fetchData(`${baseUrl}/configurations/marital-statuses-list`, maritalStatuses)

                    fetchData(`${baseUrl}/configurations/nationalities-list`, nationalities)

                    fetchData(`${baseUrl}/configurations/education-levels-list`, educationLevels)

                    $('#bio-data-app select').select2();
                })

                return {
                    user,
                    permissions,
                    processing,
                    form, 
                    submitForm,
                    genders,
                    salutations,
                    maritalStatuses,
                    nationalities,
                    educationLevels,
                    setFormValue,
                    filename,
                    fileChanged
                }
            }
        }).mount('#bio-data-app')
    </script>
@endpush