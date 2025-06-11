<div class="modal-header">
    <h5 class="modal-title">Assign Branchs to {{@$user->name}}</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
  </div>
<div class="modal-body">
    <form action="{{route('admin.users.assign_branches_post')}}" method="post" class="addExpense">
        {{ csrf_field() }}
        <input type="hidden" name="user_id" value="{{@$user->id}}">
        <div>
            <span class="branch_id"></span>
        </div>
        {{-- <div class="col-sm-12">
            <h4>MENU</h4>
        </div>      --}}
        <div class="row">
            @foreach ($branches as $item)
                <div class="col-sm-6">
                    <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="branch_id[{{$item->id}}]"  value="{{$item->id}}" 
                        @if(in_array($item->id,$assigned)) checked @endif>
                        {{$item->name}}
                    </label>
                    </div>
                </div>
            @endforeach     
            <div class="col-sm-12">
                <br>
                <br>
                <button class="btn btn-primary btn-sm" type="submit">Assign</button>
            </div>       
        </div>
    </form>
</div>