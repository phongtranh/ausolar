=== Google Content Experiments ===
Contributors: GlennM
Tags: google content experiment
Requires at least: 3.4.0
Tested up to: 3.8
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin enables you to use Google Content Experiments on your WordPress site.

== Description ==

Per August 1, 2012, Google discontinued the used of their splittesting software Google Website Optimizer. Google Analytics now contains a function called Content Experiments which can be used to splittest content on your website. This plugin enables you to use Content Experiments on your WordPress site.

== Installation ==

1. Upload the `google-content-experiments` folder to the `/wp-content/plugins/` directory
1. Activate the Google Content Experiments plugin through the 'Plugins' menu in WordPress
1. At the post/page edit screen, check the box to activate the Content Experiment for that post/page
1. Insert your experiment code
1. Publish the post/page

= Supported themes and theme frameworks =

* Child themes based on the [Genesis Framework](http://www.studiopress.com/ "Genesis Framework")
* Themes using the [Infinity](http://infinity.presscrew.com/ "Infinity") WordPress Theming Engine
* [Thematic](http://thematictheme.com/ "Thematic Theme")
* [PageLines](http://wordpress.org/extend/themes/pagelines/ "PageLines")

If your theme is not listed in the supported themes, you need to perform the following steps:

1. Within your theme, locate the file where the `<head>` tag is inserted (probably something like header.php)
1. Place `<?php do_action( 'wpe_gce_head' ); ?>` right after `<head>`
1. Save the file

By using the plugin, add the Google Content Experiments code to a page and view the source code of the web page to verify that the Google Content Experiments code is actually inserted after the `<head>` tag.

== Screenshots ==

1. Meta box to insert the experiment code at the post/page edit screen

== Frequently Asked Questions ==

= How do I setup a content experiment? =

Please see [this page](http://support.google.com/analytics/bin/answer.py?hl=en&answer=1745216 "Run an Experiment: Configure & Modify").

= Error message: 'Call to undefined function wp_get_theme' =

The function `wp_get_theme` exists since WordPress 3.4.0. Try to update to the latest version of WordPress and see if that resolves the issue.

= Error message: 'Experiment code missing the cookie domain name declared in tracking code' =

The Google Analytics code modifies the cookie domain, but this modification is not present in the experiment code.
You should add some extra code to the code you get from Google.

*Example:*

In your Google Analytics code there is some code like `['_setDomainName', 'example.com']`. You should add the following code *above* the experiment code you got from Google:
`<script>
_udn = "example.com";
</script>`

In total, the experiment code you put in the Experiment Code field of the GCE plugin should look like:
`<script>
_udn = "example.com";
</script>
<!-- Google Analytics Experiment code -->
... Contents of the experiment code ...
<!-- End of Google Analytics Experiment code -->`

For more info about error messages go to the [Code-Checking Errors](http://support.google.com/analytics/bin/answer.py?hl=en&answer=2364634 "Code-Checking Errors") page.

== Changelog ==

= 1.0.3 =

* Tested compatibility with WordPress 3.6
* Fixed some notices of undefined variables
* Updated class-wpe-GCE.php and admin/class-wpe-GCEAdmin.php according to WordPress PHP Coding Standards
* Updated language files
* Updated screenshot

= 1.0.2 =

* Updated Genesis support to be compatible with Genesis 2.0 to support HTML5 child themes
* Added metabox to activate the experiment to the Add new post/pages/custom post type screens
* Added support for Infinity
* Added support for Thematic
* Added support for PageLines

= 1.0.1 =

* Fixed bug that inserted experiment code when displaying multiple posts (e.g. on category pages)
* Updated Dutch translation

= 1.0 =

* Initial release

== Upgrade Notice ==

= 1.0 =

* Initial release