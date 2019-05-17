<?php
/**
 * Content Author Accessibility Preview
 *
 * @package content-author-accessibility-preview
 * @author Matt Rose
 * @license GPLv2
 *
 * Plugin Name:  Content Author Accessibility Preview
 * Plugin URI:   https://github.com/boswall/Content-Author-Accessibility-Preview
 * Description:  Flag up potential accessibility issues when your content authors preview the post or page that they have just added or amended
 * Version:      1.0
 * Author:       Matt Rose
 * Author URI:   https://glaikit.co.uk/
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  caa11yp
 * Domain Path:  /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'caa11yp-admin.php';

/**
 * Install - check for saved options
 */
function caa11yp_activation() {
	if ( ! get_option( 'caa11yp_options' ) ) {
		update_option(
			'caa11yp_options',
			array(
				'preview'    => 1,
				'user_roles' => true,
			)
		);
	}
}
register_activation_hook( __FILE__, 'caa11yp_activation' );

/**
 * Uninstall - remove the saved options
 */
function caa11yp_uninstall() {
	delete_option( 'caa11yp_options' );
}
register_uninstall_hook( __FILE__, 'caa11yp_uninstall' );


/**
 * Decide to include the CSS in the <head>
 */
function caa11yp_wp_head() {
	// quit if there's no user logged in.
	if ( ! is_user_logged_in() ) {
		return;
	}

	// check for options and work out if we should show the CSS.
	$options = get_option( 'caa11yp_options' );
	// clean up our options.
	$allpages   = ( isset( $options['allpages'] ) ) ? $options['allpages'] : false;
	$preview    = ( isset( $options['preview'] ) ) ? $options['preview'] : false;
	$customizer = ( isset( $options['customizer'] ) ) ? $options['customizer'] : false;
	$user_roles = ( isset( $options['user_roles'] ) ) ? $options['user_roles'] : true;

	$show_css = false;

	// main checks.
	if ( $allpages ) {
		$show_css = true;
	} elseif ( $preview && is_preview() ) {
		$show_css = true;
	} elseif ( $customizer && is_customize_preview() ) {
		$show_css = true;
	}

	// check if we need to check for user roles.
	if ( $show_css && is_array( $user_roles ) ) {
		// check for user role in allowed roles list.
		$show_css = false;

		$user = wp_get_current_user();
		foreach ( $user->roles as $role ) {
			if ( isset( $user_roles[ $role ] ) ) {
				$show_css = true;
			}
		}
	}

	/**
	 * Filter just before including the CSS.
	 *
	 * @since 1.0
	 *
	 * @param bool  $show_css True will show the accessibility CSS.
	 * @param array $options  Plugin options.
	 */
	$show_css = apply_filters( 'caa11yp_before_show_in_head', $show_css, $options );

	// finally, lets show this CSS if it is required.
	if ( $show_css ) {
		echo '<link rel="stylesheet" id="caa11yp" href="' . esc_url( plugins_url( 'assets/caa11yp.css', __FILE__ ) ) . '" type="text/css" media="all" />' . PHP_EOL;
	}
}
add_action( 'wp_head', 'caa11yp_wp_head', 100 );

/**
 * Add 'customize-preview' class to the body when in Customizer view
 *
 * @param array $classes Body Classes.
 * @return array $classes
 */
function caa11yp_body_classes( $classes ) {
	if ( is_customize_preview() ) {
		$classes[] = 'customize-preview';
	}
	return $classes;
}
add_filter( 'body_class', 'caa11yp_body_classes' );
