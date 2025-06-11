<template>
    <div>
        <div v-if="loader" class="d-flex justify-content-center align-items-center" style="height: 200px;">
        <div class="spinner-border" role="status">
            <div class="d-flex flex-column align-items-center">
                <h4><i class="fa fa-spinner fa-spin" style="color: black;"></i></h4>
                <h4 class="text-center">Loading...</h4>
            </div>
        </div>
        </div>
        <div v-else>
            <div class="table-responsive" >
                <table class="table table-bordered table-hover" id="create_datatable_yesterday_sales">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th style="text-align: right;">Y Sales</th>
                            <th style="text-align: right;">Y INV Sales</th>
                            <th style="text-align: right;">Returns</th>
                            <th style="text-align: right;">Net Sales</th>
                            <th style="text-align: right;">Eazzy</th>
                            <th style="text-align: right;">Vooma</th>
                            <th style="text-align: right;">Mpesa</th>
                            <th style="text-align: right;">Cheques</th>
                            <th style="text-align: right;">CRC</th>
                            <th style="text-align: right;">Total Cash</th>
                            <th style="text-align: right;">Variance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(ySale, index) in ySalesData" :key="ySale.id">
                            <th>{{ index + 1 }}</th> 
                            <td>{{ ySale.route_name }}</td> 
                            <td style="text-align: right;">{{ numberWithCommas(ySale.y_sales) }}</td>
                            <td style="text-align: right;">{{ numberWithCommas(ySale.y_inv_sales) }}</td>
                            <td :style="{ color: ySale.returns > 10000 ? 'white' : '', backgroundColor: ySale.returns > 10000 ? 'red' : '', textAlign: 'right' }">
                                {{ numberWithCommas(ySale.returns.toFixed(2)) }}
                            </td>

                            <td style="text-align: right;">{{ numberWithCommas((ySale.y_sales + ySale.y_inv_sales - ySale.returns).toFixed(2)) }}</td>
                            <td style="text-align: right;">{{ numberWithCommas(ySale.eazzy.toFixed(2)) }}</td>
                            <td style="text-align: right;">{{ numberWithCommas(ySale.vooma.toFixed(2)) }}</td>
                            <td style="text-align: right;">{{ numberWithCommas(ySale.mpesa.toFixed(2)) }}</td>
                            <td style="text-align: right;">{{ numberWithCommas(ySale.cheque.toFixed(2)) }}</td>
                            <td style="text-align: right;">{{ numberWithCommas(ySale.crc.toFixed(2)) }}</td>
                            <td style="text-align: right;">{{ numberWithCommas((ySale.eazzy + ySale.vooma + ySale.mpesa + ySale.crc).toFixed(2)) }}</td>
                            <td style="text-align: right;">{{ numberWithCommas(((ySale.y_sales + ySale.y_inv_sales - ySale.returns) - (ySale.eazzy + ySale.vooma + ySale.mpesa + ySale.crc)).toFixed(2)) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Totals</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalYSales.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalYInvSales.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalReturns.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalNetSales.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalEazzy.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalVooma.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalMpesa.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalCheques.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalCRC.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalCash.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalVariance.toFixed(2)) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
     
    </div>

</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { numberWithCommas } from '@/utils.js'
import Loader from '@/components/ui/Loader.vue'
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

const loader = ref(true)

const ySalesData = ref([])
// Computed properties to calculate totals for each numerical column
const totalYSales = computed(() => ySalesData.value.reduce((sum, sale) => sum + sale.y_sales, 0));
const totalYInvSales = computed(() => ySalesData.value.reduce((sum, sale) => sum + sale.y_inv_sales, 0));
const totalReturns = computed(() => ySalesData.value.reduce((sum, sale) => sum + sale.returns, 0));
const totalNetSales = computed(() => ySalesData.value.reduce((sum, sale) => sum + (sale.y_sales + sale.y_inv_sales - sale.returns), 0));
const totalEazzy = computed(() => ySalesData.value.reduce((sum, sale) => sum + sale.eazzy, 0));
const totalVooma = computed(() => ySalesData.value.reduce((sum, sale) => sum + sale.vooma, 0));
const totalMpesa = computed(() => ySalesData.value.reduce((sum, sale) => sum + sale.mpesa, 0));
const totalCheques = computed(() => ySalesData.value.reduce((sum, sale) => sum + sale.cheque, 0));
const totalCRC = computed(() => ySalesData.value.reduce((sum, sale) => sum + sale.crc, 0));
const totalCash = computed(() => ySalesData.value.reduce((sum, sale) => sum + (sale.eazzy + sale.vooma + sale.mpesa + sale.crc), 0));
const totalVariance = computed(() => ySalesData.value.reduce((sum, sale) => sum + ((sale.y_sales + sale.y_inv_sales - sale.returns) - (sale.eazzy + sale.vooma + sale.mpesa + sale.crc)), 0));

const fetchSalesmanShifts = async () => {
    loader.value = true;
    try {
        const response = await apiClient.get(`chairman-general-dashboard-yesterday-sales/${props.branchId}/${props.startDate}/receivables`);      
        ySalesData.value = response.data

        $(document).ready(function() {
                $('#create_datatable_yesterday_sales').DataTable({
                    'paging': true,
                    'lengthChange': true,
                    'searching': true,
                    'ordering': true,
                    'info': true,
                    'autoWidth': false,
                    'pageLength': 30,
                    'aoColumnDefs': [{
                        'bSortable': false,
                        'aTargets': 'noneedtoshort'
                    }],

                });

                loader.value = false
            })

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
