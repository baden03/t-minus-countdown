<?php
/*
Plugin Name: T(-) Countdown
Text Domain: tminus
Domain Path: /languages
Plugin URI: http://plugins.twinpictures.de/plugins/t-minus-countdown/
Description: Display and configure multiple T(-) Countdown timers using a shortcode or sidebar widget.
Version: 2.3.0e
Author: twinpictures, baden03
Author URI: http://www.twinpictures.de/
License: GPL2
*/

class WP_TMinusCD {
	var $plugin_name = 'T(-) Countdown';
	var $version = '2.3.0e';
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
	);
	
	var $license_name = 'WP_tminus_countdown_license';
        
	var $license_options = array(
			'tminus_event_license_key' => '',
			'tminus_event_license_status' => '',
	);
		
	function __construct() {
		$this->_set_options();
		
		// add actions
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_actions' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'wp_head', array( $this, 'plugin_head_inject' ) );
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
	}
	
	// Add link to options page from plugin list
	function plugin_actions($links) {
		$new_links = array();
		$new_links[] = '<a href="options-general.php?page='.$this->plugin_options_slug.'">' . __('Settings', $this->domain) . '</a>';
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
							<li><?php printf( __( '%sDetailed documentation%s, complete with working demonstrations of all shortcode attributes, is available for your instructional enjoyment.', 'tminus'), '<a href="http://plugins.twinpictures.de/plugins/t-minus-countdown/documentation/" target="_blank">', '</a>'); ?></li>
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
					<h3 class="handle"><?php _e( 'Level Up!' ) ?></h3>
					<div class="inside">
						<p><?php printf(__( '%sT(-) Countdown Control%s is our premium plugin that manages and schedules multiple recurring countdown timers for repeating events.', 'tminus' ), '<a href="http://plugins.twinpictures.de/premium-plugins/t-minus-countdown-control/?utm_source=t-countdown&utm_medium=plugin-settings-page&utm_content=t-countdown&utm_campaign=t-control-level-up">', '</a>'); ?></p>		
						<h4><?php _e('Reasons To Go Pro', 'tminus'); ?></h4>
						<ol>
							<li><?php _e('Schedule and manage multiple recurring countdowns', 'tminus'); ?></li>
							<li><?php _e('Highle responsive professional support', 'tminus'); ?></li>
							<li><?php printf(__('%sT(-) Countdown Control Testimonials%s', 'tminus'), '<a href="http://plugins.twinpictures.de/testimonial/t-countdown-control-testimonias/" target="_blank">', '</a>'); ?></li>
						</ol>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		
	<?php
	}
	
	function _set_options() {
		// set options
		$saved_options = get_option( $this->options_name );

		// backwards compatible (old values)
		if ( empty( $saved_options ) ) {
			$saved_options = get_option( $this->domain . 'options' );
		}
		
		// set all options
		if ( ! empty( $saved_options ) ) {
			foreach ( $this->options AS $key => $option ) {
				$this->options[ $key ] = ( empty( $saved_options[ $key ] ) ) ? '' : $saved_options[ $key ];
			}
		}
	}
	
}
$WP_TMinusCD = new WP_TMinusCD;
	
//set global vars
add_action( 'wp_head', 'tminus_js_vars' );
function tminus_js_vars(){
	echo "<script type='text/javascript'>\n";
	$plugin_url = plugins_url() .'/'. dirname( plugin_basename(__FILE__) );
	echo "var tminusnow = '".$plugin_url."/js/now.php';\n";
	echo "</script>";
}
	
//load scripts on the widget admin page
add_action( 'admin_enqueue_scripts', 'tminus_admin_scripts');
function tminus_admin_scripts($hook){		
		if( $hook == 'widgets.php' ){
				//jquery datepicker
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_register_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.css', array (), '1.10.4' );    
				wp_enqueue_style('jquery-ui-css');
		
				$plugin_url = plugins_url() .'/'. dirname( plugin_basename(__FILE__) );
				
				//jquery widget scripts
				wp_register_script('tminus-admin-script', $plugin_url.'/js/jquery.collapse.js', array ('jquery'), '1.2.1' );
				wp_enqueue_script('tminus-admin-script');
						
				wp_register_style('collapse-admin-css', $plugin_url.'/admin/collapse-style.css', array (), '1.0' );    
				wp_enqueue_style('collapse-admin-css');
		}
}

//load front-end countdown scripts
add_action('wp_enqueue_scripts', 'countdown_scripts' );
function countdown_scripts(){
		$plugin_url = plugins_url() .'/'. dirname( plugin_basename(__FILE__) );
		
		//lwtCountdown script
		wp_register_script('countdown-script', $plugin_url.'/js/jquery.t-countdown.js', array ('jquery'), '1.5.4' );
		wp_enqueue_script('countdown-script');
}

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
		print_r($result); 
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
		
		$style = empty($instance['style']) ? 'jedi' : apply_filters('widget_style', $instance['style']);		
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$tophtml = empty($instance['tophtml']) ? ' ' : apply_filters('widget_tophtml', stripslashes($instance['tophtml']));
		$bothtml = empty($instance['bothtml']) ? ' ' : apply_filters('widget_bothtml', stripslashes($instance['bothtml']));
		$launchhtml = empty($instance['launchhtml']) ? ' ' : apply_filters('widget_launchhtml', $instance['launchhtml']);
		$launchtarget = empty($instance['launchtarget']) ? 'After Countdown' : apply_filters('widget_launchtarget', $instance['launchtarget']);
		
		$day = empty($instance['day']) ? 20 : apply_filters('widget_day', $instance['day']);
		$month = empty($instance['month']) ? 12 : apply_filters('widget_month', $instance['month']);
		$year = empty($instance['year']) ? 2015 : apply_filters('widget_year', $instance['year']);
		
		$date = empty($instance['date']) ? $year.'-'.$month.'-'.$day : apply_filters('widget_date', $instance['date']);
		$hour = empty($instance['hour']) ? 20 : apply_filters('widget_hour', $instance['hour']);
		$min = empty($instance['min']) ? 12 : apply_filters('widget_min', $instance['min']);
		$sec = empty($instance['sec']) ? 20 : apply_filters('widget_sec', $instance['sec']);
		
		$weektitle = empty($instance['weektitle']) ? __('weeks', 'tminus') : apply_filters('widget_weektitle', stripslashes($instance['weektitle']));
		$daytitle = empty($instance['daytitle']) ? __('days', 'tminus') : apply_filters('widget_daytitle', stripslashes($instance['daytitle']));
		$hourtitle = empty($instance['hourtitle']) ? __('hours', 'tminus') : apply_filters('widget_hourtitle', stripslashes($instance['hourtitle']));
		$mintitle = empty($instance['mintitle']) ? __('minutes', 'tminus') : apply_filters('widget_mintitle', stripslashes($instance['mintitle']));
		$sectitle = empty($instance['sectitle']) ? __('seconds', 'tminus') : apply_filters('widget_sectitle', stripslashes($instance['sectitle']));
		
		$omitweeks = empty($instance['omitweeks']) ? 'false' : apply_filters('widget_omitweeks', $instance['omitweeks']);
		$jsplacement = empty($instance['jsplacement']) ? 'footer' : apply_filters('widget_jsplacement', $instance['jsplacement']);
		
		$event_id = '';
		
		//insert some style into your life
		$style_file_url = plugins_url('/css/'.$style.'/style.css', __FILE__);
		
		if ( file_exists( __DIR__ .'/css/'.$style.'/style.css' ) ) {
			if (! wp_style_is( 'countdown-'.$style.'-css', 'registered' )) {
				wp_register_style( 'countdown-'.$style.'-css', $style_file_url, array(), '2.0');	
			}
			wp_enqueue_style( 'countdown-'.$style.'-css' );
		}
	
		//now
		$now = strtotime(current_time('mysql'));

		//target		
		$target = strtotime( $date.' '.$hour.':'.$min.':'.$sec );
		
		//difference in seconds
		$diffSecs = $target - $now;
		
		//countdown digits
		$date = array();
		$date['secs'] = $diffSecs % 60;
		$date['mins'] = floor($diffSecs/60)%60;
		$date['hours'] = floor($diffSecs/60/60)%24;
		if($omitweeks == 'false'){
		    $date['days'] = floor($diffSecs/60/60/24)%7;
		}
		else{
		    $date['days'] = floor($diffSecs/60/60/24); 
		}
		$date['weeks']	= floor($diffSecs/60/60/24/7);
	
		foreach ($date as $i => $d) {
			$d1 = $d%10;
			//53 = 3
			//153 = 3
			if($d < 100){
				$d2 = ($d-$d1) / 10;
				//53 = 50 / 10 = 5
				$d3 = 0;
			}
			else{
				$dr = $d%100;
				//153 = 53
				//345 = 45
				$dm = $d-$dr;
				//153 = 100
				//345 = 300
				$d2 = ($d-$dm-$d1) / 10;
				//153 = 50 / 10 = 5
				//345 = 40 / 10 = 4
				$d3 = $dm / 100;
			}
			/* here is where the 1000's support might go... someday. */
			
			//now assign all the digits to the array
			$date[$i] = array(
				(int)$d3,
				(int)$d2,
				(int)$d1,
				(int)$d
			);
		}
		
		
        echo $before_widget;
        if ( $title ){
            echo $before_title . $title . $after_title;
        }
		echo '<div id="'.$args['widget_id'].'-widget">';
		echo '<div id="'.$args['widget_id'].'-tophtml" class="'.$style.'-tophtml" >';
        if($tophtml){
            echo stripslashes($tophtml); 
        }
		echo '</div>';
		
		//drop in the dashboard
		echo '<div id="'.$args['widget_id'].'-dashboard" class="'.$style.'-dashboard">';
		
			if($omitweeks == 'false'){
				//set up correct style class for double or triple digit love
				$wclass = $style.'-dash '.$style.'-weeks_dash';
				if( abs($date['weeks'][0]) > 0 ){
					$wclass = $style.'-tripdash '.$style.'-weeks_trip_dash';
				}
			
				echo '<div class="'.$wclass.'">
						<span class="'.$style.'-dash_title">'.$weektitle.'</span>';
						//show third week digit if the number of weeks is greater than 99
				if( abs($date['weeks'][0]) > 0 ){
					echo '<div class="'.$style.'-digit">'.$date['weeks'][0].'</div>';
				}
				echo '<div class="'.$style.'-digit">'.$date['weeks'][1].'</div>
						<div class="'.$style.'-digit">'.$date['weeks'][2].'</div>
					</div>'; 
			}
					
			//set up correct style class for double or triple digit love
			$dclass = $style.'-dash '.$style.'-days_dash';
			if($omitweeks == 'true' && abs($date['days'][3]) > 99){
				$dclass = $style.'-tripdash '.$style.'-days_trip_dash';
			}
			
			echo '<div class="'.$dclass.'">
					<span class="'.$style.'-dash_title">'.$daytitle.'</span>';
			//show third day digit if there are NO weeks and the number of days is greater that 99
			if($omitweeks == 'true' && abs($date['days'][3]) > 99){
				echo '<div class="'.$style.'-digit">'.$date['days'][0].'</div>';
			}
			echo '<div class="'.$style.'-digit">'.$date['days'][1].'</div>
				<div class="'.$style.'-digit">'.$date['days'][2].'</div>
			</div>
	
			<div class="'.$style.'-dash '.$style.'-hours_dash">
				<span class="'.$style.'-dash_title">'.$hourtitle.'</span>
				<div class="'.$style.'-digit">'.$date['hours'][1].'</div>
				<div class="'.$style.'-digit">'.$date['hours'][2].'</div>
			</div>
	
			<div class="'.$style.'-dash '.$style.'-minutes_dash">
				<span class="'.$style.'-dash_title">'.$mintitle.'</span>
				<div class="'.$style.'-digit">'.$date['mins'][1].'</div>
				<div class="'.$style.'-digit">'.$date['mins'][2].'</div>
			</div>
	
			<div class="'.$style.'-dash '.$style.'-seconds_dash">
				<span class="'.$style.'-dash_title">'.$sectitle.'</span>
				<div class="'.$style.'-digit">'.$date['secs'][1].'</div>
				<div class="'.$style.'-digit">'.$date['secs'][2].'</div>
			</div>
        </div>'; //close the dashboard
		
        echo '<div id="'.$args['widget_id'].'-bothtml" class="'.$style.'-bothtml">';
        if($bothtml){
            echo  stripslashes($bothtml);    
        }
		echo '</div>';
		echo '</div>';
		echo $after_widget;
		
		$t = date( 'n/j/Y H:i:s', strtotime(current_time('mysql')) );
		
		//launch div
		$launchdiv = "";
		if($launchtarget == "Above Countdown"){
			$launchdiv = "tophtml";
		}
		else if($launchtarget == "Below Countdown"){
			$launchdiv = "bothtml";
		}
		else if($launchtarget == "Entire Widget"){
			$launchdiv = "widget";
		}
		else if($launchtarget == "Count Up"){
			$launchdiv = "countup";
		}

		if($jsplacement == "footer"){
			$add_my_script[$args['widget_id']] = array(
				'id' => $args['widget_id'],
				'day' => date('d', $target),
				'month' => date('m', $target),
				'year' => date('Y', $target),
				'hour' => $hour,
				'min' => $min,
				'sec' => $sec,
				'localtime' => $t,
				'style' => $style,
				'event_id' => $event_id,
				'omitweeks' => $omitweeks,
				'content' => trim($launchhtml),
				'launchtarget' => $launchdiv,
				'launchwidth' => 'auto',
				'launchheight' => 'auto'
			);
		}
		else{
			?>            
			<script language="javascript" type="text/javascript">
				jQuery(document).ready(function($) {
					$('#<?php echo $args['widget_id']; ?>-dashboard').countDown({	
						targetDate: {
							'day': 	<?php echo date('d', $target); ?>,
							'month': 	<?php echo date('m', $target); ?>,
							'year': 	<?php echo date('Y', $target); ?>,
							'hour': 	<?php echo $hour; ?>,
							'min': 	<?php echo $min; ?>,
							'sec': 	<?php echo $sec; ?>,
							'localtime':	'<?php echo $t; ?>',
						},
						style: '<?php echo $style; ?>',
						launchtarget: '<?php echo $launchdiv; ?>',
						omitWeeks: '<?php echo $omitweeks; ?>'
								<?php
										if($launchhtml){
											echo ", onComplete: function() { $('#".$args['widget_id']."-".$launchdiv."').html('".do_shortcode($launchhtml)."'); }";
										}
								?>
					});
				});
			</script>
			<?php
		}
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
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		if($instance['isrockstar'] == 'rockstar'){
			update_option('rockstar', 'rockstar');
		}
		return $instance;
	}

    /** Form */
    function form($instance) {
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$day = empty($instance['day']) ? 12 : apply_filters('widget_day', $instance['day']);
		if($day > 31){
			$day = 31;
		}
		$month = empty($instance['month']) ? 12 : apply_filters('widget_month', $instance['month']);
		if($month > 12){
			$month = 12;
		}
		$year = empty($instance['year']) ? 2014 : apply_filters('widget_year', $instance['year']);
		$date = empty($instance['date']) ? $year.'-'.$month.'-'.$day : apply_filters('widget_date', $instance['date']);
		$hour = empty($instance['hour']) ? 12 : apply_filters('widget_hour', $instance['hour']);
		if($hour > 23){
			$hour = 23;
		}
		$min = empty($instance['min']) ? 12 : apply_filters('widget_min', $instance['min']);
		if($min > 59){
			$min = 59;
		}
		$sec = empty($instance['sec']) ? 12 : apply_filters('widget_sec', $instance['sec']);
		if($sec > 59){
			$sec = 59;
		}
		$omitweeks = empty($instance['omitweeks']) ? 'false' : apply_filters('widget_omitweeks', $instance['omitweeks']);
		$style = empty($instance['style']) ? 'jedi' : apply_filters('widget_style', $instance['style']);
		$jsplacement = empty($instance['jsplacement']) ? 'footer' : apply_filters('widget_jsplacement', $instance['jsplacement']);

		$weektitle = empty($instance['weektitle']) ? __('weeks', 'tminus') : apply_filters('widget_weektitle', stripslashes($instance['weektitle']));
		$daytitle = empty($instance['daytitle']) ? __('days', 'tminus') : apply_filters('widget_daytitle', stripslashes($instance['daytitle']));
		$hourtitle = empty($instance['hourtitle']) ? __('hours', 'tminus') : apply_filters('widget_hourtitle', stripslashes($instance['hourtitle']));
		$mintitle = empty($instance['mintitle']) ? __('minutes', 'tminus') : apply_filters('widget_mintitle', stripslashes($instance['mintitle']));
		$sectitle = empty($instance['sectitle']) ? __('seconds', 'tminus') : apply_filters('widget_sectitle', stripslashes($instance['sectitle']));
		
		$isrockstar = get_option('rockstar');
		
		if($isrockstar){
			//rockstar features
			$tophtml = empty($instance['tophtml']) ? ' ' : apply_filters('widget_tophtml', stripslashes($instance['tophtml']));
			$bothtml = empty($instance['bothtml']) ? ' ' : apply_filters('widget_bothtml', stripslashes($instance['bothtml']));
			$launchhtml = empty($instance['launchhtml']) ? ' ' : apply_filters('widget_launchhtml', stripslashes($instance['launchhtml']));
			$launchtarget = empty($instance['launchtarget']) ? 'After Counter' : apply_filters('widget_launchtarget', $instance['launchtarget']);
		}
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'tminus'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('date'); ?>"><?php _e('Target Date:', 'tminus'); ?></label><br/><input style="width: 90px;" id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" type="text" value="<?php echo $date; ?>" class="t-datepicker"/></p>
		<p><label for="<?php echo $this->get_field_id('hour'); ?>"><?php _e('Target Time (HH:MM:SS):', 'tminus'); ?></label><br/><input style="width: 30px;" id="<?php echo $this->get_field_id('hour'); ?>" name="<?php echo $this->get_field_name('hour'); ?>" type="text" value="<?php echo $hour; ?>" />:<input style="width: 30px;" id="<?php echo $this->get_field_id('min'); ?>" name="<?php echo $this->get_field_name('min'); ?>" type="text" value="<?php echo $min; ?>" />:<input style="width: 30px;" id="<?php echo $this->get_field_id('sec'); ?>" name="<?php echo $this->get_field_name('sec'); ?>" type="text" value="<?php echo $sec; ?>" /></p>
		<?php
			//Omit Week Selector
            $negative = '';
            $positive = '';
            if($omitweeks == 'false'){
                $negative = 'CHECKED';
            }else{
                $positive = 'CHECKED'; 
            }
			
            //JS Placement Selector
            $foot = '';
            $inline = '';
            if($jsplacement == 'footer'){
                $foot = 'CHECKED';
            }else{
                $inline = 'CHECKED'; 
            }
		?>
		<p><?php _e('Omit Weeks:', 'tminus'); ?> <input id="<?php echo $this->get_field_id('omitweeks'); ?>-no" name="<?php echo $this->get_field_name('omitweeks'); ?>" type="radio" <?php echo $negative; ?> value="false" /><label for="<?php echo $this->get_field_id('omitweeks'); ?>-no"> <?php _e('No', 'tminus'); ?> </label> <input id="<?php echo $this->get_field_id('omitweeks'); ?>-yes" name="<?php echo $this->get_field_name('omitweeks'); ?>" type="radio" <?php echo $positive; ?> value="true" /> <label for="<?php echo $this->get_field_id('omitweeks'); ?>-yes"> <?php _e('Yes', 'tminus'); ?></label></p>
		<p><?php _e('Style:', 'tminus'); ?> <select name="<?php echo $this->get_field_name('style'); ?>" id="<?php echo $this->get_field_name('style'); ?>">
		<?php	

			$options = get_option('WP_TMCC_options');
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
		<p><?php _e('Inject Script:', 'tminus'); ?> <input id="<?php echo $this->get_field_id('jsplacement'); ?>-foot" name="<?php echo $this->get_field_name('jsplacement'); ?>" type="radio" <?php echo $foot; ?> value="footer" /><label for="<?php echo $this->get_field_id('jsplacement'); ?>-foot"> <?php _e('Footer', 'tminus'); ?> </label> <input id="<?php echo $this->get_field_id('jsplacement'); ?>-inline" name="<?php echo $this->get_field_name('jsplacement'); ?>" type="radio" <?php echo $inline; ?> value="inline" /> <label for="<?php echo $this->get_field_id('jsplacement'); ?>-inline"> <?php _e('Inline', 'tminus'); ?></label></p>
		
		<input class="isrockstar" id="<?php echo $this->get_field_id('isrockstar'); ?>" name="<?php echo $this->get_field_name('isrockstar'); ?>" type="hidden" value="<?php echo $isrockstar; ?>" />
		<?php
		if($isrockstar){
			echo __('Rockstar Features', 'tminus').'<br/>';
		}
		else{
				$like_it_arr = array('makes me feel warm and fuzzy inside... in a good way', 'restores my faith in humanity... if only for a fleating second', 'rocked my world and is totally worth 3 bucks', 'offered me a positive vision of future living', 'inspires me to commit random acts of kindness', 'helped organize my life in one of the small ways that matter', 'saved me minutes if not tens of minutes writing your own solution', 'brightened my day... or darkened it since I wanted to sleep in anyway', 'is totally worth 3 bucks');
				$rand_key = array_rand($like_it_arr);
				$like_it = $like_it_arr[$rand_key];
			?>
			<p id="header-<?php echo $this->get_field_id('isrockstar'); ?>"><input class="rockstar" id="<?php echo $this->get_field_id('isrockstar'); ?>" name="<?php echo $this->get_field_name('isrockstar'); ?>" type="checkbox" value="rockstar" /> <label for="<?php echo $this->get_field_id('isrockstar'); ?>"><span title="<?php _e('check the box and save to unlock rockstar features.', 'tminus'); ?>"><?php printf( __('T(-) Countdown %s!', 'tminus'), $like_it); ?></span></label></p>
			<div id="target-<?php echo $this->get_field_id('isrockstar'); ?>" class="collapseomatic_content">
			<?php
		}
		
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
					$target_arr = array('Above Countdown', 'Below Countdown', 'Entire Widget', 'Count Up');
					foreach($target_arr as $target_name){
						$selected = "";
						if($launchtarget == $target_name){
							$selected = 'SELECTED';
						}
						echo '<option value="'.$target_name.'" '.$selected.'>'.__($target_name, "tminus").'</option>';
					}
				?>
				</select></p>
		</div>
		<br/>
		<a class="collapseomatic" id="titles<?php echo $this->get_field_id('weektitle'); ?>"><?php _e('Digit Titles', 'tminus'); ?></a>
		<div id="target-titles<?php echo $this->get_field_id('weektitle'); ?>" class="collapseomatic_content">
				<p><label for="<?php echo $this->get_field_id('weektitle'); ?>"><?php _e('How do you spell "weeks"?:', 'tminus'); ?> <input class="widefat" id="<?php echo $this->get_field_id('weektitle'); ?>" name="<?php echo $this->get_field_name('weektitle'); ?>" type="text" value="<?php echo $weektitle; ?>" /></label></p>
				<p><label for="<?php echo $this->get_field_id('daytitle'); ?>"><?php _e('How do you spell "days"?:', 'tminus'); ?> <input class="widefat" id="<?php echo $this->get_field_id('daytitle'); ?>" name="<?php echo $this->get_field_name('daytitle'); ?>" type="text" value="<?php echo $daytitle; ?>" /></label></p>
				<p><label for="<?php echo $this->get_field_id('hourtitle'); ?>"><?php _e('How do you spell "hours"?:', 'tminus'); ?> <input class="widefat" id="<?php echo $this->get_field_id('hourtitle'); ?>" name="<?php echo $this->get_field_name('hourtitle'); ?>" type="text" value="<?php echo $hourtitle; ?>" /></label></p>
				<p><label for="<?php echo $this->get_field_id('mintitle'); ?>"><?php _e('How do you spell "minutes"?:', 'tminus'); ?> <input class="widefat" id="<?php echo $this->get_field_id('mintitle'); ?>" name="<?php echo $this->get_field_name('mintitle'); ?>" type="text" value="<?php echo $mintitle; ?>" /></label></p>
				<p><label for="<?php echo $this->get_field_id('sectitle'); ?>"><?php _e('And "seconds" are spelled:', 'tminus'); ?> <input class="widefat" id="<?php echo $this->get_field_id('sectitle'); ?>" name="<?php echo $this->get_field_name('sectitle'); ?>" type="text" value="<?php echo $sectitle; ?>" /></label></p>
		</div>
	
		<?php
		}
		else{
			echo '</div>';
		}
		
		?>
		<br/>
		<a class="collapseomatic" id="tccc<?php echo $this->get_field_id('isrockstar'); ?>"><?php _e('Schedule Recurring Countdown', 'tminus'); ?></a>
		<div id="target-tccc<?php echo $this->get_field_id('isrockstar'); ?>" class="collapseomatic_content">
				<p><?php printf(__('%sT(-) Countdown Control%s is a premium countdown plugin that includes the ability to schedule and manage multiple recurring T(-) Countdowns... the Jedi way.', 'tminus'), '<a href="http://plugins.twinpictures.de/premium-plugins/t-minus-countdown-control/?utm_source=t-countdown&utm_medium=widget-settings&utm_content=t-countdown-control&utm_campaign=t-countdown-widget" target="blank" title="(-) Countdown Control">', '</a>'); ?></p>
		</div>
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
			omitWeeks: '<?php echo $script['omitweeks']; ?>'
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
		$date_arr['days'] = floor($diffSecs/60/60/24)%7;
	}
	else{
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
	$tminus = '<div id="'.$id.'-countdown" style="width:'.$width.'; height:'.$height.';">';
	$tminus .= '<div id="'.$id.'-above" class="'.$style.'-tophtml">';
    if($before){
        $tminus .=  $before; 
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
			
		$tminus .=  '<div class="'.$wclass.'"><span class="'.$style.'-dash_title">'.$weeks.'</span>';
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
			
	$tminus .= '<div class="'.$dclass.'"><span class="'.$style.'-dash_title">'.$days.'</span>';
	
	//show third day digit if there are NO weeks and the number of days is greater that 99
	//var_dump($date_arr['days']);  array(4) { [0]=> int(3) [1]=> int(3) [2]=> int(5) [3]=> int(335) }
	if($omitweeks == 'true' && $date_arr['days'][3] > 99){
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['days'][0].'</div>';
	}
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['days'][1].'</div><div class="'.$style.'-digit">'.$date_arr['days'][2].'</div>';
	$tminus .= '</div>';
	$tminus .= '<div class="'.$style.'-dash '.$style.'-hours_dash">';
		$tminus .= '<span class="'.$style.'-dash_title">'.$hours.'</span>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['hours'][1].'</div>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['hours'][2].'</div>';
	$tminus .= '</div>';
		$tminus .= '<div class="'.$style.'-dash '.$style.'-minutes_dash">';
		$tminus .= '<span class="'.$style.'-dash_title">'.$minutes.'</span>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['mins'][1].'</div>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['mins'][2].'</div>';
	$tminus .= '</div>';
		$tminus .= '<div class="'.$style.'-dash '.$style.'-seconds_dash">';
		$tminus .= '<span class="'.$style.'-dash_title">'.$seconds.'</span>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['secs'][1].'</div>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['secs'][2].'</div>';
	$tminus .= '</div>';
	$tminus .= '</div>'; //close the dashboard

	$tminus .= '<div id="'.$id.'-below" class="'.$style.'-bothtml">';
	if($after){
		$tminus .= $after;    
	}
	$tminus .= '</div></div>';
	
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

?>