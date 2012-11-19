<?php
/*
Plugin Name: 0gravatar
Plugin URI: http://0gravatar.com/
Author: Eli Scheetz
Author URI: http://wordpress.ieonly.com/category/my-plugins/no-gravatar/
Contributors: scheeeli
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8VWNB5QEJ55TJ
Description: This Plugin replaces the get_avatar function in pluggable.php to pass an alternate image to gravatar.com that generates the user's display_name if they do not yet have a gravatar.
Version: 1.2.11.17
*/
$NOGRAVATAR_Version='1.2.11.17';
if (!isset($_SESSION)) session_start();
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) die('You are not allowed to call this page directly.<p>You could try starting <a href="http://'.$_SERVER['SERVER_NAME'].'">here</a>.');
$_SESSION['eli_debug_microtime']['include(NOGRAVATAR)'] = microtime(true);
/**
 * 0GRAVATAR Main Plugin File
 * @package 0GRAVATAR
*/
/*  Copyright 2011 Eli Scheetz (email: wordpress@ieonly.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
function NOGRAVATAR_install() {
	global $wp_version;
$_SESSION['eli_debug_microtime']['NOGRAVATAR_install_start'] = microtime(true);
	if (version_compare($wp_version, "2.6", "<"))
		die(__("This Plugin requires WordPress version 2.6 or higher"));
$_SESSION['eli_debug_microtime']['NOGRAVATAR_install_end'] = microtime(true);
}
function NOGRAVATAR_admin_notices() {
	echo "<div class=\"error\">0gravatar will not work on your site because the function get_avatar() is already defined.</div>";
}
function NOGRAVATAR_404($email) {
	$headers = @get_headers('http://www.gravatar.com/avatar/'.md5(strtolower(trim($email))).'?d=404');
	return preg_match('/404/', $headers[0]);
}
function NOGRAVATAR_encode($unencoded_string) {
	$encoded_array = explode('=', base64_encode($unencoded_string).'=');
	return $encoded_array[0].(count($encoded_array)-1);
}
if (function_exists('get_avatar'))
	add_action('admin_notices', 'NOGRAVATAR_admin_notices');
else {
/**
 * Retrieve the avatar for a user who provided a user ID or email address.
 *
 * @since 2.5
 * @param int|string|object $id_or_email A user ID,  email address, or comment object
 * @param int $size Size of the avatar image
 * @param string $default URL is overwritten here to generate an alternate image of the user's display_name
 * @return string <img> tag for the user's avatar
*/
	function get_avatar( $id_or_email, $size = '96', $default = '', $alt = false ) {
		if ( ! get_option('show_avatars') )
			return false;

		if ( false === $alt)
			$safe_alt = '';
		else
			$safe_alt = esc_attr( $alt );

		if ( !is_numeric($size) )
			$size = '96';

		$email = '';
		if ( is_numeric($id_or_email) ) {
			$id = (int) $id_or_email;
			$user = get_userdata($id);
			if ( $user ) {
				$email = $user->user_email;
				$display_name = $user->display_name;
			}
		} elseif ( is_object($id_or_email) ) {
			// No avatar for pingbacks or trackbacks
			$allowed_comment_types = apply_filters( 'get_avatar_comment_types', array( 'comment' ) );
			if ( ! empty( $id_or_email->comment_type ) && ! in_array( $id_or_email->comment_type, (array) $allowed_comment_types ) )
				return false;

			if ( !empty($id_or_email->user_id) ) {
				$id = (int) $id_or_email->user_id;
				$user = get_userdata($id);
				if ( $user ) {
					$email = $user->user_email;
					$display_name = $user->display_name;
				}
			} elseif ( !empty($id_or_email->comment_author_email) ) {
				$email = $id_or_email->comment_author_email;
				$display_name = $id_or_email->comment_author;
			}
		} else {
			$email = $id_or_email;
			$dname = explode('@', $id_or_email.'@');
			$display_name = $dname[0];
		}

		if ( empty($default) ) {
			$avatar_default = get_option('avatar_default');
			if ( empty($avatar_default) )
				$default = 'mystery';
			else
				$default = $avatar_default;
		}

		if ( !empty($email) )
			$email_hash = md5( strtolower( $email ) );

		if ( is_ssl() ) {
			$host = 'https://secure.gravatar.com';
		} else {
			if ( !empty($email) )
				$host = sprintf( "http://%d.gravatar.com", ( hexdec( $email_hash[0] ) % 2 ) );
			else
				$host = 'http://0.gravatar.com';
		}
		$out = "http://0gravatar.com/avatar/$size/".NOGRAVATAR_encode($display_name.'@'.$email).".gif";
		if (true) {
			$safe_alt = esc_attr($display_name);
			$default = $out;
		} else {
			if ( 'mystery' == $default )
				$default = "$host/avatar/ad516503a11cd5ca435acc9bb6523536?s={$size}"; // ad516503a11cd5ca435acc9bb6523536 == md5('unknown@gravatar.com')
			elseif ( 'blank' == $default )
				$default = includes_url('images/blank.gif');
			elseif ( !empty($email) && 'gravatar_default' == $default )
				$default = '';
			elseif ( 'gravatar_default' == $default )
				$default = "$host/avatar/s={$size}";
			elseif ( empty($email) )
				$default = "$host/avatar/?d=$default&amp;s={$size}";
			elseif ( strpos($default, 'http://') === 0 )
				$default = add_query_arg( 's', $size, $default );
		}
		if ( !empty($email) ) {
			if (false && NOGRAVATAR_404($email)) {
				$safe_alt = esc_attr($display_name)." has no gravatar";
				$avatar = "<img alt='{$safe_alt}' src='{$out}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
			} else {
				$out = "$host/avatar/";
				$out .= $email_hash;
				$out .= '?s='.$size;
				$out .= '&amp;d=' . urlencode( $default );

				$rating = get_option('avatar_rating');
				if ( !empty( $rating ) )
					$out .= "&amp;r={$rating}";

				$avatar = "<img alt='{$safe_alt}' src='{$out}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
			}
		} else {
			$avatar = "<img alt='{$safe_alt}' src='{$default}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
		}

		return apply_filters('get_avatar', $avatar, $id_or_email, $size, $default, $alt);
	}
}
function NOGRAVATAR_set_plugin_row_meta($links_array, $plugin_file) {
	if ($plugin_file == substr(__file__, (-1 * strlen($plugin_file))))
		$links_array = array_merge($links_array, array('<a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8VWNB5QEJ55TJ">'.__( 'Donate' ).'</a>'));
	return $links_array;
}
add_filter('plugin_row_meta', 'NOGRAVATAR_set_plugin_row_meta', 1, 2);
register_activation_hook(__FILE__,'NOGRAVATAR_install');
$_SESSION['eli_debug_microtime']['end_include(NOGRAVATAR)'] = microtime(true);
?>