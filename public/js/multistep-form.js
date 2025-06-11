var MultistepForm = function(){
    
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

    this.form = function(input){
        $('.loder').css('display','block');
        var _form = $(input).parents('form');
        // var _form = document.querySelector('form.validate');
        var url = _form.attr('action');
        var method = _form.attr('method');
        var $this = _form;
        var form = new MultistepForm();
        var data = new FormData(_form[0]);
        data.append('_token',$(document).find('input[name="_token"]').val());
        data.append('current_step',$(input).val());
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
                    if(out.next_step){
                        $(document).find(".step-buttons[href='#step-"+out.next_step+"']").removeAttr('disabled').trigger('click');
                        return;
                    }
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
            error:function(err)
            {
                $(".remove_error").remove();
                $('.loder').css('display','none');
                form.errorMessage('Something went wrong');							
            }
        });
    }
}
var form = new MultistepForm();
$(document).on('click','.submitMe',function(e){
    e.preventDefault();
    form.form(this);
});

var navListItems = $('div.multistep .setup-panel div a'),
        allWells = $('.multistep .setup-content'),
        allNextBtn = $('.multistep .nextBtn');

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
            $item = $(this);

        if (!$item.attr('disabled')) {
            navListItems.removeClass('btn-success').addClass('btn-default');
            $item.addClass('btn-success');
            allWells.hide();
            $target.show();
        }
    });
    $('.multistep div.setup-panel div a.btn-success').trigger('click');
