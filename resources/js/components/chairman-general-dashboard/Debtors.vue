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
                <table class="table table-bordered table-hover" id="create_datatable_debtors">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th style="text-align: right;">Balance B/F</th>
                            <th style="text-align: right;">Y Sales</th>
                            <th style="text-align: right;">Total Balance</th>
                            <th style="text-align: right;">Collections</th>
                            <th style="text-align: right;">Returns</th>
                            <th style="text-align: right;">Total Collections</th>
                            <th style="text-align: right;">Discount Returns</th>
                            <th style="text-align: right;">Last Trans </th>
                            <th style="text-align: right;">PD Cheques</th>
                            <th style="text-align: right;">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(debtor, index) in debtorsData" :key="debtor.id">
                            <th>{{ index + 1 }}</th> 
                            <td>{{ debtor.route_name }}</td> 
                            <td style="text-align: right;">{{ numberWithCommas(debtor.balance_bf.toFixed(2)) }}</td> 
                            <td style="text-align: right;">{{ numberWithCommas(debtor.debits) }}</td> 
                            <td style="text-align: right;">{{ numberWithCommas(debtor.totalDebitsBalance.toFixed(2)) }}</td> 
                            <td style="text-align: right;">{{ numberWithCommas(debtor.credits) }}</td> 
                            <td style="text-align: right;">{{ numberWithCommas(debtor.returns) }}</td> 
                            <td style="text-align: right;">{{ numberWithCommas(debtor.totalCollections) }}</td> 
                            <td style="text-align: right;">{{ numberWithCommas(debtor.discountReturns) }}</td> 
                            <td style="text-align: right;">{{ debtor.last_trans_time }}</td> 
                            <td style="text-align: right;">{{ debtor.pd_cheques }}</td> 
                            <td style="text-align: right;">{{ numberWithCommas(debtor.balance.toFixed(2)) }}</td> 

                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" style="text-align: right;">Totals:</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalBalanceBF.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalDebits.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalDebitsBalance.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalCredits.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalReturns.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalCollections.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalDiscountReturns.toFixed(2)) }}</th>
                            <th></th>
                            <th style="text-align: right;">{{ numberWithCommas(totalPDCheques.toFixed(2)) }}</th>
                            <th style="text-align: right;">{{ numberWithCommas(totalBalance.toFixed(2)) }}</th>
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

const debtorsData = ref([])
const totalBalanceBF = computed(() => debtorsData.value.reduce((sum, debtor) => sum + debtor.balance_bf, 0));
const totalDebits = computed(() => debtorsData.value.reduce((sum, debtor) => sum + debtor.debits, 0));
const totalDebitsBalance = computed(() => debtorsData.value.reduce((sum, debtor) => sum + debtor.totalDebitsBalance, 0));
const totalCredits = computed(() => debtorsData.value.reduce((sum, debtor) => sum + debtor.credits, 0));
const totalReturns = computed(() => debtorsData.value.reduce((sum, debtor) => sum + debtor.returns, 0));
const totalCollections = computed(() => debtorsData.value.reduce((sum, debtor) => sum + debtor.totalCollections, 0));
const totalDiscountReturns = computed(() => debtorsData.value.reduce((sum, debtor) => sum + debtor.discountReturns, 0));
const totalPDCheques = computed(() => debtorsData.value.reduce((sum, debtor) => sum + debtor.pd_cheques, 0));
const totalBalance = computed(() => debtorsData.value.reduce((sum, debtor) => sum + debtor.balance, 0));


const fetchSalesmanShifts = async () => {
    loader.value = true;
    try {
        const response = await apiClient.get(`chairman-general-dashboard-debtors/${props.branchId}/${props.startDate}`);      
        debtorsData.value = response.data
   

        $(document).ready(function() {
                $('#create_datatable_debtors').DataTable({
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
