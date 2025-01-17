<?php
/**
 * Vitepos Model
 *
 * @package VitePos\Core
 */

namespace VitePos\Core;

use Appsbd\V1\Core\BaseModule;
use Appsbd\V1\libs\Ajax_Confirm_Response;

/**
 * Class ViteposModel
 *
 * @package VitePos\Core
 */
abstract class Vitepos_Module extends BaseModule {
	/**
	 * The check ajax referer is generated by appsbd
	 *
	 * @param bool $is_return Its checking security.
	 *
	 * @return bool
	 */
	public function app_check_ajax_referer( $is_return = false ) {
		
		if ( $this->kernel_object->is_develop_mode() ) {
			$headers = getallheaders();
			if ( ! empty( $headers['appsbd_dev'] ) ) {
				 return true;
			}
		}
		

		if ( ! check_ajax_referer( 'vitepos', '_wpnonce', false ) ) {
			if ( $is_return ) {
				return false;
			}
			$main_response = new Ajax_Confirm_Response();
			$this->add_error( 'Nonce error' );
			$main_response->display_with_response( false, null, 403 );
		}

		return true;
	}

	/**
	 * The AddAjaxAction is generated by appsbd
	 *
	 * @param any      $action_name Its action_name param.
	 * @param callable $function_to_add Its function_to_add param.
	 */
	public function add_ajax_action( $action_name, $function_to_add ) {
		$action_name = $this->get_action_name( $action_name );
		if ( $this->app_check_ajax_referer( true ) ) {
			add_action( 'wp_ajax_' . $action_name, $function_to_add );
		} else {
			add_action(
				'wp_ajax_' . $action_name,
				function() {
					$main_response = new Ajax_Confirm_Response();
					$this->add_error( 'Nonce error' );
					$main_response->display_with_response( false, null, 403 );
				}
			);
		}
	}

	/**
	 * This is a test ajax request to get nounce error.
	 *
	 * @param bool $is_return This is a test ajax request to get nounce error.
	 *
	 * @return false
	 */
	public function test_ajax_referer( $is_return = false ) {
		if ( ! check_ajax_referer( 'viteposs', '_wpnonce', false ) ) {
			if ( $is_return ) {
				return false;
			}
			$main_response = new Ajax_Confirm_Response();
			$this->add_error( 'Nonce error' );
			$main_response->display_with_response( false, null, 403 );
		}
	}

}
