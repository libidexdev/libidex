(function($) {
    $(document).ready(function() {
        $('input[type=radio][name=rapidSelection]').change(function() {
            $('#rapidSelectionForm').submit();
        });
    });
})(jQuery);

function rapidApplySilver()
{
    jQuery.post("/rapidservice/cart/add/", { 'addProduct': 'rapidSilver' })
        // Second success from post.
        .done(function (response) {

            var data = response.data;
            var message = data.message;
            var percent = data.percentage;
            var title = data.name;
            var total = data.total;
            var percentTotal = data.percentageTotal;
            var tFoot, tFootTr, tFootTotal;

            var wrapper = jQuery('#silver-rapid-total');

            var wrapperHtml = '<td class="a-right" style="" colspan="3">';
            wrapperHtml += title+'('+percent+'%)</td>';
            wrapperHtml += '<td class="a-right last" style="">';
            wrapperHtml += '<span class="price">&pound;'+percentTotal+'</span></td>';

            if(wrapper.length == 0) {

                var reviewTable = jQuery('#checkout-review-table');
                tFoot = reviewTable.find('tfoot');
                tFootTr = tFoot.children('tr.last');
                tFootTr.before('<tr id="silver-rapid-total">'+wrapperHtml+'</tr>');
            }
            else {
                wrapper.html(wrapperHtml);
                wrapper.show();

                tFoot = wrapper.parent();
                tFootTr = tFoot.children('tr.last');
            }

            tFootTotal = tFootTr.children('td.last');
            tFootTotal.html('<strong><span class="price">&pound;'+total+'</span></strong>');

        }).fail(function (jqXHR) {
        alert('Something has gone wrong, please refresh the page and try again, specific error: ' + jqXHR.toString());
    });
}

function rapidApplyStandard()
{
    jQuery.post("/rapidservice/cart/remove/", { 'removeProduct': 'rapidSilver' })
        // Second success from post.
        .done(function (response) {

            var total = response.total;
            var tFoot, tFootTr, tFootTotal;

            var wrapper = jQuery('#silver-rapid-total');
            wrapper.hide();

            tFoot = wrapper.parent();
            tFootTr = tFoot.children('tr.last');
            tFootTotal = tFootTr.children('td.last');
            tFootTotal.html('<strong><span class="price">&pound;'+total+'</span></strong>');

        }).fail(function (jqXHR) {
        alert('Something has gone wrong, please refresh the page and try again, specific error: ' + jqXHR.toString());
    });
}