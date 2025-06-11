<section class="content setup-content" id="step-2">
    <div v-if="loadingSplits" class="d-flex justify-content-center align-items-center box box-primary" style="height: 200px;">
        <div class="spinner-border" role="status">
            <div class="d-flex flex-column align-items-center">
                <h4><i class="fa fa-spinner fa-spin" style="color: black;"></i></h4>
                <h4 class="text-center">Loading Splits Details...</h4>
            </div>
        </div>
    </div>
    <div class="box box-primary" v-else>
        <div class="box-header with-border">
            <div class="d-flex justify-content-between">
                <div class="justify-content-start">
                    <p style="font-weight:bolder">
                    Split Verification Status
                    </p>
                </div>
                <div class="justify-content-end">
                    <span class="label label-success" v-if="splitsPassed">
                    Passed
                </span>
                    <span  class="label label-danger"  v-else>
                    Failed
                </span>
            </div>
            </div>
        </div>
        <div class="justify-content-start" v-if="!splitsPassed">
            <p style="font-weight:bolder">
               Pending Splits
            </p>
        </div>
        
        <div class="row" v-if="(!splitsPassed)">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="returns">
                        <thead>
                        <tr>
                            <th> # </th>
                            <th>Mother Item</th>
                            <th>Mother Bin</th>
                            <th>Mother Qoh</th>
                            <th>Child Item</th>
                            <th>Child Bin</th>
                            <th>Child Qoh</th>
                        </tr>
                        </thead>
                        <tbody v-cloak>
                            <tr v-for="(record, index) in missingOnSplit" :key="index">
                                <th scope="row" style="width: 3%;"> @{{ index + 1 }} </th>
                                <td> @{{ record.mother_stock_id_code + ' - ' + record.mother_title }} </td>
                                <td> @{{ record.mother_bin_location }} </td>
                                <td> @{{ record.mother_quantity }} </td>
                                <td> @{{ record.child_stock_id_code + ' - ' + record.child_title }} </td>
                                <td> @{{ record.child_bin_location }} </td>
                                <td> @{{ record.child_quantity }} </td>
                               
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="justify-content-start" v-if="!splitsPassed">
            <p style="font-weight:bolder">
               Pending Split Dispatches
            </p>
        </div>
        
        <div class="row" v-if="(!splitsPassed)">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="returns">
                        <thead>
                        <tr>
                            <th> # </th>
                            <th>user</th>
                            <th>Break No.</th>
                            <th>Item Count</th>
                        </tr>
                        </thead>
                        <tbody v-cloak>
                            <tr v-for="(record, index) in pendingSplits" :key="index">
                                <th scope="row" style="width: 3%;"> @{{ index + 1 }} </th>
                                <td> @{{ record.name }} </td>
                                <td> @{{ record.breaking_code }} </td>
                                <td> @{{ record.item_count }} </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <button type="button" class="btn btn-success btn-sm" style="float: left;" onclick="$('.step-buttons1').trigger('click'); return false;"> <i class="fas fa-arrow-left"></i> Previous</button>
            <div style="float: right;">
                <button type="button" class="btn btn-success btn-sm" @click="verifySplits" v-if="!splitsPassed">Run Verification</button>
                <button type="button" class="btn btn-success btn-sm submitMe" @click="handleNextStep(2)" style="float: right;" v-if="splitsPassed"> <i class="fas fa-arrow-right"></i> Next</button>

            </div>
        </div>
    </div>

</section>