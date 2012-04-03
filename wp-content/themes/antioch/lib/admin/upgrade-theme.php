<?php

function ct_upgrade_menu() {
    add_menu_page(__('Upgrade Theme', 'churchthemes'), __('Upgrade Theme', 'churchthemes'), 'read', 'upgrade-theme', 'ct_upgrade_page');
}
function ct_upgrade_page() {
	$theme_data = get_theme_data(get_stylesheet_uri());
	$theme_tmp_name = $theme_data['Name'];
	$theme_name = str_replace(' (Free Edition)', '', $theme_tmp_name);
	$theme_tmp_slug = str_replace(' ', '-', $theme_name);
	$theme_slug = strtolower($theme_tmp_slug);
	echo '
		<div class="wrap upgrade-page">
			<h2>Upgrade Theme</h2>
			<p><em>We hope you\'re enjoying this Free Edition of '.$theme_name.'!</em></p>
			<p>Our free themes are great for test driving a theme\'s design and functionality to see how it can work for your church.</p>
			<p>Once you\'re convinced, you can upgrade to the <strong>Standard Edition</strong> or <strong>Developer Edition</strong> of '.$theme_name.' and take advantage of some amazing features we made <em>just for churches.</em></p>
			<h3>After upgrading you\'ll be able to...</h3>
			<ul>
				<li>Receive free and automatic Theme Updates from within the WordPress Admin</li>
				<li>Accept Tithes and Offerings online via <a href="https://www.paypal.com/us/mrb/pal=WC5EWXNR7VAXS" target="_blank">PayPal</a> or <a href="https://www.easytithe.com/signup/?r=livi1941" target="_blank">EasyTithe</a></li>
				<li>Publish and manage Sermon Media with ease</li>
				<li>Directly embed YouTube or Vimeo videos of your Sermons</li>
				<li>Host a fully searchable Sermon Library with built-in search filters</li>
				<li>Publish your Sermons as an audio Podcast in iTunes</li>
				<li>Create directories of People (such as Staff, Pastors, Small Group Leaders, etc.)</li>
				<li>List and manage multiple Church Locations and Services</li>
				<li>Easily display Tweets in any Sidebar area</li>
				<li>Get all the help you need with <em>lifetime access</em> to ChurchThemes Support!</li>
			</ul>
			<h4>No contracts, monthly payments or hidden fees. Ever.</h4>
			<a href="http://churchthemes.net/themes/'.$theme_slug.'/#buy?utm_source=free-edition&utm_medium=upgrade-button&utm_content=upgrade-now&utm_campaign='.$theme_slug.'" class="button-primary" target="_blank">Upgrade Now</a>
		</div>';
}
add_action('admin_menu', 'ct_upgrade_menu');

?>