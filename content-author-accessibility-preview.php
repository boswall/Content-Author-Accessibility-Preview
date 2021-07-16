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
 * Version:      1.2.0
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

define( 'CAA11YP_VERSION', '1.1.1' );
define( 'CAA11YP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once plugin_dir_path( __FILE__ ) . 'caa11yp-admin.php';
require_once plugin_dir_path( __FILE__ ) . 'class-caa11yp-test.php';

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

		// localize script.
		wp_localize_script(
			'caa11yp',
			'caa11ypOptions',
			array(
				'container' => caa11yp_get_container( $options ),
				'tests'     => caa11yp_get_tests( $options ),
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
 * Get the array of tests for JS to work with
 *
 * @param array $options plugin options.
 * @return array tests, label, warning level, etc
 */
function caa11yp_get_tests( $options ) {
	$tests_selected = ( isset( $options['tests'] ) ) ? $options['tests'] : true;
	$all_tests      = caa11yp_get_tests_available( $options );
	if ( true === $tests_selected ) {
		return $all_tests;
	}
	$tests = array();
	foreach ( $all_tests as $test ) :
		if ( isset( $tests_selected[ $test->id ] ) && $tests_selected[ $test->id ] ) {
			$tests[] = $test;
		}
	endforeach;
	return $tests;
}

/**
 * Get the array of all available tests for JS to work with
 *
 * @param array $options plugin options.
 * @return array id, selector, label, severity
 */
function caa11yp_get_tests_available( $options = false ) {
	// TODO: Add filters.
	if ( ! $options ) {
		$options = get_option( 'caa11yp_options' );
	}

	$container = caa11yp_get_container( $options );

	$tests   = array();
	$tests[] = new Caa11yp_Test(
		'img-empty-alt',
		__( 'alt attribute is empty', 'caa11yp' ),
		__( 'Images with empty alt attributes', 'caa11yp' ),
		__( 'explanation', 'caa11yp' ),
		'low',
		array(
			'selector' => $container . ' img[alt=""]',
		)
	);
	$tests[] = new Caa11yp_Test(
		'a-new-window',
		__( 'link opens new window', 'caa11yp' ),
		__( 'Links that open new windows', 'caa11yp' ),
		__( 'explanation', 'caa11yp' ),
		'low',
		array(
			'selector' => $container . ' a[target]:not([target="_self"])',
		)
	);
	$tests[] = new Caa11yp_Test(
		'a-has-title',
		__( 'has title attribute', 'caa11yp' ),
		__( 'Links that have a title attribute', 'caa11yp' ),
		__( 'explanation', 'caa11yp' ),
		'high',
		array(
			'selector' => $container . ' a[title]',
		)
	);
	$tests[] = new Caa11yp_Test(
		'img-no-alt',
		__( 'alt attribute is empty', 'caa11yp' ),
		__( 'Images that have no alt attribute', 'caa11yp' ),
		__( 'explanation', 'caa11yp' ),
		'high',
		array(
			'selector' => $container . ' img:not([alt])',
		)
	);
	$tests[] = new Caa11yp_Test(
		'img-has-title',
		__( 'has title attribute', 'caa11yp' ),
		__( 'Images that have a title attribute', 'caa11yp' ),
		__( 'explanation', 'caa11yp' ),
		'high',
		array(
			'selector' => $container . ' img[title]',
		)
	);
	$tests[] = new Caa11yp_Test(
		'img-svg-no-role',
		__( 'missing role="img"', 'caa11yp' ),
		__( 'SVG files that don`t have role="img"', 'caa11yp' ),
		__( 'explanation', 'caa11yp' ),
		'high',
		array(
			'selector' => $container . ' img[src$=".svg"]:not([role="img"])',
		)
	);
	$tests[] = new Caa11yp_Test(
		'svg-no-role',
		__( 'missing role="img"', 'caa11yp' ),
		__( 'Inline SVGs that don`t have role="img"', 'caa11yp' ),
		__( 'explanation', 'caa11yp' ),
		'high',
		array(
			'selector' => $container . ' svg:not([role="img"])',
		)
	);
	$tests[] = new Caa11yp_Test(
		'heading-empty',
		__( 'empty heading', 'caa11yp' ),
		__( 'Heading tag that has no content', 'caa11yp' ),
		__( 'explanation', 'caa11yp' ),
		'high',
		array(
			'selector' => $container . ' h1:empty, ' . $container . ' h2:empty, ' . $container . ' h3:empty, ' . $container . ' h4:empty, ' . $container . ' h5:empty, ' . $container . ' h6:empty',
		)
	);
	$tests[] = new Caa11yp_Test(
		'a-empty',
		__( 'empty link', 'caa11yp' ),
		__( 'Link that has no content', 'caa11yp' ),
		__( 'explanation', 'caa11yp' ),
		'high',
		array(
			'selector' => $container . ' a:not([name]):empty',
		)
	);
	$tests[] = new Caa11yp_Test(
		'button-empty',
		__( 'empty button', 'caa11yp' ),
		__( 'Button that has no content', 'caa11yp' ),
		__( 'explanation', 'caa11yp' ),
		'high',
		array(
			'selector' => $container . ' button:empty',
		)
	);
	$tests[] = new Caa11yp_Test(
		'th-empty',
		__( 'empty header cell', 'caa11yp' ),
		__( 'Table heading that has no content', 'caa11yp' ),
		__( 'explanation', 'caa11yp' ),
		'high',
		array(
			'selector' => $container . ' th:empty',
		)
	);
	$tests[] = new Caa11yp_Test(
		'td-empty',
		__( 'empty data cell', 'caa11yp' ),
		__( 'Table cell that has no content', 'caa11yp' ),
		__( 'explanation', 'caa11yp' ),
		'low',
		array(
			'selector' => $container . ' td:empty',
		)
	);
	return $tests;
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
