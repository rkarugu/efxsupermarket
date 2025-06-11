<style>
  table {
    border-collapse: collapse; 
  }
  th, td {
    white-space: normal; 
    max-width: fit-content; 
    flex: 0 1 auto;
  }
</style>
 <table class="table table-bordered table-hover" id="create_datatable_25">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th  class="text"> <strong> Mother Item Code </strong></th>
                            <th  class="text"> <strong> Title </strong></th>
                            <th  class="text"> <strong> Pack Size </strong></th>
                            <th  class="text"> <strong> Selling Price </strong></th>
                            <th  class="text"> <strong> Qoh </strong></th>
                           

                            <th  class="text"> <strong> Child Item Code </strong></th>
                            <th  class="text"> <strong> Title </strong></th>
                            <th  class="text"> <strong> Pack Size </strong></th>
                            <th  class="text"> <strong> Selling Price </strong></th>
                            <th  class="text"> <strong> Qoh </strong></th>
                            <th  class="text"> <strong> Factor </strong></th>
                           
                           
                        </tr>
                        </thead>
                        <tbody>
                         
                        @foreach ($inventoryItems as $item)
                            <tr>
                                <th>{{ $loop->index + 1 }}</th>
                                <td class="text">{{ $item->parent_stock_id }}</td>
                                <td class="text">{{ $item->parent_title }}</td>
                                <td class="text">{{ $item->parent_pack_title }}</td>
                                <td class="num">{{ manageAmountFormat($item->parent_selling_price) }}</td>
                                <td class="num">{{ $item->parent_quantity }}</td>

                                <td class="text">{{ $item->child_stock_id }}</td>
                                <td class="text">{{ $item->child_title }}</td>
                                <td class="text">{{ $item->child_pack_title   }}</td>
                                <td class="num">{{ manageAmountFormat($item->child_selling_price) }}</td>
                                <td class="num">{{ $item->quantity }}</td>
                                <td>{{ number_format($item->conversion_factor, 0)}}</td>

                            </tr>
                            
                        @endforeach              
                        </tbody>
                    </table>