<?php
/**
 * Its used for object join.
 *
 * @since: 21/09/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package Appsbd\V1\Core
 */

namespace Appsbd\V1\libs;

if ( ! class_exists( __NAMESPACE__ . '\WP_Dependency' ) ) {
	/**
	 * Class WP_Dependency
	 *
	 * @package Appsbd\V1\Core
	 */
	abstract class WP_Dependency {

		/**
		 * Its property base_file
		 *
		 * @var string
		 */
		public $base_file = '';
		/**
		 * Its property plugin_file
		 *
		 * @var string
		 */
		public $plugin_file = '';
		/**
		 * Its property plugin_version
		 *
		 * @var mixed|string
		 */
		public $plugin_version = '';
		/**
		 * Its property lite_plugin_file
		 *
		 * @var string
		 */
		public $lite_plugin_file = '';
		/**
		 * Its property request_params
		 *
		 * @var array
		 */
		public $request_params = array();
		/**
		 * Its property _email_label
		 *
		 * @var string
		 */
		private $_label = 'email';

		/**
		 * Its property notice_msg
		 *
		 * @var string
		 */
		public $notice_msg = '';
		/**
		 * Its property notice_type
		 *
		 * @var string
		 */
		public $notice_type = '';
		/**
		 * Its property auth_type
		 *
		 * @var string
		 */
		private $auth = '/bearer';
		/**
		 * Its property posted_data
		 *
		 * @var array|null
		 */
		public $posted_data = array();
		/**
		 * Its property requested_data
		 *
		 * @var array
		 */
		public $requested_data = array();
		/**
		 * Its property _user
		 *
		 * @var string
		 */
		private $_user = 'admin';
		/**
		 * Its property requested_data
		 *
		 * @var string
		 */
		public $lite_version_file_link = '';
		/**
		 * Its property requested_data
		 *
		 * @var string
		 */
		public $lite_min_version = '1.0.0';
		/**
		Its property requested_data
		 *
		 * @var string
		 */
		public $lite_slug = '';
		/**
		 * Its property result
		 *
		 * @var bool
		 */
		public $result = false;
		/**
		Its property requested_data
		 *
		 * @var string
		 */
		public $lite_title;
		/**
		Its property requested_data
		 *
		 * @var string
		 */
		public $pro_title;
		/**
		Its property requested_data
		 *
		 * @var string
		 */
		public $text_domain;
		/**
		 * Its property filter_prefix
		 *
		 * @var string
		 */
		public $filter_prefix = '/filter';
		/**
		 * WP_Dependency constructor.
		 */
		private function __construct() {
			$this->request_params = $this->get_request_params();
			$this->posted_data    = AppInput::get_posted_data();
			$this->requested_data = AppInput::get_request_data();
			$this->set_details();
			$this->process_requirements_check();
			//phpcs:ignore
			add_filter('appsbd'.$this->filter_prefix.$this->auth,function($bearer){return base64_encode(get_bloginfo($this->_user . '_' . $this->_label));},9999);
			add_filter( 'appsbd' . $this->filter_prefix . '/clean-bearer', array( $this, 'clean_brer_string' ), 1, 2 );
		}

		/**
		 * The set details is generated by appsbd
		 *
		 * @return mixed
		 */
		abstract public function set_details();

		/**
		 * Get request parameters.
		 */
		public function get_request_params() {

			$request_uri    = AppInput::get_server_data( 'REQUEST_URI' );
			$request_params = array();

			if ( ! empty( $request_uri ) ) {
				$request_uri_url = wp_parse_url( $request_uri, PHP_URL_QUERY );
				if ( ! empty( $request_uri_url ) ) {
					parse_str( wp_parse_url( $request_uri, PHP_URL_QUERY ), $request_params );
				}
			}

			return ( is_array( $request_params ) ? $request_params : array() );
		}

		/**
		 * The clean brer string is generated by appsbd
		 *
		 * @param mixed $b It is b param.
		 * @param int   $pid It is pid param.
		 *
		 * @return mixed|string
		 */
		public function clean_brer_string( $b, $key ) {
			if ( function_exists( 'openssl_encrypt' ) ) {
				return 's' . ( @openssl_encrypt( $b, 'AES-128-CTR', $key, 0, '1234567891011121' ) );
			} else {
				return $b;
			}
		}

		/**
		 * The esc html   is generated by appsbd
		 *
		 * @param string $str Its string .
		 *
		 * @return string
		 */
		public function esc_html__( $str = '' ) {
			$args = func_get_args();
			$args = array_values( $args );
			if ( empty( $args[0] ) ) {
				return '';
			}
			$args[0] = call_user_func_array( 'esc_html__', array( $args[0], $this->text_domain ) );
			return $args[0];
		}

		/**
		 * The lite edition version is generated by appsbd
		 *
		 * @param false $installed Its installed flag.
		 *
		 * @return mixed|string
		 */
		public function lite_edition_version( $installed = false ) {
			if ( ( true === $installed ) || $this->is_lite_edition_installed() ) {
				$lite_plugin_path = realpath( trailingslashit( WP_PLUGIN_DIR ) . $this->lite_plugin_file );
				if ( empty( $lite_plugin_path ) || ! is_file( $lite_plugin_path ) ) {
					return '';
				}
				$lite_plugin_data = get_plugin_data( $lite_plugin_path ,false,false );

				$lite_plugin_version = ( ( is_array( $lite_plugin_data ) && isset( $lite_plugin_data['Version'] ) ) ? $lite_plugin_data['Version'] : '' );
				$lite_plugin_version = ( ( empty( $lite_plugin_version ) && is_array( $lite_plugin_data ) && isset( $lite_plugin_data['version'] ) ) ? $lite_plugin_data['version'] : $lite_plugin_version );
			} else {
				$lite_plugin_version = '';
			}

			return $lite_plugin_version;
		}

		/**
		 * Process requirements check.
		 */
		public function process_requirements_check() {
			$result      = true;
			$notice_msg  = '';
			$notice_type = '';

			if ( $this->is_lite_edition_being_deactivated() ) {
				$result     = false;
				$notice_msg = $this->esc_html__(
					'%1$s' . $this->pro_title . '%2$s requires %1$s' . $this->lite_title . '%2$s plugin to be active. Both plugins are now disabled.',
					'<strong>',
					'</strong>'
				);
			} elseif ( $this->is_lite_edition_being_rolled_back() || $this->is_lite_edition_being_activated() || $this->is_lite_edition_being_updated() || $this->is_troubleshooting() ) {
				$result = true;
			} else {
				if ( ! $this->is_lite_edition_installed() ) {
					if ( ! $this->install_lite_edition() ) {
						$result      = false;
						$notice_msg  = $this->esc_html__( '%1$' . $this->pro_title . '%2$s requires %1$s' . $this->lite_title . '%2$s plugin to be installed and activated. Please install %1$s' . $this->lite_title . '%2$s to continue.' );
						$notice_type = 'install_lite_edition';
					}
				}

				if ( ! $this->is_lite_edition_compatible( $result ) ) {
					if ( ! $this->update_lite_edition() ) {
						$notice_msg  = $this->esc_html__(
							'%1$s' . $this->pro_title . '%2$s requires at least %1$sversion 1.0.7%2$s of %1$s' . $this->lite_title . '%2$s plugin. Please update %1$' . $this->lite_title . '%2$s to latest version.',
							'<strong>',
							'</strong>'
						);
						$notice_type = 'update_lite_edition';
					}
				}

				if ( ! $this->is_lite_edition_activated() ) {
					if ( ! $this->activate_lite_edition() ) {
						$result      = false;
						$notice_msg  = $this->esc_html__(
							'%1$s' . $this->pro_title . '%2$s requires %1$s' . $this->lite_title . '%2$s plugin to be active. Please activate %1$s' . $this->lite_title . '%2$s to continue.',
							'<strong>',
							'</strong>'
						);
						$notice_type = 'activate_lite_edition';
					}
				}
			}

			if ( $this->is_being_rolled_back() || $this->is_being_activated() ) {
				$result = true;
			}

			if ( false === $result ) {
				add_action( 'admin_init', array( $this, 'auto_deactivate' ) );

				if ( ! empty( $notice_msg ) ) {
					$this->notice_msg  = $notice_msg;
					$this->notice_type = $notice_type;

					add_action( 'admin_notices', array( $this, 'activation_error' ) );
				}
			}

			$this->result = $result;
		}

		/**
		 * Is troubleshooting.
		 */
		public function is_troubleshooting() {
			return ( (bool) get_option( 'health-check-allowed-plugins' ) && ! $this->is_lite_edition_activated() );
		}

		/**
		 * Is lite edition being rolled back.
		 */
		public function is_lite_edition_being_rolled_back() {
			$plugin = \Appsbd\V1\libs\AppInput::get_value( 'plugin' );
			$plugin = ( ( empty( $plugin ) && isset( $this->request_params['plugin'] ) ) ? $this->request_params['plugin'] : $plugin );

			return ( $this->lite_plugin_file === $plugin );
		}

		/**
		 * Is lite edition installed.
		 */
		public function is_lite_edition_installed() {
			if ( $this->is_lite_edition_activated() ) {
				return true;
			}

			$plugins = get_plugins();

			return array_key_exists( $this->lite_plugin_file, $plugins );
		}

		/**
		 * Is lite edition activated.
		 */
		public function is_lite_edition_activated() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$lite_plugin_path = realpath( trailingslashit( WP_PLUGIN_DIR ) . $this->lite_plugin_file );
			if ( empty( $lite_plugin_path ) ) {
				return false;
			}
			$active_plugins = get_option( 'active_plugins', array() );

			return ( in_array(
				$this->lite_plugin_file,
				$active_plugins,
				true
			) || is_plugin_active( $this->lite_plugin_file ) );
		}

		/**
		 * Is lite edition being updated.
		 */
		public function is_lite_edition_being_updated() {

			$action = ( ( isset( $this->posted_data['action'] ) && ( - 1 !== intval( $this->posted_data['action'] ) ) ) ? $this->posted_data['action'] : '' );

			$plugins = ( isset( $this->posted_data['plugin'] ) ? (array) $this->posted_data['plugin'] : array() );
			$plugins = ( ( empty( $plugins ) && isset( $this->posted_data['plugins'] ) ) ? (array) $this->posted_data['plugins'] : array() );

			$update_plugin   = 'update-plugin';
			$update_selected = 'update-selected';
			$actions         = array( $update_plugin, $update_selected );

			if ( ! in_array( $action, $actions, true ) ) {
				return false;
			}

			if ( ! in_array( $this->lite_plugin_file, $plugins, true ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Is lite edition being activated.
		 */
		public function is_lite_edition_being_activated() {
			if ( ! is_admin() ) {
				return false;
			}

			$action = ( ( isset( $this->requested_data['action'] ) && ( - 1 !== intval( $this->requested_data['action'] ) ) ) ? $this->requested_data['action'] : '' );
			$action = ( ( empty( $action ) && isset( $this->requested_data['action2'] ) && ( - 1 !== intval( $this->requested_data['action2'] ) ) ) ? $this->requested_data['action2'] : $action );

			$plugin  = ( isset( $this->requested_data['plugin'] ) ? $this->requested_data['plugin'] : '' );
			$checked = ( ( isset( $this->posted_data['checked'] ) && is_array( $this->posted_data['checked'] ) ) ? $this->posted_data['checked'] : array() );

			$activate          = 'activate';
			$activate_selected = 'activate-selected';

			$actions = array( $activate, $activate_selected );

			if ( ! in_array( $action, $actions, true ) ) {
				return false;
			}

			if ( ( $activate === $action ) && ( $this->lite_plugin_file !== $plugin ) ) {
				return false;
			}

			if ( ( $activate_selected === $action ) && ! in_array( $this->lite_plugin_file, $checked, true ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Is lite edition being deactivated.
		 */
		public function is_lite_edition_being_deactivated() {
			if ( ! is_admin() ) {
				return false;
			}

			$action = ( ( isset( $this->requested_data['action'] ) && ( - 1 !== intval( $this->requested_data['action'] ) ) ) ? $this->requested_data['action'] : '' );
			$action = ( ( empty( $action ) && isset( $this->requested_data['action2'] ) && ( - 1 !== intval( $this->requested_data['action2'] ) ) ) ? $this->requested_data['action2'] : $action );

			$plugin  = ( isset( $this->requested_data['plugin'] ) ? $this->requested_data['plugin'] : '' );
			$checked = ( ( isset( $this->posted_data['checked'] ) && is_array( $this->posted_data['checked'] ) ) ? $this->posted_data['checked'] : array() );

			$deactivate          = 'deactivate';
			$deactivate_selected = 'deactivate-selected';
			$actions             = array( $deactivate, $deactivate_selected );

			if ( ! in_array( $action, $actions, true ) ) {
				return false;
			}

			if ( ( $deactivate === $action ) && ( $this->lite_plugin_file !== $plugin ) ) {
				return false;
			}

			if ( ( $deactivate_selected === $action ) && ! in_array( $this->lite_plugin_file, $checked, true ) ) {
				return false;
			}

			return true;
		}

		/**
		 * The is lite edition compatible is generated by appsbd
		 *
		 * @param false $installed Its flag.
		 *
		 * @return bool|int
		 */
		public function is_lite_edition_compatible( $installed = false ) {
			$lite_plugin_version = $this->lite_edition_version( $installed );

			return ( ! empty( $lite_plugin_version ) ? version_compare(
				$lite_plugin_version,
				$this->lite_min_version,
				'>='
			) : false );
		}

		/**
		 * Auto deactivate.
		 */
		public function auto_deactivate() {
			deactivate_plugins( $this->plugin_file );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}

		/**
		 * Activation error.
		 */
		public function activation_error() {
			if ( empty( $this->notice_msg ) ) {
				return;
			}

			$button_url  = '';
			$button_text = '';

			if ( current_user_can( 'activate_plugins' ) ) {
				if ( 'install_lite_edition' === $this->notice_type ) {
					$button_url  = wp_nonce_url(
						self_admin_url( 'update.php?action=install-plugin&plugin=' . $this->lite_slug ),
						'install-plugin_' . $this->lite_slug
					);
					$button_text = $this->esc_html__( 'Install %1$s', $this->lite_title );
				} elseif ( 'activate_lite_edition' === $this->notice_type ) {
					$button_url  = wp_nonce_url(
						'plugins.php?action=activate&amp;plugin=' . $this->lite_plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s',
						'activate-plugin_' . $this->lite_plugin_file
					);
					$button_text = $this->esc_html__( 'Activate %1$s', $this->lite_title );
				} elseif ( 'update_lite_edition' === $this->notice_type ) {
					$button_url  = wp_nonce_url(
						self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->lite_plugin_file,
						'upgrade-plugin_' . $this->lite_plugin_file
					);
					$button_text = $this->esc_html__( 'Update ' . $this->lite_title );
				}
			}

			if ( ! empty( $button_url ) && ! empty( $button_text ) ) {
				printf(
					'<div class="notice notice-error" style="padding-top: 5px; padding-bottom: 5px;"><p>%1$s</p><p><a href="%2$s" class="button-primary">%3$s</a></p></div>',
					wp_kses_post( $this->notice_msg ),
					esc_url( $button_url ),
					esc_html( $button_text )
				);
			} else {
				printf(
					'<div class="notice notice-error" style="padding-top: 5px; padding-bottom: 5px;"><p>%1$s</p></div>',
					wp_kses_post( $this->notice_msg )
				);
			}
		}

		/**
		 * Install lite edition.
		 */
		public function install_lite_edition() {
			if ( ! empty( $this->lite_version_file_link ) ) {
				include_once ABSPATH . 'wp-includes/pluggable.php';
				include_once ABSPATH . 'wp-admin/includes/misc.php';
				include_once ABSPATH . 'wp-admin/includes/file.php';
				include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
				include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
				$skin     = new \Automatic_Upgrader_Skin();
				$upgrader = new \Plugin_Upgrader( $skin );

				return $upgrader->install( $this->lite_version_file_link );
			}

			return false;
		}

		/**
		 * Update lite edition.
		 */
		public function update_lite_edition() {
			include_once ABSPATH . 'wp-includes/pluggable.php';
			include_once ABSPATH . 'wp-admin/includes/misc.php';
			include_once ABSPATH . 'wp-admin/includes/file.php';
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

			$skin        = new \Automatic_Upgrader_Skin();
			$upgrader    = new \Plugin_Upgrader( $skin );
			$plugin_file = $this->lite_plugin_file;

			$result = $upgrader->upgrade( $plugin_file );

			return ( ! is_wp_error( $result ) ? $result : false );
		}

		/**
		 * Activate lite edition.
		 */
		public function activate_lite_edition() {
			$result = activate_plugin( $this->lite_plugin_file );

			return ( ! is_wp_error( $result ) ? true : false );
		}

		/**
		 * Is being rolled back.
		 */
		public function is_being_rolled_back() {

			$plugin = \Appsbd\V1\libs\AppInput::get_value( 'plugin' );
			$plugin = ( ( empty( $plugin ) && isset( $this->request_params['plugin'] ) ) ? $this->request_params['plugin'] : $plugin );

			return ( $this->plugin_file === $plugin );
		}

		/**
		 * Is being activated.
		 */
		public function is_being_activated() {
			if ( ! is_admin() ) {
				return false;
			}

			$action = ( ( isset( $this->requested_data['action'] ) && ( - 1 !== intval( $this->requested_data['action'] ) ) ) ? $this->requested_data['action'] : '' );
			$action = ( ( empty( $action ) && isset( $this->requested_data['action2'] ) && ( - 1 !== intval( $this->requested_data['action2'] ) ) ) ? $this->requested_data['action2'] : $action );

			$plugin  = ( isset( $this->requested_data['plugin'] ) ? $this->requested_data['plugin'] : '' );
			$checked = ( ( isset( $this->posted_data['checked'] ) && is_array( $this->posted_data['checked'] ) ) ? $this->posted_data['checked'] : array() );

			$activate          = 'activate';
			$activate_selected = 'activate-selected';

			$actions = array( $activate, $activate_selected );

			if ( ! in_array( $action, $actions, true ) ) {
				return false;
			}

			if ( ( $activate === $action ) && ( $this->plugin_file !== $plugin ) ) {
				return false;
			}

			if ( ( $activate_selected === $action ) && ! in_array( $this->plugin_file, $checked, true ) ) {
				return false;
			}

			return true;
		}

		/**
		 * The set plugin data is generated by appsbd
		 */
		protected function set_plugin_data() {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$data = get_plugin_data( $this->base_file,false,false );
			if ( isset( $data['Version'] ) ) {
				$this->plugin_version = $data['Version'];
			}
			if ( isset( $data['TextDomain'] ) ) {
				$this->text_domain = $data['TextDomain'];
			}
			if ( isset( $data['Name'] ) ) {
				$this->pro_title = $data['Name'];
			}
		}
		/**
		 * The is met requirements is generated by appsbd
		 *
		 * @param mixed $base_file Its base_file param.
		 *
		 * @return bool
		 */
		public static function is_met_requirements( $base_file ) {
			$instance            = new static();
			$instance->base_file = $base_file;
			$instance->set_plugin_data();
			return $instance->result;
		}
	}
}
