// multistep-form.js
$(document).ready(function () {
    var navListItems = $('div.setup-panel div a'),
        allWells = $('.setup-content'),
        allNextBtn = $('.nextBtn'),
        allPrevBtn = $('.prevBtn');

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
            $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-success').addClass('btn-default');
            $item.addClass('btn-success');
            allWells.hide();
            $target.show();
        }
    });

    // Modified form submission handling
    $('button[type="submit"].submitMe').click(function(e) {
        e.preventDefault();
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            form = $(this).closest('form')[0];

        if (form && form instanceof HTMLFormElement) {
            var formData = new FormData(form);
            // Add your form submission logic here
            
            // For testing/development, just move to next step
            if (nextStepWizard.length) {
                nextStepWizard.removeAttr('disabled').trigger('click');
            }
        }
    });

    $('div.setup-panel div a.btn-success').trigger('click');
});

// form.js
class Form {
    constructor(form) {
        this.form = form;
    }

    static fromElement(element) {
        if (!(element instanceof HTMLFormElement)) {
            throw new Error('Parameter must be an HTMLFormElement');
        }
        return new Form(new FormData(element));
    }

    // Add other form methods as needed
}