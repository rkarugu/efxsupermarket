@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {{ $title }} <small>({{$info->document_no}} -  {{$info->saleCenter->center->name}} )</small></h3>
                    <div class="d-flex">
                        <button class="btn btn-primary" style="margin-top:0px;" onclick="dispatchBtn()"> Process Dispatch </button>
                        <a href="{{ route('small-packs.store-loading-sheets') }}" class="btn btn-primary" style="margin-top:0px;margin-left:10px"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <table class="table table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Description</th>
                                <th>Item Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dispatch as $item)
                                <tr>
                                    <td>{{ $item->stock_id_code}}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->total_quantity }}</td>
                                </tr>
                            @endforeach    
                        </tbody>                        
                    </table>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="confirmApproveModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title" id="approveModalTitle"> Process Dispatch </h3>
    
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="processDispatchForm" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        <p>Are you sure you want to dispatch items {{ $info->createdBy->name }} ?</p>
                    </div>
    
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="bin_id" id="" value="{{ $binId }}">
                            <input type="hidden" name="dispatch_id" id="" value="{{ $dispatchId }}">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" id="confirmApproveBtn" class="btn btn-primary">Confirm</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('uniquepagestyle')
<link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }
    </style>
@endsection
@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });
            $('#confirmApproveBtn').on('click', function (e) {
                e.preventDefault();  
                console.log('yes');
                var postData = $('#processDispatchForm').serialize();   

                $.ajax({
                    url: "{{route('small-packs.process-dispatch')}}", 
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                    },
                    data: postData, 
                    success: function(response) {
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
                            setTimeout(
                            function() 
                            {                        
                                location.href = `{{ route('small-packs.store-loading-sheets') }}`;
                            }, 3000);
                            
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
    function dispatchBtn()
    {
            $('#confirmApproveModal').modal();
    }
            </script>
@endsection