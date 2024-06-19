
(function ($) {
	$.each([
		'frontend/element_ready/xts_dynamic_discounts_table.default',
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.renderDynamicDiscountsTable();
		});
	});

    XTSThemeModule.renderDynamicDiscountsTable = function () {
        let $variation_forms = $('.variations_form');
        let $dynamicDiscountsTable = $('.xts-dynamic-discounts');
        let default_price_table = $dynamicDiscountsTable.html();

        function reInitPricingTableRowsClick() {
            $('.xts-dynamic-discounts tbody tr').on('click', function () {
                let $row = $(this);
                let min = $row.data('min');
                let $quantityInput = $('.quantity input.qty[name="quantity"]');
                $quantityInput.val(min).trigger('change');
            });
            
        }

        function addActiveClassToTable( $pricing_table, currentQuantityValue ) {
            $pricing_table.find('tbody tr').each(function () {
                let $row = $(this);
                let min  = $row.data('min');
                let max  = $row.data('max');

                if ( ( ! max && min <= currentQuantityValue ) || ( min <= currentQuantityValue && currentQuantityValue <= max ) ) {
                    $row.addClass('xts-active');
                } else {
                    $row.removeClass('xts-active');
                }
            });
        }

        $variation_forms.each(function () {
            let $variation_form = $(this);

            $variation_form
                .on('found_variation', function (event, variation) {
                    $.ajax({
                        url     : xts_settings.ajaxurl,
                        data    : {
                            action : 'xts_update_discount_dynamic_discounts_table',
                            variation_id: variation.variation_id,
                        },
						beforeSend: function () {
							$dynamicDiscountsTable.find('.xts-loader-overlay').addClass('xts-loading');
						},
                        success : ( data ) => {
                            $dynamicDiscountsTable.html( data );
                            reInitPricingTableRowsClick();

                            addActiveClassToTable( $('.xts-dynamic-discounts'), $(this).find('[name="quantity"]').val() );
							$dynamicDiscountsTable.find('.xts-loader-overlay').removeClass('xts-loading');
                        },
                        dataType: 'json',
                        method  : 'GET'
                    });
                })
                .on('click', '.reset_variations', function () {
                    $dynamicDiscountsTable.html(default_price_table);
                    reInitPricingTableRowsClick();

                    addActiveClassToTable( $('.xts-dynamic-discounts'), $(this).closest('form').find('.quantity input.qty[name="quantity"]').val() );
                });
        });

        reInitPricingTableRowsClick();

        $('.quantity input.qty[name="quantity"]').off('change').on('change', function() {
            addActiveClassToTable( $dynamicDiscountsTable, $(this).val() );
        });
    }

    $(document).ready(() => {
        XTSThemeModule.renderDynamicDiscountsTable();
    });
})(jQuery);


