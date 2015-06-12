<?php 
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

class TB_Autocomplete_Field{	

	public function __construct() {				
		add_action( 'admin_init', array($this, 'tb_add_tag_generator' ), 15);		
	}

	public function tb_add_tag_generator() {
		if ( ! function_exists( 'wpcf7_add_tag_generator' ) )
			return;

		wpcf7_add_tag_generator( 'autocomplete', __( 'Autocomplete', 'contact-form-7' ),
			'tb-tg-pane-autocomplete', array($this, 'tg_pane' ) );
	}	

	public function tg_pane($contact_form, $args = '' ) {
			$args = wp_parse_args( $args, array() );
			$type = 'autocomplete';

			$description = __( "Generate a form-tag for a group of autocomplete field.", 'contact-form-7' );
			$desc_link ="";

		?>
		<div class="control-box">
			<fieldset>
				<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

				<table class="form-table">
					<tbody>					
						<tr>
							<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
							<td>
								<fieldset>
								<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
								<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
								</fieldset>
							</td>
						</tr>					

						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
						</tr>

						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id (optional)', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
						</tr>

						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class (optional)', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>							
						</tr>

						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Placeholder (optional)', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="placeholder" class="oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>							
						</tr>

						<tr>
							<th scope="row"><?php echo esc_html( __( 'Values', 'contact-form-7' ) ); ?></th>
							<td>
								<fieldset>
								<legend class="screen-reader-text"><?php echo esc_html( __( 'Values', 'contact-form-7' ) ); ?></legend>
								<textarea name="values" class="values" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"></textarea><br>
								<span class="description"><?php echo esc_html( __( "* One choice per line.", 'contact-form-7' ) ); ?></span><br />
								</fieldset>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>

		<div class="insert-box">
			<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

			<div class="submitbox">
			<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
			</div>

			<br class="clear" />

			<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
		</div>
		<?php				
	}		
}