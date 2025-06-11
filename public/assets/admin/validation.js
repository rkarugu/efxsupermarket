// Set jQuery Validation Defaults
jQuery.validator.setDefaults({
    errorElement: 'div',
    errorClass: 'invalid-feedback',
    focusInvalid: false,

    errorPlacement: function (error, element) {
        if (element.attr('type') === 'radio' || element.attr('type') === 'checkbox') {
            $(element).closest('div').parent().append(error);
        } else {
            $(element).closest('div').append(error);
        }
    },

    highlight: function (element) {
        $(element).closest('.form-group').addClass('has-error').removeClass('has-success');
    },

    unhighlight: function (element) {
        $(element).closest('.form-group').removeClass('has-error');
    },

    success: function (label, element) {
        $(element).addClass('has-success').removeClass('has-error');
        $(element).closest('.invalid-feedback').remove();
    }
});