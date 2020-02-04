=== T(-) Countdown ===

Contributors: twinpictures
Donate link: https://pluginoven.com/plugins/t-countdown/#donate
Tags: countdown, timer, clock, ticker, event, counter, count down, twinpictures, t minus, t-minus, t(-), t(-) countdown, t-countdown, t (-) countdown, plugin-oven, pluginoven
Requires at least: 4.9
Tested up to: 5.3.2
Stable tag: 2.4.7
Requires PHP: 7.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Display and configure multiple countdown timers in years, months, weeks, days, hours and seconds in a number of different styles.

== Description ==

T(-) Countdown is a customisable countdown timer that can display years (optional), months (optional), weeks (optional), days, hours and seconds. This plugin is the next generation of T(-) Countdown Widget. Note: a dedicated sidebar widget is not included as shortcodes can now be used in most sidebars.

== Installation ==

1. Old-school: upload the `t-countdown` folder to the `/wp-content/plugins/` directory via FTP.
1. Hipster: Ironically add T(minus) Countdown via the WordPress Plugins menu.
1. Activate the Plugin
1. Add the shortcode to a post or page.
1. Test that the this plugin meets your demanding needs.
1. Tweak the css files for premium enjoyment.
1. Rate the plugin and verify that it works at wordpress.org.

== Frequently Asked Questions ==

= How does one use the shortcode, exactly? =
A <a href='https://pluginoven.com/plugins/t-countdown/documentation/shortcode/'>complete list of shortcode options</a> has been provided to answer this exact question.

= Where can I fork this plugin and contribute changes? =
<a href='https://github.com/baden03/t-minus-countdown'>github</a>

= Where can I translate this plugin into my favorite langauge? =
<a href='https://translate.wordpress.org/projects/wp-plugins/t-countdown/'>Community translation for T(-) Countdown</a> has been set up.

= How does one pronounce T Minus? =
* Tee&mdash;As in Tea for Two, or Tee off time
* Minus&mdash;As in the opposite of plus (+)
* T Minus&mdash;As in "This is Apollo Saturn Launch Control. We've passed the 11-minute mark. Now T minus 10 minutes 54 seconds on our countdown for Apollo 11."

= I am a Social Netwookiee, does Twinpictures have a Facebook page? =
Yes, yes... <a href='https://www.facebook.com/twinpictures'>Twinpictures is on Facebook</a>.

= Does Twinpictures do the Twitter? =
Ah yes! <a href='https://twitter.com/#!/twinpictures'>@Twinpictures</a> is on the Twitter.


== Screenshots ==

1. Style: jedi
1. Style: TIE-fighter
1. Style: darth
1. Style: carbonate
1. Style: carbonate-responsive
1. Style: carbonlite
1. Style: c-3po and c-3po-mini
1. Style: cloud-city
1. Style: hoth
1. Style: naboo
1. Style: sith
1. Style: circle

== Changelog ==

= 2.4.7 =
* fixed issue with default display units and toggles in block
* added debug attribute to shortcode for troubleshooting caching issues
* uses rest-api to recalculate time if page has been cached
* manages cached pages and timezones better
* js script is minified

= 2.4.6 =
* added %this_year% and %this_easter% time placeholders
* added omitseconds fixed issue with day label
* resolved issue with timezones

= 2.4.5 =
* added Gutenberg block
* fine tuned css on some styles
* countdown will show 00:00:00 by default if already launched

= 2.4.4 =
* fixed issue if only days are being displayed

= 2.4.3 =
* check if functions exist to prevent conflicts with legacy plugin

= 2.4.2 =
* added missing screenshots
* cleaned up plugin options page

= 2.4.1 =
* deactivate legacy plugin if activated
* removed unused functions

= 2.4.0 =
* plugin relaunched as the next generation of T(-) Countdown Widget
* get_styles() is now a static function
* uses PHP DateTime function for date calculation
* new timezone attribute to target time to different timezone
* weeks are no longer limited to 3 digits
* added optional years and months with omityears and omitmonths attributes
* all styles are css optimised and were rebuilt to support more than three digits

== Upgrade Notice ==
* fixed issue with default display units and toggles in block
* added debug attribute to shortcode for troubleshooting caching issues
* uses rest-api to recalculate time if page has been cached
* manages cached pages and timezones better
* js script is minified
