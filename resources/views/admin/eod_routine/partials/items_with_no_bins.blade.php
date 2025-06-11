<section class="content setup-content" id="step-3">
    <div v-if="loadingBinlessItems" class="d-flex justify-content-center align-items-center box box-primary" style="height: 200px;">
        <div class="spinner-border" role="status">
            <div class="d-flex flex-column align-items-center">
                <h4><i class="fa fa-spinner fa-spin" style="color: black;"></i></h4>
                <h4 class="text-center">Loading Items With No Bin...</h4>
            </div>
        </div>
    </div>
    <div class="box box-primary" v-else>
        <div class="box-header with-border">
            <div class="d-flex justify-content-between">
                <div class="justify-content-start">
                    <p style="font-weight:bolder">
                    Items With No Bin Verification Status
                    </p>
                </div>
                <div class="justify-content-end">
                        <span class="label label-success" v-if="binlessItemsPassed">
                        Passed
                    </span>
                        <span  class="label label-danger"  v-else>
                        Failed
                    </span>
                </div>
            </div>
        </div>
        <div class="justify-content-start" v-if="!binlessItemsPassed">
            <p style="font-weight:bolder">
               Items With No Bins 
            </p>
        </div>
        
        <div class="row" v-if="(!binlessItemsPassed)">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="returns">
                        <thead>
                        <tr>
                            <th> # </th>
                            <th>Stock Id Code</th>
                            <th>Title</th>
                            <th>Pack Size</th>
                        
                        </tr>
                        </thead>
                        <tbody v-cloak>
                            <tr v-for="(record, index) in binlessItems" :key="index">
                                <th scope="row" style="width: 3%;"> @{{ index + 1 }} </th>
                                <td> @{{ record.stock_id_code}} </td>
                                <td> @{{ record.title }} </td>
                                <td> @{{ record.pack_size }} </td>
                       
                               
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
  
        <div class="box-footer">
            <button type="button" class="btn btn-success btn-sm" style="float: left;" onclick="$('.step-buttons2').trigger('click'); return false;"> <i class="fas fa-arrow-left"></i> Previous</button>
            <div style="float: right;">
                <button type="button" class="btn btn-success btn-sm" @click="verifyBinlessItems" v-if="!binlessItemsPassed">Run Verification</button>
                <button type="button" class="btn btn-success btn-sm submitMe" @click="handleNextStep(3)" style="float: right;" v-if="binlessItemsPassed"> <i class="fas fa-arrow-right"></i> Next</button>

            </div>
        </div>
    </div>

</section>