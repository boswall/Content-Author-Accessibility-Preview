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

define( 'CAA11YP_VERSION', 1 );

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
 * Decide to include the CSS in the <head> and JS in the <footer>
 */
function caa11yp_enqueue_scripts() {
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

	$enqueue = false;

	// main checks.
	if ( $allpages ) {
		$enqueue = true;
	} elseif ( $preview && is_preview() ) {
		$enqueue = true;
	} elseif ( $customizer && is_customize_preview() ) {
		$enqueue = true;
	}

	// check if we need to check for user roles.
	if ( $enqueue && is_array( $user_roles ) ) {
		// check for user role in allowed roles list.
		$enqueue = false;

		$user = wp_get_current_user();
		foreach ( $user->roles as $role ) {
			if ( isset( $user_roles[ $role ] ) ) {
				$enqueue = true;
			}
		}
	}

	/**
	 * Filter just before including the CSS.
	 *
	 * @since 1.0
	 *
	 * @param bool  $enqueue True will include the accessibility CSS and JS in the page.
	 * @param array $options Plugin options.
	 */
	$enqueue = apply_filters( 'caa11yp_before_enqueue_scripts', $enqueue, $options );

	// finally, lets enqueue this CSS and JS if it is required.
	if ( $enqueue ) {
		wp_enqueue_script( 'caa11yp', plugins_url( 'assets/caa11yp.js', __FILE__ ), array(), CAA11YP_VERSION, true );
		wp_register_style( 'caa11yp', plugins_url( 'assets/caa11yp.css', __FILE__ ), false, CAA11YP_VERSION );
		wp_enqueue_style( 'caa11yp' );
		// TODO: inline styling.
		// $custom_css = '.mycolor{background: red;}';
		// wp_add_inline_style( 'caa11yp', $custom_css );

		// localize script.
		wp_localize_script(
			'caa11yp',
			'caa11ypOptions',
			array(
				'container' => caa11yp_get_container( $options ),
				'selectors' => caa11yp_get_selectors( $options ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'caa11yp_enqueue_scripts', 100 );

/**
 * Get the CSS selector of the container for JS to work inside
 *
 * @param array $options plugin options.
 * @return string container  CSS selector
 */
function caa11yp_get_container( $options ) {
	$container = ( isset( $options['container'] ) ) ? $options['container'] : '';
	return $container;
}

/**
 * Get the array of selectors for JS to work with
 *
 * @param array $options plugin options.
 * @return array selectors, label, warning level, etc
 */
function caa11yp_get_selectors( $options ) {
	$selectors = ( isset( $options['selectors'] ) ) ? $options['selectors'] : array();
	return $selectors;
}

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
