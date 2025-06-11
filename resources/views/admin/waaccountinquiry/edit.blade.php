@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight:500 !important;"> Edit Transaction Account ({{ $transaction }})</h3>
                    <a href="{{ route("admin.account-inquiry.details",$transaction) }}" role="button" class="btn btn-primary"> <i class="fas fa-long-arrow-alt-left"></i> Back </a>
                </div>
            </div>
            <div class="box-header with-border no-padding-h-b">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    
                        @php
                            $account_codes =  getChartOfAccountsList();
                        @endphp
                        <table class="table table-bordered table-sm table-hover w-100">
                            <tr>
                                <th>Branch</th>
                                <th>GL Account</th>
                                <th>Transaction No</th>
                                <th>Date</th>                           
                                <th>Narrative</th>
                                <th>Period</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Action</th>
                            </tr>

                            @foreach ($record as $row)
                                <tr>
                                    <td>{!! (isset($row->restaurant->name)) ? $row->restaurant->name : '----' !!}</td>
                                    <td id="tdAccount_{{$row->id}}">{!! isset($account_codes[$row->account]) ? $account_codes[$row->account] : '' !!} ({!! $row->account !!})</td>
                                    <td>{!! $row->transaction_no !!}</td>
                                    <td>{!! getDateFormatted($row->trans_date) !!}</td>
                                
                                    @if($row->transaction_type=="Sales Invoice" && $row->amount > 0)
                                    @php
                                    $accountno = explode(':',$row->narrative);
                                    @endphp
                                    <td>{!! (count($accountno)> 1 ) ? $accountno[0] : '---' !!}</td>
                                    @else
                                    @php
                                    $accountno = explode('/',$row->narrative);
                                    @endphp
                                    <td>{!! (count($accountno)> 1 ) ? $accountno[1] : '---' !!}</td>
                                    @endif
                                    <td>{!! $row->period_number !!}</td>
                                    <td>{!! $row->amount>='0'?$row->amount:'' !!}</td>
                                    <td>{!! $row->amount<='0'?$row->amount:'' !!}</td>
                                    <td>
                                        <button type="button" class="btn btn-link" onclick="editBtn('{{$row->id}}','{{$row->amount}}','{{$account_codes[$row->account]}}','{{$row->account}}')"><i class="fa-solid fa-pen-to-square"></i></button>
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <th colspan="7" class="text-right">Total</th>
                                <th>{{$positiveAMount}}</th>
                                <th>{{$negativeAMount}}</th>
                            </tr>
                        </table>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-solid fa-save"></i> Update</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="updateAccountModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title" id="approveModalTitle"> Edit Gl Account</h3>
    
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="updateApproveForm" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="">Account</label>
                                <input type="text" id="oldAccount" class="form-control" value="" disabled>
                            </div>
                            <div class="col-sm-6">
                                <label for="">Amount</label>
                                <input type="text" id="oldAmount" class="form-control" value="" disabled>
                            </div>
                            <div class="col-sm-12">
                                <label for="">New Account</label>
                                <select name="new_account" id="new_account" class="form-control select2">
                                    <option value="">Choose Account</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->account_code }}">{{ $account->account_name }} ({{ $account->account_code }})</option>
                                    @endforeach
                                </select>
                                <input type="hidden" value="" name="old_account">
                            </div>
                        </div>
                    </div>
    
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="" id="">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" id="updateBtn" class="btn btn-primary" data-id="0" data-dismiss="modal">Update</button>
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

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>

<script type="text/javascript">
    $(function () {
        $('body').addClass('sidebar-collapse');
        $('.select2').select2({
            dropdownParent: $('#updateAccountModal')
        });

        $('#updateBtn').on('click', function (e) {
                e.preventDefault();           
                var postData = { 
                    gl: $(this).data('id'),
                    account: $('#new_account').val(),
                };
                
                $.ajax({
                    url: "{{route('admin.account-inquiry.update')}}", 
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                    },
                    data: postData, 
                    success:function(out){
                        $(".remove_error").remove();
                        console.log(out);
                        if(out.result == 0) {
                            for(let i in out.errors) {
                                var id = i.split(".");
                                if(id && id[1]){
                                    $("[name='"+id[0]+"["+id[1]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                                }else
                                {
                                    $("[name='"+i+"']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                                    $("."+i).parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                                }
                            }
                        }
                        if(out.result === 1) {
                            form.successMessage(out.message);
                            $('#tdAccount_'+out.id).text(out.account);
                        }
                        if(out.result === -1) {
                            let errorMessage = '';
                                if (out.message) {
                                    errorMessage = out.message
                                }
                                form.errorMessage(errorMessage);
                        }
                    },
                    error:function(err)
                    {
                    $(".remove_error").remove();

                    let errorMessage = '';
                        if (err?.responseJSON?.errors) {
                            for (let key in err.responseJSON.errors) {
                                if (err.responseJSON.errors.hasOwnProperty(key)) {
                                    errorMessage += err.responseJSON.errors[key].join('<br>') + '<br>';
                                }
                            }
                        } else if (err?.responseJSON?.error) {
                            errorMessage = err.responseJSON.error
                        }else{
                            errorMessage = 'Something went wrong.'
                        }
                        form.errorMessage(errorMessage);
                    }
                });

            });
            
    });

    function editBtn(id,amount,account,code)
        {
            $('#updateBtn').data('id',id);
            $('#oldAmount').val(amount);
            $('#oldAccount').val(account);
            $('#new_account').val(code).change();
            $('#updateAccountModal').modal();
        }
    
</script>
@endsection