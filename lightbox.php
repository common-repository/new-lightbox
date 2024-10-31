<?php
/*
  Plugin Name: New Lightbox
  Author URI: http://allanplugins.weebly.com
  Description: With the New Lightbox you can Add custom lightbox attribute to linked flash files or images in posts, pages and comments, group images using ID. 
  Version: 1.0
  Author: Allan Steves
  License: GPLv3 or later
*/

/*  Copyright 2012 - 2013 Allan Steves  (email : allansteves@mail.com)

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

//create options page


add_action( 'admin_menu', 'easynewlightbox_menu' );
function easynewlightbox_menu() {
  add_options_page( 'Easy New Lightbox', 'Easy New Lightbox', 'manage_options', 'easynewlightbox-options', 'easynewlightbox_settings' );
  add_action( 'admin_init', 'register_easynewlightbox_settings' );
}


//register settings
function register_easynewlightbox_settings(){
  register_setting( 'easynewlightbox_settings_group', 'easynewlightbox' );
  register_setting( 'easynewlightbox_settings_group', 'easynewlightbox_flash' );
}


//setting page
function easynewlightbox_settings() {
?>
<div class="wrap">
  <h2>Easy New Lightbox</h2>
  <form method="post" action="options.php">
    <?php
	  settings_fields( 'easynewlightbox_settings_group' );
	  do_settings_sections( 'easynewlightbox_settings_group' );
	  $easynewlightbox_code = htmlspecialchars( get_option( 'easynewlightbox' ), ENT_QUOTES );
	  $easynewlightbox_flash_code = htmlspecialchars( get_option( 'easynewlightbox_flash' ), ENT_QUOTES );
	  $plugin_dir = basename(dirname(__FILE__));
	  load_plugin_textdomain( 'easynewlightbox', false, $plugin_dir );
	?>
	<p><?php _e( 'Input lightbox attributes below (both optional), for example <em>rel=&quot;lightbox&quot;</em>, <em>class=&quot;colorbox&quot;</em>.', 'easynewlightbox' ) ?></p>
	<p><?php _e( 'To group images by ID use <strong>[id]</strong> for example <em>rel=&quot;prettyPhoto[id]&quot;</em>.', 'easynewlightbox' ) ?></p>
	<p><strong style="float:left;display:block;width:45px;text-align:right;margin:3px 6px 0 0;">Images:</strong> <input type="text" style="width:200px;" name="easynewlightbox" value="<?php echo $easynewlightbox_code; ?>" /></p>
	<p><strong style="float:left;display:block;width:45px;text-align:right;margin:3px 6px 0 0;">Flash:</strong> <input type="text" style="width:200px;" name="easynewlightbox_flash" value="<?php echo $easynewlightbox_flash_code; ?>" /></p>
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
</div>
<?php }
//uninstall hook
if ( function_exists('register_uninstall_hook') )
    register_uninstall_hook(__FILE__, 'easynewlightbox_uninstall_hook');
function easynewlightbox_uninstall_hook() {
  delete_option('easynewlightbox');
  delete_option('easynewlightbox_flash');
}
//the replace functions
function easynewlightbox_replace( $content ) {
  global $post;
  $addpostid = '[' .$post->ID. ']';
  $easynewlightbox_replacement = preg_replace( '/\[(id)\]/', $addpostid, get_option( 'easynewlightbox' ) );
  $replacement = '<a$1href=$2$3.$4$5 ' .$easynewlightbox_replacement. '$6>$7</a>';
  $content = preg_replace( '/<a(.*?)href=(\'|")([^>]*).(bmp|gif|jpeg|jpg|png)(\'|")(.*?)>(.*?)<\/a>/i', $replacement, $content );
  return $content;
}
function easynewlightbox_flash_replace( $content ) {
  global $post;
  $addpostid = '[' .$post->ID. ']';
  $easynewlightbox_flash_replacement = preg_replace( '/\[(id)\]/', $addpostid, get_option( 'easynewlightbox_flash' ) );
  $replacement = '<a$1href=$2$3.$4$5 '.$easynewlightbox_flash_replacement.'$6>$7</a>';
  $content = preg_replace( '/<a(.*?)href=(\'|")([^>]*).(swf|flv)(\'|")(.*?)>(.*?)<\/a>/i', $replacement, $content );
  return $content;
}
//if options set add filters
if ( get_option( 'easynewlightbox' ) != null) {
  add_filter( 'the_content', 'easynewlightbox_replace', 12 );
  add_filter( 'get_comment_text', 'easynewlightbox_replace', 12 );
}
if ( get_option( 'easynewlightbox_flash' ) != null) {
  add_filter( 'the_content', 'easynewlightbox_flash_replace', 13 );
  add_filter( 'get_comment_text', 'easynewlightbox_flash_replace', 13 );
}
?>