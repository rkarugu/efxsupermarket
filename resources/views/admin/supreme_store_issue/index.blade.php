@extends('layouts.admin.admin')
@section('content')
<section class="content">
<!-- Small boxes (Stat box) -->
  <div class="box box-primary">
    <div class="box-header with-border no-padding-h-b">                            
      @include('message')
      <div class="col-md-12 no-padding-h">
        <table class="table table-bordered table-hover" id="create_datatable">
          <thead>
            <tr>
              <th width="5%">S.No.</th>
              <th width="10%">Requisition No</th>
              <th width="10%">Requisition Date</th>
              <th width="15%">User Name</th>
              <th width="10%">To Store</th>
              <th width="15%">Manual Doc</th>
              <th width="15%">Department</th>
              <th width="5%">Items</th>
              <th width="10%">Status</th>           
              <th width="5%" class="noneedtoshort">Action</th>
            </tr>
          </thead>
          <tbody>
            @if(isset($lists) && !empty($lists))
              <?php $b = 1;?>
              @foreach($lists as $list)                
                <tr>
                  <td>{!! $b !!}</td>
                  <td>{!! $list->requisition_no !!}</td>
                  <td>{!! $list->requisition_date !!}</td>
                  <td>{!! @$list->getrelatedEmployee->name !!}</td>
                  <td >{{ @$list->getRelatedToLocationAndStore->location_name }}</td>
                  <td >{{ @$list->manual_doc_no }}</td>
                  <td >{{ @$list->getDepartment->department_name }}</td>
                  <td>{{ count($list->getRelatedItem)}}</td>
                  <td>{!! $list->status !!}</td>
                  <td class = "action_crud">
                    <span>
                      <a class="btn btn-sm btn-biz-pinkish" title="View" href="{{ route($model.'.edit', $list->slug) }}" ><i class="fa fa-eye" aria-hidden="true"></i>
                      </a>
                    </span>  
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