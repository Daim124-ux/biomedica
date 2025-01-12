<?php
/**
 * CSS to file.
 *
 * @package xts
 */

namespace XTS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * CSS to file class.
 *
 * @since 1.0.0
 */
class Styles_Storage {
	/**
	 * Name.
	 *
	 * @var string
	 */
	public $data_name;
	/**
	 * Storage type.
	 *
	 * @var string
	 */
	public $storage;
	/**
	 * Post id.
	 *
	 * @var string
	 */
	public $id;
	/**
	 * Data.
	 *
	 * @var array
	 */
	public $data = array();
	/**
	 * Styles.
	 *
	 * @var array
	 */
	public $css = array();
	/**
	 * Is need check credentials.
	 *
	 * @var boolean
	 */
	public $check_credentials;
	/**
	 * Is styles file exists.
	 *
	 * @var boolean
	 */
	public $is_file_exists = false;
	/**
	 * Is styles exists.
	 *
	 * @var boolean
	 */
	public $is_css_exists = false;
	/**
	 * Options set prefix.
	 *
	 * @var array
	 */
	public $opt_name = XTS_THEME_SLUG;

	/**
	 * Set up all properties.
	 *
	 * @param string  $data_name         Name.
	 * @param string  $storage           Storage type.
	 * @param string  $id                Post id.
	 * @param boolean $check_credentials Is need check credentials.
	 */
	public function __construct( $data_name, $storage = 'option', $id = '', $check_credentials = true ) {
		$this->set_data_name( $data_name );
		$this->storage           = $storage;
		$this->id                = $id;
		$this->check_credentials = $check_credentials;

		$this->set_data( 'xts-' . $this->data_name . '-' . $this->opt_name . '-file-data' );
		$this->set_css_data( 'xts-' . $this->data_name . '-' . $this->opt_name . '-css-data' );

		$this->check_css_status();
	}

	/**
	 * Set data name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data_name Data name.
	 */
	public function set_data_name( $data_name ) {
		$this->data_name = $data_name;
	}

	/**
	 * Set data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data_name Data name.
	 */
	public function set_data( $data_name ) {
		$this->data = $this->get_data( $data_name );
	}

	/**
	 * Set css data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data_name Data name.
	 */
	public function set_css_data( $data_name ) {
		$this->css = $this->get_data( $data_name );
	}

	/**
	 * Check css status.
	 *
	 * @since 1.0.0
	 */
	public function check_css_status() {
		$data_status = $this->get_data( 'xts-' . $this->data_name . '-status' );

		if ( 'valid' === $data_status ) {
			if ( isset( $this->data['path'] ) && file_exists( $this->get_file_path( $this->data['path'] ) ) && apply_filters( 'xts_styles_storage_file', true ) && isset( $this->data['theme_version'] ) && version_compare( XTS_VERSION, $this->data['theme_version'], '==' ) && ( isset( $this->data['site_url'] ) && md5( get_site_url() ) === $this->data['site_url'] ) ) {
				$this->is_file_exists = true;
			}

			if ( isset( $this->css['css'] ) && $this->css['css'] && apply_filters( 'xts_styles_storage_db_css', true ) && isset( $this->css['theme_version'] ) && version_compare( XTS_VERSION, $this->css['theme_version'], '==' ) && ( isset( $this->css['site_url'] ) && md5( get_site_url() ) === $this->css['site_url'] ) ) {
				$this->is_css_exists = true;
			}
		}
	}

	/**
	 * Get file path.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path File path.
	 *
	 * @return string
	 */
	public function get_file_path( $path ) {
		$uploads = wp_upload_dir();

		return set_url_scheme( $uploads['basedir'] . $path );
	}

	/**
	 * Get file url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url File url.
	 *
	 * @return string
	 */
	public function get_file_url( $url ) {
		$uploads = wp_upload_dir();

		return set_url_scheme( $uploads['baseurl'] . $url );
	}

	/**
	 * Is css exists.
	 *
	 * @since 1.0.0
	 */
	public function is_css_exists() {
		return $this->is_css_exists;
	}

	/**
	 * Print styles.
	 *
	 * @since 1.0.0
	 */
	public function print_styles() {
		if ( $this->is_file_exists ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'file_css' ), 10200 );
		} else {
			add_action( 'wp_head', array( $this, 'inline_css' ), 10200 );
		}
	}

	/**
	 * FIle css.
	 *
	 * @since 1.0.0
	 */
	public function file_css() {
		if ( isset( $this->data['path'] ) && $this->data['path'] ) {
			wp_enqueue_style( 'xts-style-' . $this->data_name, $this->get_file_url( $this->data['path'] ), array(), XTS_VERSION );
		}
	}

	/**
	 * Inline css.
	 *
	 * @since 1.0.0
	 */
	public function inline_css() {
		?>
		<?php if ( isset( $this->css['css'] ) && $this->css['css'] ) : ?>
			<style data-type="xts-style-<?php echo esc_attr( $this->data_name ); ?>">
				<?php echo apply_filters( 'xts_custom_css_output', $this->css['css'] ); ?>
			</style>
		<?php endif; ?>
		<?php
	}

	/**
	 * Reset data.
	 *
	 * @since 1.0.0
	 */
	public function reset_data() {
		$this->update_data( 'xts-' . $this->data_name . '-status', 'invalid' );
		$this->delete_data( 'xts-' . $this->data_name . '-credentials' );
	}

	/**
	 * Write data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $css Styles.
	 * @param bool   $is_frontend Is frontend.
	 */
	public function write( $css, $is_frontend = false ) {
		if ( ! $css ) {
			return;
		}

		if ( ! $this->css ) {
			$this->css = array();
		}

		$this->css['css'] = $css;

		if ( ! $is_frontend ) {
			$this->write_file( $css );
		}

		$this->update_data(
			'xts-' . $this->data_name . '-' . $this->opt_name . '-css-data',
			array(
				'css'           => $css,
				'theme_version' => XTS_VERSION,
				'site_url'      => md5( get_site_url() ),
			)
		);

		$this->update_data( 'xts-' . $this->data_name . '-status', 'valid' );
	}

	/**
	 * Delete file.
	 */
	public function delete_file() {
		if ( function_exists( 'WP_Filesystem' ) ) {
			WP_Filesystem();
		}

		global $wp_filesystem;

		if ( $this->data && $this->data['path'] ) {
			$wp_filesystem->delete( $this->get_file_path( $this->data['path'] ) );
			$this->delete_data( 'xts-' . $this->data_name . '-' . $this->opt_name . '-file-data' );
		}
	}

	/**
	 * Write file.
	 *
	 * @param string $css Styles.
	 */
	private function write_file( $css ) {
		if ( function_exists( 'WP_Filesystem' ) ) {
			WP_Filesystem();
		}

		global $wp_filesystem;

		if ( ( $this->check_credentials && ( function_exists( 'request_filesystem_credentials' ) && ! $this->check_credentials() ) ) || ! $wp_filesystem ) {
			return;
		}

		$this->delete_file();

		$result = $wp_filesystem->put_contents( $this->get_file_path( $this->get_file_info( $this->data_name ) ), $css );

		if ( $result ) {
			$this->update_data(
				'xts-' . $this->data_name . '-' . $this->opt_name . '-file-data',
				array(
					'path'          => $this->get_file_info( $this->data_name ),
					'theme_version' => XTS_VERSION,
					'site_url'      => md5( get_site_url() ),
				)
			);
		}
	}

	/**
	 * Get data.
	 *
	 * @param string $name Option name.
	 *
	 * @return mixed|string|void
	 */
	private function get_data( $name ) {
		$results = '';

		if ( 'option' === $this->storage ) {
			$results = get_option( $name );
		} elseif ( 'post_meta' === $this->storage && $this->id ) {
			$results = get_post_meta( $this->id, $name );
		}

		return $results;
	}

	/**
	 * Update data.
	 *
	 * @param string $name Option name.
	 * @param mixed  $data Data.
	 *
	 * @return mixed|string|void
	 */
	private function update_data( $name, $data ) {
		if ( 'option' === $this->storage ) {
			update_option( $name, $data );
		} elseif ( 'post_meta' === $this->storage && $this->id ) {
			update_post_meta( $this->id, $name, $data );
		}
	}

	/**
	 * Delete data.
	 *
	 * @param string $name Option name.
	 *
	 * @return mixed|string|void
	 */
	private function delete_data( $name ) {
		if ( 'option' === $this->storage ) {
			delete_option( $name );
		} elseif ( 'post_meta' === $this->storage && $this->id ) {
			delete_post_meta( $this->id, $name );
		}
	}

	/**
	 * Get file info.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data_name File name.
	 *
	 * @return string
	 */
	public function get_file_info( $data_name ) {
		$uploads = wp_upload_dir();

		return set_url_scheme( $uploads['subdir'] . '/xts-' . $data_name . '-' . time() . '.css' );
	}

	/**
	 * Check credentials.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check_credentials() {
		$data_status        = $this->get_data( 'xts-' . $this->data_name . '-status' );
		$credentials_status = $this->get_data( 'xts-' . $this->data_name . '-credentials' );

		if ( ( 'valid' === $data_status || 'requested' === $credentials_status ) && ! $_POST ) { // phpcs:ignore
			return false;
		}

		$this->update_data( 'xts-' . $this->data_name . '-credentials', 'requested' );

		echo '<div class="xts-request-credentials">';
		$credentials = request_filesystem_credentials( false, '', false, false, array_keys( $_POST ) ); // phpcs:ignore
		echo '</div>';

		if ( ! $credentials || ! WP_Filesystem( $credentials ) ) {
			return false;
		}

		return true;
	}
}
