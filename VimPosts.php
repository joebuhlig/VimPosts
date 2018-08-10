<?php
/*
Plugin Name: VimPosts
Plugin URI: https://github.com/joebuhlig/vimposts
Description: This plugin adds some custom abilities for posting Vimeo videos.
Version: 1.0.0
Author: Joe Buhlig
Author URI: http://joebuhlig.com
GitHub Plugin URI: https://github.com/joebuhlig/vimposts
License: GPL2
 */

 // This is the secret key for API authentication. You configured it in the settings menu of the license manager plugin.
 define('YOUR_SPECIAL_SECRET_KEY', '5b6de157e40746.44165099'); //Rename this constant name so it is specific to your plugin or theme.

 // This is the URL where API query request will be sent to. This should be the URL of the site where you have installed the main license manager plugin. Get this value from the integration help page.
 define('YOUR_LICENSE_SERVER_URL', 'http://localhost:8888/wordpress_dev1'); //Rename this constant name so it is specific to your plugin or theme.

 // This is a value that will be recorded in the license manager data so you can identify licenses for this item/product.
 define('YOUR_ITEM_REFERENCE', 'VimPosts'); //Rename this constant name so it is specific to your plugin or theme.

 add_action('admin_menu', 'vimposts_license_menu');

 function vimposts_license_menu() {
     add_options_page('VimPosts License Activation Menu', 'VimPosts License', 'manage_options', __FILE__, 'vimposts_license_management_page');
 }

 function vimposts_license_management_page() {
     echo '<div class="wrap">';
     echo '<h2>VimPosts License Management</h2>';

     /*** License activate button was clicked ***/
     if (isset($_REQUEST['activate_license'])) {
         $license_key = $_REQUEST['vimposts_license_key'];

         // API query parameters
         $api_params = array(
             'slm_action' => 'slm_activate',
             'secret_key' => YOUR_SPECIAL_SECRET_KEY,
             'license_key' => $license_key,
             'registered_domain' => $_SERVER['SERVER_NAME'],
             'item_reference' => urlencode(YOUR_ITEM_REFERENCE),
         );

         // Send query to the license manager server
         $query = esc_url_raw(add_query_arg($api_params, YOUR_LICENSE_SERVER_URL));
         $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));

         // Check for error in the response
         if (is_wp_error($response)){
             echo "Unexpected Error! The query returned with an error.";
         }

         //var_dump($response);//uncomment it if you want to look at the full response

         // License data.
         $license_data = json_decode(wp_remote_retrieve_body($response));

         // TODO - Do something with it.
         //var_dump($license_data);//uncomment it to look at the data

         if($license_data->result == 'success'){//Success was returned for the license activation

             //Uncomment the followng line to see the message that returned from the license server
             echo '<br />The following message was returned from the server: '.$license_data->message;

             //Save the license key in the options table
             update_option('vimposts_license_key', $license_key);
         }
         else{
             //Show error to the user. Probably entered incorrect license key.

             //Uncomment the followng line to see the message that returned from the license server
             echo '<br />The following message was returned from the server: '.$license_data->message;
         }

     }
     /*** End of license activation ***/

     /*** License activate button was clicked ***/
     if (isset($_REQUEST['deactivate_license'])) {
         $license_key = $_REQUEST['vimposts_license_key'];

         // API query parameters
         $api_params = array(
             'slm_action' => 'slm_deactivate',
             'secret_key' => YOUR_SPECIAL_SECRET_KEY,
             'license_key' => $license_key,
             'registered_domain' => $_SERVER['SERVER_NAME'],
             'item_reference' => urlencode(YOUR_ITEM_REFERENCE),
         );

         // Send query to the license manager server
         $query = esc_url_raw(add_query_arg($api_params, YOUR_LICENSE_SERVER_URL));
         $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));

         // Check for error in the response
         if (is_wp_error($response)){
             echo "Unexpected Error! The query returned with an error.";
         }

         //var_dump($response);//uncomment it if you want to look at the full response

         // License data.
         $license_data = json_decode(wp_remote_retrieve_body($response));

         // TODO - Do something with it.
         //var_dump($license_data);//uncomment it to look at the data

         if($license_data->result == 'success'){//Success was returned for the license activation

             //Uncomment the followng line to see the message that returned from the license server
             echo '<br />The following message was returned from the server: '.$license_data->message;

             //Remove the licensse key from the options table. It will need to be activated again.
             update_option('vimposts_license_key', '');
         }
         else{
             //Show error to the user. Probably entered incorrect license key.

             //Uncomment the followng line to see the message that returned from the license server
             echo '<br />The following message was returned from the server: '.$license_data->message;
         }

     }
     /*** End of sample license deactivation ***/

     ?>
     <p>Please enter the license key for this product to activate it. You were given a license key when you purchased this item.</p>
     <form action="" method="post">
         <table class="form-table">
             <tr>
                 <th style="width:100px;"><label for="vimposts_license_key">License Key</label></th>
                 <td ><input class="regular-text" type="text" id="vimposts_license_key" name="vimposts_license_key"  value="<?php echo get_option('vimposts_license_key'); ?>" ></td>
             </tr>
         </table>
         <p class="submit">
             <input type="submit" name="activate_license" value="Activate" class="button-primary" />
             <input type="submit" name="deactivate_license" value="Deactivate" class="button" />
         </p>
     </form>
     <?php

     echo '</div>';
 }

function create_vimpost_taxonomies() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Groups', 'taxonomy general name' ),
		'singular_name'     => _x( 'Group', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Groups' ),
		'all_items'         => __( 'All Groups' ),
		'parent_item'       => __( 'Parent Group' ),
		'parent_item_colon' => __( 'Parent Group:' ),
		'edit_item'         => __( 'Edit Group' ),
		'update_item'       => __( 'Update Group' ),
		'add_new_item'      => __( 'Add New Group' ),
		'new_item_name'     => __( 'New Group Name' ),
		'menu_name'         => __( 'Groups' ),
	);

	$args = array(
		'hierarchical'      => true,
		'public'	    => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'groups' )
	);

	register_taxonomy( 'groups', array( 'vimpost' ), $args );
}

 function create_vimpost_posttype() {
// set up labels
	$labels = array(
 		'name' => 'VimPosts',
    	'singular_name' => 'VimPost',
    	'add_new' => 'Add New VimPost',
    	'add_new_item' => 'Add New VimPost',
    	'edit_item' => 'Edit VimPost',
    	'new_item' => 'New VimPost',
    	'all_items' => 'All VimPosts',
    	'view_item' => 'View VimPost',
    	'search_items' => 'Search VimPosts',
    	'not_found' =>  'No VimPosts Found',
    	'not_found_in_trash' => 'No VimPosts found in Trash',
    	'parent_item_colon' => '',
    	'menu_name' => 'VimPosts',
    	);
  register_post_type( 'vimpost',
    array(
	'labels' => $labels,
	'has_archive' => true,
	'public' => true,
	'publicly_queryable' => true,
	'query_var' => true,
	'supports' => array( 'title', 'editor', 'page-attributes'),
	'taxonomies' => array( 'post_tag', 'groups' ),
	'exclude_from_search' => false,
	'capability_type' => 'post',
	'rewrite' => array( 'slug' => '%groups%' ),
	'menu_icon' => 'dashicons-format-video',
    )
  );
}

/* Meta box setup function. */
function vimposts_meta_boxes_setup() {

  /* Add meta boxes on the 'add_meta_boxes' hook. */
  add_action( 'add_meta_boxes', 'vimposts_add_post_meta_boxes' );
}

function vimposts_add_post_meta_boxes() {

  add_meta_box(
    'wp-vimposts',      // Unique ID
    esc_html__( 'WP VimPosts Settings', 'example' ),    // Title
    'vimposts_meta_box',   // Callback function
    'vimpost',         // Admin page (or post type)
    'normal',         // Context
    'high'         // Priority
  );
}

/* Display the post meta box. */
function vimposts_meta_box( $object, $box ) { ?>

  <?php wp_nonce_field( basename( __FILE__ ), 'vimposts_nonce' ); ?>

  <p>
    <label for="vimeo-link"><?php _e( "Vimeo ID", 'example' ); ?></label>
    <br />
    <input type="text" name="vimeo-link" id="vimeo-link" value="<?php echo esc_attr( get_post_meta( $object->ID, 'vimeo_link', true ) ); ?>" size="30" />
    <br />
    <label for="video-duration"><?php _e( "Video Duration", 'example' ); ?></label>
    <br />
    <input type="text" name="video-duration" id="video-duration" value="<?php echo esc_attr( get_post_meta( $object->ID, 'video_duration', true ) ); ?>" size="30" placeholder="hh:mm:ss" />
    </p>
<?php }

/* Save the meta box's post metadata. */
function vimposts_save_post_class_meta( $post_id ) {
  global $post;

  /* Verify the nonce before proceeding. */
  if ( !isset( $_POST['vimposts_nonce'] ) || !wp_verify_nonce( $_POST['vimposts_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  /* Get the posted data and sanitize it for use as an HTML class. */
  $new_vimeo_link_value = ( isset( $_POST['vimeo-link'] ) ? $_POST['vimeo-link'] : '' );
  $new_video_duration_value = ( isset( $_POST['video-duration'] ) ? $_POST['video-duration'] : '' );
  $new_video_sort_value = ( isset( $_POST['video-sort'] ) ? $_POST['video-sort'] : '' );


  update_vimposts_meta($post->ID, 'vimeo_link', $new_vimeo_link_value);
  update_vimposts_meta($post->ID, 'video_duration', $new_video_duration_value);
  update_vimposts_meta($post->ID, 'video_sort', $new_video_sort_value);
}

function update_vimposts_meta($post_id, $meta_key, $new_meta_value){
  /* Get the meta value of the custom field key. */
  $meta_value = get_post_meta( $post_id, $meta_key, true );

  /* If a new meta value was added and there was no previous value, add it. */
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );

  /* If the new meta value does not match the old value, update it. */
  elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post_id, $meta_key, $new_meta_value );

  /* If there is no new meta value but an old value exists, delete it. */
  elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );
}

function get_custom_post_type_template($single_template) {
     global $post;

     if ($post->post_type == 'video') {
          $single_template = dirname( __FILE__ ) . '/single-video.php';
     }
     return $single_template;
}

function vimpost_main_shortcode_func($atts){
	global $post;
	$id = $post->ID;
	$output = '';
	if ((get_post_meta( $id, "vimeo_link", true )) || (get_post_meta( $id, "vimeo_link", true ))){
		$output .= '<div class="embed-container">';
		$output .= '<iframe id="vimeoplayer" src="//player.vimeo.com/video/' . get_post_meta( $id, "vimeo_link", true ) . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
		$output .= '</div>';
	}
	return $output;
}

function vimpost_list_func ( $atts ){
	$video_list_atts = shortcode_atts( array(
		'group' => ''
	), $atts );
	$user_id = get_current_user_id();
	$output = '';
	$args = array( 'post_type' => 'video', 'posts_per_page' => 5000, 'order' => 'DESC' );
	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) : $loop->the_post();
		$id = get_the_ID();
		$custom_field = "video_watched_" . $id;
		$watched = get_user_meta( $user_id, $custom_field, true);
		/* $match = in_array($video_list_atts['group'], get_the_terms( $post, 'groups' ); */
		$terms = get_the_terms( $id , 'groups' );
		$match = false;
		foreach ( $terms as $term ) {
			if ($term->name == $video_list_atts['group']){
				$match = true;
			};
		};
		if ($match){
			$output .= '<div class="video-item"><div class="video-title inline-block"><h6 class="inline-block">';
			if ($watched == 'true'){
				$output.= '<span class="dashicons dashicons-yes"></span>';
			};
			$vimeo = get_post_meta( $id, "vimeo_link", true );
			if (pmpro_hasMembershipLevel() && $vimeo){
				$output .= '<a href="' . get_the_permalink() . '">' . get_the_title() . '</a>';
			}
			elseif (!pmpro_hasMembershipLevel() && pmpro_has_membership_access() && $vimeo) {
				$output .= '<a href="' . get_the_permalink() . '">' . get_the_title() . '</a>';
			}
			else {
				$output .= get_the_title();
			};
			$output .= '</h6>';
			if ($vimeo) {
				$duration = get_post_meta( $id, "video_duration", true );
				$duration = explode(":", $duration);
				$duration_h = $duration[0];
				$duration_m = $duration[1];
				$duration_s = $duration[2];
				$duration = '</div><div class="duration font-light font-08em inline-block pl5"> - ';
				if (!($duration_h == "00")) {
					$duration .= ltrim($duration_h, '0') . "h ";
				}
				if (!($duration_m == "00")) {
					$duration .= ltrim($duration_m, '0') . "m ";
				}
				if (!($duration_s == "00")) {
					$duration .= ltrim($duration_s, '0') . "s";
				}
				$output .= $duration . '</div>';
				$post = get_the_tags();
				if ($posttags) {
					$output .= '<div class="video-tags font-light font-08em inline-block pl15">';
					$count = 0;
					foreach($posttags as $tag) {
						$count++;
						if (!($count == 1)) {
							$output .= " • ";
						}
						$output .= '<a href="">' . $tag->name . '</a>';
					}
					$output .= '</div>';
				}
			}
			else {
				$output .= '</div><div class="coming-soon font-light font-08em inline-block pl15">(Coming Soon)</div>';
			}
			$output .= '</div>';
		}
	endwhile;

	return $output;
}


function vimposts_assets() {
    global $post;
	$watched = get_user_meta(get_current_user_id(), "video_watched_" . $post->ID, true);
	wp_enqueue_script( 'vimeo-player', plugins_url('/js/player.js', __FILE__), array( 'jquery' ) );
	wp_enqueue_script( 'ajax-user-field', plugins_url('/js/ajax-user-field.js', __FILE__), array( 'jquery' ) );

	wp_localize_script( 'ajax-user-field', 'ajaxuserfield', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'postID' => $post->ID,
		'watched' => $watched
	));

}

function update_user_field(){
	$custom_field = "video_watched_" . $_POST['postID'];
	$user_id = get_current_user_id();
	update_user_meta( $user_id, $custom_field , $_POST['watched'] );
	echo get_user_meta( $user_id, $custom_field, true);
	die();
}

function vimposts_custom_links($post_link, $post){
    if ( is_object( $post ) ){
        $terms = wp_get_object_terms( $post->ID, 'groups' );
        if( $terms ){
            return str_replace( '%groups%' , $terms[0]->slug , $post_link );
        }
        else{
        	return str_replace( '%groups%' , "videos" , $post_link );
        }
    }
    return $post_link;
}

if(get_option('vimposts_license_key') != ''){
    add_action( 'wp_enqueue_scripts', 'vimposts_assets' );

    add_filter( 'single_template', 'get_custom_post_type_template' );
    add_action( 'init', 'create_vimpost_posttype' );
    add_action( 'init', 'create_vimpost_taxonomies', 0 );
    add_action( 'load-post.php', 'vimposts_meta_boxes_setup' );
    add_action( 'load-post-new.php', 'vimposts_meta_boxes_setup' );
    add_action('save_post', 'vimposts_save_post_class_meta');

    add_action( 'wp_ajax_update_user_field', 'update_user_field' );
    add_filter( 'post_type_link', 'vimposts_custom_links', 1, 3 );
    add_shortcode( 'vimpost', 'vimpost_main_shortcode_func' );
    add_shortcode( 'vimpost_list', 'vimpost_list_func' );
}
?>
