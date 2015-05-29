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

	public function tg_pane( $contact_form ) {
		$this->tg_pane_text();
	}	
	public function tg_pane_text() { ?>
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