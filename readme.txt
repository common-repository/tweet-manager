=== Plugin Name ===
Contributors: MyWebsiteAdvisor, ChrisHurst
Tags: twitter, api, admin, plugin, twitter, tweet
Requires at least: 3.0
Tested up to: 3.5.2
Stable tag: 1.1.5

Create Your Own Custom Twitter Application and Publish to Your Twitter account.



== Description ==

Create Your Own Custom Twitter Application and Publish to Your Twitter account.
Plugin creates a Custom Post Type called 'tweets' where you can save commonly used messages with links and videos to save time.
Plugin can read rss feeds, and allow you to post them to your Twitter account.

NOTE: Twitter no longer displays the "via YourAppName" with each tweet, but this plugin still works!


Developers Website: http://MyWebsiteAdvisor.com
Developers Plugin Page: http://MyWebsiteAdvisor.com/tools/wordpress-plugins/tweet-manager/


Check out the [Tweet Manager WordPress Plugin Setup Tutorial Video](http://www.youtube.com/watch?v=jv7Yp0nf6Dk):

http://www.youtube.com/watch?v=jv7Yp0nf6Dk&hd=1



Requirements:

1. Twitter Account
2. PHP cURL functions
3. PHP JSON functions


To-do:




== Installation ==

1. Upload plugin to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. View the plugin setup instructions and videos

Check out the [Tweet Manager WordPress Plugin Setup Tutorial Video](http://www.youtube.com/watch?v=jv7Yp0nf6Dk):
http://www.youtube.com/watch?v=jv7Yp0nf6Dk&hd=1




== Frequently Asked Questions ==

= Plugin doesn't work ... =

Please specify as much information as you can to help us debug the problem. 





== Screenshots ==

1. Plugin Example Screenshot
2. Plugin Example Screenshot
3. Setup Screenshot
4. Setup Screenshot
5. Setup Screenshot
6. Setup Screenshot
7. Setup Screenshot
8. Setup Screenshot
9. Setup Screenshot
10. Setup Screenshot
11. Setup Screenshot





== Changelog ==

= 1.1.5 =
* updated custom post type definition, changed 'exclude_from_search' to true


= 1.1.4 =
* Added better error reporting to teh plugin setup to display twitter API errors to aid in troubleshooting.


= 1.1.3 =
* Updated readme, plugin requirements, plugin needs PHP JSON and PHP cURL to work.
* Added a check on the settings page to display an warning message if JSON or cURL are not installed.

= 1.1.2 =
* updated plugin to use twitter api v1.1 because v1.0 does not work anymore.
* updated readme file

= 1.1.1 =
* fixed several improper opening php tags

= 1.1 =
* fixed issues related to subdirectory wordpress installs

= 1.0 =
* minor bug fixes in auth system

= 0.79 =
* fixed minor bug causing broken links to admin settings pages

= 0.78 =
* fixed minor bug causing errors in twitter.class.php, manage_twitter_columns function

= 0.77 =
* fixed minor bug causing errors on sites installed in subdirectories (ex: http://domain.org/subdirectory)

= 0.76 =
* fixed minor bug causing error on line 92 of twitter.class.php due to missing public delcaration before function manage_twitter_columns

= 0.75 =
* Initial release

