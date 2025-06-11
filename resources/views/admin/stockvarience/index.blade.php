
@extends('layouts.admin.admin')

@section('content')


<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <div align = "right">
                <?php if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') { ?>
                <a href = "{!! route('admin.stock-variance.add')!!}" class = "btn btn-success">Enter Stock Variance</a>
                <?php } ?>
            </div>
            @include('message')
            <div class="col-md-12 no-padding-h">

                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Date From</th>
                            <th>Date To</th>
                            <th>Location</th>
                            <th>Record Count</th>
                            <th class="noneedtoshort" >Action</th>

                            <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                        </tr>
                    </thead>
                    <tbody>
                       
                        <?php $b = 1; ?>
                        @foreach($data as $list)

                        <tr>
                            <td>{!! $b !!}</td>
                            <td>{!! $list->parent->start_date ?? NULL !!}</td>
                            <td>{!! $list->parent->end_date ?? NULL !!}</td>
                            <td>{!! $list->parent->location ?? NULL !!}</td>
                            <td>{!! $list->totalitems !!}</td>
                            

                            <td class = "action_crud">
                                <span>
                                    <a title="Print" href="{{ route('admin.stock-variance.report-pdf', $list->parent_id) }}">
                                        <i aria-hidden="true" class="fa  fa-file-pdf" style="font-size: 20px;"></i>
                                    </a>
                                </span>
                                <span>
                                    <a title="Print" href="{{ route('admin.stock-variance.report-excel', $list->parent_id) }}">
                                        <i aria-hidden="true" class="fa fa-file-excel" style="font-size: 20px;"></i>
                                    </a>
                                </span>
                            </td>
                            


                        </tr>
                        <?php $b++; ?>
                        @endforeach
                       


                    </tbody>
                </table>
            </div>
        </div>
    </div>


</section>

@endsection

@section('uniquepagescript')

@endsection