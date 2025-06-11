<table>
    <tr collspan="6" >
        <td collspan="6" ><strong>{{ getAllSettings()['COMPANY_NAME'] }}</strong></td>
    </tr>
        <tr collspan="6" >
            <td collspan="6" ><strong>{{$title}}</strong></td>
        </tr>
</table>
<table class="table table-bordered" id="create_datatable_50">
    <thead>
        <tr>
            <th><strong> Stock ID Code </strong> </th>
            <th><strong> Item </strong> </th>
            <th><strong> Category </strong> </th>
            <th><strong> Sub Category </strong> </th>
            <th><strong> SOH </strong> </th>
            <th><strong> Standard Cost </strong> </th>
            <th><strong> Selling Price </strong> </th>
            <th><strong> Purchasing Data Set </strong> </th>
            <th><strong> Last Purchase Date </strong> </th>            
        </tr>
    </thead>
    <tbody> 
      @foreach($itemlists as $item)
            @if($item->sup === null)
            <tr style="float: left; text-align: left;">
                <td>{{ $item->stock_id_code }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->category }}</td>
                <td>{{ $item->subcategory }}</td>
                <td>{{ $item->qoh }}</td>
                <td>{{ $item->standard_cost }}</td>
                <td>{{ $item->selling_price }}</td>
                <td>
                    @if($item->sup === null)
                    No 
                    @else($item->sup !== null)
                    Yes
                    @endif
                </td>
                <td>{{ $item->last_purchase }}</td>
            </tr>
            @endif
        @endforeach
    </tbody>
    
</table>