    jQuery('img.tweet_specific').click(function(){
      //alert( jQuery(this).attr('id') );
      
     var tweet_id = jQuery(this).attr('id');
      
      	var ajax_data = {
              
               		send_specific_tweet : tweet_id,
		action: 'send_specific_tweet'
                
            };
      
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
 

