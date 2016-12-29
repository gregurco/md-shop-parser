$(function() {
    var $moreDiscountProductsTable = $('#moreDiscountProductsTable');
    var $loadMoreDiscountProducts = $('#loadMoreDiscountProducts');

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
});