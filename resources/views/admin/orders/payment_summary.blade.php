      


<span id = "form_type"></span>
            <div class="modal-header">
                <button type="button" class="close" 
                   data-dismiss="modal">
                       <span aria-hidden="true">&times;</span>
                       <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                     Payment Summary For Receipt id: {!! $receipt_id!!}
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
               
                <table class="table table-bordered table-hover">
                        <tr>
                            <th>S.n.</th>
                            
                            <th>Narration</th>
                            <th>Payment Mode</th>
                            <th>Amount</th>
                        </tr>
                        <?php 
                        $total_amount = [];
                        $counter = 1;
                        ?>

                        @foreach($row->getAssociatePaymentsWithReceipt as $data)
                        <tr>
                            <td>{!! $counter !!}</td>
                            
                            <td>{!! $data->narration !!}</td>
                            <td>{!! $data->payment_mode !!}</td>
                            <td>{!! $data->amount !!}</td>
                        </tr>
                        <?php 
                        $total_amount[] = $data->amount;
                        $counter++;?>
                        @endforeach

                        <tr>
                            
                            <td colspan="3" style="text-align: right">Total</td>
                            <td>{!! array_sum($total_amount)!!}</td>
                        </tr>

                </table>
                
                
            </div>
            
            <!-- Modal Footer -->
          
