<template>
    <select :id class="form-control" :onchange="selectChanged" v-model="model">
        <option :value="option[optionValue] ?? option" v-for="option in options">{{ option[optionLabel] ?? option }}</option>
    </select>
    
</template>

<script setup>
import { onMounted, useId, watch } from 'vue';

const { placeholder, select2Options } = defineProps({
    options: {
        type: Array,
        required: true
    },
    optionValue: {
        required: false,
        type: String
    },
    optionLabel: {
        required: false,
        type: String
    },
    placeholder: {
        type: String,
        default: 'Select...'
    },
    select2Options: {
        type: Object,
        default: {}
    }
})

const model = defineModel({required: true });

const id = useId()

const selectChanged = (event) => {
    model.value = event.target.value;
}

watch(() => model.value, (newValue) => {
    $(`#${id}`).val(newValue).trigger('change');
})

onMounted(() => {
    $(`#${id}`).select2({
        placeholder,
        ...select2Options
    })
})

</script>
