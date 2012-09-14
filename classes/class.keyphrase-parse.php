<?php 
/*
 * Parsing the keyphrases with the associate link
 * */

class aLinks_keyphraseParser{
	
	const REGEXP_PARSE = '#(?!((<h.*?)(<.*?)|(<a.*?)))\b(%s)\b(?!([^>]*?</h[1-6])|(([^<>]*?)>)|([^>]*?</a>))#i';
	
	
	/*
	 * some static variables
	 * */
	static $keyPhrases = array();
	static $keyPhrase;
	
	static function init(){
		add_filter('the_content', array(get_class(), 'parse_keyPhrase'));
	}
	
	static function parse_keyPhrase($content){
		$keyPhrases = self::get_keyPhrases();
		if(!empty($keyPhrases)) :
			foreach($keyPhrases as $keyPhrase){
				self::$keyPhrase = $keyPhrase;
				$expression = self::get_regexExpression();
				
				//var_dump(self::$keyPhrase->post_title);
				//var_dump(self::$keyPhrase);
				//var_dump($expression);
				
				$phrase_found = preg_match($expression, $content, $matches);
				if($phrase_found){
					$link = self::get_associate_link();
					//var_dump($link);
					//var_dump($content);
					$content = preg_replace($expression, $link, $content);
				}
			}		
		endif;
		//var_dump($content);
		//die();
		return $content;
	}
	
	/*
	 * returnt eh keyphrases
	 * */
	static function get_keyPhrases(){
		if(empty(self::$keyPhrases)) :
			global $wpdb;
			$post_type = self::get_postType();
			$sql = "SELECT ID, post_title, post_content FROM $wpdb->posts WHERE post_type = '$post_type' AND post_status = 'publish'";
			self::$keyPhrases = $wpdb->get_results($sql);
		endif;
		return self::$keyPhrases;
	}
	
	
	/*
	 * return the posttype
	 * */
	static function get_postType(){
		return aLinks_CustomPostTypes::posttype;
	}
	
	
	/*
	 * return the regular expression
	 * */
	static function get_regexExpression(){
		return sprintf(self::REGEXP_PARSE, self::$keyPhrase->post_title);
	}
	
	
	/*
	 * ruturn the associate links using linksbuilder class
	 * */
	static function get_associate_link(){
		$link_builder = new aLinks_linksbuilder();
		$link_builder->set_ingredents(self::send_ingredents());
		$link = $link_builder->get_prepared_url();
		return $link;
	}
	
	
	/*
	 * send necessary parameters to the link builder
	 * */
	static function send_ingredents(){
		$ingredents = array(
			'keyphrase' => self::$keyPhrase->post_title,
			'href' => get_post_meta(self::$keyPhrase->ID, aLinks_CustomPostTypes::metakey_link, true),
			'title' => self::$keyPhrase->post_content			
		);
		
		return $ingredents;
	}
}