<?php
/**
 * Its used for Base model.
 *
 * @since: 21/09/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos\Api\V1
 */

namespace VitePos\Api\V1;

use Appsbd\V1\libs\ACL;
use VitePos\Libs\API_Base;
use VitePos\Libs\Manifest;
use Vitepos\Models\Database\Mapbd_Pos_Purchase;
use Vitepos\Models\Database\Mapbd_Pos_Stock_Transfer;
use VitePos\Modules\APBD_EPOS_Settings;
use VitePos\Modules\POS_Settings;

/**
 * Class heartbit_api
 *
 * @package VitePos\Api\V1
 */
class Heartbit_Api extends API_Base {
	/**
	 * The set api base is generated by appsbd
	 *
	 * @return mixed|string
	 */
	public function set_api_base() {
		return 'system';
	}

	/**
	 * The routes is generated by appsbd
	 *
	 * @return mixed|void
	 */
	public function routes() {
		$this->register_rest_route( 'GET', 'heart-bit', array( $this, 'heart_bit' ) );
	}

	/**
	 * The set route permission is generated by appsbd
	 *
	 * @param \VitePos\Libs\any $route Its string.
	 *
	 * @return bool
	 */
	public function set_route_permission( $route ) {
		return true;
	}

	/**
	 * The heart bit is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function heart_bit() {
		$resdata               = new \stdClass();
		$resdata->is_change    = false;
		$resdata->is_logged_in = is_user_logged_in();
		$resdata->settings     = POS_Settings::get_settings( 'hbit' );
		if ( current_user_can( 'pos-menu' ) || current_user_can( 'cashier-menu' ) ) {
			$resdata->drawer_info = POS_Settings::get_drawer_info( $this->get_outlet_id(), $this->get_counter_id(), 'hbit' );
		}
		$resdata->sync_id      = POS_Settings::get_current_sync_id();
		$resdata->rec_req      = 0; 
		$resdata->dec_req      = 0; 
		$resdata->up_pro_count = 0; 
		if ( $resdata->is_logged_in ) {
			$resdata->user    = get_user_by( 'id', $this->get_current_user_id() )->display_name;
			$resdata->rec_req = Mapbd_Pos_Stock_Transfer::get_receive_count( $this->get_outlet_id() );
			$resdata->dec_req = Mapbd_Pos_Stock_Transfer::get_declined_count( $this->get_outlet_id() );
			
		} else {
			$resdata->user = null;
		}
		$resdata->id = $this->get_current_user_id();
		$this->response->set_response( true, 'pulse', $resdata );

		return $this->response;
	}
}
