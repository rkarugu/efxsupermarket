<div class="box-body">
    <p v-if="loadingCb"> Loading data... </p>

    <div v-else>
        @if (can('allocate-cash-banking', 'reconciliation'))
            <div class="row">
                <div class="form-group col-md-3">
                    <label for="cb_amount" class="control-label"> Allocation Amount </label>
                    <input type="text" name="cb_amount" id="cb_amount" class="form-control"
                        v-model="cbSearchAmount">
                </div>

                <div class="form-group col-md-3">
                    <label for="cb_reference" class="control-label"> Reference </label>
                    <input type="text" name="cb_reference" id="cb_reference" class="form-control"
                        v-model="cbSearchRef">
                </div>

                <div class="form-group col-md-3">
                    <label style="color: white; display: block;"> Search </label>
                    <button class="btn btn-success" @click="searchCb" id="searchCbBtn">
                        <i class="fas fa-magnifying-glass-dollar btn-icon"></i> Search Deposit
                    </button>
                </div>
            </div>
        @endif

        <div v-if="cbErrorMessage">
            <p id="cdm-error-message" style="color: red; font-weight: 700;" v-cloak>
                @{{ cbErrorMessage }} </p>
        </div>

        <hr>

        <table class="table table-bordered" id="cb-table">
            <thead>
                <tr>
                    <th style="width: 3%;"> # </th>
                    <th> Allocation Date </th>
                    <th> Channel </th>
                    <th> Bank Reference </th>
                    <th style="text-align: right;"> Amount </th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="(record, index) in cb" :key="index">
                    <th style="width: 3%;" scope="row"> @{{ index + 1 }} </th>
                    <td> @{{ record.banking_time }} </td>
                    <td> @{{ record.channel }} </td>
                    <td> @{{ record.bank_reference }} </td>
                    <td style="text-align: right;"> @{{ record.formatted_banked_amount }} </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>