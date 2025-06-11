
                         <tr>
                                                                           
                                        <td>
                                            <input type="hidden" name="service_id[{{$issue->id}}]" value="{{$issue->id}}">
                                            
                                            {{$issue->name}}</td>
                                        <td><input type="number" name="parts[{{$issue->id}}]" class="parts calcSum"></td>
                                        <td><input type="number" name="labor[{{$issue->id}}]"  class="labour calcSum"></td>
                                        <td><input readonly class="subtotal" name="subtotal[{{$issue->id}}]" ></td>
                                        <td><button class="deleteTHis"><i class="fa fa-trash" aria-hidden="true"></i></button></td>

                                    
   </tr>

            



