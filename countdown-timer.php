<?php
/*
Plugin Name: T(-) Countdown
Text Domain: tminus
Domain Path: /languages
Plugin URI: http://plugins.twinpictures.de/plugins/t-minus-countdown/
Description: Display and configure multiple T(-) Countdown timers using a shortcode or sidebar widget.
Version: 2.3.3a
Author: twinpictures, baden03
Author URI: http://www.twinpictures.de/
License: GPL2
*/

//delete_option('WP_TMC_options');

class WP_TMinusCD {
	var $plugin_name = 'T(-) Countdown';
	var $version = '2.3.3a';
	var $domain = 'tminus';
	var $plguin_options_page_title = 'T(-) Countdown Options';
	var $plugin_options_menue_title = 'T(-) Countdown';
	var $plugin_options_slug = 't-countdown';

	var $options_name = 'WP_TMC_options';
	/**
	 * @var array
	 */
	var $options = array(
		'custom_css' => '',
		'rockstar' => '',
		'force_css' => '',
	);

	var $license_group = 'tminus_countdown_licenseing';
	var $license_name = 'WP_tminus_countdown_license';

	var $license_options = array(
			'tminus_event_license_key' => '',
			'tminus_event_license_status' => '',
	);

	function __construct() {
		$this->_set_options();

		// add actions
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'tminus_admin_scripts' ) );
		add_action( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_actions' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'wp_head', array( $this, 'plugin_head_inject' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'countdown_scripts' ) );
	}

	/**
	 * Callback admin_menu
	 */
	function admin_menu() {
		if ( function_exists( 'add_options_page' ) AND current_user_can( 'manage_options' ) ) {
			// add options page
			$options_page = add_options_page($this->plguin_options_page_title, $this->plugin_options_menue_title, 'manage_options', $this->plugin_options_slug, array( $this, 'options_page' ));
		}
	}

	/**
	 * Callback admin_init
	 */
	function admin_init() {
		register_setting( $this->domain, $this->options_name );
		if( is_plugin_active( 't-countdown-events/t-countdown-events.php' ) ){
			register_setting( $this->license_group, $this->license_name, array('WP_TminusEvents', 'edd_sanitize_license') );
		}
	}

	// Add link to options page from plugin list
	function plugin_actions($links) {
		$new_links = array();
		$new_links[] = '<a href="options-general.php?page='.$this->plugin_options_slug.'">' . __('Settings', $this->domain) . '</a>';
		return array_merge($new_links, $links);
	}

	//plugin header inject
	function plugin_head_inject(){
		// custom script
		echo "<script type='text/javascript'>\n";
		$plugin_url = plugins_url() .'/'. dirname( plugin_basename(__FILE__) );
		echo "var tminusnow = '".$plugin_url."/js/now.php';\n";
		echo "</script>";

		// custom css
		if( !empty( $this->options['custom_css'] ) ){
			echo "<style>\n";
			echo $this->options['custom_css'];
			echo "\n</style>\n";
		}
	}

	//load scripts on the widget admin page
	function tminus_admin_scripts($hook){
		if( $hook == 'widgets.php' ){
			//jquery datepicker
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-ui-slider' );

			wp_register_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.css', array (), '1.10.4' );
			wp_enqueue_style('jquery-ui-css');


			$plugin_url = plugins_url() .'/'. dirname( plugin_basename(__FILE__) );

			//jquery widget scripts
			wp_register_script('jquery-ui-timepicker-addon', $plugin_url.'/js/jquery-ui-timepicker-addon.min.js', array ('jquery'), '1.5.2', true);
			wp_enqueue_script('jquery-ui-timepicker-addon');

			wp_register_script('tminus-admin-script', $plugin_url.'/js/jquery.collapse.js', array ('jquery', 'jquery-ui-datepicker', 'jquery-ui-slider', 'jquery-ui-timepicker-addon'), '1.2.2', true);
			wp_enqueue_script('tminus-admin-script');

			wp_register_style('collapse-admin-css', $plugin_url.'/admin/collapse-style.css' );
			wp_enqueue_style('collapse-admin-css');
		}
	}

	//load front-end countdown scripts
	function countdown_scripts(){
		$plugin_url = plugins_url() .'/'. dirname( plugin_basename(__FILE__) );

		//lwtCountdown script
		wp_register_script('countdown-script', $plugin_url.'/js/jquery.t-countdown.js', array ('jquery'), '1.5.4', 'true');
		wp_enqueue_script('countdown-script');

		//force load styles
		if( !empty( $this->options['force_css'] ) ){
			$style = $this->options['force_css'];
			$style_file_url = plugins_url('/css/'.$style.'/style.css', __FILE__);
			wp_register_style( 'countdown-'.$style.'-css', $style_file_url, array(), '2.0');
			wp_enqueue_style( 'countdown-'.$style.'-css' );
		}

		// callback for t(-) events
		wp_localize_script( 'countdown-script', 'tCountAjax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'countdownNonce' => wp_create_nonce( 'tountajax-countdownonce-nonce' ),
		));
	}


	/**
	 * Admin options page
	 */
	function options_page() {
		$like_it_arr = array(
						__('made you feel all warm and fuzzy on the inside', $this->domain),
						__('restored your faith in humanity... even if only for a fleeting second', $this->domain),
						__('rocked your world', 'provided a positive vision of future living', $this->domain),
						__('inspired you to commit a random act of kindness', $this->domain),
						__('encouraged more regular flossing of the teeth', $this->domain),
						__('helped organize your life in the small ways that matter', $this->domain),
						__('saved your minutes--if not tens of minutes--writing your own solution', $this->domain),
						__('brightened your day... or darkened it if sleeping in', $this->domain),
						__('caused you to dance a little jig of joy and joyousness', $this->domain),
						__('inspired you to tweet a little @twinpictues social love', $this->domain),
						__('tasted great, while also being less filling', $this->domain),
						__('caused you to shout: "everybody spread love, give me some mo!"', $this->domain),
						__('really tied the room together, Dude', $this->domain),
						__('helped you keep the funk alive', $this->domain),
						__('<a href="http://www.youtube.com/watch?v=dvQ28F5fOdU" target="_blank">soften hands while you do dishes</a>', $this->domain),
						__('helped that little old lady <a href="http://www.youtube.com/watch?v=Ug75diEyiA0" target="_blank">find the beef</a>', $this->domain)
					);
		$rand_key = array_rand($like_it_arr);
		$like_it = $like_it_arr[$rand_key];

		$share_it_arr = array(
						'http://www.facebook.com/twinpictures',
						'http://twitter.com/twinpictures',
						'http://plus.google.com/+TwinpicturesDe',
						'https://wordpress.org/support/view/plugin-reviews/jquery-t-countdown-widget'
					);
		$rand_key = array_rand($share_it_arr);
		$share_it = $share_it_arr[$rand_key];

	?>
		<div class="wrap">
			<h2><?php echo $this->plguin_options_page_title; ?></h2>
		</div>

		<div class="postbox-container metabox-holder meta-box-sortables" style="width: 69%">
			<div style="margin:0 5px;">
				<div class="postbox">
					<div class="handlediv" title="<?php _e( 'Click to toggle', $this->domain ) ?>"><br/></div>
					<h3 class="handle"><?php _e( 'T(-) Countdown Settings', $this->domain ) ?></h3>
					<div class="inside">
						<form method="post" action="options.php">
							<?php
								settings_fields( $this->domain );
								$options = $this->options;
							?>

							<table class="form-table">
								<tr>
									<th><?php _e( 'Rockstar Features', $this->domain ) ?>:</th>
									<td><label><input type="checkbox" id="<?php echo $this->options_name ?>[rockstar]" name="<?php echo $this->options_name ?>[rockstar]" value="rockstar"  <?php echo checked( $options['rockstar'], 'rockstar' ); ?> /> <?php _e('Enable', $this->domain); ?>
										<br /><span class="description"><?php _e('Enable rockstar features.', $this->domain); ?></span></label>
									</td>
								</tr>


								<tr>
									<th><?php _e( 'Force Load CSS', $this->domain ) ?>:</th>
									<td><label>
										<select name="<?php echo $this->options_name ?>[force_css]" id="<?php echo $this->options_name ?>[force_css]">
											<option value=''> </option>
											<?php
												$styles_arr = CountDownTimer::get_styles();
												foreach($styles_arr as $style_name){
													$selected = "";
													if($options['force_css'] == $style_name){
														$selected = 'SELECTED';
													}
													echo '<option value="'.$style_name.'" '.$selected.'>'.$style_name.'</option>';
												}
											?>
										</select>
										<br /><span class="description"><?php _e('Force a css style to load in the header', $this->domain); ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Custom CSS', $this->domain ) ?>:</th>
									<td><label><textarea id="<?php echo $this->options_name ?>[custom_css]" name="<?php echo $this->options_name ?>[custom_css]" style="width: 100%; height: 537px;"><?php echo $options['custom_css']; ?></textarea>
										<br /><span class="description"><?php _e( 'Custom CSS style for <em>ultimate flexibility</em>', $this->domain ) ?></span></label>
									</td>
								</tr>
							</table>
							<p class="submit" style="margin-bottom: 20px;">
								<input class="button-primary" type="submit" value="<?php _e( 'Save Changes', $this->domain ) ?>" style="float: right;" />
							</p>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="postbox-container side metabox-holder" style="width:29%;">
			<div style="margin:0 5px;">
				<div class="postbox">
					<h3><?php _e( 'About' ) ?></h3>
					<div class="inside">
						<h4><?php echo $this->plugin_name; ?> <?php _e('Version', $this->domain); ?> <?php echo $this->version; ?></h4>
						<p><?php _e( 'T(-) Countdown is a highly customizable, HTML5 countdown timer that can be displayed as a sidebar widget or in a post or page using a shortcode.', $this->domain) ?></p>
						<ul>
							<li><?php printf( __( '%sDetailed documentation%s, complete with working demonstrations of all shortcode attributes, is availabel for your instructional enjoyment.', 'tminus'), '<a href="http://plugins.twinpictures.de/plugins/t-minus-countdown/documentation/" target="_blank">', '</a>'); ?></li>
							<li><?php printf( __( 'A %sCommunity translation%s tool has been set up that allows anyone to assist in translating T(-) Countdown. All are %swelcome to participate%s.', 'tminus'), '<a href="http://translate.twinpictures.de/projects/t-countdown" target="_blank">', '</a>', '<a href="http://translate.twinpictures.de/wordpress/wp-login.php?action=register" target="_blank">', '</a>' ); ?></li>
							<li><?php printf( __( 'If this plugin %s, please consider %ssharing your story%s with others.', 'tminus'), $like_it, '<a href="'.$share_it.'" target="_blank">', '</a>' ) ?></li>
							<li><a href="https://wordpress.org/plugins/jquery-t-countdown-widget/" target="_blank">WordPress.org</a> | <a href="http://plugins.twinpictures.de/plugins/t-minus-countdown/" target="_blank">Twinpictues Plugin Oven</a></li>
						</ul>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="postbox-container side metabox-holder meta-box-sortables" style="width:29%;">
			<div style="margin:0 5px;">
				<div class="postbox">
					<div class="handlediv" title="<?php _e( 'Click to toggle' ) ?>"><br/></div>
					<h3 class="handle"><?php _e( 'T(-) Countdown Control' ) ?></h3>
					<div class="inside">
						<p><?php printf(__( '%sT(-) Countdown Control%s is our premium plugin that manages and schedules multiple recurring countdown timers for repeating events.', 'tminus' ), '<a href="http://plugins.twinpictures.de/premium-plugins/t-minus-countdown-control/?utm_source=t-countdown&utm_medium=plugin-settings-page&utm_content=t-countdown&utm_campaign=t-control-sidebar">', '</a>'); ?></p>
						<h4><?php _e('Reasons To Go Pro', 'tminus'); ?></h4>
						<ol>
							<li><?php _e('Schedule and manage multiple recurring countdowns', 'tminus'); ?></li>
							<li><?php _e('Highly responsive professional support', 'tminus'); ?></li>
							<li><?php printf(__('%sT(-) Countdown Control Testimonials%s', 'tminus'), '<a href="http://plugins.twinpictures.de/testimonial/t-countdown-control-testimonias/" target="_blank">', '</a>'); ?></li>
						</ol>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>

		<?php if( is_plugin_active( 't-countdown-events/t-countdown-events.php' ) ) : ?>

			<div class="postbox-container side metabox-holder" style="width:29%;">
				<div style="margin:0 5px;">
					<div class="postbox">
						<h3 class="handle"><?php _e( 'Register T(-) Countdown Events', $this->domain) ?></h3>
						<div class="inside">
							<p><?php printf( __('To receive plugin updates you must register your plugin. Enter your T(-) Countdown Events licence key below. Licence keys may be viewed and managed by logging into %syour account%s.', $this->domain), '<a href="http://plugins.twinpictures.de/your-account/" target="_blank">', '</a>'); ?></p>
							<form method="post" action="options.php">
								<?php
									settings_fields( $this->license_group );
									$options = get_option($this->license_name);
									$tce_licence = ( !isset( $options['tminus_event_license_key'] ) ) ? '' : $options['tminus_event_license_key'];
								?>
								<fieldset>
									<table style="width: 100%">
										<tbody>
											<tr>
												<th><?php _e( 'License Key', $this->domain ) ?>:</th>
												<td><label for="<?php echo $this->license_name ?>[tminus_event_license_key]"><input type="password" id="<?php echo $this->license_name ?>[tminus_event_license_key]" name="<?php echo $this->license_name ?>[tminus_event_license_key]" value="<?php esc_attr_e( $tce_licence ); ?>" style="width: 100%" />
													<br /><span class="description"><?php _e('Enter your license key', $this->domain); ?></span></label>
	                                                                                        </td>

											</tr>

											<?php if( isset($options['tminus_event_license_key']) ) { ?>
												<tr valign="top">
													<th><?php _e('License Status', $this->domain); ?>:</th>
													<td>
														<?php if( isset($options['tminus_event_license_status']) && $options['tminus_event_license_status'] == 'valid' ) { ?>
															<span style="color:green;"><?php _e('active'); ?></span><br/>
															<input type="submit" class="button-secondary" name="edd_tce_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
														<?php } else {
																if( isset($options['tminus_event_license_status'])){ ?>
																	<span style="color: red"><?php echo $options['tminus_event_license_status']; ?></span><br/>
															<?php } else { ?>
																	<span style="color: grey">inactive</span><br/>
															<?php } ?>
																<input type="submit" class="button-secondary" name="edd_tce_license_activate" value="<?php _e('Activate License'); ?>"/>
														<?php } ?>
														</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</fieldset>
	                        	<?php submit_button( __( 'Register', $this->domain) ); ?>
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php else: ?>
			<div class="postbox-container side metabox-holder meta-box-sortables" style="width:29%;">
				<div style="margin:0 5px;">
					<div class="postbox">
						<div class="handlediv" title="<?php _e( 'Click to toggle', 'colomat' ) ?>"><br/></div>
						<h3 class="hndle">T(-) Countdown Events</h3>
							<div class="inside">
								<p><?php printf( __('%sT(-) Countdown Events%s is a new add-on plugin for T(-) Countdown Control that adds multiple event scheduling. Trigger events, such as changing content or firing a javascript function at specific times while the countdown is running.', $this->domain), '<a href="http://plugins.twinpictures.de/premium-plugins/t-countdown-events/?utm_source=t-countdown&utm_medium=plugin-settings-page&utm_content=t-countdown&utm_campaign=t-events-sidebar" target="_blank">', '</a>'); ?></p>
							</div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		<?php endif; ?>
	<?php
	}

	function _set_options() {
		// set options
		$saved_options = get_option( $this->options_name );

		// set all options
		if ( ! empty( $saved_options ) ) {
			foreach ( $this->options AS $key => $option ) {
				$this->options[ $key ] = ( empty( $saved_options[ $key ] ) ) ? '' : $saved_options[ $key ];
			}
		}
	}

}
$WP_TMinusCD = new WP_TMinusCD;

//style folders array
function folder_array($path, $exclude = ".|..") {
	if(is_dir($path)){
		$dh = opendir($path);
		$exclude_array = explode("|", $exclude);
		$result = array();
		while(false !==($file = readdir($dh))) {
			if( !in_array(strtolower($file), $exclude_array) && substr($file, 0, 1) != '.' ){
				$result[] = $file;
			}
		}
		closedir($dh);
		return $result;
	}
}

/**
 * CountDownTimer Class
 */
class CountDownTimer extends WP_Widget {
    /** constructor */
    function CountDownTimer() {
			load_plugin_textdomain( 'tminus', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			$widget_ops = array('classname' => 'CountDownTimer', 'description' => __('A highly customizable jQuery countdown timer by Twinpictures', 'tminus') );
			$this->WP_Widget('CountDownTimer', 'T(-) Countdown', $widget_ops);
    }

    /** Widget */
    function widget($args, $instance) {
			global $add_my_script;
			extract( $args );

			if(empty($instance['id'])){
				$instance['id'] = $args['widget_id'];
			}

			if( empty($instance['t']) ){
				//ancient
				if( !empty($instance['year']) ){
					$instance['t'] = $instance['year'].'-'.$instance['month'].'-'.$instance['day'].' '.$instance['hour'].':'.$instance['min'].':'.$instance['sec'];
					$instance['year'] = '';
					$instance['month'] = '';
					$instance['day'] = '';
					$instance['hour'] = '';
					$instance['min'] = '';
					$instance['sec'] = '';
				}
				//old
				else if( !empty( $instance['date'] )){
					$instance['t'] = $instance['date'].' '.$instance['hour'].':'.$instance['min'].':'.$instance['sec'];
					$instance['date'] = '';
					$instance['hour'] = '';
					$instance['min'] = '';
					$instance['sec'] = '';
				}
				//empty
				else{
					$instance['t'] = '2015-05-04 12:00:00';
				}
			}

			if(!empty($instance['tophtml'])){
				$instance['before'] = $instance['tophtml'];
				$instance['tophtml'] = '';
			}

			if(!empty($instance['bothtml'])){
				$instance['after'] = $instance['bothtml'];
				$instance['bothtml'] = '';
			}

			$content = '';
			if(!empty($instance['launchhtml'])){
				$content = $instance['launchhtml'];
				$instance['launchhtml'] = '';
			}

			echo $args['before_widget'];

			if ( !empty($instance['title']) ){
				echo $args['before_title']. $instance['title'] . $args['after_title'];
			}

			$sc_atts = '';
			foreach($instance AS $key => $value){
				if(!empty($value)){
					if($key == 'before' || $key == 'after'){
						$value = htmlspecialchars($value);
					}
					$sc_atts .= $key . '="'.$value.'" ';
				}
			}

			echo do_shortcode('[tminus '.$sc_atts.']'.$content.'[/tminus]');

			echo $args['after_widget'];
		}

		function get_styles($custom_css = null) {
			//default styles
			$styles_arr = folder_array(WP_PLUGIN_DIR.'/'. dirname( plugin_basename(__FILE__) ).'/css');
			if( !empty( $custom_css ) ){
				preg_match_all("/.(\w+)-dashboard/", $custom_css, $custom_styles);
				$styles_arr = array_merge($styles_arr, $custom_styles[1]);
			}
			natcasesort($styles_arr);
			return $styles_arr;
	    }

		function update( $new_instance, $old_instance ) {
			$instance = array_merge($old_instance, $new_instance);

			$instance['title'] = (empty( $new_instance['title'])) ? '' : strip_tags( $new_instance['title'] );
			$instance['omitweeks'] = (empty($new_instance['omitweeks'])) ? '' : $new_instance['omitweeks'];
			$instance['jsplacement'] = (empty($new_instance['jsplacement'])) ? '' : $new_instance['jsplacement'];

			return $instance;
		}

    	/** Form */
		function form($instance) {
			$options = get_option('WP_TMC_options');

			extract(shortcode_atts(array(
					'title' => '',
					'id' => '',
					't' => '',
					'weeks' => '',
					'days' => '',
					'hours' => '',
					'minutes' => '',
					'seconds' => '',
					'omitweeks' => '',
					'style' => 'jedi',
					'before' => '',
					'after' => '',
					'width' => '',
					'height' => '',
					'launchwidth' => '',
					'launchheight' => '',
					'launchtarget' => 'countdown',
					'jsplacement' => '',
					'event_id' => '',
			), $instance));

			//var_dump($instance);
			if( empty($t) ){
				//ancient
				if( !empty($instance['year']) ){
					$t = $instance['year'].'-'.$instance['month'].'-'.$instance['day'].' '.$instance['hour'].':'.$instance['min'].':'.$instance['sec'];
				}
				//old
				else if( !empty( $instance['date'] )){
					$t = $instance['date'].' '.$instance['hour'].':'.$instance['min'].':'.$instance['sec'];
				}
			}

			//old values remove in a few versions
			if(!empty($instance['jsplacement']) && $instance['jsplacement'] == 'footer'){
				$jsplacement = '';
			}
			if(!empty($instance['weektitle'])){
				$weeks = $instance['weektitle'];
			}
			if(!empty($instance['daytitle'])){
				$days = $instance['daytitle'];
			}
			if(!empty($instance['hourtitle'])){
				$hours = $instance['hourtitle'];
			}
			if(!empty($instance['mintitle'])){
				$minutes = $instance['mintitle'];
			}
			if(!empty($instance['sectitle'])){
				$seconds = $instance['sectitle'];
			}

			$isrockstar = empty($options['rockstar']) ? '' : $options['rockstar'];

			//rockstar features
			if($isrockstar){
				$tophtml = empty($instance['tophtml']) ? '' : apply_filters('widget_tophtml', stripslashes($instance['tophtml']));
				$bothtml = empty($instance['bothtml']) ? '' : apply_filters('widget_bothtml', stripslashes($instance['bothtml']));
				$launchhtml = empty($instance['launchhtml']) ? '' : apply_filters('widget_launchhtml', stripslashes($instance['launchhtml']));

				//old values - remove in a vew versions
				if($launchtarget == 'Above Countdown'){
					$launchtarget = 'above';
				}
				else if($launchtarget == 'Below Countdown'){
					$launchtarget = 'below';
				}
				else if($launchtarget == 'Entire Widget'){
					$launchtarget = 'countdown';
				}
				else if($launchtarget == 'Count Up'){
					$launchtarget = 'countup';
				}
			}
	    ?>
	    <p><label><?php _e('Title:', 'tminus'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

		<p><label><?php _e('Target:', 'tminus'); ?> <input style="width: 230px;" id="<?php echo $this->get_field_id('t'); ?>" name="<?php echo $this->get_field_name('t'); ?>" type="text" value="<?php echo $t; ?>" class="t-datepicker"/></label></p>
		<?php
			if( is_plugin_active( 't-countdown-events/t-countdown-events.php' ) ){
				echo '<p><label>Event: <select name="'.$this->get_field_name('event_id').'" id="'.$this->get_field_name('event_id').'">';
				echo '<option value="">'.__('Select Event', 'tminus').'</option>';
				$args = array(
					'post_type' => 't-countdown-events',
					'posts_per_page' => -1,
					'post_parent' => 0
				);
				$event_query = new WP_Query($args);

				while ($event_query->have_posts()) : $event_query->the_post();
					$eventID = get_the_ID();
					$selected = "";
					if($event_id == $eventID){
						$selected = 'SELECTED';
					}
					echo '<option value="'.$eventID.'" '.$selected.'>'.get_the_title().'</option>';
				endwhile;
				wp_reset_postdata();
				echo '</select></p>';
			}
		?>
		<p><?php _e('Style:', 'tminus'); ?> <select name="<?php echo $this->get_field_name('style'); ?>" id="<?php echo $this->get_field_name('style'); ?>">
			<?php
				$styles_arr = $this->get_styles($options['custom_css']);
				foreach($styles_arr as $style_name){
					$selected = "";
					if($style == $style_name){
						$selected = 'SELECTED';
					}
					echo '<option value="'.$style_name.'" '.$selected.'>'.$style_name.'</option>';
				}
			?>
		</select></p>

		<p><label><input type="checkbox" id="<?php echo $this->get_field_id('omitweeks'); ?>" name="<?php echo $this->get_field_name('omitweeks'); ?>" value="true"  <?php echo checked( $omitweeks, 'true' ); ?> /> <?php _e('Omit weeks from timer.', 'tminus'); ?></label></p>

		<p><label><input type="checkbox" id="<?php echo $this->get_field_id('jsplacement'); ?>" name="<?php echo $this->get_field_name('jsplacement'); ?>" value="inline"  <?php echo checked( $jsplacement, 'inline' ); ?> /> <?php _e('Inject JavaScript Inline.', 'tminus'); ?></label></p>

		<?php
			echo '<h3>'.__('Rockstar Features', 'tminus').'</h3>';
			if($isrockstar){
			?>
			<a class="collapseomatic" id="tophtml<?php echo $this->get_field_id('tophtml'); ?>"><?php _e('Above Countdown', 'tminus'); ?></a>
			<div id="target-tophtml<?php echo $this->get_field_id('tophtml'); ?>" class="collapseomatic_content">
					<p><label for="<?php echo $this->get_field_id('tophtml'); ?>"><?php _e('Top HTML:', 'tminus'); ?></label> <textarea id="<?php echo $this->get_field_id('tophtml'); ?>" name="<?php echo $this->get_field_name('tophtml'); ?>"><?php echo $tophtml; ?></textarea></p>
			</div>
			<br/>
			<a class="collapseomatic" id="bothtml<?php echo $this->get_field_id('bothtml'); ?>"><?php _e('Below Countdown', 'tminus'); ?></a>
			<div id="target-bothtml<?php echo $this->get_field_id('bothtml'); ?>" class="collapseomatic_content">
					<p><label for="<?php echo $this->get_field_id('bothtml'); ?>"><?php _e('Bottom HTML:', 'tminus'); ?></label> <textarea id="<?php echo $this->get_field_id('bothtml'); ?>" name="<?php echo $this->get_field_name('bothtml'); ?>"><?php echo $bothtml; ?></textarea></p>
			</div>
			<br/>
			<a class="collapseomatic" id="launchhtml<?php echo $this->get_field_id('launchhtml'); ?>"><?php _e('When Countdown Reaches Zero', 'tminus'); ?></a>
			<div id="target-launchhtml<?php echo $this->get_field_id('launchhtml'); ?>" class="collapseomatic_content">
					<p><label for="<?php echo $this->get_field_id('launchhtml'); ?>"><?php _e('Launch Event HTML:', 'tminus'); ?></label> <textarea id="<?php echo $this->get_field_id('launchhtml'); ?>" name="<?php echo $this->get_field_name('launchhtml'); ?>"><?php echo $launchhtml; ?></textarea></p>
					<p><?php _e('Launch Target:', 'tminus'); ?> <select name="<?php echo $this->get_field_name('launchtarget'); ?>" id="<?php echo $this->get_field_name('launchtarget'); ?>">
					<?php
						$target_arr = array(
							'tophtml' => __('Above Countdown', 'tminus'),
							'bothtml' => __('Below Countdown', 'tminus'),
							'countdown' => __('Entire Countdown', 'tminus'),
							'countup' => __('Count Up', 'tminus')
						);
						foreach($target_arr as $key => $val){
							$selected = "";
							if($launchtarget == $key){
								$selected = 'SELECTED';
							}
							echo '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
						}
					?>
					</select></p>
			</div>
			<br/>
			<a class="collapseomatic" id="titles<?php echo $this->get_field_id('weeks'); ?>"><?php _e('Digit Titles', 'tminus'); ?></a>
			<div id="target-titles<?php echo $this->get_field_id('weeks'); ?>" class="collapseomatic_content">
					<p><label for="<?php echo $this->get_field_id('weeks'); ?>"><?php _e('How do you spell "weeks"?:', 'tminus'); ?> <input class="widefat" id="<?php echo $this->get_field_id('weeks'); ?>" name="<?php echo $this->get_field_name('weeks'); ?>" type="text" value="<?php echo $weeks; ?>" /></label></p>
					<p><label for="<?php echo $this->get_field_id('days'); ?>"><?php _e('How do you spell "days"?:', 'tminus'); ?> <input class="widefat" id="<?php echo $this->get_field_id('days'); ?>" name="<?php echo $this->get_field_name('days'); ?>" type="text" value="<?php echo $days; ?>" /></label></p>
					<p><label for="<?php echo $this->get_field_id('hours'); ?>"><?php _e('How do you spell "hours"?:', 'tminus'); ?> <input class="widefat" id="<?php echo $this->get_field_id('hours'); ?>" name="<?php echo $this->get_field_name('hours'); ?>" type="text" value="<?php echo $hours; ?>" /></label></p>
					<p><label for="<?php echo $this->get_field_id('minutes'); ?>"><?php _e('How do you spell "minutes"?:', 'tminus'); ?> <input class="widefat" id="<?php echo $this->get_field_id('minutes'); ?>" name="<?php echo $this->get_field_name('minutes'); ?>" type="text" value="<?php echo $minutes; ?>" /></label></p>
					<p><label for="<?php echo $this->get_field_id('seconds'); ?>"><?php _e('And "seconds" are spelled:', 'tminus'); ?> <input class="widefat" id="<?php echo $this->get_field_id('seconds'); ?>" name="<?php echo $this->get_field_name('seconds'); ?>" type="text" value="<?php echo $seconds; ?>" /></label></p>
			</div>

			<?php
			}
			else{
				$like_it_arr = array('makes me feel warm and fuzzy inside... in a good way', 'restores my faith in humanity... if only for a fleating second', 'rocked my world and is totally worth 3 bucks', 'offered me a positive vision of future living', 'inspires me to commit random acts of kindness', 'helped organize my life in one of the small ways that matter', 'saved me minutes if not tens of minutes writing your own solution', 'brightened my day... or darkened it since I wanted to sleep in anyway', 'is totally worth 3 bucks');
				$rand_key = array_rand($like_it_arr);
				$like_it = $like_it_arr[$rand_key];
		?>
		<p>
			<?php printf( __('T(-) Countdown %s!', 'tminus'), $like_it); ?>
			<a href="options-general.php?page=t-countdown"><?php _e('Enable Rockstar Features', 'tminus'); ?></a>
		</p>
		<?php } //end if not rockstar ?>

		<br/>
		<a class="collapseomatic" id="tccc<?php echo $this->get_field_id('isrockstar'); ?>"><?php _e('Schedule Recurring Countdown', 'tminus'); ?></a>
		<div id="target-tccc<?php echo $this->get_field_id('isrockstar'); ?>" class="collapseomatic_content">
			<p><?php printf(__('%sT(-) Countdown Control%s is a premium countdown plugin that includes the ability to schedule and manage multiple recurring T(-) Countdowns... the Jedi way.', 'tminus'), '<a href="http://plugins.twinpictures.de/premium-plugins/t-minus-countdown-control/?utm_source=t-countdown&utm_medium=widget-settings&utm_content=t-countdown-control&utm_campaign=t-countdown-widget" target="blank" title="(-) Countdown Control">', '</a>'); ?></p>
		</div>
		<br/><br/>
		<?php
    }
} // class CountDownTimer

// register CountDownTimer widget
add_action('widgets_init', create_function('', 'return register_widget("CountDownTimer");'));


//code for the footer
add_action('wp_footer', 'print_my_script', 99);

function print_my_script() {
	global $add_my_script;
	if ( ! $add_my_script ){
		return;
	}

	?>
		<script language="javascript" type="text/javascript">
			jQuery(document).ready(function($) {
	<?php
	//var_dump('hey dude', $add_my_script);
	foreach((array) $add_my_script as $script){
	?>
		$('#<?php echo $script['id']; ?>-dashboard').countDown({
			targetDate: {
				'day': 	<?php echo $script['day']; ?>,
				'month': <?php echo $script['month']; ?>,
				'year': <?php echo $script['year']; ?>,
				'hour': <?php echo $script['hour']; ?>,
				'min': 	<?php echo $script['min']; ?>,
				'sec': 	<?php echo $script['sec']; ?>,
				'localtime': '<?php echo $script['localtime']; ?>',
			},
			style: '<?php echo $script['style']; ?>',
			launchtarget: '<?php echo $script['launchtarget']; ?>',
			omitWeeks: '<?php echo $script['omitweeks']; ?>',
			id: '<?php echo $script['id']; ?>',
			event_id: '<?php echo $script['event_id']; ?>'
				<?php
				if($script['content']){
					echo ", onComplete: function() {
						$('#".$script['id']."-".$script['launchtarget']."').css({'width' : '".$script['launchwidth']."', 'height' : '".$script['launchheight']."'});
						$('#".$script['id']."-".$script['launchtarget']."').html('".do_shortcode($script['content'])."');
					}";
				}
				?>
		});
	<?php
	}
	?>
			});
		</script>
	<?php
}

//the short code
function tminuscountdown($atts, $content=null) {
	global $add_my_script;
	//find a random number, if no id was assigned
	$ran = rand(1, 10000);

    extract(shortcode_atts(array(
		'id' => $ran,
		't' => '',
		'weeks' => __('weeks', 'tminus'),
		'days' => __('days', 'tminus'),
		'hours' => __('hours', 'tminus'),
		'minutes' => __('minutes', 'tminus'),
		'seconds' => __('seconds', 'tminus'),
		'omitweeks' => 'false',
		'style' => 'jedi',
		'before' => '',
		'after' => '',
		'width' => 'auto',
		'height' => 'auto',
		'launchwidth' => 'auto',
		'launchheight' => 'auto',
		'launchtarget' => 'countdown',
		'jsplacement' => 'footer',
		'event_id' => '',
	), $atts));

	if(empty($t)){
		return;
	}

	//insert some style into your life
	$style_file_url = plugins_url('/css/'.$style.'/style.css', __FILE__);

	if ( file_exists( __DIR__ .'/css/'.$style.'/style.css' ) ) {
		if (! wp_style_is( 'countdown-'.$style.'-css', 'registered' )) {
			wp_register_style( 'countdown-'.$style.'-css', $style_file_url, array(), '2.0');
		}
		wp_enqueue_style( 'countdown-'.$style.'-css' );
	}

	$now = strtotime(current_time('mysql'));
	$target = strtotime($t, $now);

	//difference in seconds
	$diffSecs = $target - $now;

	$day = date ( 'd', $target );
	$month = date ( 'm', $target );
	$year = date ( 'Y', $target );
	$hour = date ( 'H', $target );
	$min = date ( 'i', $target );
	$sec = date ( 's', $target );

	//countdown digits
	$date_arr = array();
	$date_arr['secs'] = $diffSecs % 60;
	$date_arr['mins'] = floor($diffSecs/60)%60;
	$date_arr['hours'] = floor($diffSecs/60/60)%24;

	if($omitweeks == 'false'){
		$dash_omitweeks_class = '';
		$date_arr['days'] = floor($diffSecs/60/60/24)%7;
	}
	else{
		$dash_omitweeks_class = 'omitweeks';
		$date_arr['days'] = floor($diffSecs/60/60/24);
	}
	$date_arr['weeks']	= floor($diffSecs/60/60/24/7);

	foreach ($date_arr as $i => $d) {
		$d1 = $d%10;
		if($d < 100){
			$d2 = ($d-$d1) / 10;
			$d3 = 0;
		}
		else{
			$dr = $d%100;
			$dm = $d-$dr;
			$d2 = ($d-$dm-$d1) / 10;
			$d3 = $dm / 100;
		}
		/* here is where the 1000's support will go... someday. */

		//now assign all the digits to the array
		$date_arr[$i] = array(
			(int)$d3,
			(int)$d2,
			(int)$d1,
			(int)$d
		);
	}

	if(is_numeric($width)){
		$width .= 'px';
	}
	if(is_numeric($height)){
		$height .= 'px';
	}
	$tminus = '<div id="'.$id.'-countdown" class="tminus_countdown" style="width:'.$width.'; height:'.$height.';">';
	$tminus .= '<div class="'.$style.'-countdown '.$dash_omitweeks_class.'">';
	$tminus .= '<div id="'.$id.'-tophtml" class="'.$style.'-tophtml">';
    if($before){
        $tminus .=  htmlspecialchars_decode($before);
    }
	$tminus .=  '</div>';

	//drop in the dashboard
	$tminus .=  '<div id="'.$id.'-dashboard" class="'.$style.'-dashboard">';
	if($omitweeks == 'false'){
		//set up correct style class for double or triple digit love
		$wclass = $style.'-dash '.$style.'-weeks_dash';
		if($date_arr['weeks'][0] > 0){
			$wclass = $style.'-tripdash '.$style.'-weeks_trip_dash';
		}

		$tminus .=  '<div class="'.$wclass.'"><div class="'.$style.'-dash_title">'.$weeks.'</div>';
		if($date_arr['weeks'][0] > 0){
			$tminus .=  '<div class="'.$style.'-digit">'.$date_arr['weeks'][0].'</div>';
		}
		$tminus .=  '<div class="'.$style.'-digit">'.$date_arr['weeks'][1].'</div><div class="'.$style.'-digit">'.$date_arr['weeks'][2].'</div></div>';
	}

	//set up correct style class for double or triple digit love
	$dclass = $style.'-dash '.$style.'-days_dash';

	if($omitweeks == 'true' && $date_arr['days'][3] > 99){
		$dclass = $style.'-tripdash '.$style.'-days_trip_dash';
	}

	$tminus .= '<div class="'.$dclass.'"><div class="'.$style.'-dash_title">'.$days.'</div>';

	if($omitweeks == 'true' && $date_arr['days'][3] > 99){
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['days'][0].'</div>';
	}
	$tminus .= '<div class="'.$style.'-digit">'.$date_arr['days'][1].'</div><div class="'.$style.'-digit">'.$date_arr['days'][2].'</div>';
	$tminus .= '</div>';
	$tminus .= '<div class="'.$style.'-dash '.$style.'-hours_dash">';
		$tminus .= '<div class="'.$style.'-dash_title">'.$hours.'</div>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['hours'][1].'</div>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['hours'][2].'</div>';
	$tminus .= '</div>';
		$tminus .= '<div class="'.$style.'-dash '.$style.'-minutes_dash">';
		$tminus .= '<div class="'.$style.'-dash_title">'.$minutes.'</div>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['mins'][1].'</div>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['mins'][2].'</div>';
	$tminus .= '</div>';
		$tminus .= '<div class="'.$style.'-dash '.$style.'-seconds_dash">';
		$tminus .= '<div class="'.$style.'-dash_title">'.$seconds.'</div>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['secs'][1].'</div>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['secs'][2].'</div>';
	$tminus .= '</div>';
	$tminus .= '</div>'; //close the dashboard

	$tminus .= '<div id="'.$id.'-bothtml" class="'.$style.'-bothtml">';
	if($after){
		$tminus .= htmlspecialchars_decode($after);
	}
	$tminus .= '</div></div></div>';

	$t = date( 'n/j/Y H:i:s', strtotime(current_time('mysql')) );

	if(is_numeric($launchwidth)){
		$launchwidth .= 'px';
	}
	if(is_numeric($launchheight)){
		$launchheight .= 'px';
	}

	$content = str_replace(array('\r\n', '\r', '\n<p>', '\n'), '', $content);
	$content = stripslashes($content);
	if($jsplacement == "footer"){
		$add_my_script[$id] = array(
			'id' => $id,
			'day' => $day,
			'month' => $month,
			'year' => $year,
			'hour' => $hour,
			'min' => $min,
			'sec' => $sec,
			'localtime' => $t,
			'style' => $style,
			'omitweeks' => $omitweeks,
			'content' => $content,
			'launchtarget' => $launchtarget,
			'launchwidth' => $launchwidth,
			'launchheight' => $launchheight,
			'event_id' => $event_id,
		);
	}
	else{
		$tminus .= "<script language='javascript' type='text/javascript'>
			jQuery(document).ready(function($) {
				$('#".$id."-dashboard').countDown({
					targetDate: {
						'day': 	".$day.",
						'month': ".$month.",
						'year': ".$year.",
						'hour': ".$hour.",
						'min': 	".$min.",
						'sec': 	".$sec."
					},
					style: '".$style."',
					id: '".$id."',
					event_id: '".$event_id."',
					launchtarget: '".$launchtarget."',
					omitWeeks: '".$omitweeks."'";

		if($content){
			$tminus .= ", onComplete: function() {
								$('#".$id."-".$launchtarget."').css({'width' : '".$launchwidth."', 'height' : '".$launchheight."'});
								$('#".$id."-".$launchtarget."').html('".do_shortcode($content)."');
							}";
		}
		$tminus .= "});
			});
		</script>";
	}
	return $tminus;
}
add_shortcode('tminus', 'tminuscountdown');

add_action('wp_ajax_tminusevents', 'tminusevents_callback');
add_action('wp_ajax_nopriv_tminusevents', 'tminusevents_callback');

function tminusevents_callback() {
    $nonce = $_POST['countdownNonce'];
    if ( ! wp_verify_nonce( $nonce, 'tountajax-countdownonce-nonce' ) ){
		die ( 'Busted!');
	}

	echo WP_TminusEvents::tminusEvents( $_POST['event_id'] );
	wp_die();
}

?>
