
<div style="padding:10px 20px"> 
    <form action="" method="get" role="form">   
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label for="type">Type</label>
                            <div class="d-flex">
                                <div class="form-check form-check-inline" style="margin-right: 20px;">
                                    <input class="form-check-input" type="radio" name="type" id="inlineRadio1" value="quantity" {{ request()->type == 'quantity' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="inlineRadio1">Quantity</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="inlineRadio2" value="values" {{ request()->type == 'values' || !isset(request()->type) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="inlineRadio2">Values</label>
                                  </div>
                            </div>                   
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label for="can_order">Can Order</label>
                            <select name="can_order" id="can_order" class="form-control mtselect">
                                <option value="" selected>Show All</option>
                                <option value="1" {{ request()->can_order === "1" ? 'selected' : '' }}>Full Packs
                                </option>
                                <option value="0" {{ request()->can_order === "0" ? 'selected' : '' }}>Inner Packs
                                </option>
                            </select>
                        </div>
                    </div>
                    <div style="margin-top:25px;">
                        {{-- <button type="button" class="btn btn-primary filterStockBalances">Filter</button> --}}
                        <button type="button" class="btn btn-primary" id="prindStockPdf">Print PDF</button>
                    </div>
                </div>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-4">
                <p class="text-right d-flex flex-row-reverse" style="margin:20px 50px 0 0;font-size:15px;">Stock Value Balance: <span style="font-weight: bold;font-size: 24px;margin-top:-5px;margin-left:10px;" id="fixedStockValueBalance">0</span></p>
            </div>
        </div> 
    </form>  
</div>
<div class="no-padding-h">
    <table class="table table-bordered table-hover" id="StockBalancesDataTable">
        <thead>
            <tr>    
                <th width="10%" id="st_stock_id_code">Stock ID Code</th>
                <th width="10%" id="st_title">Title</th>
                <th width="10%" id="st_t_total">Total</th>
                @foreach ($locations as $loc)
                    <th id="st_{{$loc->slug}}">{{ $loc->location_name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th style="text-align: right;" colspan="2">Total:</th>
                <th id="stockBalanceTotal" style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                </th>
                @foreach ($locations as $loc)
                    <th id="footerT{{$loc->slug}}" style="text-align: left; border-top: 1px solid #000;border-bottom: 1px solid #000;">{{$loc->slug}}</th>
                @endforeach
                
            </tr>
        </tfoot>
    </table>
</div>
@push('scripts')
    <script>
        $(document).ready(function(e) {
            var data=[];
            var footer={};
            
            getStockBalances();
            
            $('.filterStockBalances').click(function() {
                var type = $('#type').val();
                var can_order = $('#can_order').val();
                var params = {
                        type: type,
                        can_order: can_order
                    };
                getStockBalances(params)
            });
            $("input[name='type']").change(function(){
                var type = $("input[name='type']:checked").val();
                var can_order = $('#can_order').val();
                var params = {
                        type: type,
                        can_order: can_order
                    };
                getStockBalances(params)
            });
            $('#can_order').change(function() {
                var type = $("input[name='type']:checked").val();
                var can_order = $(this).val();
                var params = {
                        type: type,
                        can_order: can_order
                    };
                getStockBalances(params)
            });
            $('#prindStockPdf').click(function() {
                var type = $("input[name='type']:checked").val();
                var can_order = $('#can_order').val();
                var url = "/admin/maintain-suppliers/vendor-centre/stock-balances?print= 1&supplier={{$supplier->id}}&can_order="+can_order+"&type="+type;
                window.open(url, '_blank');
                
            });
        })

        function getStockBalances(param){
            var type='values';
            if (typeof param !== 'undefined') {
                type = param.type
            } 
            var can_order=null;
            if (typeof param !== 'undefined') {
                can_order = param.can_order
            } 
            // $.ajax({
            //     url: '{!! route('maintain-suppliers.vendor_centre.stock_balances') !!}', // Replace with the URL to your server-side script
            //     type: 'GET',
            //     data: {supplier:{{$supplier->id}},datatable:true,type:type,can_order:can_order},
            //     success: function(response) {
            //         data=response.data;
            //         footer=response.totals;
            //         var $tbody = $('#StockBalancesDataTable tbody');
            //         $tbody.empty(); // Clear any existing rows
                    
            //         $.each(data, function(index, item) {
            //             var $tr = $('<tr></tr>');
            //             console.log(item);
            //             $('#StockBalancesDataTable thead th').each(function() {
            //                 var key = $(this).attr('id');
            //                 var newKey = key.replace('st_', '');
            //                 var value = item[newKey] !== undefined ? item[newKey] : '';
            //                 $tr.append('<td>' + value + '</td>');
            //             });
            //             $tbody.append($tr);
            //         });
            //         $.each(footer, function(key, value) {
            //             if($("#footerT"+key).length){
            //                 $("#footerT"+key).text(value);
            //             }
            //         });
            //         $("#stockBalanceTotal").text(footer.t_total);
            //         $("#fixedStockValueBalance").text(footer.fixed_stock_value_balance);

            //     },
            //     error: function(xhr, status, error) {
            //         console.error('Error fetching sub-categories:', error);
            //     }
            // });



            
            $('#StockBalancesDataTable').DataTable().destroy();
            var table = $('#StockBalancesDataTable').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-suppliers.vendor_centre.stock_balances') !!}',
                    data: {supplier:{{$supplier->id}},datatable:true,type:type,can_order:can_order}
                },
                
                columns: [{
                        data: 'stock_id_code',
                        name: 'stock_id_code',
                        orderable: false
                    },
                    {
                        data: 'title',
                        name: 'title',
                        orderable: false
                    },
                    {
                        data: 't_total',
                        name: 't_total',
                        orderable: false
                    },
                    @foreach ($locations as $loc)
                        {
                            data: '{{ $loc->slug }}',
                            name: '{{ $loc->slug }}',
                            orderable: false
                        },
                    @endforeach
                    
                ],
                columnDefs: [{
                        "searchable": false,
                        "targets": 0
                    },
                    {
                        className: 'text-center',
                        targets: [1]
                    },
                    {
                        targets: 0,
                        render: function (data, type, row, meta) {
                            if (type === 'display') {
                                var actions = '';
                                actions += `<a href="/admin/item-centre/`+row.id+`" role="button" target="_blank">`+row.stock_id_code+`</a>`;    
                                return actions;
                            }
                            return data;
                        }
                    }
                ],
                language: {
                    searchPlaceholder: "Search"
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();
                    $.each(json.totals, function(key, value) {
                        if($("#footerT"+key).length){
                            $("#footerT"+key).text(value);
                        }
                    });
                    $("#stockBalanceTotal").text(json.totals.t_total);
                    $("#fixedStockValueBalance").text(json.totals.fixed_stock_value_balance);
                }
            });
        }
    </script>
@endpush
