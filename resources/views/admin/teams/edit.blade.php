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


    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Edit Team </h3>

                    <a href="{{ route("$base_route.index") }}" class="btn btn-primary"> Back </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div id="teams-page">
                   

                    <div class="tab-content">
                        <form action="{{ route("$base_route.store") }}" method="post" class="form-horizontal">
                            @csrf
                            <div class="form-group">
                                <label for="branch_id" class="col-sm-2 control-label">Branch</label>
                                <div class="col-sm-10">
                                    <select name="branch_id" id="branch_id" class="form-control" required>
                                        <option value="{{$selectedBranch->id}}" selected disabled>{{$selectedBranch->name}}</option>

                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="details" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="team_name" class="control-label col-md-2"> Team Name </label>
                                        <div class="col-md-10">
                                            <input type="text" name="team_name" id="team_name" class="form-control" value="{{$team->team_name}}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="team_leader_id" class="control-label col-md-2"> Team Leader </label>
                                        <div class="col-md-10">
                                            <select name="team_leader_id" id="team_leader_id" class="form-control" required>
                                                <option value="{{$teamLeader->id}}" selected disabled> {{$teamLeader->name  }} </option>
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
                                                @foreach($loaders as $loader)
                                                    <option value="{{ $loader->id }}" {{ in_array($loader->id, $selectedLoaders ) == 1 ? 'selected' : '' }} > {{ $loader->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="team_leader_id" class="control-label col-md-2"> Team Routes </label>
                                        <div class="col-md-10">
                                            <select name="team_route_id[]" id="team_route_id" class="form-control" multiple>
                                                @foreach($routes as $route)
                                                    <option value="{{ $route->id }}"> {{ $route->route_name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="box-footer">
                                    <div class="d-flex justify-content-end">
                                        <button class="btn-primary btn" type="submit"> Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="https://cdn.jsdelivr.net/npm/smartwizard@6/dist/css/smart_wizard_all.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
    <style>
        .tab-title {
            font-weight: 600;
            font-size: 20px;
            margin-bottom: 15px;
        }

        #teams-page .tab-content {
            /*min-height: 600px !important;*/
            overflow-y: auto !important;
        }

        :root {
            --sw-anchor-active-primary-color: #ff0000;
            --sw-progress-color: #ff0000;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="https://cdn.jsdelivr.net/npm/smartwizard@6/dist/js/jquery.smartWizard.min.js" type="text/javascript"></script>
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
                    filteredLoaders: []
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

                $("#branch_id").change(() => {
                    let selectedBranchId = parseInt($("#branch_id").val());
                    this.filteredLoaders = this.loaders.filter(loader => loader.restaurant_id === selectedBranchId)
                });
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
