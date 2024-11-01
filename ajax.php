<?php
  
  
add_action('wp_ajax_send_specific_tweet', 'send_specific_tweet');

function send_specific_tweet(){

  	require_once(ABSPATH.'/wp-config.php');
	

       	require_once( dirname(__FILE__) . '/lib/twitter/tmhOAuth.php' );
       	require_once( dirname(__FILE__) . '/lib/twitter/tmhUtilities.php' );
                       
 		$twitter_app_consumer_key = get_option('twitter_app_consumer_key');	   
		$twitter_app_consumer_secret = get_option('twitter_app_consumer_secret');	   
			   
		$tmhOAuth = new tmhOAuth(array(
			'consumer_key'    => $twitter_app_consumer_key,
			'consumer_secret' => $twitter_app_consumer_secret
		));

   	global $current_user;
	get_currentuserinfo();
        $user_id = $current_user->ID;  
                                 
  	$twitter_post_id = $_REQUEST['send_specific_tweet'];
  	$mypost = get_post($twitter_post_id);
  
  	$thumbnail_id = get_post_meta($twitter_post_id, '_thumbnail_id', "single");
  
        if(isset($thumbnail_id) && $thumbnail_id !== ''){
              $upload_dir = wp_upload_dir();
              $upload_image_location = $upload_dir['basedir'];
              $upload_image_url = $upload_dir['baseurl'];
      
              $thumbnail_meta = get_post_meta($thumbnail_id);
              $upload_image_location .= "/" . $thumbnail_meta['_wp_attached_file'][0];
              $upload_image_url .= "/" . $thumbnail_meta['_wp_attached_file'][0];
          
        	$image = $upload_image_location;
      	}
        
  
  
   	$tweet_content = $mypost->post_content;
  
  	$pattern = '/\{(.*?)\}/';
  
  	preg_match_all($pattern, $tweet_content, $matches);

    
        foreach($matches[1] as $key => $value){
          
          $val_arr = explode(", ", $value);
          shuffle($val_arr);
          $replacements[$key] = $val_arr[1];
    
        }
        
        
        if($replacements){
			foreach($replacements as $replacement){
				 $tweet_content = preg_replace($pattern, $replacement, $tweet_content, 1);
			}
      	}
   	
	$twitter_token = json_decode(get_user_meta($user_id, 'mwa_twitter_token', true), true); 
  
  	$tmhOAuth->config['user_token']  = $twitter_token['access_token']['oauth_token'];
        $tmhOAuth->config['user_secret'] = $twitter_token['access_token']['oauth_token_secret'];
  
  	
          
          $code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update'), array(
                  'status' => $tweet_content
          ));
          
  	
  
        echo $tmhOAuth->response['response'];
		
		die();
	
	
}  
  
    
  
  
add_action('wp_ajax_send_tweet', 'send_tweet');

function send_tweet(){

  	require_once(ABSPATH.'/wp-config.php');

	require_once( dirname(__FILE__) . '/lib/twitter/tmhOAuth.php' );
	require_once( dirname(__FILE__) . '/lib/twitter/tmhUtilities.php' );
				   
	$twitter_app_consumer_key = get_option('twitter_app_consumer_key');	   
	$twitter_app_consumer_secret = get_option('twitter_app_consumer_secret');	   
		   
	$tmhOAuth = new tmhOAuth(array(
		'consumer_key'    => $twitter_app_consumer_key,
		'consumer_secret' => $twitter_app_consumer_secret
	));
	
	

	global $current_user;
	get_currentuserinfo();
	$user_id = $current_user->ID;


	$twitter_token = json_decode(get_user_meta($user_id, 'mwa_twitter_token', true), true); 

	$tmhOAuth->config['user_token']  = $twitter_token['access_token']['oauth_token'];
	$tmhOAuth->config['user_secret'] = $twitter_token['access_token']['oauth_token_secret'];

	$code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update'), array(
		'status' => $_POST['twitter_message']
	));
	
  	
	echo $tmhOAuth->response['response'];
	
	die();
}


?>