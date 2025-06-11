@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
     
    <section class="content" id="vue-mount">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Competing Brands Details -  {{$competingBrand->name}}</h3>
                    <div>
                        <a href="{{route('competing-brands.listing')}}" class="btn btn-success btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
            <div class="box-body">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('competing-brands.details', $competingBrand->id) }}" method="GET">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="branch_id" class="control-label"> Branch </label>
                                <select v-model="branch_filter" id="branch_id" class="form-control mlselect">
                                    <option value="0" selected>All</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="filter_type">Select Filter Type:</label>
                                <select v-model="filterType" id="filter_type" class="form-control filterType">
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                            <div v-if="filterType === 'monthly'" class="form-group col-md-3">
                                <label for="month_filter">Select Month:</label>
                                <select v-model="month_filter" id="month_filter" class="form-control mlselect2">
                                    <option v-for="month in months" :key="month.value" :value="month.value">
                                        @{{ month.label }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="filterType === 'quarterly'" class="form-group col-md-3">
                                <label for="quarter_filter">Select Quarter:</label>
                                <select v-model="quarter_filter" id="quarter_filter" class="form-control mlselect2">
                                    <option v-for="quarter in quarters" :key="quarter.value" :value="quarter.value">
                                        @{{ quarter.label }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="filterType === 'yearly'" class="form-group col-md-3">
                                <label for="year_filter">Select Year:</label>
                                <select v-model="year_filter" id="year_filter" class="form-control mlselect2">
                                    <option v-for="year in years" :key="year" :value="year">
                                        @{{ year }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group ">
                                <label for="" style="color: white;">filter</label>
                                <button @click.prevent="refresh" class="btn btn-success" style="margin-top:25px;"><i class="fas fa-filter"></i> Filter</button>

                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h5 class="box-title">Market Share</h5>  
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-12 no-padding-h ">
                    <div class="chart-container">
                        <div class="chart">
                            <div v-if="loading" class="loading-indicator">
                                Loading data, please wait...
                            </div>
                         
                            <canvas id="salesPieChart"></canvas>
                        </div>
                        <div class="legend ">
                            <h5><strong>Key : </strong></h5>
                            <ul id="legend-list" class="scrollable"></ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Stock Id Code </th>
                                <th>Title</th>
                                <th>Supplier</th>
                                <th>Last Lpo Date</th>
                                <th>Last Grn Date</th>
                                <th>Last Grn Qty</th>
                                <th>Current Qoh</th>
                                <th>Standard Cost</th>
                                <th>Selling Price</th>
                                <th>Margin</th>
                                <th>Qty Sold</th>
                                <th>Total Cost</th>
                                <th>Sales</th>
                                <th>Total Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(product, index) in tableData" :key="product.id">

                                {{-- <tr v-for="(product, index) in tableData" :key="product.id"> --}}
                                    <tr>
                                    <th>
                                        <span @click="toggleRow(index, product.id)" class="span-action">
                                            <i :class="{'fas fa-plus': !expandedRows.includes(index), 'fas fa-minus': expandedRows.includes(index)}"></i>
                                        </span>
                                        {{-- @{{ index + 1 }} --}}
                                    </th> 
                                    <td>@{{ product.stock_id_code }}</td>
                                    <td>@{{ product.title }}</td>
                                    <td>@{{ product.supplier }}</td>
                                    <td class="qty">@{{ product.last_lpo_date }}</td>
                                    <td class="qty">@{{ product.last_grn_date }}</td>
                                    <td class="qty">@{{ product.last_grn_qoh }}</td>
                                    <td class="qty">@{{ product.current_qoh }}</td>
                                    <td class="amount">@{{ product.standard_cost }}</td>
                                    <td class="amount">@{{ product.selling_price}}</td>
                                    <td class="amount">@{{ product.margin.toFixed(2)}}</td>
                                    <td class="qty">@{{ product.qty_sold}}</td>
                                    <td class="amount">@{{ numberWithCommas(product.cost.toFixed(2))}}</td>
                                    <td class="amount">@{{ numberWithCommas(product.sales.toFixed(2)) }}</td>
                                    <td class="amount">@{{ numberWithCommas(product.computed_margin.toFixed(2)) }}</td>
                                </tr>
                                <tr v-if="expandedRows.includes(index)">
                                    <td colspan="15">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Month</th>
                                                    <th>Quantity Sold</th>
                                                    <th>Total Cost</th>
                                                    <th>Total Sales</th>
                                                    <th>Margin</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="detail in expandedDetails[index]" :key="detail.id">
                                                    <td >@{{ detail.month_year }}</td>
                                                    <td class="qty">@{{ detail.qty_sold}}</td>
                                                    <td class="amount">@{{ numberWithCommas(detail.cost.toFixed(2)) }}</td>
                                                    <td class="amount">@{{ numberWithCommas(detail.sales.toFixed(2)) }}</td>
                                                    <td class="amount">@{{ numberWithCommas(detail.computed_margin.toFixed(2)) }}</td>
                                                    
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                            </template>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="12">Total</th>
                                <th class="amount" id="totalCost" v-cloak>@{{ total_cost }}</th>
                                <th class="amount" id="totalSales" v-cloak>@{{ total_sales }}</th>
                                <th class="amount" id="totalSales" v-cloak>@{{ total_computed_margin }}</th>
                            </tr>
                        </tfoot>
                       </table>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
<link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
<style>
    .loading-indicator {
    text-align: center;
    padding: 20px;
    font-size: 18px;
    color: #333;
}

.loading-indicator::before {
    content: '⏳';
    display: block;
    font-size: 24px;
    margin-bottom: 10px;
}

    .chart-container {
        display: flex;
        width: 100%;
        height: 400px;
        margin-bottom: 15px;  
    }

    .chart {
        flex: 3; 
    }

    .legend {
        flex: 1; 
        padding-left: 20px;
        overflow-y: auto;  
        padding: 10px;
        box-sizing: border-box; 
        /* border: 1px solid #ccc; */
    }

    .legend h5 {
        margin-bottom: 10px;
    }

    .legend ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .legend li {
        margin-bottom: 5px;
        display: flex;
        align-items: center;
    }

    .legend span {
        display: inline-block;
        width: 20px; /* Size of the color box */
        height: 20px;
        margin-right: 10px;
    }
</style>

<style >
    .select2 {
        width: 100% !important;
    }
    .amount{
        text-align: right;
    }
    .qty{
        text-align: center;
    }
</style>
@endsection
@section('uniquepagescript')
   
<script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="importmap">
    {
      "imports": {
        "vue": "/js/vue.esm-browser.js"
      }
    }
</script>
<script type="module">
        import {createApp} from 'vue';

    const app = createApp({
        data() {
            return {
                branch_filter:'0',
                date_filter: '7', 
                salesData: [],   
                tableData: [],
                total_sales: 0,  
                total_cost:0,
                total_computed_margin:0,
                filterType: 'monthly', 
                month_filter: '',
                quarter_filter: '',
                year_filter: new Date().getFullYear(), 
                months: [], 
                quarters: [], 
                years: [],  
                expandedRows: [], 
                expandedDetails: {},
                index:null,
                loading: false,
            }
        },
        mounted() {
            $(".mlselect").select2().on('change', (event) => {
                this.branch_filter = event.target.value;
            });
            $(".filterType").select2().on('change', (event) => {
                this.filterType = event.target.value;

            });
            this.initializeSelect2();
            this.initializeFilters();
            if (this.filterType === 'monthly') {
                this.month_filter = new Date().getMonth() + 1;
            }
            this.fetchData();

        },
        watch: {
            filterType() {
                this.$nextTick(() => {
                    this.initializeSelect2();  
                });
            }
        },
       
        methods: {
            toggleRow(index, rowId) {
                this.index = index;
            if (this.expandedRows.includes(index)) {
                this.expandedRows = this.expandedRows.filter(i => i !== index);
            } else {
                this.expandedRows.push(index);

                if (!this.expandedDetails[index]) {
                    this.fetchRowDetails(index, rowId);
                }
            }
        },
        fetchRowDetails(index, rowId) {
            const dateFilter = this.getDateFilter();
            const branchFilter = this.branch_filter;
            const start = dateFilter.startDate;
            const end = dateFilter.endDate;
            const brandId = "{{ $competingBrand->id }}";

            $.ajax({
                url: `/admin/competing-brands/${rowId}/table-data-details`,
                method: 'GET',
                data: {
                    start: start,
                    end: end,
                    branch_filter: branchFilter
                },
                success: (response) => {
                    this.expandedDetails = { ...this.expandedDetails, [index]: response.data };

                },
                error: (error) => {
                    console.error('Error fetching row details:', error);
                }
            });
        },
            initializeSelect2() {

                $(".mlselect2").select2().on('change', (event) => {
                    const target = event.target.id;
                    if (target === 'month_filter') {
                        this.month_filter = event.target.value;
                    } else if (target === 'quarter_filter') {
                        this.quarter_filter = event.target.value;
                    } else if (target === 'year_filter') {
                        this.year_filter = event.target.value;
                    }
                });
            },
            handleFilterTypeChange() {
                    $(".mlselect2").select2('destroy');
                    this.initializeSelect2();
               
            },
            refresh() {
                this.expandedRows = [];
                this.expandedDetails = {};
                this.loading = true;
                    this.fetchData();
                    
                },
            initializeFilters() {
                const monthLabels = [
                    'January', 'February', 'March', 'April', 'May', 'June', 
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                this.months = monthLabels.map((label, index) => ({
                    label: label,
                    value: index + 1
                }));

                this.quarters = [
                    { label: 'Q1 (Jan - Mar)', value: 'Q1' },
                    { label: 'Q2 (Apr - Jun)', value: 'Q2' },
                    { label: 'Q3 (Jul - Sep)', value: 'Q3' },
                    { label: 'Q4 (Oct - Dec)', value: 'Q4' }
                ];

                const currentYear = new Date().getFullYear();
                this.years = Array.from({ length: 11 }, (_, i) => currentYear - 5 + i);
            },
            getDateFilter() {
                const currentYear = new Date().getFullYear();
                let startDate, endDate;
                console.log(this.filterType);

                switch (this.filterType) {
                    case 'monthly':
                        const currentMonth = new Date().getMonth() + 1; 
                        const selectedMonth = this.month_filter ? this.month_filter : currentMonth;
                        const month = String(selectedMonth).padStart(2, '0');
                        startDate = `${currentYear}-${month}-01`;
                        endDate = `${currentYear}-${month}-${new Date(currentYear, this.month_filter, 0).getDate()}`;
                        break;
                    case 'quarterly':
                        const quarterStartMonth = (parseInt(this.quarter_filter.charAt(1)) - 1) * 3 + 1;
                        startDate = `${currentYear}-${String(quarterStartMonth).padStart(2, '0')}-01`;
                        const quarterEndMonth = quarterStartMonth + 2;
                        endDate = `${currentYear}-${String(quarterEndMonth).padStart(2, '0')}-${new Date(currentYear, quarterEndMonth, 0).getDate()}`;
                        break;
                    case 'yearly':
                        startDate = `${this.year_filter}-01-01`;
                        endDate = `${this.year_filter}-12-31`;
                        break;
                    default:
                        const today = new Date();
                        endDate = today.toISOString().split('T')[0]; 
                        const last30Days = new Date(today.setDate(today.getDate() - 30));
                        startDate = last30Days.toISOString().split('T')[0]; 
                }
                console.log(startDate, endDate);

                return { startDate, endDate };
            },

            fetchData() {
                // const dateFilter = this.date_filter;
                const dateFilter = this.getDateFilter();
                const start  = dateFilter.startDate;
                const end = dateFilter.endDate;
                const dateFilterParams = {
                    start_date: dateFilter.startDate,
                    end_date: dateFilter.endDate
                };
                const branchFilter = this.branch_filter;
                const brandId = "{{ $competingBrand->id }}"; 

                $.ajax({
                    url: `/admin/competing-brands/${brandId}/sales-data`,
                    method: 'GET',
                    data: { start: start,end:end, branch_filter: branchFilter},
                    success: (response) => {
                        this.loading = false;
                        this.salesData = response.data;
                        this.drawPieChart();
                    },
                    error: (error) => {
                        this.loading = false;
                        console.error('Error fetching sales data:', error);
                    }
                });

                $.ajax({
                    url: `/admin/competing-brands/${brandId}/table-data`,
                    method: 'GET',
                    data: { start: start,end:end, branch_filter: branchFilter },
                    success: (response) => {
                        this.loading = false;
                        this.tableData = response.data;
                        this.total_sales = response.totalSales;
                        this.total_cost = response.totalCost;
                        this.total_computed_margin = response.totalComputedMargin;
                        

                        
                    },
                    error: (error) => {
                        this.loading = false;
                        console.error('Error fetching table data:', error);
                    }
                });
            },
            drawPieChart() {
                const ctx = document.getElementById('salesPieChart').getContext('2d');

                const salesData = this.salesData.map(item => parseFloat(item.sales.replace(/,/g, '')) || 0);
                const labels = this.salesData.map(item => `${item.stock_id_code} - ${item.title}`);
                const totalSales = salesData.reduce((acc, value) => acc + value, 0);

                const backgroundColors = this.generateRandomColors(this.salesData.length);

                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }

                this.chartInstance = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: salesData,
                            backgroundColor: backgroundColors,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        const value = tooltipItem.raw;
                                            const total = tooltipItem.dataset.data.reduce((sum, item) => sum + item, 0);
                                            const percentage = ((value / total) * 100).toFixed(2);
                                        return `${tooltipItem.raw.toLocaleString('en-US', {style: 'currency', currency: 'KES'})} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            
                const legendList = document.getElementById('legend-list');
                legendList.innerHTML = '';  

                this.salesData.forEach((item, index) => {
                    const percentage = ((salesData[index] / totalSales) * 100).toFixed(2); 
                    const listItem = document.createElement('li');
                    listItem.innerHTML = `<span style="background-color: ${backgroundColors[index]}"></span>${item.stock_id_code} ${item.title} - ${percentage}%`;
                    legendList.appendChild(listItem);
                });
            },

            generateRandomColors(numColors) {
                const colors = [];
                for (let i = 0; i < numColors; i++) {
                    const hue = Math.floor((360 / numColors) * i);
                    const color = `hsl(${hue}, 100%, 50%)`; 
                    colors.push(color);
                }
                return colors;
            },
            toNumber(value) {
                const num = parseFloat(value);
                return isNaN(num) ? 0 : num;
            },
            numberWithCommas(value) {
                if (value) {
                    let parts = value.toString().split(".");
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    return parts.join(".");
                } else {
                    return "0";
                }
            }
        }
    });

    app.mount('#vue-mount');
</script>
   
   
@endsection
