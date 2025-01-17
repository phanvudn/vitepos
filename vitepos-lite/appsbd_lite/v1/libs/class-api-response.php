<?php
/**
 * Its pos api-response model
 *
 * @since: 21/09/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos\Libs
 */

namespace Appsbd_Lite\V1\libs;

if ( ! class_exists( __NAMESPACE__ . '\API_Response' ) ) {
	/**
	 * Class API Response
	 *
	 * @package VitePos\Libs
	 */
	#[\AllowDynamicProperties]
	class API_Response {

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
		 * The set data is generated by appsbd
		 *
		 * @param null $data Its data param.
		 */
		public function set_data( $data = null ) {
			$this->data = $data;
		}
		/**
		 * The set response is generated by appsbd
		 *
		 * @param boolean $status Its status param.
		 * @param string  $message Its message param.
		 * @param null    $data Its data param.
		 */
		public function set_response( $status, $message = '', $data = null ) {
			$this->status = $status;
			if ( ! empty( $message ) && is_string( $message ) ) {
				if ( $status ) {
					\Appsbd_Lite\V1\Core\Kernel_Lite::add_info( $message );
				} else {
					\Appsbd_Lite\V1\Core\Kernel_Lite::add_error( $message );
				}
			}
			$this->msg  = \Appsbd_Lite\V1\Core\Kernel_Lite::get_msg_for_api();
			$this->data = $data;
		}

		/**
		 * The get response is generated by appsbd
		 *
		 * @return $this
		 */
		public function get_response() {
			$this->msg = \Appsbd_Lite\V1\Core\Kernel_Lite::get_msg_for_api();
			return $this;
		}
	}
}
