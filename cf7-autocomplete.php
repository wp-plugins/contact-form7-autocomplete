<?php
/*
Plugin Name: Contact Form7: Autocomplete
Plugin URI: http://wordpress.org/plugins/cf7-autocomplete-autocomplete/
Description: This is a plugin add field Autocomplete for Contact Form 7
Author: Tran Bang
Version: 1.2.1
Author URI: http://tranbang.net
*/

if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define('TB_AUTOCOMPLETE_VER', '1.2.1');	
define('TB_AUTOCOMPLETE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TB_AUTOCOMPLETE_PLUGIN_DIR', plugin_dir_path(__FILE__));

class TB_Autocomplete{	
	private $fields, $names;

	public function __construct() {
		add_action('init', array($this, 'lib_load'), 10);
		add_action('wpcf7_enqueue_scripts', array(__CLASS__, 'load_js'));
		add_action('wpcf7_enqueue_styles', array(__CLASS__, 'load_css'));		
		register_activation_hook( __FILE__, array( 'TB_Autocomplete', 'activation_hook' ) );
		register_activation_hook( __FILE__, array( 'TB_Autocomplete', 'deactivation_hook' ) );		
		add_action( 'wpcf7_init', array($this, 'tb_shortcode_add'));
		add_filter( 'wpcf7_validate_autocomplete',  array($this, 'tb_filter_validation' ), 10, 2);
		add_filter( 'wpcf7_validate_autocomplete*', array($this, 'tb_filter_validation' ), 10, 2);	
		add_action( 'wp_footer', array($this, 'tb_values'));	
		add_filter( 'wpcf7_messages', array($this, 'tb_messages'));		
	}

	 public function tb_values(){		 	
		if(!empty($this->fields)) {
		$source = array_filter($this->fields); 	
	?>
		<script type="text/javascript">
		    jQuery(document).ready(function($) {						    	
				<?php foreach ($this->fields as $key => $value) { ?>						
			        $("[name='<?php echo $key ?>']").autocomplete({
			            source: <?php echo json_encode($value); ?>
			        });						
				<?php } ?>		  		    			    			    	
		    });
		</script>
	<?php }	
	}

	public function lib_load(){
		require_once TB_AUTOCOMPLETE_PLUGIN_DIR.'cf7-autocomplete-field.php';		
		new TB_Autocomplete_Field;	
	}

	public static function load_js(){		
		wp_enqueue_script('jquery-ui-autocomplete', array('jquery','jquery-ui-core'));
	}

	public static function load_css(){				
		wp_register_style('tb-jquery-ui-theme', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/cupertino/jquery-ui.min.css?ver=1.10.3');				
		wp_enqueue_style('tb-jquery-ui-theme');
	}	

	public function tb_shortcode_add() {		
		wpcf7_add_form_tag(array( 'autocomplete', 'autocomplete*'), array($this, 'shortcode_handler'), true);
	}	

	public function activation_hook() {

	}

	public function deactivation_hook() {
		
	}	

	public function shortcode_handler( $tag ) {				
		$tag = new WPCF7_Shortcode( $tag );
		if ( empty( $tag->name ) )
			return '';

		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type, 'wpcf7-text' );

		if ( $validation_error )
			$class .= ' wpcf7-not-valid';

		$atts = array();
		$atts['size']		= $tag->get_size_option( '40' );
		$atts['maxlength']	= $tag->get_maxlength_option();
		$atts['class']		= $tag->get_class_option( $class );
		$atts['id']			= $tag->get_id_option();
		$atts['tabindex']	= $tag->get_option( 'tabindex', 'int', true );

		if ( $tag->has_option( 'readonly' ) )
			$atts['readonly'] = 'readonly';

		if ( $tag->is_required() )
			$atts['aria-required'] = 'true';
		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
		if ( $tag->has_option( 'placeholder' ) )
			$atts['placeholder'] = $tag->get_option( 'placeholder', '[-0-9a-zA-Z_\s]+', true );
		$atts['type']	= 'text';
		$atts['name']	= $tag->name;
		$atts = wpcf7_format_atts( $atts );
        $this->fields[$tag->name]   = $tag->values;
        $this->names[]  = $tag->name;                  

		$html = sprintf(
			'<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
			sanitize_html_class( $tag->name ), $atts, $validation_error );
		return $html;
	}	

	public function tb_filter_validation( $result, $tag ) {		
		$tag = new WPCF7_Shortcode( $tag );
		$name = $tag->name;

		$value = isset( $_POST[$name] )
			? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) )
			: '';

		if ( $tag->is_required() && '' == $value) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}
		
		return $result;
	}	

	public function tb_messages( $messages ) {
		return array_merge( $messages, array(
			'invalid_value' => array(
				'description' => __( "The value selected is invalid.", 'contact-form-7' ),
				'default' => __( 'Autocomplete value seems invalid.', 'contact-form-7' )
			),

			'invalid_required' => array(
			    'description' => 'Please fill in the required field.',
			    'default' => 'Please fill in the required field.'
			) ) );
	}		
}


new TB_Autocomplete;
