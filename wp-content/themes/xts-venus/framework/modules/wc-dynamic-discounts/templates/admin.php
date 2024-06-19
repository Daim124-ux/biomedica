<?php
/**
 * Admin setting page template.
 *
 * @var array $args Arguments for render template.
 * @var array $current_args Arguments from databased.
 * @var string $max_priority Max saved priority.
 * @package xts
 */

use XTS\Modules\Dynamic_Discounts\Admin;

$discount_rules              = ! empty( $current_args['discount_rules'] ) ? $current_args['discount_rules'] : $args['discount_rules'];
$discount_condition          = ! empty( $current_args['discount_condition'] ) ? $current_args['discount_condition'] : $args['discount_condition'];
$selected_discount_condition = array();
?>

<div class="xts-options xts-metaboxes">
	<?php wp_nonce_field( 'save_xts_woo_discounts', 'xts_woo_discounts_meta_boxes_nonce' ); ?>

	<div class="xts-fields-tabs">
		<div class="xts-sections">
			<div class="xts-fields-section xts-active-section" data-id="general">
				<div class="xts-section-content xts-row">
					<div class="xts-field xts-settings-field xts_rule_type-field xts-col <?php echo count( $args['xts_rule_type'] ) <= 1 ? 'xts-hidden' : ''; ?>">
						<div class="xts-field-title">
							<span for="xts_rule_type">
								<?php echo esc_html__( 'Rule type', 'xts-theme' ); ?>
							</span>
						</div>
						<div class="xts-field-inner">
							<select id="xts_rule_type" class="xts-select" name="xts_rule_type" aria-label="<?php esc_attr_e( 'Rule type', 'xts-theme' ); ?>">
								<?php foreach ( $args['xts_rule_type'] as $key => $label ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php echo isset( $current_args['xts_rule_type'] ) ? selected( $current_args['xts_rule_type'], $key ) : ''; ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="xts-field xts-settings-field xts_discount_priority-field xts-col-6 xts-col">
						<div class="xts-field-title">
							<span for="xts_discount_priority">
								<?php echo esc_html__( 'Priority', 'xts-theme' ); ?>
							</span>
						</div>
						<div class="xts-field-inner">
							<input type="number" name="xts_discount_priority" id="xts_discount_priority" min="1" placeholder="<?php esc_attr_e( 'Priority', 'xts-theme' ); ?>" aria-label="<?php esc_attr_e( 'Discount priority', 'xts-theme' ); ?>" value="<?php echo ! empty( $current_args['xts_discount_priority'] ) ? esc_attr( $current_args['xts_discount_priority'] ) : esc_attr( (int) $max_priority + 1 ); ?>">
						</div>
						<p class="xts-description">
							<?php esc_html_e( 'Set priority for current discount rules. This will be useful if several rules apply to one product.', 'xts-theme' ); ?>
						</p>
					</div>
					<div class="xts-field xts-settings-field xts_discount_quantities-field xts-col-6 xts-col <?php echo count( $args['discount_quantities'] ) <= 1 ? 'xts-hidden' : ''; ?>">
						<div class="xts-field-title">
							<span for="discount_quantities">
								<?php echo esc_html__( 'Quantities', 'xts-theme' ); ?>
							</span>
						</div>
						<div class="xts-field-inner">
							<select id="discount_quantities" class="xts-select" name="discount_quantities" aria-label="<?php esc_attr_e( 'Quantities', 'xts-theme' ); ?>">
								<?php foreach ( $args['discount_quantities'] as $key => $label ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php echo isset( $current_args['discount_quantities'] ) ? selected( $current_args['discount_quantities'], $key ) : ''; ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<p class="xts-description">
							<?php esc_html_e( 'Choose "Individual variation" to have variations of a variable product count as an individual product.', 'xts-theme' ); ?>
						</p>
					</div>
					
					<div class="xts-group-title">
						<span>
							<?php echo esc_html__( 'Discount rules', 'xts-theme' ); ?>
						</span>
					</div>
					<div class="xts-fields-group row">
						<div class="xts-field xts-select_with_table-control xts-col xts_discount_rules-field" data-dependency="xts_rule_type:equals:bulk;">
							<div class="xts-field-inner">
								<div class="xts-item-template xts-hidden">
									<div class="xts-table-controls xts-discount">
										<div class="xts-discount-from">
											<input type="number" name="discount_rules[{{index}}][xts_discount_rules_from]" id="xts_discount_rules_from_{{index}}"  min="0" placeholder="<?php esc_attr_e( 'From', 'xts-theme' ); ?>" aria-label="<?php esc_attr_e( 'Discount rules from', 'xts-theme' ); ?>" disabled>
										</div>
										<div class="xts-discount-to">
											<input type="number" name="discount_rules[{{index}}][xts_discount_rules_to]" id="xts_discount_rules_to_{{index}}"  min="0" placeholder="<?php esc_attr_e( 'To', 'xts-theme' ); ?>" aria-label="<?php esc_attr_e( 'Discount rules to', 'xts-theme' ); ?>" disabled>
										</div>
										<div class="xts-discount-type">
											<select id="xts_discount_type_{{index}}" class="xts-select" name="discount_rules[{{index}}][xts_discount_type]" aria-label="<?php esc_attr_e( 'Discount type', 'xts-theme' ); ?>" disabled>
												<?php foreach ( $args['discount_rules'][0]['xts_discount_type'] as $key => $label ) : ?>
													<option value="<?php echo esc_attr( $key ); ?>">
														<?php echo esc_html( $label ); ?>
													</option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="xts-discount-amount-value">
											<div class="xts-field-inner">
												<input type="number" name="discount_rules[{{index}}][xts_discount_amount_value]" id="xts_discount_amount_value_{{index}}"  min="0" placeholder="0.00" step="0.01" aria-label="<?php esc_attr_e( 'Discount amount value', 'xts-theme' ); ?>" disabled>
											</div>
										</div>
										<div class="xts-discount-percentage-value xts-hidden">
											<div class="xts-field-inner">
												<input type="number" name="discount_rules[{{index}}][xts_discount_percentage_value]" id="xts_discount_percentage_value_{{index}}"  min="0" max="100" placeholder="0.00" step="0.01" aria-label="<?php esc_attr_e( 'Discount percentage value', 'xts-theme' ); ?>" disabled>
											</div>
										</div>
										<div class="xts-control-remove">
											<a href="#" class="xts-remove-item xts-btn-bordered xts-btn-disable xf-remove"></a>
										</div>
									</div>
								</div>
								<div class="xts-controls-wrapper">
									<div class="xts-table-controls xts-discount title">
										<div class="xts-discount-from">
											<span><?php echo esc_html__( 'From', 'xts-theme' ); ?></span>
										</div>
										<div class="xts-discount-to">
											<span><?php echo esc_html__( 'To', 'xts-theme' ); ?></span>
										</div>
										<div class="xts-discount-type">
											<span><?php echo esc_html__( 'Type', 'xts-theme' ); ?></span>
										</div>
										<div class="xts-discount-value">
											<span><?php echo esc_html__( 'Value', 'xts-theme' ); ?></span>
										</div>
										<div class="xts-control-remove"></div>
									</div>
									<?php foreach ( $discount_rules as $id => $rule_args ) : //phpcs:ignore. ?>
										<div class="xts-table-controls xts-discount">
											<div class="xts-discount-from">
												<input type="number" name="discount_rules[<?php echo esc_attr( $id ); ?>][xts_discount_rules_from]" id="xts_discount_rules_from_<?php echo esc_attr( $id ); ?>"  min="0" placeholder="<?php esc_attr_e( 'From', 'xts-theme' ); ?>" aria-label="<?php esc_attr_e( 'Discount rules from', 'xts-theme' ); ?>" value="<?php echo isset( $current_args['discount_rules'][ $id ]['xts_discount_rules_from'] ) ? esc_attr( $current_args['discount_rules'][ $id ]['xts_discount_rules_from'] ) : ''; ?>">
											</div>
											<div class="xts-discount-to">
												<input type="number" name="discount_rules[<?php echo esc_attr( $id ); ?>][xts_discount_rules_to]" id="xts_discount_rules_to_<?php echo esc_attr( $id ); ?>"  min="0" placeholder="<?php esc_attr_e( 'To', 'xts-theme' ); ?>" aria-label="<?php esc_attr_e( 'Discount rules to', 'xts-theme' ); ?>" value="<?php echo isset( $current_args['discount_rules'][ $id ]['xts_discount_rules_to'] ) ? esc_attr( $current_args['discount_rules'][ $id ]['xts_discount_rules_to'] ) : ''; ?>">
											</div>
											<div class="xts-discount-type">
												<select id="xts_discount_type_<?php echo esc_attr( $id ); ?>" class="xts-select" name="discount_rules[<?php echo esc_attr( $id ); ?>][xts_discount_type]" aria-label="<?php esc_attr_e( 'Discount type', 'xts-theme' ); ?>">
													<?php foreach ( $args['discount_rules'][0]['xts_discount_type'] as $key => $label ) : ?>
														<option value="<?php echo esc_attr( $key ); ?>" <?php echo isset( $current_args['discount_rules'][ $id ]['xts_discount_type'] ) ? selected( $current_args['discount_rules'][ $id ]['xts_discount_type'], $key, false ) : ''; ?>>
															<?php echo esc_html( $label ); ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
											<div class="xts-discount-amount-value <?php echo isset( $current_args['discount_rules'][ $id ] ) && isset( $current_args['discount_rules'][ $id ]['xts_discount_type'] ) && 'amount' === $current_args['discount_rules'][ $id ]['xts_discount_type'] || ! isset( $current_args['discount_rules'][ $id ] ) ? '' : 'xts-hidden'; ?>">
												<div class="xts-field-inner">
													<input type="number" name="discount_rules[<?php echo esc_attr( $id ); ?>][xts_discount_amount_value]" id="xts_discount_amount_value_<?php echo esc_attr( $id ); ?>"  min="0" placeholder="0.00" step="0.01" aria-label="<?php esc_attr_e( 'Discount amount value', 'xts-theme' ); ?>" value="<?php echo isset( $current_args['discount_rules'][ $id ]['xts_discount_amount_value'] ) ? esc_attr( $current_args['discount_rules'][ $id ]['xts_discount_amount_value'] ) : ''; ?>">
												</div>
											</div>
											<div class="xts-discount-percentage-value <?php echo isset( $current_args['discount_rules'][ $id ] ) && isset( $current_args['discount_rules'][ $id ]['xts_discount_type'] ) && 'percentage' === $current_args['discount_rules'][ $id ]['xts_discount_type'] ? '' : 'xts-hidden'; ?>">
												<div class="xts-field-inner">
													<input type="number" name="discount_rules[<?php echo esc_attr( $id ); ?>][xts_discount_percentage_value]" id="xts_discount_percentage_value_<?php echo esc_attr( $id ); ?>"  min="0" max="100" placeholder="0.00" step="0.01" aria-label="<?php esc_attr_e( 'Discount percentage value', 'xts-theme' ); ?>" value="<?php echo isset( $current_args['discount_rules'][ $id ]['xts_discount_percentage_value'] ) ? esc_attr( $current_args['discount_rules'][ $id ]['xts_discount_percentage_value'] ) : ''; ?>">
												</div>
											</div>
											<div class="xts-control-remove">
												<a href="#" class="xts-remove-item xts-btn-bordered xts-btn-disable xf-remove"></a>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
								<a href="#" class="xts-add-row xts-inline-btn xts-inline-btn-add">
									<?php esc_html_e( 'Add new rule', 'xts-theme' ); ?>
								</a>
							</div>
						</div>
					</div>

					<div class="xts-group-title">
						<span>
							<?php echo esc_html__( 'Discount condition', 'xts-theme' ); ?>
						</span>
					</div>
					<div class="xts-fields-group row">
						<div class="xts-field xts-settings-field xts-select_with_table-control xts_discount_condition-field">
							<div class="xts-field-inner xts-col">
								<div class="xts-item-template xts-hidden">
									<div class="xts-table-controls xts-discount">
										<div class="xts-discount-comparison-condition">
											<select class="xts-discount-comparison-condition" name="discount_condition[{{index}}][comparison]" aria-label="<?php esc_attr_e( 'Comparison condition', 'xts-theme' ); ?>" disabled>
												<?php foreach ( $args['discount_condition'][0]['comparison'] as $key => $label ) : ?>
													<option value="<?php echo esc_attr( $key ); ?>" >
														<?php echo esc_html( $label ); ?>
													</option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="xts-discount-condition-type">
											<select class="xts-discount-condition-type" name="discount_condition[{{index}}][type]" aria-label="<?php esc_attr_e( 'Condition type', 'xts-theme' ); ?>" disabled>
												<?php foreach ( $args['discount_condition'][0]['type'] as $key => $label ) : ?>
													<option value="<?php echo esc_attr( $key ); ?>">
														<?php echo esc_html( $label ); ?>
													</option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="xts-discount-condition-query xts-hidden">
											<select class="xts-discount-condition-query" name="discount_condition[{{index}}][query]" placeholder="<?php esc_attr_e( 'Start typing...', 'xts-theme' ); ?>" aria-label="<?php esc_attr_e( 'Condition query', 'xts-theme' ); ?>" disabled></select>
										</div>
										<div class="xts-discount-product-type-condition-query">
											<select class="xts-discount-product-type-condition-query" name="discount_condition[{{index}}][product-type-query]" aria-label="<?php esc_attr_e( 'Product type condition query', 'xts-theme' ); ?>" disabled>
												<?php foreach ( $args['discount_condition'][0]['product-type-query'] as $key => $label ) : ?>
													<option value="<?php echo esc_attr( $key ); ?>">
														<?php echo esc_html( $label ); ?>
													</option>
												<?php endforeach; ?>
											</select>
										</div>

										<div class="xts-control-remove">
											<a href="#" class="xts-remove-item xts-btn-bordered xts-btn-disable xf-remove"></a>
										</div>
									</div>
								</div>

								<div class="xts-controls-wrapper">
									<div class="xts-table-controls xts-discount title">
										<div class="xts-discount-comparison-condition">
											<span><?php esc_html_e( 'Comparison condition', 'xts-theme' ); ?></span>
										</div>
										<div class="xts-discount-condition-type">
											<span><?php esc_html_e( 'Condition type', 'xts-theme' ); ?></span>
										</div>
										<div class="xts-discount-condition-query <?php echo empty( $selected_discount_condition ) ? 'xts-hidden' : ''; ?>">
											<span><?php esc_html_e( 'Condition query', 'xts-theme' ); ?></span>
										</div>
										<div class="xts-control-remove"></div>
									</div>
			                        <?php foreach ( $discount_condition as $id => $condition_args ) : //phpcs:ignore. ?>
										<?php
										if ( ! empty( $current_args['discount_condition'][ $id ]['query'] ) && ! empty( $current_args['discount_condition'][ $id ]['type'] ) ) {
											$selected_discount_condition = Admin::get_instance()->get_saved_conditions_query( $current_args['discount_condition'][ $id ]['query'], $current_args['discount_condition'][ $id ]['type'] );
										}
										?>

										<div class="xts-table-controls xts-discount">
											<div class="xts-discount-comparison-condition">
												<select class="xts-discount-comparison-condition" name="discount_condition[<?php echo esc_attr( $id ); ?>][comparison]" aria-label="<?php esc_attr_e( 'Comparison condition', 'xts-theme' ); ?>">
													<?php foreach ( $args['discount_condition'][0]['comparison'] as $key => $label ) : ?>
														<option value="<?php echo esc_attr( $key ); ?>" <?php echo isset( $current_args['discount_condition'][ $id ]['comparison'] ) ? selected( $current_args['discount_condition'][ $id ]['comparison'], $key, false ) : ''; ?>>
															<?php echo esc_html( $label ); ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
											<div class="xts-discount-condition-type">
												<select class="xts-discount-condition-type" name="discount_condition[<?php echo esc_attr( $id ); ?>][type]" aria-label="<?php esc_attr_e( 'Condition type', 'xts-theme' ); ?>">
													<?php foreach ( $args['discount_condition'][0]['type'] as $key => $label ) : ?>
														<option value="<?php echo esc_attr( $key ); ?>" <?php echo isset( $current_args['discount_condition'][ $id ]['type'] ) ? selected( $current_args['discount_condition'][ $id ]['type'], $key, false ) : ''; ?>>
															<?php echo esc_html( $label ); ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
											<div class="xts-discount-condition-query <?php echo empty( $selected_discount_condition ) ? 'xts-hidden' : ''; ?>">
												<select class="xts-discount-condition-query" name="discount_condition[<?php echo esc_attr( $id ); ?>][query]" placeholder="<?php echo esc_attr__( 'Start typing...', 'xts-theme' ); ?>" aria-label="<?php esc_attr_e( 'Condition query', 'xts-theme' ); ?>">
													<?php if ( ! empty( $selected_discount_condition ) ) : ?>
														<option value="<?php echo esc_attr( $selected_discount_condition['id'] ); ?>" selected>
															<?php echo esc_html( $selected_discount_condition['text'] ); ?>
														</option>
													<?php endif; ?>
												</select>
											</div>
											<div class="xts-discount-product-type-condition-query <?php echo isset( $current_args['discount_condition'][ $id ] ) && ( 'product_type' !== $current_args['discount_condition'][ $id ]['type'] || ! isset( $current_args['discount_condition'][ $id ]['product-type-query'] ) ) || ! isset( $current_args['discount_condition'][ $id ] ) ? 'xts-hidden' : ''; ?>">
												<select class="xts-discount-product-type-condition-query" name="discount_condition[<?php echo esc_attr( $id ); ?>][product-type-query]" aria-label="<?php esc_attr_e( 'Product type condition query', 'xts-theme' ); ?>">
													<?php foreach ( $args['discount_condition'][0]['product-type-query'] as $key => $label ) : ?>
														<option value="<?php echo esc_attr( $key ); ?>" <?php echo isset( $current_args['discount_condition'][ $id ]['product-type-query'] ) ? selected( $current_args['discount_condition'][ $id ]['product-type-query'], $key, false ) : ''; ?>>
															<?php echo esc_html( $label ); ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>

											<div class="xts-control-remove">
												<a href="#" class="xts-remove-item xts-btn-bordered xts-btn-disable xf-remove"></a>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
								<a href="#" class="xts-add-row xts-inline-btn xts-inline-btn-add">
									<?php esc_html_e( 'Add new condition', 'xts-theme' ); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
