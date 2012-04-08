<?php
/*
Plugin Name: Give Online
Description: Give Online Widget
Version: 0.1.0
Author: Eli Perelman
Author URI: http://eliperelman.com
*/

class giveonline_widget extends WP_Widget {

	// Constructor

	function giveonline_widget() {
		// Widget Settings
		$widget_ops = array(
			'classname' => 'giveonline_widget',
			'description' => 'Allow visitors to give online'
		);

		// Widget Control Settings
		$control_ops = array(
			'id_base' => 'giveonline_widget'
		);

		// Create the widget
		$this->WP_Widget('giveonline_widget', 'Give Online', $widget_ops, $control_ops);
	}

	// Extract Args
	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('Give Online', $instance['title']); // the widget title
		$paypalid = $instance['paypalid'];
		$defaultamount = $instance['defaultamount'];
		$textcontent = $instance['textcontent'];

		/*
		$soupnumber = $instance['soup_number']; // the number of posts to show
		$posttype = $instance['post_type']; // the type of posts to show
		$shownews = isset($instance['show_newsletter']) ? $instance['show_newsletter'] : false ; // whether or not to show the newsletter link
		$newsletterurl = $instance['newsletter_url']; // URL of newsletter signup
		$authorcredit = isset($instance['author_credit']) ? $instance['author_credit'] : false ; // give plugin author credit
		*/

		// Before widget
		echo $before_widget;

		// Title of widget
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		// Widget output
		?>
			<div class="give-online-container">
				<p>
					<?php echo $textcontent; ?>
				</p>
				<form method="post" action="https://www.paypal.com/cgi-bin/webscr" name="_donations">
					<input type="hidden" value="_donations" name="cmd">
					<input type="hidden" value="<?php echo $paypalid; ?>" name="business">
					<input type="hidden" value="General Fund" name="item_name">
					<input type="hidden" value="USD" name="currency_code">
					$ <input type="text" value="<?php echo $defaultamount; ?>" size="10" class="amount" name="amount">
					<input type="submit" value="Give" name="submit" class="button">
				</form>
				<div class="branding">
					<a target="_blank" href="https://www.paypal.com/us/mrb/pal=WC5EWXNR7VAXS">
						<img title="Powered by PayPal"
							alt="PayPal"
							src="wp-content/plugins/give-online/images/paypal_with_methods.png" />
					</a>
				</div>
			</div>
		<?php


		// After widget
		echo $after_widget;
	}

	// Update Settings
	function update($new_instance, $old_instance) {
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['paypalid'] = strip_tags($new_instance['paypalid']);
		$instance['defaultamount'] = strip_tags($new_instance['defaultamount']);
		$instance['textcontent'] = $new_instance['textcontent'];

		// $instance['title'] = strip_tags($new_instance['title']);
		// $instance['soup_number'] = strip_tags($new_instance['soup_number']);
		// $instance['post_type'] = $new_instance['post_type'];
		// $instance['show_newsletter'] = $new_instance['show_newsletter'];
		// $instance['newsletter_url'] = strip_tags($new_instance['newsletter_url'],'<a>');
		// $instance['author_credit'] = $new_instance['author_credit'];

		return $instance;
	}

	// Widget Control Panel
	function form($instance) {
		$defaults = array(
			'title' => 'Give Online',
			'paypalid' => '',
			'defaultamount' => 25.00,
			'textcontent' => ''
		);

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
				name="<?php echo $this->get_field_name('title'); ?>'"
				type="text"
				value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('paypalid'); ?>">
				<?php _e('PayPal User ID to receive payments'); ?>
			</label>
			<input class="widefat"
				id="<?php echo $this->get_field_id('paypalid'); ?>"
				name="<?php echo $this->get_field_name('paypalid'); ?>"
				type="text"
				placeholder="john@example.com"
				value="<?php echo $instance['paypalid']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('defaultamount'); ?>">
				<?php _e('Default donation amount'); ?>
			</label>
			<input class="widefat"
				id="<?php echo $this->get_field_id('defaultamount'); ?>"
				name="<?php echo $this->get_field_name('defaultamount'); ?>"
				type="text"
				value="<?php echo $instance['defaultamount']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('textcontent'); ?>">
				<?php _e('Content to place above payment form'); ?>
			</label>
			<textarea class="widefat"
				id="<?php echo $this->get_field_id('textcontent'); ?>"
				name="<?php echo $this->get_field_name('textcontent'); ?>"><?php echo $instance['textcontent']; ?></textarea>
		</p>
        <?php
    }

}

// End class giveonline_widget

add_action('widgets_init', create_function('', 'return register_widget("giveonline_widget");'));
?>