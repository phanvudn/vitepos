<?php
/**
 * Its pos appsbd-ajax-confirm-response model
 *
 * @since: 21/09/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package Appsbd\V1\libs
 */

namespace Appsbd\V1\libs;

use Appsbd\V1\Core\Kernel;

if ( ! class_exists( __NAMESPACE__ . '\Ajax_Confirm_Response' ) ) {
	/**
	 * Class appsbd_ajax_confirm_response
	 *
	 * @package Appsbd\V1\libs
	 */
	class Ajax_Confirm_Response {
		/**
		 * Its property status
		 *
		 * @var bool
		 */
		public $status = false;
		/**
		 * Its property msg
		 *
		 * @var string
		 */
		public $msg = '';
		/**
		 * Its property data
		 *
		 * @var null
		 */
		public $data = null;
		/**
		 * Its property icon
		 *
		 * @var string
		 */
		public $icon = '';
		/**
		 * Its property is_sticky
		 *
		 * @var bool
		 */
		public $is_sticky = false;
		/**
		 * Its property title
		 *
		 * @var null
		 */
		public $title = null;

		/**
		 * The set response is generated by appsbd
		 *
		 * @param bool   $status Its status parameter.
		 * @param null   $data Its data parameter.
		 * @param string $icon Its icon parameter.
		 * @param null   $title Its title parameter.
		 * @param false  $is_sticky Its sticky parameter.
		 */
		public function set_response( $status, $data = null, $icon = '', $title = null, $is_sticky = false ) {
			if ( empty( $icon ) ) {
				$icon = $status ? ' fa fa-check-circle-o ' : ' fa fa-times-circle-o ';
			}
			$this->status    = $status;
			$this->msg       = \Appsbd\V1\Core\Kernel::get_msg_for_api();
			$this->data      = $data;
			$this->icon      = $icon;
			$this->is_sticky = $is_sticky;
			$this->title     = $title;

		}

		/**
		 * The display with response is generated by appsbd
		 *
		 * @param mixed $status Its status param.
		 * @param null  $data Its data param.
		 */
		public function display_with_response( $status, $data = null ) {
			$this->set_response( $status, $data );
			$this->display();
		}

		/**
		 * The add error is generated by appsbd
		 *
		 * @param mixed $msg error msg.
		 */
		public function add_error( $msg ) {
			Kernel::add_error( $msg );
		}

		/**
		 * The add info is generated by appsbd
		 *
		 * @param mixed $msg info msg.
		 */
		public function add_info( $msg ) {
			Kernel::add_info( $msg );
		}

		/**
		 * The add debug is generated by appsbd
		 *
		 * @param mixed $msg debug msg.
		 */
		public function add_debug( $msg ) {
			Kernel::add_debug( $msg );
		}

		/**
		 * The add warning is generated by appsbd
		 *
		 * @param mixed $msg warning msg.
		 */
		public function add_warning( $msg ) {
			Kernel::add_warning( $msg );
		}

		/**
		 * The display is generated by appsbd
		 */
		public function display() {
			wp_send_json( $this );
		}
	}
}
