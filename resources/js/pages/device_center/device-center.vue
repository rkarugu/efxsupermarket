<template>
    <div>
        <Card cardTitle="Device Center">
            <template #header-action>
                <div class="d-flex">
                    <a href="/admin/device-center" class="btn btn-primary" style="margin-left:5px;">
                        <i class="fas fa-long-arrow-alt-left"></i>
                        Back
                    </a>
                </div>
        </template>
            <div class="row">
                <div class="col-sm-6">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <tbody>
                                <tr>
                                <th>Device No.</th>
                                <td><span v-cloak>{{ device?.device_no }}</span></td>
                            </tr>
                            <tr>
                                <th>Serial No.</th>
                                <td><span v-cloak>{{ device?.serial_no }}</span></td>
                            </tr>
                            <tr>
                                <th>IMEI</th>
                                <td><span v-cloak>{{ device?.sim_card?.phone_number }}</span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-6">
                    <table class="table table-bordered"> 
                        <tbody>
                            <tr>
                            <th>Type</th>
                            <td><span v-cloak>{{ device.device_type?.title }}</span></td>
                        </tr>
                        <tr>
                            <th>Model</th>
                            <td><span v-cloak>{{ device?.model }}</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <NavTabs>
                <template #tabs>
                    <Tab :active="true" tabTitle="Allocation" tabPaneId="allocation" @click="changedTab('allocation')" />
                    <Tab tabTitle="IMEI" tabPaneId="simcard" @click="changedTab('simcard')" />
                    <Tab tabTitle="Repair" tabPaneId="repair" @click="changedTab('repair')"/>
                </template>

                <template #tab-panes>
                    <TabPane :active="true" tabPaneId="allocation">
                        <History :userRole :userPermissions :device :user />
                    </TabPane>
                    <TabPane tabPaneId="simcard">
                        <SimCard :userRole :userPermissions :device :user />
                    </TabPane>
                    <TabPane tabPaneId="repair">
                        <RepairDevice :userRole :userPermissions :device :user />
                    </TabPane>

                </template>
            </NavTabs>
        </Card>

    </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import Card from "@/components/ui/Card.vue";
import NavTabs from "@/components/ui/NavTabs.vue"
import Tab from "@/components/ui/Tab.vue"
import TabPane from "@/components/ui/TabPane.vue"
import { useApi } from '@/composables/useApi.js'
import formUtil from '@/composables/useForm.js'
import History from "@/components/device-center/history.vue"
import SimCard from "@/components/device-center/sim.vue"
import RepairDevice from "@/components/device-center/repairs.vue"

const { apiClient } = useApi();

const props = defineProps({
    user: {
        type: String,
        required: true
    },
    userRole: {
        type: String,
        required: true
    },
    userPermissions: {
        type: String,
        required: true
    },
    device: {
        type: String,
        required: true
    }
})
const user = JSON.parse(props.user)
const device = JSON.parse(props.device);
const permissions = Object.keys(JSON.parse(props.userPermissions))

const changedTab =(id)=>{
    setTimeout(function() {
        $('#'+id).find('table').DataTable().columns.adjust().responsive.recalc();
    }, 100);
}

</script>