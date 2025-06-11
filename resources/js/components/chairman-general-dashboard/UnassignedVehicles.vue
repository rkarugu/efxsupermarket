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
                <table class="table table-bordered table-hover" id="create_datatable_unassigned_vehicles">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vehicle</th>
                            <th>Model</th>
                            <th>Designated Driver</th>
                            <th>Contact</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(entry, index) in unfuelledRoutes" :key="entry.id">
                            <th>{{ index + 1 }}</th> 
                            <td>{{ entry.vehicle }}</td>
                            <td>{{ entry.model }}</td>
                            <td>{{ entry.driver }}</td>
                            <td>{{ entry.driver_contact }}</td>
                                                 
                        
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

const unfuelledRoutes = ref([])


const fetchSalesmanShifts = async () => {
    loader.value = true;
    try {
        const response = await apiClient.get(`chairman-general-dashboard-unassigned-vehicles/${props.branchId}/${props.startDate}`);    
        console.log(response.data)  
        unfuelledRoutes.value = response.data

        $(document).ready(function() {
                $('#create_datatable_unassigned_vehicles').DataTable({
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
