<div class="box-body">
    <p v-if="loadingMpesa"> Loading Mpesa... </p>
    <div v-else>
        <ul class="nav nav-tabs" id="data-tabs">
            <li class="active"><a href="#mpesa-verified" data-toggle="tab"> Verified </a></li>
            <li><a href="#mpesa-unknown" data-toggle="tab"> Unknown </a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="mpesa-verified" v-cloak>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="mpesa-verified-table">
                            <thead>
                                <tr>
                                    <th style="width: 3%;"> # </th>
                                    <th> Time </th>
                                    <th> Reference </th>
                                    <th> Sale Number </th>
                                    <th style="text-align: right;"> Amount </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(record, index) in mpesaVerified"
                                    :key="index" v-cloak>
                                    <th scope="row" style="width: 3%;"> @{{ index + 1 }}
                                    </th>
                                    <td> @{{ record.created_at }} </td>
                                    <td> @{{ record.reference }} </td>
                                    <td> @{{ record.sales_no }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_amount }} </td>
                                </tr>
                            </tbody>

                            <tfoot>
                                <tr v-cloak>
                                    <th colspan="4"> TOTALS </th>
                                    <th style="text-align: right;"> @{{ mpesaVerifiedTotal }} </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="mpesa-unknown" v-cloak>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="mpesa-unknown-table">
                            <thead>
                                <tr>
                                    <th style="width: 3%;"> # </th>
                                    <th> Time </th>
                                    <th> Reference </th>
                                    <th style="text-align: right;"> Amount </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(record, index) in mpesaUnknown" :key="index"
                                    v-cloak>
                                    <th scope="row" style="width: 3%;"> @{{ index + 1 }}
                                    </th>
                                    <td> @{{ record.bank_date }} </td>
                                    <td> @{{ record.reference }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_amount }} </td>
                                </tr>
                            </tbody>

                            <tfoot>
                                <tr v-cloak>
                                    <th colspan="3"> TOTALS </th>
                                    <th style="text-align: right;"> @{{ mpesaUnknownTotal }} </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>