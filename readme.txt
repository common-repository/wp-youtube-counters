=== Plugin Name ===
Contributors: mateuszadamus
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=Y4RV4YRV57WVN&lc=PL&item_name=WP%20YouTube%20Counters&currency_code=PLN&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: youtube, subscribers, views, count, shortcodes, channel
Requires at least: 4.3
Tested up to: 4.6.1
Stable tag: "trunk"
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds shortcodes to show YouTube channel's subscribers and video views count.

== Description ==

Adds shortcodes to show YouTube channel's subscribers and video views count. For both shortcodes YouTube Channel ID and YouTube API Key is needed. 

For performance reasons the data is refreshed once every 12 hours. This can be modified with "timeout" parameter (value in hours).

Shortcodes are as follows:

* youtube_views_count - for channel video views count
* youtube_subscribers_count - for channel subscribers count

Parameters are as follows:

* id - YouTube channel ID (How to get YouTube channel ID? https://support.google.com/youtube/answer/3250431?hl=en)
* key - YouTube API key for browser (How to get YouTube API key? https://developers.google.com/youtube/registering_an_application#Create_API_Keys)
* timeout - cache timeout (value in hours) /default is 12 hours/

**For example:**

* [youtube_views_count id='YOUR_CHANNEL_ID' key='YOUR_API_KEY']
* [youtube_views_count id='YOUR_CHANNEL_ID' key='YOUR_API_KEY' timeout='1']

== Installation ==

1. Download *.zip file with plugin contents
2. Extract plugin from the *.zip file
3. Upload all extracted files and folders to the `/wp-content/plugins/wp-youtube-counters` directory
4. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 0.2 =
* Added caching of YouTube data to lower the use of Google API

= 0.1 =
* Initial release of the plugin