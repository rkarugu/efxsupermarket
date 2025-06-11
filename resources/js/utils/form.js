var Form = function(){
    
    this.errorMessage = function(message)
    {
        toastMixin.fire({
            title: message,
            icon: 'error'
        });
    }

    this.successMessage = function(message)
    {
        toastMixin.fire({
            title: message,
            icon: 'success'
        });
    }

    this.warningMessage = function(message)
    {
        toastMixin.fire({
            title: message,
            icon: 'warning'
        });
    }

    this.infoMessage = function(message)
    {
        toastMixin.fire({
            title: message,
            icon: 'info'
        });
    }

    this.form = function(input){
       
			$('.loder').css('display','block');
            // var data = $(input).serialize();
            var url = $(input).attr('action');
            var method = $(input).attr('method');
            var $this = $(input);
            var form = new Form();
            var data = new FormData($(input)[0]);
            data.append('_token',$(document).find('input[name="_token"]').val());
            $.ajax({
                url:url,
                method:method,
                data:data,
                contentType: false,
                cache: false,
                processData: false,
                success:function(out)
                {
                    $(".remove_error").remove();
                    $('.loder').css('display','none');
                    if(out.result == 0) {
                        for(let i in out.message) {     
                            var id = i.split(".");
                            if(id && id[1] && id[2]){
                            
                                $("[name='"+id[0]+"["+id[1]+"]["+id[2]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.message[i][0]+'</label>');
                                
                            }
                            else if(id && id[1]){
                                $("[name='"+id[0]+"["+id[1]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.message[i][0]+'</label>');
                            }
                            else{                   
                                $this.find("[name='"+i+"']").
                                parent().
                                append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.message[i][0]+'</label>');
                            }
                        }
                        for(let i in out.errors) {     
                            var id = i.split(".");
                            if(id && id[1] && id[2]){
                            
                                $("[name='"+id[0]+"["+id[1]+"]["+id[2]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                                
                            }
                            else if(id && id[1]){
                                $("[name='"+id[0]+"["+id[1]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                            }
                            else{                   
                                $this.find("[name='"+i+"']").
                                parent().
                                append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                            }
                        }
                    }
                    if(out.result === 1) {
                        form.successMessage(out.message);
                        if(!out.form_not_clear){
                            $this.find('input:not(:hidden)').val('');
                            $this.find('textarea').val('');
                        }
                        if(out.location)
                        {
                            setTimeout(() => {
                                location.href=out.location;
                            }, 1000);
                        }
					}
                    if(out.result === -1) {
						form.errorMessage(out.message);							
					}
                },
                error:function(out)
                {
                    if(out.result == 0) {
                        for(let i in out.message) {     
                            var id = i.split(".");
                            if(id && id[1] && id[2]){
                            
                                $("[name='"+id[0]+"["+id[1]+"]["+id[2]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.message[i][0]+'</label>');
                                
                            }
                            else if(id && id[1]){
                                $("[name='"+id[0]+"["+id[1]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.message[i][0]+'</label>');
                            }
                            else{                   
                                $this.find("[name='"+i+"']").
                                parent().
                                append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.message[i][0]+'</label>');
                            }
                        }
                        for(let i in out.errors) {     
                            var id = i.split(".");
                            if(id && id[1] && id[2]){
                            
                                $("[name='"+id[0]+"["+id[1]+"]["+id[2]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                                
                            }
                            else if(id && id[1]){
                                $("[name='"+id[0]+"["+id[1]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                            }
                            else{                   
                                $this.find("[name='"+i+"']").
                                parent().
                                append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                            }
                        }
                    }else if(out.result === -1) {
						form.errorMessage(out.message);							
					}else{
                        $(".remove_error").remove();
                        $('.loder').css('display','none');
                        form.errorMessage('Something went wrong');							
                    }
                }
            });
       
    }
    this.delete = function(input){

                $('.loder').css('display','block');
                // var data = $(input).serialize();
                var url = $(input).attr('action');
                var method = $(input).attr('method');
                var $this = $(input);
                var form = new Form();
                var data = new FormData($(input)[0]);
                data.append('_token',$(document).find('input[name="_token"]').val());
                $.ajax({
                    url:url,
                    method:method,
                    data:data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success:function(out)
                    {
                        $(".remove_error").remove();
                        $('.loder').css('display','none');
                        if(out.result == 0) {        
                            $this.find("[name='"+i+"']").
                                                    parent().
                                                    append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.message[i][0]+'</label>');
                        }
                        if(out.result === 1) {
                            form.successMessage(out.message);                        
                            if(out.location)
                            {
                                setTimeout(() => {
                                    location.href=out.location;
                                }, 1000);
                            }
                        }
                        if(out.result === -1) {
                            form.errorMessage(out.message);							
                        }
                    },
                    error:function(err)
                    {
                        $(".remove_error").remove();
                        $('.loder').css('display','none');
                        form.errorMessage('Something went wrong');							
                    }
                });
    }
}
var form = new Form();
$(document).on('submit','.submitMe',function(e){
    e.preventDefault();
    form.form(this);
});

$(document).on('submit','.deleteMe',function(e){
    e.preventDefault();
    var $this = this;
    Swal.fire({
      title: 'Do you want to delete this?',
      showCancelButton: true,
      confirmButtonText: `Procced`,
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        form.delete(this);
      }
    })

});


jQuery(document).ready(function($){
      
    $(".rating input").on('click',(function(e) {        
        var selected_value = $(this).val();
        $("#selected_rating").val(selected_value);            
    }));  
});

export default Form