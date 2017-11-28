=== T(-) Countdown ===

Contributors: twinpictures, baden03
Donate link: https://plugins.twinpictures.de/plugins/t-minus-countdown/
Tags: countdown, timer, clock, ticker, widget, event, counter, count down, twinpictures, t minus, t-minus, t(-), t(-) countdown, t-countdown, t (-) countdown, plugin-oven, pluginoven, G2, spaceBros, littlewebtings, jQuery, javascript
Requires at least: 4.5
Tested up to: 4.9.0
Stable tag: 2.3.18
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

T(-) Countdown will display a highly customizable, HTML5 countdown timer as a sidebar widget or in a post or page using a shortcode.

== Description ==

T(-) Countdown will display a highly customizable HTML5 countdown timer as a sidebar widget or in a post or page using a shortcode. Perfect for informing one's website visitors of an upcoming event, such as a pending space voyage. Using Jedi Mind-tricks and CSS... but mostly CSS, the countdown timer is highly customizable for your viewing pleasure. A <a href='https://plugins.twinpictures.de/plugins/t-minus-countdown/documentation/'>complete listing of shortcode options</a> are available, as well as <a href='https://wordpress.org/support/plugin/jquery-t-countdown-widget'>free community</a> support. This plug-in was inspired by littlewebthings' CountDown jQuery plugin. Intergalactic planetary thanks to g2.de, siliconstudio.com and be.net/arturex for the included css styles.

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
1. Leave a comment regarding bugs, feature request or cocktail recipes at https://wordpress.org/tags/jquery-t-countdown-widget

== Frequently Asked Questions ==

= How does one use the shortcode, exactly? =
A <a href='https://plugins.twinpictures.de/plugins/t-minus-countdown/documentation/'>complete list of shortcode options</a> has been provided to answer this exact question.

= Where can I fork this plugin and contribute changes? =
<a href='https://github.com/baden03/t-minus-countdown'>github</a>

= Where can I translate this plugin into my favorite langauge? =
<a href='https://translate.wordpress.org/projects/wp-plugins/jquery-t-countdown-widget'>Community translation for T(-) Countdown</a> has been set up.

= How does one pronounce T Minus? =
* Tee&mdash;As in Tea for Two, or Tee off time
* Minus&mdash;As in the opposite of plus (+)
* T Minus&mdash;As in "This is Apollo Saturn Launch Control. We've passed the 11-minute mark. Now T minus 10 minutes 54 seconds on our countdown for Apollo 11."

= I am a Social Netwookiee, does Twinpictures have a Facebook page? =
Yes, yes... <a href='https://www.facebook.com/twinpictures'>Twinpictures is on Facebook</a>.

= Does Twinpictures do the Twitter? =
Ah yes! <a href='https://twitter.com/#!/twinpictures'>@Twinpictures</a> is on the Twitter.

= Where may one enjoy U.S. news that gives giggle? =
* The Daily Show with <i>Trevor Noah</i>
* Last Week Tonight with John Oliver

== Screenshots ==

1. T(-) Countdown in action with styles: Darth, Jedi and Carbonite.
1. Styles: C-3PO, TIE-Fighter and Carbonlite.
1. The basic T(-) Countdown widget options.
1. An expansive view of the available Countdown widget options, provided for your viewing pleasure.
1. The magical jQuery Datepicker.
1. Plugin options page with Custom CSS section

== Changelog ==

= 2.3.18 =
* rebuilt method of keeping countdown timer in sync
* Prevent errors when $(this) isn't an array
* fixed bug, where timers freeze if you have more than one on a page
* fully tested with WordPress 4.9.0

= 2.3.17 =
* scripts do not load on customize.php page
* removed now.php from js
* fully tested with WordPress 4.8

= 2.3.16 =
* no longer requires now.php
* widget now accepts only expected values
* all links now https
* fully tested with WordPress 4.7.3

= 2.3.15 =
* widget scripts are only called on widget page
* updated method jQuery datepicker was being called in Widget

= 2.3.14 =
* accepts numTransObj as object that holds number translations non arabic numerals
* fully tested with WordPress 4.6.1

= 2.3.13 =
* updated version and method of loading remote jQuery UI from google cdn
* widget now uses esc_attr for the title
* cloud-city digits have more space between digits for smaller viewports
* updated jquery-ui-timepicker to version 1.6.3
* added jquery-ui-timepicker css
* only triggers callback when required

= 2.3.12 =
* replaced top and bottom class names to tc_top and tc_bottom to prevent conflicts
* renamed js functions to prevent conflict with the theme: Avada

= 2.3.11 =
* Removed included language files in favour of WordPress Language Packs
* Fully tested with WordPress 4.5

= 2.3.10 =
* fixed: issue on some systems to thrown a Catchable fatal error: Object of class stdClass could not be converted to string
* Fully tested with WordPress 4.4.0
* Notice: Last chance to go pro at 2015 pricing

= 2.3.9 =
* fixed: bug with localtime not being passed when js was placed inline

= 2.3.8 =
* updated the readme.txt file to reflect the retirement of J-Steu
* count-up will switch to 3 digits when above 99 days/weeks

= 2.3.7 =
* fixed error with wrongly named function, thank you Stefano

= 2.3.6 =
* Added missing text domain
* Renamed language files to xx_XX.mo and xx_XX.po
* Fixed a typo

= 2.3.5 =
* Modified php constructor
* Adjusted language domain inline with new WordPress Translation tool
* Fixed bug with html in launch text

= 2.3.4 =
* Corrected name of widget

= 2.3.3 =
* added IE 10+ gradients to c-3p0 styles
* added PHP5 style constructors for the Widget

= 2.3.2 =
* added htmlencoding for html areas above and below countdown.

= 2.3.1 =
* fixed fatal bug when registering events ajax callback
* added force load css option for systems that will not dynamically load css
* misc cleanup

= 2.3.0 =
* complete code update
* added plugin options page
* added datetimepicker to widget
* added T(-) Countdown Event integration
* new method of handling styles and custom styles

= 2.2.20 =
* updated method of formatting time in meta-box
* error handling if the ajax call is not successful

= 2.2.19 =
* time calculation for NOW is done via ajax, so now caching plugins may be used

= 2.2.18 =
* countdown script moved in loading priority to after jQuery, when loaded in the footer

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
* added cloud-city style which is a clone of vintage that came with vintage wedding theme: https://wordpress.org/support/topic/cannot-see-boxes

= 2.2.11 =
* When setting jsplacment to inline, script is forced to print after countdown elements.
* Escape characters no longer converted in widget tile
* Fixed 'Top Scroll' issue: https://wordpress.org/support/topic/timer-working-but-top-scroll-feature-conflicting?replies=7

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
* Added source to Github: https://github.com/baden03/t-minus-countdown

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
* rebuilt method of keeping countdown timer in sync
* Prevent errors when $(this) isn't an array
* fixed bug, where timers freeze if you have more than one on a page
* fully tested with WordPress 4.9.0