<div style="padding:10px">
    <div class="table-responsive">
        <table class="table" id="fuel-history-table">
            <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th> LPO Number</th>
                <th>Fueled By</th>
                <th>Shift Type</th>
                <th>Last Fuel Entry Milage</th>
                <th>Fuel Quantity</th>
                <th>Date</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="(fuel, index) in fuelHistory" :key="index" v-cloak>
                <th scope="row" style="width: 3%;">@{{ index + 1 }}</th>
                <td> @{{ fuel.lpo_number }}</td>
                <td> @{{ fuel.fueled_by }} </td>
                <td> @{{ fuel.shift_type }} </td>
                <td> @{{ fuel.last_fuel_entry_mileage }} </td>
                <td> @{{ fuel.fuel_quantity }} </td>
                <td> @{{ fuel.created_at }} </td>
            </tr>
            <tfoot>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total</th>
                <th><span v-cloak>@{{ fuelHistoryTotal }}</span></th>
                <th></th>
            </tfoot>
            </tbody>
        </table>
    </div>
</div>