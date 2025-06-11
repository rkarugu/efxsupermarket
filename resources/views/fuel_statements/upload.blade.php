@php use Carbon\Carbon; @endphp
@extends('layouts.admin.admin')

@section('content')
    <script>
        window.branches = {!! $branches !!};
    </script>

    <section class="content" id="vue-mount">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Upload Fuel Statements </h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left btn-icon"></i> Back
                    </a>
                </div>
            </div>

            <div class="box-body">
                <form @submit.prevent="processUpload" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-3">
                                <label for="branch_id" class="control-label"> Branch </label>
                                <select id="branch_id" class="form-control mlselect" required>
                                    <option value="" disabled selected>Select a branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="upload_file" class="control-label"> Fuel Statement </label>
                                <input type="file" id="upload_file" class="form-control" required @change="onUploadFileChanged($event)">
                            </div>

                            <div class="form-group col-md-2">
                                <label class="control-label">&nbsp; </label>
                                <div class="d-flex">
                                    <button class="btn btn-primary" @click="processUpload"><i class="fas fa-file-arrow-up btn-icon"></i> Upload & Preview</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div v-if="processedUpload">
                    <hr>

                    <table class="table table-hover table-bordered" id="preview-table">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Receipt #</th>
                            <th>Fueled Quantity</th>
                            <th>Description</th>
                            <th style="text-align: right;">Fuel Total</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-for="(record, index) in processedUpload" :key="index">
                            <th style="width: 3%;" scope="row"> @{{ index + 1 }}</th>
                            <td> @{{ record.timestamp }}</td>
                            <td> @{{ record.branch_name }}</td>
                            <td> @{{ record.receipt_number }}</td>
                            <td> @{{ record.quantity }}</td>
                            <td> @{{ record.narrative }}</td>
                            <td style="text-align: right;"> @{{ record.total }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary" @click="saveStatements"><i class="fas fa-circle-check"></i> Confirm Statements</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <span class="btn-loader" style="display:none;">
        <img src="<?= asset('/assets/admin/images/loader.gif') ?>" alt="Loader"/>
    </span>
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
                    uploadedFile: null,
                    processedUpload: null
                }
            },

            mounted() {
                $(".mlselect").select2();
            },

            computed: {
                branches() {
                    return window.branches
                },

                toaster() {
                    return new Form();
                },
            },

            methods: {
                onUploadFileChanged(e) {
                    const target = e.target;
                    if (target && target.files) {
                        this.uploadedFile = target.files[0];
                    }
                },

                processUpload(e) {
                    e?.preventDefault();
                    if (!this.uploadedFile) {
                        return this.toaster.errorMessage("Please select a file to upload.");
                    }

                    let selectedBranchId = $('#branch_id').find(":selected").val();
                    if (!selectedBranchId) {
                        return this.toaster.errorMessage("Please select a branch");
                    }

                    let payload = new FormData();
                    payload.append('upload_file', this.uploadedFile);
                    payload.append('branch_id', selectedBranchId);
                    payload.append('_token', '{{ csrf_token() }}');

                    $(".btn-loader").show();
                    axios.post('{{ route("fuel-statements.upload") }}', payload, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(response => {
                        $(".btn-loader").hide();
                        this.processedUpload = response.data;

                        setTimeout(() => {
                            this.initDataTable();
                        }, 2000);
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                initDataTable() {
                    $('#preview-table').DataTable({
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'ordering': true,
                        'info': true,
                        'autoWidth': false,
                        'pageLength': 100,
                        'initComplete': function (settings, json) {
                            let info = this.api().page.info();
                            let total_record = info.recordsTotal;
                            if (total_record < 101) {
                                $('.dataTables_paginate').hide();
                            }
                        },
                        'aoColumnDefs': [{
                            'bSortable': false,
                            'aTargets': 'noneedtoshort'
                        }],
                    });
                },

                saveStatements() {
                    $(".btn-loader").show();
                    axios.post('{{ route("fuel-statements.save") }}', {data: JSON.stringify(this.processedUpload)}).then(response => {
                        $(".btn-loader").hide();
                        this.processedUpload = null;
                        this.toaster.successMessage('Statements saved successfully.');
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },
            },
        })

        app.mount('#vue-mount')
    </script>
@endsection