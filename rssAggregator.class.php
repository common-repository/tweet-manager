<?php

class rssAggregator{
  
	public function get_aggregation(){
       
		$twitter_app_feeds_list = get_option('twitter_app_feeds_list');
		$twitter_app_google_news_topics_list = get_option('twitter_app_google_news_topics_list');  
										   
		$feedUrls = explode("\r\n", $twitter_app_feeds_list);
		$googleTopics = explode("\r\n", $twitter_app_google_news_topics_list);
                 
                  
		$rss = array();
                               
		foreach($feedUrls as $feedUrl){ 
                               
			$rawFeed = file_get_contents($feedUrl);
			$xml = new SimpleXmlElement($rawFeed);   
                               
			foreach($xml->channel->item as $rss_item){   
				$new_item['pubDate'] 	= (string)$rss_item->pubDate;
				$new_item['title'] 	= (string)$rss_item->title;
				$new_item['link'] 	= (string)$rss_item->link;
			
				$new_item['tags'] 	= array();
	
				$title_base = explode(" - ", $new_item['title']);  		
				$new_item['title'] = $title_base[0];
				  
				parse_str((string)$rss_item->link, $url_vars);
				if(isset($url_vars['url'])){
					$new_item['link'] = $url_vars['url'];
					$new_item['source'] = (string)$rss_item->link;
				}
	
				foreach($googleTopics as $kw){
					if(preg_match("/$kw/i", $new_item['title']) > 0){
						$new_item['title'] = preg_replace("/$kw/i", "#$kw", $new_item['title']);
						$new_item['tags'][] = $kw;
					}
				}
		
				
				$rss[] = $new_item;
				           
			}       
		}
					 
		uasort($rss, array($this, 'date_sorter'));   
		return $rss;
       
	}
       
                         
	public function date_sorter($a, $b){
		return ( strtotime($b['pubDate']) - strtotime($a['pubDate']) );
	}
                                                          
}
                          
?>