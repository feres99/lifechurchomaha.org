<?php

add_action( 'admin_init', 'ct_slide_settings_init' );
add_action( 'admin_menu', 'ct_slide_settings_add_page' );

/**
 * Init plugin options to white list our options
 */
function ct_slide_settings_init(){
	register_setting( 'ct_slide', 'ct_slide_settings', 'ct_slide_settings_validate' );
}

/**
 * Load up the menu page
 */
function ct_slide_settings_add_page() {
	add_submenu_page('edit.php?post_type=slide', __( 'Settings', 'churchthemes' ), __( 'Settings', 'churchthemes' ), 'edit_themes', 'slide-settings', 'ct_slide_settings_do_page');
}

/**
 * Create arrays for our select and radio options
 */
$select_orderby = array(
	'date' => array(
		'value' =>	'date',
		'label' => __( 'Post Date', 'churchthemes' )
	),
	'title' => array(
		'value' =>	'title',
		'label' => __( 'Title', 'churchthemes' )
	),
	'modified' => array(
		'value' => 'modified',
		'label' => __( 'Date Modified', 'churchthemes' )
	),
	'menu_order' => array(
		'value' => 'menu_order',
		'label' => __( 'Menu Order', 'churchthemes' )
	),
	'id' => array(
		'value' => 'id',
		'label' => __( 'Post ID', 'churchthemes' )
	),
	'rand' => array(
		'value' => 'rand',
		'label' => __( 'Random', 'churchthemes' )
	),
	'views' => array(
		'value' => 'views',
		'label' => __( 'View Count', 'churchthemes' )
	)
);

$radio_order = array(
	'asc' => array(
		'value' => 'ASC',
		'label' => __( 'Ascending', 'churchthemes' )
	),
	'desc' => array(
		'value' => 'DESC',
		'label' => __( 'Descending', 'churchthemes' )
	)
);

/**
 * Create the options page
 */
function ct_slide_settings_do_page() {
	global $select_orderby, $radio_order;

	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	?>
	<div class="wrap churchthemes">
		<?php screen_icon(); echo "<h2>" . __( 'Slide Settings', 'churchthemes' ) . "</h2>"; ?>

		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Options saved', 'churchthemes' ); ?></strong></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'ct_slide' ); ?>
			<?php $options = get_option( 'ct_slide_settings' ); ?>

			<table class="form-table churchthemes">
				<tr>
					<th scope="row"><?php _e( 'Order Slides By', 'churchthemes' ); ?></th>
					<td class="option">
						<select name="ct_slide_settings[orderby]">
							<?php
								$selected = $options['orderby'];
								$p = '';
								$r = '';
								foreach ( $select_orderby as $option ) {
									if(isset($option['label'])) {
										$label = $option['label'];
									} else {
										$label = null;
									}
									if ( $selected == $option['value'] ) // Make default first in list
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr( $option['value'] ) . "'>$label</option>";
									else
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr( $option['value'] ) . "'>$label</option>";
								}
								echo $p . $r;
							?>
						</select>
					</td>
					<td class="info">
						<p><?php _e( 'Select what you would like to order your Featured Slides by.', 'churchthemes' ); ?></p>
					</td>
				</tr>
				
				<tr><th scope="row"><?php _e( 'Order Direction', 'churchthemes' ); ?></th>
					<td class="option">
						<fieldset><legend class="screen-reader-text"><span><?php _e( 'Order Direction', 'churchthemes' ); ?></span></legend>
						<?php
							if ( ! isset( $checked ) )
								$checked = '';
							foreach ( $radio_order as $option ) {
								if(isset($options['order'])) {
									$radio_setting = $options['order'];
								} else {
									$radio_setting = null;
								}
								if ( '' != $radio_setting ) {
									if ( $options['order'] == $option['value'] ) {
										$checked = "checked=\"checked\"";
									} else {
										$checked = '';
									}
								}
								?>
								<label class="description">
									<input type="radio" name="ct_slide_settings[order]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php echo $checked; ?> /> <?php echo $option['label']; ?>
								</label>
								<br />
								<?php
							}
						?>
						</fieldset>
					</td>
					<td class="info">
						<p><?php _e( 'Select the order direction for the Featured Slides.', 'churchthemes' ); ?></p>
					</td>
				</tr>
				
				<tr>
					<th scope="row"><?php _e( 'Speed', 'churchthemes' ); ?></th>
					<td class="option">
						<input id="ct_slide_settings[speed]" class="regular-text" type="text" name="ct_slide_settings[speed]" placeholder="5" value="<?php esc_attr_e( $options['speed'] ); ?>" />
					</td>
					<td class="info">
						<p><?php _e( 'Enter the time (in seconds) that the slides will rotate by.', 'churchthemes' ); ?><p>
						<p><em><?php _e( 'Leave blank to rotate a slide every 5 seconds.', 'churchthemes' ); ?></em></p>
					</td>
				</tr>
				
				<tr>
					<th scope="row"><?php _e( 'Active Slide Tags', 'churchthemes' ); ?></th>
					<td class="option">
						<input id="ct_slide_settings[active_slide_tags]" class="regular-text" type="text" name="ct_slide_settings[active_slide_tags]" value="<?php esc_attr_e( $options['active_slide_tags'] ); ?>" />
					</td>
					<td class="info">
						<p><?php _e( 'Enter the', 'churchthemes'); ?> <a href="edit-tags.php?taxonomy=slide_tag&post_type=slide"><?php _e( 'slide tag slugs', 'churchthemes'); ?></a> <?php _e( '(comma separated) of the Featured Slides you would like to be active on the homepage.', 'churchthemes' ); ?><p>
						<p><em><?php _e( 'Leave blank to make all slide tags active.', 'churchthemes' ); ?></em></p>
					</td>
				</tr>
				
				<tr>
					<th scope="row"><?php _e( 'Slide Limit', 'churchthemes' ); ?></th>
					<td class="option">
						<input id="ct_slide_settings[slide_limit]" class="regular-text" type="text" name="ct_slide_settings[slide_limit]" value="<?php esc_attr_e( $options['slide_limit'] ); ?>" />
					</td>
					<td class="info">
						<p><?php _e( 'Set the maximum number of Featured Slides to be shown on the homepage.', 'churchthemes' ); ?><p>
						<p><em><?php _e( 'Leave blank for unlimited.', 'churchthemes' ); ?></em></p>
					</td>
				</tr>

			</table>
			
			<p class="submit clear">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Settings', 'churchthemes' ); ?>" />
			</p>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function ct_slide_settings_validate( $input ) {
	global $select_orderby, $radio_order;

	// Say our text option must be safe text with no HTML tags
	if ( ! isset( $input['sometext'] ) )
		$input['sometext'] = null;
	$input['sometext'] = wp_filter_nohtml_kses( $input['sometext'] );

	// Our select option must actually be in our array of select options
	if ( ! isset( $input['select1'] ) || ! array_key_exists( $input['select1'], $select_orderby ) )
		$input['select1'] = null;

	// Our radio option must actually be in our array of radio options
	if ( ! isset( $input['radio1'] ) || ! array_key_exists( $input['radio1'], $radio_order ) )
		$input['radio1'] = null;

	return $input;
}

// adapted from http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/

?>