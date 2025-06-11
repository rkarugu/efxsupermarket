<div style="padding:10px" id="beneficiaries-app">
    <div v-cloak>
        <div style="text-align: right">
            <button class="btn btn-primary" @click="addBeneficiary">
                <i class="fa fa-plus"></i>
                Add Beneficiary
            </button>
        </div>

        <hr>
        
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
                        <select :id="`relationship-select-${index}`" v-model="form.beneficiaries[index].relationship_id" :data-index="index" :onchange="relationshipChanged">
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

        <hr>

        <div style="text-align: right">
            <button class="btn btn-primary" :disabled="!totalPercentageAllocated" @click="saveBeneficiariesChanges">
                <i class="fa fa-floppy-disk"></i>
                Save
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <script type="module">
        import { createApp, ref, onMounted, computed } from 'vue';

        createApp({
            setup() {
                const employee = {!! $employee !!}

                const formUtil = new Form()
                const baseUrl = 'hr'

                const form = ref({
                    employee_id : employee.id,
                    beneficiaries: [],
                    deleted: []
                })
                
                const beneficiaries = ref([])

                employee.beneficiaries.forEach(beneficiary => {
                    form.value.beneficiaries.push({
                        id: beneficiary.id,
                        relationship_id: beneficiary.relationship_id,
                        custom_relationship: beneficiary.custom_relationship,
                        is_minor: beneficiary.is_minor,
                        full_name: beneficiary.full_name,
                        email: beneficiary.is_minor ? beneficiary.guardian_email : beneficiary.email,
                        phone_no: beneficiary.is_minor ? beneficiary.guardian_phone_no : beneficiary.phone_no,
                        place_of_work: beneficiary.place_of_work ?? '',
                        id_no: beneficiary.id_no ?? '',
                        guardian_name: beneficiary.guardian_name ?? '',
                        percentage: beneficiary.percentage,
                    })
                })

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
                        $('#beneficiaries-app select').each(function() {
                            if (!$(this).data('select2')) {
                                $(this).select2({
                                    placeholder: "Select..."
                                })
                            }
                        })
                    }, 100)
                }

                const removeBeneficiary = (index) => {
                    if (form.value.beneficiaries[index].id) {
                        form.value.deleted.push(form.value.beneficiaries[index].id)
                    }
                    form.value.beneficiaries.splice(index, 1)
                }

                const relationshipChanged = (event) => {
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

                const processing = ref(false)

                const saveBeneficiariesChanges = () => {
                    
                    let error = false

                    form.value.beneficiaries.forEach((beneficiary, index) => {
                        if (!beneficiary.relationship_id) {
                            formUtil.errorMessage(`Select relationship for beneficiary #${index + 1}`)
                            error = true
                            return
                        }

                        if (selectedRelationship(beneficiary.relationship_id) == 'Other' && !beneficiary.custom_relationship) {
                            formUtil.errorMessage(`Enter relationship for beneficiary #${index + 1}`)
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

                    if (error) {
                        return
                    }
                    
                    if (!totalPercentageAllocated.value) {
                        formUtil.errorMessage('Percentage allocated should equal 100%')
                        return
                    }
                    
                    processing.value = true

                    axios.post('hr/management/employee-beneficiaries-update', form.value)
                        .then(response => {
                            formUtil.successMessage(response.data.message)

                            processing.value = false
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
                const relationships = ref([])
                onMounted(() => {
                    fetchData('hr/configurations/relationships', relationships)

                    $('#beneficiaries-app select').select2({
                        placeholder: 'Select...'
                    })
                })

                const selectedRelationship = (id) => {
                    return relationships.value.find(relationship => relationship.id == id)?.name ?? ''
                }

                return {
                    processing,
                    form, 
                    addBeneficiary,
                    relationships,
                    removeBeneficiary,
                    relationshipChanged,
                    totalPercentage,
                    saveBeneficiariesChanges,
                    totalPercentageAllocated,
                    isMinorChanged,
                    selectedRelationship
                }
            }
        }).mount('#beneficiaries-app')
    </script>
@endpush