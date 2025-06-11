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
import { onMounted, ref } from 'vue'
import { numberWithCommas } from '@/utils.js'
import ApexChart from '@/components/ui/ApexChart.vue';
import { useApi } from '@/composables/useApi.js'
import formUtil from '@/composables/useForm.js'

const { apiClient } = useApi()

const { branchId, months } = defineProps({
    branchId: {
        type: String,
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

const generateSeriesData = (data, key) => {
    return Object.keys(months).map(month => {
        let dataItem = data.find(item => item.month == month)

        return dataItem ? dataItem[key] : 0
    })
}

onMounted(() => {
    apiClient.get('chairman-dashboard-met-unmet/' + branchId)
        .then(response => {
            let data = response.data

            series.value = [{
                    name: 'Met',
                    data: generateSeriesData(data, 'total_met'),
                },
                {
                    name: 'Unmet',
                    data: generateSeriesData(data, 'total_unmet'),
                },
                {
                    name: 'Onsite',
                    data: generateSeriesData(data, 'onsite'),
                },
                {
                    name: 'Offsite',
                    data: generateSeriesData(data, 'offsite'),
                },
                {
                    name: 'Met Without Orders',
                    data: generateSeriesData(data, 'met_without_orders'),
                },
                {
                    name: 'Total Shops Count',
                    data: generateSeriesData(data, 'shops_count'),
                }
            ]
            
            options.value = {
                chart: {
                    type: 'line',
                    height: 350,
                    stacked: false,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#2196F3', '#4CAF50', '#FF6347', '#9966FF', '#FF9F40', '#000000'],
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
                        text: 'Customers in thousands',
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
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return numberWithCommas(val.toFixed(2))
                        }
                    }
                }
            };

        loader.value = false

        })
        .catch(error => formUtil.errorMessage(error.response.data.message))
})

</script>
<style scoped>
.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>
