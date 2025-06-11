<section class="content setup-content" id="step-5" >
    <div class="d-flex justify-content-center align-items-center box box-primary" style="height: 200px;" v-if="loadingCashAtHand">
        <div class="spinner-border" role="status">
            <div class="d-flex flex-column align-items-center">
                <h4><i class="fa fa-spinner fa-spin" style="color: black;"></i></h4>
                <h4 class="text-center">Loading Cash At Hand Details...</h4>
            </div>
        </div>
    </div>


    <div class="box box-primary" v-else>
        <div class="box-header with-border">
            <div class="d-flex justify-content-between">
                <div class="justify-content-start">
                    <p style="font-weight:bolder">
                    Cash At Hand Verification Status
                    </p>
                </div>
              
                <div class="justify-content-end">
                    <span class="label label-success" v-if="cashAtHandPassed">
                        Passed
                    </span>
                        <span  class="label label-danger"  v-else>
                        Failed
                    </span>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">

                        <table class="table table-hover table-bordered mt-10" id="cash-banking-balance-table">
                            <thead>
                                <tr>
                                    <th style="text-align: right;"> Sales </th>
                                    <th style="text-align: right;"> Rtns </th>
                                    <th style="text-align: right;"> Expenses </th>
                                    <th style="text-align: right;"> Net Sales </th>
                                    <th style="text-align: right;"> Eazzy </th>
                                    <th style="text-align: right;"> Equity Main </th>
                                    <th style="text-align: right;"> Vooma </th>
                                    <th style="text-align: right;"> KCB Main </th>
                                    <th style="text-align: right;"> MPESA </th>
                                    <th style="text-align: right;"> CDM </th>
                                    <th style="text-align: right;"> Total Rcts </th>
                                    {{-- <th style="text-align: right;"> Verified </th> --}}
                                    <th style="text-align: right;"> Balance </th>
                                    {{-- <th style="text-align: right;"> Allocated CDM </th> --}}
                                    {{-- <th style="text-align: right;"> Allocated CB </th> --}}
                                    {{-- <th style="text-align: right;"> Balance </th> --}}
                                </tr>
                            </thead>
    
                            <tbody v-cloak>
                                <tr>
                                    <td style="text-align: right;"> @{{ posCashAtHand.sales }} </td>
                                    <td style="text-align: right;"> @{{ posCashAtHand.returns }} </td>
                                    <td style="text-align: right;"> @{{ posCashAtHand.expenses }} </td>
                                    <td style="text-align: right;"> @{{ posCashAtHand.net_sales }} </td>
                                    <td style="text-align: right;"> @{{ posCashAtHand.eazzy }} </td>
                                    <td style="text-align: right;"> @{{ posCashAtHand.eb_main }} </td>
                                    <td style="text-align: right;"> @{{ posCashAtHand.vooma }} </td>
                                    <td style="text-align: right;"> @{{ posCashAtHand.kcb_main }} </td>
                                    <td style="text-align: right;"> @{{ posCashAtHand.mpesa }} </td>
                                    <td style="text-align: right;"> @{{ posCashAtHand.cdm }} </td>
                                    <td style="text-align: right;"> @{{ posCashAtHand.total_bankings }} </td>
                                    {{-- <td style="text-align: right;"> @{{ posCashAtHand.verified }} </td> --}}
                                    <td style="text-align: right;"> @{{ posCashAtHand.sales_variance }} </td>
                                    {{-- <td style="text-align: right;"> @{{ posCashAtHand.allocated_cdms }} </td> --}}
                                    {{-- <td style="text-align: right;"> @{{ posCashAtHand.allocated_cb }} </td> --}}
                                    {{-- <td style="text-align: right;"> @{{ posCashAtHand.balance }} </td> --}}
                                </tr>
                            </tbody>
                        </table>
    
                    </div>
                </div>
            </div>
          
        </div>
        <div class="box-footer">
            <button type="button" class="btn btn-success btn-sm" style="float: left;" onclick="$('.step-buttons4').trigger('click'); return false;"> <i class="fas fa-arrow-left"></i> Previous</button>
            <div style="float: right;">
                <button type="button" class="btn btn-success btn-sm" @click="fireCashAtHandModal" v-if="!cashAtHandPassed">Run Verification</button>
                <button type="button" class="btn btn-success btn-sm submitMe" @click="closeDay" style="float: right;" v-if="(cashAtHandPassed && !dayClosed)"> <i class="fas fa-arrow-right"></i> Close</button>
            </div>
          


        </div>
    </div>

</section>