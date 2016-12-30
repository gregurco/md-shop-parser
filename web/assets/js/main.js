$(function() {
    var $moreDiscountProductsTable = $('#moreDiscountProductsTable');
    var $loadMoreDiscountProducts = $('#loadMoreDiscountProducts');
    var $topDiscountSearchTypeahead = $('#topDiscountSearchTypeahead');

    $loadMoreDiscountProducts.on('click', function() {
        $loadMoreDiscountProducts.addClass('disabled');
        $loadMoreDiscountProducts.find('.fa').addClass('fa-spin fa-fw');

        $.ajax({
            url: Routing.generate('load_more_discount_products', {firstRecord: $moreDiscountProductsTable.find('tbody tr').length}),
            success: function(date) {
                $moreDiscountProductsTable.find('tbody').append(date);

                $loadMoreDiscountProducts.removeClass('disabled');
                $loadMoreDiscountProducts.find('.fa').removeClass('fa-spin fa-fw');
            }
        });
    });

    var discountProducts = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: Routing.generate('search_discount_products', {query: '%QUERY'}),
            wildcard: '%QUERY',
            replace: function () {
                return Routing.generate('search_discount_products', {query: $topDiscountSearchTypeahead.val()});
            },
            filter: function (response) {
                // Map the remote source JSON array to a JavaScript object array
                return $.map(response.results, function (result) {
                    return {
                        value: result.title
                    };
                });
            }
        }
    });

    $topDiscountSearchTypeahead.typeahead(null, {
        name: '',
        display: 'value',
        limit: 100,
        source: discountProducts
    });

    $topDiscountSearchTypeahead.bind('typeahead:select', function(ev, suggestion) {
        console.log(suggestion);
    });
});