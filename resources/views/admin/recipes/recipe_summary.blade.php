
@extends('layouts.admin.admin')

@section('content')
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                        <tr>
                            <th width="10%">S.No.</th>
                            <th width="10%"  >Ingredient</th>
                            <th width="10%"  >Unit</th>
                            <th width="10%"  >Material cost</th>
                            <th width="10%"  >Weight</th>
                            <th width="10%"  >Portion</th>
                            <th width="10%"  >Cost</th>
  
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($data) && !empty($data))
                        <?php $b = 1; ?>
                        @foreach($data as $lists)
	                        <tr>
	                            <td colspan="7">{!! isset($lists->title) ? $lists->title : '' !!}</td>
 	                        </tr>

	                        @foreach($lists->getAssociateIngredient as $list)
	                        <tr>
	                            <td>{!! $b !!}</td>
	                            <td>{!! isset($list->getAssociateItemDetail->title) ? $list->getAssociateItemDetail->title : '' !!}</td>
	                            <td>
	                                <?php
	                                echo isset($list->getAssociateItemDetail->getUnitOfMeausureDetail->title) ? $list->getAssociateItemDetail->getUnitOfMeausureDetail->title : ''
	                                ?>
	                            </td>
	                            <td>{!! @$list->material_cost !!}</td>
	                            <td>{!! @$list->weight !!}</td>
	                            <td>{!! @$list->number_of_portion !!}</td>
	                            <td>{!! @$list->cost !!}</td>
	                        </tr>
	                        <?php $b++; ?>
	                        @endforeach
	                    @endforeach    
                        @endif

                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold;">
                            <td></td>
                             <td></td>
                              <td></td>
                               <td></td>
                                <td></td>
                                <td>Total</td>
                                <td></td>

                        </tr>
                    </tfoot>
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
   #note{
    height: 80px !important;
   }
   .align_float_right
{
  text-align:  right;
}
 </style>
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    var inventory_items_list_data = <?php echo json_encode($inventory_items_list_data) ?>;
    $(function () {
        $(".mlselec6t").select2();
    });
    
    $('document').ready(function(){
       manageCosts(); 
    });
    $('#weight, #wa_inventory_item_id, #number_of_portion').bind("change keyup input",function() { 
        manageCosts();
    });
    
    
    function manageCosts(){
        inventory_id = $('#wa_inventory_item_id').val();
        if(inventory_id){
            standard_cost = inventory_items_list_data[inventory_id]['standard_cost'];
            number_of_portion = $('#number_of_portion').val();
            $('#material_cost').val(standard_cost);
            number_of_portion = $('#number_of_portion').val();
            weight_portion = standard_cost/number_of_portion;
            $('#weight_portion').val(weight_portion);
            weight = $('#weight').val();
            cost = standard_cost * weight;
            $('#cost').val(cost);
        }
    }

</script>
@endsection