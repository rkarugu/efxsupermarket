<template>
    <DepartmentalPerformance 
        :branchId 
        :departmentalPerformanceData 
        title="Departmental Tonnage Performance" 
        dataName="Tonnage" 
        dataKey="total_tonnage" 
    />
</template>

<script setup>
import DepartmentalPerformance from '@/components/chairman-dashboard/DepartmentalPerformance.vue';

defineProps({
    branchId: {
        type: String,
        required: true
    },
    departmentalPerformanceData: {
        type: Array,
        required: true
    },
})
</script>
