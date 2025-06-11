
@extends('layouts.admin.admin')

@section('content')

<style>
    .span-action {

    display: inline-block;
    margin: 0 3px;

}
</style>

<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
          <b>Supplier Name: {{ ucfirst($supplier->name) }} <br>
                             Supplier Number: {{ $supplier->supplier_code }}</b>
           
            @include('message')
             <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.print-remittance-advice') }}" enctype = "multipart/form-data">
            {{ csrf_field() }}

            <input type="hidden" name= "slug" value="{{ $supplier->slug }}">
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="10%"  >Date</th>
                           

                           
                            <th width="15%"  >GRN NUMBER</th>
                            <th width="18%"  >Reference</th>
                            <th width="15%"  >Accounting Period</th>
                            <th width="14%"  >Amount</th>
                              <th width="18%"  >Allocation</th>
                            <th  width="20%" class="noneedtoshort" >Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php 
                    $total_amount = [];

                    ?>
                    @foreach($lists as $list)
                  
                    <tr>
                        <td>{{ $list->trans_date }}</td>
                       
                         <td>{{ $list->document_no }}</td>
                        <td>{{ $list->suppreference }}</td>
                        <td></td>
                        <td>{{ manageAmountFormat($list->total_amount_inc_vat) }}</td>
                        <td>
                      <!--  <input type = "radio" name =  "trans[{{ $list->id}}]" value="S">S -->
                         <input type = "checkbox" name =  "trans[{{ $list->id}}]" value="F">

                        </td>
                        <td>
                      


                                                <a title="Split" data-href="" onclick="getspiltedview({{$list->id}});"  data-toggle="modal" data-target="#splited-popup" data-dismiss="modal" style="cursor: pointer;"><i class="fa fa-list"></i>
                                                </a>
                                                


                        </td>
                    </tr>

                    <?php 

                    $total_amount[] = $list->total_amount_inc_vat;
                    ?>
                   
                    @endforeach
                    </tbody>

                    <tfoot>
                      <td></td>
                       <td></td>
                        <td></td>
                         <td style="font-weight: bold;">Total</td>
                           <td style="font-weight: bold;">{{manageAmountFormat(array_sum($total_amount))}}</td>
                          <td></td>
                           <td></td>

                      

                    </tfoot>



                </table>

                <div align="right"><button class="btn btn-success">Print Remittance Advice</button></div>
            </div>
            </form>
        </div>
    </div>


</section>

 <div class="modal fade new-m tnc send-lesson-popup" id="splited-popup" role="dialog" tabindex="-1"  aria-hidden="true" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
    </div>
  </div>
</div> 

@endsection

@section('uniquepagescript')

 <script type="text/javascript">
    function getspiltedview(supplyTransId)
    {
       
      var url = '{{ route("maintain-suppliers.supplier-popup", ":supplyTransId") }}';
      url = url.replace(':supplyTransId', supplyTransId);
      $('#splited-popup').find(".modal-content").load(url);
    }
   </script>

@endsection