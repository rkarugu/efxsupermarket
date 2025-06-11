<template>
    <DepartmentalPerformance 
        :branchId 
        :departmentalPerformanceData 
        title="Departmental Sales Performance" 
        dataName="Cost" 
        dataKey="total_cost" 
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
