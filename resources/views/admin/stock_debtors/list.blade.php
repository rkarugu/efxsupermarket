@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight:500 !important;"> Stock Debtors </h3>
                    <div class="d-flex">
                        <div>
                            <select name="role" id="role" class="mtselect form-control">
                                <option value="">Choose Role</option>
                                @foreach (getRoles() as $key => $item)
                                    <option value="{{$key}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary" name="action" id="generatePDFBtn" style="margin:0 10px;height: 35px;">
                            <i class="fa fa-file"></i> PDF
                        </button>
                        <a type="button" href="{{ route('stock-debtors.index') }}" class="btn btn-primary" style="margin-right:10px;height: 35px;">
                            <i class="fa fa-file"></i> Clear
                        </a>
                        @if (can('add', 'stock-debtors'))
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                                Add Stock Debtors
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            {{-- <div class="box-header with-border">
               
                <div class="box-header-flex">
                    <h3 class="box-title"></h3>
                </div>
            </div> --}}
    <div class="box-body">
        @include('message')
        <table class="table table-bordered table-hover" id="debtorDataTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Role</th>
                    <th>Bin Location</th>
                    <th>Store</th>
                    <th class="text-right">Amount</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right"><b>Total</b></td>
                    <td><b id="grandTotal"></b></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
</section>

<div class="modal fade" id="createModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Add Stock Debtor </h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form action="{{ route('stock-debtors.store') }}" method="POST">
                @csrf
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="employee" class="control-label"> Employees </label>
                                <select name="employee" id="employee" class="form-control select2" required>
                                    <option value="" selected disabled> Select Employee </option>
                                    @foreach ($employees as $employee)
                                        <option value="{{$employee->id}}">{{$employee->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add</button>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade splitModal" id="splitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title" id="splitTitle"> Split Debtor </h3>
                    <div>
                        <h2 class="box-title" id="splitModalTotal" style="margin-right: 20px;">0</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            <form id="split-form" method="POST">
                @csrf
                <div class="box-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="position: relative;">User <button type="button" class="btn btn-danger btn-sm addNewrow" style="background-color: transparent !important; border:none; color:green !important;right:0;position:absolute;"><i class="fa fa-plus" aria-hidden="true"></i></button></th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th colspan="2" id="splitTotal" style="padding-left: 22px;">0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <input type="hidden" name="debtor" id="debtor">
                        <input type="hidden" name="total" id="total">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="splitConfirmButton">Split</button>
                        <span id="splitErrorMessage" style="position: absolute;color: red;right: 20px;font-weight: 700;">Check Split Total</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade splitModal" id="splitNonDebtorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title" id="splitNonDebtorTitle"> Split Debtor </h3>
                    <div>
                        <h2 class="box-title" id="splitNonDebtorModalTotal" style="margin-right: 20px;">0</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            <form id="split-non-debtor-form" method="POST">
                @csrf
                <div class="box-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="position: relative;">User <button type="button" class="btn btn-danger btn-sm addNewrowNon" style="background-color: transparent !important; border:none; color:green !important;right:0;position:absolute;"><i class="fa fa-plus" aria-hidden="true"></i></button></th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th colspan="2" id="splitNonDebtorTotal" style="padding-left: 22px;">0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <input type="hidden" name="debtor" id="debtorNonDebtor">
                        <input type="hidden" name="total" id="totalNonDebtor">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="splitNonDebtorConfirmButton">Split</button>
                        <span id="splitNonDebtorErrorMessage" style="position: absolute;color: red;right: 20px;font-weight: 700;">Check Split Total</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection
@push('scripts')

<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script>
    $(function() {
        
        $('#generatePDFBtn').on('click',function(){
            var rol = $("#role").val();
            location.href=`{!! route('stock-debtors.index', ['print' => 'pdf']) !!}&role=` + rol;
        });

        $('.select2').select2({
            dropdownParent: $('#createModal')
        });
        $('.select22').select2({
            dropdownParent: $('.splitModal')
        });
        $('.select3').select2({
            dropdownParent: $('#createModal')
        });
        $('.mtselect').select2();
        $('#role').on('change', function() {
            $("#debtorDataTable").DataTable().ajax.reload();
        });

        let table = $("#debtorDataTable").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, "asc"]
            ],
            autoWidth: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route('stock-debtors.index') !!}',
                data: function(data) {
                    data.role = $("#role").val();
                }
            },
            columns: [{
                    data: "employee.name",
                    name: "employee.name",
                },
                {
                    data: "employee.phone_number",
                    name: "employee.phone_number",
                },
                {
                    data: "employee.user_role.title",
                    name: "employee.userRole.title"
                },
                {
                    data: "employee.uom.title",
                    name: "employee.uom.title",
                },
                {
                    data: "employee.location_stores.location_name",
                    name: "employee.location_stores.location_name",
                },
                {
                    data: "total",
                    name: "total",
                    className: "text-right",
                },
                {
                        data: null,
                        orderable: false,
                        searchable: false
                }
            ],
            columnDefs: [
                    
                    {
                        targets: -1,
                        render: function (data, type, row, meta) {
                            if (type === 'display') {
                                var actions = '<div class="d-flex">';
                                @if (can('view-details', 'stock-debtors'))
                                    actions += `<a href="/admin/stock-debtors/view/`+row.id+`" role="button" title="View"><i class="fa fa-solid fa-eye"></i></a>`;
                                @endif
                                @if (can('split', 'stock-debtors'))
                                    actions += `<a onclick="splitDebtor(`+row.id+`,'`+row.total+`','`+row.employee.name+`','`+row.employee.uom.id+`')" href="javascript:void(0)" style="margin-left:5px;" role="button" title="Internal Split"><i class="fas fa-people-arrows"></i></a>`;
                                @endif
                                @if (can('split-non-debtor', 'stock-debtors'))
                                    actions += `<a onclick="splitNonDebtor(`+row.id+`,'`+row.total+`','`+row.employee.name+`','`+row.employee.uom.id+`')" href="javascript:void(0)" style="margin-left:5px;" role="button" title="External Split"><i class="fas fa-solid fa-person-walking-dashed-line-arrow-right"></i></a>`;
                                @endif
                                actions +='</div>';
                                return actions;
                            }
                            return data;
                        }
                    }
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var json = api.ajax.json();

                $("#grandTotal").text(json.grand_total);
            }
        });

        table.on('draw', function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        

        $(document).on('input', '.amount-split', function() {
            let dataType = $(this).attr('data-type');
            let totalType = '';
            if (dataType=='nonDebtor') {
                totalModal = '#splitNonDebtorModalTotal';
                splitTotal = '#splitNonDebtorTotal';
                footTotal = '#totalNonDebtor';
            } else {
                totalModal = '#splitModalTotal';
                splitTotal = '#splitTotal';
                footTotal = '#total';
            }
            sumAmountSplit(totalModal,splitTotal,footTotal);          
        });
        var form = new Form();

        $('#split-form').on('submit', function(event) {
            event.preventDefault(); 

            $.ajax({
                url: '{{ route('stock-debtors.split') }}',
                type: 'POST',
                data: $(this).serialize(),
                // dataType: 'json',
                success: function(response) {
                    $('#splitModal').modal('hide');
                    if(response.result == 0) {
                    for(let i in response.errors) {
                        var id = i.split(".");
                        if(id && id[1]){
                            $("[name='"+id[0]+"["+id[1]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+response.errors[i][0]+'</label>');
                        }else
                        {
                            $("[name='"+i+"']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+response.errors[i][0]+'</label>');
                            $("."+i).parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+response.errors[i][0]+'</label>');
                        }
                    }
                }
                if(response.result === 1) {
                    form.successMessage(response.message);
                    location.reload();
                }
                if(response.result === -1) {
                    form.errorMessage(response.message);
                }
                },
                error: function(xhr, status, error) {
                    $('#response-message').html('<p>Error: ' + xhr.responseText + '</p>');
                }
            });
        });

        $('#split-non-debtor-form').on('submit', function(event) {
            event.preventDefault(); 

            $.ajax({
                url: '{{ route('stock-debtors.split.non_debtor') }}',
                type: 'POST',
                data: $(this).serialize(),
                // dataType: 'json',
                success: function(response) {
                    $('#splitNonDebtorModal').modal('hide');
                    if(response.result == 0) {
                    for(let i in response.errors) {
                        var id = i.split(".");
                        if(id && id[1]){
                            $("[name='"+id[0]+"["+id[1]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+response.errors[i][0]+'</label>');
                        }else
                        {
                            $("[name='"+i+"']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+response.errors[i][0]+'</label>');
                            $("."+i).parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+response.errors[i][0]+'</label>');
                        }
                    }
                }
                if(response.result === 1) {
                    form.successMessage(response.message);
                    location.reload();
                }
                if(response.result === -1) {
                    form.errorMessage(response.message);
                }
                },
                error: function(xhr, status, error) {
                    $('#response-message').html('<p>Error: ' + xhr.responseText + '</p>');
                }
            });
        });
        
    });

    function splitDebtor(id,total,name,bin)
    {
        $('#splitErrorMessage').hide();
        $('#splitTitle').html('Split Debtor ('+name+')');
        $('#debtor').val(id);
        var amount = total.replace(/,/g, '')
        $('#total').val(amount);
        $('#splitModalTotal').html(total);
        $('#splitModal').modal();
        $('#splitTotal').text(0);
        const $splitBody = $('#splitModal .box-body table tbody');
        $splitBody.empty();
        getBinUsers(id,bin).done(function(response) {
            let row = `
                                <tr data-id="1">
                                    <td>
                                        <select class="form-control select3 split_select" name="split_debtor[1]">
                                        <option value="">Choose Storekeeper</option>`;
                                        response.forEach(item => {
                                            row += `<option value="${item.id}">${item.name}</option>`;
                                        });
                                        row += `</select>
                                        </td>
                                    <td><input type="number" name="split_amount[1]" class="form-control amount-split" data-type="debtor" value="0" /></td>
                                    <td><button type="button" data-id="1" class="btn btn-danger btn-sm deleteparent" data-type="debtor" style="background-color: transparent !important; border:none; color:red !important;"><i class="fas fa-trash" style="color:red;" aria-hidden="true"></i></button></td>
                                </tr>
                            `;
                            $splitBody.append(row);
            
        }).fail(function(data) {
            console.log(data);
            console.error('Failed to fetch dates.');
        });
    }



    $(document).on('click', '.addNewrow', function() {
        var lastTr = $("#splitModal .box-body table tbody tr:last-child").attr("data-id");
        var clonedRow = $("#splitModal .box-body table tbody tr:first").clone();
        let newId = parseInt(lastTr)+1;
        clonedRow.attr('data-id', newId);
        clonedRow.find("input").val("0").attr('name', 'split_amount['+newId+']');
        clonedRow.find("select").val("").attr('name', 'split_debtor['+newId+']');
        clonedRow.find("button").val("").attr('data-id', newId);
        $("#splitModal .box-body table tbody").append(clonedRow);
        
        total=[];
        $('.split_select').each(function(key, value){
            let selectedOption = $(this).find("option:selected").val();
            if (selectedOption){
                $(".split_select option[value=" + selectedOption + "]").hide();
            }
            total.push();
        });
    });

    $(document).on('click', '.addNewrowNon', function() {
        var lastTr = $("#splitNonDebtorModal .box-body table tbody tr:last-child").attr("data-id");
        var clonedRow = $("#splitNonDebtorModal .box-body table tbody tr:first").clone();
        let newId = parseInt(lastTr)+1;
        clonedRow.attr('data-id', newId);
        clonedRow.find("input").val("0").attr('name', 'split_amount['+newId+']');
        clonedRow.find("select").val("").attr('name', 'split_debtor['+newId+']');
        clonedRow.find("button").val("").attr('data-id', newId);
        $("#splitNonDebtorModal .box-body table tbody").append(clonedRow);
        
        total=[];
        $('.split_select').each(function(key, value){
            let selectedOption = $(this).find("option:selected").val();
            if (selectedOption){
                $(".split_select option[value=" + selectedOption + "]").hide();
            }
            total.push();
        });
    });

    $(document).on('click', '.deleteparent', function() {
        let id = $(this).attr("data-id");
        let dataType = $(this).attr('data-type');
            let totalType = '';
            if (dataType=='nonDebtor') {
                totalModal = '#splitNonDebtorModalTotal';
                splitTotal = '#splitNonDebtorTotal';
                footTotal = '#totalNonDebtor';
            } else {
                totalModal = '#splitModalTotal';
                splitTotal = '#splitTotal';
                footTotal = '#total';
            }
            
        if (id !=1) {
            $(this).parents('tr').remove();
        }
        sumAmountSplit(totalModal,splitTotal,footTotal); 
    });
    

    function getBinUsers(id,bin){
        var url = "{{ route('stock-debtors.split.users',[':id',':bin']) }}";
            url = url.replace(':id', id);
            url = url.replace(':bin', bin);
        return $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json'
                });
    }

    function getNonDebtorUsers(){
        var url = "{{ route('stock-debtors.split.users.non_debtors') }}";
        return $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json'
                });
    }

    function sumAmountSplit(totalModal,splitTotal,footTotal) {
            let total = 0;
            $('.amount-split').each(function() {
                let value = parseFloat($(this).val()) || 0;
                total += value;
            });
            $(splitTotal).text(formatNumber(total));
            let topTotal = $(footTotal).val();
            let newTotal = topTotal-total;
            console.log(totalModal);
            $(totalModal).text(formatNumber(newTotal));
            if (newTotal >= 0) {
                $('#splitErrorMessage').hide();
                $('#splitConfirmButton').show();
            } else {
                $('#splitErrorMessage').show();
                $('#splitConfirmButton').hide();
            }
    }
    
    function formatNumber(number) {
                // Fix to 2 decimal places
                let fixedNumber = number.toFixed(2);

                // Add thousand separators
                let parts = fixedNumber.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                return parts.join('.');
    }

    function splitNonDebtor(id,total,name,bin)
    {
        $('#splitNonDebtorErrorMessage').hide();
        $('#splitNonDebtorTitle').html('Split to Non Debtor ('+name+')');
        $('#debtorNonDebtor').val(id);
        var amount = total.replace(/,/g, '')
        console.log(amount);
        $('#totalNonDebtor').val(amount);
        $('#splitNonDebtorModalTotal').html(total);
        $('#splitNonDebtorModal').modal();
        $('#splitNonDebtorTotal').text(0);
        const $splitBody = $('#splitNonDebtorModal .box-body table tbody');
        $splitBody.empty();
        getNonDebtorUsers().done(function(response) {
            let row = `
            <tr data-id="1">
                <td>
                    <select class="form-control select3 split_select" name="split_debtor[1]">
                    <option value="">Choose Users</option>`;
                    response.forEach(item => {
                        row += `<option value="${item.id}">${item.name}</option>`;
                    });
                    row += `</select>
                    </td>
                <td><input type="number" name="split_amount[1]" class="form-control amount-split" data-type="nonDebtor" value="0" /></td>
                <td><button type="button" data-id="1" class="btn btn-danger btn-sm deleteparent" data-type="nonDebtor" style="background-color: transparent !important; border:none; color:red !important;"><i class="fas fa-trash" style="color:red;" aria-hidden="true"></i></button></td>
            </tr>
            `;
            $splitBody.append(row);
            
        }).fail(function(data) {
            console.log(data);
            console.error('Failed to fetch dates.');
        });
    }
</script>
@endpush
