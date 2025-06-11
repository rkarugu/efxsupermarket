			<h2 class="fh2">Expense History</h2> 
			<table id="dataTable" class="table   table-hover categories-table-list" border="0.5">

				<thead>
					<tr>
						<th scope="col">Vehicle</th>
						<th scope="col">Date</th>
						<th scope="col">Fueling</th>
						<th scope="col">Vendor</th>
						<th scope="col">Amount</th>
					</tr>
				</thead>
				<tbody>
					@foreach($expensehistory as $expensehistory) 	 
					<tr>
						<th>{{$expensehistory->LicensePlate->license_plate}}</th>
						<th>{{$expensehistory->date}}</th>
						<th>{{$expensehistory->Type->title}}</th>
						<th>{{$expensehistory->VendorName->name}}</th>
						<th>{{$expensehistory->amount}}</th>
					</tr>
					<!-- <input type=""  class="expensehistory" value="{{$expensehistory}}"> -->
					@endforeach	
				</tbody>
				 <tfoot>
                                       <tr>
                                           <td colspan="4"><b>Total<b></td>
                                           <td id="amount"></td>
                                       </tr>
                                   </tfoot>
			</table>
		