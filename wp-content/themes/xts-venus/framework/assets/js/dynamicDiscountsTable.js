/* global xtsAdminConfig */

(function($) {
	// Condition query select2.
	function conditionQuerySelect2($field) {
		 $field.select2({
			  ajax             : {
					url     : xtsAdminConfig.ajaxUrl,
					data    : function(params) {
						 return {
							  action    : 'xts_discount_conditions_query',
							  security  : xtsAdminConfig.get_theme_settings_data_nonce,
							  query_type: $field.attr('data-query-type'),
							  search    : params.term
						 };
					},
					method  : 'POST',
					dataType: 'json'
			  },
			  theme            : 'xts',
			  dropdownAutoWidth: false,
			  width            : 'resolve'
		 });
	}

	function conditionQueryFieldInit( conditionType, $querySelect ) {
		 if ($querySelect.data('select2')) {
			  $querySelect.val('');
			  $querySelect.select2('destroy');
		 }

		 var $conditionQueryFieldTitle      = $querySelect.parents('.xts-controls-wrapper').find('.xts-discount-condition-query').first();
		 var $querySelectWrapper            = $querySelect.parent();
		 var $productTypeQuerySelectWrapper = $querySelect.parent().siblings('.xts-discount-product-type-condition-query');

		 if ('all' === conditionType) {
			  $querySelectWrapper.addClass('xts-hidden');
			  $productTypeQuerySelectWrapper.addClass('xts-hidden');
			  $querySelect.removeAttr('data-query-type');
		 } else if ('product_type' === conditionType) {
			  $querySelectWrapper.addClass('xts-hidden');
			  $productTypeQuerySelectWrapper.removeClass('xts-hidden');
			  $querySelect.removeAttr('data-query-type');
		 } else {
			  $querySelectWrapper.removeClass('xts-hidden');
			  $productTypeQuerySelectWrapper.addClass('xts-hidden');
			  $querySelect.attr('data-query-type', conditionType);
			  conditionQuerySelect2($querySelect);
		 }

		 // Show or hide Condition query field title.
		 var showTitle = false;

		 $('select.xts-discount-condition-type').each((key, type) => {
			  if ( 'all' !== $(type).val() ) {
					showTitle = true;
			  }
		 });

		 if ( showTitle ) {
			  $conditionQueryFieldTitle.removeClass('xts-hidden');
		 } else {
			  $conditionQueryFieldTitle.addClass('xts-hidden');
		 }
	}

	function validate() {
		 let isValid = true;
		 let ruleType = $('#xts_rule_type').val();
		 let $ruleRows = $('.xts_discount_rules-field .xts-controls-wrapper > .xts-discount:not(.title)');
		 let $conditionRows = $('.xts_discount_condition-field .xts-controls-wrapper > .xts-discount:not(.title)');
		 let discountRulesSelector = '.xts_discount_rules-field';
		 let discountConditionSelector = '.xts_discount_condition-field';

		 if ( 'undefined' === typeof ruleType ) {
			  ruleType = 'bulk';
		 }

		 if ( 'bulk' !== ruleType ) {
			  return isValid;
		 }

		 removeNotices( discountRulesSelector );
		 removeNotices( discountConditionSelector );

		 if ( 0 === $ruleRows.length ) {
			  showNotice( discountRulesSelector, xtsAdminConfig.ddiscounts_no_quantity_range );
			  isValid = false;
		 }

		 if ( 0 === $conditionRows.length ) {
			  showNotice( discountConditionSelector, xtsAdminConfig.ddiscounts_no_discount_condition );
			  isValid = false;
		 }

		 $ruleRows.each((key,ruleRow) => {
			  let $ruleRow              = $(ruleRow);
			  let priceFrom     = parseInt( $ruleRow.find('.xts-discount-from input').val() );
			  let priceTo       = parseInt( $ruleRow.find('.xts-discount-to input').val() );
			  let type              = $ruleRow.find('.xts-discount-type select').val();
			  let discountPercentageValue = parseInt( $ruleRow.find('.xts-discount-percentage-value input').val() );
			  let nextPriceFrom = parseInt( $ruleRow.next().find('.xts-discount-from input').val() );

			  if ( isNaN( priceFrom ) || isNaN( priceTo ) ) {
					return isValid;
			  }

			  if ( key !== $ruleRows.length - 1 && priceTo >= nextPriceFrom ) {
					if ( isNaN( nextPriceFrom ) ) {
						 return isValid;
					}

					showNotice( discountRulesSelector, xtsAdminConfig.ddiscounts_quantity_range_start );
					isValid = false;
			  }

			  if ( priceFrom > priceTo ) {
					showNotice( discountRulesSelector, xtsAdminConfig.ddiscounts_closing_quantity );
					isValid = false;
			  }

			  if ( 'percentage' === type && discountPercentageValue > 100 ) {
					showNotice( discountRulesSelector, xtsAdminConfig.ddiscounts_max_value );
					isValid = false;
			  }
		 });

		 return isValid;
	}

	function showNotice(selector, notice) {
		removeNotices(selector);
	
		$(selector).prepend(
			'<div class="xts-msg xts-notice xts-warning">' +
			notice +
			'</div>'
		);
	
		$(selector).off('click', '.xts-notice').on('click', '.xts-notice', function (e) {
			e.preventDefault();
	
			let $this = $(this);
	
			$this.fadeTo(100, 0, function () {
				$this.slideUp(100, function () {
					$this.remove();
				});
			});
		});
	}
	
	
	function removeNotices(selector) {
		$(selector).find('.xts-notice').remove();
	}
	

	function updateConditions($ruleRow) {
		 $ruleRow.find('.xts-discount-from input').attr('required', true);
		 $ruleRow.find('.xts-discount-type select').attr('required', true);
		 $ruleRow.find('.xts-discount-amount-value:not(.xts-hidden) input').attr('required', true);
		 $ruleRow.find('.xts-discount-percentage-value:not(.xts-hidden) input').attr('required', true);

		 $ruleRow.find('.xts-discount-type select').on('change', function() {
			  let $discountTypeSelect = $(this);
			  let $discountTypeWrapper = $discountTypeSelect.parent();
			  let $discountAmountInputWrapper = $discountTypeWrapper.siblings('.xts-discount-amount-value');
			  let $discountPercentageInputWrapper = $discountTypeWrapper.siblings('.xts-discount-percentage-value');
			  let $discountAmountInput = $discountAmountInputWrapper.find('input');
			  let $discountPercentageInput = $discountPercentageInputWrapper.find('input');

			  if ( 'amount' === $discountTypeSelect.val() ) {
					$discountAmountInputWrapper.removeClass('xts-hidden');
					$discountPercentageInputWrapper.addClass('xts-hidden');

					$discountAmountInput.attr('required', true);
					$discountPercentageInput.attr('required', false);
			  } else if ( 'percentage' === $discountTypeSelect.val() ) {
					$discountPercentageInputWrapper.removeClass('xts-hidden');
					$discountAmountInputWrapper.addClass('xts-hidden');

					$discountPercentageInput.attr('required', true);
					$discountAmountInput.attr('required', false);
			  }
		 })
	}

	$('#post:has(.xts-options)').on('submit', function(e){
		 if ( ! validate() ) {
			  e.preventDefault();
		 }
	});

	$(document)
		 .ready( function() {
			  $('select.xts-discount-condition-query:not(.xts-hidden)').each((key, field) => {
					var $querySelect  = $( field );
					var conditionType = $querySelect.parents('.xts-discount').find('select.xts-discount-condition-type').val();

					conditionQueryFieldInit( conditionType, $querySelect );
			  });

			  $('.xts_discount_rules-field .xts-controls-wrapper > .xts-discount:not(.title)').each((key,ruleRow) => {
					updateConditions( $(ruleRow) );
			  });
		 })
		 .on('change', 'select.xts-discount-condition-type', function() {
			  var $this = $(this);
			  var conditionType = $this.val();
			  var $querySelect = $this.parents('.xts-discount').find('select.xts-discount-condition-query');

			  conditionQueryFieldInit( conditionType, $querySelect );
		 })
		 .on('click', '.xts_discount_rules-field .xts-add-row', function() {
			  let ruleType = $('#xts_rule_type').val();
			  let $ruleRows = $('.xts_discount_rules-field .xts-controls-wrapper > .xts-discount:not(.title)');
			  if ( 'undefined' === typeof ruleType ) {
					ruleType = 'bulk';
			  }

			  if ( 'bulk' !== ruleType ) {
					return;
			  }

			  $ruleRows.each((key,ruleRow) => {
					let $ruleRow = $(ruleRow);

					updateConditions( $ruleRow );

					if ( key !== $ruleRows.length - 1 ) {
						 $ruleRow.find('.xts-discount-to input').attr('required', true);
					}
			  });
		 })
		 .on('click', '.column-xts_discounts_status .xts-switcher-btn', function() {
			  var $switcher = $(this);

			  $switcher.addClass('xts-loading');

			  $.ajax({
					url     : xtsAdminConfig.ajaxUrl,
					method  : 'POST',
					data    : {
						 action  : 'xts_woo_discounts_change_status',
						 id      : $switcher.data('id'),
						 status  : 'publish' === $switcher.data('status') ? 'draft' : 'publish',
						 security: xtsAdminConfig.get_theme_settings_data_nonce
					},
					dataType: 'json',
					success : function(response) {
						 $switcher.replaceWith(response.new_html);
					},
					error   : function(error) {
						 console.error(error);
					}
			  });
		 });
})(jQuery)
