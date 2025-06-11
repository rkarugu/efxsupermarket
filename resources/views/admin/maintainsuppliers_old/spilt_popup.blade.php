      


<span id = "form_type"></span>
            <div class="modal-header">
                <button type="button" class="close" 
                   data-dismiss="modal">
                       <span aria-hidden="true">&times;</span>
                       <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                     <b>
                    Supplier Name: {{ ucfirst($supplier->name) }} <br>
                             Supplier Number: {{ $supplier->supplier_code }}</b>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
               
              <form class="validate form-horizontal"  role="form" method="POST" action="{{ route('maintain-suppliers.post-splitted-amount',$supplierTransID) }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Other Amount To Allocate</label>
                    <div class="col-sm-7">
                        {!! Form::number('amount', null, ['min'=>'1','placeholder' => $row->total_amount_inc_vat, 'required'=>true, 'class'=>'form-control','max'=>$row->total_amount_inc_vat]) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Description</label>
                    <div class="col-sm-7">
                        {!! Form::text('description', null, ['placeholder' => 'Description', 'required'=>false, 'class'=>'form-control','maxlength'=>'255']) !!}  
                    </div>
                </div>
            </div>

          

              

            
           
            


            
           
             


            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Ok</button>

                <button type="button" class="btn btn-primary" 
                   data-dismiss="modal">
                      
                      Cancel
                </button>


            </div>
        </form>
                
                
            </div>
            
            <!-- Modal Footer -->
          
