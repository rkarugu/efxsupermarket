<template>
    <div>
        <div v-if="loader" class="d-flex justify-content-center align-items-center" style="height: 200px;">
        <div class="spinner-border" role="status">
            <div class="d-flex flex-column align-items-center">
                <h4><i class="fa fa-spinner fa-spin" style="color: green;"></i></h4>
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

const { salesData, months } = defineProps({
    salesData: {
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

watch(() => salesData, () => {
    series.value = [
        {
            name: 'Amount',
            data: Object.keys(months).map(month => salesData[month] ?? 0)
        }
    ]
    
    options.value = {
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: false
            }
        },
        colors: ['#008000'],
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
            min: 0,
            title: {
                text: 'Amount (KES) in millions',
                style: {
                    fontSize: '14px'
                }
            },
            labels: {
                formatter: function(value) {
                    if (value >= 1000000) {
                        return (value / 1000000).toFixed(0);
                    } else {
                        return parseFloat(value).toFixed(2);
                    }
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return 'KES ' + numberWithCommas(val);
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
