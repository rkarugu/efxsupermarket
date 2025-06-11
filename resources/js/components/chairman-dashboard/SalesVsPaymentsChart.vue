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
  import { computed, onMounted, ref, toRaw, watch } from 'vue'
  import { numberWithCommas } from '@/utils.js'
  import ApexChart from '@/components/ui/ApexChart.vue';
  import { useApi } from '@/composables/useApi.js'
  import formUtil from '@/composables/useForm.js'
  
  const { apiClient } = useApi()
  
  const { branchId, salesData, returnsData, months } = defineProps({
      branchId: {
          type: String,
          required: true
      },
      salesData: {
          type: Object,
          required: true
      },
      returnsData: {
          type: Object,
          required: true
      },
      months: {
          type: Array,
          required: true
      }
  })
  
  const paymentData = ref({})
  const fetchPaymentData = () => {
      apiClient.get(`chairman-dashboard-payments/${branchId}`)
          .then(response => paymentData.value = response.data)
          .catch(error => formUtil.errorMessage(error.response.data.message))
  }
  
  const refObjectIsNotNull = (refObject) => {
      return JSON.stringify(toRaw(refObject)) !== JSON.stringify({})
  }
  
  const chartDataPresent = computed(() => {
      return refObjectIsNotNull(salesData) && refObjectIsNotNull(returnsData) && refObjectIsNotNull(paymentData.value)
  })
  
  const generateSeriesData = (data) => Object.keys(months).map(month => data[month] ?? 0)
  
  let series = ref([])
  let options = ref({})
  const loader = ref(true)
  
  watch(() => chartDataPresent.value, () => {
      if (chartDataPresent.value) {
          series.value = [
              {
                  name: 'Sales',
                  data: generateSeriesData(salesData)
              },
              {
                  name: 'Payments',
                  data: generateSeriesData(paymentData.value)
              },
              {
                  name: 'Returns',
                  data: generateSeriesData(returnsData)
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
              colors: ['#2196F3', '#008000','#FF0000'],
              tooltip: {
                  y: {
                      formatter: function (val) {
                          return "KES " + numberWithCommas(val);
                      }
                  }
              }
          };
  
          loader.value = false; 
      }
  })
  
  onMounted(() => {
      fetchPaymentData()
  })
  
  </script>
  
  <style scoped>
  .spinner-border {
      width: 3rem;
      height: 3rem;
  }
  </style>
    
 