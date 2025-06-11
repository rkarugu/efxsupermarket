@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Create GL Reconcile </h3>
                    <div class="d-flex">
                        
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <form id="fetchPaymentForm" action="{{ route('gl-reconciliation.store') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="bank_account" class="control-label"> Bank Account </label>
                            <select name="bank_account" id="bank_account" class="form-control select2" required>
                                <option value="">Choose Bank Account</option>
                                @foreach ($bankAccounts as $item)
                                    <option value="{{$item->account_code}}">{{$item->account_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                              <label for="">Start Date</label>
                              <input type="date" name="start_date" id="start_date" class="form-control" placeholder="" required>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                              <label for="">Ending Date</label>
                              <input type="date" name="ending_date" id="ending_date" class="form-control" placeholder="" required>
                            </div>
                        </div>
                        
                        <div class="form-group col-sm-4 text-right">
                            <h2 style="margin-bottom: 0px;">Ksh. <span id="difference">0</span></h2>
                            <span>Difference</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                              <label for="">Beginning balance</label>              
                              <span class="form-control balance">0</span>                    
                              <input type="hidden" value="0" class="form-control compute-input" name="balance" id="balance" readonly>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">Ending Balance</label>      
                                <input type="text"  name="ending_balance" onkeyup="addThousandSeparator(this)" class="form-control compute-input" value="0" id="ending_balance" required>
                                <span class="error-message" style="color:red; display:none;"></span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12 d-flex justify-content-between">
                            <h4>Enter the service charge earned, if necessary</h4>
                            <button type="button" class="btn btn-primary" id="addServiceBtn" style="padding-top: 0px; padding-bottom: 0px; height: 30px;"><i class="fa-solid fa-plus"></i> Service Charge</button>
                        </div>
                    </div>
                    <div class="row" id="mainExpenses">
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label for="">Date</label>
                              <input type="date" name="expense_date[]" class="form-control expense_date" placeholder="" required>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label for="">Reference</label>
                              <input type="text" name="expense_reference[]" class="form-control" placeholder="" required>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                              <label for="">Service charge</label>
                              <input type="text" value="0" name="expense_charge[]" class="form-control compute-input charge-input" onkeyup="addThousandSeparator(this)" required>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Expense account</label>                                  
                                <select class="form-control select2" id="expense_account" name="expense_account[]" required>
                                    <option value="">Choose Expense</option>
                                    @foreach ($accounts as $expense)
                                        <option value="{{ $expense->id }}">{{ $expense->account_name }} ({{ $expense->account_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1"><button type="button" class="btn btn-link deleteInterestBtn text-danger" style="margin-top:25px; color:red;"><i class="fa-solid fa-trash-can"></i></button></div>
                    </div>
                    <div id="moreServices"></div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12 d-flex justify-content-between">
                            <h4>Enter the interest earned, if necessary</h4>
                            <button type="button" class="btn btn-primary" id="addInterestBtn" style="padding-top: 0px; padding-bottom: 0px; height: 30px;"><i class="fa-solid fa-plus"></i> Interest</button>
                        </div>
                    </div>
                    <div class="row" id="mainInterests">
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label for="">Date</label>
                              <input type="date" name="income_date[]" class="form-control interest_date" placeholder="" required>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label for="">Reference</label>
                              <input type="text" name="income_reference[]" class="form-control" placeholder="" required>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                              <label for="">Interest Earned</label>          
                              <input type="text" value="0" name="income_earned[]" class="form-control compute-input interest-input" onkeyup="addThousandSeparator(this)" required>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Income account</label>                                  
                                <select class="form-control select2" name="income_account[]" required>
                                    <option value="">Choose Income</option>
                                    @foreach ($accounts as $income)
                                        <option value="{{ $income->id }}">{{ $income->account_name }} ({{ $income->account_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1"><button type="button" class="btn btn-link deleteInterestBtn text-danger" style="margin-top:25px; color:red;"><i class="fa-solid fa-trash-can"></i></button></div>
                    </div>
                    <div id="moreInterests"></div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-solid fa-save"></i> Reconcile</button>
                </form>
            </div>
        </div>
    </section>

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
        $('.select2').select2();
        $('#start_date').on('change', function() {
            var startDate = $(this).val();
            
            // Set the minimum date for the end date input to the selected start date
            $('#ending_date').attr('min', startDate);
            $('.interest_date').attr('min', startDate);
            $('.expense_date').attr('min', startDate);

        });
        $('#ending_date').on('change', function() {
            var endDate = $(this).val();
            
            // Set the maximum date for the start date input to the selected end date
            $('#start_date').attr('max', endDate);
            $('.interest_date').attr('max', endDate);
            $('.expense_date').attr('max', endDate);
        });
        
        $('#addServiceBtn').click(function() {

            var newRow = `<div class="row" id="mainExpenses">
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label for="">Date</label>
                              <input type="date" name="expense_date[]" class="form-control expense_date" placeholder="" required>
                              <span class="error-message" style="color:red; display:none;"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label for="">Reference</label>
                              <input type="text" name="expense_reference[]" class="form-control" required>
                              <span class="error-message" style="color:red; display:none;"></span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                              <label for="">Service charge</label>
                              <input type="text" value="0" name="expense_charge[]" class="form-control charge-input" onkeyup="getDifference()" onkeyup="addThousandSeparator(this)" required>
                              <span class="error-message" style="color:red; display:none;"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Expense account</label>                                  
                                <select class="form-control select2" id="expense_account" name="expense_account[]" required>
                                    <option value="">Choose Expense</option>
                                    @foreach ($accounts as $expense)
                                        <option value="{{ $expense->id }}">{{ $expense->account_name }} ({{ $expense->account_code }})</option>
                                    @endforeach
                                </select>
                                <span class="error-message" style="color:red; display:none;"></span>
                            </div>
                        </div>
                        <div class="col-sm-1"><button type="button" class="btn btn-link deleteServiceBtn text-danger" style="margin-top:25px; color:red;"><i class="fa-solid fa-trash-can"></i></button></div>
                    </div>`;

            $('#moreServices').append(newRow);
            $('#moreServices').find('.select2').last().select2();
        });

        $('#addInterestBtn').click(function() {

            var newRow = `<div class="row" id="mainInterests">
                <div class="col-sm-3">
                            <div class="form-group">
                              <label for="">Date</label>
                              <input type="date" name="income_date[]" class="form-control interest_date" placeholder="" required>
                              <span class="error-message" style="color:red; display:none;"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label for="">Reference</label>           
                              <input type="text" name="income_reference[]" class="form-control" required>
                              <span class="error-message" style="color:red; display:none;"></span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                              <label for="">Interest Earned</label>           
                              <input type="text" value="0" name="income_earned[]" class="form-control interest-input" onkeyup="getDifference()" onkeyup="addThousandSeparator(this)" required>
                              <span class="error-message" style="color:red; display:none;"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Income account</label>                                  
                                <select class="form-control select2" name="income_account[]" required>
                                    <option value="">Choose Income</option>
                                    @foreach ($accounts as $income)
                                        <option value="{{ $income->id }}">{{ $income->account_name }} ({{ $income->account_code }})</option>
                                    @endforeach
                                </select>
                                <span class="error-message" style="color:red; display:none;"></span>
                            </div>
                        </div>
                        <div class="col-sm-1"><button type="button" class="btn btn-link deleteInterestBtn text-danger" style="margin-top:25px; color:red;"><i class="fa-solid fa-trash-can"></i></button></div>
                    </div>`;

            $('#moreInterests').append(newRow);
            $('#moreInterests').find('.select2').last().select2();
        });

        // Handle delete button click
        $(document).on('click', '.deleteServiceBtn', function() {
            $(this).closest('.row').remove();
            getDifference();
        });
        
        // Handle delete button click
        $(document).on('click', '.deleteInterestBtn', function() {
            $(this).closest('.row').remove();
            getDifference();
        });


        $('#bank_account').change(function(e){
                e.preventDefault();
                console.log('called');
                getBeginBalance();
        });
        $('#start_date').change(function(e){
                e.preventDefault();
                getBeginBalance();
        });
        $('#ending_date').change(function(e){
                e.preventDefault();
                getBeginBalance();
        });
        $('.compute-input').on('input', function() {
            getDifference();
        });
        
        $('#fetchPaymentForm').on('submit', function(e) {
            let isValid = true;

            // Reset all error messages
            $('.error-message').hide().text('');

            // Loop through each required field
            $(this).find('input[required]').each(function() {
                if ($(this).val().trim() === '') {
                    $(this).css('border-color', 'red');
                    $(this).next('.error-message').text('This field is required.').show();
                    isValid = false;
                } else {
                    $(this).css('border-color', '#d2d6de');
                }
            });

            // Loop through each required select field (handling select2)
            $(this).find('select[required]').each(function() {
                if ($(this).val().trim() === '') {
                    $(this).next('.select2-container').find('.select2-selection').css('border-color', 'red');
                    $(this).nextAll('.error-message').text('This field is required.').show();
                    isValid = false;
                } else {
                    $(this).next('.select2-container').find('.select2-selection').css('border-color', '#d2d6de');
                }
            });

            // Prevent form submission if validation fails
            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    function getBeginBalance()
    {
        var id = $('#bank_account').val();
        var start_date = $('#start_date').val();
        var end_date = $('#ending_date').val();
        console.log(id);
        console.log(end_date);
        if (id && start_date && end_date) {
            $.ajax({
            method:'get',
            url:'{{route("gl-reconciliation.get_bank_balance")}}',
            data:{
                'account':id,
                'start_date': start_date,
                'end_date':end_date,
            },
            success:function(suc){
                $('.balance').html(parseFloat(suc.amount).toLocaleString());
                $('#balance').val(suc.amount);
                getDifference();
            }
        });
        }
    }
    
    let typingTimeout;
    function addThousandSeparator(input) {
        clearTimeout(typingTimeout);
        var val = $(input).val().replace(/,/g, '');
        if(isNaN(val) || val == ""){
            return false;
        }
        typingTimeout = setTimeout(() => {
            $(input).parent().find('.new_val').val(val);
            $(input).val(parseFloat(val).toLocaleString());
        }, 500);
    }

    function getDifference()
    {
        let beginningBalance=0;

        var begining_balance = Math.abs($('#balance').val());
        
        var ending_balance = $('#ending_balance').val();
        ending_balance = ending_balance.replace(/,/g, '')

        let totalExpense=0;
        $('.charge-input').each(function() {
            var value = $(this).val() || 0; // Get the value or default to 0
            if (value.includes(',')) {
                value = value.replace(/,/g, '');
            }

            totalExpense += value; // Add the value to the total
        });
        let totalIncome=0;
        $('.interest-input').each(function() {
            var value = $(this).val() || 0; // Get the value or default to 0
            if (value.includes(',')) {
                value = value.replace(/,/g, '');
            }

            totalIncome += value; // Add the value to the total
        });
        console.log(totalExpense);
        console.log(totalIncome);
        var cleared_bal = begining_balance - (parseFloat(totalIncome)-parseFloat(totalExpense));
        let diff= parseFloat(ending_balance - cleared_bal)
        console.log(diff);
        $('#difference').html(formatNumber(parseFloat(ending_balance - cleared_bal)));
    }

    function formatNumber(number) {
                // Fix to 2 decimal places
                let fixedNumber = number.toFixed(2);

                // Add thousand separators
                let parts = fixedNumber.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                return parts.join('.');
    }
</script>
@endsection