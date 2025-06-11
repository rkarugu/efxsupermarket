<template>
    <div>
        <div v-if="loader" class="d-flex justify-content-center align-items-center" style="height: 200px;">
            <div class="spinner-border" role="status">
                <div class="d-flex flex-column align-items-center">
                    <h4><i class="fa fa-spinner fa-spin" style="color: red;"></i></h4>
                    <h4 class="text-center">Loading...</h4>
                </div>
            </div>
        </div>
        <div v-else>
            <ApexChart :options="options" :series="series" />
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { numberWithCommas } from '@/utils.js'
import ApexChart from '@/components/ui/ApexChart.vue';

const { returnsData, months } = defineProps({
    returnsData: {
        type: Object,
        required: true
    },
    months: {
        type: Array,
        required: true
    }
})

let options = ref({})
let series = ref([])

const loader = ref(true)

watch(() => returnsData, () => {
    series.value = [
        {
            name: 'Returns',
            data: Object.keys(months).map(month => returnsData[month] ?? 0)
        }
    ]
    
    options.value = {
        chart: {
            type: 'bar',
            height: 'auto'
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: Object.values(months),
        },
        yaxis: {
            title: {
                text: 'Amount'
            },
            labels: {
                formatter: function (val) {
                    return numberWithCommas(val)
                }
            }
        },
        fill: {
            opacity: 1
        },
        colors: ['#FF0000'],
        tooltip: {
            y: {
                formatter: function (val) {

                    return "KES " + numberWithCommas(val);
                }
            }
        }
    };

    loader.value = false
})

</script>
<style scoped>
.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>
