@extends('layouts.admin.admin')

@php
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;
@endphp

<script>
    window.users = @json($users);
    window.accounts = @json($accounts);
    var myPermissions = @json($my_permissions);
    var loggedUserInfo = @json($logged_user_info);
</script>

@section('content')
    <div id="app" v-cloak>
        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title">Users</h3>
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['assign-account-view___add']))
                            <button type="button" class="btn btn-danger btn-sm" style="margin-top: 25px"
                                @click.prevent="openModal()">
                                <i class="fa fa-plus"></i> Create Account User
                            </button>
                        @endif

                    </div>
                </div>
                <div class="box-body">
                    <div class="col-md-12 no-padding-h">
                        <table class="table table-bordered table-hover" id="item-moves-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>No of Accounts</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(user, index) in users" :key="user.id">
                                    <td>@{{ index + 1 }}</td>
                                    <td>@{{ user.name }}</td>
                                    <td>@{{ user.usergeneralledgeraccounts_count }}</td>
                                    <td>
                                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['assign-account-view___edit']))
                                            <i class="fa fa-edit"
                                                @click.prevent="openModal(user, user.usergeneralledgeraccounts)"
                                                style="color: #155CA2;cursor: pointer;"></i>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="createAccountUserModal" role="dialog" aria-labelledby="createAccountUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document" style="height: 1000px !important">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createAccountUserModalLabel">@{{ editmode ? 'Edit Account User' : 'Create Account User' }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="modal-body" style="height: auto">
                            <div class="row">

                                {{-- <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="userSelect">User</label>
                                        <select class="form-control" id="userSelect" v-model="form.userId">
                                            <option v-for="user in users" :key="user.id" :value="user.id">
                                                @{{ user.name }}</option>
                                        </select>
                                    </div>
                                </div> --}}

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="userSelect">User</label>
                                        <select class="form-control" id="userSelect" v-model="form.userId"
                                            :disabled="editmode">
                                            <option v-for="user in users" :key="user.id" :value="user.id">
                                                @{{ user.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>


                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="accountSelect">Accounts</label>
                                        <select id="accountSelect" class="form-control" multiple v-model="form.accountIds">
                                            <option v-for="account in filteredAccounts" :key="account.id"
                                                :value="account.id">@{{ account.account_name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="margin-top: 100px">
                            <button type="submit" class="btn btn-primary" id="show-create-button" v-if="!editmode">Save
                                changes</button>
                            <button type="submit" class="btn btn-primary" id="show-edit-button"
                                v-if="editmode">Update</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .checkbox-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            max-height: 200px;
            overflow-y: auto;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
        }

        .checkbox-item input {
            margin-right: 5px;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('select').select2({
                placeholder: 'Select...',
            });

            $('table select').select2({
                placeholder: '',
            });
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
        import {
            createApp,
            ref,
            computed,
            onMounted
        } from 'vue';

        createApp({
            setup() {
                const users = ref(window.users);
                const accounts = ref(window.accounts);
                const editmode = ref(false);
                const accountSearch = ref("");
                const formUtil = new Form()

                const form = ref({
                    userId: null,
                    accountIds: []
                });

                const openModal = (user, selectedAccounts) => {
                    if (user) {
                        console.log(user)
                        editmode.value = true;
                        form.value.userId = user.id;
                        form.value.accountIds = selectedAccounts.map(account => account.account_id);
                        setTimeout(() => {
                            $('#userSelect').val(user.id).trigger('change');
                            $('#accountSelect').val(form.value.accountIds).trigger('change');
                        }, 100);
                    } else {
                        editmode.value = false;
                        form.value.userId = null;
                        form.value.accountIds = [];
                        $('#userSelect').val(null).trigger('change');
                        $('#accountSelect').val(null).trigger('change');
                    }
                    $('#createAccountUserModal').modal('show');
                };

                const submitForm = () => {
                    const url = editmode.value ? `/admin/update-account-user/${form.value.userId}` :
                        '/admin/create-account-user';
                    axios.post(url, form.value)
                        .then(response => {
                            formUtil.successMessage('Accounts assigned to user successfully')
                            setTimeout(() => {
                                location.reload();
                            }, 3000);
                            $('#createAccountUserModal').modal('hide');
                        })
                        .catch(error => {
                            if (error.response && error.response.data && error.response.data.errors) {
                                const messages = [];
                                for (const key in error.response.data.errors) {
                                    if (error.response.data.errors.hasOwnProperty(key)) {
                                        messages.push(...error.response.data.errors[key]);
                                    }
                                }
                                formUtil.errorMessage(messages.join('<br>'));
                            } else {
                                formUtil.errorMessage('An unexpected error occurred.');
                            }
                        })
                        .finally(() => {
                            // $('#createAccountUserModal').modal('hide');
                        });
                };

                const filteredAccounts = computed(() => {
                    return accounts.value.filter(account => account.account_name.toLowerCase().includes(
                        accountSearch.value.toLowerCase()));
                });

                onMounted(() => {
                    $('#userSelect').select2({
                        placeholder: 'Select a user',
                        width: '100%',
                        allowClear: true
                    }).on('change', function() {
                        form.value.userId = $(this).val();
                    });

                    $('#accountSelect').select2({
                        placeholder: 'Select accounts',
                        width: '100%',
                        multiple: true,
                        allowClear: true
                    }).on('change', function() {
                        form.value.accountIds = $(this).val();
                    });

                    $('#createAccountUserModal').on('hidden.bs.modal', function() {
                        form.value.userId = null;
                        form.value.accountIds = [];
                        accountSearch.value = "";
                        // Reset Select2 values
                        $('#userSelect').val(null).trigger('change');
                        $('#accountSelect').val(null).trigger('change');
                    });
                    $('#item-moves-table').DataTable({
                        "paging": true,
                        "pageLength": 10,
                        "searching": true,
                        "lengthChange": true,
                        "lengthMenu": [10, 20, 50, 100],
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
                        "order": [
                            [0, "asc"]
                        ]
                    });
                });

                return {
                    users,
                    accounts,
                    editmode,
                    accountSearch,
                    form,
                    openModal,
                    submitForm,
                    filteredAccounts
                };
            }
        }).mount('#app');
    </script>
@endsection
