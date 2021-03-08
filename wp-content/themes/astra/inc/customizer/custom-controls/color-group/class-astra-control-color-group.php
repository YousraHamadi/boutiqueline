<?php
/**
 * Customizer Control: Color Group.
 *
 * Creates a Color Group control.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       3.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Toggle control.
 */
class Astra_Control_Color_Group extends WP_Customize_Control {

	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'ast-color-group';

	/**
	 * The text to display.
	 *
	 * @access public
	 * @var string
	 */
	public $text = '';

	/**
	 * The control name.
	 *
	 * @access public
	 * @var string
	 */
	public $name = '';

	/**
	 * The control suffix.
	 *
	 * @access public
	 * @var string
	 */
	public $suffix = '';

	/**
	 * The fields for group.
	 *
	 * @access public
	 * @var string
	 */
	public $ast_fields = '';

	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $help = '';

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @see WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();

		$this->json['label'] = esc_html( $this->label );
		$this->json['text']  = $this->text;
		$this->json['help']  = $this->help;
		$this->json['name']  = $this->name;

		$config = array();

		if ( isset( Astra_Customizer::$color_group_configs[ $this->name ] ) ) {
			$config = wp_list_sort( Astra_Customizer::$color_group_configs[ $this->name ], 'priority' );
		}

		$this->json['ast_fields'] = $config;
	}

	/**
	 * Render the control's content.
	 *
	 * @see WP_Customize_Control::render_content()
	 */
	protected function render_content() {}
}
