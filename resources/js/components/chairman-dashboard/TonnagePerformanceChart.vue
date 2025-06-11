<template>
    <div>
        <div v-if="loader" class="d-flex justify-content-center align-items-center" style="height: 200px;">
            <div class="spinner-border" role="status">
                <div class="d-flex flex-column align-items-center">
                    <h4><i class="fa fa-spinner fa-spin" style="color: blue;"></i></h4>
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

const { months, tonnageData } = defineProps({
    months: {
        type: Array,
        required: true
    },
    tonnageData: {
        type: Object,
        required: true
    }
})

let options = ref({})
let series = ref([])

const loader = ref(true)

watch(() => tonnageData, (value) => {
    series.value = [
        {
            name: 'Tonnage',
            data: Object.keys(months).map(month => tonnageData[month] ?? 0)
        },
    ]

    options.value = {
        chart: {
            type: 'bar',
            height: 'auto',
            stacked: true,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                endingShape: 'rounded'
            },
        },
        colors: ['#2196F3'],
        xaxis: {
            categories: Object.values(months),
            title: {
                text: 'Month',
                style: {
                    fontSize: '14px'
                }
            },
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            title: {
                text: 'Tonnage',
                style: {
                    fontSize: '14px'
                }
            },
            labels: {
                formatter: function (val) {
                    return numberWithCommas(val)
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return numberWithCommas(val)
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
