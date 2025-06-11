@extends('layouts.admin.admin')
@section('content')
<form action="{{route('maintain-items.postassignInventoryItems',$data->id)}}" method="POST" class="submitMe">  
    {{csrf_field()}}
    <input type="hidden" name="id" value="{{$data->id}}">
<!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">	                        
                @include('message')
                <div class="col-md-12 no-padding-h">
              
                    <h3 class="box-title">       <button type="button" class="btn btn-danger btn-sm addNewrow" ><i class="fa fa-plus" aria-hidden="true"></i></button> Assign Inventory Items - {{$data->title}} {{$data->stock_id_code}}</h3>
                    <div>
                        <span class="destination_item"></span>
                    </div>
                    <table class="table table-bordered table-hover assigneditems">
                        <thead>
                            <tr>                                
                                <th>
                                    Destination Item
                                </th>
                                <th>
                                    Conversion factor
                                </th>
                                <th>
                                    ##
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($data->destinated_items->isNotEmpty())
                                @foreach ($data->destinated_items as $key => $item)
                                    <tr>
                                        <td>
                                            <select name="destination_item[{{$key}}]" class="form-control destination_item destination_items">
                                                @if ($item->destinated_item)
                                                    <option value="{{$item->destinated_item->id}}">{{$item->destinated_item->title}}</option>                                                
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="conversion_factor[{{$key}}]" class="form-control conversion_factor" value="{{$item->conversion_factor}}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger deleteMe"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>
                                        <select name="destination_item[0]" class="form-control destination_item destination_items">
                                            
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="conversion_factor[0]" class="form-control conversion_factor" value="">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger deleteMe"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>                        
                    </table>
                    <br>
                    <button type="submit" class="btn btn-danger">Assign</button>
                    <button type="button" onclick="location.href='{{route('maintain-items.index')}}'" class="btn btn-danger">Cancel</button>
                </div>
            </div>
        </div>
    </section>   
    
</form>
@endsection
@section('uniquepagescript')

<link href="{{url('/')}}/assets/admin/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
<div id="loader-on" style="
position: absolute;
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
<script src="{{url('/')}}/public/js/sweetalert.js"></script>
<script src="{{url('/')}}/public/js/form.js"></script>
<script src="{{url('/')}}/assets/admin/bower_components/select2/dist/js/select2.full.min.js"></script>
    <script>
        $(document).on('click','.deleteMe',function () {
            $(this).parents('tr').remove();
            return false;
        });
        var item = '<tr>'+
                        '<td>'+
                            '<select name="destination_item[0]" class="form-control destination_item destination_items"></select>'+
                        '</td>'+
                        '<td>'+
                            '<input type="text" name="conversion_factor[0]" class="form-control conversion_factor">'+
                        '</td>'+
                        '<td>'+
                            '<button type="button" class="btn btn-danger deleteMe"><i class="fa fa-trash" aria-hidden="true"></i></button>'+
                        '</td>'+
                    '</tr>';
        $(document).on('click','.addNewrow',function () {
            $(".destination_items").select2('destroy');
            $('.assigneditems tbody').append(item);
            var assigneditems = $('.assigneditems tbody tr');
            $.each(assigneditems, function (indexInArray, valueOfElement) { 
                 $(this).find('.destination_item').attr('name','destination_item['+indexInArray+']');
                 $(this).find('.conversion_factor').attr('name','conversion_factor['+indexInArray+']');
            });
            destinated_item();
        });
        //maintain-items.inventoryDropdown

        var destinated_item = function(){
            $(".destination_items").select2({
                ajax: {
                    url: "{{route('maintain-items.inventoryDropdown',['id'=>$data->id])}}",
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

    
@endsection
