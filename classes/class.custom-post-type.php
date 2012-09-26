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
	const metakey_exchange = "aLink_keyPhrase_exchange";
	const metakey_option = "aLink_keyPhrase_option";
	const metakey_randomness = "aLink_keyPhrase_randomness";
	
	
	// xml parsing log;
	static $log = array();
	
	
	//globla option keys
	const global_options_key = "aLinks_global_options";

	

	static $counted = 0;

	//meta keys different groups


	static function init(){
		add_action('init', array(get_class(), 'register_new_posttype'));
		add_action('add_meta_boxes',array(get_class(), 'add_metaboxes'));
		add_action('save_post', array(get_class(), 'saveMetaBoxesData'), 100, 2);
		add_filter('wp_insert_post_data', array(get_class(), 'alter_postdata_with_keyphrase'), 10, 2);
		add_action('admin_menu', array(get_class(), 'submenupage'));
		//manage_posts_columns , manage_posts_custom_column

		add_filter('manage_' . self::posttype . '_posts_columns', array(get_class(), 'manage_posts_columns'));
		
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
		add_meta_box('aLinks_keyphrase_exchange', __('Replacing Keyphrase'), array(get_class(), 'metabox_keypPhrase_exchange'), self::posttype, 'normal', 'high');
		add_meta_box('aLinks_keyphrase_options', __('KeyPhrase Options'), array(get_class(), 'metabox_keypPhrase_options'), self::posttype, 'side', 'high');
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
	
	static function metabox_keypPhrase_exchange(){
		global $post;
		$exchange = self::get_exchange_word($post->ID);	
		include aLinks_DIR . '/metaboxes/metabox-keyphrase-exchange.php';
	}
	
	static function metabox_keypPhrase_options(){
		global $post;
		$options = self::get_keyPhrase_options($post->ID);
		//var_dump($options);
		include aLinks_DIR . '/metaboxes/metabox-keyphrase-options.php';
	}

	//save the metabox data
	static function saveMetaBoxesData($post_ID, $post){
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		if($post->post_type == self::posttype && isset($_POST[self::post_title])) :
			$similar_post_ids = self::get_similar_post_ids($post->post_title);			
			foreach($similar_post_ids as $pid){
				update_post_meta($pid, self::metakey_option, empty($_POST[self::metakey_option]) ? "1" : $_POST[self::metakey_option]);
				if($_POST[self::metakey_option] == 4){
					update_post_meta($pid, self::metakey_randomness, $_POST[self::metakey_randomness]);
				}
				if($pid == $post_ID){
					update_post_meta($post_ID, self::metakey_link, trim($_POST[self::metakey_link]));
					update_post_meta($post_ID, self::metakey_exchange, trim($_POST[self::metakey_exchange]));
				}			
			}					
						
		endif;
	}
	
	
	//get the exchange keywords if any
	static function get_exchange_word($post_id){
		return get_post_meta($post_id, self::metakey_exchange, true);
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
	
	
	/*
	 * submenupage
	 * */
	static function submenupage(){
		add_submenu_page( 'edit.php?post_type=' . self::posttype, __('aLinks Global Settings'), __('Options'), 'manage_options', 'aLinks_optionsPage', array(get_class(), 'submenupage_content'));
		add_submenu_page( 'edit.php?post_type=' . self::posttype, __('aLinks Import Export'), __('Import Export'), 'manage_options', 'aLinks_import_export', array(get_class(), 'submenupage_import_export'));
	}
	
	static function submenupage_content(){
		if($_POST['aLinks-options-save'] == "Y"):
			$new_options = array(
				'max_link_p_post' => empty($_POST['aLinks-maximumLinksperpost']) ? 1 : $_POST['aLinks-maximumLinksperpost'],
				'randomize' => $_POST['aLinks-radomizeLinks'],
				'raw_url_position' => $_POST['aLinks-rowurl-position'],
				'max_links' => trim($_POST['aLinks-maximumLinks'])
			);
			update_option(self::global_options_key, $new_options);
		endif;
		
		$options = self::get_global_options();
		//var_dump($options);
		include aLinks_DIR . '/includes/submenupage.php';
	}
	
	
	/*
	 * import export
	 * */
	static function submenupage_import_export(){
		
		if(!empty($_FILES['alinks-FileUpload']['tmp_name'])){
			self::parse_xml_file();
		}		
				
		include aLinks_DIR . '/includes/submenupage-import-export.php';
	}
	
	
	//return the global options
	static function get_global_options(){
		return get_option(self::global_options_key);		
	}
	
	
	/*
	 * returns the similar ids
	 * */
	static function get_similar_post_ids($title){		
		global $wpdb;		
		$sql = "SELECT ID from $wpdb->posts WHERE post_title = '$title'";
		return $wpdb->get_col($sql);		
	}
	
	
	/*
	 * returns the posts options
	 * */
	static function get_keyPhrase_options($post_ID){		
	   return array(
			self::metakey_option => get_post_meta($post_ID, self::metakey_option, true),
			self::metakey_randomness => get_post_meta($post_ID, self::metakey_randomness, true)
		);		
	}
	
	
	/*
	 * creates a column in the aLinks post table
	 * */
	static function manage_posts_columns($columns){
		$new_columns = $columns;
		unset($new_columns['date']);		
		$new_columns['title'] = "KeyPhrase";
		$new_columns['link'] = "Link";
				
		
		return $new_columns;
	}
	
	
	/*
	 * parse xml file for alinks 
	 * */
	static function parse_xml_file(){
		$file = $_FILES['alinks-FileUpload']['tmp_name'];
		
		$xml = @ simplexml_load_file($file);
				
		if(!$xml){
			self::$log['error'][] = __("XML format is not valid. Please see the sample in plugins directory");
			return;
		}
		
		$keyphrases = $xml->Keyphrases->Keyphrase;
		
		if(empty($keyphrases)){
			self::$log['error'][] = __("XML format is not valid. Please see the sample in plugins directory");
			return;
		}
		
		$parsed = 0;
		$skipped = 0;
		
		foreach($keyphrases as $key => $keyphrase){
			$single_keyphrase = array();
			
			$single_keyphrase['url'] = (string) $keyphrase->Option;				
			foreach($keyphrase->attributes() as $k => $v){
				$single_keyphrase[$k] = (string) $v;
			}

			if(empty($single_keyphrase['phrase']) || $single_keyphrase['url']){
				$skipped ++ ;
				continue;
			}
			
			$post_id = self::create_post();
			if($post_id){
				$parsed ++ ;
			}
		}
		
		self::$log['updated'][] = __("total number of pharsed : $parsed and <br/> total number of skipped : $skipped");
		
		
	}
	
	//creates post
	static function create_post($data){
		$post_data = array(
			'post_title' => $data['phrase'],
			'post_content' => $data['description'],
			'post_type' => slef::posttype,
		);
		
		var_dump($data);
		
		$pid = wp_insert_post($post_data);
		
		var_dump($pid);
		die();
		
		update_post_meta($pid, self::metakey_link, $data['url']);
		update_post_meta($pid, self::metakey_option, "1");
				
		return $pid;
	}
	
	//print log message
	static function print_log(){
		if(!empty(self::$log['updated'])){
			echo "<div class='updated'>";
			foreach(self::$log['updated'] as $msg){
				echo "<p>" . $msg . "</p>";
			}
			echo "</div>";
		}
		
	if(!empty(self::$log['error'])){
			echo "<div class='error'>";
			foreach(self::$log['error'] as $msg){
				echo "<p>" . $msg . "</p>";
			}
			echo "</div>";
		}
	}
}