@extends('layouts.admin.admin')

@section('content')
    @php

 @endphp
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Create Fuel Purchase Order </h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary"> Back </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route("$base_route.store") }}" method="post" class="form-horizontal">
                    {{ @csrf_field() }}

                    <div class="form-group">
                        <div class="row">
                            <label for="branch-id" class="control-label col-md-2"> Branch </label>
                            <div class="col-md-9">
                                <select name="branch_id" id="branch-id" class="form-control mlselect">
                                    <option value="" disabled selected> Select a branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                    @endforeach
                                </select>
                            </div>
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
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
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
                return {}
            },

            created() {

            },

            mounted() {
                $(".mlselect").select2();
            },

            computed: {
                currentUser() {
                    return window.user
                },

                branches() {
                    return window.branches
                },

                toaster() {
                    return new Form();
                },
            },

            methods: {},
        })

        app.mount('#create-fuel-lpo-page')
    </script>
@endsection