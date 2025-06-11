@extends('layouts.admin.admin')

@section('content')
    <section class="content" id="departmental-performace-app">
        <template v-if="!loader">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="box-header-flex">
                        <h3 class="box-title"> Departmental Performance by Month for Current Year </h3>
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th v-for="(month, index) in months" :key="index">@{{ month.name }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(category, index) in categories" :key="index">
                                <tr>
                                    <td>@{{category }}</td>
                                    <td v-for="(month, index) in months" :key="index">@{{ numberWithCommas(getSaleData(category, month.number)) }}</td>
                                </tr>
                            </template>
                            <tr>
                                <th>Total (KES)</th>
                                <th v-for="(month, index) in months" :key="index">@{{ numberWithCommas(getMonthlySales(month.number)) }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-body" style="height: 500px">
                    <div id="sales-chart"></div>
                </div>
            </div>
        </template>

        <div class="box box-primary" v-if="loader">
            <div style="padding-block: 30px">
                <div class="loader" id="loader-1"></div>
            </div>
        </div>

    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        window.user = {!! $user !!}
    </script>

    <script type="importmap">
        {
            "imports": {
                "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
            }
        }
    </script>

    <script type="module">
        import {createApp, computed, onMounted, ref } from 'vue';

        createApp({
            setup() {
                const user = window.user
                const formUtil = new Form()

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

                const categories = computed(() => {
                    return salesData.value.map(saleData => Object.keys(saleData)[0])
                })

                const getSaleData = (category, monthNumber) => {
                    let categoryData = salesData.value.find(saleData => Object.keys(saleData)[0] == category);

                    let monthsData = categoryData[category];

                    let data = null;

                    monthsData.forEach(monthData => {
                        let keys = Object.keys(monthData);
                        let index = keys.findIndex(key => key === monthNumber.toString());

                        if (index !== -1) {
                            data = monthData[monthNumber];
                        }
                    });

                    return data ?? 0;
                }

                const getMonthlySales = (monthNumber) => {
                    let total = 0;

                    salesData.value.forEach(saleData => {
                        total += getSaleData(Object.keys(saleData)[0], monthNumber)
                    })

                    return total
                }

                const loader = ref(true)
                onMounted(async () => {
                    try {
                        let response = await axios.get(`/api/supplier-overview-sales/${user.id}`)
                        
                        salesData.value = response.data
    
                        let options = {
                            series: salesData.value.map(saleData => {
    
                                let category = Object.keys(saleData)[0]
    
                                let categoryData = salesData.value.find(saleData => Object.keys(saleData)[0] == category)[category]
                                
                                return {
                                    name: category,
                                    data: months.map(month => {
                                        let monthData = categoryData.find(monthData => Object.keys(monthData)[0] == month.number)
                                        
                                        if (monthData) {
                                            return monthData[month.number]
                                        } else {
                                            return 0
                                        }
                                    })
                                }
                            }),
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
                            // title: {
                            //     text: 'Departmental Performance by Month for Current Year',
                            //     align: 'left'
                            // },
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
    
                        loader.value = false

                        setTimeout(() => {
                            let chart = new ApexCharts(document.querySelector("#sales-chart"), options);
                            chart.render();
                        }, 100);

                    } catch (error) {
                        formUtil.errorMessage(error.response.data.message)
                    }
                })

                return {
                    numberWithCommas,
                    dayjs,
                    user,
                    categories,
                    months,
                    salesData,
                    getSaleData,
                    getMonthlySales,
                    loader
                }
            }
        }).mount('#departmental-performace-app')
    </script>
@endpush
