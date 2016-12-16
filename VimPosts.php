<?php
/**
Plugin Name: VimPosts
Plugin URI: https://github.com/joebuhlig/vimposts
Description: This plugin adds some custom abilities for posting Vimeo videos.
Version: 1.0.0
Author: Joe Buhlig
Author URI: http://joebuhlig.com
GitHub Plugin URI: https://github.com/joebuhlig/vimposts
License: GPL2
 */
 
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

	register_taxonomy( 'groups', array( 'video' ), $args );
}
 
 function create_vimpost_posttype() {
// set up labels
	$labels = array(
 		'name' => 'Videos',
    	'singular_name' => 'Video',
    	'add_new' => 'Add New Video',
    	'add_new_item' => 'Add New Video',
    	'edit_item' => 'Edit Video',
    	'new_item' => 'New Video',
    	'all_items' => 'All Videos',
    	'view_item' => 'View Video',
    	'search_items' => 'Search Videos',
    	'not_found' =>  'No Videos Found',
    	'not_found_in_trash' => 'No Videos found in Trash', 
    	'parent_item_colon' => '',
    	'menu_name' => 'Videos',
    	);
  register_post_type( 'video',
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
	'rewrite' => array( 'slug' => '%group%' ),
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
    'video',         // Admin page (or post type)
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


function vimposts_content_filter( $content ) {
    $custom_content = 'YOUR CONTENT GOES HERE';
    $custom_content .= $content;
    return $custom_content;
}
add_filter( 'the_content', 'vimposts_content_filter' );

function get_custom_post_type_template($single_template) {
     global $post;

     if ($post->post_type == 'video') {
          $single_template = dirname( __FILE__ ) . '/single-video.php';
     }
     return $single_template;
}

function video_list_func ( $atts ){
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
				$posttags = get_the_tags();
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

add_action( 'wp_enqueue_scripts', 'vimposts_assets' );

add_filter( 'single_template', 'get_custom_post_type_template' );
add_action( 'init', 'create_vimpost_posttype' );
add_action( 'init', 'create_vimpost_taxonomies', 0 );
add_action( 'load-post.php', 'vimposts_meta_boxes_setup' );
add_action( 'load-post-new.php', 'vimposts_meta_boxes_setup' );
add_action('save_post', 'vimposts_save_post_class_meta');

add_action( 'wp_ajax_update_user_field', 'update_user_field' );

add_shortcode( 'video_list', 'video_list_func' );

?>