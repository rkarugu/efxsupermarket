<section class="content setup-content" id="step-4">
    <div v-if="loadingUnbalancedTransactions" class="d-flex justify-content-center align-items-center box box-primary" style="height: 200px;">
        <div class="spinner-border" role="status">
            <div class="d-flex flex-column align-items-center">
                <h4><i class="fa fa-spinner fa-spin" style="color: black;"></i></h4>
                <h4 class="text-center">Loading Unbalanced Transactions ...</h4>
            </div>
        </div>
    </div>
    <div class="box box-primary" v-else>
        <div class="box-header with-border">
            <div class="d-flex justify-content-between">
                <div class="justify-content-start">
                    <p style="font-weight:bolder">
                        Unbalanced Transactions Verification Status
                    </p>
                </div>
                <div class="justify-content-end">
                    <span class="label label-success" v-if="unbalancedTransactionsPassed">
                        Passed
                    </span>
                        <span  class="label label-danger"  v-else>
                        Failed
                    </span>

                </div>
            
            </div>
            </div>
        </div>
        <div class="justify-content-start" v-if="!unbalancedTransactionsPassed">
            <p style="font-weight:bolder">
                Sales Vs Stocks Ledger
            </p>
        </div>
        
        <div class="row" v-if="!loadingUnbalancedTransactions">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="salesvsstocksledger">
                        <thead>
                        <tr>
                            <th> # </th>
                            <th>Parameter</th>
                            <th>Sales Ledger</th>
                            <th>Stock Moves Ledger</th>
                            <th>Payments Ledger</th>
                            <th>Debtors Ledger</th>
                        
                        </tr>
                        </thead>
                        <tbody v-cloak>
                            <tr>
                                <th> 1 </th>
                                <td> Transactions</td>
                                <td> @{{ stocksVsSales.invoicesCount }} </td>
                                <td> @{{ stocksVsSales.movesCount }} </td>
                                <td> @{{ stocksVsSales.posPaymentsRecords }} </td>
                                <td> @{{ stocksVsSales.debtorsRecords }} </td>
                       
                            </tr>
                            <tr>
                                <th> 2 </th>
                                <td> Quantity</td>
                                <td> @{{ stocksVsSales.soldQoh }} </td>
                                <td> @{{ stocksVsSales.movedQoh }} </td>
                                <td> @{{ '-'}} </td>
                                <td> @{{ '-'}} </td>
                       
                            </tr>
                            <tr>
                                <th> 3 </th>
                                <td> Value</td>
                                <td> @{{ stocksVsSales.soldAmount }} </td>
                                <td> @{{ stocksVsSales.movedAmount }} </td>
                                <td> @{{ stocksVsSales.posPaymentsAmount }} </td>
                                <td> @{{ stocksVsSales.debtorsAmount }} </td>
                            </tr>
                           
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="justify-content-start" v-if="unbalancedInvoicesExist">
                <p style="font-weight:bolder">
                    Unbalanced Sales 
                </p>
            </div>
            <div class="col-sm-12">
                <div class="table-responsive" v-if="unbalancedInvoicesExist">
                    <table class="table table-bordered table-hover" id="unbalancedInvoices">
                        <thead>
                        <tr>
                            <th> # </th>
                            <th>Sales No</th>
                            <th>Item Count</th>
                            <th>Moves Count</th>
                            {{-- <th>Invoice Total</th>
                            <th>Moves Total</th> --}}
                        </tr>
                        </thead>
                        <tbody v-cloak>
                            <tr v-for="(record, index) in unbalanced_invoices" :key="index">
                                <th scope="row" style="width: 3%;"> @{{ index + 1 }} </th>
                                <td> @{{ record.requisition_no }} </td>
                                <td> @{{ record.get_related_item_count }} </td>
                                <td> @{{ record.stock_moves_count }} </td>
                             
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <button type="button" class="btn btn-success btn-sm" style="float: left;" onclick="$('.step-buttons3').trigger('click'); return false;"> <i class="fas fa-arrow-left"></i> Previous</button>
            <div style="float: right;">
                <button type="button" class="btn btn-success btn-sm" @click="balanceInvoices" v-if="unbalancedInvoicesExist">Balance Sales</button>
                <button type="button" class="btn btn-success btn-sm" @click="verifyUnbalancedTransactions" v-if="(!unbalancedTransactionsPassed && !unbalancedInvoicesExist)">Run Verification</button>
                <button type="button" class="btn btn-success btn-sm submitMe" @click="handleNextStep(4)" style="float: right;" v-if="unbalancedTransactionsPassed"> <i class="fas fa-arrow-right"></i> Next</button>
                {{-- <button type="button" class="btn btn-success btn-sm submitMe" @click="closeDay" style="float: right;" v-if="unbalancedTransactionsPassed"> <i class="fas fa-arrow-right"></i> Close</button> --}}
            </div>
        </div>
    </div>

</section>