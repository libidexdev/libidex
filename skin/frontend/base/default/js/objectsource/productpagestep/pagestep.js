(function($) {
    $(document).ready(function() {

        $('#nextStepBtn').click(function() {
            var currentTab = $('#product-options-wrapper').find('> dl > .ui-tabs');
            var currentListItem = -1;

            var findActiveTabFn = function() {
                var labelList = currentTab.find('> ul.ui-tabs-nav > li');
                var lengthList = labelList.length;
                if (lengthList > 0) {
                    labelList.each(function(i) {
                        if ($(this).attr('aria-selected') == 'true') {
                            currentTab = $('#'+$(this).attr('aria-controls')).find('> .ui-tabs');
                            currentListItem = $(this);
                            findActiveTabFn();
                            return false;
                        }
                    });
                }

                return currentTab;
            };

            var activeTab = findActiveTabFn();

            var nextListItem = currentListItem;

            var setNextListItemFn = function() {
                if (nextListItem.hasClass('ui-tabs-active')) {
                    var temp = nextListItem.next();
                    if (temp.length > 0) {
                        nextListItem = temp;
                        nextListItem.closest('.ui-tabs').tabs("option", "active", nextListItem.index());
                        nextListItem.closest('.ui-tabs').find('.ui-tabs').each(function() {
                            $(this).tabs("option", "active", 0);
                        });
                        return nextListItem;
                    } else {
                        var parentListItems = nextListItem.closest('.ui-tabs').parent()
                            .closest('.ui-tabs').find('> ul.ui-tabs-nav > li');
                        if (parentListItems.length > 0) {
                            parentListItems.each(function(i) {
                                if ($(this).attr('aria-selected') == 'true') {
                                    nextListItem = $(this);
                                    setNextListItemFn();
                                    return false;
                                }
                            });
                        }
                        else {
                            //alert('We reached the end');
                        }
                    }
                } else {
                    return nextListItem;
                }
            };

            setNextListItemFn();

        });
    });
})(jQuery);
