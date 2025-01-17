<?php
/**
 * Its for ajax data response
 *
 * @since: 21/09/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package Appsbd\V1\libs
 */

namespace Appsbd\V1\libs;

use Appsbd\V1\Core\BaseModel;

/**
 * Class Ajax Data Response
 *
 * @package Appsbd\V1\libs;
 */
if ( ! class_exists( __NAMESPACE__ . '\Ajax_Data_Response' ) ) {
	/**
	 * Class Ajax_Data_Response
	 *
	 * @package Appsbd\V1\libs
	 */
	class Ajax_Data_Response {
		/**
		 * Its property page
		 *
		 * @var mixed|string
		 */
		public $page;
		/**
		 * Its property src_item
		 *
		 * @var string
		 */
		public $src_item;
		/**
		 * Its property src_text
		 *
		 * @var string
		 */
		public $src_text;
		/**
		 * Its property multiparam
		 *
		 * @var array
		 */
		public $multiparam;

		/**
		 * Its property limit
		 *
		 * @var mixed|string
		 */
		public $limit;
		/**
		 * Its property src_by
		 *
		 * @var mixed|string
		 */
		public $src_by = array();
		/**
		 * Its property sort_by
		 *
		 * @var array
		 */
		public $sort_by = array();
		/**
		 * Its property is_download_csv
		 *
		 * @var bool
		 */
		public $is_download_csv = false;
		/**
		 * Its property csv_cols
		 *
		 * @var string
		 */
		private $csv_cols = '';
		/**
		 * Its property final_response
		 *
		 * @var \stdClass
		 */
		public $final_response;

		/**
		 * Ajax_Data_Response constructor.
		 */
		public function __construct() {
			$this->is_download_csv = ( 'Y' == AppInput::request_value( 'is_dl_csv', 'N' ) );
			if ( $this->is_download_csv ) {
				$this->csv_cols = AppInput::request_value( 'csv_cols', '' );
			}
			$this->page                    = intval( AppInput::post_value( 'page', 1 ) );
			$this->limit                   = intval( AppInput::post_value( 'limit', 20 ) );
			if ( $this->limit <= 0 ) {
				$this->limit = '';
			}
			$this->src_by                  = (array) AppInput::post_value( 'src_by', array() );
			$this->sort_by                 = (array) AppInput::post_value( 'sort_by', array() );
			$this->final_response          = new \stdClass();
			$this->final_response->rowdata = array();
			$this->final_response->records = 0;
			$this->final_response->limit   = 20;
			$this->final_response->page    = 1;
			$this->final_response->total   = 0;
		}

		/**
		 * The set download filename is generated by appsbd
		 *
		 * @param mixed $filename Its filename param.
		 */
		public function set_download_filename( $filename ) {

		}

		/**
		 * The set default order is generated by appsbd
		 *
		 * @param mixed  $prop its default prop.
		 * @param string $ord Its order ASC or DESC.
		 */
		public function set_default_order( $prop, $ord = 'ASC' ) {
			if ( empty( $this->sort_by ) ) {
				$this->sort_by = array(
					array(
						'prop' => $prop,
						'ord' => $ord,
					),
				);
			}
		}
		/**
		 * The limit start is generated by appsbd.
		 *
		 * @return float|int|mixed|string Its type .
		 */
		public function limit_start() {
			if ( empty( $this->limit ) || $this->limit <= 0 ) {
				return 0;
			}
			return ( $this->limit * $this->page ) - $this->limit;
		}

		/**
		 * The set total records is generated by appsbd
		 *
		 * @param int $record_counter Its record counter.
		 *
		 * @return bool Its bool parameter.
		 */
		public function set_grid_records( $record_counter ) {

			$this->final_response->records = (int) $record_counter;
			if ( $this->final_response->records > 0 ) {
				if ( empty( $this->limit ) ) {
					$this->final_response->total = $this->final_response->records > 0 ? 1 : 0;
					return;
				}
				$this->final_response->total = ceil( $this->final_response->records / $this->limit );

				return true;
			}

			return false;
		}

		/**
		 * The add additional property is generated by appsbd
		 *
		 * @param mixed $prop Its prop param.
		 * @param mixed $value Its value param.
		 */
		public function add_additional_property( $prop, $value ) {
			$this->final_response->{$prop} = $value;
		}


		/**
		 * The set grid data is generated by appsbd
		 *
		 * @param array $data Its data param.
		 */
		public function set_grid_data( $data = array() ) {
			$this->final_response->rowdata = $data;
		}

		/**
		 * The display grid response is generated by appsbd
		 */
		public function display_grid_response() {
			$this->final_response->limit = $this->limit;
			$this->final_response->page  = $this->page;

			wp_send_json( $this->final_response );
		}

	}
}


