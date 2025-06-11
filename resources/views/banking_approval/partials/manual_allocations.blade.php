<div class="box-body">
    <p v-if="loadingManualAllocations"> Loading Manual Allocations... </p>
    <div v-else>
        <div class="table-responsive">
            <table class="table table-bordered" id="manual_allocations_table">
                <thead>
                    <tr>
                        <th style="width: 3%;"> # </th>
                        <th> Sale No. </th>
                        <th> Cashier </th>
                        <th> Narration </th>
                        <th> Receipt No. </th>
                        <th> Channel </th>
                        <th> Reference </th>
                        <th style="text-align: right;"> Allocation Amount </th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr v-for="(record, index) in manualAllocations" :key="index" v-cloak>
                        <th scope="row" style="width: 3%;"> @{{ index + 1 }} </th>
                        <td> @{{ record.sales_no }} </td>
                        <td> @{{ record.cashier }} </td>
                        <td> @{{ record.comment }} </td>
                        <td> @{{ record.receipt_no }} </td>
                        <td> @{{ record.channel }} </td>
                        <td> @{{ record.reference }} </td>
                        <td style="text-align: right;"> @{{ record.formatted_amount }} </td>
                       
                    </tr>
                </tbody>
                <tfoot>
                    <tr v-cloak>
                        <th colspan="7"> TOTALS </th>
                        <th style="text-align: right;"> @{{ manualAllocationsTotal }} </th>
                    
                    </tr>
                </tfoot>
            </table>

        </div>
       
    </div>
</div>