
@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">                            
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <form action="{{route('maintain-items.bulk_inventory_import_export')}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Supplier</label>
                                  <select class="form-control mlselec6t" name="supplier">
                                    <option selected disabled>-- Select Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->supplier_code}} {{$supplier->name}}</option>
                                    @endforeach
                                  </select>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <br>
                                <button type="submit" class="btn btn-primary" name="export" id="export" value="export">
                                    <i class="fa fa-download"></i> Download Excel
                                </button>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modelId">
                                  Import Data
                                </button>
                                
                                <!-- Modal -->
                                <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Import Data</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                  <label for="">Import File</label>
                                                  <input type="file" name="excel" id="excel" class="form-control">
                                                  <small id="helpId" class="text-muted">Attach same excel file which you have downloaded!</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-computer-classic"></i>Import</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
   
    </section>
   
@endsection

@section('uniquepagestyle')
<link rel="stylesheet" href="{{asset('css/multistep-form.css')}}">
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
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/multistep-form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
      $(".mlselec6t").select2();
    });
</script>
@endsection
