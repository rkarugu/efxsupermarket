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
                        <th> Sale Type </th>
                        <th> Tablet Cashier </th>
                        <th> Counter Cashier </th>
                        <th style="text-align: right;"> Item Count </th>
                        <th style="text-align: right;"> Gross Total </th>
                        <th style="text-align: right;"> Discount </th>
                        <th style="text-align: right;"> Net Total </th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr v-for="(record, index) in sales" :key="index" v-cloak>
                        <th scope="row" style="width: 3%;"> @{{ index + 1 }} </th>
                        <td> @{{ record.created_at }} </td>
                        <td> @{{ record.sales_no }} </td>
                        <td> @{{ record.type }} </td>
                        <td> @{{ record.tablet_cashier }} </td>
                        <td> @{{ record.counter_cashier }} </td>
                        <td style="text-align: right;"> @{{ record.item_count }} </td>
                        <td style="text-align: right;"> @{{ record.formatted_gross_total }} </td>
                        <td style="text-align: right;"> @{{ record.formatted_discount_amount }} </td>
                        <td style="text-align: right;"> @{{ record.formatted_net_total }} </td>
                    </tr>
                </tbody>
    
                <tfoot>
                    <tr v-cloak>
                        <th colspan="7"> TOTALS </th>
                        <th style="text-align: right;"> @{{ salesGrossTotal }} </th>
                        <th style="text-align: right;"> @{{ salesDiscountTotal }} </th>
                        <th style="text-align: right;"> @{{ salesNetTotal }} </th>
                    </tr>
                </tfoot>
            </table>

        </div>
       
    </div>
</div>