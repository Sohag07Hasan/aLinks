<?php
/*
 * creates a custom post type service
 */

class aLinks_CustomPostTypes{
	
	//custom post type constant
	const posttype = 'alink';
	const menu = 'nLinks';
	const name = 'nLinks';
	const singular = 'nLink';
	
	
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
		
		add_action('manage_' . self::posttype . '_posts_custom_column', array(get_class(), 'manage_custom_post_rows'), 10, 2);
		
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
		add_submenu_page( 'edit.php?post_type=' . self::posttype, __('aLinks Bulk Operation'), __('Bulk Operation'), 'manage_options', 'aLinks_bulk_delete', array(get_class(), 'submenupage_bulk_delete'));
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

		$xml_type_1 = aLinks_URL . 'sample_xml/type_1.xml';
		$xml_type_2 = aLinks_URL . 'sample_xml/type_2.xml';
		
		include aLinks_DIR . '/includes/submenupage-import-export.php';
	}
	
	//bulk delete option
	static function submenupage_bulk_delete(){
		if($_POST['alinks-bulk-operation-submitted'] == "Y"){
			switch($_POST['alinks-bulk-operation']){
				case 1 :
					self::make_posts_drafts();
					$msg = "All the keyphrases are drafts";
					break;
				case 2 :
					self::make_posts_trash();
					$msg = "All the keyphrases are trash";
					break;
				case 3 :
					self::make_posts_publish();
					$msg = "All the keyphrases are made publish";
					break;
				case 4 :
					self::make_posts_delete();
					$msg = "All the keyphrase are deleted";
					break;
				default:
					$msg = "No operation is selected";
					break;
					
			}
		}
		include aLinks_DIR . '/includes/submenupage-bulk-delete.php';
	}
	
	//bulk operation to make posts drafts
	static function make_posts_drafts(){
		global $wpdb;
		$posttype = self::posttype;
		$sql = "UPDATE $wpdb->posts SET post_status = 'draft' WHERE post_type = '$posttype'";
		return $wpdb->query($sql);
	}
	
	//bulk operation to make posts trash
	static function make_posts_trash(){
		global $wpdb;
		$posttype = self::posttype;
		$sql = "UPDATE $wpdb->posts SET post_status = 'trash' WHERE post_type = '$posttype'";
		return $wpdb->query($sql);
	}
	
	//bulk operation to make posts publish
	static function make_posts_publish(){
		global $wpdb;
		$posttype = self::posttype;
		$sql = "UPDATE $wpdb->posts SET post_status = 'publish' WHERE post_type = '$posttype'";
		return $wpdb->query($sql);
	}
	
	//bulk operation to make posts delete
	static function make_posts_delete(){
		global $wpdb;
		$posttype = self::posttype;
		$meta_key_1 = self::metakey_exchange;
		$meta_key_2 = self::metakey_link;
		$meta_key_3 = self::metakey_option;
		$meta_key_4 = self::metakey_randomness;
		
		$sql_1 = "DELETE FROM $wpdb->posts WHERE post_type = '$posttype'";
		$sql_2 = "DELETE FROM $wpdb->postmeta WHERE meta_key = '$meta_key_1' OR meta_key = '$meta_key_2' OR meta_key = '$meta_key_3' OR meta_key = '$meta_key_4'";
		
		$wpdb->query($sql_1);
		$wpdb->query($sql_2);
		return;
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
		$new_columns['des'] = "Description";
				
		
		return $new_columns;
	}
	
	static function manage_custom_post_rows($column_name, $post_ID){
		
		switch($column_name){
			case "link" :
				$link = get_post_meta($post_ID, self::metakey_link, true);
				echo "<a href='$link' target='_blank'>$link</a>";
				break;
			case "des" :
				echo self::get_keyphrase_description($post_ID);
				break;
			
		}
	}
	
	
	/*
	 * parse xml file for alinks 
	 * */
	static function parse_xml_file(){
		
		if($_POST['alinks-xml-type'] == 1){
			return self::parse_xml_type_1();
		}

		if($_POST['alinks-xml-type'] == 2){
			return self::parse_xml_type_2();
		}
		
	}
	
	
	static function parse_xml_type_1(){
		$file = $_FILES['alinks-FileUpload']['tmp_name'];
		
		$xml = @ simplexml_load_file($file);
				
		if(!$xml){
			self::$log['error'][] = __("XML Type 1 format is not valid. Please see the sample in plugins directory");
			return;
		}
		
		$keyphrases = $xml->Keyphrases->Keyphrase;
		
		if(empty($keyphrases)){
			self::$log['error'][] = __("KeyPhrases are empty");
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
			
			
			if(empty($single_keyphrase['phrase']) || empty($single_keyphrase['url'])){
				$skipped ++ ;
				continue;
			}
			
			$post_id = self::create_post($single_keyphrase);
			if($post_id){
				$parsed ++ ;
			}
		}
		
		self::$log['updated'][] = __("total number of pharsed : $parsed and <br/> total number of skipped : $skipped");
		
	}
	
	
	//parsing xml type 2
	static function parse_xml_type_2(){
		$file = $_FILES['alinks-FileUpload']['tmp_name'];
		
		$xml = @ simplexml_load_file($file);
					
		if(!$xml){
			self::$log['error'][] = __("XML Type 2 format is not valid. Please see the sample in plugins directory");
			return;
		}
		
		$keyphrases = $xml->Keyphrase;		
		
		if(empty($keyphrases)){
			self::$log['error'][] = __("Key Phrases are empty");
			return;
		}
		
		$parsed = 0;
		$skipped = 0;
		
		foreach($keyphrases as $keyphrase){
			$single_keyphrase = array();			
						
			foreach($keyphrase->attributes() as $k => $v){
				$key = ($k == 'kw') ? 'phrase' : "url";
				$single_keyphrase[$key] = (string) $v;
			}
			
			$single_keyphrase['description'] = $single_keyphrase['phrase'];						
			if(empty($single_keyphrase['phrase']) || empty($single_keyphrase['url'])){
				$skipped ++ ;
				continue;
			}
			
			$post_id = self::create_post($single_keyphrase);
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
			'post_type' => self::posttype,
			'post_status' => 'publish'
		);
			
		
		$pid = wp_insert_post($post_data);
		
		
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
	
	
	/*
	 * return the keyphrase descripion
	 * */
	static function get_keyphrase_description($post_ID){
		global $wpdb;
		return $wpdb->get_var("SELECT post_content FROM $wpdb->posts WHERE ID = '$post_ID'");
	}
}