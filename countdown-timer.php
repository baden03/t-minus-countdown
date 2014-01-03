<?php
/*
Plugin Name: T(-) Countdown
Text Domain: tminus
Domain Path: /languages
Plugin URI: http://plugins.twinpictures.de/plugins/t-minus-countdown/
Description: Display and configure multiple T(-) Countdown timers using a shortcode or sidebar widget.
Version: 2.2.12
Author: twinpictures, baden03
Author URI: http://www.twinpictures.de/
License: GPL2
*/

/*  Copyright 2014 Twinpictures (www.twinpictures.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//widget scripts
function countdown_scripts(){
		$current_version = '2.2.12';
		$installed_version  = get_option('t-minus_version');
		
		if($current_version != $installed_version){
			//delete the old style system
			delete_option( 't-minus_styles' );
			//add version check
			update_option('t-minus_version', '2.2.12');
			
			//reset rockstar option
			delete_option( 'rockstar' );
			add_option('rockstar', '');
		}
		$styles_arr = array("hoth","TIE-fighter","c-3po","c-3po-mini","carbonite","carbonite-responsive","carbonlite","cloud-city","darth","jedi", "sith");
		add_option('t-minus_styles', $styles_arr);
		$plugin_url = plugins_url() .'/'. dirname( plugin_basename(__FILE__) );
		//wp_enqueue_script('jquery');
        if (is_admin() && $_SERVER["REQUEST_URI"] == '/wp-admin/widgets.php'){
                //jquery admin stuff
                wp_register_script('tminus-admin-script', $plugin_url.'/js/jquery.collapse.js', array ('jquery'), '1.2' );
                wp_enqueue_script('tminus-admin-script');
				
				wp_register_style('colapse-admin-css', $plugin_url.'/admin/collapse-style.css', array (), '1.0' );    
                wp_enqueue_style('colapse-admin-css');
				
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_register_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css', array (), '1.8.23' );    
				wp_enqueue_style('jquery-ui-css');
        }
		else{
				//lwtCountdown script
				wp_register_script('countdown-script', $plugin_url.'/js/jquery.t-countdown.js', array ('jquery'), '1.5' );
                wp_enqueue_script('countdown-script');
				
				//register all countdown styles for enqueue-as-needed
				$styles_arr = get_option('t-minus_styles');
				foreach($styles_arr as $style_name){
					wp_register_style( 'countdown-'.$style_name.'-css', $plugin_url.'/css/'.$style_name.'/style.css', array(), '1.3' );
				}
		}
}
add_action( 'init', 'countdown_scripts' );

//style folders array
function folder_array($path, $exclude = ".|..") {
	if(is_dir($path)){
		$dh = opendir($path);
		$exclude_array = explode("|", $exclude);
		$result = array();
		while(false !== ( $file = readdir($dh) ) ) { 
			if( !in_array( strtolower( $file ), $exclude_array) ){
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
		//insert some style into your life
		$style = empty($instance['style']) ? 'jedi' : apply_filters('widget_style', $instance['style']);
		wp_enqueue_style( 'countdown-'.$style.'-css' );
		
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$tophtml = empty($instance['tophtml']) ? ' ' : apply_filters('widget_tophtml', stripslashes($instance['tophtml']));
        $bothtml = empty($instance['bothtml']) ? ' ' : apply_filters('widget_bothtml', stripslashes($instance['bothtml']));
        $launchhtml = empty($instance['launchhtml']) ? ' ' : apply_filters('widget_launchhtml', $instance['launchhtml']);
        $launchtarget = empty($instance['launchtarget']) ? 'After Countdown' : apply_filters('widget_launchtarget', $instance['launchtarget']);
		
		$day = empty($instance['day']) ? 20 : apply_filters('widget_day', $instance['day']);
		$month = empty($instance['month']) ? 12 : apply_filters('widget_month', $instance['month']);
		$year = empty($instance['year']) ? 2014 : apply_filters('widget_year', $instance['year']);
		
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
		
		//now
		//$now = time() + ( get_option( 'gmt_offset' ) * 3600);
		//$now = current_time('timestamp');
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
		//$t = date( 'n/j/Y H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600));
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
				jQuery(document).ready(function() {
					jQuery('#<?php echo $args['widget_id']; ?>-dashboard').countDown({	
						targetDate: {
							'day': 	<?php echo date('d', $target); ?>,
							'month': 	<?php echo date('m', $target); ?>,
							'year': 	<?php echo date('Y', $target); ?>,
							'hour': 	<?php echo $hour; ?>,
							'min': 	<?php echo $min; ?>,
							'sec': 	<?php echo $sec; ?>,
							'localtime':	'<?php echo $t; ?>',
							'mysqltime':  '<?php echo current_time('mysql'); ?>'
						},
						style: '<?php echo $style; ?>',
						launchtarget: '<?php echo $launchdiv; ?>',
						omitWeeks: <?php echo $omitweeks;
										if($launchhtml){
											echo ", onComplete: function() { jQuery('#".$args['widget_id']."-".$launchdiv."').html('".do_shortcode($launchhtml)."'); }";
										}
									?>
					});
				});
			</script>
			<?php
		}
    }

    /** Update */
	/*
    function update($new_instance, $old_instance) {
		$instance = array_merge($old_instance, $new_instance);
		if($instance['isrockstar'] == 'rockstar'){
			update_option('rockstar', 'rockstar');
		}
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return array_map('mysql_real_escape_string', $instance);
    }
	*/
	
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

		    $styles_arr = folder_array(WP_PLUGIN_DIR.'/'. dirname( plugin_basename(__FILE__) ).'/css');
			update_option('t-minus_styles', $styles_arr);
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
				<p><?php printf(__('%sT(-) Countdown Control%s is a premium countdown plugin that includes the ability to schedule and manage multiple recurring T(-) Countdowns... the Jedi way.', 'tminus'), '<a href="http://plugins.twinpictures.de/premium-plugins/t-minus-countdown-control/" target="blank" title="(-) Countdown Control">', '</a>'); ?></p>
		</div>
		<?php
    }
} // class CountDownTimer

// register CountDownTimer widget
add_action('widgets_init', create_function('', 'return register_widget("CountDownTimer");'));


//code for the footer
add_action('wp_footer', 'print_my_script');
 
function print_my_script() {
	global $add_my_script;
 
	if ( ! $add_my_script ){
		return;
	}
	
	?>
		<script language="javascript" type="text/javascript">
			jQuery(document).ready(function() {
	<?php			
	foreach((array) $add_my_script as $script){
	?>
		jQuery('#<?php echo $script['id']; ?>-dashboard').countDown({	
			targetDate: {
				'day': 	<?php echo $script['day']; ?>,
				'month': <?php echo $script['month']; ?>,
				'year': <?php echo $script['year']; ?>,
				'hour': <?php echo $script['hour']; ?>,
				'min': 	<?php echo $script['min']; ?>,
				'sec': 	<?php echo $script['sec']; ?>,
				'localtime': '<?php echo $script['localtime']; ?>',
				'mysqltime':  '<?php echo current_time('mysql'); ?>'
			},
			style: '<?php echo $script['style']; ?>',
			launchtarget: '<?php echo $script['launchtarget']; ?>',
			omitWeeks: <?php echo $script['omitweeks'];
				if($script['content']){
					echo ", onComplete: function() {
						jQuery('#".$script['id']."-".$script['launchtarget']."').css({'width' : '".$script['launchwidth']."', 'height' : '".$script['launchheight']."'});
						jQuery('#".$script['id']."-".$script['launchtarget']."').html('".do_shortcode($script['content'])."');
					}";
				}?>
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
		't' => '20-12-2013 20:12:20',
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
	), $atts));
	
	//enqueue style that was already registerd
	wp_enqueue_style( 'countdown-'.$style.'-css' );
		
	//$now = time() + ( get_option( 'gmt_offset' ) * 3600);
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

	//$t = date( 'n/j/Y H:i:s', gmmktime() + ( get_option( 'gmt_offset' ) * 3600));
	$t = date( 'n/j/Y H:i:s', strtotime(current_time('mysql')) );
	
	if(is_numeric($launchwidth)){
		$launchwidth .= 'px';
	}
	if(is_numeric($launchheight)){
		$launchheight .= 'px';
	}
	$content = mysql_real_escape_string( $content);
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
			'launchheight' => $launchheight
		);
	}
	else{
		$tminus .= "<script language='javascript' type='text/javascript'>
			jQuery(document).ready(function() {
				jQuery('#".$id."-dashboard').countDown({	
					targetDate: {
						'day': 	".$day.",
						'month': ".$month.",
						'year': ".$year.",
						'hour': ".$hour.",
						'min': 	".$min.",
						'sec': 	".$sec.",
						'localtime': '".$t."',
						'mysqltime':  '".current_time('mysql')."'
					},
					style: '".$style."',
					launchtarget: '".$launchtarget."',
					omitWeeks: ".$omitweeks;
					
		if($content){
			$tminus .= ", onComplete: function() {
								jQuery('#".$id."-".$launchtarget."').css({'width' : '".$launchwidth."', 'height' : '".$launchheight."'});
								jQuery('#".$id."-".$launchtarget."').html('".do_shortcode($content)."');	
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