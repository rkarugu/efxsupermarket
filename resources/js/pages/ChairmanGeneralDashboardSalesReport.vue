<template>
    <div class="box box-primary">
            <div :class="{ 'box-header-sticky': isSticky }" class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="box-title">Operations Dashboard - Sales Report</h4>
                    <div>
                    <!-- Tabs go here -->
                    <ul class="nav nav-tabs">
                        <!-- <li class="nav-item">
                            <a 
                                class="nav-link" 
                                :class="{ active: activeTab === 'sales-summary' }" 
                                href="#sales-summary" 
                                @click.prevent="setActiveTab('sales-summary')"
                            >Sales Summary</a>
                        </li> -->
                     
                        <!-- <li class="nav-item">
                            <a 
                                class="nav-link" 
                                :class="{ active: activeTab === 'deliveries' }" 
                                href="#deliveries" 
                                @click.prevent="setActiveTab('deliveries')"
                            >Deliveries</a>
                        </li> -->
                        <li class="nav-item">
                            <a 
                                class="nav-link" 
                                :class="{ active: activeTab === 'yesterday-sales' }" 
                                href="#yesterday-sales" 
                                @click.prevent="setActiveTab('yesterday-sales')"
                            >Receivables</a>
                        </li>
                        <li class="nav-item">
                            <a 
                                class="nav-link" 
                                :class="{ active: activeTab === 'other-receivables' }" 
                                href="#other-receivables" 
                                @click.prevent="setActiveTab('other-receivables')"
                            >Other Receivables</a>
                        </li>
                        <li class="nav-item">
                            <a 
                                class="nav-link" 
                                :class="{ active: activeTab === 'receivables-summary' }" 
                                href="#receivables-summary" 
                                @click.prevent="setActiveTab('receivables-summary')"
                            >Receivables Summary</a>
                        </li>
                        <li class="nav-item">
                            <a 
                                class="nav-link" 
                                :class="{ active: activeTab === 'debtors' }" 
                                href="#debtors" 
                                @click.prevent="setActiveTab('debtors')"
                            >Debtors</a>
                        </li>
                        <li class="nav-item">
                            <a 
                                class="nav-link" 
                                :href="`/banking/route/details?date=${startDate}&branch=${branchId}`" 
                                target="_blank"
                            >Banking</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a 
                                class="nav-link" 
                                :class="{ active: activeTab === 'expenses' }" 
                                href="#expenses" 
                                @click.prevent="setActiveTab('expenses')"
                            >Expenses</a>
                        </li>
                        <li class="nav-item">
                            <a 
                                class="nav-link" 
                                :class="{ active: activeTab === 'other-expenses' }" 
                                href="#other-expenses" 
                                @click.prevent="setActiveTab('other-expenses')"
                            >Other Expenses</a>
                        </li> -->
                    </ul>
                </div>
                </div>
            </div>
            
            <div class="box-body">
                <!-- Filters -->
                <div class="row">
                    <div class="col-md-2">
                        <label for="branch">Branch</label>
                        <Select2Select 
                            :options="branches" 
                            optionValue="id" 
                            optionLabel="name" 
                            placeholder="Select Branch..." 
                            v-model="branchId" 
                            style="width: 350px;"
                        />
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="date">Date</label>
                        <input type="date" v-model="startDate" class="form-control"/>
                    </div>
                    <!-- <div class="col-md-3 form-group">
                       <h5>KEY:</h5>
                       <h6><strong>CC:</strong> Customer Count  <strong>MWO:</strong> Met With Orders <strong>WO:</strong> Met Without Orders</h6>
                    </div>
                    <div class="col-md-5 form-group pull-right">
                        <div class="row">
                            <h5>Delivery Key:</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h6><strong>Consolidating:</strong> Store keepers dispatching</h6>
                                <h6><strong>Consolidated:</strong> All stores have dispatched </h6>
                            </div>
                            <div class="col-md-4">
                                <h6><strong>Loaded:</strong> Driver has received all items </h6>
                                <h6><strong>In Progess:</strong> Delivery ongoing </h6>
                                <h6><strong>Finished:</strong> Delivery complete </h6>



                            </div>
                        </div>
                    </div> -->
                   
                </div>
                
                 <!-- Salesman Sales Summary -->
                <!-- <div class="row"  id="sales-summary">
                        <div class="col-md-12">
                            <Card cardTitle="Sales Summary">
                                <SalesmanShiftSummary :branchId="branchId" :startDate="startDate" />
                            </Card>
                        </div>
                </div>   -->

                 <!-- Deliveries -->
                 <!-- <div class="row"  id="deliveries">
                        <div class="col-md-12">
                            <Card cardTitle="Deliveries">
                                <Deliveries :branchId="branchId" :startDate="startDate" />
                            </Card>
                        </div>
                </div>   -->

                  <!-- Other Deliveries -->
                  <!-- <div class="row"  id="other-deliveries">
                        <div class="col-md-12">
                            <Card cardTitle="Other Deliveries">
                                <OtherDeliveries :branchId="branchId" :startDate="startDate" />
                            </Card>
                        </div>
                </div>   -->
                <!-- Undelivered Routes -->
                <!-- <div class="row"  id="undelivered">
                        <div class="col-md-12">
                            <Card cardTitle="Undelivered">
                                <Undelivered :branchId="branchId" :startDate="startDate" />
                            </Card>
                        </div>
                </div>   -->
                <!-- Unassigned Vehicles -->
                <!-- <div class="row"  id="unassigned-vehicles">
                        <div class="col-md-12">
                            <Card cardTitle="Unassigned Vehicles">
                                <UnassignedVehicles :branchId="branchId" :startDate="startDate" />
                            </Card>
                        </div>
                </div> -->

                 


                <!-- Yesterday Sales -->
                 <div class="row">
                    <div class="col-md-12" id="yesterday-sales">
                        <Card cardTitle="Receivables">
                            <YesterdaySales :branchId="branchId" :startDate="startDate" />
                        </Card>
                    </div>
                 </div>

                 <!-- Other Receivables -->
                 <div class="row" id="other-receivables">
                    <div class="col-md-12">
                        <Card cardTitle="Other Receivables">
                            <OtherReceivables :branchId="branchId" :startDate="startDate" />
                        </Card>
                    </div>
                 </div>
                 <!-- Receivables summary -->
                 <div class="row" id="receivables-summary">
                    <div class="col-md-12">
                        <Card cardTitle="Receivables Summary">
                            <ReceivablesSummary :branchId="branchId" :startDate="startDate" />
                        </Card>
                    </div>
                 </div>

                 <!-- Debtors -->
                 <div class="row">
                    <div class="col-md-12" id="debtors">
                        <Card cardTitle="Debtors">
                            <Debtors :branchId="branchId" :startDate="startDate" />
                        </Card>
                    </div>
                 </div>


                <!-- Expenses -->
                <!-- <div class="row" id="expenses">
                    <div class="col-md-12">
                       <h4>Expenses</h4>
                    </div>
   
                 </div> -->
                 <!-- travel order taking -->
                 <!-- <div class="row">
                    <div class="col-md-12">
                        <Card cardTitle="Travel Order Taking">
                            <SalesmanPettyCash :branchId="branchId" :startDate="startDate" />
                        </Card>
                    </div>
                 </div> -->
                 <!-- travel delivery -->
                 <!-- <div class="row">
                    <div class="col-md-12">
                        <Card cardTitle="Travel Delivery">
                            <DeliveryPettyCash :branchId="branchId" :startDate="startDate" />
                        </Card>
                    </div>
                 </div> -->

               
                   <!-- Other Epenses -->
                   <!-- <div class="row" id="other-expenses">
                    <div class="col-md-12">
                        <Card cardTitle="Other Expenses">
                            <OtherExpenses :branchId="branchId" :startDate="startDate" />
                        </Card>
                    </div>
                 </div> -->

            </div>

    </div>
</template>

<script setup>
import { onMounted, onUnmounted,ref, watch } from 'vue'
import Card from '@/components/ui/CardWithoutBorder.vue'
import StatCard from '@/components/ui/StatCard.vue'
import Select2Select from '@/components/ui/form/Select2Select.vue'
import SalesmanShiftSummary from '@/components/chairman-general-dashboard/SalesmanShiftSummary.vue'
import YesterdaySales from '@/components/chairman-general-dashboard/YesterdaySales.vue'
import OtherReceivables from '@/components/chairman-general-dashboard/OtherReceivables.vue'
import Debtors from '@/components/chairman-general-dashboard/Debtors.vue'
import SalesmanPettyCash from '@/components/chairman-general-dashboard/SalesmanPettyCash.vue'
import DeliveryPettyCash from '@/components/chairman-general-dashboard/DeliveryPettyCash.vue'
import OtherExpenses from '@/components/chairman-general-dashboard/OtherExpenses.vue'
import ReceivablesSummary from '@/components/chairman-general-dashboard/ReceivablesSummary.vue'
import FuelConsumedYesterday from '@/components/chairman-general-dashboard/FuelConsumedYesterday.vue'
import UnfuelledVehicles from '@/components/chairman-general-dashboard/UnfuelledVehicles.vue'
import Deliveries from '@/components/chairman-general-dashboard/Deliveries.vue'
import OtherDeliveries from '@/components/chairman-general-dashboard/OtherDeliveries.vue'
import Undelivered from '@/components/chairman-general-dashboard/Undelivered.vue'
import { numberWithCommas } from '@/utils.js'
import { useApi } from '@/composables/useApi.js'
import UnassignedVehicles from '../components/chairman-general-dashboard/UnassignedVehicles.vue'

const { apiClient } = useApi()

const props = defineProps({
    branches: {
        required: true,
        type: String
    },
    branchId: {
        required: true,
        type: String
    },
 
 
})

const branchId = ref(props.branchId)
const branches = JSON.parse(props.branches);

const getYesterdayDate = () => {
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    const yyyy = yesterday.getFullYear();
    const mm = String(yesterday.getMonth() + 1).padStart(2, '0');   
    const dd = String(yesterday.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
};
const startDate = ref(getYesterdayDate());
const activeTab = ref('sales-summary');
const isSticky = ref(false);
const checkStickyHeader = () => {
    const header = document.querySelector('.box-header');
    if (header) {
        isSticky.value = window.scrollY > header.offsetTop;
    }
};

// Set active tab when clicked
const setActiveTab = (tab) => {
    activeTab.value = tab;
    const element = document.getElementById(tab);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

// Highlight active tab based on scroll position
const handleScroll = () => {
    const sections = ['sales-summary', 'expenses', 'other-expenses', 'yesterday-sales', 'other-receivables', 'debtors', 'receivables-summary', 'deliveries'];
    const scrollPosition = window.scrollY;

    sections.forEach(section => {
        const element = document.getElementById(section);
        if (element) {
            const { offsetTop, offsetHeight } = element;
            if (scrollPosition >= offsetTop && scrollPosition < offsetTop + offsetHeight) {
                activeTab.value = section;
            }
        }
    });
};




const fetchData = async () => {

};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('scroll', checkStickyHeader);


    fetchData()
 
   
})
// Clean up the scroll event listener
onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('scroll', checkStickyHeader);

});

</script>
<style scoped>
.box {
    position: relative;
}

.box-header {
    position: sticky; 
    top: 0;        
    z-index: 10;   
    background-color: white; 
}

.box-body {
    max-height: 83vh; 
    overflow-y: auto; 
    padding: 10px;   
}
.box-header-sticky {
    position: sticky;
    top: 0;
    z-index: 10; 
    background-color: white;
}
</style>


