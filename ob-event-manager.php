<?php
/**
 * Plugin Name: OB Event Manger
 * Description: Easily manage your events
 * Version:  1.0
 * Author: Oudaryamay Burai
 * Author URI: https://oudarya.wordpress.com/
 * License: GPLv2 or later
 */
include_once( plugin_dir_path( __FILE__ ) .'inc/import.php');
include_once( plugin_dir_path( __FILE__ ) .'inc/welcome.php');
include_once( plugin_dir_path( __FILE__ ) .'inc/front.php');

function obem_admin_wp_enqueue_scripts() {
  wp_enqueue_script('jquery-ui-datepicker');
  wp_enqueue_style('jquery-ui-css', plugins_url('assets/css/jquery-ui.min.css',__FILE__));
}
add_action( 'admin_enqueue_scripts', 'obem_admin_wp_enqueue_scripts' );

function obem_shortcode_wp_enqueue_scripts() {
    wp_register_style( 'get-datatable-style', plugins_url( 'assets/css/jquery.dataTables.min.css', __FILE__ ), array(), '1.0.0', 'all' );
    wp_register_style( 'get-modal-style', plugins_url( 'assets/css/jquery.modal.min.css', __FILE__ ), array(), '1.0.0', 'all' );
    wp_register_script( 'get-datatable-script', plugins_url( 'assets/js/jquery.dataTables.min.js', __FILE__ ), false, '1.4.2', true);
    wp_register_script( 'get-modal-script', plugins_url( 'assets/js/jquery.modal.min.js', __FILE__ ), false, '1.4.2', true);
}
add_action( 'wp_enqueue_scripts', 'obem_shortcode_wp_enqueue_scripts' );

add_action('admin_menu', 'obem_admin_menu');
function obem_admin_menu() {
$link_CPT = 'edit.php?post_type=ob-event';
$link_CPT_new = 'post-new.php?post_type=ob-event';
add_menu_page('ObEventManager', 'Event Manger', 'administrator', 'ObEventManager', 'obem_home',plugins_url( 'assets/img/obem-icon.png', __FILE__ ));
    add_submenu_page(
        'ObEventManager',          // parent slug
        'All Events',             // page title
        'All Events',            // menu title
        'administrator',       // capability
        $link_CPT             // callback
    ); 
    add_submenu_page(
        'ObEventManager',          // parent slug
        'New Events',             // page title
        'New Events',            // menu title
        'administrator',       // capability
        $link_CPT_new         // callback
    ); 
    add_submenu_page(
        'ObEventManager',          // parent slug
        'Import',             // page title
        'Import',            // menu title
        'administrator',       // capability
        'ObEventManagerImport',   // slug
        'obem_import'   // callback
    ); 

}
//Calling the functions
function obem_setup_postType() {
    // register the "event" custom post type
    register_post_type( 'ob-event', ['labels' => ['name' => __( 'Event','Event' ),
    'singular_name' => __( "Event", 'ob' ), 
    'add_new'            => __( 'Add New', 'Event', 'ob' ),
    'add_new_item'       => __( 'Add New Event', 'ob' ),
    'new_item'           => __( 'New Event', 'ob' ),
    'edit_item'          => __( 'Edit Event', 'ob' ),
    'view_item'          => __( 'View Event', 'ob' ),
    'all_items'          => __( 'All Events', 'ob' ),
    'update_item'           => __( 'Update Event', 'ob' ),
    'search_items'          => __( 'Search Events', 'ob' ),
    'not_found'             => __( 'No Events found', 'ob' ),
    ],'public' => 'true', 'supports' => ['title']] );
    function obem_change_title_text( $title ){
     $screen = get_current_screen();
 
     if  ( 'ob-event' == $screen->post_type ) {
          $title = 'Enter event date here';
     }
 
     return $title;
    }
     
add_filter( 'enter_title_here', 'obem_change_title_text' );
}

function obem_details_get_meta( $value ) {
  global $post;

  $field = get_post_meta( $post->ID, $value, true );
  if ( ! empty( $field ) ) {
    return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
  } else {
    return false;
  }
}

function obem_details_add_meta_box() {
  add_meta_box(
    'event_details-event-details',
    __( 'Event details', 'event_details' ),
    'obem_details_html',
    'ob-event',
    'normal',
    'high'
  );
}
add_action( 'add_meta_boxes', 'obem_details_add_meta_box' );

function obem_details_html( $post) {
  wp_nonce_field( '_event_details_nonce', 'event_details_nonce' ); ?>

  <p class="event-heading">:::Details of this event:::</p>

  <p>
    <label for="event_details_event_start_date"><?php _e( 'Event start date', 'event_details' ); ?></label><br>
    <input type="text" id="eventStartDate" class="event-input-width" name="event_details_event_start_date" id="event_details_event_start_date" value="<?php echo obem_details_get_meta( 'event_details_event_start_date' ); ?>">
  </p>  
  <p>
    <label for="event_details_event_end_date"><?php _e( 'Event end date', 'event_details' ); ?></label><br>
    <input type="text" id="eventEndDate" class="event-input-width" name="event_details_event_end_date" id="event_details_event_end_date" value="<?php echo obem_details_get_meta( 'event_details_event_end_date' ); ?>">
  </p>

    <p>
    <label for="event_details_event_start_time"><?php _e( 'Event start time', 'event_details' ); ?></label><br>
    <input type="text" id="eventStartTime" class="event-input-width" name="event_details_event_start_time" id="event_details_event_start_time" value="<?php echo obem_details_get_meta( 'event_details_event_start_time' ); ?>">
  </p>  
  <p>
    <label for="event_details_event_end_time"><?php _e( 'Event end time', 'event_details' ); ?></label><br>
    <input type="text" id="eventEndTime" class="event-input-width" name="event_details_event_end_time" id="event_details_event_end_time" value="<?php echo obem_details_get_meta( 'event_details_event_end_time' ); ?>">
  </p>

  <p>
    <label for="event_details_event_name"><?php _e( 'Event name', 'event_details' ); ?></label><br>
    <input type="text" class="event-input-width" name="event_details_event_name" id="event_details_event_name" value="<?php echo obem_details_get_meta( 'event_details_event_name' ); ?>">
  </p>  

  <p>
    <label for="event_details_event_location"><?php _e( 'Event Location', 'event_details' ); ?></label><br>
    <input type="text" class="event-input-width" name="event_details_event_location" id="event_details_event_location" value="<?php echo obem_details_get_meta( 'event_details_event_location' ); ?>">
  </p>  

    <p>
    <label for="event_details_event_category"><?php _e( 'Event Category', 'event_details' ); ?></label><br>
    <input type="text" class="event-input-width" name="event_details_event_category" id="event_details_event_category" value="<?php echo obem_details_get_meta( 'event_details_event_category' ); ?>">
  </p> 

   <p>
    <label for="event_details_event_type"><?php _e( 'Event Type', 'event_details' ); ?></label><br>
    <input type="text" class="event-input-width" name="event_details_event_type" id="event_details_event_type" value="<?php echo obem_details_get_meta( 'event_details_event_type' ); ?>">
  </p> 

  <p>
    <label for="event_details_event_description"><?php _e( 'Event Description', 'event_details' ); ?></label><br>
    <textarea class="event-input-width textarea" name="event_details_event_description" id="event_details_event_description" ><?php echo obem_details_get_meta( 'event_details_event_description' ); ?></textarea>
  
  </p>  
<style>
.event-input-width{
  width: 100%;
  height: 35px;
}
.event-input-width.textarea{
  height: 100px;
}
.event-heading {
	font-size: 18px;
	text-align: center;
	border: 1px solid #000;
	pading: 10px;
	margin: 5px;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function($) {
$('#eventStartDate').datepicker({
dateFormat : 'DD, MM dd, yy',
showButtonPanel: true,
changeMonth: true,
changeYear: true,
});
$('#eventEndDate').datepicker({
dateFormat : 'DD, MM dd, yy',
showButtonPanel: true,
changeMonth: true,
changeYear: true,
});
$('#title').datepicker({
dateFormat : 'DD, MM dd, yy',
showButtonPanel: true,
changeMonth: true,
changeYear: true,
});
});
</script>
  <?php
}

function obem_details_save( $post_id ) {
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
  if ( ! isset( $_POST['event_details_nonce'] ) || ! wp_verify_nonce( $_POST['event_details_nonce'], '_event_details_nonce' ) ) return;
  if ( ! current_user_can( 'edit_post', $post_id ) ) return;

  if ( isset( $_POST['event_details_event_start_date'] ) )
    update_post_meta( $post_id, 'event_details_event_start_date', sanitize_text_field( $_POST['event_details_event_start_date'] ) );

  if ( isset( $_POST['event_details_event_end_date'] ) )
    update_post_meta( $post_id, 'event_details_event_end_date', sanitize_text_field( $_POST['event_details_event_end_date'] ) );

  if ( isset( $_POST['event_details_event_start_time'] ) )
    update_post_meta( $post_id, 'event_details_event_start_time', sanitize_text_field( $_POST['event_details_event_start_time'] ) );

  if ( isset( $_POST['event_details_event_end_time'] ) )
    update_post_meta( $post_id, 'event_details_event_end_time', sanitize_text_field( $_POST['event_details_event_end_time'] ) );

  if ( isset( $_POST['event_details_event_name'] ) )
    update_post_meta( $post_id, 'event_details_event_name', sanitize_text_field( $_POST['event_details_event_name'] ) );

  if ( isset( $_POST['event_details_event_location'] ) )
    update_post_meta( $post_id, 'event_details_event_location', sanitize_text_field( $_POST['event_details_event_location'] ) );

  if ( isset( $_POST['event_details_event_category'] ) )
    update_post_meta( $post_id, 'event_details_event_category', sanitize_text_field( $_POST['event_details_event_category'] ) );

  if ( isset( $_POST['event_details_event_type'] ) )
    update_post_meta( $post_id, 'event_details_event_type', sanitize_text_field( $_POST['event_details_event_type'] ) );

  if ( isset( $_POST['event_details_event_description'] ) )
    update_post_meta( $post_id, 'event_details_event_description', sanitize_textarea_field( $_POST['event_details_event_description'] ) );

}
add_action( 'save_post', 'obem_details_save' );

/*
  Usage: obem_details_get_meta( 'event_details_event_start_date' )
  Usage: obem_details_get_meta( 'event_details_event_end_date' )
  Usage: obem_details_get_meta( 'event_details_event_start_time' )
  Usage: obem_details_get_meta( 'event_details_event_end_time' )
  Usage: obem_details_get_meta( 'event_details_event_name' )
  Usage: obem_details_get_meta( 'event_details_event_location' )
  Usage: obem_details_get_meta( 'event_details_event_category' )
  Usage: obem_details_get_meta( 'event_details_event_type' )
  Usage: obem_details_get_meta( 'event_details_event_description' )
  
*/

add_action( 'init', 'obem_setup_postType' );

function obem_add_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=ObEventManager">' . __( 'Go to dashboard' ) . '</a>';
    array_push( $links, $settings_link );
    return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'obem_add_settings_link' );

function obem_custom_page() {
    $page_status = get_option('obem_page_id');
    if($page_status != null) {
    $event_page_status_chg = array( 'ID' => $page_status, 'post_status' => 'publish' );
    wp_update_post($event_page_status_chg);
    } else {
    // Create post object
    $event_crt_page = array(
      'post_title'    => wp_strip_all_tags( 'Event Manager' ),
      'post_content'  => '[ob-event-manager]',
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_type'     => 'page',
    );

    // Insert the page into the database
    $event_page = wp_insert_post( $event_crt_page );
    if($event_page != null):
      add_option("obem_page_id", $event_page, '', 'yes'); 
    endif;

  }
}

function obem_activation() {
    // trigger our function that registers the custom post type
    obem_setup_postType();
    // clear the permalinks after the post type has been registered
    flush_rewrite_rules();
    //adding page for this plugin
    obem_custom_page();
  
}
register_activation_hook( __FILE__, 'obem_activation' );
function obem_deactivation() {
    // unregister the post type, so the rules are no longer in memory
    unregister_post_type( 'ob-event' );
    // clear the permalinks to remove our post type's rules from the database
    flush_rewrite_rules();
    //trash the event page
    $ob_event_page = get_option('obem_page_id');
    if($ob_event_page != null) :
    $event_page = array( 'ID' => $ob_event_page, 'post_status' => 'trash' );
    wp_update_post($event_page);
    endif;
}
register_deactivation_hook( __FILE__, 'obem_deactivation' );
