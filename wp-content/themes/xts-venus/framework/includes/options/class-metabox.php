<?php
/**
 * Create metabox object with fields.
 *
 * @package xts
 */

namespace XTS\Options;

use XTS\Framework\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Metabox class to store all fields for particular metaboxes created.
 */
class Metabox {
	/**
	 * Metabox ID.
	 *
	 * @var int
	 */
	private $_id; // phpcs:ignore

	/**
	 * Metabox title.
	 *
	 * @var string
	 */
	private $_title; // phpcs:ignore

	/**
	 * Taxonomies that we add this metabox to.
	 *
	 * @var array
	 */
	private $_taxonomies; // phpcs:ignore

	/**
	 * Post type array where this metabox will be displayed.
	 *
	 * @var array
	 */
	private $_post_types; // phpcs:ignore

	/**
	 * Fields array for this metabox. Array of Field objects.
	 *
	 * @var array
	 */
	private $_fields = array(); // phpcs:ignore

	/**
	 * Metaboxes may have sections as well.
	 *
	 * @var array
	 */
	private $_sections = array(); // phpcs:ignore

	/**
	 * Basic arguments array.
	 *
	 * @var array
	 */
	private $_args; // phpcs:ignore

	/**
	 * Can be post or term.
	 *
	 * @var array
	 */
	private $_object; // phpcs:ignore

	/**
	 * Array of field type and controls mapping.
	 *
	 * @var array
	 */
	private $_controls_classes = array( // phpcs:ignore
		'select'            => 'XTS\Options\Controls\Select',
		'text_input'        => 'XTS\Options\Controls\Text_Input',
		'switcher'          => 'XTS\Options\Controls\Switcher',
		'color'             => 'XTS\Options\Controls\Color',
		'checkbox'          => 'XTS\Options\Controls\Checkbox',
		'buttons'           => 'XTS\Options\Controls\Buttons',
		'upload'            => 'XTS\Options\Controls\Upload',
		'upload_list'       => 'XTS\Options\Controls\Upload_List',
		'background'        => 'XTS\Options\Controls\Background',
		'textarea'          => 'XTS\Options\Controls\Textarea',
		'typography'        => 'XTS\Options\Controls\Typography',
		'custom_fonts'      => 'XTS\Options\Controls\Custom_Fonts',
		'range'             => 'XTS\Options\Controls\Range',
		'editor'            => 'XTS\Options\Controls\Editor',
		'import'            => 'XTS\Options\Controls\Import',
		'size_guide_table'  => 'XTS\Options\Controls\Size_Guide_Table',
		'select_with_table' => 'XTS\Options\Controls\Select_With_Table',
	);

	/**
	 * Create an object from args.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Basic arguments for the object.
	 */
	public function __construct( $args ) {
		$this->_id         = $args['id'];
		$this->_title      = $args['title'];
		$this->_post_types = $args['post_types'];
		$this->_taxonomies = $args['taxonomies'];
		$this->_object     = $args['object'];
		$this->_args       = $args;

		add_action( 'wp_enqueue_scripts', array( $this, 'fields_css_output' ), 20000 );
	}

	/**
	 * Get the metabox ID.
	 *
	 * @since 1.0.0
	 *
	 * @return int Metabox id field.
	 */
	public function get_id() {
		return $this->_id;
	}

	/**
	 * Getter for the metabox title.
	 *
	 * @since 1.0.0
	 *
	 * @return string The metabox title.
	 */
	public function get_title() {
		return $this->_title;
	}

	/**
	 * Getter for the metaboxes taxonomies.
	 *
	 * @since 1.0.0
	 *
	 * @return array Taxonomies array for this metabox.
	 */
	public function get_taxonomies() {
		return $this->_taxonomies;
	}

	/**
	 * Getter for the metabox object.
	 *
	 * @since 1.0.0
	 *
	 * @return string The metabox object.
	 */
	public function get_object() {
		return $this->_object;
	}

	/**
	 * Getter for the metaboxes post types array.
	 *
	 * @since 1.0.0
	 *
	 * @return array Post types array for this metabox.
	 */
	public function get_post_types() {
		return $this->_post_types;
	}

	/**
	 * Adds the Field object to this metabox.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Field arguments.
	 */
	public function add_field( $args ) {

		$control_classname = $this->_controls_classes[ $args['type'] ];

		$control = new $control_classname( $args, false, 'metabox', $this->get_object() );

		$this->_fields[] = $control;

		// Override theme setting option based on the meta value for this post and field.
		if ( isset( $args['option_override'] ) ) {
			Options::register_meta_override( $args['option_override'] );
		}
	}

	/**
	 * Static method to add a section to the array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $section Arguments array for new section.
	 */
	public function add_section( $section ) {
		$this->_sections[ $section['id'] ] = $section;
	}
	/**
	 * Static method to get all fields objects.
	 *
	 * @since 1.0.0
	 *
	 * @return array Field objects array.
	 */
	public function get_fields() {
		$fields = $this->_fields;

		usort(
			$fields,
			function ( $item1, $item2 ) {

				if ( ! isset( $item1->args['priority'] ) ) {
					return 1;
				}

				if ( ! isset( $item2->args['priority'] ) ) {
					return -1;
				}

				return $item1->args['priority'] - $item2->args['priority'];
			}
		);

		return $fields;

	}

	/**
	 * Output fields CSS code based on its controls and values.
	 *
	 * @since 1.0.0
	 */
	public function fields_css_output() {
		$output = '';
		$post   = get_post();

		if ( is_singular( 'xts-template' ) ) {
			$post = xts_get_preview_product();
		}

		foreach ( $this->get_fields() as $key => $field ) {
			$field->set_post( $post );
			$output .= $field->css_output();
		}

		wp_add_inline_style( 'xts-style', $output );
	}

	/**
	 * Static method to get all sections.
	 *
	 * @since 1.0.0
	 *
	 * @return array Section array.
	 */
	public function get_sections() {
		$sections = $this->_sections;

		usort(
			$sections,
			function ( $item1, $item2 ) {

				if ( ! isset( $item1['priority'] ) ) {
					return 1;
				}

				if ( ! isset( $item2['priority'] ) ) {
					return -1;
				}

				return $item1['priority'] - $item2['priority'];
			}
		);

		$sections_assoc = array();

		foreach ( $sections as $key => $section ) {
			$sections_assoc[ $section['id'] ] = $section;
		}

		return $sections_assoc;
	}

	/**
	 * Load all field objects and add them to the sections set.
	 *
	 * @since 1.0.0
	 */
	private function load_fields() {
		foreach ( $this->get_fields() as $key => $field ) {
			$this->_sections[ $field->args['section'] ]['fields'][] = $field;
		}
	}

	/**
	 * Generate a unique nonce for each registered meta_box
	 *
	 * @since  1.0.0
	 * @return string unique nonce string.
	 */
	public function nonce() {
		return sanitize_html_class( 'xts-metabox-nonce_' . basename( __FILE__ ) );
	}

	/**
	 * Render this metabox and all its fields.
	 *
	 * @since 1.0.0
	 *
	 * @param  object $object Post or Term object to render with its meta values.
	 */
	public function render( $object ) {
		$this->load_fields();

		?>
		<?php if ( $this->get_sections() ) : ?>
			<div class="xts-options xts-metaboxes">
				<?php wp_nonce_field( $this->nonce(), $this->nonce(), false, true ); ?>
				<div class="xts-fields-tabs">
					<?php if ( count( $this->get_sections() ) > 1 ) : ?>
						<div class="xts-sections-nav">
							<ul>
								<?php $this->display_sections_tree(); ?>
							</ul>
						</div>
					<?php endif; ?>

					<div class="xts-sections">
						<?php $this->display_sections( $object ); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Display sections navigation tree.
	 *
	 * @since 1.0.0
	 */
	private function display_sections_tree() {
		foreach ( $this->get_sections() as $key => $section ) {
			if ( isset( $section['parent'] ) ) {
				continue;
			}
			$subsections = array_filter(
				$this->get_sections(),
				function( $el ) use ( $section ) {
					return isset( $el['parent'] ) && $el['parent'] === $section['id'];
				}
			);

			$classes = $key === $this->get_last_tab() ? 'xts-active-nav' : '';
			?>
				<li class="<?php echo esc_attr( $classes ); ?>">
					<a href="" data-id="<?php echo esc_attr( $key ); ?>"  data-id="<?php echo esc_attr( $key ); ?>">
						<?php if ( isset( $section['icon'] ) && $section['icon'] ) : ?>
							<span class="xts-section-icon <?php echo esc_attr( $section['icon'] ); ?>"></span>
						<?php endif; ?>

						<?php echo esc_html( $section['name'] ); ?>
					</a>

					<?php if ( is_array( $subsections ) && count( $subsections ) > 0 ) : ?>
						<ul>
							<?php foreach ( $subsections as $subsection_key => $subsection ) : ?>
								<li class="xts-subsection-nav">
									<a href="" data-id="<?php echo esc_attr( $subsection_key ); ?>">
										<?php echo esc_html( $subsection['name'] ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>
			<?php
		}
	}

	/**
	 * Get last visited tab by visitor.
	 *
	 * @since 1.0.0
	 */
	private function get_last_tab() {
		reset( $this->_sections );

		return key( $this->_sections );
	}

	/**
	 * Loop through all the sections and render all the fields.
	 *
	 * @since 1.0.0
	 *
	 * @param object $object Object.
	 */
	private function display_sections( $object ) {
		foreach ( $this->_sections as $key => $section ) {
			$classes = $this->get_last_tab() !== $key ? 'xts-hidden' : 'xts-active-section';
			?>
				<div class="xts-fields-section <?php echo esc_attr( $classes ); ?>" data-id="<?php echo esc_attr( $key ); ?>">
					<?php if ( count( $this->get_sections() ) > 1 ) : ?>
						<div class="xts-section-title">
							<?php if ( isset( $section['icon'] ) && $section['icon'] ) : ?>
								<span class="xts-section-icon <?php echo esc_attr( $section['icon'] ); ?>"></span>
							<?php endif; ?>

							<h3><?php echo esc_html( $section['name'] ); ?></h3>
						</div>
					<?php endif; ?>

					<div class="xts-section-content xts-row">
						<?php
						$previous_group = false;
						foreach ( $section['fields'] as $field_key => $field ) {
							if ( $previous_group && ( ! isset( $field->args['group'] ) || $previous_group !== $field->args['group'] ) ) {
								echo '</div><!-- close group ' . esc_html( $previous_group ) . '-->';
								$previous_group = false;
							}
							if ( isset( $field->args['group'] ) && $previous_group !== $field->args['group'] ) {
								$previous_group = $field->args['group'];
								echo '<div class="xts-group-title"><span>' . esc_html( $previous_group ) . '</span></div>';
								echo '<div class="xts-fields-group xts-row">';
							}
							$field->render( $object );
						}
						if ( $previous_group ) {
							echo '</div><!-- close group ' . esc_html( $previous_group ) . '-->';
						}
						?>
					</div>
				</div>
			<?php
		}

	}

	/**
	 * Save all fields to the metadata database table for posts.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post id.
	 */
	public function save_posts_fields( $post_id ) {
		if ( ! isset( $_POST[ $this->nonce() ] ) || ! wp_verify_nonce( $_POST[ $this->nonce() ], $this->nonce() ) ) { // phpcs:ignore
			return;
		}

		foreach ( $this->_fields as $key => $field ) {
			if ( ( 'checkbox' === $field->args['type'] || 'select' === $field->args['type'] ) && ! isset( $_POST[ $field->get_input_name() ] ) ) { // phpcs:ignore
				delete_metadata(
					'post',
					$post_id,
					$field->get_input_name()
				);

				continue;
			}

			if ( ! array_key_exists( $field->get_input_name(), $_POST ) ) { // phpcs:ignore
				continue;
			}

			$value = $field->sanitize( $_POST[ $field->get_input_name() ] ); // phpcs:ignore

			update_metadata(
				'post',
				$post_id,
				$field->get_input_name(),
				$value
			);
		}
	}

	/**
	 * Save all fields to the metadata database table for terms.
	 *
	 * @since 1.0.0
	 *
	 * @param  int $term_id Term id.
	 */
	public function save_terms_fields( $term_id ) {
		foreach ( $this->_fields as $key => $field ) {
			if ( ( 'checkbox' === $field->args['type'] || 'select' === $field->args['type'] ) && ! isset( $_POST[ $field->get_input_name() ] ) ) { // phpcs:ignore
				delete_metadata(
					'term',
					$term_id,
					$field->get_input_name()
				);

				continue;
			}

			if ( ! array_key_exists( $field->get_input_name(), $_POST ) ) { // phpcs:ignore
				continue;
			}

			$value = $field->sanitize( $_POST[ $field->get_input_name() ] ); // phpcs:ignore

			update_metadata(
				'term',
				$term_id,
				$field->get_input_name(),
				$value
			);
		}
	}

	/**
	 * Save all fields to the metadata database table for terms.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $comment_id Comment id.
	 */
	public function save_comments_fields( $comment_id ) {
		foreach ( $this->_fields as $key => $field ) {
			if ( ( 'checkbox' === $field->args['type'] || 'select' === $field->args['type'] ) && ! isset( $_POST[ $field->get_input_name() ] ) ) { // phpcs:ignore
				delete_comment_meta(
					$comment_id,
					$field->get_input_name()
				);

				continue;
			}

			if ( ! array_key_exists( $field->get_input_name(), $_POST ) ) { // phpcs:ignore
				continue;
			}

			$value = $field->sanitize( $_POST[ $field->get_input_name() ] ); // phpcs:ignore

			update_comment_meta(
				$comment_id,
				$field->get_input_name(),
				$value
			);
		}
	}
}
