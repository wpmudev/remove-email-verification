<?php
/*
Plugin Name: Remove Email Verification
Plugin URI: 
Description: This plugin automatically activates user and blog signups, effectively disabling the need for the user to respond to an email
Author: Barry at clearskys.net (Incsub)
Version: 1.0.3
Author URI:
*/

/* 
Copyright 2007-2009 Incsub (http://incsub.com)

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

// Remove existing filters - we need to do this in the whitelist_options filter because there isn't another action between
// the built in MU one and the saving, besides which we need to add our new_admin_email field to the list anyway.
add_filter('whitelist_options', 'remove_mu_option_hooks');
// Blog signup - autoactivate
add_filter( 'wpmu_signup_blog_notification', 'activate_on_blog_signup', 10, 7 );
// User signup - autoactivate
add_filter( 'wpmu_signup_user_notification', 'activate_on_user_signup', 10, 4 );
// End activation message display
add_action( 'signup_finished', 'activated_signup_finished', 1 );
// Change internal confirmation message - user-new.php
add_filter('gettext', 'activated_newuser_msg', 10, 3);
//Remove BP activation emails.
add_filter('wp_mail', 'remove_bp_activation_emails');

function remove_bp_activation_emails($data) {
	if ( strstr($data['message'], 'To activate your user, please click the following link') || strstr($data['message'], 'To activate your blog, please click the following link') ) {
		unset( $data );
		$data['message'] = '';
		$data['to'] = '';
		$data['subject'] = '';
	}
	return $data;
}

function remove_mu_option_hooks($whitelist_options) {
	global $wp_filter;
	
	if(has_action('update_option_new_admin_email', 'update_option_new_admin_email')) {
		remove_action('update_option_new_admin_email', 'update_option_new_admin_email', 10, 2);
		// Add our own replacement action
		add_action('pre_update_option_new_admin_email', 'custom_update_option_new_admin_email', 10, 2);
	}
	
	$whitelist_options['general'][] = 'new_admin_email';
	
	return $whitelist_options;
	
}

function custom_update_option_new_admin_email($new_value, $old_value) {
	global $current_site;
	
	// Update the correct fields
	update_option('admin_email', $new_value);
	// Return the old value so that the new_admin_email option isn't set
	return $old_value;
}

function activate_on_blog_signup($domain, $path, $title, $user, $user_email, $key, $meta) {
	
	global $current_site;
	
	// Rather than recreate the wheel, just activate the blog immediately
	$result = wpmu_activate_signup($key);
	
	if ( is_wp_error($result) ) {
		if ( 'already_active' == $result->get_error_code() || 'blog_taken' == $result->get_error_code() ) {
		    $signup = $result->get_error_data();
			?>
			<h2><?php _e('Congratulations! Your new blog is ready!'); ?></h2>
			
			<?php
		    if( $signup->domain . $signup->path != '' ) {
		    	printf(__('<p class="lead-in">Your blog at <a href="%1$s">%2$s</a> is active. You may now login to your blog using your chosen username of "%3$s".  Please check your email inbox at %4$s for your password and login instructions.  If you do not receive an email, please check your junk or spam folder.  If you still do not receive an email within an hour, you can <a href="%5$s">reset your password</a>.</p>'), 'http://' . $signup->domain, $signup->domain, $signup->user_login, $signup->user_email, 'http://' . $current_site->domain . $current_site->path . 'wp-login.php?action=lostpassword');
		    }
		} else {
			?>
			<h2><?php _e('An error occurred during the signup'); ?></h2>
			<?php
		    echo '<p>'.$result->get_error_message().'</p>';
		}
	} else {
		extract($result);
		
		$url = get_blogaddress_by_id( (int) $blog_id);
		$user = new WP_User( (int) $user_id);
		?>
		<h2><?php _e('Congratulations! Your new blog is ready!'); ?></h2>
		
		<div id="signup-welcome">
			<p><span class="h3"><?php _e('Username:'); ?></span> <?php echo $user->user_login ?></p>
			<p><span class="h3"><?php _e('Password:'); ?></span> <?php echo $password; ?></p>
		</div>
				
		<?php if( !empty($url) ) : ?>
			<p class="view"><?php printf(__('You\'re all set up and ready to go. <a href="%1$s">View your site</a> or <a href="%2$s">Login</a>'), $url, $url . 'wp-login.php' ); ?></p>
		<?php else: ?>
			<p class="view"><?php printf( __( 'You\'re all set up and ready to go. <a href="%1$s">Login</a> or go back to the <a href="%2$s">homepage</a>.' ), 'http://' . $current_site->domain . $current_site->path . 'wp-login.php', 'http://' . $current_site->domain . $current_site->path ); ?></p>
		<?php endif;
	}
	
	// Now we need to hijack the sign up message so it isn't displayed
	ob_start();
	
	return false; // Returns false so that the activation email isn't sent out to the user
}

function activate_on_user_signup($user, $user_email, $key, $meta) {
	
	global $current_site, $current_blog;
	
	// Output buffer in case we need to email instead of output
	$html = '';
	
	// Rather than recreate the wheel, just activate the user immediately
	$result = wpmu_activate_signup($key);
	
	if ( is_wp_error($result) ) {
		if ( 'already_active' == $result->get_error_code() || 'blog_taken' == $result->get_error_code() ) {
		    $signup = $result->get_error_data();
			$html .= '<h2>' . __('Hello, your account has been created!') . "</h2>\n";
		    if( $signup->domain . $signup->path == '' ) {
		    	$html .= sprintf(__('<p class="lead-in">Your account has been activated. You may now <a href="%1$s">login</a> to the site using your chosen username of "%2$s".  Please check your email inbox at %3$s for your password and login instructions. If you do not receive an email, please check your junk or spam folder. If you still do not receive an email within an hour, you can <a href="%4$s">reset your password</a>.</p>'), 'http://' . $current_blog->domain . $current_blog->path . 'wp-login.php', $signup->user_login, $signup->user_email, 'http://' . $current_blog->domain . $current_blog->path . 'wp-login.php?action=lostpassword');
			} else {
		    	$html .= sprintf(__('<p class="lead-in">Your account at <a href="%1$s">%2$s</a> is active. You may now login to your account using your chosen username of "%3$s".  Please check your email inbox at %4$s for your password and login instructions.  If you do not receive an email, please check your junk or spam folder.  If you still do not receive an email within an hour, you can <a href="%5$s">reset your password</a>.</p>'), 'http://' . $signup->domain, $signup->domain, $signup->user_login, $signup->user_email, 'http://' . $current_blog->domain . $current_blog->path . 'wp-login.php?action=lostpassword');
			}
		} else {
			
			$html .= '<h2>' . __('An error occurred during the signup') . "</h2>\n";
		    $html .=  '<p>'.$result->get_error_message().'</p>';
		}
	} else {
		extract($result);
		
		$user = new WP_User( (int) $user_id);
		
		$html = '<h2>' . sprintf(__('Hello %s, your account has been created!'), $user->user_login ) . "</h2>\n";
		
		$html .= '<div id="signup-welcome">';
		$html .= '<p><span class="h3">' . __('Username:') . '</span>' . $user->user_login . '</p>';
		$html .= '<p><span class="h3">' . __('Password:') . '</span>' . $password . '</p>';
		$html .= '</div>';
				
		$html .= '<p class="view">' . sprintf( __( 'You can now update your details by <a href="%1$s">Logging in</a> to your account or go back to the <a href="%2$s">homepage</a>.' ), 'http://' . $current_blog->domain . $current_blog->path . 'wp-login.php', 'http://' . $current_blog->domain . $current_blog->path ) . '</p>';
		 
	}
	
	// Check if we are passed in an admin area
	if(!is_admin() || !(isset($_POST['_wp_http_referer']) && strstr($_POST['_wp_http_referer'], 'user-new.php'))) {
		echo $html;
	}
	
	// Now we need to hijack the sign up message so it isn't displayed
	ob_start();
	
	return false; // Returns false so that the activation email isn't sent out to the user
}

//Invitation email sent to new user. A confirmation link must be clicked before their account is created.
function activated_newuser_msg($transtext, $normtext, $domain) {
	
	switch ($normtext) {
		// Plugin page text that we want to remove
		case 'Invitation email sent to new user. A confirmation link must be clicked before their account is created.':
			$transtext = __('The new user has been created and an email containing their account details has been sent to them.');
			break;
		case 'If you change this we will send you an email at your new address to confirm it. <strong>The new address will not become active until confirmed.</strong>':
			$transtext = '';
			break;
	}
	
	return $transtext;
	
}


function activated_signup_finished() {
	// Flush the activation buffer
	ob_end_clean();
}
?>