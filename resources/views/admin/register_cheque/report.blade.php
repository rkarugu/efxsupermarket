@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3>
           
        </div>
         @include('message')
         <div class="box-body" style="padding-bottom:15px">
            <form action="{{route('register-cheque.report')}}" method="get">
              <input type="hidden" name="source" value="register-cheque">
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="">From date</label>
                      <input type="date" name="from" id="from" value="{{request()->from ?? date('Y-m-d')}}" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="">To</label>
                      <input type="date" name="to" id="to" value="{{request()->to ?? date('Y-m-d')}}" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="All" {{request()->status ? (request()->status == 'All' ? 'selected' : '') : 'selected'}}>All</option>
                            <option value="Registered" {{request()->status == 'Registered' ? 'selected' : ''}}>Registered</option>
                            <option value="Deposited" {{request()->status == 'Deposited' ? 'selected' : ''}}>Deposited</option>
                            <option value="Cleared" {{request()->status == 'Cleared' ? 'selected' : ''}}>Cleared</option>
                            <option value="Bounced" {{request()->status == 'Bounced' ? 'selected' : ''}}>Bounced</option>
                        </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="">&nbsp;</label>
                      
                      <button type="submit" class="btn btn-danger" name="manage" value="filter">Filter</button>
                      <button type="submit" class="btn btn-danger"  name="manage" value="pdf">PDF</button>
                    </div>
                  </div>
                </div>
              </form>
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover" >
                    <thead>
                        <tr>
                            <th>Date received</th>
                            <th>Salesman</th>
                            <th>Cheque no</th>
                            <th>Drawers name</th>
                            <th>Drawers bank</th>
                            <th>Cheque date</th>
                            <th>Bank deposited</th>
                              <th>Date Deposited</th>
                              <th>Status</th> 
                              <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                      @php
                          $total = 0;
                      @endphp
                        @foreach($data as $key => $item)
                        <tr>
                            <td>{{$item->date_received}}</td>
                            <td>{{@$item->salesman->location_name}}</td>
                            <td>{{$item->cheque_no}}</td>
                            <td>{{$item->drawers_name}}</td>
                            <td>{{$item->drawers_bank}}</td>
                            
                            <td>{{$item->cheque_date}}</td>
                            <td>{{$item->bank_deposited}}</td>                         
                           
                            <td>{{$item->deposited_date}}</td>
                            <td>{{@$item->status}}</td>
                            
                            <td>{{manageAmountFormat($item->amount)}}</td>
                            @php
                            $total += $item->amount;
                        @endphp
                           
                        </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                      <tr>
                          <th colspan="10" style="text-align: right">Grand Total : {{manageAmountFormat($total)}}</th>
                      </tr>
                    </tfoot>
                </table>

                {{$data->appends($_GET)->links()}}
            </div>
            </div>
    </div>
</section>

@endsection
@section('uniquepagescript')

@endsection