<template>
    <Card :cardTitle="title">
        <template #header-action>
                <div class="d-flex">
                    <div style="width: 100px; margin-right: 10px;">
                        <Select2Select
                            :options="years"
                            placeholder="Select year..."
                            v-model="year"
                        />
                    </div>
                    <div style="width: 150px; margin-right: 10px;">
                        <Select2Select
                            :options="months"
                            placeholder="Select month..."
                            v-model="month"
                        />
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" @click="handleFilter">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                </div>
        </template>

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
    </div>    </Card>
</template>

<script setup>
import { ref, watch } from 'vue'
import Card from '@/components/ui/CardWithoutBorder.vue'
import Select2Select from '@/components/ui/form/Select2Select.vue'
import ApexChart from '@/components/ui/ApexChart.vue';
import { numberWithCommas } from '@/utils.js'
import { useApi } from '@/composables/useApi.js'
import formUtil from '@/composables/useForm.js'

const { apiClient } = useApi()

const { branchId, departmentalPerformanceData, dataName, dataKey } = defineProps({
    branchId: {
        type: String,
        required: true
    },
    departmentalPerformanceData: {
        type: Array,
        required: true
    },
    title: {
        type: String,
        required: true
    },
    dataName: {
        type: String,
        required: true
    },
    dataKey: {
        type: String,
        required: true
    }
})

const currentYear = dayjs().year()
const years = Array.from({ length: 4 }, (_, index) => currentYear - index)

const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']

const year = ref(currentYear)
const month = ref(dayjs().format('MMMM'))

const series = ref([])
const options = ref([])
const loader = ref(true)

const generateChartData = (categories) => {
    series.value = [{
        name: `Total ${dataName}`,
        data: categories.map(category => parseFloat(category[dataKey]))
    }]
    
    options.value = {
        chart: {
            type: 'bar',
            height: 'auto',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                columnWidth: '50%',
                endingShape: 'rounded'
            }
        },
        xaxis: {
            categories: categories.map(category => category.category_description),
            title: {
                text: `Total ${dataName}`,
                style: {
                    fontSize: '14px'
                }
            },
            labels: {
                formatter: function(val) {
                    return numberWithCommas(val)
                }
            }
        },
        yaxis: {
            title: {
                text: 'Department',
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
        tooltip: {
            y: {
                formatter: function(val) {
                    return `Total ${dataName}: ${val.toLocaleString()}`;
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        responsive: [{
            breakpoint: 600,
            options: {
                plotOptions: {
                    bar: {
                        horizontal: false
                    }
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    loader.value = false
}

const fetchDepartmentalPerformance = () => {
    loader.value = true

    apiClient.get('chairman-dashboard-category-performance/' + branchId, {
        params: {
            year: year.value,
            month: month.value
        }
    })
        .then(response => {
            generateChartData(response.data)
        })
        .catch(error => formUtil.errorMessage(error.response.data.message))
}

const handleFilter = () => {
    fetchDepartmentalPerformance()
}

watch(() => departmentalPerformanceData, (data) => {
    generateChartData(data)
})

</script>
<style scoped>
.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>
