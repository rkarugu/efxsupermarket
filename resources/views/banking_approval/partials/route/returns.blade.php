<div class="box-body">
    <p v-if="loadingReturns"> Loading Returns... </p>
    <div v-else>
        <div class="table-responsive">
            <table class="table table-bordered" id="returns-table">
                <thead>
                    <tr>
                        <th style="width: 3%;"> # </th>
                        <th> Date </th>
                        <th> Route </th>
                        <th> Return No. </th>
                        <th style="text-align: right;"> Amount </th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr v-for="(record, index) in returns" :key="index" v-cloak>
                        <th scope="row" style="width: 3%;"> @{{ index + 1 }} </th>
                        <td> @{{ record.return_date }} </td>
                        <td> @{{ record.route }} </td>
                        <td> @{{ record.document_no }} </td>
                        <td style="text-align: right;"> @{{ record.formatted_amount }} </td>
                    </tr>
                </tbody>
    
                <tfoot>
                    <tr v-cloak>
                        <th colspan="4"> TOTALS </th>
                        <th style="text-align: right;"> @{{ returnsTotal }} </th>
    
                    </tr>
                </tfoot>
            </table>

        </div>
      
    </div>
</div>