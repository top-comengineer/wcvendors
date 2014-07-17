<?php
/**
 * MGates Plugin Update Checker
 *
 * Use the WordPress Update Manager to check for plugin updates at mgates.me.
 *
 * @version  1.0.0
 * @category Plugins
 * @since    1.0.0
 *
 * @author   Matt Gates
 * @package  WordPress
 */


class MGates_Plugin_Updater
{
	private static $instance;

	private $api_key = ''; // Unique value, used to determine the plugin to be updated.
	private $api_url = 'https://shop.mgates.me';

	private $plugin_token = 'geczy-plugin-updater';
	private $plugin_prefix = 'geczy_plugin_updater_';

	private static $keys = array();
	private static $plugins = array();

	private $plugin_path;
	private $plugin_url;
	private $plugin_base;
	private $file;


	/**
	 * __construct
	 *
	 * @param string $file
	 * @param string $key (optional)
	 *
	 * @return void
	 */
	function __construct( $file, $key = '' )
	{
		// Don't do anything without the api key.
		if ( empty( $key ) ) return false;

		$this->api_key     = $key;
		$this->plugin_url  = trailingslashit( WP_PLUGIN_URL ) . plugin_basename( dirname( $file ) );
		$this->plugin_slug = plugin_basename( $file );
		$this->plugin_path = dirname( $this->plugin_slug );
		$this->file        = $file;

		add_action( 'init', array( $this, 'init' ) );
	}


	/**
	 * Init
	 */
	function init()
	{
		// Running this here rather than the constructor since get_file_data is a bit expensive
		$info                 = get_file_data( $this->file, array( 'Title' => 'Plugin Name', 'Version' => 'Version' ), 'plugin' );
		$this->plugin_title   = $info[ 'Title' ];
		$this->plugin_version = $info[ 'Version' ];

		// Store the plugin to a static variable
		self::$plugins[ $this->api_key ] = array(
			'version' => $this->plugin_version,
			'slug'    => $this->plugin_slug,
			'url'     => $this->plugin_url,
			'path'    => $this->plugin_path,
			'title'   => $this->plugin_title,
		);

		// Check For Plugin Information
		add_filter( 'plugins_api', array( $this, 'plugin_information' ), 10, 3 );

		// Check For Updates
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_check' ) );
		add_filter( 'upgrader_post_install', array( $this, 'upgrader_post_install' ), 10, 3 );

		if ( !self::$instance ) {
			self::$instance = true;

			// Register Navigation Menu Link
			add_action( 'admin_menu', array( $this, 'register_nav_menu_link' ), 10 );
			add_filter( 'http_request_args', array( $this, 'http_request_sslverify' ), 10, 2 );

			if ( !$this->instance_exists() ) {
				// Setup Admin Notices
				add_action( 'admin_notices', array( $this, 'admin_notice' ) );
				add_action( 'admin_init', array( $this, 'hide_admin_notice' ) );
			}
		}
	}


	/**
	 * Callback fn for the http_request_args filter
	 *
	 *
	 * @param unknown $args
	 * @param unknown $url
	 *
	 * @return mixed
	 */
	public function http_request_sslverify( $args, $url )
	{
		if ( strpos( $url, $this->api_url ) !== false )
			$args[ 'sslverify' ] = false;

		return $args;
	}


	/**
	 * Upgrader/Updater
	 * Move & activate the plugin, echo the update message
	 *
	 * @since 1.0
	 *
	 * @param boolean $true       always true
	 * @param mixed   $hook_extra not used
	 * @param array   $result     the result of the move
	 *
	 * @return array $result the result of the move
	 */
	public function upgrader_post_install( $true, $hook_extra, $result )
	{
		global $wp_filesystem;

		// Move & Activate
		$proper_destination = trailingslashit( WP_PLUGIN_DIR ) . self::$plugins[ $this->api_key ][ 'path' ];
		$wp_filesystem->move( $result[ 'destination' ], $proper_destination );
		$result[ 'destination' ] = $proper_destination;
		$activate                = activate_plugin( trailingslashit( WP_PLUGIN_DIR ) . self::$plugins[ $this->api_key ][ 'slug' ] );

		// Output the update message
		$fail    = __( 'The plugin has been updated, but could not be reactivated. Please reactivate it manually.', 'geczy' );
		$success = __( 'Plugin reactivated successfully.', 'geczy' );
		echo is_wp_error( $activate ) ? $fail : $success;

		return $result;
	}


	/**
	 * activate function.
	 *
	 * @access public
	 *
	 * @param unknown $api_key
	 * @param unknown $license_key
	 *
	 * @return boolean $is_valid
	 */
	function activate( $api_key, $license_key )
	{
		// Don't continue if it's not entered!
		if ( empty( $api_key ) || empty( $license_key ) ) return false;
		if ( empty( self::$plugins[ $api_key ] ) ) return false;

		// POST data to send to your API
		$args = array(
			'request'     => 'activation',
			'api_key'     => $api_key,
			'version'     => self::$plugins[ $api_key ][ 'version' ],
			'license_key' => $license_key,
		);

		// Send request for detailed information
		$response = $this->prepare_request( $args );

		return !empty( $response->activated );
	} // End activate()


	/**
	 * deactivate function.
	 *
	 * @access public
	 *
	 * @param unknown $api_key
	 * @param unknown $license_key
	 *
	 * @return boolean $is_valid
	 */
	function deactivate( $api_key, $license_key )
	{
		// Don't continue if it's not entered!
		if ( empty( $api_key ) || empty( $license_key ) ) return false;
		if ( empty( self::$plugins[ $api_key ] ) ) return false;

		// POST data to send to your API
		$args = array(
			'request'     => 'deactivation',
			'api_key'     => $api_key,
			'license_key' => $license_key,
		);

		// Send request for detailed information
		$response = $this->prepare_request( $args );

		return !empty( $response->deactivated );
	} // End deactivate()


	/**
	 * load_user_data function.
	 *
	 * @access public
	 * @return void
	 */
	function load_user_data()
	{
		// Only check if necessary
		if ( empty( self::$keys ) ) {
			self::$keys = get_option( $this->plugin_prefix . 'license_keys' );
		}
	} // End load_user_data()


	/**
	 * register_nav_menu_link function.
	 *
	 * @access public
	 * @return void
	 * @uses   save_license_keys()
	 */
	function register_nav_menu_link()
	{
		// Don't register the menu if it's already there.
		if ( $this->instance_exists() ) {
			return;
		}

		if ( function_exists( 'add_submenu_page' ) ) {
			$this->admin_screen = add_submenu_page( 'index.php', __( 'MGates.me Updates', 'geczy' ), __( 'MGates.me Updates', 'geczy' ), 'switch_themes', $this->plugin_token, array( $this, 'admin_screen' ) );
		}

		// Load admin screen logic.
		if ( !empty( $_POST[ $this->plugin_prefix . 'nonce' ] ) && wp_verify_nonce( $_POST[ $this->plugin_prefix . 'nonce' ], $this->plugin_token . '-nonce' ) ) {
			if ( isset( $_POST[ $this->plugin_token . '-login' ] ) ) {
				$this->save_license_keys();
			}

			if ( isset( $_POST[ $this->plugin_token . '-update-now' ] ) ) {
				delete_option( '_site_transient_update_plugins' );
				wp_redirect( 'update-core.php' );
				exit;
			}
		}

	} // End register_nav_menu_link()


	/**
	 * admin_screen function.
	 *
	 * @access public
	 * @return void
	 */
	function admin_screen()
	{
		$this->load_user_data(); ?>
		<div class="wrap">

			<?php screen_icon( 'plugins' ); ?>
			<h2><?php _e( 'MGates.me Plugin Updater', 'geczy' ); ?></h2>

			<?php if ( !empty( $_SESSION[ $this->plugin_prefix ][ 'flash_data' ] ) ) {
				echo '<div class="updated">';
				foreach ( $_SESSION[ $this->plugin_prefix ][ 'flash_data' ] as $message ) {
					printf( '<p>%s</p>', $message );
				}
				echo '</div>';
				unset( $_SESSION[ $this->plugin_prefix ][ 'flash_data' ] );
			} ?>

			<p><?php _e( 'Enter your license key to automatically receive updates.', 'geczy' ); ?></p>

			<form name="<?php echo $this->plugin_token; ?>-login" id="<?php echo $this->plugin_token; ?>-login"
				  action="<?php echo admin_url( 'index.php?page=' . $this->plugin_token ); ?>" method="post">
				<?php wp_nonce_field( $this->plugin_token . '-nonce', $this->plugin_prefix . 'nonce' ); ?>
				<fieldset>
					<table class="form-table">
						<tbody>
						<?php $i = 0;
						foreach ( self::$plugins as $api_key => $info ) {
							$i++;
							$value = '';
							$valid = !empty( self::$keys[ $api_key ][ 'status' ] );
							$value = !empty( self::$keys[ $api_key ][ 'license_key' ] ) ? self::$keys[ $api_key ][ 'license_key' ] : '';
							if ( !empty( $_POST[ 'license_keys' ][ $api_key ] ) ) {
								$value = $_POST[ 'license_keys' ][ $api_key ];
							} ?>
							<tr>
								<th scope="row"><label
										for="license_key-<?php echo $i; ?>"><?php echo $info[ 'title' ] ?></label></th>
								<td><input type="text"
										   class="input-text input-license regular-text" <?php echo !$valid ? 'style="border: 1px solid #B94A48;"' : 'style="border: 1px solid #468847;"'; ?>
										   name="license_keys[<?php echo $api_key; ?>]"
										   id="license_key-<?php echo $i; ?>" value="<?php echo $value; ?>"/></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</fieldset>

				<fieldset>
					<p class="submit">
						<button type="submit" name="<?php echo $this->plugin_token; ?>-login"
								id="<?php echo $this->plugin_token; ?>-login"
								class="button-primary"><?php _e( 'Save', 'geczy' ); ?></button>
						<button type="submit" name="<?php echo $this->plugin_token; ?>-update-now"
								id="<?php echo $this->plugin_token; ?>-login"
								class="button"><?php _e( 'Check for updates <b>now</b>', 'geczy' ); ?></button>
					</p>
				</fieldset>
			</form>

		</div><!--/.wrap-->
	<?php
	} // End admin_screen()


	/**
	 * save_license_keys function.
	 *
	 * @access public
	 * @return void
	 */
	function save_license_keys()
	{
		$this->load_user_data();
		$messages = array();

		foreach ( $_POST[ 'license_keys' ] as $api_key => $license_key ) {
			$license_key = trim( $license_key );

			// Deactivate this key as it was removed
			if ( empty( $license_key ) && !empty( self::$keys[ $api_key ][ 'status' ] ) ) {
				if ( $deactivated = $this->deactivate( $api_key, self::$keys[ $api_key ][ 'license_key' ] ) ) {
					$messages[ ] = sprintf( __( '<b style="color: #468847;">Key deactivated.</b> License key for <i>%s</i> has been <b>deactivated</b>.', 'geczy' ), self::$plugins[ $api_key ][ 'title' ] );
				} else {
					$messages[ ] = sprintf( __( '<b style="color: #B94A48;">Error.</b> License key for <i>%s</i> could not be deactivated.', 'geczy' ), self::$plugins[ $api_key ][ 'title' ] );
				}
				continue;
			}

			// Only check keys that are not yet valid
			$is_valid = $this->activate( $api_key, $license_key );
			if ( $is_valid ) {
				$messages[ ] = sprintf( __( '<b style="color: #468847;">Key activated.</b> License key for <i>%s</i> has been <b>activated</b>.', 'geczy' ), self::$plugins[ $api_key ][ 'title' ] );
			} else {
				$messages[ ] = sprintf( __( '<b style="color: #B94A48;">Error.</b> License key for <i>%s</i> is invalid.', 'geczy' ), self::$plugins[ $api_key ][ 'title' ] );
			}

			$keys[ $api_key ] = array( 'license_key' => $license_key, 'status' => $is_valid );
		}

		if ( !empty( $messages ) )
			$_SESSION[ $this->plugin_prefix ][ 'flash_data' ] = $messages;

		self::$keys = $keys;
		update_option( $this->plugin_prefix . 'license_keys', $keys );

	} // End save_license_keys()


	/**
	 * admin_notice function.
	 *
	 * @access public
	 * @return void
	 */
	function admin_notice()
	{
		$this->load_user_data();
		$hide_notice = get_user_meta( get_current_user_id(), $this->plugin_prefix . 'hide-admin-notice', true );

		if ( !empty( self::$keys ) ) {
			foreach ( self::$keys as $value ) if ( $value[ 'status' ] ) {
				$hide_notice = true;
				break;
			}
		}

		$is_settings_page = ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == $this->plugin_token );

		// Admin notice for if no login details are set, to notify the user.
		if ( !$is_settings_page && !$hide_notice ) {
			$notice = '<div id="geczy-plugin-updater-notice" class="updated fade">' . "\n";
			$notice .= '<p class="alignleft"><strong>' . __( 'Enable MGates.me Plugin Updates.', 'geczy' ) . '</strong> ' . "\n";

			$notice .= sprintf( __( '<a href="%1$s">Add your license keys</a> to enable automatic plugin updates.', 'geczy' ), 'index.php?page=' . $this->plugin_token );
			$notice .= "\n" . '</p>' . "\n";

			$query                                               = $_GET;
			$query[ $this->plugin_token . '-hide-updatenotice' ] = 'true';
			$query                                               = http_build_query( $query );

			$notice .= '<p class="alignright submitbox"><a href="?' . $query . '" class="submitdelete">' . __( 'Hide This Message', 'geczy' ) . '</a></p>' . "\n";
			$notice .= '<br class="clear" />';
			$notice .= '</div>' . "\n";

			echo $notice;
		}
	} // End admin_notice()


	/**
	 * update_check function.
	 *
	 * @access public
	 *
	 * @param object $transient
	 *
	 * @return object $transient
	 */
	function update_check( $transient )
	{
		$this->load_user_data();

		// Check if the transient contains the 'checked' information
		// If no, just return its value without hacking it
		if ( empty( $transient->checked ) )
			return $transient;

		// No valid license key exists for this API key
		if ( empty ( self::$keys[ $this->api_key ][ 'status' ] ) )
			return $transient;

		// The transient contains the 'checked' information
		// Now append to it information form your own API
		$plugin_slug = self::$plugins[ $this->api_key ][ 'slug' ];

		// POST data to send to your API
		$args = array(
			'request'     => 'check_update',
			'slug'        => $plugin_slug,
			'version'     => $transient->checked[ $plugin_slug ],
			'license_key' => self::$keys[ $this->api_key ][ 'license_key' ],
			'api_key'     => $this->api_key,
		);

		// Send request checking for an update
		$response = $this->prepare_request( $args );

		// If response is false, don't alter the transient
		if ( !empty( $response->new_version ) ) {
			$transient->response[ $plugin_slug ] = $response;
		}

		return $transient;
	} // End update_check()


	/**
	 *
	 *
	 * @param unknown $false
	 * @param unknown $action
	 * @param unknown $args
	 *
	 * @return unknown
	 */
	function plugin_information( $false, $action, $args )
	{
		$this->load_user_data();

		$plugin_slug = self::$plugins[ $this->api_key ][ 'slug' ];

		$transient = get_site_transient( 'update_plugins' );

		// Check if this plugins API is about this plugin
		if ( empty( $args->slug ) || $args->slug != $plugin_slug ) {
			return false;
		}

		// No valid license key exists for this API key
		if ( empty ( self::$keys[ $this->api_key ][ 'status' ] ) ) {
			return false;
		}

		// POST data to send to your API
		$args = array(
			'request'     => 'plugin_info',
			'slug'        => $plugin_slug,
			'version'     => $transient->checked[ $plugin_slug ],
			'license_key' => self::$keys[ $this->api_key ][ 'license_key' ],
			'api_key'     => $this->api_key,
		);

		// Send request for detailed information
		$response           = $this->prepare_request( $args );
		$response->per_page = 24;

		return $response;
	} // End plugin_information()


	/**
	 * prepare_request function.
	 *
	 * @access public
	 *
	 * @param array $args
	 *
	 * @return object $response or boolean false
	 */
	function prepare_request( $args )
	{
		$url = add_query_arg( 'wc-api', 'software-api', $this->api_url );

		$args[ 'SERVER_GLOBAL' ] = $_SERVER;
		$request                 = wp_remote_post( $url, array(
															  'method'      => 'POST',
															  'timeout'     => 45,
															  'redirection' => 5,
															  'httpversion' => '1.0',
															  'blocking'    => true,
															  'headers'     => array(),
															  'body'        => $args,
															  'cookies'     => array(),
															  'sslverify'   => false,
														 )
		);

		// Make sure the request was successful
		if ( is_wp_error( $request )
			or
			wp_remote_retrieve_response_code( $request ) != 200
		) {
			// Request failed
			return false;
		}

		// Read server response, which should be an object
		$response = maybe_unserialize( json_decode( wp_remote_retrieve_body( $request ) ) );
		if ( is_object( $response ) ) {
			return $response;
		} else {
			// Unexpected response
			return false;
		}
	} // End prepare_request()


	/**
	 * instance_exists function.
	 *
	 * @access public
	 * @return void
	 */
	function instance_exists()
	{
		global $submenu;

		$exists = false;

		// Check if the menu item already exists.
		if ( isset( $submenu[ 'index.php' ] ) && is_array( $submenu[ 'index.php' ] ) ) {
			foreach ( $submenu[ 'index.php' ] as $k => $v ) {
				if ( isset( $v[ 2 ] ) && ( $v[ 2 ] == $this->plugin_token ) ) {
					$exists = true;
					break;
				}
			}
		}

		return $exists;
	} // End instance_exists()


	/**
	 * hide_admin_notice function.
	 *
	 * @access public
	 * @return void
	 */
	function hide_admin_notice()
	{
		if ( isset( $_GET[ $this->plugin_token . '-hide-updatenotice' ] ) && ( $_GET[ $this->plugin_token . '-hide-updatenotice' ] == 'true' ) ) {
			add_user_meta( get_current_user_id(), $this->plugin_prefix . 'hide-admin-notice', true, true );
		}
	} // End hide_admin_notice()
} // End Class
