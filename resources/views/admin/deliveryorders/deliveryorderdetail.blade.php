
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> Delivery Order Id: {!! $row->id !!} </h3></div>
         @include('message')
      

         {!! Form::model($row, ['method' => 'post','route' => ['admin.delivery-orders.post.assign', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}

       

          

           <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Customer Name:</label>
                    <div class="col-sm-10">
                        {!! $row->getAssociateUserForOrder->name !!}
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Address:</label>
                    <div class="col-sm-10">
                        {!! $row->address !!}
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Amount:</label>
                    <div class="col-sm-10">
                        {!! manageAmountFormat($row->order_final_price) !!}
                    </div>
                </div>
            </div>


             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Current Status:</label>
                    <div class="col-sm-10">
                        {!! str_replace('_',' ',$row->status) !!}
                    </div>
                </div>
            </div>

            @if($row->getAssociatedSalesRepresentatitv)
                 <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Representative:</label>
                    <div class="col-sm-10">
                        {!! $row->getAssociatedSalesRepresentatitv->representativeDetail->name !!}
                    </div>
                </div>
            </div>


            @endif


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Created at:</label>
                    <div class="col-sm-10">
                        {!! date('d/m/Y h:i A',strtotime($row->created_at)) !!}
                    </div>
                </div>
            </div>


             <div class="box-body">
               
                   
                       


                    <table width="100%" class="table table-bordered table-hover" id="create_datatable_desc">
                        <tr>
                            <td width="10%">Sn.</td>
                            <td width="90%">Description</td>
                        </tr>
                        <?php $i=1;?>
                        @foreach($row->getAssociateItemWithOrder as $ordered_item)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>   <?php 
                                   
                                   echo  '<b>Item:</b> '.$ordered_item->item_title;
                                    if($ordered_item->item_comment && $ordered_item->item_comment !="")
                                    {
                                        echo  '('.$ordered_item->item_comment.')';
                                    }
                                    echo  '<br><b>Qty:</b> '.$ordered_item->item_quantity;

                                ?></td>
                        </tr>
                          <?php $i++;?>
                        @endforeach
                    </table>
            </div>

            @if($row->status == 'PAID')

                 <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Sales Representative:</label>
                    <div class="col-sm-9">
                         {!!Form::select('representative_id',getSaleRepresentative(), null, ['placeholder'=>'Select Sales Representative', 'class' => 'form-control','required'=>true])!!}
                    </div>
                </div>
            </div>


              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Assign</button>
            </div>


            @endif
           
        </form>
    </div>
</section>
@endsection



