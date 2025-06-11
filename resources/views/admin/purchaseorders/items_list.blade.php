  <table class="table table-striped">
    <thead>
      <tr>
        <th>Code</th>
        <th>Description</th>
        <th>Our Units</th>
        <th>Available Qty</th>
        <th>Qty</th>
      </tr>
    </thead>
    <tbody>
	    <?php //echo "<pre>"; print_r($rows); die; ?>
	  @foreach($rows as $key=> $val)
      <tr>
        <td>{{$val->stock_id_code}}</td>
        <td>{{$val->description}}</td>
        <td>{{@$val->getUnitOfMeausureDetail->title}}</td>
        <td>{{@$val->item_total_qunatity}}</td>
        <td><input type="text" name="qty[{{$val->id}}]" value="0" style="text-align: right;"></td>
      </tr>
      @endforeach
    </tbody>
  </table>