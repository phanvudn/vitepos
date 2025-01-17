<?php
/**
 * Its api for vendor
 *
 * @since: 12/07/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos\Api\V1
 */

namespace VitePos\Api\V1;

use Appsbd\V1\libs\API_Data_Response;
use Appsbd\V1\libs\AppInput;
use VitePos\Libs\API_Base;
use Vitepos\Models\Database\Mapbd_Pos_Table;
use VitePos\Modules\POS_Settings;
use WPMailSMTP\Vendor\phpseclib3\Math\PrimeField\Integer;

/**
 * Class pos_vendor_api
 *
 * @package VitePos\Api\V1
 */
class Pos_Table_Api extends API_Base {

	/**
	 * The set api base is generated by appsbd
	 *
	 * @return mixed|string
	 */
	public function set_api_base() {
		return 'table';
	}

	/**
	 * The routes is generated by appsbd
	 *
	 * @return mixed|void
	 */
	public function routes() {
		$this->register_rest_route( 'POST', 'list', array( $this, 'table_list' ) );
		$this->register_rest_route( 'POST', 'create', array( $this, 'create_table' ) );
		$this->register_rest_route( 'POST', 'update', array( $this, 'update_table' ) );
		$this->register_rest_route( 'POST', 'delete-table', array( $this, 'delete_table' ) );
		$this->register_rest_route( 'GET', 'details/(?P<id>\d+)', array( $this, 'table_details' ) );
	}
	/**
	 * The set route permission is generated by appsbd
	 *
	 * @param \VitePos\Libs\any $route Its string.
	 *
	 * @return bool
	 */
	public function set_route_permission( $route ) {
		switch ( $route ) {
			case 'list':
				return current_user_can( 'table-menu' ) || POS_Settings::is_pos_user();
			case 'create':
				return current_user_can( 'table-add' );
			case 'update':
				return current_user_can( 'table-edit' );
			case 'delete-table':
				return current_user_can( 'table-delete' );
			default:
				return POS_Settings::is_pos_user();
		}

		return parent::set_route_permission( $route );
	}

	/**
	 * The vendor list is generated by appsbd
	 *
	 * @return API_Data_Response
	 */
	public function table_list() {
		$response_data = new API_Data_Response();
		$mainobj       = new Mapbd_Pos_Table();
		if ( ! empty( $this->payload['limit'] ) && intval( $this->payload['limit'] ) <= 0 ) {
			$response_data->limit = 0;
			$mainobj->status( 'A' );
		} else {
			$response_data->limit = $this->get_payload( 'limit', 20 );
		}
		$response_data->page = $this->get_payload( 'page', 1 );
		$src_props           = $this->get_payload( 'src_by', array() );
		$sort_props          = $this->get_payload( 'sort_by', array() );
		$all_props           = 'title,id';
		$mainobj->set_search_by_param( $src_props, $all_props );
		$mainobj->set_sort_by_param( $sort_props );
		if ( $response_data->set_total_records( $mainobj->count_all() ) ) {
			$response_data->rowdata = $mainobj->select_all_grid_data( '', '', '', $response_data->limit, $response_data->limit_start() );
			foreach ( $response_data->rowdata as &$data ) {
				$data->image = intval( $data->image );
				if ( ! empty( $data->image ) ) {
					$data->image = wp_get_attachment_url( $data->image );
				}
			}
		}
		return $response_data;
	}

	/**
	 * The vendor details is generated by appsbd
	 *
	 * @param any $data Its string.
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function table_details( $data ) {
		if ( ! empty( $data['id'] ) ) {
			$id       = intval( $data['id'] );
			$main_obj = new Mapbd_Pos_Table();
			$main_obj->id( $id );
			if ( $main_obj->Select() ) {
				if ( isset( $main_obj->assigned_waiters ) ) {
					$main_obj->assigned_waiters = explode( '|', $main_obj->assigned_waiters );
				} else {
					$main_obj->assigned_waiters = array();
				}
				$main_obj->image = intval( $main_obj->image );
				if ( ! empty( $main_obj->image ) ) {
					$main_obj->image = wp_get_attachment_url( $main_obj->image );
				}
				$this->set_response( true, 'data found', $main_obj );
				return $this->response;
			} else {
				$this->set_response( false, 'data not found or invalid param' );
				return $this->response;
			}
		} else {
			$this->set_response( false, 'data not found or invalid param' );
			return $this->response;
		}

	}

	/**
	 * The create vendor is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function create_table() {
		$table_obj = new Mapbd_Pos_Table();
		$table_obj->set_from_array( $this->payload, true, false );
		$table_obj->added_by( $this->get_current_user_id() );
		$table_obj->outlet_id( $this->get_outlet_id() );
		if ( isset($this->payload['assigned_waiters']) && is_array( $this->payload['assigned_waiters'] ) ) {
			$table_obj->assigned_waiters( implode( '|', $this->payload['assigned_waiters'] ) );
		}
		if ( $table_obj->is_valid_form( true ) ) {
			if ( $table_obj->save() ) {
				$this->save_update_image( $table_obj->id );
				$this->add_info( 'successfully saved' );
				$this->set_response( true );
			} else {
				$this->set_response( false, \Appsbd\V1\Core\Kernel::get_msg_for_api() );
			}
		}
		return $this->response->get_response();
	}

	/**
	 * The save update image is generated by appsbd
	 *
	 * @param mixed $table_id Its table id param.
	 */
	public function save_update_image( $table_id ) {
		$files = AppInput::get_uploaded_files();
		if ( ! empty( $files['image']['name'] ) && empty( $files['image']['error'] ) ) {
			$attach_id = vitepos_insert_media_attachment( $files['image']['tmp_name'], $files['image']['name'], $files['image']['type'], $table_id );
			if ( ! empty( $attach_id ) ) {
				$obj = new Mapbd_Pos_Table();
				$obj->image( $attach_id );
				$obj->set_where_update( 'id', $table_id );
				return $obj->update( false, false );
			}
		}
		return false;
	}
	/**
	 * The update status is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function update_table() {
		$old_object = Mapbd_Pos_Table::find_by( 'id', $this->payload['id'] );
		$is_updated = false;
		if ( $old_object ) {
			$table_obj = new Mapbd_Pos_Table();
			if ( $table_obj->set_from_array( $this->payload, false ) ) {
				if ( is_array( $this->payload['assigned_waiters'] ) ) {
					$table_obj->assigned_waiters( implode( '|', $this->payload['assigned_waiters'] ) );
				} else {
					$table_obj->assigned_waiters( '' );
				}
				$table_obj->unset_all_excepts( 'title,status,des,is_reserved,is_mergeable,type,seat_cap,outlet_id,assigned_waiters' );
				if ( $table_obj->is_set_data_for_save_update() ) {
					$table_obj->set_where_update( 'id', $this->payload['id'] );
					if ( $table_obj->update() ) {
						$is_updated  = true;
						$updated_obj = Mapbd_Pos_Table::find_by( 'id', $table_obj->id );
					}
				}
				if ( $this->save_update_image( $old_object->id ) ) {
					$is_updated = true;
				}
				if ( $is_updated ) {
					$this->response->set_response( true, 'Successfully updated', $updated_obj );
				} else {
					$this->response->set_response( false, \Appsbd\V1\Core\Kernel::get_msg_for_api(), $table_obj );
				}
				return $this->response->get_response();
			}
		}
		$this->response->set_response( false, \Appsbd\V1\Core\Kernel::get_msg_for_api() );
		return $this->response->get_response();
	}

	/**
	 * The delete vendor is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function delete_table() {
		if ( ! empty( $this->payload['id'] ) ) {
			$id = intval( $this->payload['id'] );
			$mr = new Mapbd_Pos_Table();
			$mr->id( $id );
			if ( $mr->Select() ) {
				if ( Mapbd_Pos_Table::delete_by_id( $id ) ) {
					$this->add_info( 'Table deleted successfully' );
					$this->response->set_response( true );

				} else {
					$this->add_error( 'Table delete failed' );
					$this->response->set_response( false );
				}
			} else {
				$this->add_error( 'No Table found with this param' );
				$this->response->set_response( false );
			}
			return $this->response->get_response();
		}
	}
}
