<div class="box-body">
    <p v-if="loadingVooma"> Loading Vooma... </p>
    <div v-else>
        <ul class="nav nav-tabs" id="data-tabs">
            <li class="active"><a href="#vooma-verified" data-toggle="tab"> Verified </a></li>
            <li><a href="#vooma-unknown" data-toggle="tab"> Unknown </a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="vooma-verified" v-cloak>
                <div class="box-body">
                    <div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="vooma-verified-table">
                                <thead>
                                    <tr>
                                        <th style="width: 3%;"> # </th>
                                        <th> Time </th>
                                        <th> Route </th>
                                        <th> Reference </th>
                                        <th> Sale Number </th>
                                        <th style="text-align: right;"> Amount </th>
                                    </tr>
                                </thead>
    
                                <tbody>
                                    <tr v-for="(record, index) in voomaVerified" :key="index"
                                        v-cloak>
                                        <th scope="row" style="width: 3%;"> @{{ index + 1 }}
                                        </th>
                                        <td> @{{ record.created_at }} </td>
                                        <td> @{{ record.route }} </td>
                                        <td> @{{ record.reference }} </td>
                                        <td> @{{ record.document_no }} </td>
                                        <td style="text-align: right;"> @{{ record.formatted_amount }} </td>
                                    </tr>
                                </tbody>
    
                                <tfoot>
                                    <tr v-cloak>
                                        <th colspan="5"> TOTALS </th>
                                        <th style="text-align: right;"> @{{ voomaVerifiedTotal }} </th>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                    
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="vooma-unknown" v-cloak>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="vooma-unknown-table">
                            <thead>
                                <tr>
                                    <th style="width: 3%;"> # </th>
                                    <th> Time </th>
                                    <th> Reference </th>
                                    <th style="text-align: right;"> Amount </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(record, index) in voomaUnknown" :key="index"
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
                                    <th style="text-align: right;"> @{{ voomaUnknownTotal }} </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>