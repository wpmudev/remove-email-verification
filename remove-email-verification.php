<?php

/*
  Plugin Name: Remove Email Verification
  Plugin URI: http://premium.wpmudev.org/project/remove-email-verification-from-signup
  Description: This plugin automatically activates and log in users and blog signups, effectively disabling the need for the user to respond to a verification e-mail.
  Author: Incsub
  Version: 2.2
  Author URI:
  TextDomain: removeev
  Domain Path: /languages/
  WDP ID: 74
  Network: true
 */

/*
  Copyright 2007-2014 Incsub (http://incsub.com)

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
    include_once('external/wpmudev-dash-notification.php');
} else {
    require_once('classes/public.removeverification.php');
    $removeemailverification = new removeemailverification();
}

function ap_action_init() {
    // Localization  
    load_plugin_textdomain('removeev', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Add actions  
add_action('init', 'ap_action_init');