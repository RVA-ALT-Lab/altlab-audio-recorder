<?php 
/*
Plugin Name: ALT Lab Audio Recorder
Plugin URI:  https://github.com/
Description: For stuff that's magical
Version:     1.0
Author:      ALT Lab
Author URI:  http://altlab.vcu.edu
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


add_action('wp_enqueue_scripts', 'prefix_load_scripts');

function prefix_load_scripts() {                             
    wp_enqueue_script('audio-recorder-install', plugin_dir_url( __FILE__) . 'js/install.js', '', '1', true); 
    wp_enqueue_script('audio-recorder-polyfill', plugin_dir_url( __FILE__) . 'js/mediaDevices-getUserMedia-polyfill.js', 'audio-recorder-install', '1', true); 
    wp_enqueue_script('audio-recorder', plugin_dir_url( __FILE__) . 'js/app.js', 'audio-recorder-install', '1', true); 
    wp_enqueue_style( 'audio-recorder-css', plugin_dir_url( __FILE__) . 'css/audio-recorder-main.css');
}



//add custom field to gravity forms 

add_action('plugins_loaded', 'load_after_gforms');

function load_after_gforms(){
	class GF_Field_Audio extends GF_Field {
 
    	public $type = 'audio';

    	public function get_form_editor_field_title() {
	    	return esc_attr__( 'Audio', 'gravityforms' );
		}

		public function get_form_editor_button() {
		    return array(
		        'group' => 'advanced_fields',
		        'text'  => $this->get_form_editor_field_title(),
		    );
		}

		function get_form_editor_field_settings() {
		    return array(
		        'conditional_logic_field_setting',
		        'prepopulate_field_setting',
		        'error_message_setting',
		        'label_setting',
		        'label_placement_setting',
		        'admin_label_setting',
		        'size_setting',
		        'rules_setting',
		        'visibility_setting',
		        'duplicate_setting',
		        'default_value_setting',
		        'placeholder_setting',
		        'description_setting',
		        'css_class_setting',
		    );
		}

		public function get_field_input( $form, $value = '', $entry = null ) {
		    $form_id         = $form['id'];
		    $id              = (int) $this->id;
		 
		 
		    $disabled_text         = $this->is_form_editor() ? 'disabled="disabled"' : '';
		    $logic_event           = version_compare( GFForms::$version, '2.4.1', '<' ) ? $this->get_conditional_logic_event( 'change' ) : '';
		    $placeholder_attribute = $this->get_field_placeholder_attribute();		   
		 
		    $input = '<section class="main-controls"><canvas class="visualizer" height="60px"></canvas><div id="buttons"><button class="record">Record</button><button class="stop">Stop</button></div></section><section class="sound-clips"></section>';
		 
		    return $input;
		}

	}

	GF_Fields::register( new GF_Field_Audio() );

}

add_filter( 'gform_field_css_class', 'gf_custom_class', 10, 3 );
function gf_custom_class( $classes, $field, $form ) {
    if ( $field->type == 'audio' ) {
        $classes .= ' audio-recorder';
    }
    return $classes;
}



