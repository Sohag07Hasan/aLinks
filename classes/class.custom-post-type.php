<?php
/*
 * creates a custom post type service
 */

class aLinks_CustomPostTypes{
	
	//custom post type constant
	const posttype = 'alink';
	const menu = 'aLinks';
	const name = 'aLinks';
	const singular = 'aLink';
	
	
	//metabox constant
	const post_title = "aLink_key_phrase_name";
	const post_content = "aLink_key_phrase_description";
	const metakey_link = "aLink_link";
	const metakey_factory = "aLink_factory";

	

	static $counted = 0;

	//meta keys different groups


	static function init(){
		add_action('init', array(get_class(), 'register_new_posttype'));
		add_action('add_meta_boxes',array(get_class(), 'add_metaboxes'));
		add_action('save_post', array(get_class(), 'saveMetaBoxesData'), 100, 2);
		add_filter('wp_insert_post_data', array(get_class(), 'alter_postdata_with_keyphrase'), 10, 2);
		//manage_posts_columns , manage_posts_custom_column

	//	add_action('admin_enqueue_scripts', array(get_class(), 'js_add'));

		//post visit counts
	//	add_action('post_view_count', array(get_class(), 'count_update'));
	}

	/*
	 * css and js add for default media uploader
	 * */
	static function js_add(){
		wp_enqueue_style('thickbox');
		wp_enqueue_script('jquery');
		wp_enqueue_script('media-uploader-services', SERVICE_POST_TYPE_url . '/js/media-uploader.js', array('jquery', 'media-upload', 'thickbox'));
	}



	/*
	 * creates a new post type
	 */
	static function register_new_posttype(){
		$labels = array(
			'name' => _x(self::name, 'post type general name'),
			'singular_name' => _x(self::singular, 'post type singular name'),
			'add_new' => _x('Add New', 'book'),
			'add_new_item' => __('Add New ' . self::singular),
			'edit_item' => __('Edit ' . self::singular),
			'new_item' => __('New ' . self::singular),
			'all_items' => __('All ' . self::name),
			'view_item' => __('View ' . self::singular),
			'search_items' => __('Search ' . self::singular),
			'not_found' =>  __('No ' . self::name .' found'),
			'not_found_in_trash' => __('No ' . self::name . ' found in Trash'), 
			'parent_item_colon' => '',
			'menu_name' => self::menu

		);
		$args = array(
			'labels' => $labels,
			'exclude_from_search' => true,
			'public' => true,
			'publicly_queryable' => false,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => false,
			'rewrite' => false,
			'capability_type' => 'post',
			'has_archive' => false, 
			'hierarchical' => false,
			'menu_position' => 5,
			'supports' => array('custom-fields')			
		); 
		register_post_type(self::posttype, $args);	
	}

	/*
	 * add metaboxes
	 */
	static function add_metaboxes(){		

		add_meta_box('aLinks_key_phrase', __('Key Phrase'), array(get_class(), 'metabox_keyPhrase'), self::posttype, 'normal', 'high');
		add_meta_box('aLinks_link', __('Assocate Link'), array(get_class(), 'metabox_link'), self::posttype, 'normal', 'high');
		add_meta_box('aLinks_key_phrase_description', __('Description'), array(get_class(), 'metabox_keyPhrase_description'), self::posttype, 'normal', 'high');
		add_meta_box('aLinks_factory', __('Factory'), array(get_class(), 'metabox_factory'), self::posttype, 'side', 'high');
	}

	//metabox content
	static function metabox_keyPhrase(){
		global $post;		
		include aLinks_DIR . '/metaboxes/metabox-keyphrase.php';
	}
	
	static function metabox_keyPhrase_description(){
		global $post;		
		include aLinks_DIR . '/metaboxes/metabox-keyphrase-description.php';
	}
	
	static function metabox_link(){
		global $post;
		$link = self::get_associate_link($post->ID);
		include aLinks_DIR . '/metaboxes/metabox-link.php';
	}
	
	static function metabox_factory(){
		global $post;
		$factory = self::get_factory($post->ID);
		include aLinks_DIR . '/metaboxes/metabox-factory.php';
	}

	//save the metabox data
	static function saveMetaBoxesData($post_ID, $post){
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		if($post->post_type == self::posttype) :
			update_post_meta($post_ID, self::metakey_link, trim($_POST[self::metakey_link]));
			update_post_meta($post_ID, self::metakey_factory, trim($_POST[self::metakey_factory]));
		endif;
	}

	
	//update the count
	static function count_update($post_id){
		//global $post;
		//var_dump($post);
		if(self::$counted > 0 ) return;		
		$count = get_post_meta($post_id, 'total_viewed', true);
		if($count){
			$count ++;
		}
		else{
			$count = 1;
		}

		update_post_meta($post_id, 'total_viewed', $count);

		self::$counted ++ ;
	}
	
	
	/*
	 * alerts post data with keyphrase data
	 * */
	static function alter_postdata_with_keyphrase($data, $tags){
		if(isset($_POST[self::post_title])){
			$data['post_title'] = trim($_POST[self::post_title]);
		}
		if(isset($_POST[self::post_content])){
			$data['post_content'] = trim($_POST[self::post_content]);
		}
		return $data;
	}
	
	
	/*
	 * return the associatelink
	 * */
	static function get_associate_link($post_id){
		return get_post_meta($post_id, self::metakey_link, true);
	}
	
	/*
	 * return the factory
	 * */
	static function get_factory($post_id){
		return get_post_meta($post_id, self::metakey_factory, true);
	}
	
}