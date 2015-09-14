<?php
/*
 * Plugin Name: JAV post types
 * Description: Adds custom JAV post type
 *
 */

/*--------------------------------------------------------------------------* 
 *                                                                          *
 *                                                                          *
 *                      Creating post type JAV movies                       * 
 *                                                                          * 
 *                                                                          *
 *--------------------------------------------------------------------------*/
function register_jav_movies_post_type(){

	$labels = array(
				'name'               =>  'JAV Movies',
				'singular_name'      =>  'JAV Movie',
				'menu_name'          =>  'JAV Movies',
				'name_admin_bar'     =>  'JAV Movies',
				'add_new'            =>  'Add New JAV movie',
				'add_new_item'       =>  'Add New JAV Movie',
				'new_item'           =>  'New JAV Movie',
				'edit_item'          =>  'Edit JAV Movie',
				'view_item'          =>  'View JAV Movie',
				'all_items'          =>  'All JAV Movies',
				'search_items'       =>  'Search JAV Movies',
				'parent_item_colon'  =>  'Parent JAV Movies:',
				'not_found'          =>  'No JAV movies found.',
				'not_found_in_trash' =>  'No JAV movies found in Trash.'
		);

	$args = array(
				'labels'             => $labels,
        'description'        => 'JAV Movies',
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'download-jav-movies' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'thumbnail' ),
				'register_meta_box_cb' => 'cd_meta_box_add',
				'taxonomies' => array( 'jav_director' )
			);

	register_post_type( 'jav_movies', $args );

}

add_action( 'init', 'register_jav_movies_post_type' );




//Rewriting permalinks
function my_rewrite_flush(){

	register_jav_movies_post_type();
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'my_rewrite_flush');




/*--------------------------------------------------------------------------* 
 *                                                                          *
 *                                                                          *
 *                      Creating taxonomy JAV Director                      * 
 *                                                                          * 
 *                                                                          *
 *--------------------------------------------------------------------------*/
function create_taxonomies(){
		$labels = array(
				'name'              => 'JAV Director',
				'singular_name'     => 'JAV Director',
				'search_items'      => 'Search JAV Director',
				'all_items'         => 'All JAV Directors',
				'edit_item'         => 'Edit JAV Director',
				'update_item'       => 'Update JAV Director',
				'add_new_item'      => 'Add New JAV Director',
				'new_item_name'     => 'New JAV Director Name',
				'menu_name'         => 'JAV Director'
			);

	$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'jav-director' ),
	);	
	
	register_taxonomy( 'jav_director', 'jav_movies', $args );	
}

add_action('init', 'create_taxonomies');




/*--------------------------------------------------------------------------* 
 *                                                                          *
 *                                                                          *
 *                     Enque plugin scriptis and styles                     * 
 *                                                                          * 
 *                                                                          *
 *--------------------------------------------------------------------------*/
function enqueue_plugin_scripts() {

 global $typenow;

 if( $typenow == 'jav_movies' ) {
     wp_enqueue_media();

     // Registers and enqueues the required javascript.
     wp_register_script( 'meta-box-image', plugin_dir_url( __FILE__ ) . 'meta-box-image.js', array( 'jquery' ) );
     wp_localize_script( 'meta-box-image', 'meta_image',
         array(
             'title' => __( 'Choose or Upload an Image', 'prfx-textdomain' ),
             'button' => __( 'Use this image', 'prfx-textdomain' ),
         )
     );

     wp_enqueue_script( 'meta-box-image' );
 }
}

add_action( 'admin_enqueue_scripts', 'enqueue_plugin_scripts' );



function enqueue_plugin_styles(){
	global $typenow;

	if( $typenow == 'jav_movies' ){
		wp_enqueue_style( 'jav_style', plugin_dir_url( __FILE__ ) . 'jav-style.css' );	
	}
}

add_action( 'admin_print_styles', 'enqueue_plugin_styles' );



/*--------------------------------------------------------------------------* 
 *                                                                          *
 *                                                                          *
 *                           JAV Movies metaboxes                           * 
 *                                                                          * 
 *                                                                          *
 *--------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------*
 *                           Register Metabox                               *
 *--------------------------------------------------------------------------*/
function register_jav_movies_metaboxes(){

	//Registering metabox
	add_meta_box( 'upload_images', 'Upload title and screenshot', 'draw_metabox_html', 'jav_movies', 'normal', 'high' );

	
function draw_metabox_html( $post ){

	$cover_image = get_post_meta( $post->ID, 'cover_image', true ); 
	$screenshot_image = get_post_meta( $post->ID, 'screenshot_image', true ); 
	$techinfo = get_post_meta( $post->ID, 'techinfo', true ); 
	$movie_urls = get_post_meta( $post->ID, 'movie_urls', true ); 

	wp_nonce_field( 'jav_movie_nonce_context', 'jav_movie_nonce' );		
	
	
	//Drawing HTML form elements
?>

<!-- Tech Info input field -->
<div>
	<label for="techinfo">Movie Tech. Info</label>
	<input id="techinfo" type="text" name="techinfo" value="<?php echo $techinfo; ?>" >
</div>

<!-- Movie URLs input field -->
<div>
	<label for="techinfo">Movie URLs</label><br>
	<textarea id="movie_urls" name="movie_urls" cols="30" rows="10"><?php echo $movie_urls; ?></textarea>
</div>

<!-- Cover image -->
<div>
	<img src="<?php echo $cover_image; ?>" id="cover_image" <?php echo '' === $cover_image ? 'class="hide_when_no_image"' : ''; ?> />
	<input id="cover_image_hidden" type="hidden" name="cover_image" value="<?php echo $cover_image; ?>" />
	<input id="cover_image_button" type="button" name="submit" value="select cover image" />
</div>

<!-- Screenshot image -->
<div>
	<img src="<?php echo $screenshot_image; ?>" id="screenshot_image" <?php echo '' === $screenshot_image ? 'class="hide_when_no_image"' : ''; ?> />
	<input id="screenshot_image_hidden" type="hidden" name="screenshot_image" value="<?php echo $screenshot_image; ?>" />
	<input id="screenshot_image_button" type="button" name="submit" value="select screenshot image" />
</div>


<?php	
	}

}

add_action( 'add_meta_boxes', 'register_jav_movies_metaboxes' );


/*--------------------------------------------------------------------------*
 *                           Save Metabox Data                              *
 *--------------------------------------------------------------------------*/
function save_metabox_data( $postID ){
	
	//Checking if we are allowed to save data
	if ( defined( DOING_AUTOSAVE ) && DOING_AUTOSAVE ) return;
	if ( !current_user_can( 'edit_post' ) ) return;
	if ( !isset($_POST['jav_movie_nonce']) ) return;
	if (!wp_verify_nonce($_POST['jav_movie_nonce'], 'jav_movie_nonce_context') ) return;

	
	//Saving custom fields to the database
	if ( isset( $_POST['cover_image'] ) )
		update_post_meta( $postID, 'cover_image', $_POST['cover_image'] );	

	if ( isset( $_POST['screenshot_image'] ) )
		update_post_meta( $postID, 'screenshot_image', $_POST['screenshot_image'] );	

	if ( isset( $_POST['techinfo'] ) )
		update_post_meta( $postID, 'techinfo', $_POST['techinfo'] );	

	if ( isset( $_POST['movie_urls'] ) )
		update_post_meta( $postID, 'movie_urls', $_POST['movie_urls'] );	
}

add_action( 'save_post', 'save_metabox_data' );








