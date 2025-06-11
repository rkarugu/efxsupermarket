<div class="box-body">
    <p v-if="loadingSales"> Loading sales... </p>
    <div v-else>
        <div class="table-responsive">
            <table class="table table-bordered" id="sales-table">
                <thead>
                    <tr>
                        <th style="width: 3%;"> # </th>
                        <th> Sale Time </th>
                        <th> Sale Number </th>
                        <th> Route </th>
                        <th style="text-align: right;"> Amount </th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr v-for="(record, index) in sales" :key="index" v-cloak>
                        <th scope="row" style="width: 3%;"> @{{ index + 1 }} </th>
                        <td> @{{ record.created_at }} </td>
                        <td> @{{ record.sales_no }} </td>
                        <td> @{{ record.route }} </td>
                        <td style="text-align: right;"> @{{ record.formatted_amount }} </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr v-cloak>
                        <th colspan="4"> TOTALS </th>
                        <th style="text-align: right;"> @{{ salesGrossTotal }} </th>
               
                    </tr>
                </tfoot>
            </table>

        </div>
       
    </div>
</div>