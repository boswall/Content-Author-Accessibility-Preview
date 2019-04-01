<?php
/**
 * Content Author Accessibility Preview
 *
 * @package content-author-accessibility-preview
 * @author Matt Rose
 * @license GPLv2
 *
 * Plugin Name:  Content Author Accessibility Preview
 * Plugin URI:   https://glaikit.co.uk/
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
 * Put settings page link on plugin actions
 *
 * @param  array  $links HTML links.
 * @param  string $file Filename of plugin.
 * @return array $links
 */
function caa11yp_add_settings_link( $links, $file ) {
	if ( plugin_basename( __FILE__ ) === $file ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=caa11yp' ) . '">' . __( 'Settings', 'caa11yp' ) . '</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_filter( 'plugin_action_links', 'caa11yp_add_settings_link', 2, 10 );

/**
 * Register the settings page
 */
function caa11yp_settings_init() {
	register_setting( 'caa11yp', 'caa11yp_options', 'caa11yp_options_validate_input' );

	add_settings_section(
		'caa11yp_section_visibility',
		__( 'Visibility', 'caa11yp' ),
		'caa11yp_section_visibility_cb',
		'caa11yp'
	);

	add_settings_field(
		'caa11yp_options[views]',
		__( 'Show on specific views', 'caa11yp' ),
		'caa11yp_views_cb',
		'caa11yp',
		'caa11yp_section_visibility'
	);

	add_settings_field(
		'caa11yp_options[user_roles]',
		__( 'Show to User Roles', 'caa11yp' ),
		'caa11yp_user_roles_cb',
		'caa11yp',
		'caa11yp_section_visibility'
	);
}
add_action( 'admin_init', 'caa11yp_settings_init' );

/**
 * Callback functions for settings page:
 */

/**
 * Top of the Visibility Section
 *
 * @param array $args Arguments.
 */
function caa11yp_section_visibility_cb( $args ) {
}

/**
 * Settings views callback.
 *
 * @param array $args Arguments.
 */
function caa11yp_views_cb( $args ) {
	$options    = get_option( 'caa11yp_options' );
	$allpages   = ( isset( $options['allpages'] ) ) ? $options['allpages'] : 0;
	$preview    = ( isset( $options['preview'] ) ) ? $options['preview'] : 0;
	$customizer = ( isset( $options['customizer'] ) ) ? $options['customizer'] : 0;
	?>
	<input type="checkbox" id="caa11yp_options_allpages" name="caa11yp_options[allpages]" value="1" <?php checked( 1, $allpages, true ); ?> />
	<label for="caa11yp_options_allpages"><?php esc_html_e( 'Show site-wide', 'caa11yp' ); ?></label><br>
	<input type="checkbox" id="caa11yp_options_preview" name="caa11yp_options[preview]" value="1" <?php checked( 1, $preview, true ); ?> />
	<label for="caa11yp_options_preview"><?php esc_html_e( 'Show in Preview', 'caa11yp' ); ?></label><br>
	<input type="checkbox" id="caa11yp_options_customizer" name="caa11yp_options[customizer]" value="1" <?php checked( 1, $customizer, true ); ?> />
	<label for="caa11yp_options_customizer"><?php esc_html_e( 'Show in Customizer', 'caa11yp' ); ?></label><br>
	<?php
}

/**
 * Settings user roles callback.
 *
 * @param array $args Arguments.
 */
function caa11yp_user_roles_cb( $args ) {
	$options    = get_option( 'caa11yp_options' );
	$user_roles = ( isset( $options['user_roles'] ) ) ? $options['user_roles'] : true;
	foreach ( wp_roles()->roles as $key => $role ) :
		if ( true === $user_roles ) {
			$user_role = true;
		} else {
			$user_role = ( isset( $user_roles[ $key ] ) ) ? $user_roles[ $key ] : 0;
		}
		?>
		<input type="checkbox" id="caa11yp_options_user_roles_<?php echo esc_attr( $key ); ?>" name="caa11yp_options[user_roles][<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( 1, $user_role, true ); ?> />
		<label for="caa11yp_options_user_roles_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $role['name'] ); ?></label><br>
		<?php
	endforeach;
}

/**
 * Validate user input options
 *
 * @param  array $input User inputted fields.
 * @return array $input User inputted fields.
 */
function caa11yp_options_validate_input( $input ) {
	// check for no user roles selected (make it All selected).
	if ( ! isset( $input['user_roles'] ) ) {
		$input['user_roles'] = true;
	} else {
		// check for ALL roles selected.
		$user_roles = true;
		foreach ( wp_roles()->roles as $key => $role ) {
			if ( ! array_key_exists( $key, $input['user_roles'] ) ) {
				$user_roles = $input['user_roles'];
			}
		}
		$input['user_roles'] = $user_roles;
	}

	/**
	 * Validate admin options for Content Author Accessibility Preview.
	 *
	 * Check option inputs before saving them to the options.
	 *
	 * @since 1.0
	 *
	 * @param array $input User inputted fields.
	 */
	return apply_filters( 'caa11yp_options_validate_input', $input );
}

/**
 * Add settings page to admin menu
 */
function caa11yp_options_page() {
	add_options_page(
		'Content Author Accessibility Preview',
		'Content Author Accessibility Preview',
		'manage_options',
		'caa11yp',
		'caa11yp_options_page_html'
	);
}
add_action( 'admin_menu', 'caa11yp_options_page' );

/**
 * Top level menu:
 * callback functions
 */
function caa11yp_options_page_html() {
	// check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'caa11yp' );
			do_settings_sections( 'caa11yp' );
			submit_button( esc_html( 'Save Settings', 'caa11yp' ) );
			?>
		</form>
	</div>
	<div class="wrap">
		<h2><?php esc_html_e( 'Information', 'caa11yp' ); ?></h2>
		<p><?php esc_html_e( 'Flag up potential accessibility issues when your content authors preview the post or page that they have just added or amended.', 'caa11yp' ); ?></p>
		<p><?php esc_html_e( 'Site visitors who are not logged in will not see the potential issues.', 'caa11yp' ); ?></p>
		<p><?php esc_html_e( 'Currently contains checks for:', 'caa11yp' ); ?></p>
		<ul class="ul-disc">
			<li><?php esc_html_e( 'Images with empty alt attributes', 'caa11yp' ); ?></li>
			<li><?php esc_html_e( 'Links that open new windows', 'caa11yp' ); ?></li>
			<li><?php esc_html_e( 'Links that have a title attribute', 'caa11yp' ); ?></li>
			<li><?php esc_html_e( 'images that have no alt attribute', 'caa11yp' ); ?></li>
			<li><?php esc_html_e( 'images that have the title attribute', 'caa11yp' ); ?></li>
			<li><?php esc_html_e( 'svg files that don\'t have role="img"', 'caa11yp' ); ?></li>
			<li><?php esc_html_e( 'inline svgs that don\'t have role="img"', 'caa11yp' ); ?></li>
			<li><?php esc_html_e( 'empty headings', 'caa11yp' ); ?></li>
			<li><?php esc_html_e( 'empty links', 'caa11yp' ); ?></li>
			<li><?php esc_html_e( 'empty buttons', 'caa11yp' ); ?></li>
			<li><?php esc_html_e( 'empty headings', 'caa11yp' ); ?></li>
			<li><?php esc_html_e( 'empty table header cells', 'caa11yp' ); ?></li>
			<li><?php esc_html_e( 'empty table data cells', 'caa11yp' ); ?></li>
		</ul>
		<p><?php esc_html_e( 'Flags each element found with an outline. Where possible explains what the issue is on the page.', 'caa11yp' ); ?></p>
	</div>
	<?php
}

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
