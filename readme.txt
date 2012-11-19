=== Display Name If No Gravatar ===
Plugin URI: http://0gravatar.com/
Author: Eli Scheetz
Author URI: http://wordpress.ieonly.com/category/my-plugins/no-gravatar/
Contributors: scheeeli
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8VWNB5QEJ55TJ
Tags: gravatar, plugin, avatar, display-name, comment, post, author, image, 0gravatar.com
Stable tag: 1.2.11.17
Version: 1.2.11.17
Requires at least: 2.6
Tested up to: 3.4.2

This Plugin passes an alternate image to gravatar.com that generates the user's display_name if they do not yet have a gravatar.

== Description ==

This Plugin replaces the get_avatar function in pluggable.php to pass an alternate image to gravatar.com that generates the user's display_name if they do not yet have a gravatar.

More features coming soon...

Plugin Updated Nov-17th

== Installation ==

1. Download and unzip the plugin into your WordPress plugins directory (usually `/wp-content/plugins/`).
1. Activate the plugin through the 'Plugins' menu in your WordPress Admin.

== Screenshots ==

1. A comment on my blog from a user with no gravatar.

== Changelog ==

= 1.2.11.17 =
* Increased performance by sending the URL for the auto-generated display_name to gravatar.com as the d parameter instead of checking for a 404 on every gravatar.

= 1.2.11.07 =
* First versions uploaded to WordPress.

== Upgrade Notice ==

= 1.2.11.17 =
Increased performance by sending the URL to gravatar.com instead of checking for a 404.

= 1.2.11.07 =
First versions available through WordPress.