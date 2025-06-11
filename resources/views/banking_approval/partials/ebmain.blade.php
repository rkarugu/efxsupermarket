<div class="box-body">
    <p v-if="loadingEbMain"> Loading Equity Main... </p>
    <div v-else>
        <ul class="nav nav-tabs" id="data-tabs">
            <li class="active"><a href="#eb-main-verified" data-toggle="tab"> Verified </a></li>
            <li><a href="#eb-main-unknown" data-toggle="tab"> Unknown </a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="eb-main-verified" v-cloak>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="eb-main-verified-table">
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
                                <tr v-for="(record, index) in ebMainVerified"
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
                                    <th style="text-align: right;"> @{{ ebMainVerifiedTotal }} </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="eb-main-unknown" v-cloak>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="eb-main-unknown-table">
                            <thead>
                                <tr>
                                    <th style="width: 3%;"> # </th>
                                    <th> Time </th>
                                    <th> Reference </th>
                                    <th style="text-align: right;"> Amount </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(record, index) in ebMainUnknown" :key="index"
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
                                    <th style="text-align: right;"> @{{ ebMainUnknownTotal }} </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>