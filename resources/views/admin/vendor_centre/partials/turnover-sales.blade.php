@php
    $user = getLoggeduserProfile();
@endphp

<div style="padding: 10px;" id="turnover-sales-app">
    <div class="row">
        <div class="col-md-6">
            <table class="table table-bordered" v-cloak>
                <thead>
                    <tr>
                        <th></th>
                        <th v-for="(year, index) in years" :key="index">@{{ year }}</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="month in months" :key="month.number">
                        <tr>
                            <td>@{{ month.name }}</td>
                            
                            <td v-for="(year, index) in years" :key="index">KES @{{ numberWithCommas(getSaleData(year, month.number)) }}</td>
                        </tr>
                    </template>

                    <tr>
                        <th :colspan="years.length">Total</th>
                        <th v-for="(year, index) in years" :key="index">KES @{{ getYearSales(year) }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6" id="sales-chart"></div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        window.user = {!! $user !!}
        window.supplier_id = {!! $supplier->id !!}
    </script>

    <script type="module">
        import {createApp, computed, onMounted, ref } from 'vue';

        createApp({
            setup() {
                const user = window.user
                const supplier_id = window.supplier_id
                const formUtil = new Form()

                const numberWithCommas = (value) => {
                    if (value) {
                        let parts = value.toString().split(".");
                        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        return parts.join(".");
                    } else {
                        return "0";
                    }
                }

                const months = [
                    { name: "January", number: '01' },
                    { name: "February", number: '02' },
                    { name: "March", number: '03' },
                    { name: "April", number: '04' },
                    { name: "May", number: '05' },
                    { name: "June", number: '06' },
                    { name: "July", number: '07' },
                    { name: "August", number: '08' },
                    { name: "September", number: '09' },
                    { name: "October", number: '10' },
                    { name: "November", number: '11' },
                    { name: "December", number: '12' }
                ];
                
                const salesData = ref([])

                const years = computed(() => {
                    return salesData.value.map(saleData => Object.keys(saleData)[0])
                })

                const getSaleData = (year, monthNumber) => {
                    let yearData = salesData.value.find(saleData => Object.keys(saleData)[0] == year);

                    let monthsData = yearData[year];

                    let data = null;

                    monthsData.forEach(monthData => {
                        let keys = Object.keys(monthData);
                        let index = keys.findIndex(key => key === monthNumber.toString());

                        if (index !== -1) {
                            data = monthData[monthNumber];
                        }
                    });

                    return data;
                }

                const getYearSales = (year) => {
                    let monthsData = salesData.value.find(saleData => Object.keys(saleData)[0] == year)[year]
                    
                    let total = 0;

                    monthsData.forEach(monthData => {
                        for (let key in monthData) {
                            total += monthData[key];
                        }
                    });

                    return numberWithCommas(total)
                }

                onMounted(() => {
                    $('body').addClass('sidebar-collapse');

                    axios.get(`/api/turnover-sales/${supplier_id}`)
                        .then(response => {
                            salesData.value = response.data

                            const currentYear = new Date().getFullYear();
                            let options = {
                                series: [{
                                    name: "Sales",
                                    data: months.map(month => month.number)
                                        .map(monthNumber => {
                                            let sales = 0
                                            
                                            salesData.value[0][currentYear].forEach(monthData => {
                                                let key = Object.keys(monthData)[0];

                                                if (key == monthNumber) {
                                                    sales = monthData[key]
                                                }
                                            })

                                            return sales
                                        })
                                }],
                                chart: {
                                    height: '100%',
                                    type: 'line',
                                    zoom: {
                                        enabled: false
                                    }
                                },
                                dataLabels: {
                                    enabled: false
                                },
                                stroke: {
                                    curve: 'straight'
                                },
                                title: {
                                    text: 'Turnover Sales by Month for Current Year',
                                    align: 'left'
                                },
                                grid: {
                                    row: {
                                        colors: ['#f3f3f3', 'transparent'],
                                        opacity: 0.5
                                    },
                                },
                                xaxis: {
                                    categories: months.map(month => month.name),
                                },

                                yaxis: {
                                    labels: {
                                        formatter: function (val) {
                                            return numberWithCommas(val)
                                        }
                                    }
                                },

                                tooltip: {
                                    y: {
                                        formatter: function (val) {
                                            return `KES ${numberWithCommas(val)}`
                                        },
                                    },
                                },
                            };

                            let chart = new ApexCharts(document.querySelector("#sales-chart"), options);
                            chart.render();
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                        })
                })

                return {
                    numberWithCommas,
                    dayjs,
                    user,
                    years,
                    months,
                    salesData,
                    getSaleData,
                    getYearSales 
                }
            }
        }).mount('#turnover-sales-app')
    </script>
@endpush

