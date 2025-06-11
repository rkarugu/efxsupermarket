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
                <table class="table table-bordered table-hover" id="unvisited-shifts">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Route</th>
                            <th>Shift Type</th>
                            <th>Opened At</th>
                            <th>Closed At</th>
                            <th>Duration</th>
                            <th>Salesman</th>
                            <th>CC</th>
                            <th>MWO</th>
                            <th>WO</th>
                            <th>Unmet</th>
                            <th>Tonnage</th>
                            <!-- <th>Delivery Status</th>
                            <th>Vehicle</th>
                            <th>Fuel</th> -->
                            <th style="text-align: right;">Shift Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(shift, index) in salesmanShiftsData" :key="shift.id">
                            <th>{{ index + 1 }}</th> 
                            <td>{{ shift.route_name }}</td> 
                            <td>{{ shift.computed_shift_type }}</td> 
                            <td>{{ shift.shift_start_time }}</td> 
                            <td>{{ shift.shift_close_time }}</td> 
                            <td>{{ shift.duration }}</td> 
                            <td>{{ shift.salesman }}</td> 
                            <td style="text-align: center;">{{ shift.shift_met_customers + '/' + shift.customer_count }}</td>
                            <td style="text-align: center;">{{ shift.met_with_order }}</td>
                            <td style="text-align: center;">{{ shift.met_without_order }}</td>
                            <td style="text-align: center;">{{ shift.unmet }}</td>
                            <td>
                                <div class="progress mt-1" style="flex-grow: 1; margin-right: 10px; background-color: grey; position: relative;">
                                    <div class="progress-bar" role="progressbar" 
                                        :style="{ width: shift.tonnage_percentage + '%' }" 
                                        :aria-valuenow="shift.tonnage_percentage" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                    </div>
                                    <span style="position: absolute; width: 100%; text-align: center; top: 0; left: 0; color:white;">
                                        {{ numberWithCommas(shift.shift_tonnage, 2) }} / {{ shift.tonnage_target }}
                                    </span>
                                </div>
                            </td>
                            <!-- <td>
                                <a :href="`/admin/delivery-schedules/${shift.delivery_id}`" class="text-primary" title="Delivery Schedule" target="_blank">
                                    {{ shift.delivery_status }}
                                </a>
                                </td> -->
                            <!-- <td>{{ shift.license_plate_number + ' (' + shift.driver_name + ')' }}</td> -->
                            <!-- <td style="text-align: center;">
                                <template v-if="shift.fuel_entry_id !== null">
                                    <a :href="`/admin/chairman-dashboard/fuel-entries/details/${shift.fuel_entry_id}`" class="text-primary" title="Details" target="_blank">
                                        {{ shift.lpo_number + ' (' + (shift.fuel_consumed !== null ? shift.fuel_consumed + ' lts' : '-') + ')' }}
                                    </a>
                                </template>
                                <template v-else>
                                    {{ shift.lpo_number + ' (' + (shift.fuel_consumed !== null ? shift.fuel_consumed + ' lts' : '-') + ')' }}
                                </template>
                            </td> -->

                            <td style="text-align: right;">{{ numberWithCommas(shift.shift_total) }}</td>
                         
                            <td>
                                <a :href="`/admin/salesman-shift/${shift.id}`" class="text-primary" title="Summary" target="_blank">
                                    <i class="fa fa-eye text-primary fa-lg"></i>
                                </a>

                            </td>


                        </tr>
                    </tbody>
                    
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

const salesmanShiftsData = ref([])

const fetchSalesmanShifts = async () => {
    loader.value = true;
    try {
        const response = await apiClient.get(`chairman-general-dashboard-unvisited-shifts-summary/${props.branchId}/${props.startDate}`);
        salesmanShiftsData.value = response.data

        $(document).ready(function() {
                $('#unvisited-shifts').DataTable({
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

    });

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
