@extends('layouts.admin.admin')

@section('content')
    <script>
        window.alertsFromSystem = {!! $alerts !!};
        window.users = {!! $users !!};
        window.roles = {!! $roles !!};
        window.roles = {!! $roles !!};
        window.alertRecipientTypes = {!! $alertRecipientTypes !!};
    </script>

    <section class="content" id="alerts-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Alerts Setup </h3>
            </div>

            <div class="box-body" v-cloak>
                <p v-if="alerts.length === 0"> No alerts have been configured. </p>

                <div class="table-responsive" v-else>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Alert</th>
                            <th>Recipient Type</th>
                            <th>Recipients</th>
                            <th>Send SMS Notification</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-for="(alert, index) in alerts" :key="index">
                            <td>
                                <input type="text" class="form-control" :value="alert.label" disabled>
                            </td>

                            <td>
                                <select class="form-control" :id="`recipient-type-${index}`" v-model="alert.recipient_type">
                                    <option :value="type.value" v-for="type in alertRecipientTypes" :key="type.value"> @{{ type.label }}</option>
                                </select>
                            </td>

                            <td >
                             
                            <select class="form-control users-recipient" :id="`users-recipient-${index}`" multiple v-model="selectedUsers">
                                 <option :value="user.id" v-for="user in users" :key="user.id">
                                 @{{ user.name }}</option>
                            </select>

                                <select class="form-control" :id="`roles-recipient-${index}`" multiple style="display: none;">
                                    <option :value="role.id" v-for="role in roles" :key="role.id"> @{{ role.title }}</option>
                                </select>
                            </td>

                            <td>
                                <select class="form-control" :id="`sms-notification-${index}`">
                                    <option :value="value" v-for="value in ['Yes', 'No']" :key="value"> @{{ value }}</option>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary" @click="updateAlerts"> Update</button>
                    </div>
                </div>
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

    <div id="loader-on" style="position: fixed;top: 0;text-align: center;z-index: 999999;width: 100%;height: 100%;background: #000000b8;display:none;">
        <div class="loader" id="loader-1"></div>
    </div>

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
                    alerts: []
                }
            },

            computed: {
                toaster() {
                    return new Form();
                },

                users() {
                    return window.users
                },

                roles() {
                    return window.roles
                },

                alertRecipientTypes() {
                    return window.alertRecipientTypes
                },
            },

            mounted() {
                this.alerts = JSON.parse(JSON.stringify(window.alertsFromSystem))
                console.log(this.alerts)


                setTimeout(() => {
                    this.alerts.forEach((alert, index) => {
                        this.selectedUsers = alert.user_recipients || [];
                        $(`#recipient-type-${index}`).select2()
                        $(`#sms-notification-${index}`).select2()
                        $(`#users-recipient-${index}`).select2()
                        $(`#users-recipient-${index}`).val(alert.user_recipients)

                        if (alert.recipient_type === 'role') {
                            $(`#roles-recipient-${index}`).select2()
                            $(`#users-recipient-${index}`).select2('destroy')
                            $(`#users-recipient-${index}`).css('display', 'none')
                            $(`#roles-recipient-${index}`).css('display', 'block')

                            $(`#roles-recipient-${index}`).val(alert.role_recipients)

                            setTimeout(() => {
                                $(`#roles-recipient-${index}`).select2()
                            }, 50)
                        }

                        $(`#users-recipient-${index}`).val(this.selectedUsers).trigger('change');


                        $(`#recipient-type-${index}`).change(() => {
                            let type = $(`#recipient-type-${index}`).val();
                            this.alerts[index].recipient_type = type


                            $(`#users-recipient-${index}`).val([])
                            $(`#roles-recipient-${index}`).val([])

                            if (type === 'user') {
                                $(`#roles-recipient-${index}`).select2('destroy')
                                $(`#roles-recipient-${index}`).css('display', 'none')
                                $(`#users-recipient-${index}`).css('display', 'block')

                                setTimeout(() => {
                                    $(`#users-recipient-${index}`).select2()
                                }, 100)
                            } else {
                                $(`#users-recipient-${index}`).select2('destroy')
                                $(`#users-recipient-${index}`).css('display', 'none')
                                $(`#roles-recipient-${index}`).css('display', 'block')

                                setTimeout(() => {
                                    $(`#roles-recipient-${index}`).select2()
                                }, 100)
                            }
                        })

                        $(`#users-recipient-${index}`).change(() => {
                            this.alerts[index].user_recipients = $(`#users-recipient-${index}`).val()
                        })

                        $(`#roles-recipient-${index}`).change(() => {
                            this.alerts[index].role_recipients = $(`#roles-recipient-${index}`).val()
                        })

                        $(`#sms-notification-${index}`).change(() => {
                            this.alerts[index].sms_notification = $(`#sms-notification-${index}`).val()
                        })
                    })
                }, 100)
            },

            methods: {
                updateAlerts() {
                    let payload = {
                        alerts: this.alerts,
                    }

                    $("#loader-on").show();
                    axios.post('/api/alerts/update', {payload: JSON.stringify(payload)}).then(response => {
                        $("#loader-on").hide();
                        this.toaster.successMessage('Alerts updated successfully')
                    }).catch((error) => {
                        $("#loader-on").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data)
                    })
                },
            },
        })

        app.mount('#alerts-page')
    </script>
@endsection


