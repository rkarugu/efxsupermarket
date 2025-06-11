<template>
    <!-- <div v-if="!loader"> -->
        <apexchart :options :series :height="options.chart?.height ?? 'auto'"></apexchart>
    <!-- </div> -->

    <!-- <Loader v-else /> -->
</template>

<script setup>
import Loader from '@/components/ui/Loader.vue'

defineProps({
    series: {
        type: Array,
        required: true
    },
    options: {
        type: Object,
        required: true
    }
    // loader: {
    //     type: Boolean,
    //     required: true
    // }
})

</script>
