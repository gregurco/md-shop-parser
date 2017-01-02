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
                return Routing.generate('search_discount_products', {query: $topDiscountSearchTypeahead.val().replace('/', '%2F')});
            },
            filter: function (response) {
                // Map the remote source JSON array to a JavaScript object array
                return $.map(response.results, function (result) {
                    return {
                        value: result.title,
                        shop: result.shop,
                        externalId: result.externalId
                    };
                });
            }
        }
    });

    $topDiscountSearchTypeahead.typeahead(null, {
        name: '',
        display: 'value',
        limit: 100,
        source: discountProducts,
        templates: {
            empty: [
                '<div class="empty-message">',
                    '<strong>Unable to find any Products that match the current query</strong>',
                '</div>'
            ].join('\n'),
            suggestion: Handlebars.compile('<div><strong>{{value}}</strong> – {{shop}}</div>')
        }
    });

    $topDiscountSearchTypeahead.bind('typeahead:select', function(ev, suggestion) {
        showProductChart(suggestion.shop, suggestion.externalId);
    });

    $('.show-product-chart').on('click', function() {
        var $this = $(this);
        showProductChart($this.data('shop'), $this.data('external-id'));
    });
});

function showProductChart(shop, externalId) {
    $.ajax({
        url: Routing.generate('show_product_chart', {shop: shop, externalId: externalId}),
        success: function(data) {
            var $data = $(data);

            $('body').append($data);
            $data.modal('show');
            $data.on('shown.bs.modal', function (e) {
                new Chart($data.find('.chart'), {
                    type: 'line',
                    data: {
                        datasets: JSON.parse($data.find('.chart-datasets').val())
                    },
                    options: {
                        scales: {
                            xAxes: [{
                                type: 'time',
                                time: {
                                    displayFormats: {
                                        hour: 'DD/MM/YY'
                                    }
                                }
                            }]
                        }
                    }
                });
            });
        }
    });
}