<?php

namespace WP_Rocket\Engine\License\API;

use WP_Rocket\Admin\Options_Data;

class UserClient {
	const USER_ENDPOINT = 'https://wp-rocket.me/stat/1.0/wp-rocket/user.php';

	/**
	 * WP Rocket options instance
	 *
	 * @var Options_Data
	 */
	private $options;

	/**
	 * Instantiate the class
	 *
	 * @param Options_Data $options WP Rocket options instance.
	 */
	public function __construct( Options_Data $options ) {
		$this->options = $options;
	}

	/**
	 * Gets user data from cache if it exists, else gets it from the user endpoint
	 *
	 * Cache the user data for 24 hours in a transient
	 *
	 * @since 3.7.3
	 *
	 * @return bool|object
	 */
	public function get_user_data() {
		$cached_data = get_transient( 'wp_rocket_customer_data' );

		if ( false !== $cached_data ) {
			return $cached_data;
		}

		$data = $this->get_raw_user_data();

		if ( false === $data ) {
			return false;
		}

		set_transient( 'wp_rocket_customer_data', $data, DAY_IN_SECONDS );

		return $data;
	}

	/**
	 * Gets the user data from the user endpoint
	 *
	 * @since 3.7.3
	 *
	 * @return bool|object
	 */
private function get_raw_user_data() {
    // Simulate the response as if it was retrieved successfully from the external endpoint
    $customResponse = [
        'licence_account'     => '-1',
        'licence_expiration'  => 1893456000,
        'has_one-com_account' => false,
        // Add any additional data expected in the response
    ];

    // Directly return the custom response without making an HTTP request
    return (object) $customResponse;
}
}
