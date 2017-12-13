(function ($) {
    $(document).ready(function() {
        stepAccordionInit = 0;
        $('#product-options-wrapper dl.last dt').click(function(){
            var current = $(this);
            var next = $(this).next();
            current.siblings('dd').each(function(i) {
                if (i*2 != next.index()-1) {
                    $(this).prev().removeClass('stepSelected');
                    $(this).slideUp();
                }
            });

            if(next.is(':hidden')) {
                current.addClass('stepSelected');
                if (!stepAccordionInit) {
                    stepAccordionInit = 1;
                    next.show();
                } else {
                    next.slideDown();
                }
            }
        });

        $('#product-options-wrapper dl.last dd').hide();
        $('#product-options-wrapper dl.last dt:first').click();
    });
})(jQuery);