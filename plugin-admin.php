<?php

class Tweeter_Admin extends Tweeter {
	/**
	 * Error messages to diplay
	 *
	 * @var array
	 */
	private $_messages = array();
	
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct() {
		parent::__construct();
         
		
		$this->_plugin_dir   = DIRECTORY_SEPARATOR . str_replace(basename(__FILE__), null, plugin_basename(__FILE__));
		$this->_settings_url = 'options-general.php?page=' . plugin_basename(__FILE__);;
		
		$allowed_options = array();
          
		
		// set watermark options
          	if(array_key_exists('option_name', $_GET) && array_key_exists('option_value', $_GET) && in_array($_GET['option_name'], $allowed_options)){
			update_option($_GET['option_name'], $_GET['option_value']);
			
			header("Location: " . $this->_settings_url);
			die();	
		} else {
			// register installer function
			register_activation_hook(TWEETER_LOADER, array(&$this, 'activateCustomPlugin'));
			
			// add plugin "Settings" action on plugin list
			add_action('plugin_action_links_' . plugin_basename(TWEETER_LOADER), array(&$this, 'add_plugin_actions'));
			
			// add links for plugin help, donations,...
			add_filter('plugin_row_meta', array(&$this, 'add_plugin_links'), 10, 2);
			
			// push options page link, when generating admin menu
			add_action('admin_menu', array(&$this, 'adminMenu'));

		}
	}
  
	
	/**
	 * Add "Settings" action on installed plugin list
	 */
	public function add_plugin_actions($links) {
		array_unshift($links, '<a href="options-general.php?page=' . plugin_basename(__FILE__) . '">' . __('Settings') . '</a>');
		
		return $links;
	}
	
  
	/**
	 * Add links on installed plugin list
	 */
	public function add_plugin_links($links, $file) {
		if($file == plugin_basename(TWEETER_LOADER)) {
			$links[] = '<a href="http://MyWebsiteAdvisor.com">Visit Us Online</a>';
		}
		
		return $links;
	}
	
                               
	/**
	 * Add menu entry for Transparent Watermark settings and attach style and script include methods
	 */
	public function adminMenu() {		
		// add option in admin menu, for setting details on watermarking
		$plugin_page = add_menu_page('Tweet Manager', 'Tweet Manager', 8, __FILE__, array(&$this, 'optionsPage'));
                 add_submenu_page( __FILE__, 'Twitter Settings', 'Twitter Settings', 8, 'twitter-settings', array(&$this, 'optionsPageTwitter'));              
		add_submenu_page( __FILE__, 'Feed Settings', 'Feed Settings', 8, 'feed-settings', array(&$this, 'optionsPageFeed')); 
                               
                               
		add_action('admin_print_styles-' . $plugin_page,     array(&$this, 'installStyles'));
	
		// also add JS to media upload popup
		//add_action('admin_print_scripts-media-upload-popup', array(&$this, 'installScripts'));
	}
	
                               
	/**
	 * Include styles used by Plugin
	 */
	public function installStyles() {
		$site_base = get_bloginfo('url'); 
		//wp_enqueue_style('tweet-manager', WP_PLUGIN_URL . $this->_plugin_dir . 'tweeter.css');
		wp_enqueue_style('tweet-manager-css', "$site_base/wp-content/plugins/tweeter/tweeter.css");
	}
	
                              
                               
                               
	public function optionsPageFeed(){


		if(!function_exists('json_decode')){
			echo "<div class='error'><p>WARNING: PHP JSON is not available. Please install PHP JSON to continue.</p></div>";	
		}
		
		
		if(!function_exists('curl_init')){
			echo "<div class='error'><p>WARNING: PHP cURL is not available. Please install PHP cURL to continue.</p></div>";	
		}




		$site_base = get_option('siteurl'); 
                               
		if(isset($_POST['Submit_Feed_Settings'])) {
			foreach($this->_feed_options as $option => $value) {
				if(array_key_exists($option, $_POST)) {
					update_option($option, $_POST[$option]);
				} else {
					update_option($option, $value);
				}
			}

			//$this->_messages['updated'][] = 'Options updated!';
		}
                 
                  
                               
           ?>  
          <div class="wrap">   
       	<div id="icon-options-general" class="icon32"><br /></div>
		<h2>Feed Settings</h2>
       	<?php
               
  		$twitter_app_consumer_key = get_option('twitter_app_consumer_key');	   
		$twitter_app_consumer_secret = get_option('twitter_app_consumer_secret');	

		if(!isset($twitter_app_consumer_key) || !isset($twitter_app_consumer_secret) || $twitter_app_consumer_key == '' || $twitter_app_consumer_secret == ''){
     
			echo "Twitter Application is not setup yet!<br>";
			echo "<a href='$site_base/wp-admin/admin.php?page=twitter-settings'>Click Here to Setup Custom Twitter Application!</a>";
                                                  
		}else{                                             
                             
                   echo "<form method='post'>";  
                  
                   	$twitter_app_feeds_list = get_option('twitter_app_feeds_list');
               		echo "<h3>RSS News Feed</h3>";
                  	echo "<input type='text' name='twitter_app_feeds_list' value='$twitter_app_feeds_list' size='100'><br>";

                  	$twitter_app_google_news_topics_list = get_option('twitter_app_google_news_topics_list');
               		echo "<h3>Keyword</h3>";
                        echo "<input type='text' name='twitter_app_google_news_topics_list' value='$twitter_app_google_news_topics_list' size='30'>";
                  
                    	echo '<p class="submit"><input type="submit" name="Submit_Feed_Settings" class="button-primary" value="Save Feed Settings" /></p>';    
                               
                    echo "</form>";          
                        
                  
				  if($twitter_app_feeds_list != "" && $twitter_app_google_news_topics_list != ''){
				  
					  require_once(dirname(__FILE__) .'/rssAggregator.class.php');
		
						$rss = new rssAggregator;
					  $rss = $rss->get_aggregation();
					  
					  $i=0;
					  
					  foreach($rss as $rss_item){
						
						echo "<div class='rss_item' id='rss_tweet_$i'>";
						echo "<b>";
						echo $rss_item['pubDate'];
						echo "</b><br>";
						echo "<a href='".$rss_item['link']."' target='_blank'>";
						echo $rss_item['title'];
						echo "</a>";
						 echo "<div class='send_tweet'><img id='$i' class='send_tweet' src='$site_base/wp-content/plugins/tweet-manager/images/twitter_tweet_this.png' ></div>";
						echo "</div>";
						
					
						$i++;
					  
						
					  }
                  }
				  
                  
                  
                  echo "<script>
                  jQuery(document).ready(function() {
                    jQuery('img.send_tweet').click(function(event) {
                      
                      	//alert(event.target.id);
                      
                      var rss_tweet_id = event.target.id;
                      
                      var twitter_message = jQuery('div#rss_tweet_'+rss_tweet_id+' a').text();
                      var twitter_message_url = jQuery('div#rss_tweet_'+rss_tweet_id+' a').attr('href');
                      
                     jQuery('img#'+rss_tweet_id).attr('src', '$site_base/wp-content/plugins/tweet-manager/images/ajax-loader.gif');
                      
                      
                      
                      var ajax_data = {
                          twitter_message: twitter_message+' '+twitter_message_url,
                          send_tweet: 'send_tweet',
						  action: 'send_tweet'
                      };
                                  
                      //alert(cb_time);
                      
                    jQuery.ajax({
                      type: 'POST',
                      url: ajaxurl,
                      data: ajax_data,
                      dataType: 'json',
                      success:function(response){
                        jQuery('img#'+rss_tweet_id).attr('src', '$site_base/wp-content/plugins/tweet-manager/images/success.png');
						
                         if(response.text){
							jQuery('img#'+rss_tweet_id).after('Success: ' + response.text );  
	             			//alert(response.text); 
					
					
		}else{
			alert(response.error);
		}	
                      }
                    });
          
                   });        
                  });   
                    
                    </script>";
       
                  
                  
                  
                	  //echo "<hr>";
                  
                               
               		 echo "<pre>";                       
                	//var_dump($rss);
                	echo "</pre>";                       
                    
		}
                        
           echo "</div>";                    
                               
       
       }
               
                                             
               
                               

                               
	public function optionsPageTwitter(){
           
   
		if(!function_exists('json_decode')){
			echo "<div class='error'><p>WARNING: PHP JSON is not available. Please install PHP JSON to continue.</p></div>";	
		}
		
		
		if(!function_exists('curl_init')){
			echo "<div class='error'><p>WARNING: PHP cURL is not available. Please install PHP cURL to continue.</p></div>";	
		}	
				
                               
       if(isset($_POST['Submit_Twitter_Options'])) {
			foreach($this->_twitter_options as $option => $value) {
				if(array_key_exists($option, $_POST)) {
					update_option($option, $_POST[$option]);
				} else {
					update_option($option, $value);
				}
			}

			//$this->_messages['updated'][] = 'Options updated!';
		}
                  
                               
                 $site_base = get_option('siteurl');              
                               
                               
            global $current_user;
              	//get_currentuserinfo();
               	$user_id = $current_user->ID;                                
          
          //parse_str($_SERVER["REQUEST_URI"], $_GET); 
          if(isset($_GET['tweeter_logout'])){
			//echo $user_id;
                  	update_user_meta($user_id, 'mwa_twitter_token', '');	
                  	//wp_redirect('/wp-admin/admin.php?page=twitter-settings');
            		//die();
                    	echo "<a href='$site_base/wp-admin/admin.php?page=twitter-settings'>Logged Out, Click Here to Continue!</a>";

		} 
       ?>
        <div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2>Twitter Application Settings</h2>
                  
                  <form method="post" action="">
                  
                  You need to go to the <a href='https://dev.twitter.com/apps' target='_blank'>Twitter Application Developer Page</a> and create a Twitter Application.  <a href='https://dev.twitter.com/apps' target='_blank'>Click Here!</a><br>
                  
                    You will want to give the Twitter Application Read and Write access.<br>
                      
                    You will need to use the 'Callback URL' located below when you setup your Twitter Application.<br>
                      
                      
                      
                  <br>
                      
                   <?php 
                      	$callback_url = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]; 
                  	$callback_url_fixed = strtok($callback_url, "&");
                  	$callback_url = $callback_url_fixed;
                  
                  
                  ?> 
                     
                  
                  <h3>Use this information when you create your Twitter Application</h3>
                  <p><b>Callback URL:</b> (Should be something like 'http://domain.com/wp-admin/admin.php?page=twitter-settings') <br>
                   <input type='text' value='<?php echo $callback_url; ?>' size=100 >
                    </p>
                  
                         
                         
                  <br>
                         
                  <h3>Fill in the following after you have created your Twitter Application</h3>       
                  <?php $twitter_app_consumer_key = get_option('twitter_app_consumer_key'); ?>
                  
                    
                  <p>
                  <b>Consumer Key:</b><br>
                  <input type='text' id='twitter_app_consumer_key' name='twitter_app_consumer_key' value='<?php echo $twitter_app_consumer_key; ?>' size=40>
                  </p>
                  
                    
                   <?php $twitter_app_consumer_secret = get_option('twitter_app_consumer_secret'); ?>
                  
                  <p>
                  <b>Consumer Secret:</b><br>
                  <input type='text' id='twitter_app_consumer_secret' name='twitter_app_consumer_secret' value='<?php echo $twitter_app_consumer_secret; ?>' size=70>
                  </p>
                    
                    
                    <p class="submit">
				<input type="submit" name="Submit_Twitter_Options" class="button-primary" value="Save Twitter Application Settings" />
			</p>
                                  
                                  </form>
                  
         <?php                      
                               
                               
            require_once( dirname(__FILE__) . '/lib/twitter/tmhOAuth.php' );
            require_once( dirname(__FILE__) . '/lib/twitter/tmhUtilities.php' );
            
                               
            $tmhOAuth = new tmhOAuth(array(
              'consumer_key'    => $twitter_app_consumer_key,
              'consumer_secret' => $twitter_app_consumer_secret
            ));
   
      
              global $current_user;
              get_currentuserinfo();
               $user_id = $current_user->ID;     
                           
                               
             $twitter_token = json_decode(get_user_meta($user_id, 'mwa_twitter_token', true), true);                  
                    
                      //var_dump($twitter_token);
                     
                        parse_str($_SERVER["REQUEST_URI"], $_GET);        
                  
                 if ( isset($twitter_token['access_token']) ) {
                       
                    
                    $tmhOAuth->config['user_token']  = $twitter_token['access_token']['oauth_token'];
                    $tmhOAuth->config['user_secret'] = $twitter_token['access_token']['oauth_token_secret'];
                  
                    $code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/account/verify_credentials'));
                    if ($code == 200) {
                      $resp = json_decode($tmhOAuth->response['response']);
                      echo "Welcome " . $resp->screen_name . "!<br>";
                      echo "<a href='$site_base/wp-admin/admin.php?page=twitter-settings&tweeter_logout=tweeter_logout'>Click Here to Log Out!</a>";
                    } else {

						echo "<div class='error'>";
						echo "<p>" . "Error!" . "</p>";
                        echo "<pre>" . print_r(json_decode($tmhOAuth->response['response'], true), true) . "</pre>";
						echo "</div>";

                    }             
                                                 
                               
                  }elseif(isset($_GET['oauth_verifier'])){
                    
                      	$tmhOAuth->config['user_token']  = $twitter_token['oauth']['oauth_token'];
  						$tmhOAuth->config['user_secret'] = $twitter_token['oauth']['oauth_token_secret'];
                    
                        $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
                          'oauth_verifier' => $_GET['oauth_verifier']
                        ));
                      
                        if ($code == 200) {
                          $twitter_token['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
                          
                          unset($twitter_token['oauth']);
                          update_user_meta( $user_id, "mwa_twitter_token", json_encode($twitter_token) );
                          
                          echo "<a href='$site_base/wp-admin/admin.php?page=twitter-settings'>Connection Established, Click Here to Continue!</a>";
                          
                          
                          
                          
                          $tmhOAuth2 = new tmhOAuth(array(
                            'consumer_key'    => $twitter_app_consumer_key,
                            'consumer_secret' => $twitter_app_consumer_secret
                          ));
                           	//$tmhOAuth2->config['user_token']  = $twitter_token['oauth']['oauth_token'];
  				//$tmhOAuth2->config['user_secret'] = $twitter_token['oauth']['oauth_token_secret'];
                          
             			$tmhOAuth2->config['user_token']  = $twitter_token['access_token']['oauth_token'];
                    		$tmhOAuth2->config['user_secret'] = $twitter_token['access_token']['oauth_token_secret'];





				//scheduled_tweet(array('user_id' => $user_id));


				//add_action('scheduled_tweet_action', 'scheduled_tweet');
				$schedule_args= array('user_id' => $user_id);
			
			
				if ( !wp_next_scheduled( 'scheduled_tweet_action' ) ) {
   					wp_schedule_event(time(), 'daily', 'scheduled_tweet_action', $schedule_args);
				}






                        } else {
                          	echo 'Error 1: ' . $tmhOAuth->response['response'] . PHP_EOL;
  				tmhUtilities::pr($tmhOAuth);
                          	//outputError($tmhOAuth);
                        }
                  }elseif(count($twitter_token['access_token']) == 0){
                    if(isset($twitter_app_consumer_key) && isset($twitter_app_consumer_secret) && $twitter_app_consumer_key != '' && $twitter_app_consumer_secret != ''){
                          $here = tmhUtilities::php_self();
                          $params = array(
                                  //'oauth_callback'     => $here,
                                  'x_auth_access_type' => 'write'
                          );
                          
                          $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), $params);
                 

                         if ($code == 200) {
                            $twitter_token['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
                  
                  		update_user_meta( $user_id, "mwa_twitter_token", json_encode($twitter_token) );
                          
                            //$method = isset($_REQUEST['authenticate']) ? 'authenticate' : 'authorize';
                           //$force  = isset($_REQUEST['force']) ? '&force_login=1' : '';
                  		$method= 'authorize';
                  		$force = '';
                            $authurl = $tmhOAuth->url("oauth/{$method}", '') .  "?oauth_token={$twitter_token['oauth']['oauth_token']}{$force}";
                           //echo "Please Login!<br>";
                           echo "<p><a href='$authurl'><img src='$site_base/wp-content/plugins/tweet-manager/images/connect-twitter-button.png' ></a></p>";
                           
                 		} else {

							echo "<div class='error'>";
							echo "<p>" . "Error!" . "</p>";
                        	echo "<pre>" . print_r(json_decode($tmhOAuth->response['response'], true), true) . "</pre>";
							echo "</div>";
                      	}

                  }

            }
                               
                               
                               
       ?>                        
        </div>                  
      <?php
       }
                               
                               
                               

	
	/**
	 * Display options page
	 */
	public function optionsPage() {
		
	
	
	
	if(!function_exists('json_decode')){
		echo "<div class='error'><p>WARNING: PHP JSON is not available. Please install PHP JSON to continue.</p></div>";	
	}
	
	
	if(!function_exists('curl_init')){
		echo "<div class='error'><p>WARNING: PHP cURL is not available. Please install PHP cURL to continue.</p></div>";	
	}
		
		
		
		

	$site_base = get_option('siteurl'); 

?>
<script type="text/javascript">var wpurl = "<?php bloginfo('wpurl'); ?>";</script>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>Tweet Manager</h2>

	<?php

 	global $current_user;
        get_currentuserinfo();
        $user_id = $current_user->ID;

	require_once( dirname(__FILE__) . '/lib/twitter/tmhOAuth.php' );
        require_once( dirname(__FILE__) . '/lib/twitter/tmhUtilities.php' );

        $twitter_token = json_decode(get_user_meta($user_id, 'mwa_twitter_token', true), true);
                                 
	$tmhOAuth = new tmhOAuth(array(
		'consumer_key'    	=> get_option('twitter_app_consumer_key'),
		'consumer_secret' 	=> get_option('twitter_app_consumer_secret'),
		'user_token'  		=> $twitter_token['access_token']['oauth_token'],
		'user_secret' 		=> $twitter_token['access_token']['oauth_token_secret']
	));
    

	$code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/account/verify_credentials'));
        if ($code == 200) {
        	$resp = json_decode($tmhOAuth->response['response']);
        	echo "Welcome " . $resp->screen_name . "!<br>";
        	echo "<a href='$site_base/wp-admin/admin.php?page=twitter-settings&tweeter_logout=tweeter_logout'>Click Here to Log Out!</a>";
        } else {
	?>	
	<a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/tweet-manager/' target='_blank'>View the Setup Guide</a><br>
	<a href='<?php echo $site_base ?>/wp-admin/admin.php?page=twitter-settings'>Click Here setup Tweet Manager for WordPress!</a>
	<?php } ?>




<h2><a href='http://www.youtube.com/watch?v=jv7Yp0nf6Dk' target='_blank'>Plugin Setup and Description</a></h2>


<iframe width="640" height="480" src="http://www.youtube.com/embed/jv7Yp0nf6Dk" frameborder="0" allowfullscreen></iframe>




<h2><a href='http://youtube.com/MyWebsiteAdvisor' target='_blank'>More Video Tutorials</a></h2>


<script src="http://www.gmodules.com/ig/ifr?url=http://www.google.com/ig/modules/youtube.xml&up_channel=MyWebsiteAdvisor&synd=open&w=320&h=390&title=&border=%23ffffff%7C3px%2C1px+solid+%23999999&output=js"></script>

<a href='http://www.youtube.com/subscription_center?add_user=MyWebsiteAdvisor' target='_blank'>Subscribe to our YouTube Channel</a>


	
</div>
<?php
	}


} //end class



function scheduled_tweet($args){

	if(isset($args['user_id']) && $args['user_id'] > 0){
		$user_id = $args['user_id'];
	}else{
		$user_id = 1;
	}

	require_once( dirname(__FILE__) . '/lib/twitter/tmhOAuth.php' );
        require_once( dirname(__FILE__) . '/lib/twitter/tmhUtilities.php' );

        $twitter_token = json_decode(get_user_meta($user_id, 'mwa_twitter_token', true), true);
                                 
	$tmhOAuth = new tmhOAuth(array(
		'consumer_key'    	=> get_option('twitter_app_consumer_key'),
		'consumer_secret' 	=> get_option('twitter_app_consumer_secret'),
		'user_token'  		=> $twitter_token['access_token']['oauth_token'],
		'user_secret' 		=> $twitter_token['access_token']['oauth_token_secret']
	));
	
	$url = "http://MyWebsiteAdvisor.com/tools/wordpress-plugins/tweet-manager/ http://youtube.com/watch?v=jv7Yp0nf6Dk";

	$messages[0] = "I'm using the Free #Tweet Manager Plugin for #WordPress by MyWebsiteAdvisor.com $url";
	$messages[1] = "I'm using the Free #WordPress Plugin for #Twitter by MyWebsiteAdvisor.com $url";	

	shuffle($messages);

	 $resp_code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update'), array(
         	'status' => $messages[0]
         ));
	

}

add_action('scheduled_tweet_action', 'scheduled_tweet');



?>