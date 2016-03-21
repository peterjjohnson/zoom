<?php
/**
 * Plugin Name: Zoom Integration
 * Description: Allows integration with Zoom.us API Partner Platform
 * Author: Peter Johnson
 * Version: 0.8.0
 * License: BSD 3-Clause
 */

/**
 * Set up the autoloader for this plugin
 *
 * @param string $class - The name of the as-yet undefined class
 *
 * @return void
 */
function zoom_autoload( $class )
{
    // Since our SDK is using namespaces, we need to ensure our folder structure matches
    $class = str_replace( '\\', '/', $class );
    if ( file_exists( __DIR__ . "/lib/$class.php" ) ) {
        include __DIR__ . "/lib/$class.php";
    }
}
spl_autoload_register( 'zoom_autoload' );

/**
 * Check whether the current user can host or join a meeting
 *
 * @param string $action - The action to check ( host_zoom_meeting or join_zoom_meeting )
 *
 * @return bool
 */
function zoom_current_user_can( $action )
{
    switch ( $action ) {
    case 'host_zoom_meeting':
        $user_has_permission = current_user_can( 'edit_pages' );
        break;
    case 'join_zoom_meeting':
        $user_has_permission = current_user_can( 'read' );
        break;
    case 'list_zoom_users':
        $user_has_permission = current_user_can( 'list_users' );
        break;
    case 'add_zoom_users':
        $user_has_permission = current_user_can( 'add_users' );
        break;
    default:
        $user_has_permission = false;
    }

    // Make this function hookable
    return apply_filters( 'zoom_current_user_can', $user_has_permission, $action );
}

/**
 * Return a JSON encoded error message
 *
 * @param string $message - The error message
 *
 * @return string
 */
function zoom_error( $message )
{
    return json_encode(
        array(
            'error' => $message
        )
    );
}

/**
 * Load a view template.
 *
 * @param string $template - The name of the template to load (must be the same as the file name, excluding the .php)
 * @param array  $vars     - (optional) A key-value array of data to make available to the template
 *
 * @return void
 */
function zoom_load_template( $template, $vars = array() )
{
    $file = '/templates/' . $template . '.php';

    // Check if the template exists in the active theme under the 'zoom/templates' folder.
    // If not, then we'll try to load it from within our plugin's 'templates' folder.
    if ( file_exists( get_stylesheet_directory() . '/zoom' . $file ) ) {
        include_once get_stylesheet_directory() . '/zoom' . $file;
    } elseif ( file_exists( __DIR__ . $file ) ) {
        include_once __DIR__ . $file;
    }
}

/**
 * Enqueue a Zoom Javascript file
 *
 * @param string $script - The name of the script (must be the same as the file name, excluding the .js)
 *
 * @return void
 */
function zoom_enqueue_script( $script )
{
    $resources_dir = 'zoom/resources/js/';

    // Check if the script exists in the active theme under the 'zoom/resources/js' folder.
    // If not, then we'll try to load it from within our plugin's own 'zoom/resources/js' folder.
    if ( file_exists( get_stylesheet_directory() . '/' . $resources_dir . $script . '.js' ) ) {
        wp_enqueue_script( $script, get_stylesheet_directory_uri() . '/' . $resources_dir . $script . '.js' );
    } else {
        wp_enqueue_script( $script, plugins_url( $resources_dir . $script . '.js' ) );
    }

    wp_localize_script( $script, 'zoom_ajax_' . $script, array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

/**
 * Enqueue a Zoom CSS file
 *
 * @param string $style - The name of the style (must be the same as the file name, excluding the .css)
 *
 * @return void
 */
function zoom_enqueue_style( $style )
{
    $resources_dir = 'zoom/resources/css/';

    // Check if the script exists in the active theme under the 'zoom/resources/js' folder.
    // If not, then we'll try to load it from within our plugin's own 'zoom/resources/js' folder.
    if ( file_exists( get_stylesheet_directory() . '/' . $resources_dir . $style . '.css' ) ) {
        wp_enqueue_style( $style, get_stylesheet_directory_uri() . '/' . $resources_dir . $style . '.css' );
    } else {
        wp_enqueue_style( $style, plugins_url( $resources_dir . $style . '.js' ) );
    }
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Shortcode Handlers

/**
 * Handler to display the schedule meeting view
 *
 * @param array $vars - Key-value array of attributes passed in from the shortcode
 *
 * @return void
 */
function zoom_schedule_meeting( $vars )
{
    if ( zoom_current_user_can( 'host_zoom_meeting' ) ) {
        zoom_enqueue_style( 'zoom' );
        wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/flick/jquery-ui.css' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        zoom_enqueue_script( 'schedule_meeting' );
        $vars['timezones'] = array(
            'America/New_York'    => __( 'Eastern', 'zoom' ),
            'America/Chicago'     => __( 'Central', 'zoom' ),
            'America/Denver'      => __( 'Mountain', 'zoom' ),
            'America/Phoenix'     => __( 'Mountain (no DST)', 'zoom' ),
            'America/Los_Angeles' => __( 'Pacific', 'zoom' ),
            'America/Anchorage'   => __( 'Alaska', 'zoom' ),
            'America/Adak'        => __( 'Hawaii', 'zoom' ),
            'Pacific/Honolulu'    => __( 'Hawaii (no DST)', 'zoom' ),
        );
        zoom_load_template( 'schedule_meeting', $vars );
    }
}

/**
 * Handler to display a list of users
 *
 * @param array $vars - Key-value array of attributes passed in from the shortcode
 *
 * @return void
 */
function zoom_list_users( $vars )
{
    if ( zoom_current_user_can( 'list_zoom_users' ) ) {
        zoom_enqueue_style( 'zoom' );
        $vars['user_list'] = \Zoom\User::listUsers();
        zoom_load_template( 'list_users', $vars );
    }
}

/**
 * Handler to display a meeting
 *
 * @param $vars
 */
function zoom_get_meeting( $vars )
{
    global $current_user;
    get_currentuserinfo();

    zoom_enqueue_style( 'zoom' );

    $meeting = \Zoom\Meeting::getMeeting( $vars );

    if ( zoom_current_user_can( 'host_zoom_meeting' ) && get_user_meta( $current_user->ID, 'zoom_host_id', true ) == $vars['host_id'] ) {
        $vars['start_url'] = $meeting->start_url;
    } elseif ( zoom_current_user_can( 'join_zoom_meeting' ) ) {
        $vars['join_url'] = $meeting->join_url;
    } else {
        $vars['error'] = __( 'Permission Denied', 'zoom' );
    }

    zoom_load_template( 'get_meeting', $vars );
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Connect Shortcodes

add_shortcode( 'zoom_schedule_meeting', 'zoom_schedule_meeting' );
add_shortcode( 'zoom_list_users', 'zoom_list_users' );
add_shortcode( 'zoom_get_meeting', 'zoom_get_meeting' );


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// AJAX Handlers

/**
 * Callback to schedule a meeting
 *
 * @return void
 */
function zoom_schedule_meeting_callback()
{
    // Permission check because a little paranoia goes a long way
    if ( zoom_current_user_can( 'host_zoom_meeting' ) ) {
        global $current_user;
        get_currentuserinfo();

        $user_host_id = get_user_meta( $current_user->ID, 'zoom_host_id', true );

        // If the current user doesn't yet have an host_id let's add them to Zoom and get one
        if ( empty( $user_host_id ) ) {
            $zoom_user = new \Zoom\User(
                array(
                    'email'      => $current_user->user_email,
                    'first_name' => $current_user->user_firstname,
                    'last_name'  => $current_user->user_lastname
                )
            );
            $zoom_user->custCreate();
            update_user_meta( $current_user->ID, 'zoom_host_id', $zoom_user->id ); // Attach the host id to the Wordpress user
            $user_host_id = $zoom_user->id;
        }

        $start_time = new DateTime( $_POST['date'] . ' ' . $_POST['time'], new DateTimeZone( $_POST['timezone'] ) );
        $start_time->setTimezone( new DateTimeZone( 'UTC' ) );

        // Zoom expects ISO UTC datetime but PHP formats it as an offset time (offset of 00:00)
        $_POST['start_time'] = $start_time->format( 'Y-m-d\TH:i:s\Z' );
        $_POST['host_id']    = $user_host_id;
        $meeting             = new \Zoom\Meeting( $_POST );
        $response            = apply_filters( 'zoom_schedule_meeting_callback', $meeting->create(), $start_time );
    } else {
        $response = zoom_error( __( 'Permission Denied', 'zoom' ) );
    }

    wp_send_json( $response );
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Connect AJAX callbacks
add_action( 'wp_ajax_zoom_schedule_meeting', 'zoom_schedule_meeting_callback' );