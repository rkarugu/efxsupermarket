<template>
    <div>
      <div v-for="type in pettyCashRequestTypes" :key="type.slug">
        <h4>{{ type.name }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                    <th>#</th>
                    <th v-for="header in headers(type.slug)" :key="header" :style="{ textAlign: header === 'Amount' ? 'right' : 'left' }">{{ header }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                    v-for="(item, index) in otherExpensesData[type.slug]"
                    :key="index"
                    :style="{
                        fontWeight: index === otherExpensesData[type.slug].length - 1 ? 'bold' : '',
                        borderTop: index === otherExpensesData[type.slug].length - 1 ? '2px solid #000' : ''
                    }"
                    >
                    <td>{{ index === otherExpensesData[type.slug].length - 1 ? '' : index + 1 }}</td>
                    <td v-for="(value, i) in item" :key="i" :style="{ textAlign: i === item.length - 1 ? 'right' : 'left' }">
                        {{ value }}
                    </td>
                    </tr>
                </tbody>
            </table>

        </div>
       
      </div>
      
      <!-- Grand total -->
       <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <tbody>
            <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                <th colspan="6">Expenses Total</th>
                <th style="text-align: right;">{{ manageAmountFormat(grandTotal) }}</th>
            </tr>
            </tbody>
      </table>

       </div>
     
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    props: {
      branchId: {
        type: String,
        required: true
      },
      startDate: {
        type: String,
        required: true
      }
    },
    data() {
      return {
        pettyCashRequestTypes: [],
        pettyCashData: [],
        otherExpensesData: {},
        grandTotal: 0
      };
    },
    created() {
      this.fetchPettyCashRequestTypes();
      this.fetchPettyCashData();
    },
    watch: {
    branchId(newBranchId, oldBranchId) {
      if (newBranchId !== oldBranchId) {
        this.fetchPettyCashData();
      }
    },
    startDate(newStartDate, oldStartDate) {
      if (newStartDate !== oldStartDate) {
        this.fetchPettyCashData();
      }
    }
  },
    methods: {
      async fetchPettyCashRequestTypes() {
        const response = await axios.get('/api/chairman-general-dashboard-petty-cash-request-types');
        this.pettyCashRequestTypes = response.data;
      },
      async fetchPettyCashData() {
        const response = await axios.get(`/api/chairman-general-dashboard-petty-cash-data/${this.branchId}/${this.startDate}`);
        this.pettyCashData = response.data;
        this.processPettyCashData();
      },
      processPettyCashData() {
        this.grandTotal = 0;
        const groupedData = {};
  
        this.pettyCashRequestTypes.forEach(type => {
          groupedData[type.slug] = [];
          let total = 0;
  
          this.pettyCashData.forEach(item => {
            if (item.petty_cash_request?.type == type.slug) {
              let rowData = [];
  
              switch (type.slug) {
                case 'parking-fees':
                  rowData = [
                    item.route?.route_name,
                    item.payee_name,
                    item.payment_reason,
                    item.deliverySchedule?.vehicle?.license_plate_number,
                    item.employee?.name,
                    this.manageAmountFormat(item.amount)
                  ];
                  break;
                case 'driver-grn':
                  rowData = [
                    item.grn_number || item.transfer?.transfer_no,
                    item.payee_name,
                    item.payment_reason,
                    item.grn_number ? item.grn?.supplier?.name : item.transfer?.fromStoreDetail?.location_name,
                    this.manageAmountFormat(item.amount)
                  ];
                  break;
                case 'staff-welfare':
                case 'supplier-cash-payments':
                case 'repairs-maintenance-buildings':
                case 'repairs-maintenance-motor-vehicle':
                  rowData = [
                    item.payee_name,
                    item.payment_reason,
                    this.manageAmountFormat(item.amount)
                  ];
                  break;
              }
  
              groupedData[type.slug].push(rowData);
              total += item.amount;
              this.grandTotal += item.amount;
            }
          });
          const columnCount = groupedData[type.slug].length > 0 ? groupedData[type.slug][0].length : 0;

        // Construct totals row based on the number of columns
        let totalRow = [];
        switch (columnCount) {
        case 3:
            totalRow = ['', 'Total', this.manageAmountFormat(total)];
            break;
        case 4:
            totalRow = ['', 'Total', '', this.manageAmountFormat(total)];
            break;
        case 6:
            totalRow = ['', 'Total', '', '', '', this.manageAmountFormat(total)];
            break;
        case 5:
        totalRow = ['', 'Total', '', '', this.manageAmountFormat(total)];
        break;
        default:
            totalRow =  ['', 'Total', this.manageAmountFormat(total)]; // Handles any number of columns
            break;
        }
        groupedData[type.slug].push(totalRow);


          
  
        //   groupedData[type.slug].push(['', 'Total', '', '', '', this.manageAmountFormat(total)]);
        });
  
        this.otherExpensesData = groupedData;
      },
  

    manageAmountFormat(amount) {
        return new Intl.NumberFormat().format(amount);
      },
      headers(slug) {
        switch (slug) {
          case 'parking-fees':
            return ['Route', 'Payee', 'Payment Reason', 'Vehicle', 'Driver', 'Amount'];
          case 'driver-grn':
            return ['Document No', 'Payee', 'Payment Reason', 'Source', 'Amount'];
          case 'staff-welfare':
          case 'supplier-cash-payments':
          case 'repairs-maintenance-buildings':
          case 'repairs-maintenance-motor-vehicle':
            return ['Payee', 'Payment Reason', 'Amount'];
          default:
            return [];
        }
      }
    }
  };
  </script>
  