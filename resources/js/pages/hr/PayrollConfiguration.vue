<template>
    <div>
        <Card cardTitle="HR and Payroll Configurations - Payroll"></Card>

        <Card>
            <NavTabs>
                <template #tabs>
                    <Tab :active="true" tabTitle="Earnings" tabPaneId="earnings" v-if="canViewEarnings" />
                    <Tab tabTitle="Deductions" tabPaneId="deductions" v-if="canViewDeductions" />
                    <Tab tabTitle="PAYE" tabPaneId="paye" v-if="canViewPaye" />
                    <Tab tabTitle="NSSF" tabPaneId="nssf" v-if="canViewNssf" />
                    <Tab tabTitle="SHIF" tabPaneId="shif" v-if="canViewShif" />
                    <Tab tabTitle="Housing Levy" tabPaneId="housing-levy" v-if="canViewHousingLevy" />
                    <Tab tabTitle="Reliefs" tabPaneId="reliefs" v-if="canViewRelief" />
                    <Tab tabTitle="Settings" tabPaneId="settings" v-if="canViewSetting" />
                </template>

                <template #tab-panes>
                    <TabPane :active="true" tabPaneId="earnings" v-if="canViewEarnings">
                        <Earnings :userRole :userPermissions :earnings @earnings-form-submitted="fetchEarnings" />
                    </TabPane>

                    <TabPane tabPaneId="deductions" v-if="canViewDeductions">
                        <Deductions :userRole :userPermissions :deductions @refresh-deductions="fetchDeductions" />
                    </TabPane>

                    <TabPane tabPaneId="paye" v-if="canViewPaye">
                        <Paye :userRole :userPermissions />
                    </TabPane>

                    <TabPane tabPaneId="nssf" v-if="canViewNssf">
                        <Nssf :userRole :userPermissions />
                    </TabPane>

                    <TabPane tabPaneId="shif" v-if="canViewShif">
                        <Shif :userRole :userPermissions />
                    </TabPane>

                    <TabPane tabPaneId="housing-levy" v-if="canViewHousingLevy">
                        <HousingLevy :userRole :userPermissions />
                    </TabPane>

                    <TabPane tabPaneId="reliefs" v-if="canViewRelief">
                        <Reliefs :userRole :userPermissions :earnings :deductions />
                    </TabPane>

                    <TabPane tabPaneId="settings" v-if="canViewSetting">
                        <Settings :userRole :userPermissions />
                    </TabPane>
                </template>
            </NavTabs>
        </Card>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue"
import Card from "@/components/ui/Card.vue"
import NavTabs from "@/components/ui/NavTabs.vue"
import Tab from "@/components/ui/Tab.vue"
import TabPane from "@/components/ui/TabPane.vue"
import Earnings from "@/components/payroll-configuration/Earnings.vue"
import Deductions from "@/components/payroll-configuration/Deductions.vue"
import Paye from "@/components/payroll-configuration/Paye.vue"
import Nssf from "@/components/payroll-configuration/Nssf.vue"
import Shif from "@/components/payroll-configuration/Shif.vue"
import HousingLevy from "@/components/payroll-configuration/HousingLevy.vue"
import Reliefs from "@/components/payroll-configuration/Reliefs.vue"
import Settings from "@/components/payroll-configuration/Settings.vue"
import { useApi } from '@/composables/useApi.js'
import formUtil from '@/composables/useForm.js'

const { apiClient } = useApi()

const props = defineProps({
    userRole: {
        type: String,
        required: true
    },
    userPermissions: {
        type: String,
        required: true
    },
})

const permissions = Object.keys(JSON.parse(props.userPermissions))

const canViewEarnings = computed(() => permissions.includes('hr-and-payroll-configurations-earning___view') || props.userRole == 1)
const canViewDeductions = computed(() => permissions.includes('hr-and-payroll-configurations-deduction___view') || props.userRole == 1)
const canViewPaye = computed(() => permissions.includes('hr-and-payroll-configurations-paye___view') || props.userRole == 1)
const canViewNssf = computed(() => permissions.includes('hr-and-payroll-configurations-nssf___view') || props.userRole == 1)
const canViewRelief = computed(() => permissions.includes('hr-and-payroll-configurations-relief___view') || props.userRole == 1)
const canViewShif = computed(() => permissions.includes('hr-and-payroll-configurations-shif___view') || props.userRole == 1)
const canViewHousingLevy = computed(() => permissions.includes('hr-and-payroll-configurations-housing-levy___view') || props.userRole == 1)
const canViewSetting = computed(() => permissions.includes('hr-and-payroll-configurations-setting___view') || props.userRole == 1)

const earnings = ref([])
const fetchEarnings = async () => {
    try {
        let response = await apiClient.get('hr/configurations/earnings')

        earnings.value = response.data
    } catch (error) {
        formUtil.errorMessage(error.response.data.message)
    }
}

const deductions = ref([])
const fetchDeductions = async () => {
    try {
        let response = await apiClient.get('hr/configurations/deductions')

        deductions.value = response.data
    } catch (error) {
        formUtil.errorMessage(error.response.data.message)
    }
}

onMounted(() => {
    fetchEarnings()
    fetchDeductions()
})

</script>