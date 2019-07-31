<?php
/*
Plugin Name: T(-) Countdown
Text Domain: t-countdown
Plugin URI: https://plugins.twinpictures.de/plugins/t-countdown/
Description: Display and configure multiple countdown timers in years, months, weeks, days, hours and seconds in a number of different styles.
Version: 2.4.6b
Author: twinpictures
Author URI: https://plugins.twinpictures.de/
License: GPL2
*/

class WP_TMinus {
	var $plugin_name = 'T(-) Countdown';
	var $version = '2.4.6b';
	var $domain = 'tminus';
	var $plguin_options_page_title = 'T(-) Countdown Options';
	var $plugin_options_menue_title = 'T(-) Countdown';
	var $plugin_options_slug = 't-countdown';

	var $options_name = 'WP_TMC_options';

	/**
	 * @var array
	 */

	var $options = array(
		'omityears'   => 'false',
		'omitmonths'  => 'false',
		'omitweeks'		=> 'false',
		'omitseconds'	=> 'false',
		'yearlabel'		=> 'Years',
		'monthlabel'	=> 'Months',
		'weeklabel'		=> 'Weeks',
		'daylabel'		=> 'Days',
		'hourlabel'		=> 'Hours',
		'minutelabel' => 'Minutes',
		'secondlabel' => 'Seconds',
		'custom_css' 	=> '',
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
		add_action( 'init', array( $this, 'register_tminus_block' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_actions' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'wp_head', array( $this, 'plugin_head_inject' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'countdown_scripts' ) );
		//add_action( 'enqueue_block_assets', array( $this, 'countdown_scripts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_assets' ) );
		add_action( 'plugins_loaded', array( $this, 'tminus_load_textdomain' ) );

		add_action( 'wp_ajax_tminusevents', array( $this, 'tminusevents_callback' ) );
		add_action( 'wp_ajax_nopriv_tminusevents', array( $this, 'tminusevents_callback') );

		// the shortcode
		add_shortcode('tminus', array( $this, 'tminus_shortcode') );
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
		//deactivate legacy version if installed
		if( is_plugin_active( 'jquery-t-countdown-widget/countdown-timer.php' ) ){
			deactivate_plugins( 'jquery-t-countdown-widget/countdown-timer.php' );
		}
		if( is_plugin_active( 't-countdown-events/t-countdown-events.php' ) ){
			register_setting( $this->license_group, $this->license_name, array('WP_TminusEvents', 'edd_sanitize_license') );
		}
	}

	/**
	 * Load textdomain.
	 *
	 * @since 2.5.6
	 */
	function tminus_load_textdomain() {
		load_plugin_textdomain( 't-countdown' );
	}

	static function folder_array($path, $exclude = ".|..") {
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
 	* Register tminus block
 	*/
	function register_tminus_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active.
			return;
		}

		wp_register_script(
			'tminus-block',
			plugin_dir_url(__FILE__) . 'block.js',
			array(
				'wp-blocks',
				'wp-element',
				'wp-components',
				'wp-i18n',
				'wp-editor',
			),
			'0.2',
			true
		);

		$styles_arr = $this->folder_array(WP_PLUGIN_DIR.'/'. dirname( plugin_basename(__FILE__) ).'/css');
		if( !empty( $this->options['custom_css'] ) ){
			preg_match_all("/.(\w+)-dashboard/", $this->options['custom_css'], $custom_styles);
			$styles_arr = array_merge($styles_arr, $custom_styles[1]);
		}
		natcasesort($styles_arr);
		$tminus_style = array();
		foreach($styles_arr as $style){
			$tminus_style[] = array(
				'value' => $style,
				'label' => __($style, 't-countdown')
			);
		}

		$tminus_options = array(
			'styles' => $tminus_style,

			'omityears' => $this->options['omityears'],
			'omitmonths' => $this->options['omitmonths'],
			'omitweeks' => $this->options['omitweeks'],
			'omitseconds' => $this->options['omitseconds'],

			'yearlabel' => $this->options['yearlabel'],
			'monthlabel' => $this->options['monthlabel'],
			'weeklabel' => $this->options['weeklabel'],
			'daylabel' => $this->options['daylabel'],
			'hourlabel' => $this->options['hourlabel'],
			'minutelabel' => $this->options['minutelabel'],
			'secondlabel' => $this->options['secondlabel']
		);

		//pass default options to js
		wp_localize_script('tminus-block', 'tminus_options', $tminus_options );

		register_block_type( 'tminus/countdown', array(
			'editor_script' => 'tminus-block',
			'render_callback' => [$this, 'tminus_callback'],
			'attributes' => array(
														'content' => array('type' => 'string'),
														'id' => array ('type' => 'string'),
														'style' => array ('type' => 'string', 'default' => 'jedi'),
														't' => array ('type' => 'number'),
														'timestr' => array ('type' => 'string'),
														'launchtarget' => array ('type' => 'string'),
													  'secs' => array ('type' => 'string', 'default' => '00'),
														'omityears' => array ('type' => 'boolean', 'default' => $this->options['omityears']),
														'omitmonths' => array ('type' => 'boolean', 'default' => $this->options['omitmonths']),
														'omitweeks' => array ('type' => 'boolean', 'default' => $this->options['omitweeks']),
														'omitseconds' => array ('type' => 'boolean', 'default' => $this->options['omitseconds']),
														'yearlabel' => array ('type' => 'string', 'default' => $this->options['yearlabel']),
														'monthlabel' => array ('type' => 'string', 'default' => $this->options['monthlabel']),
														'weeklabel' => array ('type' => 'string', 'default' => $this->options['weeklabel']),
														'daylabel' => array ('type' => 'string', 'default' => $this->options['daylabel']),
														'hourlabel' => array ('type' => 'string', 'default' => $this->options['hourlabel']),
														'minutelabel' => array ('type' => 'string', 'default' => $this->options['minutelabel']),
														'secondlabel' => array ('type' => 'string', 'default' => $this->options['secondlabel']),
											)
		) );
	}

	// Add link to options page from plugin list
	function plugin_actions($links) {
		$new_links = array();
		$new_links[] = '<a href="options-general.php?page='.$this->plugin_options_slug.'">' . __('Settings', 't-countdown') . '</a>';
		return array_merge($new_links, $links);
	}

	//plugin header inject
	function plugin_head_inject(){
		// custom css
		if( !empty( $this->options['custom_css'] ) ){
			echo "<style>\n";
			echo $this->options['custom_css'];
			echo "\n</style>\n";
		}
	}

	// load editor only styles
	function block_assets(){
		$styles_arr = $this->folder_array(WP_PLUGIN_DIR.'/'. dirname( plugin_basename(__FILE__) ).'/css');
		foreach($styles_arr as $style){
			if (! wp_style_is( 'countdown-'.$style.'-css', 'registered' )) {
				$style_file_url = plugins_url('/css/'.$style.'/style.css', __FILE__);
				wp_register_style( 'countdown-'.$style.'-css', $style_file_url, array(), '2.0');
			}
			wp_enqueue_style( 'countdown-'.$style.'-css' );
		}
	}

	//load front-end countdown scripts
	function countdown_scripts(){
		$plugin_url = plugins_url() .'/'. dirname( plugin_basename(__FILE__) );

		//tCountdown script
		wp_register_script('countdown-script', $plugin_url.'/js/jquery.t-countdown.js', array ('jquery'), '2.4.2', 'true');
		// callback for t(-) events
		$response = array( 'now' => date( 'n/j/Y H:i:s', strtotime(current_time('mysql'))));
		wp_localize_script( 'countdown-script', 'tCountAjax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'countdownNonce' => wp_create_nonce( 'tountajax-countdownonce-nonce' ),
			'tminusnow' => json_encode($response)
		));
		wp_enqueue_script('countdown-script');
	}


	/**
	 * Admin options page
	 */
	function options_page() {
		$like_it_arr = array(
						__('made you feel all warm and fuzzy on the inside', 't-countdown'),
						__('restored your faith in humanity... even if only for a fleeting second', 't-countdown'),
						__('rocked your world', 'provided a positive vision of future living', 't-countdown'),
						__('inspired you to commit a random act of kindness', 't-countdown'),
						__('encouraged more regular flossing of the teeth', 't-countdown'),
						__('helped organize your life in the small ways that matter', 't-countdown'),
						__('saved your minutes--if not tens of minutes--writing your own solution', 't-countdown'),
						__('brightened your day... or darkened it if sleeping in', 't-countdown'),
						__('caused you to dance a little jig of joy and joyousness', 't-countdown'),
						__('inspired you to tweet a little @twinpictues social love', 't-countdown'),
						__('tasted great, while also being less filling', 't-countdown'),
						__('caused you to shout: "everybody spread love, give me some mo!"', 't-countdown'),
						__('really tied the room together, Dude', 't-countdown'),
						__('helped you keep the funk alive', 't-countdown'),
						__('<a href="https://www.youtube.com/watch?v=dvQ28F5fOdU" target="_blank">soften hands while you do dishes</a>', 't-countdown'),
						__('helped that little old lady <a href="https://www.youtube.com/watch?v=Ug75diEyiA0" target="_blank">find the beef</a>', 't-countdown')
					);
		$rand_key = array_rand($like_it_arr);
		$like_it = $like_it_arr[$rand_key];

		$share_it_arr = array(
						'https://www.facebook.com/twinpictures',
						'https://twitter.com/twinpictures',
						'https://wordpress.org/plugins/t-countdown/#reviews'
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
					<div class="handlediv" title="<?php _e( 'Click to toggle', 't-countdown' ) ?>"><br/></div>
					<h3 class="handle"><?php _e( 'T(-) Countdown Settings', 't-countdown' ) ?></h3>
					<div class="inside">
						<form method="post" action="options.php">
							<?php
								settings_fields( $this->domain );
								$options = $this->options;
							?>

							<table class="form-table">
								<tr>
									<th><?php _e( 'Omit Years', 't-countdown' ) ?>:</th>
									<td><label><input type="radio" name="<?php echo $this->options_name ?>[omityears][]" value="false"  <?php echo checked( $options['omityears'], 'false' ); ?> /> <?php _e('Display', 't-countdown'); ?></label>
										&nbsp; &nbsp; <label><input type="radio" name="<?php echo $this->options_name ?>[omityears][]" value="true"  <?php echo checked( $options['omityears'], 'true' ); ?> /> <?php _e('Hide', 't-countdown'); ?></label>
										<br /><span class="description"><?php _e('Display or hide years from countdowns', 't-countdown'); ?></span>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Omit Months', 't-countdown' ) ?>:</th>
									<td><label><input type="radio" name="<?php echo $this->options_name ?>[omitmonths][]" value="false"  <?php echo checked( $options['omitmonths'], 'false' ); ?> /> <?php _e('Display', 't-countdown'); ?></label>
										&nbsp; &nbsp; <label><input type="radio" name="<?php echo $this->options_name ?>[omitmonths][]" value="true"  <?php echo checked( $options['omitmonths'], 'true' ); ?> /> <?php _e('Hide', 't-countdown'); ?></label>
										<br /><span class="description"><?php _e('Display or hide months from countdowns', 't-countdown'); ?></span>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Omit Weeks', 't-countdown' ) ?>:</th>
									<td><label><input type="radio" name="<?php echo $this->options_name ?>[omitweeks][]" value="false"  <?php echo checked( $options['omitweeks'], 'false' ); ?> /> <?php _e('Display', 't-countdown'); ?></label>
										&nbsp; &nbsp; <label><input type="radio" name="<?php echo $this->options_name ?>[omitweeks][]" value="true"  <?php echo checked( $options['omitweeks'], 'true' ); ?> /> <?php _e('Hide', 't-countdown'); ?></label>
										<br /><span class="description"><?php _e('Display or hide weeks from countdowns', 't-countdown'); ?></span>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Omit Seconds', 't-countdown' ) ?>:</th>
									<td><label><input type="radio" name="<?php echo $this->options_name ?>[omitseconds][]" value="false"  <?php echo checked( $options['omitseconds'], 'false' ); ?> /> <?php _e('Display', 't-countdown'); ?></label>
										&nbsp; &nbsp; <label><input type="radio" name="<?php echo $this->options_name ?>[omitseconds][]" value="true"  <?php echo checked( $options['omitseconds'], 'true' ); ?> /> <?php _e('Hide', 't-countdown'); ?></label>
										<br /><span class="description"><?php _e('Display or hide seconds from countdowns', 't-countdown'); ?></span>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Years Label', 't-countdown' ) ?>:</th>
									<td><label><input type="text" id="<?php echo $this->options_name ?>[yearlabel]" name="<?php echo $this->options_name ?>[yearlabel]" value="<?php echo $options['yearlabel']; ?>" />
										<br /><span class="description"><?php _e( 'Default label for years.', 't-countdown' ) ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Months Label', 't-countdown' ) ?>:</th>
									<td><label><input type="text" id="<?php echo $this->options_name ?>[monthlabel]" name="<?php echo $this->options_name ?>[monthlabel]" value="<?php echo $options['monthlabel']; ?>" />
										<br /><span class="description"><?php _e( 'Default label for months.', 't-countdown' ) ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Weeks Label', 't-countdown' ) ?>:</th>
									<td><label><input type="text" id="<?php echo $this->options_name ?>[weeklabel]" name="<?php echo $this->options_name ?>[weeklabel]" value="<?php echo $options['weeklabel']; ?>" />
										<br /><span class="description"><?php _e( 'Default label for weeks.', 't-countdown' ) ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Days Label', 't-countdown' ) ?>:</th>
									<td><label><input type="text" id="<?php echo $this->options_name ?>[daylabel]" name="<?php echo $this->options_name ?>[daylabel]" value="<?php echo $options['daylabel']; ?>" />
										<br /><span class="description"><?php _e( 'Default label for days.', 't-countdown' ) ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Hours Label', 't-countdown' ) ?>:</th>
									<td><label><input type="text" id="<?php echo $this->options_name ?>[hourlabel]" name="<?php echo $this->options_name ?>[hourlabel]" value="<?php echo $options['hourlabel']; ?>" />
										<br /><span class="description"><?php _e( 'Default label for hours.', 't-countdown' ) ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Minutes Label', 't-countdown' ) ?>:</th>
									<td><label><input type="text" id="<?php echo $this->options_name ?>[minutelabel]" name="<?php echo $this->options_name ?>[minutelabel]" value="<?php echo $options['minutelabel']; ?>" />
										<br /><span class="description"><?php _e( 'Default label for minutes.', 't-countdown' ) ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Seconds Label', 't-countdown' ) ?>:</th>
									<td><label><input type="text" id="<?php echo $this->options_name ?>[secondlabel]" name="<?php echo $this->options_name ?>[secondlabel]" value="<?php echo $options['secondlabel']; ?>" />
										<br /><span class="description"><?php _e( 'Default label for seconds.', 't-countdown' ) ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Custom CSS', 't-countdown' ) ?>:</th>
									<td><label><textarea id="<?php echo $this->options_name ?>[custom_css]" name="<?php echo $this->options_name ?>[custom_css]" style="width: 100%; height: 537px;"><?php echo $options['custom_css']; ?></textarea>
										<br /><span class="description"><?php _e( 'Custom CSS style for <em>ultimate flexibility</em>', 't-countdown' ) ?></span></label>
									</td>
								</tr>
							</table>
							<p class="submit" style="margin-bottom: 20px;">
								<input class="button-primary" type="submit" value="<?php _e( 'Save Changes', 't-countdown' ) ?>" style="float: right;" />
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
						<h4><?php echo $this->plugin_name; ?> <?php _e('Version', 't-countdown'); ?> <?php echo $this->version; ?></h4>
						<p><?php _e( 'T(-) Countdown is a highly customizable, HTML5 countdown timer that can be displayed in a post or page using a shortcode.', 't-countdown') ?></p>
						<ul>
							<li><?php printf( __( '%sDetailed documentation%s, complete with working demonstrations of all shortcode attributes, is available for your instructional enjoyment.', 't-countdown'), '<a href="https://plugins.twinpictures.de/plugins/t-countdown/documentation/" target="_blank">', '</a>'); ?></li>
							<li><?php printf( __( 'A %sCommunity translation%s tool has been set up that allows anyone to assist in translating T(-) Countdown.', 't-countdown'), '<a href="https://translate.wordpress.org/projects/wp-plugins/t-countdown/" target="_blank">', '</a>' ); ?></li>
							<li><?php printf( __( 'If this plugin %s, please consider %ssharing your story%s with others.', 't-countdown'), $like_it, '<a href="'.$share_it.'" target="_blank">', '</a>' ) ?></li>
							<li><a href="https://wordpress.org/plugins/t-countdown/" target="_blank">WordPress.org</a> | <a href="https://plugins.twinpictures.de/plugins/t-countdown/" target="_blank">Twinpictues Plugin Oven</a></li>
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
						<p><?php printf(__( '%sT(-) Countdown Control%s is our premium plugin that manages and schedules multiple recurring countdown timers for repeating events.', 't-countdown' ), '<a href="https://plugins.twinpictures.de/premium-plugins/t-countdown-control/?utm_source=t-countdown&utm_medium=plugin-settings-page&utm_content=t-countdown&utm_campaign=t-control-sidebar">', '</a>'); ?></p>
						<h4><?php _e('Reasons To Go Pro', 't-countdown'); ?></h4>
						<ol>
							<li><?php _e('Schedule and manage multiple recurring countdowns', 't-countdown'); ?></li>
							<li><?php _e('Highly responsive professional support', 't-countdown'); ?></li>
							<li><?php printf(__('%sT(-) Countdown Control Testimonials%s', 't-countdown'), '<a href="https://plugins.twinpictures.de/testimonial/t-countdown-control-testimonias/" target="_blank">', '</a>'); ?></li>
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
						<h3 class="handle"><?php _e( 'Register T(-) Countdown Events', 't-countdown') ?></h3>
						<div class="inside">
							<p><?php printf( __('To receive plugin updates you must register your plugin. Enter your T(-) Countdown Events licence key below. Licence keys may be viewed and managed by logging into %syour account%s.', 't-countdown'), '<a href="https://plugins.twinpictures.de/your-account/" target="_blank">', '</a>'); ?></p>
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
												<th><?php _e( 'License Key', 't-countdown' ) ?>:</th>
												<td><label for="<?php echo $this->license_name ?>[tminus_event_license_key]"><input type="password" id="<?php echo $this->license_name ?>[tminus_event_license_key]" name="<?php echo $this->license_name ?>[tminus_event_license_key]" value="<?php esc_attr_e( $tce_licence ); ?>" style="width: 100%" />
													<br /><span class="description"><?php _e('Enter your license key', 't-countdown'); ?></span></label>
	                                                                                        </td>

											</tr>

											<?php if( isset($options['tminus_event_license_key']) ) { ?>
												<tr valign="top">
													<th><?php _e('License Status', 't-countdown'); ?>:</th>
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
	              <?php submit_button( __( 'Register', 't-countdown') ); ?>
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
								<p><?php printf( __('%sT(-) Countdown Events%s is a new add-on plugin for T(-) Countdown Control that adds multiple event scheduling. Trigger events, such as changing content or firing a javascript function at specific times while the countdown is running.', 't-countdown'), '<a href="https://plugins.twinpictures.de/premium-plugins/t-countdown-events/?utm_source=t-countdown&utm_medium=plugin-settings-page&utm_content=t-countdown&utm_campaign=t-events-sidebar" target="_blank">', '</a>'); ?></p>
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
						if(isset($saved_options[ $key ])){
							  if(is_array($saved_options[ $key ])){
									$this->options[ $key ] = ( empty( $saved_options[ $key ][0] ) ) ? '' : $saved_options[ $key ][0];
								}
								else{
									$this->options[ $key ] = ( empty( $saved_options[ $key ] ) ) ? '' : $saved_options[ $key ];
								}
						}
				}
		}
	}

	function tminus_callback($atts) {
		//return 'Hey dude...';
		if(empty($atts['content'])){
			$atts['content'] = '';
		}
		if(empty($atts['launchtarget'])){
			$atts['launchtarget'] = 'countdown';
		}
		$style = $atts['style'];
		$timestamp = new DateTime( );

		$timezone = get_option('timezone_string');
		if(!empty($atts['timezone'])){
			$timezone = $atts['timezone'];
		}

		if(!empty($atts['timestr'])){
			$t = $atts['timestr'];
		}
		else if(!empty($atts['t'])){
			$timestamp->setTimestamp($atts['t']);
			$t = $timestamp->format('Y-m-d H:i').':'.$atts['secs'];
		}
		else{
			$t = '+ 1 day';
		}


		$omityears = ($atts['omityears']) ? 'true' : 'false';
		$omitmonths = ($atts['omitmonths']) ? 'true' : 'false';
		$omitweeks = ($atts['omitweeks']) ? 'true' : 'false';
		$omitseconds = ($atts['omitseconds']) ? 'true' : 'false';

		return do_shortcode('[tminus t="'.$t.'" timezone="'.$timezone.'" style="'.$style.'" omityears="'.$omityears.'" omitmonths="'.$omitmonths.'" omitweeks="'.$omitweeks.'" omitseconds="'.$omitseconds.'" years="'.$atts['yearlabel'].'" months="'.$atts['monthlabel'].'" weeks="'.$atts['weeklabel'].'" days="'.$atts['daylabel'].'" hours="'.$atts['hourlabel'].'" minutes="'.$atts['minutelabel'].'" seconds="'.$atts['secondlabel'].'" launchtarget="'.$atts['launchtarget'].'"]'.$atts['content'].'[/tminus]');
	}

	//the shortcode
	function tminus_shortcode($atts, $content=null) {
		//find a random number, if no id was assigned
		$ran = uniqid();
	    extract(shortcode_atts(array(
			'id' => $ran,
			't' => '',
			'timezone' => get_option('timezone_string'),
			'years' => $this->options['yearlabel'],
			'months' => $this->options['monthlabel'],
			'weeks' => $this->options['weeklabel'],
			'days' => $this->options['daylabel'],
			'hours' => $this->options['hourlabel'],
			'minutes' => $this->options['minutelabel'],
			'seconds' => $this->options['secondlabel'],
			'omityears' => 'false',
			'omitmonths' => 'false',
			'omitweeks' => 'false',
			'omitseconds' => 'false',
			'style' => 'jedi',
			'before' => '',
			'after' => '',
			'width' => 'auto',
			'height' => 'auto',
			'launchwidth' => 'auto',
			'launchheight' => 'auto',
			'launchtarget' => 'countdown',
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

		$now = new DateTime( );

		if($timezone){
			$now->setTimezone( new DateTimeZone( $timezone ) );
		}

		// deal with this_year and this_easter
		if(stristr($t, '%') != FALSE){
			$scode = array('%this_year%', '%this_easter%');
			$swap = array(date('Y'), date('Y-m-d', easter_date(date('Y'))));

			if( strtotime( str_replace($scode, $swap, $t)) <  strtotime("now") ){
				$swap = array(date('Y', strtotime('+1 year')), date('Y-m-d', easter_date(date('Y', strtotime('+1 year')))));
			}
			$t = str_replace($scode, $swap, $t);
		}

		$error = '';
		try {
		    $target = new DateTime( $t );
				if($timezone){
					$target->setTimezone( new DateTimeZone( $timezone ) );
				}
		} catch (Exception $e) {
		    $error = $e->getMessage();
		    $target = $now;
		}

		$diffSecs = $target->getTimestamp() - $now->getTimestamp();

		$day = $target->format('d');
		$month = $target->format('m');
		$year = $target->format('Y');
		$hour = $target->format('H');
		$min = $target->format('i');
		$sec = $target->format('s');

		// interval
		$interval = $now->diff($target);

		// next digits
		$pop_day = new DateInterval('P1D');
		$tomorrow_target = $target->sub($pop_day);
		$tomorrow_interval = $now->diff($tomorrow_target);

		// countdown digits
		$date_arr = array(
			'secs' => $interval->s,
			'mins' => $interval->i,
			'hours' => $interval->h,
			'days' => $interval->d,
			'months' => $interval->m,
			'years' => $interval->y,
	 	);

		$next_arr = array(
			'next_day' => $tomorrow_interval->d
		);

		if($interval->m != $tomorrow_interval->m){
			$next_arr['next_month'] = $tomorrow_interval->m;
		}

		if($interval->y != $tomorrow_interval->y){
			$next_arr['next_year'] = $tomorrow_interval->y;
		}

	  // deal with omit years
		if(!empty($interval->y) && $omityears != 'false'){
			// if no months, calculate everyting with total days
			if($omitmonths != 'false'){
				$date_arr['days'] = $interval->days;
				$next_arr['next_day'] = $tomorrow_interval->days;
			}
			// add years to months.
			else{
				$date_arr['months'] = $date_arr['months'] + ($interval->y * 12);
				if(isset($next_arr['next_month'])){
					$next_arr['next_month'] = $next_arr['next_month'] + ($tomorrow_interval->y * 12);
				}
				else{
					$next_arr['next_month'] = ($tomorrow_interval->y * 12);
				}

			}
		}

		// deal with omit months
		if(!empty($date_arr['months']) && $omitmonths != 'false'){
			if(!empty($interval->y) && $omityears == 'false'){
				$pop_years = new DateInterval('P'.$interval->y.'Y');
				$adjusted_target = $target->sub($pop_years);
				$interval = $now->diff($adjusted_target);

				if(!empty($next_arr['next_year'])){
					$pop_tomorrow_time = new DateInterval('P'.$interval->y.'Y1D');
					$adjusted_tomorrow = $target->sub($pop_tomorrow_time);
					$tomorrow_interval = $now->diff($adjusted_tomorrow);
				}
			}
			$date_arr['days'] = $interval->days;
			$next_arr['next_day'] = $tomorrow_interval->days;
		}

		//but what if months where empty, but next day we have months...
		else if(!empty($next_arr['next_month']) && $omitmonths != 'false'){
			$next_arr['next_day'] = $tomorrow_interval->days;
		}

		// just days
		if($omitweeks != 'false'){
			$dash_omitweeks_class = 'omitweeks';
		}
		//weeks and days
		else{
			$dash_omitweeks_class = '';

			$date_arr['weeks'] = (int) floor( $date_arr['days'] / 7 );
			$date_arr['days'] = (int) floor($date_arr['days'] %7);

			$next_week = (int) floor( $next_arr['next_day'] / 7 );
			if($date_arr['weeks'] != $next_week ){
				$next_arr['next_week'] = $next_week;
			}
			$next_arr['next_day'] = (int) floor($next_arr['next_day'] %7);
		}

		if( $diffSecs <= 0 && $launchtarget != 'countup'){
			$date_arr = array(
				'secs' => 0,
				'mins' => 0,
				'hours' => 0,
				'days' => 0,
				'weeks' => 0,
				'months' => 0,
				'years' => 0,
		 	);
		}

		// break numbers into digit elements
		foreach ($date_arr as $i => $d) {
			if($i == 'days' && $next_arr['next_day'] > 99){
				if($d > 9){
					$d = sprintf("%02d", $d);
				}
				if($d < 10){
					$d = sprintf("%03d", $d);
				}
			}
			//single digits get a padding zero
			else if($d < 10){
				$d = sprintf("%02d", $d);
			}
			$date_arr[$i] = array_map('intval', str_split($d));
		}


		if(is_numeric($width)){
			$width .= 'px';
		}
		if(is_numeric($height)){
			$height .= 'px';
		}
		$tminus = $error.'<div id="'.$id.'-countdown" class="tminus_countdown" style="width:'.$width.'; height:'.$height.';">';
		$tminus .= '<div class="'.$style.'-countdown '.$dash_omitweeks_class.'">';
		$tminus .= '<div id="'.$id.'-tophtml" class="'.$style.'-tophtml">';
	    if($before){
	        $tminus .=  htmlspecialchars_decode($before);
	    }
		$tminus .=  '</div>';

		//drop in the dashboard
		$tminus .=  '<div id="'.$id.'-dashboard" class="'.$style.'-dashboard">';

		if(!empty($date_arr['years']) && $omityears == 'false'){
			$class = $style.'-dash '.$style.'-years_dash';
			$next_year = '';
			if(isset($next_arr['next_year'])){
				$next_year = 'data-next="'.$next_arr['next_year'].'"';
			}
			$tminus .=  '<div class="'.$class.'" '.$next_year.'"><div class="'.$style.'-dash_title">'.$years.'</div>';
			foreach( $date_arr['years'] AS $digit ){
				$tminus .=  '<div class="'.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
			}
			$tminus .= '</div>';
		}

		if(!empty($date_arr['months']) && $omitmonths == 'false'){
			$class = $style.'-dash '.$style.'-months_dash';
			$next_month = '';
			if(isset($next_arr['next_month'])){
				$next_month = 'data-next="'.$next_arr['next_month'].'"';
			}
			$tminus .=  '<div class="'.$class.'" '.$next_month.'><div class="'.$style.'-dash_title">'.$months.'</div>';
			foreach( $date_arr['months'] AS $digit ){
				$tminus .=  '<div class="'.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
			}
			$tminus .= '</div>';
		}

		if($omitweeks == 'false'){
			$wclass = $style.'-dash '.$style.'-weeks_dash';
			$next_week = '';
			if(isset($next_arr['next_week'])){
				$next_week = 'data-next="'.$next_arr['next_week'].'"';
			}
			$tminus .=  '<div class="'.$wclass.'" '.$next_week.'><div class="'.$style.'-dash_title">'.$weeks.'</div>';
			foreach( $date_arr['weeks'] AS $digit ){
				$tminus .=  '<div class="'.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
			}
			$tminus .= '</div>';
		}

		$dclass = $style.'-dash '.$style.'-days_dash';
		$tminus .= '<div class="'.$dclass.'" data-next="'.$next_arr['next_day'].'"><div class="'.$style.'-dash_title">'.$days.'</div>';
		foreach( $date_arr['days'] AS $digit ){
			$tminus .=  '<div class="'.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
		}
		$tminus .= '</div>';

		$tminus .= '<div class="'.$style.'-dash '.$style.'-hours_dash">';
			$tminus .= '<div class="'.$style.'-dash_title">'.$hours.'</div>';
			foreach( $date_arr['hours'] AS $digit ){
				$tminus .=  '<div class="'.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
			}
		$tminus .= '</div>';

		$tminus .= '<div class="'.$style.'-dash '.$style.'-minutes_dash">';
			$tminus .= '<div class="'.$style.'-dash_title">'.$minutes.'</div>';
			foreach( $date_arr['mins'] AS $digit ){
				$tminus .=  '<div class="'.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
			}
		$tminus .= '</div>';

		if($omitseconds == 'false'){
				$tminus .= '<div class="'.$style.'-dash '.$style.'-seconds_dash">';
					$tminus .= '<div class="'.$style.'-dash_title">'.$seconds.'</div>';
					foreach( $date_arr['secs'] AS $digit ){
						$tminus .=  '<div class="'.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
					}
				$tminus .= '</div>';
		}

		$tminus .= '</div>'; //close the dashboard

		$tminus .= '<div id="'.$id.'-bothtml" class="'.$style.'-bothtml">';
		if($after){
			$tminus .= htmlspecialchars_decode($after);
		}
		$tminus .= '</div></div></div>';

		$lt = date( 'n/j/Y H:i:s', strtotime(current_time('mysql')) );

		if(is_numeric($launchwidth)){
			$launchwidth .= 'px';
		}
		if(is_numeric($launchheight)){
			$launchheight .= 'px';
		}

		$content = json_encode(do_shortcode($content));
		$content = str_replace(array('\r\n', '\r', '\n<p>', '\n', '""'), '', $content);

		$tminus .= "<script language='javascript' type='text/javascript'>
			jQuery(document).ready(function($) {
				$('#".$id."-dashboard').tminusCountDown({
					targetDate: {
						'day': 	".$day.",
						'month': ".$month.",
						'year': ".$year.",
						'hour': ".$hour.",
						'min': 	".$min.",
						'sec': 	".$sec.",
						'localtime': '".$lt."'
					},
					style: '".$style."',
					id: '".$id."',
					event_id: '".$event_id."',
					launchtarget: '".$launchtarget."'";

		if(!empty($content)){
			$tminus .= ", onComplete: function() {
								$('#".$id."-".$launchtarget."').css({'width' : '".$launchwidth."', 'height' : '".$launchheight."'});
								$('#".$id."-".$launchtarget."').html(".$content.");
							}";
		}
		$tminus .= "});
			});
		</script>";

		return $tminus;
	}

	function tminusevents_callback() {
	    $nonce = $_POST['countdownNonce'];
	    if ( ! wp_verify_nonce( $nonce, 'tountajax-countdownonce-nonce' ) ){
			die ( 'Busted!');
		}

		esc_attr( WP_TminusEvents::tminusEvents( $_POST['event_id'] ));
		wp_die();
	}

}
$WP_TMinus = new WP_TMinus;

?>
