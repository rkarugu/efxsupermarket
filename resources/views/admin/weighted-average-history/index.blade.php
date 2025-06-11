
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            <form action="" method="get">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="">From Date</label>
                                            <input type="date" class="form-control" name="from" value="{{request()->from ?? date('Y-m-d',strtotime('-7 Days'))}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="">To Date</label>
                                            <input type="date" class="form-control" name="to" value="{{request()->to ?? date('Y-m-d')}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="">Show Record</label>
                                            <select name="show" id="show" class="form-control">
                                                <option value="" selected>Show All</option>
                                                <option value="1" {{request()->show ? 'selected' : ""}}>Show Weight Different Items</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th width="5%">S.No.</th>
                                        <th>Date</th>
                                        <th>GRN No.</th>
                                        <th>LPO No.</th>
                                        <th>Item Code</th>
                                        <th>Item Description</th>
                                        <th>Opening Standard Cost</th>
                                        <th>Opening Qty</th>
                                        <th>Opening Value</th>
                                        <th>GRN Standard Cost</th>
                                        <th>GRN Qty</th>
                                        <th>GRN Value</th>
                                        <th>Total Value</th>
                                        <th>Total Inventory</th>
                                        <th>New Weighted Average</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        @foreach($lists as $key => $list)
                                         
                                            <tr>
                                                <td>{!! ++$key !!}</td>
                                                
                                                <td>{!! $list->date !!}</td>
                                                <td>{!! $list->grn_no !!}</td>
                                                <td>{!! @$list->lpo_no !!}</td>
                                                <td>{!! @$list->item_code !!}</td>
                                                <td>{!! @$list->item_description !!}</td>
                                                <td>{!! @$list->opening_standard_cost !!}</td>
                                                <td>{!! @$list->opening_qty !!}</td>
                                                <td>{!! @$list->opening_value !!}</td>
                                                <td>{!! @$list->grn_standard_cost !!}</td>
                                                <td>{!! @$list->grn_qty !!}</td>
                                                <td>{!! @$list->grn_value !!}</td>
                                                <td>{!! @$list->total_value !!}</td>
                                                <td>{!! @$list->total_inventory !!}</td>
                                                <td>{!! @$list->new_weighted_average !!}</td>
                                            </tr>
                                        @endforeach
                                    @endif


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
@endsection
