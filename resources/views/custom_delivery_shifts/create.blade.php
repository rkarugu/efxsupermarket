@extends('layouts.admin.admin')

@section('content')
    <script>
        window.vehicles = {!! $vehicles !!};
        window.shiftTypes = {!! $shiftTypes !!};
        window.branches = {!! $branches !!};
        window.user = {!! Auth::user() !!};
    </script>

    <section class="content" id="create-custom-delivery-shift-page">
        <div class="box box-primary" v-cloak>
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Create Custom Delivery Shift </h3>
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
                            <label for="shift_date" class="control-label col-md-2 required"> Shift Date </label>
                            <div class="col-md-9">
                                <input type="date" id="shift_date" v-model="customShiftDetails.shift_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="vehicle-id" class="control-label col-md-2 required"> Vehicle </label>
                            <div class="col-md-9">
                                <select name="vehicle_id" id="vehicle-id" class="form-control mlselect" v-model="customShiftDetails.vehicle_id">
                                    <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id"> @{{ vehicle.license_plate_number }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="shift_types" class="control-label col-md-2 required"> Shift Type (s) </label>
                            <div class="col-md-9">
                                <select id="shift_types" class="form-control mlselect" v-model="customShiftDetails.shift_types" multiple>
                                    <option v-for="(type, index) in shiftTypes" :key="index" :value="type"> @{{ type }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="manual_documents" class="control-label col-md-2"> Document Numbers </label>
                            <div class="col-md-9">
                                <input type="text" id="manual_documents" v-model="customShiftDetails.document_numbers" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="tonnage" class="control-label col-md-2"> Tonnage </label>
                            <div class="col-md-9">
                                <input type="text" id="tonnage" v-model="customShiftDetails.tonnage" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="fueling_branch_id" class="control-label col-md-2 required"> Fueling Branch </label>
                            <div class="col-md-9">
                                <select id="fueling_branch_id" class="form-control mlselect" v-model="customShiftDetails.fueling_branch_id">
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id"> @{{ branch.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="shift_status" class="control-label col-md-2"> Shift Status </label>
                            <div class="col-md-9">
                                <select id="shift_status" class="form-control mlselect" v-model="customShiftDetails.shift_status">
                                    <option v-for="(status, index) in statuses" :key="index" :value="status"> @{{ status }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" v-if="(customShiftDetails.shift_status === 'In Progress') || (customShiftDetails.shift_status === 'Completed')">
                        <div class="row">
                            <label for="shift_start_time" class="control-label col-md-2"> Start Time </label>
                            <div class="col-md-9">
                                <input type="datetime-local" id="shift_start_time" v-model="customShiftDetails.shift_start_time" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group" v-if="(customShiftDetails.shift_status === 'Completed')">
                        <div class="row">
                            <label for="shift_end_time" class="control-label col-md-2"> Completion Time </label>
                            <div class="col-md-9">
                                <input type="datetime-local" id="shift_end_time" v-model="customShiftDetails.shift_end_time" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                       <div class="row">
                           <div class="col-md-2"></div>
                           <div class="d-flex justify-content-end col-md-9">
                               <button class="btn btn-primary" @click="saveRequest"> Submit </button>
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
                return {
                    customShiftDetails: {},
                    statuses: ['Pending', 'In Progress', 'Completed']
                }
            },

            created() {

            },

            mounted() {
                $(".mlselect").select2();

                $("#shift_status").change(() => {
                    this.customShiftDetails.shift_status = $("#shift_status").val();
                })
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

                vehicles() {
                    return window.vehicles
                },

                shiftTypes() {
                    return window.shiftTypes
                },
            },

            methods: {
                saveRequest() {
                    if (!this.customShiftDetails.shift_date || !this.customShiftDetails.vehicle_id) {
                        this.toaster.error('Please fill all required fields');
                    }

                    axios.post('{{ route("$base_route.store") }}', this.customShiftDetails)
                       .then(response => {
                            if (response.data.success) {
                                this.toaster.success(response.data.message);
                                window.location.href = response.data.redirect_url;
                            } else {
                                this.toaster.error(response.data.message);
                            }
                        })
                       .catch(error => {
                            console.log(error);
                            this.toaster.error('Something went wrong. Please try again.');
                        });
                },
            },
        })

        app.mount('#create-custom-delivery-shift-page')
    </script>
@endsection