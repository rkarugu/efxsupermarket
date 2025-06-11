<div class="box-body">
    <p v-if="loadingCb"> Loading data... </p>

    <div v-else>
        @if (can('add-short-banking-comments', 'reconciliation'))
            <div class="row">
                <div class="form-group col-md-12">
                    <button class="btn btn-success btn-sm" @click="shortBankingModalInitiate" id="searchSbBtn" style="float: right;">
                        <i class="fas fa-plus btn-icon"></i> Add 
                    </button>
                </div>
            </div>
        @endif
        <div v-if="sbErrorMessage">
            <p id="sb-error-message" style="color: red; font-weight: 700;" v-cloak>
                @{{ cbErrorMessage }} </p>
        </div>

        <hr>

        <table class="table table-bordered" id="cb-table">
            <thead>
                <tr>
                    <th style="width: 3%;"> # </th>
                    <th> Comment By </th>
                    <th> Type </th>
                    <th> Comment </th>
                    <th style="text-align: right;"> Amount </th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="(record, index) in sb" :key="index">
                    <th style="width: 3%;" scope="row"> @{{ index + 1 }} </th>
                    <td> @{{ record.name }} </td>
                    <td> @{{ record.type }} </td>
                    <td> @{{ record.comment }} </td>
                    <td style="text-align: right;"> @{{ record.formatted_amount }} </td>
                    <td>
                        @if (can('edit-short-banking-comments', 'reconciliation'))
                            <i @click="editSbRecord(record.id)"
                                class="fas fa-pen"
                                title="edit"
                                style="cursor: pointer; font-size: 16px; ">
                            </i>
                        @endif

                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr v-cloak>
                    <th colspan="4"> TOTALS </th>
                    <th style="text-align: right;"> @{{ sbTotal }} </th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>