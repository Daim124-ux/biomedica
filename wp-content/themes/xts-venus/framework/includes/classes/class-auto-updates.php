<?php
/**
 * Auto theme update class.
 *
 * @package xts
 */

namespace XTS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Auto theme update class.
 *
 * @since 1.0.0
 */
class Auto_Updates extends Singleton {
	/**
	 * Api.
	 *
	 * @var object
	 */
	private $api = null;

	/**
	 * Themes info.
	 *
	 * @var array
	 */
	private $info = array();

	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->api = new Api_Client();

		add_filter( 'site_transient_update_themes', array( $this, 'set_transient' ), 10 );

		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'update_plugins_version' ), 30 );
		add_filter( 'xts_setup_wizard', array( $this, 'update_plugins_version' ), 30 );
	}

	/**
	 * Set transient.
	 *
	 * @param mixed $transient Transient.
	 *
	 * @return mixed
	 */
	public function set_transient( $transient ) {
		$this->check_for_update();
		$installed_theme_list = wp_get_themes();
		$theme_list           = xts_get_config( 'theme-list' );

		if ( $transient && property_exists( $transient, 'response' ) ) {
			foreach ( $installed_theme_list as $theme ) {
				$theme_name = strtolower( $theme->get( 'Name' ) );
				if ( isset( $this->info[ $theme_name ] ) && version_compare( $theme->get( 'Version' ), $this->info[ $theme_name ]['new_version'], '<' ) ) {
					$transient->response[ 'xts-' . $theme_name ] = $this->info[ $theme_name ];
				} elseif ( isset( $theme_list[ $theme_name ] ) ) {
					unset( $transient->response[ $theme_name ] );
				}
			}
		}

		return $transient;
	}

	/**
	 * Check for update.
	 */
	protected function check_for_update() {
		$force = false;

		if ( isset( $_GET['force-check'] ) && '1' === $_GET['force-check'] ) { // phpcs:ignore
			$force = true;
		}

		if ( empty( $this->info ) ) {
			$this->info = get_option( 'xts-auto-update-info', false );
		}

		$last_check = get_option( 'xts-auto-update-time' );

		if ( ! $last_check ) {
			update_option( 'xts-auto-update-time', time() );
		}

		if ( time() - $last_check > 172800 || $force || ! $last_check ) {
			$response = $this->get_api_info();
			update_option( 'xts-auto-update-time', time() );

			foreach ( $response as $theme ) {
				if ( ! isset( $theme['slug'] ) || ( isset( $theme['slug'] ) && ! $theme['slug'] ) || ! $theme['has_access'] ) {
					continue;
				}

				$this->info[ $theme['slug'] ] = array(
					'theme'       => $theme['name'],
					'new_version' => $theme['version'],
					'checked'     => time(),
					'url'         => $this->get_changelog_url( $theme['slug'] ),
					'package'     => $this->get_download_url( $theme['slug'] ),
				);
			}
		}

		update_option( 'xts-auto-update-info', $this->info );
	}

	/**
	 * Get API info.
	 *
	 * @return array|mixed
	 */
	public function get_api_info() {
		$response = $this->api->get( 'info' );

		if ( ! isset( $response['success'] ) ) {
			return array();
		}

		return $response;
	}

	/**
	 * Get download url.
	 *
	 * @param string $slug Theme slug.
	 *
	 * @return string
	 */
	public function get_download_url( $slug ) {
		return $this->api->get_url( 'download', array( 'theme' => $slug, 'build_type' => XTS_BUILD_TYPE ) );
	}

	/**
	 * Get changelog url.
	 *
	 * @param string $slug Theme slug.
	 *
	 * @return string
	 */
	public function get_changelog_url( $slug ) {
		return XTS_SPACE_URL . 'wordpress-themes/' . $slug . '?changelog';
	}

	/**
	 * Update plugins version with server.
	 *
	 * @param mixed $transient Transient.
	 *
	 * @return mixed
	 */
	public function update_plugins_version( $transient ) {
		$api        = XTEMOS_API_URL;
		$plugins    = array( 'revslider' );
		$force      = false;
		$last_check = get_option( 'xts-plugins-update-time' );

		if ( ( isset( $_GET['force-check'] ) && '1' === $_GET['force-check'] ) || ( isset( $_GET['tab'] ) && 'wizard' === $_GET['tab'] ) ) {
			$force = true;
		}

		if ( ! $last_check ) {
			update_option( 'xts-plugins-update-time', time() );
		}

		if ( time() - $last_check > 172800 || $force || ! $last_check ) {
			update_option( 'xts-plugins-update-time', time() );

			foreach ( $plugins as $plugin ) {
				$query         = wp_remote_get( $api . 'info/' . $plugin );
				$response_code = wp_remote_retrieve_response_code( $query );

				if ( '200' !== (string) $response_code ) {
					continue;
				}

				$response = json_decode( wp_remote_retrieve_body( $query ) );

				if ( ! property_exists( $response, 'version' ) ) {
					continue;
				}

				update_option( 'xts_' . $plugin . '_version', $response->version );
			}
		}

		return $transient;
	}
}
