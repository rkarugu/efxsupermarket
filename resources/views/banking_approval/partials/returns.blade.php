<div class="box-body">
    <p v-if="loadingReturns"> Loading Returns... </p>
    <div v-else>
        <div class="table-responsive">
            <table class="table table-bordered" id="returns-table">
                <thead>
                    <tr>
                        <th style="width: 3%;"> # </th>
                        <th> Initiated At </th>
                        <th> Return No. </th>
                        <th> Sale Date </th>
                        <th> Sale No. </th>
                        <th> Cashier </th>
                        <th> Returned By </th>
                        <th> Item Code </th>
                        <th> Item Title</th>
                        <th style="text-align: right;"> Selling Price </th>
                        <th style="text-align: right;"> Qty </th>
                        <th style="text-align: right;"> Amount </th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr v-for="(record, index) in returns" :key="index" v-cloak>
                        <th scope="row" style="width: 3%;"> @{{ index + 1 }} </th>
                        <td> @{{ record.return_date }} </td>
                        <td> @{{ record.return_grn }} </td>
                        <td> @{{ record.sale_date }} </td>
                        <td> @{{ record.sales_no }} </td>
                        <td> @{{ record.sale_cashier }} </td>
                        <td> @{{ record.return_by }} </td>
                        <td> @{{ record.stock_id_code }} </td>
                        <td> @{{ record.title }} </td>
                        <td style="text-align: right;"> @{{ record.selling_price }} </td>
                        <td style="text-align: right;"> @{{ record.return_quantity }} </td>
                        <td style="text-align: right;"> @{{ record.formated_amount }} </td>
                    </tr>
                </tbody>
    
                <tfoot>
                    <tr v-cloak>
                        <th colspan="11"> TOTALS </th>
                        <th style="text-align: right;"> @{{ returnsTotal }} </th>
    
                    </tr>
                </tfoot>
            </table>

        </div>
      
    </div>
</div>