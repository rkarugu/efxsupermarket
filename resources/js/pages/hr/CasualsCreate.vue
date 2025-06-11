<template>
    <Card cardTitle="Add New Casual">
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
                <label for="date-of-birth">Date of Birth <span style="color: red">*</span></label>
                <input type="date" class="form-control" id="date-of-birth" :max="dayjs().subtract(18, 'year').format('YYYY-MM-DD')" v-model="form.date_of_birth">
            </div>
            <div class="form-group col-md-4">
                <label for="id-no">ID No. <span style="color: red">*</span></label>
                <input type="text" class="form-control" id="id-no" placeholder="Enter ID no." v-model="form.id_no">
            </div>
            <div class="form-group col-md-4">
                <label for="phone-no">Phone No. <span style="color: red">*</span></label>
                <input type="text" class="form-control" id="phone-no" placeholder="Enter phone no." v-model="form.phone_no">
            </div>
            <div class="form-group col-md-4">
                <label for="email">Email </label>
                <input type="text" class="form-control" id="email" placeholder="Enter email" v-model="form.email">
            </div>
            <div class="form-group col-md-4">
                <label>Gender <span style="color: red">*</span></label>
                <Select2Select 
                    :options="genders" 
                    optionValue="id" 
                    optionLabel="name" 
                    placeholder="Select gender..." 
                    v-model="form.gender_id" 
                />
            </div>
            <div class="form-group col-md-4">
                <label>Nationality <span style="color: red">*</span></label>
                <Select2Select 
                    :options="nationalities" 
                    optionValue="id" 
                    optionLabel="name" 
                    placeholder="Select nationality..." 
                    v-model="form.nationality_id" 
                />
            </div>
            <div class="form-group col-md-4">
                <label>Branch <span style="color: red">*</span></label>
                <Select2Select 
                    :options="branches" 
                    optionValue="id" 
                    optionLabel="name" 
                    placeholder="Select branch..." 
                    v-model="form.branch_id" 
                />
            </div>
        </div>

        <hr>

        <div class="pull-right">
            <button class="btn btn-secondary" :disabled="processing" @click="clearForm">
                <i class="fas fa-ban"></i>
                Reset
            </button>
            <button class="btn btn-primary" style="margin-left: 10px;" :disabled="processing" @click="handleSubmit">
                <i class="fas fa-floppy-disk"></i>
                Save
            </button>
        </div>
    </Card>
</template>

<script setup>
import dayjs from 'dayjs'
import { ref } from "vue"
import Card from "@/components/ui/Card.vue"
import Select2Select from "@/components/ui/form/Select2Select.vue"
import { useApi } from '@/composables/useApi.js'
import formUtil from '@/composables/useForm.js'

const { apiClient } = useApi()

const props = defineProps({
    branches: {
        type: String,
        required: true
    },
    genders: {
        type: String,
        required: true
    },
    nationalities: {
        type: String,
        required: true
    },
})

const branches = JSON.parse(props.branches)
const genders = JSON.parse(props.genders)
const nationalities = JSON.parse(props.nationalities)

const form = ref({
    first_name: '',
    middle_name: '',
    last_name: '',
    date_of_birth: '',
    id_no: '',
    phone_no: '',
    email: '',
    gender_id: '',
    nationality_id: '',
    branch_id: '',
})

const clearForm = () => {
    form.value = {
        first_name: '',
        middle_name: '',
        last_name: '',
        date_of_birth: '',
        id_no: '',
        phone_no: '',
        email: '',
        gender_id: '',
        nationality_id: '',
        branch_id: '',
    }
}

const processing = ref(false)

const handleSubmit = () => {
    if (!form.value.first_name) {
        formUtil.errorMessage('Enter first name')
        return true
    }

    if (!form.value.last_name) {
        formUtil.errorMessage('Enter last name')
        return true
    }

    if (!form.value.date_of_birth) {
        formUtil.errorMessage('Select date of birth')
        return true
    }

    if (!form.value.id_no) {
        formUtil.errorMessage('Enter ID no.')
        return true
    }

    if (!form.value.phone_no) {
        formUtil.errorMessage('Enter phone no.')
        return true
    }

    if (!form.value.gender_id) {
        formUtil.errorMessage('Select gender')
        return true
    }

    if (!form.value.nationality_id) {
        formUtil.errorMessage('Select nationality')
        return true
    }

    if (!form.value.branch_id) {
        formUtil.errorMessage('Select branch')
        return true
    }
    
    processing.value = true

    apiClient.post('hr/management/casuals', form.value)
        .then(response => {
            formUtil.successMessage(response.data.message)

            clearForm()

            processing.value = false
 
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

</script>
