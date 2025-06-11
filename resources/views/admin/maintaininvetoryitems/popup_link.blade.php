@if($type=='1')
<span>
    <a style='@if(!isset($class)) font-size: 16px; @endif'  href='javascript:void(0);' row_id='<?= $id ?>' onclick="manageStockPopup('<?= $link_popup ?>')" class="{{isset($class) ? $class : NULL}}">
        <i class='fa fa-bolt' title= 'Manage Item Stock'></i>
    </a>
</span>
@endif
@if($type=='2')
<span>
    <a style='font-size: 16px;'  href='javascript:void(0);' row_id='<?= $id ?>' data-title="{{$data['title']}}({{$data['stock_id_code']}})" onclick="manageCategoryPopup('<?= $link_popup2 ?>',this)">
        <i class='fa fa-money' title= 'Manage Item Category Price'></i>
    </a>
</span>
@endif
