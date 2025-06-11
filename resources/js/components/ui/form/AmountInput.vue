<template>
    <input type="text" class="form-control" :placeholder v-model="inputValue" @input="formatAmount">
</template>

<script setup>
import { ref, watch } from 'vue';
import { numberWithCommas } from '@/utils.js'

defineProps({
    placeholder: {
        type: String,
        default: 'Amount'
    }
})

const model = defineModel({required: true });

const inputValue = ref(numberWithCommas(model.value));

const formatAmount = (event) => {
    let value = event.target.value.replace(/,/g, '')

    inputValue.value = numberWithCommas(value)
    
    model.value = value
}

watch(() => model.value,  () => {
    inputValue.value = numberWithCommas(model.value)
})

</script>
