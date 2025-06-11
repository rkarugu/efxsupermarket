<section class="content setup-content" id="step-6">
    <div v-if="loadingNumberSeries" class="d-flex justify-content-center align-items-center box box-primary" style="height: 200px;">
        <div class="spinner-border" role="status">
            <div class="d-flex flex-column align-items-center">
                <h4><i class="fa fa-spinner fa-spin" style="color: black;"></i></h4>
                <h4 class="text-center">Loading Number Series ...</h4>
            </div>
        </div>
    </div>
    <div class="box box-primary" v-else>
        <div class="box-header with-border">
            <div class="d-flex justify-content-between">
                <div class="justify-content-start">
                    <p style="font-weight:bolder">
                        Number Series Verification Status
                    </p>
                </div>
                <span class="label label-success" v-if="numberSeriesPassed">
                    Passed
                </span>
                    <span  class="label label-danger"  v-else>
                    Failed
                </span>
            </div>
        </div>
        <div class="justify-content-start" v-if="!numberSeriesPassed">
            <p style="font-weight:bolder">
               Summary
            </p>
        </div>
        
        <div class="row" v-if="!numberSeriesPassed">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="numberSeries">
                        <thead>
                        <tr>
                            <th>Complete Sales</th>
                            <th>Archived Sales</th>
                        
                        </tr>
                        </thead>
                        <tbody v-cloak>
                            <tr>
                                <td> @{{ salesSummary.completeSales }} </td>
                                <td> @{{ salesSummary.archivedSales }} </td>
                            </tr>
                          
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <button type="button" class="btn btn-success btn-sm" style="float: left;" onclick="$('.step-buttons4').trigger('click'); return false;"> <i class="fas fa-arrow-left"></i> Previous</button>
            <div style="float: right;">
                <button type="button" class="btn btn-success btn-sm" @click="verifyNumberSeries" v-if="!numberSeriesPassed">Run Verification</button>
                <button type="button" class="btn btn-success btn-sm submitMe" @click="handleNextStep(5)" style="float: right;" v-if="numberSeriesPassed"> <i class="fas fa-arrow-right"></i> Next</button>
            </div>
        </div>
    </div>

</section>