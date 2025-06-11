@extends('layouts.admin.admin')

@section('content')

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
      <div class="box-header with-border no-padding-h-b">
        <div class="col-sm-12">
          <form action="" method="get">
            <div class="form-group col-sm-4">
                <select name="item" class="form-control destination_items inventoryItems" id="inventoryItems">
                    <option value="" disabled selected></option>
                    @foreach($inventoryItems as $item)
                        <option value="{{ $item->id }}" @if(request()->item == $item->id) selected @endif> {{ $item->title }} </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-4">
                <select name="supplier" class="form-control suppliers" id="supplier-id">
                    <option value="" disabled selected></option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @if(request()->supplier == $supplier->id) selected @endif> {{ $supplier->name }} </option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary col-sm-2">Filter</button>
            {{-- <a class="btn btn-info ml-12" href="{!! route('maintain-items.item_price_pending_list') !!}">Clear </a> --}}

        </form>
          <div align="right">
            <a href="{{route('maintain-items.standard.cost',['block_all'=>true])}}" class="btn btn-warning">Block
              All</a>
            <a href="{{route('maintain-items.standard.cost',['un_block_all'=>true])}}" class="btn btn-danger">Un-Block
              All</a>
          </div>
        </div>
        <br>
        @include('message')
        <div class="col-md-12 no-padding-h">
          <table class="table table-bordered table-hover" id="create_datatable_25">
            <thead>
            <tr>
              <th>S.No.</th>

              <th>Stock ID Code</th>
              <th>Title</th>

              <th>Standard Cost</th>

              <th>Prev. Standard Cost</th>

              <th>Cost Update Time</th>
              <th>Selling Price</th>
              <th>Is Blocked</th>

              <th class="noneedtoshort">Action</th>

              <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

            </tr>
            </thead>
            <tbody>
            @if(isset($lists) && !empty($lists))
                <?php $b = 1; ?>
              @foreach($lists as $list)

                <tr>
                  <td>{!! $b !!}</td>

                  <td>{!! $list->stock_id_code !!}</td>
                  <td>{!! $list->title !!}</td>
                  <td>{!! $list->standard_cost !!}</td>
                  <td>{!! $list->prev_standard_cost !!}</td>
                  <td>{!! date("d-m-Y H:i:s", strtotime($list->cost_update_time)) !!}</td>
                  <td>{!! $list->selling_price !!}</td>
                  <td>{!! ($list->block_this ? 'Yes' : 'No') !!}</td>

                  <td class="action_crud">
                    @if($list->slug != 'mpesa')
                      @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                        <span>
                        <a title="Edit" href="{{ route('maintain-items.edit.standard.cost', $list->slug) }}">
                          <img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                        </a>
                        </span>

                      @endif

                    @endif


                  </td>


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
@section('uniquepagescript')
<div id="loader-on" style="
position: fixed;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
" class="loder">
  <div class="loader" id="loader-1"></div>
</div>
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>

<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">
    $("#supplier-id").select2({
        placeholder: 'Select supplier',
        allowClear: true
    });
   

    var destinated_item = function(){
            $(".destination_items").select2({
                placeholder: 'Select item',
                allowClear: true,
                ajax: {
                    url: "{{route('maintain-items.inventoryDropdown')}}",
                    dataType: 'json',
                    type: "GET",
                    data: function (term) {
                        return {
                            q: term.term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        }
        destinated_item();
</script>
<script type="text/javascript">
 $("#suppliers").select2({
  placeholder: 'Select Supplier'
  allowClear: true

 });
 $("#inventoryItems").select2({
  placeholder: 'Select Item'
  allowClear: true

 });



</script>
@endsection

@section('uniquepagestyle')

<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

