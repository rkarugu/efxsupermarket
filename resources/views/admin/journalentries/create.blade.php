
@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
            @include('message')
            <form class="validate" role="form" method="POST" action="{{ route($model.'.store') }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                <?php

                use App\Model\WaNumerSeriesCode;
                use App\Model\WaJournalEntry;

                $journal_entry_no = getCodeWithNumberSeries('JOURNAL_ENTRY');
                $row = WaJournalEntry::select('journal_entry_no')->orderBy('id', 'desc')->first();
                if ($row) {
                    $string = $row->journal_entry_no;

                    // Extract the numeric portion of the string
                    $numericPart = intval(substr($string, strpos($string, '-') + 1));

                    // Increment the numeric part
                    $numericPart++;

                    // Combine the updated numeric part with the original non-numeric portion
                    $journal_entry_no = substr($string, 0, strpos($string, '-') + 1) . str_pad($numericPart, strlen($string) - strpos($string, '-') - 1, '0', STR_PAD_LEFT);
                }

                $date_to_process = date('Y-m-d');
                ?>

                <div class="row">
                    <div class="col-sm-6">

                        @php
                            $type = array(
                                "GL Account"=>"GL Account",
                                "Bank Account"=>"Bank Account",
                                "Customer Account"=>"Customer Account",
                                "Supplier Account"=>"Supplier Account"
                               );
                        @endphp

                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Account Type</label>
                                    <div class="col-sm-9">
                                        {!!Form::select('entry_type', $type,null, ['class' => 'form-control account_type','required'=>true  ])!!}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Account Name/No</label>
                                    <div class="col-sm-9">
                                        {!!Form::select('gl_account_id', getChartOfAccountsDropdown(), null, ['class' => 'form-control mlselect accountno','required'=>true,'placeholder' => 'Please select'  ])!!}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Debit</label>
                                    <div class="col-sm-4">


                                        {!! Form::number('debit',  0 , ['min'=>'0', 'class'=>'form-control','required'=>true]) !!}
                                    </div>
                                    <label for="inputEmail3" class="col-sm-1 control-label">Credit</label>
                                    <div class="col-sm-4">
                                        {!! Form::number('credit',  0 , [ 'min'=>'0','class'=>'form-control','required'=>true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Narrative</label>
                                    <div class="col-sm-9">
                                        {!! Form::text('narrative', null , ['maxlength'=>'255' ,'class'=>'form-control','required'=>true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">

                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Entry No.</label>
                                    <div class="col-sm-7">


                                        {!! Form::text('journal_entry_no',  $journal_entry_no , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Date to Process Journal</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('date_to_process', $date_to_process, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                                    <div class="col-sm-7">
                                        {!! Form::select('restaurant',$restroList, null,['placeholder'=>"Select Branch", 'required'=>true,'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Reference</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('reference', null , ['maxlength'=>'255' ,'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>


                </div>


            </form>
        </div>
    </section>



    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">


                <div class="col-md-12 no-padding-h table-responsive">
                    <h3 class="box-title">Journal Summary</h3>

                    <span id="requisitionitemtable">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                      <th>S.No.</th>
                                      <th>Account Type</th>
                                      <th>Account No</th>
                                      <th>Account Name</th>
                                      <th>Debit</th>
                                       <th>Credit</th>
                                        <th>Narrative</th>
                                      <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                       <tr>
                                      <td colspan="7">Do not have any item in list.</td>
                                      
                                    </tr>
                        
                                   


                                    </tbody>
                                </table>
                                </span>
                </div>


            </div>
        </div>


    </section>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')

    <script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.account_type').on('change', function () {
            var account_type = $(this).val();
            //      alert(account_type);
            $.ajax({
                url: "{{route('journal-entries.getAccountNo')}}?type=" + account_type,
                method: "GET",
                dataType: "json",
                success: function (response) {
                    console.log(JSON.stringify(response));
                    $('.accountno')
                        .empty();
                    $('.accountno')
                        .append($("<option></option>").attr("value", "")
                            .text("Please select"));
                    $.each(response.data, function (key, value) {
                        $('.accountno')
                            .append($("<option></option>")
                                .attr("value", key)
                                .text(value));
                    });
                }
            });

        });
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {

        $(".mlselect").select2();
});
</script>

@endsection


