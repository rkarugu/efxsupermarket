@extends('layouts.admin.admin')

@section('content')

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
      <div class="box-header with-border no-padding-h-b">
        <div class="col-sm-12">
          <div align="left">
            {{-- <a href="{{route('maintain-items.standard.cost',['block_all'=>true])}}" class="btn btn-warning">Block
              All</a>
            <a href="{{route('maintain-items.standard.cost',['un_block_all'=>true])}}" class="btn btn-danger">Un-Block
              All</a> --}}
              <h4 class="box-title">Manual Cost Change</h4>
          </div>
        </div>
      </div>
      <div class="box-body">

        <br>
        @include('message')
        <div class="col-md-12 no-padding-h">
          <table class="table table-bordered table-hover" id="create_datatable">
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
                        <a title="Edit" href="{{ route('maintain-items.manual-cost-change.editStandardCost', $list->slug) }}">
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
