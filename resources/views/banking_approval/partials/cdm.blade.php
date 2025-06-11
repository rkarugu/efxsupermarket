<div class="box-body">
    <p v-if="loadingCdms"> Loading data... </p>

    <div v-else>
        @if (can('allocate-cdm', 'reconciliation'))
            <div class="row">
                <div class="form-group col-md-3">
                    <label for="drop_id" class="control-label"> Drop Transaction </label>
                    <select id="drop_id" class="form-control" required v-model="selectedDropId">
                        <option v-for="drop in drops" :key="drop.id" :value="drop.id">
                            @{{ drop.reference }} </option>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="amount" class="control-label"> Allocation Amount </label>
                    <input type="text" name="amount" id="amount" class="form-control"
                        v-model="cdmSearchAmount">
                </div>

                <div class="form-group col-md-3">
                    <label for="reference" class="control-label"> Reference </label>
                    <input type="text" name="reference" id="reference" class="form-control"
                        v-model="cdmSearchRef">
                </div>

                <div class="form-group col-md-3">
                    <label style="color: white; display: block;"> Search </label>
                    <button class="btn btn-success" @click="searchCdm" id="searchCdmBtn">
                        <i class="fas fa-magnifying-glass-dollar btn-icon"></i> Search Deposit
                    </button>
                </div>
            </div>
        @endif

     

        <div v-if="cdmErrorMessage">
            <p id="cdm-error-message" style="color: red; font-weight: 700;" v-cloak>
                @{{ cdmErrorMessage }} </p>
        </div>

        <hr>

        <table class="table table-bordered" id="cdms-table">
            <thead>
                <tr>
                    <th style="width: 3%;"> # </th>
                    <th> Date </th>
                    <th> Drop Number </th>
                    <th> Bank Reference </th>
                    <th style="text-align: right;"> Amount </th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="(record, index) in cdms" :key="index">
                    <th style="width: 3%;" scope="row"> @{{ index + 1 }} </th>
                    <td> @{{ record.bd_time }} </td>
                    <td> @{{ record.cd_reference }} </td>
                    <td> @{{ record.bank_reference }} </td>
                    <td style="text-align: right;"> @{{ record.amount }} </td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <th colspan="4"> TOTAL DEPOSITS </th>
                    <th style="text-align: right;"> @{{ cdmTotal }} </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>