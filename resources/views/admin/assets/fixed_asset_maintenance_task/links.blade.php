<a href="{{route('fixed_asset_maintenance_task_edit',['id'=>$data['id']])}}" style="display: inline-block;">
    <i class="fa fa-eye" aria-hidden="true"></i>
</a>

    <form action="{{route('fixed_asset_maintenance_task_delete')}}" method="post" class="addAssetParts" style="display: inline-block;margin-left:2px">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$data['id']}}">
        <button type="submit" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></button>
    </form>