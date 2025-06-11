@extends('layouts.admin.admin')
@section('content')
<section class="content" style="padding-bottom:0px;">
    <div class="box box-primary" style="margin-bottom: 0px;">
        <div class="box-header with-border">
            <div class="box-header-flex">
                <h4 class="box-title" style="font-weight: 500;"> Create Bulk Message </h4>
            </div>
        </div>
        <div class="session-message-container">
            @include('message')
        </div>
        <form id="bulkSmsSave" action="{{route('bulk-sms.save')}}" method="POST">
            @csrf
            <div class="row">
                <div class="col-sm-6">
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="title" class="col-sm-5 text-left" style="padding-left: 0px;">Title:</label>
                            <div class="col-sm-7">
                                <input type="text" id="title" name="title" class="form-control" oninput="this.value = this.value.toUpperCase()">
                                <span class="error-message" style="color:red; display:none;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="box-body" id="branch_body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="branch" class="col-sm-5 " style="padding-left: 0px;">Branch:</label>
                            <div class="col-sm-7">
                                <select name="branch" id="branch" class="form-control select2" required>
                                    <option value="all">All Branches</option>
                                    @foreach (getBranchesDropdown() as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}</option>
                                    @endforeach
                                </select>                                       
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="contact_group" class="col-sm-5 " style="padding-left: 0px;">Contact Group:</label>
                            <div class="col-sm-7">
                                <select name="contact_group" id="contact_group" class="form-control select2" required>
                                    <option value="">Choose Contact Group</option>
                                    <option value="Suppliers">Suppliers</option>
                                    <option value="Employees">Employees</option>
                                    <option value="Customers">Customers</option>
                                </select>   
                                <span class="error-message" style="color:red; display:none;"></span>
                                <div class="text-right" id="all_employees_body">
                                    <input type="checkbox" name="all_employees" id="all_employees"> All Employees
                                </div>  
                                <div class="text-right" id="all_customers_body">
                                    <input type="checkbox" name="all_customers" id="all_customers"> All Customers
                                </div>  
                                <div class="text-right" id="all_suppliers_body">
                                    <input type="checkbox" name="all_suppliers" id="all_suppliers"> All Suppliers
                                </div>            
                                                        
                            </div>
                        </div>
                    </div>
                    <div class="box-body" id="supplier_body">
                        <div class="form-group">
                            <label for="suppliers" class="control-label col-md-5" style="padding-left: 0px;"> Suppliers </label>
                            <div class="col-md-7">
                                <select name="suppliers[]" id="suppliers" class="form-control select2" multiple>
                                    <option value="">Choose Suppliers</option>
                                    {{-- @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="box-body" id="role_body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="role" class="col-sm-5 " style="padding-left: 0px;">Role:</label>
                            <div class="col-sm-7">
                                <select name="role" id="role" class="form-control flex-grow-1 select2"
                                    style="margin-left:10px">
                                    <option value="">Choose Role</option>
                                    @foreach (getRoles() as $key => $role)
                                        <option value="{{$key}}">{{$role}}</option>
                                    @endforeach
                                </select>                                     
                            </div>
                        </div>
                    </div>

                    <div class="box-body" id="route_body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="route" class="col-sm-5 " style="padding-left: 0px;">Route:</label>
                            <div class="col-sm-7">
                                <select name="route" id="route" class="form-control flex-grow-1 select2"
                                    style="margin-left:10px">
                                    <option value="">Choose Route</option>
                                </select>                                     
                            </div>
                        </div>
                    </div>

                    <div class="box-body" id="employees_body">
                        <div class="form-group">
                            <label for="employees" class="control-label col-md-5" style="padding-left: 0px;"> Employees </label>
                            <div class="col-md-7">
                                <div class="text-right">
                                    <input type="checkbox" name="all_employees_role" id="all_employees_role"> <span id="all_employees_role_title">All Employees</span>
                                </div>
                                <div id="employees_select">
                                    <select name="employees[]" id="employees" class="form-control select2" multiple>
                                    </select>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <div class="box-body" id="customers_body">
                        <div class="form-group">
                            <label for="customers" class="control-label col-md-5" style="padding-left: 0px;"> Customers </label>
                            <div class="col-md-7">
                                <div class="text-right">
                                    <input type="checkbox" name="all_customers_route" id="all_customers_route"><span id="all_customers_route_title">All Customers</span> 
                                </div>
                                <div id="customers_select">
                                    <select name="customers[]" id="customers" class="form-control select2" multiple>
                                        <option value="">Choose Customers</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                   
                </div>
                <div class="col-sm-6">
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="issn" class="col-sm-5 text-left">ISSN:</label>
                            <div class="col-sm-7">
                                <select name="issn" id="issn" class="select2 form-control" required>
                                    <option value="">Choose ISSN</option>
                                    <option value="{{ env("KANINI_SMS_SENDER_ID_2") }}">{{ env("KANINI_SMS_SENDER_ID_2") }}</option>
                                    <option value="{{ env("AIRTOUCH_ISSN") }}">{{ env("AIRTOUCH_ISSN") }}</option>
                                    <option value="{{ env("KANINI_SMS_SENDER_ID") }}">{{ env("KANINI_SMS_SENDER_ID") }}</option>
                                </select>
                                <span class="error-message" style="color:red; display:none;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="message" class="col-sm-5 text-left">Message:</label>
                            <div class="col-sm-7">
                                <textarea name="message" id="message" class="form-control" style="min-height: 150px;font-size:12px;height:auto;" required></textarea>  
                                <span class="error-message" style="color:red; display:none;"></span>  
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right col-sm-12">
                    <div class="box-body">
                        <button type="button" id="send" class="btn btn-primary">
                            Send
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<div class="modal fade" id="bulkSmsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title" id="splitTitle"> Bulk Sms</h3>
                    <div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            <form id="split-form" method="POST">
                @csrf
                <div class="box-body" id="bulkSmsMessage">
                    
                </div>
                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <span class="btn btn-primary" id="sendMessage">Send Message</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<style>
    .select2.select2-container.select2-container--default
    {
        width: 100% !important;
    }

</style>
@endsection
@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');
        $('.select2').select2();
        clearInputs();
    
        $('#contact_group').change(function(){
            clearInputs();
            $('#customers_body').hide();
            let selected = $(this).val();
            if (selected == 'Suppliers') {
                $('#supplier_body').show();
                $('#all_suppliers_body').show();
                getSuppliers();
            }
            if (selected == 'Employees') {
                // $('#branch_body').show();
                $('#role_body').show();
                $('#all_employees_body').show();
            }
            if (selected == 'Customers') {
                // $('#branch_body').show();
                $('#route_body').show();
                $('#all_customers_body').show();
            }
        });

        $('#all_suppliers').change(function()
        {
            if ($(this).is(':checked')) {
                $('#supplier_body').hide();
                $("#suppliers").val("").change();
            } else{
                $('#supplier_body').show();
            }
        });

        $('#all_employees').change(function()
        {
            if ($(this).is(':checked')) {
                // $('#branch_body').hide();
                // $("#branch").val("").change();
                $('#role_body').hide();
                $("#role").val("").change();
                $('#employees_body').hide();
                $('#employees_select').hide();
                $("#employees").val("").change();
                $('#all_employees_role').prop( "checked", false );
            } else {
                // $('#branch_body').show();
                $('#role_body').show();
                $('#employees_select').show();
            }
        });

        $('#all_employees_role').change(function()
        {
            if ($(this).is(':checked')) {
                $('#employees_select').hide();
                $("#employees").val("").change();
            } else {
                $('#employees_select').show();
            }
        });

        $('#all_customers').change(function()
        {
            if ($(this).is(':checked')) {
                // $('#branch_body').hide();
                // $("#branch").val("").change();
                $('#route_body').hide();
                $("#route").val("").change();
                $('#customers_body').hide();
                $('#customers_select').hide();
                $("#customers").val("").change();
                $('#all_customers_route').prop( "checked", false );
            } else {
                // $('#branch_body').show();
                $('#route_body').show();
                $('#customers_body').show();
                $('#customers_select').show();
            }
        });

        $('#all_customers_role').change(function()
        {
            if ($(this).is(':checked')) {
                $('#customers_select').hide();
                $("#customers").val("").change();
            } else {
                $('#customers_select').show();
            }
        });

        $("#branch").change(function() {
            let branch = $(this).val();
            var actionUrl = "{{ route('bulk-sms.routes', ['branch']) }}";
            actionUrl = actionUrl.replace('branch', branch);
            clearInputs();
            $("#contact_group").val("").change();
            
            $.ajax({
                url: actionUrl,
                success: function(data) {
                    $("#route").html(new Option('Please Select', '', false, false));
                    var res = data.routes.map(function(item) {
                        let option = new Option(item.route_name, item.id, false,
                            false)
                        $("#route").append(option)
                    });
                }
            });
        });

        $("#route").change(function() {
            let route = $(this).val();
            if(route){
                if ($('#contact_group').val() == 'Customers') {
                    getCustomers();
                    $('#customers_body').show();
                    $('#all_customers_route_title').html(' All '+$( "#route option:selected" ).text()+' Customers');
                } else{
                    $('#customers_body').hide();
                    $("#customers").val("").change();
                }
            }
        });

        $("#role").change(function() {
            if ($('#contact_group').val() == 'Employees') {
                getEmployees();
                $('#employees_body').show();
                $('#all_employees_role_title').html(' All '+$( "#role option:selected" ).text()+' Employees');
            } else{
                $('#employees_body').hide();
                $("#employees").val("").change();
            }
        });

        $("#send").on('click',function(e){
            e.preventDefault();
            let errors=0;
            if ($.trim($("#title").val()) == "") {
                $("#title").css('border-color','red');
                $("#title").next('.error-message').text('Title is required.').show();
                errors ++;
            } else{
                $("#title").css('border-color','#d2d6de');
            }
            
            if (!$("#contact_group").val()) {
                $("#contact_group").css('border-color','red');
                $("#contact_group").next('.error-message').text('Contact Group is required.').show();
                errors ++;
            } else {
                $("#contact_group").css('border-color','#d2d6de');
            }
            if ($.trim($("#issn").val()) == "") {
                $("#issn").css('border-color','red');
                $("#issn").next('.error-message').text('ISSN is required.').show();
                errors ++;
            } else{
                $("#issn").css('border-color','#d2d6de');
            }
            if ($.trim($("#message").val()) == "") {
                $("#message").css('border-color','red');
                $("#message").next('.error-message').text('Message is required.').show();
                errors ++;
            } else{
                $("#message").css('border-color','#d2d6de');
            }
            
            if (Number(errors) <= 0) { 
                let message=$("#title").val()+"<br>"+$("#message").val();
                $('#bulkSmsMessage').html(message);
                $('#bulkSmsModal').modal();
            }
        });

        $("#sendMessage").on('click', function(){
            $("#bulkSmsSave").submit();
        })
    }); 
    
    function clearInputs() {
        // $('#branch_body').hide();
        // $("#branch").val("").change();
        $('#route_body').hide();
        $("#route").val("").change();
        $('#supplier_body').hide();
        $("#suppliers").val("").change();
        $('#role_body').hide();
        $("#role").val("").change();
        $('#employees_body').hide();
        $("#employees").val("").change();
        $('#customers_body').hide();
        $("#customers").val("").change();

        $('#all_employees_body').hide();
        $('#all_employees').prop( "checked", false );
        $('#all_customers_body').hide();
        $('#all_customers').prop( "checked", false );
        $('#all_suppliers_body').hide();
        $('#all_suppliers').prop( "checked", false );
    }

    function getEmployees()
    {
        let branch = $('#branch').val();
        let route = $('#route').val();
        let role = $('#role').val();

        $.ajax({
            url: "{{ route('bulk-sms.employee_info') }}",
            data: {
                branch: branch,
                route: route,
                role: role,
            },
            success: function(data) {
                $("#employees").prop('disabled', false);
                $("#all_employees").prop('disabled', false);
                $("#employees").html(new Option('Choose Employee', '', false, false));
                var res = data.map(function(item) {
                    let option = new Option(item.name, item.id, false,
                        false)
                    $("#employees").append(option)
                });
            }
        });
    }

    function getCustomers()
    {
        // let branch = $('#branch').val();
        let route = $('#route').val();

        $.ajax({
            url: "{{ route('bulk-sms.customer_info') }}",
            data: {
                route: route,
            },
            success: function(data) {
                $("#customers").html(new Option('Choose Customers', '', false, false));
                var res = data.map(function(item) {
                    let option = new Option(item.name, item.id, false,
                        false)
                    $("#customers").append(option)
                });
            }
        });
    }

    function getSuppliers()
    {
        let branch = $('#branch').val();
        var actionUrl = "{{ route('bulk-sms.supplier_info', ['branch']) }}";
        actionUrl = actionUrl.replace('branch', branch);

        $.ajax({
            url: actionUrl,
            data: {
                
            },
            success: function(data) {
                $("#suppliers").html(new Option('Choose Supplier', '', false, false));
                var res = data.map(function(item) {
                    let option = new Option(item.name, item.id, false,
                        false)
                    $("#suppliers").append(option)
                });
            }
        });
    }
</script>
@endsection