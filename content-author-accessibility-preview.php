<?php
/*
Plugin Name:  Content Author Accessibility Preview
Plugin URI:   https://glaikit.co.uk/
Description:  Flag up potential accessibility issues when your content authors preview the post or page that they have just added or amended
Version:      1.0
Author:       Matt Rose
Author URI:   https://glaikit.co.uk/
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  caa11yp
Domain Path:  /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
* install - check for saved options
*/
function caa11yp_activation() {
  if ( ! get_option( 'caa11yp_options' ) ) {
    update_option(
      'caa11yp_options',
      array(
        'preview' => 1,
        'user_roles' => true,
      )
    );
  }
}
register_activation_hook( __FILE__, 'caa11yp_activation' );

/**
* uninstall - remove the saved options
*/
function caa11yp_uninstall() {
  delete_option( 'caa11yp_options' );
}
register_uninstall_hook( __FILE__, 'caa11yp_uninstall' );

/**
* put settings page link on plugin actions
*/
function caa11yp_add_settings_link( $links, $file ){
  if( $file == plugin_basename( __FILE__ ) ){
    $settings_link = '<a href="' . admin_url( 'options-general.php?page=caa11yp' ) . '">' . __('Settings', 'caa11yp') . '</a>';
    array_unshift( $links, $settings_link );
  }
  return $links;
}
add_filter( 'plugin_action_links', 'caa11yp_add_settings_link', 2, 10 );

/**
* register the settings page
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
    'caa11yp_options[allpages]',
    __( 'Show site-wide', 'caa11yp' ),
    'caa11yp_allpages_cb',
    'caa11yp',
    'caa11yp_section_visibility'
  );

  add_settings_field(
    'caa11yp_options[preview]',
    __( 'Show in Preview', 'caa11yp' ),
    'caa11yp_preview_cb',
    'caa11yp',
    'caa11yp_section_visibility'
  );

  add_settings_field(
    'caa11yp_options[customizer]',
    __( 'Show in Customizer', 'caa11yp' ),
    'caa11yp_customizer_cb',
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
* callback functions for settings page:
*/
function caa11yp_section_visibility_cb( $args ) {
}

function caa11yp_allpages_cb( $args ) {
  $options = get_option( 'caa11yp_options' );
  $allpages = ( isset( $options['allpages'] ) ) ? $options['allpages'] : 0;
  ?>
  <input type="checkbox" name="caa11yp_options[allpages]" value="1" <?php checked( 1, $allpages, true ); ?> />
  <?php
}

function caa11yp_preview_cb( $args ) {
  $options = get_option( 'caa11yp_options' );
  $preview = ( isset( $options['preview'] ) ) ? $options['preview'] : 0;
  ?>
  <input type="checkbox" name="caa11yp_options[preview]" value="1" <?php checked( 1, $preview, true ); ?> />
  <?php
}

function caa11yp_customizer_cb( $args ) {
  $options = get_option( 'caa11yp_options' );
  $customizer = ( isset( $options['customizer'] ) ) ? $options['customizer'] : 0;
  ?>
  <input type="checkbox" name="caa11yp_options[customizer]" value="1" <?php checked( 1, $customizer, true ); ?> />
  <?php
}

function caa11yp_user_roles_cb( $args ) {
  $options = get_option( 'caa11yp_options' );
  $user_roles = ( isset( $options['user_roles'] ) ) ? $options['user_roles'] : true;
  foreach( wp_roles()->roles as $key => $role ) :
    if ( true === $user_roles ) {
      $user_role = true;
    } else {
      $user_role = ( isset( $user_roles[$key] ) ) ? $user_roles[$key] : 0;
    }
    ?>
    <label>
      <input type="checkbox" name="caa11yp_options[user_roles][<?php echo $key; ?>]" value="1" <?php checked( 1, $user_role, true ); ?> />
      <?php echo $role['name']; ?>
    </label><br>
    <?php
  endforeach;
}

function caa11yp_options_validate_input( $input ) {
  // check for no user roles selected (make it All selected)
  if ( ! isset( $input['user_roles'] ) ) {
    $input['user_roles'] = true;
  } else {
    // check for ALL roles selected
    $user_roles = true;
    foreach( wp_roles()->roles as $key => $role ) {
      if ( ! array_key_exists( $key, $input['user_roles'] ) ) $user_roles = $input['user_roles'];
    }
    $input['user_roles'] = $user_roles;
  }

  return apply_filters( 'caa11yp_options_validate_input', $input );
}

/**
* add settings page to menu
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
* top level menu:
* callback functions
*/
function caa11yp_options_page_html() {
  // check user capabilities
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
      print_r( get_option( 'caa11yp_options' ) );
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

  function caa11yp_wp_head() {
    // quit if there's no user logged in
    if ( ! is_user_logged_in() ) return;

    // check for options and work out if we should show the CSS
    $options = get_option( 'caa11yp_options' );
    // clean up our options
    $allpages = ( isset( $options['allpages'] ) ) ? $options['allpages'] : false;
    $preview = ( isset( $options['preview'] ) ) ? $options['preview'] : false;
    $customizer = ( isset( $options['customizer'] ) ) ? $options['customizer'] : false;
    $user_roles = ( isset( $options['user_roles'] ) ) ? $options['user_roles'] : true;

    $show_a11y_CSS = false;

    // main checks
    if ( $allpages ) {
      $show_a11y_CSS = true;
    } elseif ( $preview && is_preview() ) {
      $show_a11y_CSS = true;
    } elseif ( $customizer && is_customize_preview() ) {
      $show_a11y_CSS = true;
    }

    // check if we need to check for user roles
    if ( $show_a11y_CSS && is_array( $user_roles ) ) {
      // check for user role in allowed roles list
      $show_a11y_CSS = false;
      $user = wp_get_current_user();
      foreach ( $user->roles as $role ) {
        if ( isset( $user_roles[$role] ) ) $show_a11y_CSS = true;
      }
    }

    // apply some filters in case anyone wants to hook in
    $show_a11y_CSS = apply_filters( 'caa11yp_before_show_in_head', $show_a11y_CSS, $options );

    // finally, lets show this CSS if it is required
    if ( $show_a11y_CSS ) {
      echo '<link rel="stylesheet" id="caa11yp" href="' . plugins_url( 'assets/caa11yp.css', __FILE__) . '" type="text/css" media="all" />' . PHP_EOL;
    }
  }
  add_action( 'wp_head', 'caa11yp_wp_head', 100 );


  function caa11yp_body_classes( $classes ) {
    if ( is_customize_preview() ) {
      $classes[] = 'customize-preview';
    }
    return $classes;
  }
  add_filter('body_class', 'caa11yp_body_classes');
