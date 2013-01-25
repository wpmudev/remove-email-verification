<?php
/*
Plugin Name: Remove Email Verification
Plugin URI: http://premium.wpmudev.org/project/remove-email-verification-from-signup
Description: This plugin automatically activates user and blog signups, effectively disabling the need for the user to respond to an email
Author: Barry (Incsub)
Version: 2.1.1
Author URI:
WDP ID: 74
Network: true
*/

/*
Copyright 2007-2012 Incsub (http://incsub.com)

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

if( is_admin() ) {
	include_once('external/wpmudev-dash-notification.php');
}

require_once('classes/public.removeverification.php');

$removeemailverification = new removeemailverification();


?>