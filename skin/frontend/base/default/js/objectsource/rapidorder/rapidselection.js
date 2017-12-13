(function($) {
    $(document).ready(function() {
        $('input[type=radio][name=rapidSelection]').change(function() {
            $('#rapidSelectionForm').submit();
        });
    });
})(jQuery);
