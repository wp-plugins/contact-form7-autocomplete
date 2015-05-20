<?php
/*
Plugin Name: Contact Form7: Autocomplete
Plugin URI: http://wordpress.org/plugins/cf7-autocomplete-autocomplete/
Description: This is a plugin add field Autocomplete for Contact Form 7
Author: Tran Bang
Version: 1.0.0
Author URI: http://tranbang.net
*/

if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define('TB_AUTOCOMPLETE_VER', '1.0.0');	
define('TB_AUTOCOMPLETE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TB_AUTOCOMPLETE_PLUGIN_DIR', plugin_dir_path(__FILE__));

class TB_Autocomplete{	

	function __construct() {
		add_action('init', array($this, 'lib_load'), 10);
		add_action('wpcf7_enqueue_scripts', array(__CLASS__, 'load_js'));
		add_action('wpcf7_enqueue_styles', array(__CLASS__, 'load_css'));		
		register_activation_hook( __FILE__, array( 'TB_Autocomplete', 'activation_hook' ) );
		register_activation_hook( __FILE__, array( 'TB_Autocomplete', 'deactivation_hook' ) );		
	}

	function lib_load(){
		require_once dirname(__FILE__) . '/cf7-autocomplete-field.php';	
		$tb_autocomplete_field = new TB_Autocomplete_Field;	
	}

	public static function load_js(){		
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-autocomplete');
	}

	public static function load_css(){		
		wp_register_style('tb-jquery-ui-structure', TB_AUTOCOMPLETE_PLUGIN_URL.'css/jquery-ui.structure.min.css');
		wp_register_style('tb-jquery-ui-theme', TB_AUTOCOMPLETE_PLUGIN_URL.'css/jquery-ui.theme.min.css');

		
		wp_enqueue_style('tb-jquery-ui-structure');
		wp_enqueue_style('tb-jquery-ui-theme');
	}	

	public function activation_hook() {

	}

	public function deactivation_hook() {
		
	}	
}


$tb_autocomplete = new TB_Autocomplete;