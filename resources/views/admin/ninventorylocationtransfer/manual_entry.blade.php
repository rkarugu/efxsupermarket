  <table class="table table-striped">
    <thead>
      <tr>
        <th>Item Code</th>
         <th>Qty</th>
         @if($type=='sales-invoice')
         <th>Selling Price</th>         
         @endif
      </tr>
    </thead>
    <tbody>
	    <?php 
		$rows = [1,2,3,4,5,6,7,8,9,10];  
		?>
	  @foreach($rows as $val)
      <tr>
        <td><input type="text" name="item_code[]" value="" style="text-align: left;"></td>
        <td><input type="text" name="qty[]" value="0" style="text-align: right;"></td>
        @if($type=='sales-invoice')
        <td><input type="number" min="0" name="selling_price[]" value="0" style="text-align: right;"></td>
        @endif
      </tr>
      @endforeach
    </tbody>
  </table>