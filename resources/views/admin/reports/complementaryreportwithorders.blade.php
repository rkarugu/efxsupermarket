<?php
$category_of_complimentary = \Config::get('params.category_of_complimentary');
?>

@extends('layouts.admin.admin')

@section('content')


<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <div  style="height: 150px ! important;"> 
                <div class="card-header">
                    <i class="fa fa-filter"></i> Filter
                </div><br>
                {!! Form::open(['route' => 'reports.get-complementary-reports-with-orders','method'=>'get']) !!}

                <div>
                    <div class="col-md-12 no-padding-h">
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::text('start-date', null, [
                                'class'=>'datepicker form-control',
                                'placeholder'=>'Start Date' ,'readonly'=>true]) !!}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::text('end-date', null, [
                                'class'=>'datepicker form-control',
                                'placeholder'=>'End Date','readonly'=>true]) !!}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                {!!Form::select('restaurant', $restro, null, ['placeholder'=>'Branch', 'class' => 'form-control'  ])!!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 no-padding-h">
                        <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>



                        <div class="col-sm-1">
                            <button title="Export In PDF" type="submit" class="btn btn-warning" name="manage-request" value="pdf"  ><i class="fa fa-file-pdf" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="col-sm-1">
                            <a class="btn btn-info" href="{!! route('reports.get-complementary-reports-with-orders').getReportDefaultFilter() !!}"  >Clear</a>
                        </div>
                    </div>
                </div>

                </form>
            </div>

            <br>
            @include('message')
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Order.No.</th>
                            <th style="width: 10%;"  >Date & Time</th>
                            <th style="width: 20%;" >Item Desc</th>
                            <th style="width: 10%;"  >Price</th>
                            <th  style="width: 10%;" >Reason Complementary</th>
                            <!--th  style="width: 10%;" >Complementary Code</th-->
                            <th  style="width: 10%;" >Employee Name</th>
                            <?php /* <th  style="width: 10%;" >Family Groups</th> */ ?>
                            <th  style="width: 10%;" >Category of Complimentary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $price_total = 0;
                        ?>

                        @foreach($data as $list)


                        <tr>
                            <td>{!! manageOrderidWithPad($list->id) !!}</td>
                            <td>{!! date('Y-m-d h:i A',strtotime($list->created_at)) !!}</td>
                            <td>
                            <?php
                                $price_total += $list->order_final_price;
                                $item_desc_array = [];
                            ?>
                                @foreach($list->getAssociateItemWithOrder as $ordered_item)
                                <?php
                                $condiment_arr = json_decode($ordered_item->condiments_json);
                                $item_desc = 'Item: ' . $ordered_item->item_title;
                                $item_desc .= '<br>Qty: ' . $ordered_item->item_quantity;
                                $item_desc_array[] = $item_desc;
                                
                                ?>
                                @endforeach  
                                {!! implode(' ,<br>',$item_desc_array)!!} </td>

                            <td>{!! manageAmountFormat($list->order_final_price) !!}</td>
                            <td>{!! $list->compliementary_reason !!}</td>
                            <!--td>{!! $list->complimentry_code !!}</td-->



                            <td>{!! isset($list->getAssociateComplimentaryUserDetail)?$list->getAssociateComplimentaryUserDetail->name:'-'!!}</td>

                            <?php /* <td><?= getFamilyGroupsListByOrderId($list->id) ?></td> */ ?>
                            <td><?= (!empty($list->category_of_complimentary) && isset($category_of_complimentary[$list->category_of_complimentary][$list->category_of_complimentary])) ? $category_of_complimentary[$list->category_of_complimentary] : '-' ?></td>

                        </tr>

                        @endforeach



                    </tbody>

                    <tfoot>
                    <td></td>
                    <td></td>
                    <td style="font-weight: bold;">Total</td>
                    <td style="font-weight: bold;">{{ manageAmountFormat($price_total)}}</td>
                    <td></td>
                    <td></td>
                    
                    </tfoot>
                </table>
            </div>


        </div>
    </div>
</section>



@endsection


@section('uniquepagestyle')
<link rel="stylesheet" href="{{asset('assets/admin/dist/bootstrap-datetimepicker.min.css')}}">
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datetimepicker.js')}}"></script>
<script>


$('.datepicker').datetimepicker({
    format: 'yyyy-mm-dd hh:ii:00',
    minuteStep: 1
});




</script>




@endsection