
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            <div>
                                <form action="{{route('store-c-issue.stock-movements',['stockIdCode'=>$StockIdCode])}}" method="get">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                              <label for="">Stock Movement From</label>
                                              <input type="date" name="from" id="from" class="form-control" value="{{request()->from}}" placeholder="" aria-describedby="helpId" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                              <label for="">Stock Movement To</label>
                                              <input type="date" name="to" id="to" class="form-control" value="{{request()->to}}" aria-describedby="helpId" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                              <label for="">Location</label>
                                              <select type="date" name="location" id="location" class="form-control mlselec6t">
                                                  @foreach ($location as $item)
                                                  <option value="{{$item->id}}" {{(request()->location && request()->location == $item->id) ? 'selected' : NULL}}>{{$item->location_name}}</option>
                                                  @endforeach
                                              </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <a style="float:right;" class="btn btn-danger" href="{{route('store-c-issue.inventoryItems')}}">Back</a>
                                            <button type="submit" value="filter" name="type" class="btn btn-warning">Filter</button>
                                            <button type="submit" value="pdf" name="type" class="btn btn-primary">Stock Card</button>
                                            <button type="button" value="print" name="type" onclick="printStockCard(this); return false;" class="btn btn-default"><i class="fa fa-print" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th  >Date</th>
                                       
                                        <th  >User Name</th>
                                        <th  >Store Location</th>
                                         <th  >Qty In</th>
                                         <th  >Qty Out</th>
                                         <th  >New QOH</th>
                                          <th  >To Store</th>
                                             <th  >Document No</th>
                                          <th  >Manual Doc No</th>
                                          
                                          {{-- <th  class="noneedtoshort" >Action</th> --}}

                                        
                                          
                                        
                                          
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! date('d/M/Y',strtotime(@$list->created_at)) !!}</td>
                                                <td>{!! ucfirst(@$list->getRelatedUser->name) !!}</td>
                                                <td>{!! isset($list->getLocationOfStore->location_name) ? ucfirst($list->getLocationOfStore->location_name) : '' !!}</td>
                                                <td>{!! (($list->qauntity >= 0) ? +$list->qauntity : NULL) !!}</td>
                                                <td>{!! (($list->qauntity < 0) ? -$list->qauntity : NULL) !!}</td>
                                                <td>{!! @$list->new_qoh !!}</td>
                                                <td>{!! @$list->getRequisition->getRelatedToLocationAndStore->location_name !!}</td>
                                                 <td>{!! @$list->document_no !!}</td>
                                                <td>{!! @$list->getRequisition->manual_doc_no !!}</td>
                                            </tr>
                                           <?php $b++; ?>
                                        @endforeach
                                    @endif


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<style type="text/css">
    .select2{
      width: 100% !important;
     }
     #create_datatable tr:nth-child(even) td{
         background: #ddd;
     }
</style>
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script>
    $(function () {
        $(".mlselec6t").select2();
    });
    function printStockCard(input) { 
        var url = "{{route('store-c-issue.stock-movements',['stockIdCode'=>$StockIdCode])}}?"+$(input).parents('form').serialize()+'&type=print';
        print_this(url);

    }
</script>
@endsection
