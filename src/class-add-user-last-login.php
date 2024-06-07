<?php
/**
 * Add last users login
 *
 * @package multisite-enhancements
 */

namespace Multisite_Enhancements;

/**
 * Class Add_User_Last_Login
 */
class Add_User_Last_Login {
	/**
	 * Add it to filters and actions
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'manage_users-network_columns', array( $this, 'manage_users_columns' ) );
		add_filter( 'manage_users-network_sortable_columns', array( $this, 'manage_users_sortable_columns' ) );
		add_action( 'pre_get_users', array( $this, 'pre_get_users' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'manage_users_custom_column' ), 10, 3 );
		add_action( 'set_auth_cookie', array( $this, 'record_last_logged_in' ), 10, 4 );
	}

	/**
	 * Add Label to colum
	 *
	 * @param array $columns Columns names.
	 *
	 * @return array
	 */
	public function manage_users_columns( array $columns ) {
		$columns['last-logged-in'] = 'Last Logged In';

		return $columns;
	}

	/**
	 * Define colum as sortable
	 *
	 * @param array $columns Columns sortable.
	 *
	 * @return array
	 */
	public function manage_users_sortable_columns( array $columns ) {
		$columns['last-logged-in'] = 'last-logged-in';

		return $columns;
	}

	/**
	 * Filter users for sorting
	 *
	 * @param \WP_User_Query $query User query.
	 *
	 * @return void
	 */
	public function pre_get_users( \WP_User_Query $query ) {
		if ( ! is_network_admin() || 'last-logged-in' !== $query->get( 'orderby' ) || ! current_user_can( 'list_users' ) ) {
			return;
		}

		// Must use a meta query to account for users who have never logged in.
		$meta_query = array(
			'relation'             => 'OR',
			'last_logged_in_never' => array(
				'key'     => 'last_logged_in',
				'compare' => 'NOT EXISTS',
			),
			'last_logged_in'       => array(
				'key'  => 'last_logged_in',
				'type' => 'DATE',
			),
		);

		$query->set( 'orderby', 'last_logged_in' );
		$query->set( 'meta_query', $meta_query );
	}

	/**
	 * Display colum content
	 *
	 * @param string $value Value form before.
	 * @param string $column current column name.
	 * @param int    $user_id user ID to display information for.
	 *
	 * @return string
	 */
	public function manage_users_custom_column( $value, $column, $user_id ) {
		if ( 'last-logged-in' !== $column || ! current_user_can( 'list_users' ) ) {
			return $value;
		}

		$last_login = \DateTime::createFromFormat( 'Y-m-d H:i:s', get_user_meta( $user_id, 'last_logged_in', true ), new \DateTimeZone( 'UTC' ) );

		if ( ! $last_login ) {
			return '<em title="Has never logged in">Never since registering</em>';
		}

		$last_login->setTimezone( wp_timezone() );
		return '<span title="' . $last_login->format( 'c' ) . '">' . $last_login->format( 'd. F Y' ) . '</span>';
	}

	/**
	 * Record the last date a user logged in.
	 *
	 * Note: This might be before they agree to the new TOS, which is recorded separately.
	 *
	 * @param string $auth_cookie Authentication cookie value.
	 * @param int    $expire The time the login grace period expires as a UNIX timestamp.
	 *                                  Default is 12 hours past the cookie's expiration time.
	 * @param int    $expiration The time when the authentication cookie expires as a UNIX timestamp.
	 *                                  Default is 14 days from now.
	 * @param int    $user_id User ID.
	 *
	 * @throws \Exception For hooked function.
	 */
	public function record_last_logged_in( $auth_cookie, $expire, $expiration, $user_id ) {
		$login_at = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
		update_user_meta( $user_id, 'last_logged_in', $login_at->format( 'Y-m-d H:i:s' ) );
	}
}
