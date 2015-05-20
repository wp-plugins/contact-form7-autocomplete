<?php 
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

class TB_Autocomplete_Field{
	private $fields, $name;

	function __construct() {
		add_action( 'wpcf7_init',           array(&$this, 'tb_shortcode_add'));
		add_filter( 'wpcf7_validate_list',  array(&$this, 'tb_field_validation' ), 10, 2);
		add_filter( 'wpcf7_validate_list*', array(&$this, 'tb_filter_validation' ), 10, 2);
		add_filter( 'wpcf7_messages',       array(&$this, 'tb_messages'));
		add_action( 'admin_init',           array(&$this, 'tb_add_tag_generator' ), 15);		
		add_action( 'wp_footer',            array(&$this, 'tb_values'));
	}

	function tb_values(){
		$source = array_filter( $this->fields ); 
	?>
		<script type="text/javascript">

		    jQuery(document).ready(function($) {
		        jQuery("[name='<?php echo $this->name; ?>']").autocomplete({
		            source: <?php echo json_encode( $source ); ?>
		        })
		    });

		</script>
	<?php }


	function tb_shortcode_add() {
		wpcf7_add_shortcode(array( 'autocomplete', 'autocomplete*'),
		array( &$this, 'shortcode_handler' ), true);
	}	

function shortcode_handler( $tag ) {
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
        $this->fields   = $tag->values;
        $this->name     = $tag->name;
		$html = sprintf(
			'<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
			sanitize_html_class( $tag->name ), $atts, $validation_error );
		return $html;
	}	

	function tb_filter_validation( $result, $tag ) {
		$tag = new WPCF7_Shortcode( $tag );
		$name = $tag->name;
		$value = isset( $_POST[$name] )
			? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) )
			: '';

		if ( '' == $value ) {
			$result['valid'] = false;
			$result['reason'][$name] = wpcf7_get_message( 'invalid_required' );
		}

		if ( isset( $result['reason'][$name] ) && $id = $tag->get_id_option() ) {
			$result['idref'][$name] = $id;
		}

        if ( !in_array( $value, $tag->values ) ) {
            $result['valid'] = false;
            $result['reason'][$name] = wpcf7_get_message( 'invalid_required' );
        }
		return $result;
	}	

	function tb_messages( $messages ) {
		return array_merge( $messages, array(
			'invalid_value' => array(
				'description' => __( "The value selected is invalid.", 'contact-form-7' ),
				'default' => __( 'Autocomplete value seems invalid.', 'contact-form-7' )
			),

			'invalid_required' => array(
			    'description' => 'You need to give this a value.',
			    'default' => 'You need to give this a value.'
			) ) );
	}	

	function tb_add_tag_generator() {
		if ( ! function_exists( 'wpcf7_add_tag_generator' ) )
			return;

		wpcf7_add_tag_generator( 'autocomplete', __( 'Autocomplete', 'contact-form-7' ),
			'tb-tg-pane-autocomplete', array( &$this, 'tg_pane' ) );
	}	

	public function tg_pane( $contact_form ) {
		$this->tg_pane_text();
	}	
function tg_pane_text() { ?>
	<div id="tb-tg-pane-autocomplete" class="hidden">
		<form action="">
			<table>
				<tr>
					<td><input type="checkbox" name="required" />&nbsp;<?php echo esc_html( __( 'Required field?', 'contact-form-7' ) ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?><br /><input type="text" name="name" class="tg-name oneline" /></td>
					<td></td>
				</tr>
			</table>

			<table>
				<tr>
					<td>
						<code>id</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
						<input type="text" name="id" class="idvalue oneline option" />
					</td>

					<td>
						<code>class</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
						<input type="text" name="class" class="classvalue oneline option" />
					</td>
				</tr>

				<tr>
					<td>
						<code>size</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
						<input type="number" name="size" class="numeric oneline option" min="1" />
					</td>
					<td>
						<code>placeholder</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
						<input type="text" name="placeholder" class="oneline option" />
					</td>
				</tr>

				<tr>
					<td>
						<code>values</code><br />
						<textarea name="values"></textarea><br />
						<span style="font-size: smaller">* One choice per line.</span>
					</td>
					<td></td>
				</tr>
			</table>

			<div class="tg-tag">
				<?php echo esc_html( __( "Copy this code and paste it into the form left.", 'contact-form-7' ) ); ?><br /><input type="text" name="autocomplete" class="tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.select()" />
			</div>

			<div class="tg-mail-tag">
				<?php echo esc_html( __( "And, put this code into the Mail fields below.", 'contact-form-7' ) ); ?><br /><input type="text" class="mail-tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.select()" />
			</div>
		</form>
	</div>
	<?php
	}	
}