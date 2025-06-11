
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            <div style="border-bottom:1px solid rgb(184, 184, 184)">
                                <div class="row" >
                                    <div class="col-sm-12">
                                        <h4>{{$title}}</h4>
                                    </div>
                                    <div class="col-sm-12">
                                        <form action="" method="get">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                      <label for="">Date From</label>
                                                      <input type="date" name="date_from" id="date_from" value="{{$dateFrom}}" class="form-control" placeholder="" >
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                      <label for="">Date To</label>
                                                      <input type="date" name="date_to" id="date_to" value="{{$dateTo}}" class="form-control" placeholder="" >
                                                    </div>
                                                </div><br>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                      <input type="submit" class="btn btn-danger" name="filtered" value="Filter">  
                                                      <button type="submit" name="manage" value="pdf" class="btn-inline btn btn-danger  mt-4 float-right">Pdf</button>
                                                    </div>
                                                </div>

                                                <!-- <div class="col-sm-3">
                                                    <div class="form-group">
                                                       <button type="submit" class="btn btn-danger" name="manage-request" value="PDF"  >PDF</button>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                          
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Salesman</th>
                                            {{-- <th>Customer Name</th>
                                            <th>Phone No.</th>
                                            <th>Business</th>
                                            <th>Town</th>
                                            <th>Contact Person</th> --}}
                                            <th>Invoice Amount</th>
                                            <th>Return Amount</th>
                                            <th>Gross Sales Amount</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                         $total = 0;
                                         $grand_return = 0;
                                         $grand_gross = 0;
                                         @endphp
                                        @foreach ($lists as $item)   
                                            
                                            @php
                                                $total_sales= (isset($item->total_sales))?$item->total_sales:0;
                                                $total_return= (isset($item->total_return))?$item->total_return:0;

                                                $gross_total =  $total_sales -  $total_return;
                                                $grand_gross +=  $gross_total;
                                                 
                                                 
                                            @endphp                                                                                  
                                            <tr>
                                                <td>{{@$item->toStoreDetail->location_name}}</td>
                                               {{--  <td>{{$item->name}}</td>
                                                <td>{{$item->phone}}</td>
                                                <td>{{$item->bussiness_name}}</td>
                                                <td>{{$item->town}}</td>
                                                <td>{{$item->contact_person}}</td> --}}
                                                <td>{{ manageAmountFormat(@$item->total_sales)}}</td>
                                                <td>{{ manageAmountFormat(@$item->total_return) }}</td>
                                                <td>{{ manageAmountFormat($gross_total)}}</td>

                                                @php
                                                $total += $item->total_sales;
                                                $grand_return += $item->total_return;
                                                
                                                @endphp
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                             <th colspan="" style="text-align: right">
                                                 Total
                                             </th>
                                             <th>{{manageAmountFormat($total)}}</th>
                                             <th>{{manageAmountFormat($grand_return)}}</th>
                                             <th>{{manageAmountFormat($grand_gross)}}</th>
                                        </tr>
                                    </tfoot>
                                </table>

                                @if(request()->has('filtered'))
                                    {{$lists->appends(request()->input())->links()}}
                                @endif

                            </div>
                        </div>
                    </div>


    </section>
   
@endsection
