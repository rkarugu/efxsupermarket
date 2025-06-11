<div >
    <!-- Button trigger modal -->
    <a href="{{route('assets.getData',['id'=>$data['id']])}}" style="display: inline-block;" >
        <i class="fa fa-eye" aria-hidden="true"></i>
    </a>

    <a href="{{route('assets.addJournal',['id'=>$data['id']])}}" style="display: inline-block;" >
        <i class="fa fa-plus" title="Add Journal" aria-hidden="true"></i>
    </a>

    {{-- <form action="{{route('assets.delete')}}" method="post" class="addAssetParts" style="display: inline-block;margin-left:2px">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$data['id']}}">
        <button type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
    </form> --}}
</div>