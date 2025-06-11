<table class="table table-bordered table-hover" >
                        <thead>
                        <tr>
                            <th> <strong> # </strong></th>
                            <th> <strong>  Item Code </strong></th>
                            <th> <strong> Title </strong></th>
                            <th> <strong> Pack Size </strong></th>
                            <th> <strong> Selling Price </strong></th>
                            <th> <strong> Qoh </strong></th>
                        </tr>
                        </thead>
                        <tbody>
                         
                        @foreach ($inventoryItems as $item)
                            <tr>
                                <th>{{ $loop->index + 1 }}</th>
                                <td>{{ $item->stock_id_code }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->pack }}</td>
                                <td>{{ manageAmountFormat($item->parent_selling_price) }}</td>
                                <td>{{ $item->parent_quantity }}</td>
                            </tr>
                            
                        @endforeach              
                        </tbody>
                    </table>