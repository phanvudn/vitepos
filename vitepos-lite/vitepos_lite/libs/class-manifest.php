<?php
/**
 * Its used for ui Manifest object
 *
 * @since: 21/09/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos\Libs
 */

namespace VitePos_Lite\Libs;

/**
 * Class Manifest
 *
 * @package VitePos\Libs
 */
#[\AllowDynamicProperties]
class Manifest {
	/**
	 * Its property name
	 *
	 * @var String
	 */
	public $name = '';
	/**
	 * Its property short_name
	 *
	 * @var String
	 */
	public $short_name = '';
	/**
	 * Its property theme_color
	 *
	 * @var String
	 */
	public $theme_color = '#fff';
	/**
	 * Its property icons
	 *
	 * @var array
	 */
	public $icons = array();
	/**
	 * Its property start_url
	 *
	 * @var String
	 */
	public $start_url = '.';


	/**
	 * Its property display
	 *
	 * @var String
	 */
	public $display = '';
	/**
	 * Its property background_color
	 *
	 * @var String
	 */
	public $background_color = '#ffffff';
	/**
	 * Its property schema
	 *
	 * @var String
	 */
	public $schema = '';
	/**
	 * Its property description
	 *
	 * @var String
	 */
	public $description = '';
	/**
	 * Its property related_applications
	 *
	 * @var array
	 */
	public $related_applications = array();

	/**
	 * Manifest constructor.
	 */
	public function __construct() {
	}

	/**
	 * The set name is generated by appsbd
	 *
	 * @param mixed $name Its the app name.
	 * @param mixed $short_name Its app short name.
	 */
	public function set_name( $name, $short_name ) {
		$this->name       = $name;
		$this->short_name = $short_name;
	}


	/**
	 * The set display is generated by appsbd
	 *
	 * @param string $display Its display param.
	 */
	public function set_display( string $display ) {
		$this->display = $display;
	}

	/**
	 * The set background color is generated by appsbd
	 *
	 * @param string $background_color Its background_color param.
	 */
	public function set_background_color( string $background_color ) {
		$this->background_color = $background_color;
	}

	/**
	 * The set schema is generated by appsbd
	 *
	 * @param string $schema Its schema param.
	 */
	public function set_schema( string $schema ) {
		$this->schema = $schema;
	}
	/**
	 * The set schema is generated by appsbd
	 *
	 * @param string $start_url Its schema param.
	 */
	public function set_start_url( string $start_url ) {
		$this->start_url = $start_url;
	}
	/**
	 * The set description is generated by appsbd
	 *
	 * @param string $description Its description param.
	 */
	public function set_description( string $description ): void {
		$this->description = $description;
	}

	/**
	 * The set theme color is generated by appsbd
	 *
	 * @param mixed $theme_color Its theme_color param.
	 */
	public function set_theme_color( $theme_color ) {
		$this->theme_color = $theme_color;
	}

	/**
	 * The set prop is generated by appsbd
	 *
	 * @param mixed $prop_name Its prop_name param.
	 * @param mixed $prop_value Its prop_value param.
	 */
	public function set_prop( $prop_name, $prop_value ) {
		$this->{$prop_name} = $prop_value;
	}
	/**
	 * The add icon is generated by appsbd
	 *
	 * @param mixed $src Its src param.
	 * @param mixed $sizes Its sizes param.
	 * @param mixed $type Its type param.
	 */
	public function add_icon( $src, $sizes, $type ) {
		$icon          = new \stdClass();
		$icon->src     = $src;
		$icon->sizes   = $sizes;
		$icon->type    = $type;
		$this->icons[] = $icon;
	}

	/**
	 * The add related applications is generated by appsbd
	 *
	 * @param mixed $platform Its platform param.
	 * @param mixed $url Its url param.
	 */
	public function add_related_applications( $platform, $url ) {
		$obj                          = new \stdClass();
		$obj->platform                = $platform;
		$obj->url                     = $url;
		$this->related_applications[] = $obj;
	}
}
