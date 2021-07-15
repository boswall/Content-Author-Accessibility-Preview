<?php
/**
 * Content Author Accessibility Preview
 *
 * @package content-author-accessibility-preview
 * @author Matt Rose
 * @license GPLv2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Put settings page link on plugin actions
 *
 * @param array $links HTML links.
 * @return array $links
 */
function caa11yp_add_settings_link( $links ) {
	$links = array_merge(
		array(
			'<a href="' . admin_url( 'options-general.php?page=caa11yp' ) . '">' . __( 'Settings', 'caa11yp' ) . '</a>',
		),
		$links
	);
	return $links;
}
add_action( 'plugin_action_links_' . CAA11YP_PLUGIN_BASENAME, 'caa11yp_add_settings_link' );


/**
 * Enqueue a script on our settings page.
 *
 * @param int $hook Hook suffix for the current admin page.
 */
function caa11yp_admin_enqueue_scripts( $hook ) {
	if ( 'settings_page_caa11yp' !== $hook ) {
			return;
	}
	wp_enqueue_script( 'caa11yp_admin', plugin_dir_url( __FILE__ ) . 'assets/caa11yp-admin.js', array( 'jquery' ), CAA11YP_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'caa11yp_admin_enqueue_scripts' );

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

	add_settings_field(
		'caa11yp_options[container]',
		__( 'Container', 'caa11yp' ),
		'caa11yp_container_cb',
		'caa11yp',
		'caa11yp_section_visibility'
	);

	add_settings_field(
		'caa11yp_options[tests]',
		__( 'Tests', 'caa11yp' ),
		'caa11yp_tests_cb',
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
	<input type="checkbox" id="caa11yp_options_preview" name="caa11yp_options[preview]" class="depends-allpages" value="1" <?php checked( 1, $preview, true ); ?> />
	<label for="caa11yp_options_preview"><?php esc_html_e( 'Show in Preview', 'caa11yp' ); ?></label><br>
	<input type="checkbox" id="caa11yp_options_customizer" name="caa11yp_options[customizer]" class="depends-allpages" value="1" <?php checked( 1, $customizer, true ); ?> />
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
		<input type="checkbox" id="caa11yp_options_user_roles_<?php echo esc_attr( $key ); ?>" class="caa11yp_options_user_roles" name="caa11yp_options[user_roles][<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( 1, $user_role, true ); ?> />
		<label for="caa11yp_options_user_roles_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $role['name'] ); ?></label><br>
		<?php
	endforeach;
	?>
	<div id="setting-error-caa11yp_user_roles" class="error settings-error notice" style="display: none;">
<p><strong><?php esc_html_e( 'You must select at least 1 User Role.', 'caa11yp' ); ?></strong></p></div>
	<?php
}

/**
 * Settings container callback.
 *
 * @param array $args Arguments.
 */
function caa11yp_container_cb( $args ) {
	$options   = get_option( 'caa11yp_options' );
	$container = ( isset( $options['container'] ) ) ? $options['container'] : '';
	?>
	<input id="caa11yp_options_container" name="caa11yp_options[container]" size="40" type="text" value="<?php echo esc_html( $container ); ?>" /><br>
	<label for="caa11yp_options_container"><?php _e( 'Enter a CSS selector of the main content area. Will limit the checks to that area. For example: <code>#primary</code>, <code>#main</code> or <code>.site-content</code> depending on your theme.', 'caa11yp' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction ?></label>
	<?php
}

/**
 * Settings tests callback.
 *
 * @param array $args Arguments.
 */
function caa11yp_tests_cb( $args ) {
	$options   = get_option( 'caa11yp_options' );
	$tests     = ( isset( $options['tests'] ) ) ? $options['tests'] : true;
	$all_tests = caa11yp_get_tests_available( $options );
	foreach ( $all_tests as $test ) :
		if ( true === $tests ) {
			$test_selected = true;
		} else {
			$test_selected = ( isset( $tests[ $test->id ] ) ) ? $tests[ $test->id ] : 0;
		}
		?>
		<input type="checkbox" id="caa11yp_options_tests_<?php echo esc_attr( $test->id ); ?>" class="caa11yp_options_tests" name="caa11yp_options[tests][<?php echo esc_attr( $test->id ); ?>]" value="1" <?php checked( 1, $test_selected, true ); ?> />
		<label for="caa11yp_options_tests_<?php echo esc_attr( $test->id ); ?>">
			<strong><?php echo esc_html( $test->label ); ?>: </strong>
			<?php echo esc_html( $test->description ); ?>
		</label><br>
		<?php
	endforeach;
	?>
	<div id="setting-error-caa11yp_tests" class="error settings-error notice" style="display: none;">
		<p><strong><?php esc_html_e( 'You must select at least 1 Test.', 'caa11yp' ); ?></strong></p>
	</div>
	<?php
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

	// check for no tests selected (make it All selected).
	if ( ! isset( $input['tests'] ) ) {
		$input['tests'] = true;
	} else {
		// check for ALL tests selected.
		$tests = true;
		foreach ( caa11yp_get_tests_available() as $test ) {
			if ( ! array_key_exists( $test->id, $input['tests'] ) ) {
				$tests = $input['tests'];
			}
		}
		$input['tests'] = $tests;
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
		__( 'Content Author Accessibility Preview', 'caa11yp' ),
		__( 'Content Author Accessibility Preview', 'caa11yp' ),
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
			<li><?php _e( 'Images with empty <code>alt</code> attributes', 'caa11yp' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction ?></li>
			<li><?php esc_html_e( 'Links that open new windows', 'caa11yp' ); ?></li>
			<li><?php _e( 'Links that have a <code>title</code> attribute', 'caa11yp' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction ?></li>
			<li><?php _e( 'images that have no <code>alt</code> attribute', 'caa11yp' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction ?></li>
			<li><?php _e( 'images that have a <code>title</code> attribute', 'caa11yp' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction ?></li>
			<li><?php _e( 'svg files that don`t have <code>role="img"</code>', 'caa11yp' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction ?></li>
			<li><?php _e( 'inline svgs that don`t have <code>role="img"</code>', 'caa11yp' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction ?></li>
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
