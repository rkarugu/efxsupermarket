@extends('layouts.admin.admin')

@section('content')
    <div id="app" v-cloak>
        <section class="content" style="padding-bottom: 0">
            <div class="box box-primary" style="margin-bottom: 10px">
                <div class="box-header with-border">
                    <h3 class="box-title">Petty Cash Types</h3>
                    <div style="text-align: right" v-if="(permissions.includes('petty-cash-request-types___create') || user.role_id == '1')">
                        <button class="btn btn-primary" @click="showAddModal">Add Petty Cash Type</button>
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th>Petty Cash Type</th>
                                <th>Expense Account</th>
                                <th>No. of Assigned Users</th>
                                <th style="width: 10%" v-if="canViewActions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(type, index) in types" :key="type.id">
                                <td>@{{ index + 1 }}</td>
                                <td>@{{ type.name }}</td>
                                <td>@{{ type.chart_of_account?.account_name }}</td>
                                <td>@{{ type.users_count }}</td>
                                <td style="text-align: center" v-if="canViewActions">
                                    <div class="action-button-div">
                                        <a href="#" @click.prevent="showEditModal(type); assigningUsers = false" v-if="(permissions.includes('petty-cash-request-types___edit') || user.role_id == '1')">
                                            <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                                        </a>
                                        <a href="#" @click.prevent="showEditModal(type); ; assigningUsers = true ; assigningAccount = false" v-if="canAssignUsers">
                                            <i class="fa fa-users fa-lg text-primary" title="Assign Users"></i>
                                        </a>
                                        <a href="#" @click.prevent="showEditModal(type); ; assigningUsers = false ; assigningAccount = true" v-if="canAssignAccount">
                                            <i class="fa fa-credit-card-alt fa-lg text-primary" title="Assign Expense Account"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <div class="modal fade" id="type-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" v-if="!assigningUsers">@{{ action }} Petty Cash Type</h4>
                        <h4 class="modal-title" v-else>Assign Users</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="type-name">Name</label>
                            <input type="text" class="form-control" id="type-name" placeholder="Enter name" :disabled="assigningUsers" v-model="form.name">
                        </div>
                        <div class="form-group" v-show="canAssignUsers && assigningUsers">
                            <label for="assigned-users">Assign To Users</label>
                            <select id="assigned-users-select" class="form-control" multiple v-model="form.users" :onchange="assignedUsersChanged">
                                <option :value="user.id" v-for="user in users">@{{ user.name }}</option>
                            </select>
                        </div>
                        <div class="form-group" v-show="canAssignAccount && assigningAccount">
                            <label for="assigned-account">Assign To Account</label>
                            <select id="assigned-account-select" class="form-control" v-model="form.wa_charts_of_account_id" :onchange="assignedAccountChanged">
                                <option :value="account.id" v-for="account in expenseAccounts">@{{ account.account_name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm" v-if="!assigningUsers">@{{ action }} Petty Cash Type</button>
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm" v-else>Assign Users</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script type="importmap">
        {
            "imports": {
                "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
            }
        }
    </script>

    <script>

        axios.defaults.baseURL = '/api'

        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response && error.response.status === 401) {
                    window.location = '/'
                }
                return Promise.reject(error);
            }
        );
        
    </script>

    <script type="module">
        import { createApp, onMounted, computed, ref } from 'vue';

        createApp({
            setup() {
                const formUtil = new Form()
                
                const user = {!! $user !!}
                const permissions = Object.keys(user.permissions)

                const canViewActions = computed(() => {
                    return permissions.includes('petty-cash-request-types___edit') || 
                        permissions.includes('petty-cash-request-types___assign-users') || 
                        permissions.includes('petty-cash-request-types___assign-account') ||
                        user.role_id == '1'
                })

                const canAssignUsers = computed(() => {
                    return permissions.includes('petty-cash-request-types___assign-users') || 
                        user.role_id == '1'
                })

                const canAssignAccount = computed(() => {
                    return permissions.includes('petty-cash-request-types___assign-account') || 
                        user.role_id == '1'
                })

                const form = ref({
                    id: '',
                    name: '',
                    users: [],
                    wa_charts_of_account_id: ''
                })

                const action = ref('Add')

                const assigningUsers = ref(false)
                const assigningAccount = ref(false)

                const clearForm = () => {
                    form.value.name = ''
                    form.value.users = []
                    form.value.wa_charts_of_account_id = ''

                    $('#assigned-users-select').val([]).trigger('change');
                    $('#assigned-account-select').val('').trigger('change');
                }

                const showAddModal = () => {
                    assigningUsers.value = false
                    assigningAccount.value = false
                    
                    action.value = 'Add'

                    clearForm()

                    $('#type-modal').modal('show')
                }
                
                const showEditModal = (type) => {                    
                    form.value.id = type.id
                    form.value.name = type.name
                    form.value.users = type.users.map(user => user.id)
                    form.value.wa_charts_of_account_id = type.wa_charts_of_account_id

                    $('#assigned-users-select').val(type.users.map(user => user.id)).trigger('change');
                    $('#assigned-account-select').val(type.wa_charts_of_account_id).trigger('change');

                    action.value = 'Edit'
                    
                    $('#type-modal').modal('show')
                }

                const assignedUsersChanged = (event) => {
                    form.value.users = $(event.target).val()
                }

                const assignedAccountChanged = (event) => {
                    form.value.wa_charts_of_account_id = $(event.target).val()
                }

                const processing = ref(false)
                
                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage("Enter name")
                        return
                    }

                    if (assigningUsers.value) {
                        if (!form.value.users.length) {
                            formUtil.errorMessage("Select at least one user")
                            return
                        }
                    }

                    if (assigningAccount.value) {
                        if (!form.value.wa_charts_of_account_id) {
                            formUtil.errorMessage("Select an account")
                            return
                        }
                    }

                    processing.value = true

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = 'petty-cash-request-types'
                    } else if (action.value == 'Edit') {
                        uri = `petty-cash-request-types/${form.value.id}`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchTypes()

                            formUtil.successMessage(response.data.message)

                            $('#type-modal').modal('hide')
                        
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                            processing.value = false
                        })
                }

                const users = ref([])
                
                const types = ref([])

                const fetchData = (uri, refVariable) => {
                    axios.get(uri)
                        .then(response => refVariable.value = response.data.data ?? response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))
                }

                const fetchTypes = () => {
                    fetchData('petty-cash-request-types', types)
                }

                const expenseAccounts = ref([])
                onMounted(() => {
                    fetchTypes()

                    // Fetch users
                    fetchData('users', users)

                    // Fetch expense accounts
                    fetchData('expense-accounts', expenseAccounts)

                    $('select').select2({
                        placeholder: 'Select...',
                    });

                })
                
                return {
                    user,
                    permissions,
                    canViewActions,
                    canAssignUsers,
                    canAssignAccount,
                    form,
                    action,
                    showAddModal,
                    showEditModal,
                    processing,
                    submitForm,
                    types,
                    users,
                    assignedUsersChanged,
                    assigningUsers,
                    assignedAccountChanged,
                    assigningAccount,
                    expenseAccounts
                }
            }
        }).mount('#app')
    </script>
@endsection

