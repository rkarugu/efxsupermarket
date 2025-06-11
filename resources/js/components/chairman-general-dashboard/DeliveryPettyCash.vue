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
                <table class="table table-bordered table-hover" id="create_datatable_delivery_petty_cash">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th >Reference</th>
                            <th >Recipient</th>
                            <th style="text-align: center;">Tonnage</th>
                            <th style="text-align: right;">Sales Amount</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(data, index) in deliveryPettyCashDate" :key="data.shift_id">
                            <th>{{ index + 1 }}</th> 
                            <td>{{ data.route_name }}</td> 
                            <td>{{ data.recipient + '-' + data.phone_number }}</td> 
                            <td style="text-align: center;">{{ data.tonnage.toFixed(2) }}</td> 
                            <td style="text-align: right;">{{ numberWithCommas(data.gross_sales) }}</td> 
                            <td style="text-align: right;">{{ numberWithCommas(data.amount) }}</td> 
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" style="text-align: right;">Totals:</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalTonnage.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalSales.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalAmount.toFixed(2)) }}</th>
                           
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

const deliveryPettyCashDate = ref([])
const totalTonnage = computed(() => deliveryPettyCashDate.value.reduce((sum, data) => sum + data.tonnage, 0));
const totalSales = computed(() => deliveryPettyCashDate.value.reduce((sum, data) => sum + data.gross_sales, 0));
const totalAmount = computed(() => deliveryPettyCashDate.value.reduce((sum, data) => sum + data.amount, 0));

const fetchSalesmanShifts = async () => {
    loader.value = true;
    try {
        const response = await apiClient.get(`chairman-general-dashboard-delivery-petty-cash/${props.branchId}/${props.startDate}`);      
        deliveryPettyCashDate.value = response.data

        $(document).ready(function() {
                $('#create_datatable_delivery_petty_cash').DataTable({
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
