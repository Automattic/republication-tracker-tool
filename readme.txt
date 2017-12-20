=== Creative Commons Sharing ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: http://example.com/
Tags: comments, spam
Requires at least: 4.4
Tested up to: 4.8.2
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a widget to allow readers to easily acquire Creative-Commons-licensed HTML of articles to facilitate embedding posts on external sites. Includes a tracking mechanism similar to ProPublica's PixelPing.

== Description ==

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the widget to your per-post sidebars. It doesn't work outside of single post pages.

== Frequently Asked Questions ==

= What is the PixelPing-style tracking mechanism? =

[PixelPing](https://www.propublica.org/pixelping) is a tracking technology developed by ProPublica to allow them to track where their stories are embedded on external sites, and how many readers those stories achieve.

In this plugin, the tracking is achieved through a small JavaScript tag that is added to the HTML of stories when the reader copies the story HTML using the widget's share dialog.

== Changelog ==

= 1.0.1 =
* improved post HTML cleaning through better stripping of shortcodes, figures, images, audio, and video
* Cleanup of the shareable text generation endpoint code
* fixes bug where certain plugins interfered with shortcode removal
* fixes widget CSS on passblue.com

= 1.0 =
* Initial release
