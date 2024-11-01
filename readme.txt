=== SPDM Shortcode Slider Multisite ===
Contributors: _DorsVenabili, bi0xid, mecus
Tags: featured content slider, slideshow, gallery, featured image, shortcodes, tag, term, taxonomy, category, pages, posts, jquery, multiple instances, page excerpts, multisite, sites
Requires at least: 3.0
Tested up to: 3.5.2
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BDUEBSM9QRPDU

Now you can show posts/pages from other sites of your multisite in a Jquery Featured Slideshow controlled by shortcodes (has 5 default layouts).

== Description ==

SPDM Shortcode Slider Multisite is a plugin based on the plugin SPD Shortcode Slider. This plugin has 2 new freatures: [sites__in] and [sites__not_in] to show posts/pages from other sites of your multisite in your Slideshows. This is a first version, more features for multisite are coming soon. Patches are welcome ;)

Slider uses featured image for slides and thumbs. Multiple instances supported (although not on same page). Since content, slider layouts, image sizes and animations are controlled by attributes with multiple possible values you can create unlimited unique configurations with every instance. 

Specify layouts (plain, thumbnail-top, thumbnail-bottom, numbers-top, numbers-bottom and buttons) along with slide content by custom taxonomy term, post type, category id, tag id, or just their post and page ids. You can also define the amount of slides, order, effect, speed between transitions, speed of transition and whether the slide title, slide excerpt, thumb title, or navigation arrows are visible by their corresponding shortcodes.

Supports custom taxonomy terms, Post AND Page excerpts*, and has 5 default layout choices. (*for custom post types you will have to add excerpt support in your theme functions file)

You can override plugin default CSS styles by adding #shortcodeslider to any css styles in your theme stylesheet.

To clear up any confusion about the shortcode usage: The [spdm_slider] shortcode is NOT for embedding a image slider within a post or page content editor (i.e within the loop). If you need a shortcode image gallery plugin (that pulls all images attached to the post/page while on that post or page) there are many other plugins that do this (I recommend Raygun's Portfolio Slideshow Pro).  The spdm Shortcode Slider is for creating featured content slideshows that display the featured image, title and excerpt of selected posts/pages. It uses the [spdm_slider] shortcode to facilitate adding custom variables to the slider query within the do_shortcode template tag.

== Installation ==
1. Upload `spdm-shortcode-slider-multisite` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place this code in template to show slider`<?php do_shortcode('[spdm_slider]'); ?>`
3. For a list of all shortcodes and supported values Go on your Dashboard > Settings > SPDM Shortcode Slider Multisite

== Screenshots ==
1. numbers-bottom layout
2. numbers-top layout
3. thumbnails-bottom layout
4. thumbnails-side layout
5. buttons layout

== Changelog ==

= 0.1 =
* First release

= 0.2 =
* Fix prepare() usage

Please [contact](http://dorsvenabili.com/contacto/ "contact me") me if you notice any bugs or errors and I will try and fix them.