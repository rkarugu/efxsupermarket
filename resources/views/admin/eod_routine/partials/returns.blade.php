<section class="content setup-content" id="step-1">
    <div v-if="loadingReturns" class="d-flex justify-content-center align-items-center box box-primary" style="height: 200px;">
        <div class="spinner-border" role="status">
            <div class="d-flex flex-column align-items-center">
                <h4><i class="fa fa-spinner fa-spin" style="color: black;"></i></h4>
                <h4 class="text-center">Loading Returns Details...</h4>
            </div>
        </div>
    </div>
    <div class="box box-primary" v-else>
        <div class="box-header with-border">
            <div class="d-flex justify-content-between">
                <div class="justify-content-start">
                    <p style="font-weight:bolder">
                    Return Verification Status
                    </p>
                </div>
              
                <div class="justify-content-end">
                        <span  class="label label-success" v-if="returnsPassed">
                        Passed
                    </span>
                        <span  class="label label-danger" v-else>
                        Failed
                    </span>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="returns">
                            <thead>
                            <tr>
                                <th>Total Returns </th>
                                <th>Processed Returns </th>
                                <th>Pending Returns Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>@{{ returnSummary.allReturns  }}</td>
                                <td>@{{ returnSummary.allReturns -  returnSummary.pendingReturns  }}</td>
                                <td>@{{ returnSummary.pendingReturns }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="justify-content-start" v-if="!returnsPassed">
                <p style="font-weight:bolder">
                   Pending Returns
                </p>
            </div>
            
            <div class="row" v-if="(!returnsPassed)">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="returns">
                            <thead>
                            <tr>
                                <th> # </th>
                                <th>Return No.</th>
                                <th>Sale No.</th>
                                <th>Item</th>
                                <th>Bin</th>
                                <th>Qty</th>
                            </tr>
                            </thead>
                            <tbody v-cloak>
                                <tr v-for="(record, index) in pendingReturns" :key="index">
                                    <th scope="row" style="width: 3%;"> @{{ index + 1 }} </th>
                                    <td> @{{ record.return_no }} </td>
                                    <td> @{{ record.sale_no }} </td>
                                    <td> @{{ record.item }} </td>
                                    <td> @{{ record.bin }} </td>
                                    <td> @{{ record.quantity }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
         
            <div style="float: right;">
                <button type="button" class="btn btn-success btn-sm" @click="verifyReturns" v-if="!returnsPassed">Run Verification</button>
                <button type="button" class="btn btn-success btn-sm submitMe" @click="handleNextStep(1)" style="float: right;" v-if="returnsPassed"> <i class="fas fa-arrow-right"></i> Next</button>
            </div>
          


        </div>
    </div>

</section>