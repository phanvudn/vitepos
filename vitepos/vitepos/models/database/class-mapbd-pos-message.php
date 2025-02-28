<?php
/**
 * Pos Vendor Database Model
 *
 * @package Vitepos\Models\Database
 */

namespace Vitepos\Models\Database;

use VitePos\Core\ViteposModel;

/**
 * Class Mapbd_Pos_Message
 *
 * @properties id,name,email,contact_no,vendor_note,status,added_by
 */
class Mapbd_Pos_Message extends ViteposModel {
	/**
	 * Its property id
	 *
	 * @var int
	 */
	public $id;
	/**
	 * Its property msg
	 *
	 * @var string
	 */
	public $msg;
	/**
	 * Its property type
	 *
	 * @var string
	 */
	public $msg_type;
	/**
	 * Its property des
	 *
	 * @var string
	 */
	public $msg_panel;
	/**
	 * Its property des
	 *
	 * @var string
	 */
	public $title;
	/**
	 * Its property des
	 *
	 * @var string
	 */
	public $created_at;
	/**
	 * Its property status
	 *
	 * @var bool
	 */
	public $status;


	/**
	 * Mapbd_pos_vendor constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->set_validation();
		$this->table_name     = 'apbd_pos_message';
		$this->primary_key    = 'id';
		$this->unique_key     = array();
		$this->multi_key      = array();
		$this->auto_inc_field = array( 'id' );
		$this->app_base_name  = 'apbd-elite-pos';

	}

	/**
	 * The select is generated by appsbd
	 *
	 * @param string $select Its select param.
	 * @param false  $add_field_error Its add_field_error param.
	 *
	 * @return bool
	 */
	public function select( $select = '', $add_field_error = false ) {
		$is_selected = parent::select( $select, $add_field_error );
		if ( $is_selected ) {
			if ( ! empty( $this->options ) && is_string( $this->options ) ) {
				$this->options = unserialize( $this->options );
			}
		}

		return $is_selected;
	}
	/**
	 * The set validation is generated by appsbd
	 */
	public function set_validation() {
		$this->validations = array(
			'id'        => array(
				'Text' => 'Id',
				'Rule' => 'max_length[11]|integer',
			),
			'msg'       => array(
				'Text' => 'Message',
				'Rule' => 'Required',
			),
			'title'     => array(
				'Text' => 'Title',
				'Rule' => 'Required|max_length[255]',
			),
			'msg_type'  => array(
				'Text' => 'Message Type',
				'Rule' => 'max_length[1]',
			),
			'msg_panel' => array(
				'Text' => 'User Role',
				'Rule' => 'max_length[1]',
			),
			'status'    => array(
				'Text' => 'Status',
				'Rule' => 'max_length[1]',
			),
		);
	}

	/**
	 * The get property raw options is generated by appsbd
	 *
	 * @param \Appsbd\V1\Core\any $property Its string.
	 * @param false               $is_with_select Its bool.
	 *
	 * @return array|string[]
	 */
	public function get_property_raw_options( $property, $is_with_select = false ) {
		$return_obj = array();
		switch ( $property ) {
			case 'msg_panel':
				$return_obj = array(
					'A' => 'All',
					'C' => 'Cashier',
					'K' => 'Kitchen',
					'W' => 'Waiter',
				);
				break;
			case 'msg_type':
				$return_obj = array(
					'D' => 'Deny',
					'M' => 'Message',
				);
				break;
			case 'status':
				$return_obj = array(
					'A' => 'Active',
					'I' => 'Inactive',
				);
				break;
			default:
		}
		if ( $is_with_select ) {
			return array_merge( array( '' => 'Select' ), $return_obj );
		}

		return $return_obj;

	}

	/**
	 * The create db table is generated by appsbd
	 */
	public static function create_db_table() {
		$this_obj = new static();
		$table    = $this_obj->db->prefix . $this_obj->table_name;
		if ( $this_obj->db->get_var( "show tables like '{$table}'" ) != $table ) {
			$sql = "CREATE TABLE `{$table}` (
					  	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  						`msg` text NOT NULL,
  						`msg_type` char(1) NOT NULL COMMENT 'radio(D=Deny,M=Message)',
  						`msg_panel` char(1) NOT NULL DEFAULT 'A' COMMENT 'radio(A=All,C=Cashier,K=Kitchen,W=Waiter)',
  						`status` char(1) NOT NULL DEFAULT 'A' COMMENT 'radio(A=Active,I=Inactive)',
  						`title` char(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  						`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  						PRIMARY KEY (`id`)
					)";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
	}

	/**
	 * The delete by id is generated by appsbd
	 *
	 * @param any $id Its vendor id param.
	 *
	 * @return bool
	 */
	public static function delete_by_id( $id ) {
		return parent::delete_by_key_value( 'id', $id );
	}
}
