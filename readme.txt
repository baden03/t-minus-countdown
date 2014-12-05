=== T(-) Countdown ===

Contributors: twinpictures, baden03
Donate link: http://plugins.twinpictures.de/plugins/t-minus-countdown/
Tags: countdown, timer, clock, ticker, widget, event, counter, count down, twinpictures, t minus, t-minus, plugin-oven, pluginoven, G2, spaceBros, littlewebtings, jQuery, javascript
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 2.2.17
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

T(-) Countdown will display a highly customizable, flash-free countdown timer as a sidebar widget or in a post or page using a shortcode.

== Description ==

T(-) Countdown will display a highly customizable jQuery countdown timer as a sidebar widget or in a post or page using a shortcode. Perfect for informing one's website visitors of an upcoming event, such as a pending space voyage. Using Jedi Mind-tricks and CSS... but mostly CSS, the countdown timer is highly customizable for your viewing pleasure. A <a href='http://plugins.twinpictures.de/plugins/t-minus-countdown/documentation/'>complete listing of shortcode options</a> are available, as well as <a href='http://wordpress.org/support/plugin/jquery-t-countdown-widget'>free community</a> support. This plug-in was inspired by littlewebthings' CountDown jQuery plugin. Intergalactic planetary thanks to g2.de, siliconstudio.com and be.net/arturex for the included css styles.

== Installation ==

1. Old-school: upload the `countdown-timer` folder to the `/wp-content/plugins/` directory via FTP.
1. Hipster: Ironically add T(minus) Countdown via the WordPress Plugins menu.
1. Activate the Plugin
1. Add the Widget to the desired sidebar in the WordPress Widgets menu.
1. Configure the `T(-) Countdown' widget options.
1. Add the shortcode to a post or page.
1. Test that the this plugin meets your demanding needs.
1. Tweak the css files for premium enjoyment.
1. Rate the plugin and verify that it works at wordpress.org.
1. Leave a comment regarding bugs, feature request or cocktail recipes at http://wordpress.org/tags/jquery-t-countdown-widget

== Frequently Asked Questions ==

= How does one use the shortcode, exactly? =
A <a href='http://plugins.twinpictures.de/plugins/t-minus-countdown/documentation/'>complete list of shortcode options</a> has been provided to answer this exact question.

= Where can I fork this plugn and contribute changes? =
<a href='http://github.com/baden03/t-minus-countdown'>github</a>

= Where can I translate this plugin into my favorite langauge? =
<a href='http://translate.twinpictures.de/projects/t-countdown'>Community translation for T(-) Countdown</a> has been set up. You are <a href='http://translate.twinpictures.de/wordpress/wp-login.php?action=register'>welcome to join</a>.

= How does one pronounce T Minus? =
* Tee&mdash;As in Tea for Two, or Tee off time
* Minus&mdash;As in the opposite of plus (+)
* T Minus&mdash;As in "This is Apollo Saturn Launch Control. We've passed the 11-minute mark. Now T minus 10 minutes 54 seconds on our countdown for Apollo 11."

= I am a Social Netwookiee, does Twinpictures have a Facebook page? =
Yes, yes... <a href='http://www.facebook.com/twinpictures'>Twinpictures is on Facebook</a>.

= Does Twinpictures do the Twitter? =
Ah yes! <a href='http://twitter.com/#!/twinpictures'>@Twinpictures</a> is on the Twitter.

= Where may one view political news that gives giggle? =
The Daily Show with John Stewart

== Screenshots ==

1. T(-) Countdown in action with styles: Darth, Jedi and Carbonite.
1. Styles: C-3PO, TIE-Fighter and Carbonlite.
1. The basic T(-) Countdown widget options.
1. An expansive view of the available Countdown widget options, provided for your viewing pleasure.
1. The magical jQuery Datepicker.

== Changelog ==

= 2.2.17 =
* addressed jQuery undefined function error on some WordPress installs
* javascript cleanup

= 2.2.16 =
* fixed TIE-Fighter style to deal with box-sizing and will now adjust width automatically
* updated the enqueue_scripts to be called using the proper hooks
* only load the widget admin scripts on the widget page
* datepicker now works when widget is first dropped on a sidebar

= 2.2.15 =
* fixed but with omitweeks attribute

= 2.2.14 =
* fixed IE rotation issue in some styles

= 2.2.13 =
* updated jQuery UI datepicker style.
* added Russian language
* added Slovak language
* added Persian language
* added Czech language
* added Lithuanian language
* added Catalan language
* added French language

= 2.2.12 =
* added cloud-city style which is a clone of vintage that came with vintage wedding theme: http://wordpress.org/support/topic/cannot-see-boxes

= 2.2.11 =
* When setting jsplacment to inline, script is forced to print after countdown elements.
* Escape characters no longer converted in widget tile
* Fixed 'Top Scroll' issue: http://wordpress.org/support/topic/timer-working-but-top-scroll-feature-conflicting?replies=7

= 2.2.10 =
* only load jQuery datepicker and related css on widgets admin page
* countup will switch to triple digits for numbers above 100
* added carbonite-responsive style
* added hoth style

= 2.2.9 =
* calculate local time using WordPress current_time() function
* fixed bug for iOS devices using Chrome

= 2.2.8 =
* fixed missing php tag.

= 2.2.7 =
* Added I18n localization support
* Added German translation
* Added source to Github: http://github.com/baden03/t-minus-countdown

= 2.2.6 =
* Fixed issue with flashing animations on inactive tabs (again)

= 2.2.5 =
* Bug fix: missing single quote on launchtarget.

= 2.2.4 =
* Requires WordPress 3.3 or newer
* Added jQuery datepicker for selecting target date
* Added new 'Count Up' feature

= 2.2.3 =
* Fixed spacing issues with some styles
* Rockstar features will default to collapsed to save space
* Updated method of locating plugins directory

= 2.2.2 =
* Upgraded to a more efficient method of loading only the styles that are being used

= 2.2.1 =
* Fixed CSS bugs, added new style: c-p30 mini

= 2.2 =
* Greatly improved countdown script efficiency

= 2.1 =
* Added two new image-free css styles: TIE-Fighter and C-3PO
* Fixed an issue with strange spacing caused by WordPress' wonky wpautop function
* Moved plugin page at Twinpictures' Plugin Oven
* Consolidated support to WordPress Forums and added Premium Support option.
* Fixed closed arrow issue on the widget options page.

= 2.0.9 =
* Fixed issue with Digit Titles not being saved unless in rockstar mode

= 2.0.8 =
* adjusted CSS to be compatible with WordPress 3.3

= 2.0.7 =
* super fun with svn tagging issues.

= 2.0.6 =
* further countdown optimizations.

= 2.0.5 =
* Reworked the countdown timer function to deal with the requestAnimationFrame issue on an inactive tab in Chrome.  By not blindly stacking timers on a tab the user cannot see them, the browser will have a reduced CPU footprint, leading to improved battery life in mobile devices.

= 2.0.4 =
* Now works with retched Internet Explorer browser-like crap.  Included new 'carbonlite' theme for single line countdown love.

= 2.0.3 =
* Fixed bug with onlaunch HTML when using shortcode.

= 2.0.2 =
* Added option of inserting the javascript in the footer or inline, after the countdown has been inserted.

= 2.0.1 =
* Verify that a style has been set before looping - was throwing an error.
* Improved load times by printing all javascript in the footer
* Workaround for strange behavior with html content sent using shortcode

= 2.0 =
* Multiple instance sidebar widgets.
* Advanced above and below HTML areas.
* Advanced 'on-launch' event that will display custom HTML in a target area when the countdown reaches 00:00:00.
* Added shortcode to include multiple countdowns in post and pages.

= 1.7 =
* 1.6 had completely different file structure that hosed the svn repository.

= 1.6 =
* Added automatic 3-digit weeks and days.
* Pimped out the Jedi css switcher to better handle user generated styles.
* Added new carbonite style by Lauren at siliconstudio.com

= 1.5 =
* Cleaned up code that was throwing array_merge warning errors on some systems.

= 1.4 =
* NOW, when making time calculations, refers to the local time as set by WordPress in Settings > General > Timezone.

= 1.3 =
* fixed issue with 1.2 not extracting the args... therefore there was not before-widget / before-title love.  Sleep is important, as it turns out.

= 1.2 =
* Reverted to the standard jQuery Library that comes with WordPress as it was conflicting with the TinyMCE/Visual Editor.  To gain jQuery Google Caching, give the "Use Google Libraries" Plugin a whirl.

= 1.1 =
* Squashed a bug that caused PHP to throw an extract() warning on some systems.

= 1.0 =
* The plugin came to be.

== Upgrade Notice ==

= 2.2.17 =
* addressed jQuery undefined function error on some WordPress installs
* javascript cleanup

= 2.2.16 =
* fixed TIE-Fighter style to deal with box-sizing and will now adjust width automatically
* updated the enqueue_scripts to be called using the proper hooks
* only load the widget admin scripts on the widget page
* datepicker now works when widget is first dropped on a sidebar

= 2.2.15 =
* fixed but with omitweeks attribute

= 2.2.14 =
* fixed IE rotation issue on some styles

= 2.2.13 =
* updated jQuery UI datepicker style.
* added Russian language
* added Slovak language
* added Persian language
* added Czech language
* added Lithuanian language
* added Catalan language
* added French language

= 2.2.12 =
* added cloud-city style

= 2.2.11 =
* added new style: sith
* jsplacment inline improved
* Escape characters no longer converted in widget tile
* Fixed 'Top Scroll' issue

= 2.2.10 =
* jQuery datepicker and related css will only load when needed
* countup will use triple digit class for numbers above 100
* new carbonite-responsive style

= 2.2.9 =
* current time calculation now uses WordPress current_time() function
* bug fix for iOS devices using Chrome

= 2.2.8 =
* small bug fix that was causing issues on some systems.

= 2.2.7 =
* Language support added
* German translation added

= 2.2.6 =
* Animations remain smooth for inactive tabs.

= 2.2.5 =
* Fixed missing single quote that was causing JS error.

= 2.2.4 =
* jQuery datepicker is now used to select date
* Added new Count Up feature

= 2.2.3 =
* fixed spacing issues with some styles
* rockstar features will now display collapsed by default
* will discover plugin directory if wp-content is renamed

= 2.2.2 =
* Streamlined the method of loading css styles.  Now only the styles that are being used will be loaded.

= 2.2.1 =
* Fixed CSS bugs, added new style: c-p30 mini

= 2.2 =
* Countdown scrip has been streamlined to improved efficiency.

= 2.0.9 =
* Two new image-free css styles have been added: TIE-Fighter and C-3PO
* Spacing issued caused by WordPress' wonky wpautop function has been fixed
* Fixed closed arrow issue on the widget options page.
* Moved plugin page at Twinpictures' Plugin Oven
* Consolidated support to WordPress Forums and added Premium Support option.

= 2.0.9 =
* fixed issue with Display Titles not saving

= 2.0.8 =
* fixed css issue for WordPress 3.3

= 2.0.7 =
* fixing svn issues.  old js file is being uploaded. grrr.

= 2.0.6 =
* Additional countdown timer optimizations.

= 2.0.5 =
* Improved countdown timer that reduces the browser's CPU footprint and improves battery life for mobile devices.

= 2.0.4 =
* Works with retched Internet Explorer browser-like crap.  Includes new single-line 'carbonlite' theme.

= 2.0.3 =
Onlaunch HTML shortcode bug fix.

= 2.0.2 =
New option of placing the javascript in the footer or inline.

= 2.0.1 =
Minor bug fixes and improved load times.

= 2.0 =
Requires WordPress version 2.8 or higher.  Backup custom CSS folders.

= 1.7 =
1.6 failed to upload correctly to svn... very messed up situation

= 1.6 =
Version 1.6 brings much love to the countdown user.  First, automatic triple digit weeks and days have been added.  Next, the Jedi style switcher has been revamped to better handle user generated css.  Finally, a third default style has been added called Carbonite designed by Laruen at siliconstudio.com.

= 1.5 =
Version 1.5 cleans up code that was causing array_merge errors on some systems.  NOTE: if a custom CSS is being used, be sure to back up your css and image files before updating.  Updating will overwrite custom css styles.

= 1.4 =
Version 1.4 refers to your local WordPress Timezone for time calculations.  In case the server is hosted outside of the website's local timezone.  It happens.

= 1.3 =
Version 1.3 fixes the issue no before/after widget/title issues due to lack of sleep during v. 1.2.

= 1.2 =
Version 1.2 fixes the issue that disabled the TinyMCE/Visual Editor.

= 1.1 =
Version 1.1 fixes the extract() warning that was being thrown on some systems.

= 1.0 =
Where once there was not, there now is.
