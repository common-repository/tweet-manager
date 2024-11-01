<?php

class Tweeter extends Plugin_Template {
	/**
	 * Transparent Watermark version
	 *
	 * @var string
	 */
	public $version                 = '1.1.5';
	
  
  
	/**
	 * Array with default options
	 *
	 * @var array
	 */
	protected $_options             = array(
		
	);
  
  
	protected $_twitter_options             = array(
		'twitter_app_consumer_key' => '',
          	'twitter_app_consumer_secret' => ''
          	
	);

  
  	protected $_feed_options             = array(
		
          'twitter_app_feeds_list' => 'http://MyWebsiteAdvisor.com/feed',
          	'twitter_app_google_news_topics_list' => 'WordPress'
	);

	
  
  
	/**
	 * Plugin work path
	 *
	 * @var string
	 */
	protected $_plugin_dir          = null;
	
	/**
	 * Settings url
	 *
	 * @var string
	 */
	protected $_settings_url        = null;
	

	function __construct(){
		parent::__construct();
	
	}


}

?>