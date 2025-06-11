@php use Carbon\Carbon; @endphp
@extends('layouts.admin.admin')

@section('content')
    <script>
        window.customer = {!! $customer !!};
        window.user = {!! $user !!};
    </script>


    <section class="content" id="vue-mount">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Settle Account From Fraud - {{ $customer->customer_name }} </h3>

                    <a href="{{ url()->previous() }}" class="btn btn-primary"> <i class="fas fa-arrow-left btn-icon"></i> Back
                    </a>
                </div>
            </div>

            <div class="box-body">
                <form method="post" class="form-horizontal" @submit.prevent="promptSettleAccount">
                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-2 control-label"> Current Balance </label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" value="{{ $customer->balance }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-2 control-label"> Amount To Settle </label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" v-model="transaction.amount" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-2 control-label"> Comments </label>
                            <div class="col-md-8">
                                <textarea class="form-control" v-model="transaction.comment" required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="d-flex justify-content-end col-md-10">
                                <button class="btn btn-primary" @click="promptSettleAccount"> <i class="fas fa-check-double btn-icon"></i> POST </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="confirm-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Confirm Fraud Posting </h3>
                    </div>

                    <div class="box-body">
                        Are you sure you want to post a sum of @{{ parseFloat(transaction.amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') }} to fraud? 
                        This action will credit {{ $customer->customer_name }}'s account with the set amount.
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" @click="settleAccount"> <i class="fas fa-thumbs-up btn-icon"></i> Yes, I'm Sure </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <span class="btn-loader" style="display:none;">
        <img src="<?= asset('/assets/admin/images/loader.gif') ?>" alt="Loader" />
    </span>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/dayjs.min.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>

    <script>
        dayjs().format()
    </script>

    <script type="importmap">
        {
          "imports": {
            "vue": "/js/vue.esm-browser.js"
          }
        }
    </script>

    <script type="module">
        import {
            createApp
        } from 'vue';

        const app = createApp({
            data() {
                return {
                    transaction: {}
                }
            },

            mounted() {
                // $("body").addClass('sidebar-collapse');

                // this.fetchRecords();
                // this.fetchVerifiedReceipts();
                // this.fetchFraudTrans();
                // this.fetchUnVerifiedReceipts();
                // this.fetchOtherReceivables();
            },

            computed: {
                customer() {
                    return window.customer
                },

                user() {
                    return window.user
                },

                toaster() {
                    return new Form();
                },
            },

            methods: {
                
                promptSettleAccount() {
                    if (!this.transaction.amount) {
                        return this.toaster.errorMessage('Settlement amount is required');
                    }

                    if (isNaN(parseFloat(this.transaction.amount))) {
                        return this.toaster.errorMessage('Settlement amount should be a valid amount');
                    }

                    if (!this.transaction.comment) {
                        return this.toaster.errorMessage('Settlement comments is required');
                    }

                    $("#confirm-modal").modal("show");
                },

                settleAccount() {
                    $("#confirm-modal").modal("hide");
                    $(".btn-loader").show();

                    let payload = {
                        customer_id: this.customer.id,
                        comment: this.transaction.comment,
                        amount: this.transaction.amount,
                        blamable: this.user.id
                    };

                    axios.post('/customer-accounts/settle-from-fraud', payload).then(response => {
                        $(".btn-loader").hide();
                        this.toaster.successMessage('Amount posted successfully.');
                        window.location.reload();
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
