@extends('layouts.admin.admin')

@section('content')
    <section class="content" id="app">
        <div class="box box-primary" v-cloak>
            <div class="box-header with-border">
                <h3 class="box-title"> Employee Drafts </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th>Name</th>
                            <th>ID No.</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                            <th style="width: 10%" v-if="canViewActions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(employeeDraft, index) in employeeDrafts" :key="employeeDraft.id">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ employeeDraft.full_name }}</td>
                            <td>@{{ employeeDraft.id_no }}</td>
                            <td>@{{ employeeDraft.date_of_birth }}</td>
                            <td>@{{ employeeDraft.gender.name }}</td>
                            <td style="text-align: center" v-if="canViewActions">
                                <div class="action-button-div">                                         
                                    <a :href="`/admin/hr/management/employees/create?id=${employeeDraft.id}`">
                                        <i class="fa fa-arrow-right" title='Open'></i>
                                    </a>                                           
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </section>
@endsection

@section('uniquepagescript')
    <script type="importmap">
        {
            "imports": {
                "vue": "{{ config('app.env') == 'local' ?  asset('js/vue.esm-browser.min.js') : asset('js/vue.esm-browser.prod.min.js') }}"
            }
        }
    </script>

    <script type="module">
        import { createApp, onMounted, ref, computed, watch } from 'vue';

        createApp({
            setup() {
                const user = {!! $user !!}
                const employeeDrafts = {!! $employeeDrafts !!}
                
                const permissions = Object.keys(user.permissions)

                const canViewActions = computed(() => {
                    return permissions.includes('hr-management-employees___create') || user.role_id == '1'
                })

                onMounted(async () => {
                    
                    $('table').DataTable({
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'ordering': true,
                        'info': true,
                        'autoWidth': false,
                        'pageLength': 25,
                        'initComplete': function (settings, json) {
                            let info = this.api().page.info();
                            let total_record = info.recordsTotal;
                            if (total_record < 26) {
                                $('.dataTables_paginate').hide();
                            }
                        },
                        'aoColumnDefs': [{
                            'bSortable': false,
                            'aTargets': 'noneedtoshort'
                        }],
                    });

                })

                return {
                    user,
                    employeeDrafts,
                    canViewActions,
                }
            }
        }).mount('#app')
    </script>
@endsection
