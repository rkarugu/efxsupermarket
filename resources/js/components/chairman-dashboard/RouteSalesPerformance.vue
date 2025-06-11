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
            <div class="table-responsive" >
                <table class="table table-bordered table-hover" id="create_datatable_10">
                    <thead>
                        <tr>
                            <th>Routes</th>
                            <template v-for="month in Object.values(months)">
                                <th>{{ month }}</th>
                            </template>
                            <th>Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(routeSales, route) in routeSalesData">
                            <td>{{ route }}</td>
                            <template v-for="month in Object.keys(months)">
                                <td class="text-right">{{ numberWithCommas((routeSales[month] ?? 0).toFixed(2)) }}</td>
                            </template>
                            <th class="text-right">{{ numberWithCommas(getRowGrandTotal(routeSales)) }}</th>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Grand Total</th>
                            <template v-for="month in Object.keys(months)">
                                <th class="text-right">{{ numberWithCommas(getColumnGrandTotal(month)) }}</th>
                            </template>
                            <th class="text-right">{{ numberWithCommas(grandestTotal) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
     
    </div>






   

</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { numberWithCommas } from '@/utils.js'
import Loader from '@/components/ui/Loader.vue'
import { useApi } from '@/composables/useApi.js'
import formUtil from '@/composables/useForm.js'

const { apiClient } = useApi()

const { branchId, months } = defineProps({
    branchId: {
        type: String,
        required: true
    },
    months: {
        type: Object,
        required: true
    }
})

const loader = ref(true)

const routeSalesData = ref([])

const getRowGrandTotal = (routeSales) => {
    return Object.values(routeSales).reduce((total, routeSale) => total + routeSale, 0).toFixed(2)
}

const getColumnGrandTotal = (month) => {
    return Object.values(routeSalesData.value).reduce((total, routeSales) => total += routeSales[month] ?? 0, 0).toFixed(2)
}

const grandestTotal = computed(() => {
    return Object.values(routeSalesData.value).reduce((total, routeSales) => total += Object.values(routeSales).reduce((acc, routeSale) => acc + routeSale, 0) ?? 0, 0).toFixed(2)
})

onMounted(() => {
    apiClient.get('chairman-dashboard-route-sales-performance/' + branchId)
        .then(response => {
            routeSalesData.value = response.data

            $(document).ready(function() {
                $('#create_datatable_10').DataTable({
                    'paging': true,
                    'lengthChange': true,
                    'searching': true,
                    'ordering': true,
                    'info': true,
                    'autoWidth': false,
                    'pageLength': 10,
                    'initComplete': function (settings, json) {
                        let info = this.api().page.info();
                        let total_record = info.recordsTotal;
                        if (total_record < 11) {
                            $('.dataTables_paginate').hide();
                        }
                    },
                    'aoColumnDefs': [{
                        'bSortable': false,
                        'aTargets': 'noneedtoshort'
                    }],

                });

                loader.value = false
            })
        })
        .catch(error => formUtil.errorMessage(error.response.data.message))
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
