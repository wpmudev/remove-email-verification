<?php

/*
  Plugin Name: Remove Email Verification
  Plugin URI: http://premium.wpmudev.org/project/remove-email-verification-from-signup
  Description: This plugin automatically activates and log in users and blog signups, effectively disabling the need for the user to respond to a verification e-mail.
  Author: WPMU DEV
  Version: 2.3
  Author URI:
  TextDomain: removeev
  Domain Path: /languages/
  WDP ID: 74
  Network: true
 */

/*
  Copyright 2014 Incsub (http://incsub.com)
  
  Lead Developer - Marko Miljus
  
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
  the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if (is_admin()) {
  //load dashboard notice
  global $wpmudev_notices;
  $wpmudev_notices[] = array( 'id'=> 74, 'name'=> 'Remove Email Verification', 'screens' => array() );
  include_once( dirname( __FILE__ ) . '/external/dash-notice/wpmudev-dash-notification.php' );
}

require_once('classes/public.removeverification.php');
$removeemailverification = new removeemailverification();

function ap_action_init() {
    // Localization  
    load_plugin_textdomain('removeev', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Add actions  
add_action('init', 'ap_action_init');

//replaced pluggable function
if (!function_exists('wp_new_user_notification')) :

    function wp_new_user_notification($user_id, $plaintext_pass = '') {
        return;
    }


endif;