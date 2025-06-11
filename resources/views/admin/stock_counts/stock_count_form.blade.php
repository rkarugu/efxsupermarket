@if(count($items)>0)
<?php foreach($items as $key => $row) { ?>
<tr>
    <td><?= $row->stock_id_code ?></td>
    <td><?= $row->title ?></td>
    <td><?= @$row->pack_size ?></td>
    <td>
         {!! Form::number('quantity_'.$row->inventory_id,  null, ['min'=>'0','class'=>'form-control delivered_quantity']) !!}  
        
    </td>
    <td>
        {!! Form::text('reference_'.$row->inventory_id,  null , ['class'=>'form-control delivered_quantity']) !!}  
    </td>
    
</tr>
<?php } ?>
<tr id = "last_total_row" >
    <td colspan="5" style="text-align: right;">  
        {{ Form::submit('Enter Above Counts',['class'=>'btn btn-primary']) }}
    </td>
</tr>
@else
<tr>
    <td colspan="5" style="text-align:center;font-size:18px;font-weight:bold">The selected category has already been entered into the counts, please use the edit option</td>    
</tr>
@endif