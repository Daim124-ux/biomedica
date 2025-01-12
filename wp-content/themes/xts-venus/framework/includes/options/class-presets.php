<?php
/**
 * Static singleton class for presets functions.
 *
 * @package xts
 */

namespace XTS\Options;

use XTS\Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Create presets post type and functionality.
 */
class Presets extends Singleton {
	/**
	 * All presets.
	 *
	 * @var array
	 */
	private static $presets;
	/**
	 * Options set prefix.
	 *
	 * @var array
	 */
	public static $opt_name = XTS_THEME_SLUG;

	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'wp_ajax_xts_new_preset_action', array( $this, 'new_preset_action' ) );
		add_action( 'wp_ajax_xts_remove_preset_action', array( $this, 'remove_preset_action' ) );
		add_action( 'wp_ajax_xts_save_preset_conditions_action', array( $this, 'save_preset_conditions_action' ) );
		add_action( 'wp_ajax_xts_get_entity_ids_action', array( $this, 'get_entity_ids_action' ) );

		$this->load_presets();
	}

	/**
	 * Load presets from the database.
	 *
	 * @since 1.0.0
	 */
	public function load_presets() {
		$presets = get_option( 'xts-' . self::$opt_name . '-options-presets' );

		if ( ! $presets || empty( $presets ) ) {
			$presets = array();
		}

		self::$presets = $presets;
	}

	/**
	 * AJAX action for saving preset conditions.
	 *
	 * @since 1.0.0
	 */
	public function save_preset_conditions_action() {
		check_ajax_referer( 'xts-preset-form', 'security' );

		$id   = isset( $_POST['preset'] ) ? (int) sanitize_text_field( $_POST['preset'] ) : false; // phpcs:ignore
		$name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : ''; // phpcs:ignore

		$condition = array(
			'relation' => 'OR',
			'rules'    => array(),
		);

		if ( $id && isset( $_POST['data'] ) && is_array( $_POST['data'] ) ) {
			foreach ( $_POST['data'] as $key => $rule ) { // phpcs:ignore
				$condition['rules'][] = wp_parse_args(
					$rule,
					array(
						'type'       => '',
						'comparison' => '=',
						'post_type'  => '',
						'taxonomy'   => '',
						'custom'     => '',
						'value_id'   => '',
					)
				);
			}
		}

		$this->update_preset_conditions( $id, $condition, $name );

		$this->ajax_response(
			array(
				'error_msg'   => esc_html__( 'Something went wrong during the AJAX request.', 'xts-theme' ),
				'success_msg' => esc_html__( 'Options preset has been successfully saved.', 'xts-theme' ),
			)
		);

		wp_die();
	}

	/**
	 * Update preset conditions.
	 *
	 * @param integer $id Preset's id.
	 * @param array   $condition Conditions array.
	 * @param string  $name Name presets.
	 *
	 * @since 1.0.0
	 */
	public function update_preset_conditions( $id, $condition, $name = '' ) {
		self::$presets[ $id ]['condition'] = $condition;

		if ( ! empty( $name ) ) {
			self::$presets[ $id ]['name'] = $name;
		}

		$this->update_presets();
	}

	/**
	 * Create preset AJAX action.
	 *
	 * @since 1.0.0
	 */
	public function new_preset_action() {
		check_ajax_referer( 'xts-preset-form', 'security' );

		$name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : 'New preset'; // phpcs:ignore

		$id = $this->add_preset( $name );

		$this->ajax_response(
			array(
				'id' => $id,
			)
		);

		wp_die();
	}

	/**
	 * Remove preset AJAX action.
	 *
	 * @since 1.0.0
	 */
	public function remove_preset_action() {
		check_ajax_referer( 'xts-preset-form', 'security' );

		$id = isset( $_POST['id'] ) ? (int) sanitize_text_field( $_POST['id'] ) : false; // phpcs:ignore

		if ( ! $id ) {
			wp_die();
		}

		$this->remove_preset( $id );

		$this->ajax_response();

		wp_die();
	}

	/**
	 * Create a preset in the database.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $name Preset name.
	 *
	 * @return int|string|null
	 */
	public function add_preset( $name ) {
		$all = self::get_all();

		end( $all );

		$last_id = key( $all );

		if ( empty( $all ) ) {
			$last_id = apply_filters( 'xts_presets_start_id', 0 );
		}

		$id = $last_id + 1;

		$new_preset = array(
			'id'        => $id,
			'name'      => $name,
			'condition' => array(),
		);

		self::$presets[ $id ] = $new_preset;

		$this->update_presets();

		return $id;
	}

	/**
	 * Remove preset from the database.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id Remove preset by id.
	 */
	public function remove_preset( $id ) {
		if ( ! isset( self::$presets[ $id ] ) ) {
			return;
		}

		unset( self::$presets[ $id ] );

		$this->update_presets();
	}

	/**
	 * Update presets option.
	 *
	 * @since 1.0.0
	 */
	public function update_presets() {
		update_option( 'xts-' . self::$opt_name . '-options-presets', self::$presets );
	}

	/**
	 * Send AJAX response data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Additional data array.
	 */
	public function ajax_response( $data = array() ) {
		ob_start();
		self::output_ui();
		$ui = ob_get_clean();

		echo wp_json_encode(
			array_merge(
				array(
					'ui' => $ui,
				),
				$data
			)
		);
	}

	/**
	 * AJAX action to load entities names.
	 *
	 * @since 1.0.0
	 */
	public function get_entity_ids_action() {
		check_ajax_referer( 'xts-preset-form', 'security' );

		$type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : false; // phpcs:ignore
		$name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : false; // phpcs:ignore

		$items = array();

		switch ( $type ) {
			case 'term_id':
			case 'single_posts_term_id':
				$args = array(
					'hide_empty' => false,
					'fields'     => 'all',
					'name__like' => $name,
				);

				$terms = get_terms( $args );

				if ( count( $terms ) > 0 ) {
					foreach ( $terms as $term ) {
						$items[] = array(
							'id'   => $term->term_id,
							'text' => $term->name,
						);
					}
				}
				break;
			case 'post_id':
				$args = array(
					's'              => $name,
					'post_type'      => get_post_types( array( 'public' => true ) ),
					'posts_per_page' => 100,
				);

				$posts = get_posts( $args );

				if ( count( $posts ) > 0 ) {
					foreach ( $posts as $post ) {
						$items[] = array(
							'id'   => $post->ID,
							'text' => $post->post_title,
						);
					}
				}
				break;
		}

		echo wp_json_encode(
			array(
				'results' => $items,
			)
		);

		wp_die();
	}

	/**
	 * Output Presets UI.
	 *
	 * @since 1.0.0
	 */
	public static function output_ui() {
		?>
		<div class="xts-presets-wrapper" data-current-id="<?php echo esc_attr( self::get_current_preset() ); ?>" data-preset-url="<?php echo esc_url( admin_url( 'admin.php?page=xtemos_options&preset=' ) ); ?>" data-base-url="<?php echo esc_url( admin_url( 'admin.php?page=xtemos_options' ) ); ?>">
			<div class="xts-dashboard-title">
				<h3>
					<?php esc_html_e( 'Options presets', 'xts-theme' ); ?>
				</h3>
			</div>

			<?php if ( self::get_current_preset() ) : ?>
				<?php $preset_name = self::$presets[ self::get_current_preset() ]['name']; ?>
				<div class="xts-current-preset">
					<div class="xts-presets-response"></div>
					<div class="xts-preset-conditions">
						<form action="">
							<div class="xts-presets-title">
								<input type="hidden" class="xts-input-edit-preset-rule" name="edit-preset-rule" value="<?php echo esc_html( $preset_name ); ?>">
								<h4>
									<span class="xts-presets-title-inner">
										<?php echo esc_html( $preset_name ); ?>
									</span>
									<?php esc_html_e( 'rules', 'xts-theme' ); ?>
								</h4>
								<a href="#" class="xts-edit-preset-rule xf-pencil">
									<?php esc_html_e( 'Edit', 'xts-theme' ); ?>
								</a>
							</div>
							<?php self::display_current_conditions(); ?>

							<?php wp_nonce_field( 'xts-preset-form' ); ?>

							<button type="submit" class="xts-button xts-btn xts-btn-primary xts-btn-shadow xf-save">
								<?php esc_html_e( 'Save preset', 'xts-theme' ); ?>
							</button>

							<a href="#" class="xts-add-preset-rule xts-btn-bordered xts-btn-primary">
								<?php esc_html_e( 'Add new rule', 'xts-theme' ); ?>
							</a>
						</form>
					</div>
				</div>

				<div class="xts-rule-template">
					<?php self::rule_template(); ?>
				</div>
			<?php else : ?>
				<?php wp_nonce_field( 'xts-preset-form' ); ?>
			<?php endif; ?>

			<div class="xts-presets-list xts-design-list">
				<?php if ( is_array( self::$presets ) && count( self::$presets ) ) : ?>
					<h4>
						<?php esc_html_e( 'All options presets', 'xts-theme' ); ?>
					</h4>

					<ul>
						<?php foreach ( self::$presets as $id => $preset ) : ?>
							<li class="<?php echo self::get_current_preset() === $id ? 'xts-active' : ''; ?>">
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=xtemos_options&preset=' . $id ) ); ?>">
									<span class="xts-preset-title"><?php echo esc_html( $preset['name'] ); ?></span>
									<button href="#" class="xts-remove-preset-btn xts-btn-bordered xts-btn-disable xts-size-s" data-id="<?php echo esc_attr( $id ); ?>">
										<?php echo esc_html__( 'Delete', 'xts-theme' ); ?>
									</button>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<div class="xts-notice xts-info">
						<?php esc_html_e( 'There are no custom presets yet.', 'xts-theme' ); ?>
					</div>
				<?php endif; ?>

				<button class="xts-add-new-preset xts-btn-primary xts-btn-bordered">
					<?php esc_html_e( 'Add a new preset', 'xts-theme' ); ?>
				</button>
			</div>
		</div>
		<?php
	}

	/**
	 * Display current preset conditions form.
	 *
	 * @since 1.0.0
	 */
	public static function display_current_conditions() {
		$preset    = self::$presets[ self::get_current_preset() ];
		$condition = $preset['condition'];

		?>
		<div class="xts-condition-rules">
			<?php if ( ! empty( $condition['rules'] ) ) : ?>
				<?php foreach ( $condition['rules'] as $rule ) : ?>
					<?php self::rule_template( false, $rule ); ?>
				<?php endforeach; ?>
			<?php else : ?>
				<?php self::rule_template( false ); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * HTML template for one rule.
	 *
	 * @since 1.0.0
	 *
	 * @param bool  $hidden Is this template hidden.
	 * @param array $rule   Rule array.
	 */
	public static function rule_template( $hidden = true, $rule = array() ) {
		$rule = wp_parse_args(
			$rule,
			array(
				'type'       => '',
				'comparison' => '=',
				'post_type'  => '',
				'taxonomy'   => '',
				'custom'     => '',
				'value_id'   => '',
				'user_role'  => '',
			)
		);

		$post_types = get_post_types(
			array(
				'public' => true,
			)
		);

		$taxonomies = get_taxonomies(
			array(
				'public' => true,
			)
		);

		$custom_conditions = apply_filters(
			'xts_get_custom_conditions_for_preset',
			array(
				'search'         => 'Search results',
				'blog'           => 'Default "Your Latest Posts" screen',
				'front'          => 'Front page',
				'archives'       => 'All archives',
				'author'         => 'Author archives',
				'error404'       => '404 error screens',
				'shop'           => 'Shop page',
				'single_product' => 'Single product',
				'cart'           => 'Cart page',
				'checkout'       => 'Checkout page',
				'account'        => 'Account pages',
				'is_mobile'      => 'Is mobile device',
				'is_rtl'         => 'Is RTL',
			)
		);

		$title = false;

		if ( 'post_id' === $rule['type'] && ! empty( $rule['value_id'] ) ) {
			$title = get_the_title( $rule['value_id'] );
		}

		if ( ( 'term_id' === $rule['type'] || 'single_posts_term_id' === $rule['type'] ) && ! empty( $rule['value_id'] ) ) {
			$taxonomies = get_taxonomies();

			foreach ( $taxonomies as $taxonomy ) {
				$term_object = get_term_by( 'id', $rule['value_id'], $taxonomy );

				if ( $term_object ) {
					$title = $term_object->name;
				}
			}
		}

		$classes = $hidden ? 'xts-hidden' : '';

		?>
		<div class="xts-rule <?php echo esc_attr( $classes ); ?>">
			<select class="xts-rule-type">
				<option value=""><?php esc_html_e( '--type--', 'xts-theme' ); ?></option>
				<option value="post_type" <?php selected( 'post_type', $rule['type'] ); ?>><?php esc_html_e( 'Post type', 'xts-theme' ); ?></option>
				<option value="post_id" <?php selected( 'post_id', $rule['type'] ); ?>><?php esc_html_e( 'Post ID', 'xts-theme' ); ?></option>
				<option value="taxonomy" <?php selected( 'taxonomy', $rule['type'] ); ?>><?php esc_html_e( 'Taxonomy', 'xts-theme' ); ?></option>
				<option value="term_id" <?php selected( 'term_id', $rule['type'] ); ?>><?php esc_html_e( 'Term ID', 'xts-theme' ); ?></option>
				<option value="single_posts_term_id" <?php selected( 'single_posts_term_id', $rule['type'] ); ?>><?php esc_html_e( 'Single posts from term', 'xts-theme' ); ?></option>
				<option value="user_role" <?php selected( 'user_role', $rule['type'] ); ?>><?php esc_html_e( 'User role', 'xts-theme' ); ?></option>
				<option value="custom" <?php selected( 'custom', $rule['type'] ); ?>><?php esc_html_e( 'Custom', 'xts-theme' ); ?></option>
			</select>

			<select class="xts-rule-comparison">
				<option value="equals" <?php selected( 'equals', $rule['comparison'] ); ?>><?php esc_html_e( 'equals', 'xts-theme' ); ?></option>
				<option value="not_equals" <?php selected( 'not_equals', $rule['comparison'] ); ?>><?php esc_html_e( 'not equals', 'xts-theme' ); ?></option>
			</select>

			<select class="xts-rule-post-type <?php echo 'post_type' !== $rule['type'] ? 'xts-hidden' : ''; ?>">
				<?php foreach ( $post_types as $key => $type ) : ?>
					<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $type, $rule['post_type'] ); ?>><?php echo esc_html( $type ); ?></option>
				<?php endforeach; ?>
			</select>

			<select class="xts-rule-taxonomy <?php echo 'taxonomy' !== $rule['type'] ? 'xts-hidden' : ''; ?>">
				<?php foreach ( $taxonomies as $key => $taxonomy ) : ?>
					<option value="<?php echo esc_attr( $taxonomy ); ?>" <?php selected( $taxonomy, $rule['taxonomy'] ); ?>><?php echo esc_html( $taxonomy ); ?></option>
				<?php endforeach; ?>
			</select>

			<select class="xts-rule-custom <?php echo 'custom' !== $rule['type'] ? 'xts-hidden' : ''; ?>">
				<?php foreach ( $custom_conditions as $key => $condition ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $rule['custom'] ); ?>><?php echo esc_html( $condition ); ?></option>
				<?php endforeach; ?>
			</select>

			<div class="xts-rule-value-wrapper <?php echo 'post_id' !== $rule['type'] && 'term_id' !== $rule['type'] && 'single_posts_term_id' !== $rule['type'] ? 'xts-hidden' : ''; ?>">
				<select data-placeholder="<?php esc_attr_e( 'Start typing...', 'xts-theme' ); ?>" class="xts-rule-value-id" data-value="<?php echo esc_attr( $rule['value_id'] ); ?>">
					<?php if ( $title ) : ?>
						<option value="<?php echo esc_attr( $rule['value_id'] ); ?>" selected="selected"><?php echo esc_html( $title ); ?></option>
					<?php endif; ?>
				</select>
			</div>

			<select class="xts-rule-user-role<?php echo 'user_role' !== $rule['type'] ? ' xts-hidden' : ''; ?>">
				<?php foreach ( get_editable_roles() as $user_role_id => $user_role ) : ?>
					<option value="<?php echo esc_html( $user_role_id ); ?>" <?php selected( $user_role_id, $rule['user_role'] ); ?>>
						<?php echo esc_html( $user_role['name'] ); ?>
					</option>
				<?php endforeach ?>
			</select>

			<a href="#" class="xts-remove-preset-rule">
				<?php esc_html_e( 'Delete', 'xts-theme' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Get currently editing preset.
	 *
	 * @since 1.0.0
	 */
	public static function get_current_preset() {
		return isset( $_REQUEST['preset'] ) && isset( self::$presets[ $_REQUEST['preset'] ] ) ? intval( $_REQUEST['preset'] ) : false; // phpcs:ignore
	}

	/**
	 * Presets getter.
	 *
	 * @since 1.0.0
	 */
	public static function get_all() {
		return self::$presets;
	}

	/**
	 * Get presets that active for the current page.
	 *
	 * @since 1.0.0
	 */
	public static function get_active_presets() {
		$all            = self::get_all();
		$active_presets = array();

		foreach ( $all as $preset ) {
			if ( empty( $preset['condition'] ) || ! isset( $preset['condition']['rules'] ) || empty( $preset['condition']['rules'] ) ) {
				continue;
			}

			$rules = $preset['condition']['rules'];
			foreach ( $rules as $rule ) {
				$is_active = false;
				switch ( $rule['type'] ) {
					case 'post_type':
						$condition = get_post_type() === $rule['post_type'];
						$is_active = 'equals' === $rule['comparison'] ? $condition : ! $condition;
						break;
					case 'post_id':
						if ( $rule['value_id'] && ! is_admin() ) {
							$condition = (int) xts_get_page_id() === (int) $rule['value_id'];
							$is_active = 'equals' === $rule['comparison'] ? $condition : ! $condition;
						}
						break;
					case 'term_id':
						$object  = get_queried_object();
						$term_id = is_object( $object ) && property_exists( $object, 'term_id' ) ? get_queried_object()->term_id : false;

						if ( $term_id ) {
							$condition = (int) $term_id === (int) $rule['value_id'];
							$is_active = 'equals' === $rule['comparison'] ? $condition : ! $condition;
						}
						break;
					case 'single_posts_term_id':
						if ( is_singular() ) {
							$terms = wp_get_post_terms( xts_get_page_id(), get_taxonomies(), array( 'fields' => 'ids' ) );

							if ( $terms ) {
								$condition = in_array( $rule['value_id'], $terms, false );
								$is_active = 'equals' === $rule['comparison'] ? $condition : ! $condition;
							}
						}
						break;
					case 'taxonomy':
						$object = get_queried_object();

						$taxonomy = is_object( $object ) && property_exists( $object, 'taxonomy' ) ? get_queried_object()->taxonomy : false;

						if ( $taxonomy ) {
							$condition = $taxonomy === $rule['taxonomy'];
							$is_active = 'equals' === $rule['comparison'] ? $condition : ! $condition;
						}
						break;
					case 'user_role':
						$condition = in_array( $rule['user_role'], xts_get_current_user_roles(), true );
						$is_active = 'equals' === $rule['comparison'] ? $condition : ! $condition;
						break;
					case 'custom':
						switch ( $rule['custom'] ) {
							case 'search':
								$is_active = 'equals' === $rule['comparison'] ? is_search() : ! is_search();
								break;
							case 'blog':
								$condition = (int) xts_get_page_id() === (int) get_option( 'page_for_posts' );
								$is_active = 'equals' === $rule['comparison'] ? $condition : ! $condition;
								break;
							case 'front':
								$condition = (int) xts_get_page_id() === (int) get_option( 'page_on_front' );
								$is_active = 'equals' === $rule['comparison'] ? $condition : ! $condition;
								break;
							case 'archives':
								$is_active = 'equals' === $rule['comparison'] ? is_archive() : ! is_archive();
								break;
							case 'author':
								$is_active = 'equals' === $rule['comparison'] ? is_author() : ! is_author();
								break;
							case 'error404':
								$is_active = 'equals' === $rule['comparison'] ? is_404() : ! is_404();
								break;
							case 'is_mobile':
								$is_active = 'equals' === $rule['comparison'] ? wp_is_mobile() : ! wp_is_mobile();
								break;
							case 'is_rtl':
								$is_active = 'equals' === $rule['comparison'] ? is_rtl() : ! is_rtl();
								break;
						}

						if ( xts_is_woocommerce_installed() ) {
							switch ( $rule['custom'] ) {
								case 'shop':
									$is_active = 'equals' === $rule['comparison'] ? is_shop() : ! is_shop();
									break;
								case 'single_product':
									$is_active = 'equals' === $rule['comparison'] ? is_product() : ! is_product();
									break;
								case 'cart':
									$is_active = 'equals' === $rule['comparison'] ? is_cart() : ! is_cart();
									break;
								case 'checkout':
									$is_active = 'equals' === $rule['comparison'] ? is_checkout() : ! is_checkout();
									break;
								case 'account':
									$is_active = 'equals' === $rule['comparison'] ? is_account_page() : ! is_account_page();
									break;
							}
						}
						break;
				}

				if ( isset( $_GET['page'] ) && isset( $_GET['preset'] ) && 'xtemos_options' === $_GET['page'] ) { // phpcs:ignore
					$is_active    = true;
					$preset['id'] = $_GET['preset']; // phpcs:ignore
				}

				if ( $is_active ) {
					$active_presets[] = $preset['id'];
				}
			}
		}

		foreach ( $all as $preset ) {
			if ( isset( $_GET['opt'] ) && $preset['name'] === $_GET['opt'] ) { // phpcs:ignore
				array_push( $active_presets, $preset['id'] );
			}
		}

		return apply_filters( 'xts_active_options_presets', array_unique( $active_presets ), $all );
	}
}

Presets::get_instance();
