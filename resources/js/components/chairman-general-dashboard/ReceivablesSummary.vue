<template>
        <div class="row">
                        <StatCard 
                            color="bg-aqua"
                            title="Y Sales"
                            :value="loader ? null : numberWithCommas(yesterdaySales.toFixed(2))"
                            icon="fas fa-money-bill-wave"
                            sectionId="yesterday-sales"
                        />

                        <StatCard 
                            color="bg-red"
                            title="Y Returns"
                            :value="loader ? null : numberWithCommas(yesterdayReturns.toFixed(2))"
                            icon="fas fa-undo-alt"
                            sectionId="yesterday-sales"

                        />

                        <StatCard 
                            color="bg-green"
                            title="Total Collections"
                            :value="loader ? null: numberWithCommas(yesterdayCollections.toFixed(2))"
                            icon="fas fa-wallet"
                            sectionId="yesterday-sales"

                        />

                        <StatCard 
                            color="bg-blue"
                            title="Variance"
                            :value="loader ? null : numberWithCommas((yesterdaySales - yesterdayReturns - yesterdayCollections).toFixed(2))"
                            icon="fas fa-chart-line"
                            sectionId="yesterday-sales"

                        />
                        
        </div>
        <div class="row">
                        <StatCard 
                            color="bg-aqua"
                            title="Other Returns"
                            :value="loader ? null : numberWithCommas(otherReturns.toFixed(2))"
                            icon="fas fa-undo-alt"
                            sectionId="other-receivables"
                        />

                        <StatCard 
                            color="bg-red"
                            title="Total Returns"
                            :value="loader ? null : numberWithCommas((yesterdayReturns + otherReturns).toFixed(2))"
                            icon="fas fa-undo-alt"
                            sectionId="other-receivables"

                        />

                        <StatCard 
                            color="bg-green"
                            title="Other Collections"
                            :value="loader ? null: numberWithCommas(otherCollections.toFixed(2))"
                            icon="fas fa-wallet"
                            sectionId="other-receivables"

                        />

                        <LinkedStatCard 
                            color="bg-blue"
                            title="Total Bankings"
                            :value="loader ? null : numberWithCommas((yesterdayCollections + otherCollections).toFixed(2))"
                            icon="fas fa-piggy-bank"
                            :route="`/banking/route/details?date=${props.startDate}&branch=${props.branchId}`"
                        />
                        
        </div>

</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { numberWithCommas } from '@/utils.js'
import Loader from '@/components/ui/Loader.vue'
import StatCard from '@/components/ui/SmallStartCard.vue'
import LinkedStatCard from '@/components/ui/StatCard.vue'

import { useApi } from '@/composables/useApi.js'

const { apiClient } = useApi()

const props= defineProps({
    branchId: {
        type: String,
        required: true
    },
    startDate: {
        type: String,
        required: true
    }
})

const loader = ref(true);
const yesterdaySales = ref(0);
const yesterdayReturns = ref(0);
const yesterdayCollections = ref(0);
const otherReturns = ref(0);
const otherCollections = ref(0);

const fetchSalesmanShifts = async () => {
    loader.value = true;
    try {
        const response = await apiClient.get(`chairman-general-dashboard-receivables-summary/${props.branchId}/${props.startDate}`);  
        yesterdaySales.value = response.data.yesterdaySales;
        yesterdayReturns.value = response.data.yesterdayReturns;
        yesterdayCollections.value = response.data.yesterdayCollections;
        otherReturns.value = response.data.otherReturns;
        otherCollections.value = response.data.otherCollections;

        loader.value = false;
    } catch (error) {
        console.error('Error fetching data:', error);
        loader.value = false;
    }
};


watch([() => props.branchId, () => props.startDate], () => {
    fetchSalesmanShifts();
});

onMounted(() => {
    fetchSalesmanShifts();

    })
</script>

<style scoped>
.text-right {
    text-align: right;
}
.spinner-border {
      width: 3rem;
      height: 3rem;
  }
</style>
