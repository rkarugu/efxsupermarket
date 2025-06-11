@extends('layouts.admin.admin')

@section('content')
    <script>
        window.routes = {!! $routes !!}
    </script>

    <section class="content" id="create-schedule-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Create Geomapping Schedule </h3>
                    <a href="{{ route("geomapping-schedules.index") }}" class="btn btn-success"> Back </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route("geomapping-schedules.store") }}" method="post" novalidate id="form">
                    {{ @csrf_field() }}
                    {{-- @if ($logged_user_info->role_id == 1) --}}

                    <div class="col-md-4 form-group">
                        <label for="date" class="control-label"> Branch </label>

                        <select name="branch" id="branch" class="mlselect form-control">
                            <option value="" selected disabled>Select branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{$branch->id}}" {{ $branch->id == 10 ? 'selected' : '' }} required>{{$branch->name}}</option>

                            @endforeach
                        </select>

                    </div>
                {{-- @endif --}}
              

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
                            <div class="form-group col-md-2 custom-column">
                                <label for="route"> Route </label>
                                <input type="text" class="form-control" :id="`route-${index}`" v-model="row.route_name" name="route[]"  disabled>
                                <input type="hidden" class="form-control" :id="`route-id-${index}`" v-model="row.route_id" name="route_id[]">
                            </div>

                            <div class="form-group col-md-2 custom-column">
                                <label for="salesman"> Sales Rep </label>
                                <input type="text" class="form-control" :id="`salesman-${index}`" v-model="row.salesman_name" disabled>
                            </div>
                            <div class="form-group col-md-2 custom-column">
                                <label for="supervisor"> Supervisor </label>
                                <input type="text" class="form-control" name="supervisor[]" v-model="row.supervisor" required>
                            </div>
                            <div class="form-group col-md-1 custom-column">
                                <label for="supervisor_contact"> Supervisor Contact</label>
                                <input type="text" class="form-control" name="supervisor_contact[]" v-model="row.supervisor_contact" required>
                            </div>
                            <div class="form-group col-md-1 custom-column">
                                <label for="supervisor"> Route Manager </label>
                                <input type="text" class="form-control" name="supervisor2[]" v-model="row.supervisor2" >
                            </div>
                            <div class="form-group col-md-1 custom-column">
                                <label for="supervisor_contact"> RM Contact</label>
                                <input type="text" class="form-control" name="supervisor_contact2[]" v-model="row.supervisor_contact2" >
                            </div>

                            {{-- <div class="form-group col-md-1 custom-column">
                                <label for="bizwiz_rep"> BW REP </label>
                                <select v-model="row.bizwiz_rep" name="bizwiz_rep[]"  class="form-control visit">
                                    <option value="Isabella" selected>Isabella</option>
                                    <option value="Peter" selected>Peter</option>
                                    <option value="Gideon" selected>Gideon</option>
                                    <option value="Roy" selected>Roy</option>
                                    <option value="Mercy" selected>Mercy</option>
                                    <option value="Elly" selected>Elly</option>
                                    <option value="Patrick" selected>Patrick</option>
                                    <option value="Kiarie" selected>Kiarie</option>
                                </select>
                            </div> --}}


                            <div class="form-group col-md-1 custom-column">
                                <label for="Ga-rep"> GA Rep </label>
                                <input type="text" class="form-control" v-model="row.ga_rep" name="Ga_rep[]">
                            </div>

                            <div class="form-group col-md-1 custom-column">
                                <label for="Ga_rep_contact"> GA Rep Contact </label>
                                <input type="text" class="form-control"  v-model="row.ga_rep_contact" name="Ga_rep_contact[]">
                            </div>
                            <div class="form-group col-md-1 ">
                                <label>&nbsp;</label>
                              
                                <div class="action-button-div" @click="removeRow(index)" style="margin-top: 17px !important; padding-top:17px !important;">
                                    <a href="#" title="Detailed Report"><i class="fas fa-trash fa-lg text-primary" style="color:red;"></i></a>
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
<style scoped>
    .custom-column {
        padding-left: 0px !important;
        padding-right: 0px !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    </style>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
         $(document).ready(function () {
            $('body').addClass('sidebar-collapse');
        });
         $(function () {
            $(".mlselect").select2();
        });
    </script>
    <script type="importmap">
        {
          "imports": {
            "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
          }
        }
    </script>
    <script type="module">
        import { createApp } from 'vue';
        
        const app = createApp({
            data() {
                return {
                    rows: [],
                }
            },
        
            mounted() {
                $("#date").change(this.updateRows);
                $("#branch").change(this.updateRows);
            },
        
            methods: {
                updateRows() {
                    this.rows = [];
                    let date = $("#date").val();
                    let branchId = $("#branch").val();
                    let selectedDate = new Date(date);
                    let dayOfWeek = selectedDate.getDay();
                    let todayRoutes = this.routes.filter(route => {
                        return route.order_taking_dates.includes(String(dayOfWeek)) && route.restaurant_id == branchId;
                    });
        
                    todayRoutes.forEach(route => {
                        this.rows.push({
                            route_name: route.route_name,
                            route_id: route.id,
                            salesman_name: route.salesman ? `${route.salesman.name} (${route.salesman.phone_number})` : 'MISSING',
                            supervisor: '',
                            supervisor_contact: '',
                            // bizwiz_rep: '',
                            ga_rep: '',
                            ga_rep_contact: ''
                        });
                    });
        
                    this.$nextTick(() => {
                        $(".visit").select2();
                    });
                },
        
                validateForm() {
                    for (let row of this.rows) {
                        if (!row.supervisor || !row.supervisor_contact) {
                            return false;
                        }
                    }
                    return true;
                },

                submit(e) {
                    e.preventDefault();
                    if (this.validateForm()) {
                        $("#form").submit();
                    } else {
                        alert('Please fill in all supervisors and their contacts required fields.');
                    }
                },
                
                removeRow(index) {
                    this.rows.splice(index, 1);
                },
            },
        
            computed: {
                currentUser() {
                    return window.user;
                },
        
                routes() {
                    return window.routes;
                },
        
                toaster() {
                    return new Form();
                },
            },
        });
        
        app.mount('#create-schedule-page');
        </script>
        
@endsection



