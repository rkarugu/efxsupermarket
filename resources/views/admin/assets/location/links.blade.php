<a href="{{route('assets.location.edit',['id'=>$data['id']])}}" style="display: inline-block;">
    <i class="fa fa-eye" aria-hidden="true"></i>
</a>

@if (!in_array($data['id'],$dataDelete))
    <form action="{{route('assets.location.delete')}}" method="post" class="addAssetParts" style="display: inline-block;margin-left:2px">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$data['id']}}">
        <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fas fa-trash" aria-hidden="true"></i></button>
    </form>
@endif