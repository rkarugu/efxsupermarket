@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp
    <script>
        window.user = {!! $user !!}
            window.loaders = {!! $loaders !!}
            window.routes = {!! $routes !!}
    </script>

    <section class="content" id="teams-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Add Team </h3>

                    <a href="{{ route("$base_route.index") }}" class="btn btn-primary"> Back </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route("$base_route.store") }}" method="post" class="form-horizontal">
                    @csrf

                    <div class="form-group">
                        <label for="branch_id" class="col-sm-2 control-label">Branch</label>
                        <div class="col-sm-10">
                            @if ($user->restaurant_id != 1)
                            <select name="branch_id" id="branch_id" class="form-control" required>                                   
                                    <option value="{{ $user->restaurant_id }}"> {{ getRestaurantNameById($user->restaurant_id) }} </option>
                            </select>
                                
                            @else

                            <select name="branch_id" id="branch_id" class="form-control" required>
                                @foreach($branches as $branch)
                                    <option value="" selected disabled></option>
                                    <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                @endforeach
                            </select>
                                
                            @endif
                         
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="team_name" class="control-label col-md-2"> Team Name </label>
                            <div class="col-md-10">
                                <input type="text" name="team_name" id="team_name" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="team_leader_id" class="control-label col-md-2"> Team Leader </label>
                            <div class="col-md-10">
                                <select name="team_leader_id" id="team_leader_id" class="form-control" required>
                                    <option value="" selected disabled> Select a team leader</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}"> {{ $user->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="team_leader_id" class="control-label col-md-2"> Team Members </label>
                            <div class="col-md-10">
                                <select name="team_member_id[]" id="team_member_id" class="form-control" multiple>
                                    <option v-for="loader in filteredLoaders" :key="loader.id" :value="loader.id">@{{ loader.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="team_route_id" class="control-label col-md-2"> Team Routes </label>
                            <div class="col-md-10">
                                <select name="team_route_id[]" id="team_route_id" class="form-control" multiple>
                                    <option v-for="route in filteredRoutes" :key="route.id" :value="route.id">@{{ route.route_name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-end">
                            <button class="btn-primary btn" type="submit"> Submit</button>
                        </div>
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
                    loaders: [],
                    filteredLoaders: [],
                    routes:[],
                    filteredRoutes: []
                }
            },

            created() {

            },

            mounted() {
                $("#branch_id").select2()
                $("#team_leader_id").select2();
                $("#team_member_id").select2();
                $("#team_route_id").select2();

                this.loaders = window.loaders
                this.filteredLoaders = this.loaders
                this.routes = window.routes
                this.filteredRoutes = this.routes

                $("#branch_id").change(() => {
                    let selectedBranchId = parseInt($("#branch_id").val());
                    this.filteredLoaders = this.loaders.filter(loader => loader.restaurant_id === selectedBranchId)
                });
                $("#team_route_id").change(() =>{
                    let selectedRouteId = parseInt($("#team_route_id").val());
                    this.filteredRoutes = this.routes.filter(route => route.id === selectedRouteId)
                })
            },

            computed: {
                currentUser() {
                    return window.user
                },

                toaster() {
                    return new Form();
                },
            },
        })

        app.mount('#teams-page')
    </script>
@endsection
