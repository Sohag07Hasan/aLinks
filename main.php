<?php 

/**
Plugin Name: aLinks
Plugin URI: http://alinks.headzoo.com
Description: A WordPress plugin that automatically links keywords in your blog post.
Author: Sohag
Version: 1.0.1
Author URI: http://www.headzoo.com
*/

define("aLinks_FILE", __FILE__);
define("aLinks_DIR", dirname(__FILE__));

include aLinks_DIR . '/classes/class.custom-post-type.php';
aLinks_CustomPostTypes::init();

include aLinks_DIR . '/classes/class.keyphrase-parse.php';
aLinks_keyphraseParser::init();

include aLinks_DIR . '/classes/class.link-builder.php';

