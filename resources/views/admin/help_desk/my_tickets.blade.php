@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight: 500 !important;"> {{$title}} </h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="d-flex">
                    <div class="row" style="min-width: 380px !important; margin-right:10px;">
                        <input type="hidden" id="startDate" name="from">
                        <input type="hidden" id="endDate" name="to">
                        <label class="col-sm-4">Select Dates</label>
                        <div class="reportRange col-sm-8">
                            <i class="fa fa-calendar" style="padding:8px"></i>
                            <span class="flex-grow-1" style="padding:8px"></span>
                            <i class="fa fa-caret-down" style="padding:8px"></i>
                        </div>
                    </div>
                    <div class="" style="margin-left:25px;">
                        <select class="form-control select2" id="priorityFilter">
                            <option value="all">Choose Priority</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                    <div class="" style="margin-left:10px;">
                        <select class="form-control select2" id="categoryFilter">
                            <option value="all">Choose Category</option>
                            @foreach ($categories as $category)
                                <option value="{{$category->id}}">{{$category->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <table class="table table-bordered" id="ticketsDataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Code</th>
                                <th>Subject</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Branch</th>
                                <th>Assignee</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>                        
                    </table>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="newTicketModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> New Ticket</h3>
    
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="newTicketForm" action="{{ route('help-desk.tickets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="module" class="control-label"> Module </label>
                                <select name="module" id="module" class="form-control mtselect">
                                    <option value="">Choose Module</option>
                                    <option value="Sales & Receivables">Sales & Receivables</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="priority" class="control-label"> Priority </label>
                                <select name="priority" id="priority" class="form-control mtselect">
                                    <option value="">Choose Priority</option>
                                    <option value="High">High</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Low">Low</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="module" class="control-label"> Subject </label>
                                <input type="text" class="form-control" name="subject">
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="module" class="control-label"> Attachment </label>
                                <input type="file" class="form-control" name="attachment">
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="message" class="control-label"> Message </label>
                                <textarea name="message" id="message" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
    
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" id="newTicketBtn" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
<style>
    .select2.select2-container.select2-container--default
    {
        width: 100% !important;
    }
    .reportRange {
        display: flex;
        align-content: center;
        justify-content: stretch;
        border: 1px solid #eee;
        cursor: pointer;
        height: 35px;
    }
    .alert-danger a,.alert-success a,.alert-info a {
            color: #fff
        }
</style>    
@endsection
@section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    
<script>
    var form = new Form();
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');
        $('.select2').select2();
        $('.mtselect').select2({
            dropdownParent: $('#newTicketModal')
        });
        let start = moment().subtract(30, 'days');
        let end = moment();
        $('.reportRange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            $("#startDate").val(start.format('YYYY-MM-DD'));
            $("#endDate").val(end.format('YYYY-MM-DD'));

            $('.reportRange').daterangepicker({
                startDate: start,
                endDate: end,
                alwaysShowCalendars: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(7, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            });
            $('.reportRange').on('apply.daterangepicker', function(ev, picker) {
                $("#startDate").val(picker.startDate.format('YYYY-MM-DD'));
                $("#endDate").val(picker.endDate.format('YYYY-MM-DD'));

                $('.reportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' + picker.endDate
                    .format('MMM D, YYYY'));
                $("#ticketsDataTable").DataTable().ajax.reload();
            });

        $('#startDate, #endDate, #priorityFilter, #categoryFilter').on('change', function() {
            let start_date = $("#startDate").val();
            let end_date = $("#endDate").val();
            $("#startDate").val(start_date);
            $("#endDate").val(end_date);
            $("#ticketsDataTable").DataTable().ajax.reload();
        });
        
        $("#ticketsDataTable").DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            pageLength: 100,
            ajax: {
                url: '{!! route('help-desk.my.tickets') !!}',
                data: function(data) {
                    data.start_date = $("#startDate").val();
                    data.end_date = $("#endDate").val();
                    data.status = '{{ request()->status }}';
                    data.priority = $("#priorityFilter").val();
                    data.category = $("#categoryFilter").val();
                }
            },
            columns: [
                { 
                    data: 'DT_RowIndex', 
                    name: 'DT_RowIndex', 
                    orderable: false, 
                    searchable: false 
                },
                { 
                    data: 'created_at', 
                    name: 'created_at', 
                },
                { 
                    data: 'code', 
                    name: 'code', 
                },
                { 
                    data: 'subject', 
                    name: 'subject', 
                },
                { 
                    data: 'category.title', 
                    name: 'category.title', 
                },
                { 
                    data: 'priority', 
                    name: 'priority', 
                },
                { 
                    data: 'branch.name', 
                    name: 'branch.name', 
                },
                { 
                    data: 'current_assignee.assignee.name', 
                    name: 'current_assignee.assignee.name', 
                },
                { 
                    data: 'current_status.status', 
                    name: 'current_status.status', 
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false
                }
            ], columnDefs: [

                {
                    targets: -1,
                    render: function (data, type, row, meta) {
                        if (type === 'display') {
                            var actions = '';
                            @if (can('show', 'help-desk-tickets'))
                                var url = "{{ route('help-desk.tickets.show',':id') }}";
                                url = url.replace(':id', row.id);
                                actions += `<a href="`+url+`" title="view"><i class="fa fa-solid fa-eye"></i></a>`;
                            @endif
                            return actions;
                        }
                        return data;
                    }
                }
            ],
        });

        $('#newTicketBtn').on('click', function (e) {
            e.preventDefault();
            var postData = new FormData($(this).parents('form')[0]);
            var url = $(this).parents('form').attr('action');
            postData.append('_token',$(document).find('input[name="_token"]').val());
            console.log(url);
            console.log(postData);
            $.ajax({
                url:url,
                data:postData,
                contentType: false,
                cache: false,
                processData: false,
                method:'POST',
                success:function(out){

                    $(".remove_error").remove();
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
                        $("#ticketsDataTable").DataTable().ajax.reload();
                        $('#newTicketModal').modal('hide');
                        form.successMessage(out.message);            
                        
                        
                    }
                    if(out.result === -1) {
                        form.errorMessage(out.message);
                    }
                },
                
                error:function(err)
                {
                    console.log(err);
                $('#loader-on').hide();
                    $(".remove_error").remove();
                    form.errorMessage('Something went wrong');							
                }
            });
        });
    });

        
    </script>
@endsection