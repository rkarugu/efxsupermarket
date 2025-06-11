<div class="modal " id="edit-stock-model" role="dialog" tabindex="-1"  aria-hidden="true" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {!! Form::open(['route' => 'admin.stock-counts.update-stock-row','class'=>'validate form-horizontal']) !!}
            <div class="modal-header">
                <button type="button" class="close" 
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Update Stock
                </h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    {!! Form::hidden('row_id',null,['id'=>'hidden_row_id']) !!}  
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Quantity</label>
                        <div class="col-sm-5">
                            {!! Form::number('quantity',null, ['class'=>'form-control', 'id'=>'row-quantity','placeholder' => 'Quantity', 'required'=>true, 'min'=>0]) !!}  
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" value="Update">

                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Close
                </button>

            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div> 
