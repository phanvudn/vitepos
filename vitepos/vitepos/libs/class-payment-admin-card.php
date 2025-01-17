<?php
/**
 * Its payment method base.
 *
 * @since: 21/09/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos\Libs
 */

namespace VitePos\Libs;

/**
 * Class Payment_Admin_card
 *
 * @package VitePos\Libs
 */
class Payment_Admin_Card {
	/**
	 * Its property name
	 *
	 * @var String
	 */
	public $name;
	/**
	 * Its property title
	 *
	 * @var mixed|string
	 */
	public $title;

	/**
	 * Its property settings
	 *
	 * @var \stdClass
	 */
	public $fields = array();
	/**
	 * Its property msgs
	 *
	 * @var array
	 */
	public $msgs = null;

	/**
	 * Payment_Admin_card constructor.
	 *
	 * @param string $name Its name param.
	 * @param string $title Its title param.
	 */
	public function __construct( $name = '', $title = '' ) {
		$this->name  = $name;
		$this->title = $title;
		$this->msgs=new \stdClass();
		$this->msgs->error=[];
		$this->msgs->info=[];
		$this->msgs->debug=[];
		$this->msgs->warning=[];

	}


	/**
	 * The add field is generated by appsbd
	 *
	 * @param mixed  $title Its title param.
	 * @param mixed  $name Its name param.
	 * @param false  $is_required Its is_required param.
	 * @param string $type Its type param.
	 * @param array  $options Its options param.
	 */
	public function add_settings_field( $title, $name, $is_required = false, $type = 'T', $options = array() ) {
		$obj              = new \stdClass();
		$obj->label       = $title;
		$obj->id          = $name;
		$obj->is_required = $is_required ? 'Y' : 'N';
		$obj->type        = $type;
		$obj->options     = $options;
		$this->fields[]   = $obj;
	}
	public function add_error( $msg) {
		$this->msgs->error[]   = $msg;
	}
	public function add_info( $msg) {
		$this->msgs->info[]   = $msg;
	}
	public function add_warning( $msg) {
		$this->msgs->warning[]   = $msg;
	}
	public function add_description( $msg,$css_class='') {
		$obj              = new \stdClass();
		$obj->label       = $css_class;
		$obj->type        = 'I';
		$obj->des     = $msg;
		$this->fields[]   = $obj;
	}
}
