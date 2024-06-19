<?php
/**
 * The client class for patch.
 *
 * @package XTS
 */

namespace XTS\Modules\Patcher;

use XTS\Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * The client class for patch.
 */
class Client extends Singleton {
	/**
	 * The uri of the patches remote server.
	 *
	 * @var string
	 */
	public $remote_patches_uri = 'https://xtemos.com/wp-json/xts/v1/patches_maps/';

	/**
	 * Version site.
	 *
	 * @var string
	 */
	public $theme_version;

	/**
	 * Process notices.
	 *
	 * @var array
	 */
	public $notices = array();

	/**
	 * Register hooks and load base data.
	 */
	public function init() {
		$this->theme_version = XTS_FRAMEWORK_VERSION;
	}

	/**
	 * Get count patches map.
	 *
	 * @return string
	 */
	public function get_count_pacthes_map() {
		global $pagenow;

		if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && ( strpos( $_GET['page'], 'xts_' ) || 'xtemos_options' === $_GET['page'] ) ) { //phpcs:ignore
			$patches_maps = $this->get_patches_maps();
		} else {
			$patches_maps = get_transient( 'xts_patches_map' );
		}

		if ( ! $patches_maps || ! is_array( $patches_maps ) ) {
			return '';
		}

		$patches_installed = get_option( 'xts_successfully_installed_patches', array() );

		if ( isset( $patches_installed[ $this->theme_version ] ) ) {
			$patches_maps = array_diff_key( $patches_maps, $patches_installed[ $this->theme_version ] );
		}

		$count = count( $patches_maps );

		if ( 0 === $count ) {
			return '';
		}

		ob_start();
		?>
		<span class="xts-patcher-counter update-plugins count-<?php echo esc_attr( $count ); ?>">
			<span class="patcher-count">
				<?php echo esc_html( $count ); ?>
			</span>
		</span>
		<?php
		return ob_get_clean();
	}

	/**
	 * Interface in admin panel.
	 */
	public function interface() {
		wp_enqueue_script( 'xts-patcher-scripts', XTS_ASSETS_URL . '/js/patcher.js', array(), XTS_VERSION, true );

		$patches               = $this->get_patches_maps_from_server();
		$patch_installed       = get_option( 'xts_successfully_installed_patches' );
		$theme_patches         = array_keys( $patches );
		$installed_patches     = isset( $patch_installed[ $this->theme_version ] ) ? array_keys( $patch_installed[ $this->theme_version ] ) : array();
		$all_patches_installed = empty( array_diff( $theme_patches, $installed_patches ) );

		?>
		<div class="xts-patcher-content">
			<div class="xts-dashboard-box-header">
				<div class="xts-dashboard-box-header-inner">
					<h3>
						<?php esc_html_e( 'Patcher', 'xts-theme' ); ?>
					</h3>
				</div>
				
				<?php if ( $patches && $this->check_filesystem_api() ) : ?>
					<div class="xts-patch-button-wrapper<?php echo $all_patches_installed ? ' xts-applied' : ''; ?>">
						<a href="#" class="xts-btn xts-btn-primary xts-patch-apply-all">
							<?php esc_html_e( 'Apply all', 'xts-theme' ); ?>
						</a>
						<span class="xts-patch-label-applied">
							<?php esc_html_e( 'All applied', 'xts-theme' ); ?>
						</span>
					</div>
				<?php endif; ?>
			</div>
			
			<div class="xts-notices-wrapper xts-patches-notice"><?php $this->print_notices(); // Must be in one line. ?></div>

			<?php if ( $patches ) : ?>
				<div class="xts-patches-wrapper">

					<div class="xts-patch-item xts-patch-title-wrapper">
						<div class="xts-patch-id">
							<?php esc_html_e( 'Patch ID', 'xts-theme' ); ?>
						</div>
						<div class="xts-patch-description">
							<?php esc_html_e( 'Description', 'xts-theme' ); ?>
						</div>
						<div class="xts-patch-date">
							<?php esc_html_e( 'Date', 'xts-theme' ); ?>
						</div>
						<div class="xts-patch-button-wrapper"></div>
					</div>

					<?php foreach ( $patches as $patch_id => $patcher ) : ?>
						<?php $classes = isset( $patch_installed[ $this->theme_version ][ $patch_id ] ) ? ' xts-applied' : ''; ?>
						<div class="xts-patch-item<?php echo esc_attr( $classes ); ?>">
							<div class="xts-patch-id">
								<?php echo esc_html( $patch_id ); ?>
							</div>
							<div class="xts-patch-description">
								<?php echo apply_filters( 'the_content', $patcher['description'] ); //phpcs:ignore ?>
							</div>
							<div class="xts-patch-date">
								<?php echo esc_html( $patcher['date'] ); ?>
							</div>
							<div class="xts-patch-button-wrapper">
								<?php if ( ! $this->check_filesystem_api() ) : ?>
									<a href="<?php echo esc_url( $patcher['patch_link'] ); ?>" class="xts-btn xts-btn-primary">
										<?php esc_html_e( 'Download', 'xts-theme' ); ?>
									</a>
								<?php else : ?>
									<a href="#" class="xts-btn xts-btn-primary xts-patch-apply" data-patches-map='<?php echo wp_json_encode( $patcher['files'] ); ?>' data-id="<?php echo esc_html( $patch_id ); ?>">
										<?php esc_html_e( 'Apply', 'xts-theme' ); ?>
									</a>
									<span class="xts-patch-label-applied">
										<?php esc_html_e( 'Applied', 'xts-theme' ); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Get patches maps.
	 *
	 * @return array
	 */
	public function get_patches_maps() {
		$patches_maps = get_transient( 'xts_patches_map' );

		if ( ! $patches_maps ) {
			$patches_maps = $this->get_patches_maps_from_server();
		}

		if ( ! is_array( $patches_maps ) ) {
			return array();
		}

		return $patches_maps;
	}

	/**
	 * Queries the patches server for a list of patches.
	 *
	 * @return array
	 */
	public function get_patches_maps_from_server() {
		$url = add_query_arg(
			array(
				'theme_slug' => XTS_BUILD_TYPE . ',' . XTS_THEME_SLUG,
				'version'    => $this->theme_version,
			),
			$this->remote_patches_uri
		);

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			$this->notices['error'] = $response->get_error_message();
			$this->update_set_transient( 'error' );
			return array();
		}

		if ( ! isset( $response['body'] ) ) {
			$this->notices['error'] = $response['response']['code'] . ': ' . $response['response']['message'];
			$this->update_set_transient( 'error' );
			return array();
		}

		$response_body = json_decode( $response['body'], true );

		if ( isset( $response_body['code'] ) && isset( $response_body['message'] ) ) {
			$this->notices['error'] = $response_body['message'];
			$this->update_set_transient( 'error' );
			return array();
		}

		if ( isset( $response_body['type'] ) && isset( $response_body['message'] ) ) {
			$this->notices[ $response_body['type'] ] = $response_body['message'];
			$this->update_set_transient( $response_body['type'] );
			return array();
		}

		if ( ! $response_body ) {
			$this->update_set_transient( 'actual' );
			return array();
		}

		$this->update_set_transient( $response_body );

		return $response_body;
	}

	/**
	 * Sets/updates the value of a transient.
	 *
	 * @param string|array $data Value.
	 *
	 * @return void
	 */
	public function update_set_transient( $data ) {
		set_transient( 'xts_patches_map', $data, DAY_IN_SECONDS );
	}

	/**
	 * Print notices.
	 */
	public function print_notices() {
		if ( ! $this->check_filesystem_api() ) {
			$this->notices['warning'] = esc_html__( 'Direct access to theme file is not allowed on your server. You need to download and replace the files manually.', 'xts-theme' );
		}

		if ( ! $this->notices ) {
			return;
		}

		foreach ( $this->notices as $type => $notice ) {
			$this->print_notice( $notice, $type );
		}
	}

	/**
	 * Print notice.
	 *
	 * @param string $message Message.
	 * @param string $type    Type.
	 */
	private function print_notice( $message, $type = 'warning' ) {
		?>
		<div class="xts-notice xts-patcher-notice xts-<?php echo esc_attr( $type ); ?>">
			<?php echo wp_kses( $message, 'xts_notice' ); ?>
		</div>
		<?php
	}

	/**
	 * Check filesystem API.
	 *
	 * @return bool
	 */
	public function check_filesystem_api() {
		global $wp_filesystem;

		if ( function_exists( 'WP_Filesystem' ) ) {
			WP_Filesystem();
		}

		return 'direct' === $wp_filesystem->method;
	}
}
