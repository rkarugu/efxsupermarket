<div class="modal fade" id="route_customer_create">
    <form action="{{route('pos.route_customer.store')}}" method="POST" role="form" class="whenSubmit">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Customer</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">First Name</label>
                        <input type="text"  class="form-control" name="f_name" placeholder="First Name">
                    </div>
                    <div class="form-group">
                        <label for="">Last Name</label>
                        <input type="text"  class="form-control" name="l_name" placeholder="Last Name">
                    </div>
                    <div class="form-group">
                        <label for="">Telephone</label>
                        <input type="number" min="10" class="form-control" name="telephone" placeholder="Enter Telephone">
                    </div>
                    <div class="form-group">
                        <label for="">PIN</label>
                        <input type="text" class="form-control" name="pin" placeholder="Enter pin">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('#route_customer_create').modal('show');
        $('.whenSubmit').submit(function(e){
            e.preventDefault();
            var $this = $(this);
            var url = $this.attr('action');
            var form = new Form();
            var data = new FormData($this[0]);
            data.append('_token',$(document).find('input[name="_token"]').val());
            $.ajax({
                url:url,
                data:data,
                contentType: false,
                cache: false,
                processData: false,
                method:'POST',
                success:function(out){
                    $('#loader-on').hide();
                    $(".remove_error").remove();
                    if(out.result == 0) {
                        for(let i in out.errors) {
                            $("[name='"+i+"']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                            $("."+i).parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                        }
                    }
                    if(out.result === 1) {
                        form.successMessage(out.message);
                        $('.route_customer').select2('destroy');
                        $(document).find('.route_customer').html("<option value='"+out.data.id+"' selected>"+out.data.phone+"("+out.data.name+")</option>");
                        route_customer();
                        $('#route_customer_create').modal('hide');
                        checkButtonState()
                    }
                    if (out.result === 3) {
                        Swal.fire({
                            title: 'Create Duplicate?',
                            text: out.message, // Display the message from the response
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Proceed',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true // Optional, switches the positions of Confirm and Cancel
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Append the `duplicate` field and submit the form again
                                let formData = new FormData(); // Use FormData to handle file uploads and other form data
                                formData.append('duplicate', true); // Add the `duplicate` field
                                for (let key of data.keys()) {
                                    formData.append(key, data.get(key)); // Re-add original form data
                                }

                                // Send the form again with the `duplicate` field
                                $.ajax({
                                    url: url, // The same URL
                                    data: formData,
                                    contentType: false,
                                    cache: false,
                                    processData: false,
                                    method: 'POST',
                                    success: function (out) {
                                        if (out.result === 1) {
                                            form.successMessage(out.message);
                                            // $('.route_customer').select2('destroy');
                                            // $(document).find('.route_customer').html("<option value='" + out.data.id + "' selected>" + out.data.phone + "(" + out.data.name + ")</option>");
                                            // route_customer();
                                            $('#route_customer_create').modal('hide');
                                        }
                                        if (out.result === -1) {
                                            form.errorMessage(out.message);
                                        }
                                    },
                                    error: function (err) {
                                        $('#loader-on').hide();
                                        console.log(err)
                                        form.errorMessage('Something went wrong');
                                    }
                                });
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                console.log('Cancel button clicked');
                            }
                        });
                    }



                    if(out.result === -1) {
                        form.errorMessage(out.message);
                    }
                },
                
                error:function(err)
                {
                    $('#loader-on').hide();
                    $(".remove_error").remove();
                    form.errorMessage('Something went wrong');							
                }
            });
        })
    });
</script>
