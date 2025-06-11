
@extends('layouts.admin.admin')

@section('content')
<?php 
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;

?>


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            
                            <form action="{{route('maintain-items.update-stock-status')}}" method="post" class="submitMe">
                            @csrf
                            <input type="hidden" name="inventory_id" value="{{$inventory->id}}">
                            <input type="hidden" name="stockIdCode" value="{{$inventory->stock_id_code}}">
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th width="10%">S.No.</th>
                                        <th width="10%"  >Store Location</th>
                                        <th width="10%"  >Quantity On Hand</th>
                                        <th width="10%"  >Max Stock</th>
                                        <th width="10%"  >Re-Order Level</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                <td>{!! ucfirst($list->location_name) !!}</td>
                                                <td>{!! isset($storeBiseQty[$list->id])?$storeBiseQty[$list->id]:'0' !!}</td>
                                                <td><input type="text" class="form-control" name="max_stock[{!! $list->id !!}]" value="{{$list->max_stock ?? 0}}"            
                                                   @if(!isset($my_permissions['maintain-items___edit-max-stock'])  && $user->role_id != 1)
                                                  readonly
                                                  @endif
                                                  ></td>
                                                <td><input type="text" class="form-control" name="re_order_level[{!! $list->id !!}]" value="{{$list->re_order_level ?? 0}}"
                                                  @if(!isset($my_permissions['maintain-items___edit-reorder-level']) && $user->role_id != 1)
                                                  readonly
                                                  @endif
                                                  ></td>
                                            </tr>
                                           <?php $b++; ?>
                                        @endforeach
                                    @endif


                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Stock Details</button>
                            </form>
                        </div>
                    </div>


    </section>
   
@endsection

@section('uniquepagestyle')
<style type="text/css">
   .select2{
     width: 100% !important;
    }
    #note{
      height: 60px !important;
    }
    .align_float_right
    {
      text-align:  right;
    }
    .textData table tr:hover,.SelectedLi{
      background:#000 !important;
      color:white !important;
      cursor: pointer !important;
    }

/* ALL LOADERS */

.loader{
  width: 100px;
  height: 100px;
  border-radius: 100%;
  position: relative;
  margin: 0 auto;
  top: 35%;
}

/* LOADER 1 */

#loader-1:before, #loader-1:after{
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 100%;
  border: 10px solid transparent;
  border-top-color: #3498db;
}

#loader-1:before{
  z-index: 100;
  animation: spin 1s infinite;
}

#loader-1:after{
  border: 10px solid #ccc;
}

@keyframes spin{
  0%{
    -webkit-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }

  100%{
    -webkit-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

    </style>
@endsection

@section('uniquepagescript')
<div id="loader-on" style="
position: fixed;
top: 0;
text-align: center;
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
@endsection
