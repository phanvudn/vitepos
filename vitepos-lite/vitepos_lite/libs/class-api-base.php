<?php
/**
 * Its used for api base
 *
 * @since: 21/09/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos_Lite\Libs
 */

namespace VitePos_Lite\Libs;

use Appsbd_Lite\V1\libs\API_Response;
use Appsbd_Lite\V1\libs\AppInput;
use VitePos_Lite\Core\VitePos;
use VitePos_Lite\Models\Database\Mapbd_Pos_Counter;
use VitePos_Lite\Models\Database\Mapbd_Pos_Warehouse;

if ( ! class_exists( __NAMESPACE__ . '\API_Base' ) ) {
	/**
	 * Class VitePOS_API_Base
	 *
	 * @package VitePos_Lite\Libs
	 */
	abstract class API_Base {

		/**
		 * Its property response
		 *
		 * @var API_Response Its string.
		 */
		public $response;
		/**
		 * Its property namespace
		 *
		 * @var string Its string.
		 */
		public $namespace;
		/**
		 * Its property version
		 *
		 * @var string Its string.
		 */
		public $version;
		/**
		 * Its property api_base
		 *
		 * @var mixed|string Its string.
		 */
		protected $api_base = '';
		/**
		 * Its property logged_user
		 *
		 * @var false|\WP_User Its string.
		 */
		public $logged_user;
		/**
		 * Its property payload
		 *
		 * @var string Its string.
		 */
		public $payload;
		/**
		 * Its property payload_obj
		 *
		 * @var string Its string.
		 */
		public static $payload_obj;
		/**
		 * Its property is_loaded_payload
		 *
		 * @var bool Its bool.
		 */
		public static $is_loaded_payload = false;
		/**
		 * Its property is_vite_pos_request
		 *
		 * @var bool Its bool.
		 */
		public static $is_vite_pos_request = false;
		/**
		 * Its Kernel Object
		 *
		 * @var VitePos
		 */
		public $kernel_object;

		/**
		 * Its property outlet_id
		 *
		 * @var int
		 */
		public $outlet_id;
		/**
		 * Its property outlet_obj
		 *
		 * @var Mapbd_Pos_Warehouse
		 */
		public $outlet_obj;
		/**
		 * Its property counter_id
		 *
		 * @var int
		 */
		public $counter_id;
		/**
		 * Its property counter_obj
		 *
		 * @var Mapbd_Pos_Counter
		 */
		public $counter_obj;

		/**
		 * API_Base constructor.
		 *
		 * @param string  $namespace Its string of namespace.
		 * @param VitePos $kernel_object Its kernel object.
		 */
		public function __construct( $namespace, &$kernel_object ) {
			$this->load_payload();
			$this->kernel_object =&$kernel_object;
			$this->response      = new API_Response();
			$this->namespace     = $namespace;
			$this->logged_user   = wp_get_current_user();

			ob_start();
			$this->api_base = $this->set_api_base();
			if ( appsbd_is_rest() ) {
				$this->routes();
				add_action(
					'set_logged_in_cookie',
					function ( $logged_in_cookie ) {
						$_COOKIE[ LOGGED_IN_COOKIE ] = $logged_in_cookie;
					}
				);
				add_filter( 'posts_where', array( $this, 'query_search_filter' ), 10, 2 );
				add_filter( 'woocommerce_get_tax_location', array( $this, 'set_outlet_location' ), 99, 3 );
				add_filter( 'vitepos/filter/current-outlet', array( $this, 'current_outlet_filter' ) );
			}
		}

		/**
		 * The current outlet filter is generated by appsbd
		 *
		 * @param mixed $outlet_obj Its outlet object.
		 *
		 * @return Mapbd_Pos_Warehouse|null
		 */
		public function current_outlet_filter( $outlet_obj ) {
			return $this->get_outlet_obj();
		}
		/**
		 * The set outlet location is generated by appsbd
		 *
		 * @param any $location Its string.
		 * @param any $tax_class Its string.
		 * @param any $customer Its string.
		 *
		 * @return array Its array.
		 */
		public function set_outlet_location( $location, $tax_class, $customer ) {
			if ( self::$is_vite_pos_request ) {

				$outlet = $this->get_outlet_obj();
				if ( ! empty( $outlet ) ) {

					if ( ! empty( $outlet->country ) && ! empty( $outlet->state ) ) {
						$location = array(
							$outlet->country,
							$outlet->state,
							$outlet->zip_code,
							$outlet->city,
						);
					}
				}
			}
			return $location;
		}

		/**
		 * The query search filter is generated by appsbd
		 *
		 * @param any $where Its string.
		 * @param any $wp_query Its string.
		 *
		 * @return mixed Its string.
		 */
		public function query_search_filter( $where, $wp_query ) {
			return $where;
		}

		/**
		 * The set vite pos request is generated by appsbd
		 */
		public function set_vite_pos_request() {
			self::$is_vite_pos_request = true;
		}

		/**
		 * The set outlet is generated by appsbd
		 *
		 * @param mixed $outlet_id Its outlet id.
		 * @param mixed $counter_id Its counter id.
		 */
		public function set_outlet( $outlet_id, $counter_id ) {
			$this->outlet_id   = $outlet_id;
			$this->counter_id  = $counter_id;
			$this->outlet_obj  = null;
			$this->counter_obj = null;
		}
		/**
		 * The get outlet id is generated by appsbd
		 *
		 * @return mixed
		 */
		public function get_outlet_id() {
			return $this->outlet_id;
		}

		/**
		 * The get outlet obj is generated by appsbd
		 *
		 * @return Mapbd_Pos_Warehouse|null
		 */
		public function get_outlet_obj() {
			if ( ! empty( $this->outlet_id ) && empty( $this->outlet_obj ) ) {
				$this->outlet_obj = Mapbd_Pos_Warehouse::find_by( 'id', $this->outlet_id );
			}
			return $this->outlet_obj;
		}

		/**
		 * The get counter id is generated by appsbd
		 *
		 * @return mixed
		 */
		public function get_counter_id() {
			return $this->counter_id;
		}

		/**
		 * The get counter obj is generated by appsbd
		 *
		 * @return Mapbd_Pos_Counter|null
		 */
		public function get_counter_obj() {
			if ( ! empty( $this->counter_id ) && empty( $this->counter_obj ) ) {
				$this->counter_obj = Mapbd_Pos_Counter::find_by( 'id', $this->counter_id );
			}
			return $this->counter_obj;
		}

		/**
		 * The load payload is generated by appsbd
		 */
		public function load_payload() {
			if ( ! self::$is_loaded_payload ) {
				$type = get_request_content_type();
				$req_type = ! empty( $type ) ? strtolower( get_request_content_type() ) : '';
				if ( 'application/x-www-form-urlencoded' == $req_type || 'application/json' == $req_type ) {
					self::$payload_obj = file_get_contents( 'php://input' );
					if ( ! empty( self::$payload_obj ) ) {
						self::$payload_obj = json_decode( self::$payload_obj, true );
					}
				} else {
					self::$payload_obj = AppInput::get_posted_data();
				}
				self::$is_loaded_payload = true;
			}
			$this->payload          =& self::$payload_obj;
			$vite_outlet            = AppInput::get_server_data( 'HTTP_VITE_OUTLET' );
			$request_outlet_counter = ! empty( $vite_outlet ) ? explode( '|', $vite_outlet ) : array();
			if ( ! empty( $request_outlet_counter[0] ) ) {
				$this->outlet_id = $request_outlet_counter[0];
			}
			if ( ! empty( $request_outlet_counter[1] ) ) {
				$this->counter_id = $request_outlet_counter[1];
			}
		}

		/**
		 * The get current user id is generated by appsbd
		 *
		 * @return int Its integer.
		 */
		public function get_current_user_id() {
			return $this->logged_user->ID;
		}

		/**
		 * The add error is generated by appsbd
		 *
		 * @param any  $message Its string.
		 * @param null $parameter Its null.
		 * @param null $_ Its null.
		 */
		public function add_error( $message, $parameter = null, $_ = null ) {
			$args    = func_get_args();
			$message = call_user_func_array( array( $this->kernel_object, '__' ), $args );
			\Appsbd_Lite\V1\Core\Kernel_Lite::add_error( $message );
		}

		/**
		 * The filter for api is generated by appsbd
		 *
		 * @param any $item Its null.
		 */
		public function filter_for_api( &$item ) {
		}

		/**
		 * The GetPayload is generated by appsbd
		 *
		 * @param any  $key Its string.
		 * @param null $default Its null.
		 *
		 * @return mixed|string|null Its string.
		 */
		public function get_payload( $key, $default = null ) {
			return ! empty( $this->payload[ $key ] ) ? $this->payload[ $key ] : $default;
		}

		/**
		 * The add info is generated by appsbd
		 *
		 * @param any  $message Its string.
		 * @param null $parameter Its null.
		 * @param null $_ Its null.
		 */
		public function add_info( $message, $parameter = null, $_ = null ) {
			$args    = func_get_args();
			$message = call_user_func_array( array( $this->kernel_object, '__' ), $args );
			\Appsbd_Lite\V1\Core\Kernel_Lite::add_info( $message );
		}

		/**
		 * The add debug is generated by appsbd
		 *
		 * @param any $obj Its string.
		 */
		public function add_debug( $obj ) {
			\Appsbd_Lite\V1\Core\Kernel_Lite::add_debug( $obj );
		}

		/**
		 * The add warning is generated by appsbd
		 *
		 * @param any  $message Its string.
		 * @param null $parameter Its null.
		 * @param null $_ Its null.
		 */
		public function add_warning( $message, $parameter = null, $_ = null ) {
			$args    = func_get_args();
			$message = call_user_func_array( array( $this->kernel_object, '__' ), $args );
			\Appsbd_Lite\V1\Core\Kernel_Lite::add_warning( $message );
		}

		/**
		 * The routes is generated by appsbd
		 *
		 * @return mixed Its mixed.
		 */
		abstract public function routes();

		/**
		 * The setAPIBase is generated by appsbd
		 *
		 * @return mixed Its mixed.
		 */
		abstract public function set_api_base();


		/**
		 * The register rest route is generated by appsbd
		 *
		 * @param string   $methods Its string.
		 * @param string   $route Its string.
		 * @param callable $callback Its string.
		 * @param string   $permission_callback Its string.
		 */
		public function register_rest_route( $methods, $route, $callback, $permission_callback = '' ) {
			 $thisobj =&$this;
			if ( empty( $permission_callback ) ) {
				$permission_callback = function ( \WP_REST_Request $request ) use ( $route, $thisobj ) {
					$mainroute = explode( '/', $route );
					if ( ! empty( $mainroute[0] ) ) {
						$permission = $this->set_route_permission( $mainroute[0] );
						/**
						 * Its for api permission.
						 *
						 * @since 1.0
						 */
						$permission = apply_filters( 'apbd-wps/filter/api-permission', $permission, $mainroute[0], $request );
						/**
						 * Its for api permission.
						 *
						 * @since 1.0
						 */
						$permission = apply_filters( 'apbd-wps/filter/api-permission/' . $this->api_base . '/' . $mainroute[0], $permission, $request );
						return $permission;
					} else {
						return true;
					}
				};
			}
			if ( ! empty( $this->api_base ) ) {

				register_rest_route(
					$this->namespace,
					'/' . $this->api_base . '/' . $route,
					array(
						'methods'             => $methods,
						'callback'            => $callback,
						'permission_callback' => $permission_callback,
					)
				);
			}
		}

		/**
		 * Its destruct
		 */
		public function __destruct() {
			$debuglog = ob_get_clean();
		}

		/**
		 * The set response is generated by appsbd
		 *
		 * @param any    $status Its string.
		 * @param string $message Its String.
		 * @param null   $data Its null.
		 */
		public function set_response( $status, $message = '', $data = null ) {
			$this->response->status = $status;
			$this->response->data   = $data;
			$this->response->msg    = $message;
		}

		/**
		 * The set route permission is generated by appsbd
		 *
		 * @param any $route Its string.
		 *
		 * @return bool Its bool.
		 */
		public function set_route_permission( $route ) {
			return is_user_logged_in();
		}
	}
}
