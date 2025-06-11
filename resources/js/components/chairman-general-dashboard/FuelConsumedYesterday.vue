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
                    <table class="table table-bordered table-hover" id="create_datatable_yesterday_fuel_entries">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Route</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Shift Date</th>
                                <th>Delivery Date</th>
                                <th style="text-align: center;">Tonnage</th>
                                <th style="text-align: right;">Sales</th>
                                <th>LPO</th>
                                <th>Receipt</th>
                                <th>Status</th>
                                <th style="text-align: center;">Fuel</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(entry, index) in yesterdayFuelEntries" :key="entry.id">
                                <th>{{ index + 1 }}</th> 
                                <td>{{ entry.route }}</td>
                                <td>{{ entry.vehicle }}</td>
                                <td>{{ entry.driver }}</td>
                                <td>{{ entry.shift_date }}</td>
                                <td>{{ entry.delivery_date }}</td>
                                <td style="text-align: center;">{{ numberWithCommas(entry.shift_tonnage.toFixed(2)) +  ' T'}}</td>
                                <td style="text-align: right;">{{ numberWithCommas(entry.shift_total.toFixed(2)) }}</td>
                                <td>
                                        {{ entry.lpo_number  }}

                                </td>
                                <td>{{ entry.receipt_number }}</td>
                                <td>{{ entry.entry_status }}</td>
                                <td style="text-align: center;">{{ entry.actual_fuel_quantity + ' lts'}}</td>
                                <td>
                                    <a :href="`/admin/chairman-dashboard/fuel-entries/details/${entry.id}`" class="text-primary" title="Details" target="_blank">
                                        <i class="fa fa-eye text-primary fa-lg"></i>
                                        </a>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                            <th colspan="6">Total</th>
                            <th style="text-align: center;">{{ totalTonnage }}</th>
                            <th style="text-align: right;">{{ totalSales }}</th>
                            <th colspan="3"></th>
                            <th style="text-align: center;">{{ totalFuel }}</th>
                            <th></th>
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

    const yesterdayFuelEntries = ref([])
    // Computed properties for totals
    const totalSales = computed(() => {
    return numberWithCommas(yesterdayFuelEntries.value.reduce((sum, entry) => sum + entry.shift_total, 0).toFixed(2));
    });

    const totalTonnage = computed(() => {
    return numberWithCommas(yesterdayFuelEntries.value.reduce((sum, entry) => sum + entry.shift_tonnage, 0).toFixed(2)) + ' T';
    });

    const totalFuel = computed(() => {
    return numberWithCommas(yesterdayFuelEntries.value.reduce((sum, entry) => sum + entry.actual_fuel_quantity, 0) + ' lts');
    });

    const fetchSalesmanShifts = async () => {
        loader.value = true;
        try {
            const response = await apiClient.get(`chairman-general-dashboard-yesterday-fuel-entries/${props.branchId}/${props.startDate}`);      
            yesterdayFuelEntries.value = response.data

            $(document).ready(function() {
                    $('#create_datatable_yesterday_fuel_entries').DataTable({
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
