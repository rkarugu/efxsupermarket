@extends('layouts.admin.admin')

@section('content')
    <script>
        window.routes = {!! $routes !!}
    </script>

    <section class="content" id="create-schedule-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Create Field Visit Schedule </h3>
                    <a href="{{ route("$base_route.index") }}" class="btn btn-primary"> Back </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route("$base_route.store") }}" method="post" novalidate id="form">
                    {{ @csrf_field() }}

                    <div class="row zero">
                        <div class="form-group col-md-4">
                            <label for="date" class="control-label"> Visit Date </label>
                            <input type="date" class="form-control" id="date" name="date">
                        </div>
                    </div>

                    <div class="box-header with-border">
                        <h3 class="box-title" style="font-weight: 800;"> Schedule </h3>
                    </div>

                    <p v-if="rows.length === 0" v-cloak> No routes added </p>

                    <div id="rows" v-else v-cloak>
                        <div class="schedule-row row zero" v-for="(row, index) in rows" :key="index">
                            <div class="form-group col-md-2">
                                <label for="route"> Route </label>
                                <input type="text" class="form-control" :id="`route-${index}`" name="route[]" disabled>
                                <input type="hidden" class="form-control" :id="`route-id-${index}`" name="route_id[]">
                            </div>

                            <div class="form-group col-md-2">
                                <label for="salesman"> Sales Rep </label>
                                <input type="text" class="form-control" :id="`salesman-${index}`" disabled>
                            </div>

                            <div class="form-group col-md-1">
                                <label for="hq-rep"> Visit </label>
                                <select name="visit[]" class="form-control visit">
                                    <option value="yes">Yes</option>
                                    <option value="no" selected>No</option>
                                </select>
                            </div>


                            <div class="form-group col-md-1">
                                <label for="hq-rep"> HQ Rep </label>
                                <input type="text" class="form-control" name="hq_rep[]">
                            </div>

                            <div class="form-group col-md-2">
                                <label for="salesman"> HQ Rep Contact </label>
                                <input type="text" class="form-control" name="hq_rep_contact[]">
                            </div>

                            <div class="form-group col-md-2">
                                <label for="salesman"> Bizwiz Rep </label>
                                <input type="text" class="form-control" name="bw_rep[]">
                            </div>

                            <div class="form-group col-md-2">
                                <label for="salesman"> Bizwiz Rep Contact </label>
                                <div class="d-flex align-items-center">
                                    <input type="text" class="form-control" name="bw_rep_contact[]">
                                    <!-- <a href="javascript:void(0);" style="font-size: 25px; margin-left: 12px;" @click="removeRow(index)"><i class="fas fa-trash-alt text-danger"></i></a> -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end" style="margin-top: 20px;">
                        <button class="btn btn-primary" @click="submit" :disabled="rows.length === 0">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="importmap">
        {
          "imports": {
            "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
          }
        }
    </script>
    <script type="module">
        import {createApp} from 'vue';

        const app = createApp({
            data() {
                return {
                    rows: [],
                }
            },

            mounted() {
                $("#date").change(() => {
                    this.rows = []
                    let date = $("#date").val()
                    let todayRoutes = this.routes.filter(route => route.order_taking_dates.includes(date))
                    todayRoutes.forEach((route, index) => {
                        this.rows.push({})
                    })

                    setTimeout(() => {
                        this.rows.forEach((row, index) => {
                            let route = todayRoutes[index]
                            $(`#route-${index}`).val(route.route_name)
                            $(`#route-id-${index}`).val(route.id)
                            if (route.salesman) {
                                $(`#salesman-${index}`).val(`${route.salesman.name} (${route.salesman.phone_number})`)
                            } else {
                                $(`#salesman-${index}`).val('MISSING')
                            }
                        })

                        $(".visit").select2()
                    }, 100)
                })
            },

            computed: {
                currentUser() {
                    return window.user
                },

                routes() {
                    return window.routes
                },

                toaster() {
                    return new Form();
                },
            },

            methods: {
                submit(e) {
                    e.preventDefault();

                    $("#form").submit();
                },

                removeRow(index) {
                    console.log('Asiiii')
                    console.log(index)
                    this.rows.splice(index, 1)
                },
            },
        })

        app.mount('#create-schedule-page')
    </script>
@endsection
