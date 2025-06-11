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
        <div class="table-responsive" v-else>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Branch</th>
                    <th>Visited Routes</th>
                    <th>Centers</th>
                    <th>Customers</th>
                    <th>Tonnage</th>
                    <th>Route Sales</th>
                    <th>Pos Sales</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(branch, index) in branchPerformanceData" :key="branch.name">
                    <td>{{ index + 1 }}</td>
                    <td>{{ branch.name }}</td>
                    <td>{{ branch.routes_with_orders ?? 0 }}</td>
                    <td>{{ branch.centers ?? 0 }}</td>
                    <td>{{ branch.branch_customers ?? 0 }}</td>
                    <td>{{ numberWithCommas(branch.tonnage ?? 0) }}</td>
                    <td>{{ numberWithCommas(branch.route_sales ?? 0) }}</td>
                    <td>{{ numberWithCommas(branch.pos_sales ?? 0) }}</td>
                    <td>{{ numberWithCommas((branch.pos_sales + branch.route_sales ?? 0)) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Totals</th>
                    <th>{{ numberWithCommas(visitedRoutes) }} </th>
                    <th>{{ numberWithCommas(totalCenters) }}</th>
                    <th>{{ numberWithCommas(totalBranchCustomers) }}</th>
                    <th>{{ numberWithCommas(totalTonnage) }}</th>
                    <th>{{ numberWithCommas(totalRouteSales) }}</th>
                    <th>{{ numberWithCommas(totalPosSales) }}</th>
                    <th>{{ numberWithCommas(totalRouteSales + totalPosSales) }}</th>

                </tr>
            </tfoot>
        </table>
    </div>
    </div>



</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { numberWithCommas } from '@/utils.js'
import { useApi } from '@/composables/useApi.js'
import formUtil from '@/composables/useForm.js'
import Loader from '@/components/ui/Loader.vue'

const { apiClient } = useApi()

const visitedRoutes = computed(() => branchPerformanceData.value.reduce((acc, branch) => acc + branch.routes_with_orders, 0))

const totalCenters = computed(() => branchPerformanceData.value.reduce((acc, branch) => acc + branch.centers, 0))

const totalBranchCustomers = computed(() => branchPerformanceData.value.reduce((acc, branch) => acc + branch.branch_customers, 0))

const totalTonnage = computed(() => branchPerformanceData.value.reduce((acc, branch) => acc + branch.tonnage, 0))

const totalRouteSales = computed(() => branchPerformanceData.value.reduce((acc, branch) => acc + branch.route_sales, 0))

const totalPosSales = computed(() => branchPerformanceData.value.reduce((acc, branch) => acc + branch.pos_sales, 0))

const loader = ref(true)

const branchPerformanceData = ref([])
onMounted(() => {
    apiClient.get('chairman-dashboard-branch-performance')
        .then(response => {
            console.log(response);
            branchPerformanceData.value = response.data || [];

            loader.value = false
        })
        .catch(error => {
              branchPerformanceData.value = [];
            formUtil.errorMessage(error.response.data.message)
        });
})
</script>
  
<style scoped>
.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>
