<div class="box-body">
    <p v-if="loadingUnverified"> Loading data... </p>
    <div v-else>
        <div class="table-responsive">
            <table class="table table-bordered" id="unverified-table">
                <thead>
                    <tr>
                        <th style="width: 3%;"> # </th>
                        <th> Entry Time </th>
                        <th> Channel </th>
                        <th> Reference </th>
                        <th> Sale Number </th>
                        <th> Cashier </th>
                        <th style="text-align: right;"> Amount </th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr v-for="(record, index) in unverified" :key="index" v-cloak>
                        <th scope="row" style="width: 3%;"> @{{ index + 1 }} </th>
                        <td> @{{ record.created_at }} </td>
                        <td> @{{ record.channel }} </td>
                        <td> @{{ record.reference }} </td>
                        <td> @{{ record.sales_no }} </td>
                        <td> @{{ record.cashier }} </td>
                        <td style="text-align: right;"> @{{ record.amount }} </td>
                    </tr>
                </tbody>
    
                <tfoot>
                    <tr v-cloak>
                        <th colspan="6"> TOTALS </th>
                        <th style="text-align: right;"> @{{ unverifiedTotal }} </th>
                    </tr>
                </tfoot>
            </table>

        </div>
       
    </div>
</div>