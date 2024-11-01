<?php

// Initialize the Class and add the action
add_action('init', 'twitterCustomPostTypeInit');

function twitterCustomPostTypeInit() {
    global $twitter_cpt;
    $twitter_cpt = new twitterCustomPostType();
}

  
class twitterCustomPostType{
   
  	public function __construct(){
    
              	//custom post type configs
                $tweets_config = array(
                      'labels' => array(
                          'name' => __( 'Tweets' ),
                          'menu_name' => __( 'Tweets' ),
                          'singular_name' => __( 'Tweet' ),
                          'add_new' => __( 'Add New Tweet' ),
                          'add_new_item' => __( 'Add New Tweet' ),
                          'edit' => __( 'Edit Tweet' ),
                          'edit_item' => __( 'Edit Tweet' ),
                          'new_item' => __( 'Add New Tweet' ),
                          'view' => __( 'View Tweet' ),
                          'view_item' => __( 'View Tweet' ),
                          'search_items' => __( 'Search Tweets' ),
                          'not_found' => __( 'No Tweets Found' ),
                          'not_found_in_trash' => __( 'No Tweets found in Trash' ),
                      ),
                      'description' => __('Tweets to be shown in Resources section.'),
                      'public' => false,
                      'show_ui' => true,
                      'publicly_queryable' => false,
                      'exclude_from_search' => true,
                      'menu_position' => 25.25,
                      'supports' => array('title', 'author', 'editor', 'thumbnail'), 
                      'can_export' => true,
                      'hierarchichal' => false,
                      'capability_type' => 'post'
                  );
                  
                register_post_type( 'tweets', $tweets_config ); 
          
          
		add_action('admin_menu', array(&$this, 'create_tweets_meta_box'));
          
          	add_action('manage_posts_custom_column',  array(&$this, 'manage_twitter_columns'));
          	add_filter('manage_edit-tweets_columns', array(&$this, 'add_new_tweets_columns'));
          
          	add_action('admin_head', array(&$this, 'add_tweeter_js_scripts'));
          
	}
  
  
  public function add_tweeter_js_scripts(){
    $site_base = get_bloginfo('url'); 

    wp_enqueue_script('tweet-manager', "$site_base/wp-content/plugins/tweet-manager/tweeter.js");
    	

	//$myStyleUrl = plugins_url('tweeter.css', __FILE__); 
      	//$myStyleFile = WP_PLUGIN_DIR . '/tweet-manager/tweeter.css';
        //if ( file_exists($myStyleFile) ) {
            wp_register_style('tweet-manager-css', "$site_base/wp-content/plugins/tweet-manager/tweeter.css");
            wp_enqueue_style( 'tweet-manager-css');
        //}

   //wp_enqueue_style('tweet-manager-css', '/wp-content/plugins/tweet-manager/tweeter.css');
    
  }
  
  
 public function add_new_tweets_columns($tweets_columns) {
		$new_columns['cb'] = '<input type="checkbox" />';
 
		
		$new_columns['title'] = _x('Tweet Name', 'column name');
		//$new_columns['content'] = __('Content');
   		
		$new_columns['author'] = __('Author');
 
 
		$new_columns['date'] = _x('Date', 'column name');
 		$new_columns['tweet_button'] = __('Tweet This');
		return $new_columns;
	}
  
  

  public function manage_twitter_columns($column_name) {
		global $post;

		$site_base = get_bloginfo('url'); 

		switch ($column_name) {
		
 		case 'tweet_button':
			//$post = get_post($id);
            $post_id = $post->ID;
			echo "<img id='$post_id' class='tweet_specific' src='$site_base/wp-content/plugins/tweet-manager/images/twitter_tweet_this.png'>";
			
			break;
                    
                    
		case 'content':
			//$post = get_post($id);
			
			echo $post->post_content;
			break;
		default:
			break;
		} // end switch
	}
  
  
  
    public function create_tweets_meta_box() {
      
    	add_meta_box( 'new-tweets-meta-boxes', 'Tweet This', array($this, 'new_tweets_meta_boxes'), 'tweets', 'normal', 'high' );
    	
  }
  
  
  public function new_tweets_meta_boxes($object, $post_type){
    
   	 
    
    	$tweet_content = $object->post_content;
	$site_base = get_bloginfo('url'); 


	
    echo "<textarea id='twitter_message' name='twitter_message' rows='4' cols='100'>$tweet_content</textarea>";
    echo "<img id='send_tweet' src='$site_base/wp-content/plugins/tweet-manager/images/twitter_tweet_this.png' >";
    
   
    
     echo "<script>
          jQuery('#send_tweet').click(function() {
         
            var twitter_message = jQuery('#twitter_message').val();
            //alert(twitter_message);       
            
            var ajax_data = {
                twitter_message: twitter_message,
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
		if(response.text){
	             	alert(response.text); 
		}else{
			alert(response.error);
		}	
            }
          });

         });        
          
          
          </script>";
            
            

  }
  
  
}

?>