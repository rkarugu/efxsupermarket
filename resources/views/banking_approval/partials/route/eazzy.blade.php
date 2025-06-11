<div class="box-body">
    <div>

        <ul class="nav nav-tabs" id="data-tabs">
            <li class="active"><a href="#eazzy-verified" data-toggle="tab"> Verified </a></li>
            <li><a href="#eazzy-unknown" data-toggle="tab"> Unknown </a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="eazzy-verified" v-cloak>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="eazzy-verified-table">
                            <thead>
                                <tr>
                                    <th style="width: 3%;"> # </th>
                                    <th> Time </th>
                                    <th> Route </th>
                                    <th> Reference </th>
                                    <th> Document Number </th>
                                    <th style="text-align: right;"> Amount </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(record, index) in eazzyVerified" :key="index"
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
                                    <th style="text-align: right;"> @{{ eazzyVerifiedTotal }} </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="eazzy-unknown" v-cloak>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="eazzy-unknown-table">
                            <thead>
                                <tr>
                                    <th style="width: 3%;"> # </th>
                                    <th> Time </th>
                                    <th> Reference </th>
                                    <th style="text-align: right;"> Amount </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(record, index) in eazzyUnknown" :key="index"
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
                                    <th style="text-align: right;"> @{{ eazzyUnknownTotal }} </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>