<?php
/*
Plugin Name: WP YouTube Counters
Description: Shortcodes to show YouTube channel's subscribers and video views count.
Version: 0.2
Author: Mateusz Adamus
Author URI: http://mateuszadamus.pl
License: GPLv2
*/

	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

	$subsCount = -1;
	$viewsCount = -1;
	
	function clearCache( ) {
		$files = glob( getCacheDir( ) .'*' ); // get all file names
		foreach( $files as $file ) {
			if( is_file( $file ) ) {
				unlink( $file );
			}
		}
	}
	
	function getCacheDir( ) {
		return plugin_dir_path( __FILE__ ) .'/cache/';
	}
	
	function getCacheTimePath( $id ) {
		return getCacheDir( ) . $id .'_cache_time.txt';
	}
	
	function getCacheViewsPath( $id ) {
		return getCacheDir( ) . $id .'_views_html.txt';
	}
	
	function getCacheSubscribersPath( $id ) {
		return getCacheDir( ) . $id .'_subscribers_html.txt';
	}
	
	function writeValueToFile( $value, $path ) {
		$f = fopen( $path, 'w' );
		if( $f ) {
			fwrite( $f, $value );
			fclose( $f );
		}
	}
	
	function shouldLoadFromFile( $id, $timeout ) {
		$f = @fopen( getCacheTimePath( $id ), 'r' );
		if( $f ) {
			$fileDate = fgets( $f );
			fclose( $f );
			
			$fileDateTime = new DateTime( $fileDate );
			$currentDateTime = new DateTime( );
			
			$fileTime = strtotime( $fileDateTime->format( 'Y-m-d H:i:s' ) );
			$currentTime = strtotime( $currentDateTime->format( 'Y-m-d H:i:s' ) );
			$hours = abs( ( $currentTime - $fileTime ) / 3600 );
			
			// load from file if file data younger than timeout parameter
			return $hours <= $timeout;
		}
		
		return false;
	}
	
	function getYouTubeData( $attributes ) {
		global $subsCount;
		global $viewsCount;
		
		extract(shortcode_atts(array(
			'id' => '',
			'key' => '',
			'timeout' => 12,
		), $atts));
		
		if( $attributes[ 'id' ] == '' ) {
			echo 'Parameter "id" is missing';
			return;
		} 
		if( $attributes[ 'key' ] == '' ) {
			echo 'Parameter "key" is missing';
			return;
		}
		
		$attributes[ 'timeout' ] = ( int )$attributes[ 'timeout' ];
		if( $attributes[ 'timeout' ] == 0 ) {
			$attributes[ 'timeout' ] = 12;
		}
		
		// check if it should be loaded from file
		$loadFromFile = shouldLoadFromFile( $attributes[ 'id' ], $attributes[ 'timeout' ] );
		
		// load from file
		if( $loadFromFile ) {
			$subsCount = intval( file_get_contents( getCacheSubscribersPath( $attributes[ 'id' ] ) ) );
			$viewsCount = intval( file_get_contents( getCacheViewsPath( $attributes[ 'id' ] ) ) );
		} else {
			$params = array( 'sslverify' => false, 'timeout' => 60 );
			$url = 'https://www.googleapis.com/youtube/v3/channels?part=statistics&id='. $attributes[ 'id' ] .'&key='. $attributes[ 'key' ];
			
			$youTubeData = wp_remote_get( $url, $params );
			if ( is_wp_error( $youTubeData ) || $youTubeData[ 'response' ][ 'code' ] >= 400 ) {
				return;
			}
			
			$response = json_decode( $youTubeData[ 'body' ], true );
			
			$subsCount = intval( $response[ 'items' ][ 0 ][ 'statistics' ][ 'subscriberCount' ] );
			$viewsCount = intval( $response[ 'items' ][ 0 ][ 'statistics' ][ 'viewCount' ] );
			
			// cache data in the files
			$currentDateTime = new DateTime( );
			writeValueToFile( $currentDateTime->format( 'Y-m-d H:i:s' ), getCacheTimePath( $attributes[ 'id' ] ) );
			writeValueToFile( $subsCount, getCacheSubscribersPath( $attributes[ 'id' ] ) );
			writeValueToFile( $viewsCount, getCacheViewsPath( $attributes[ 'id' ] ) );
			
			// clear cache for the page
			// if WP Fastest Cache is installed
			if( isset( $GLOBALS[ "wp_fastest_cache" ] ) ) {
				$GLOBALS[ "wp_fastest_cache" ]->singleDeleteCache( false, get_the_ID( ) );
			}
		}
	}
	
	// shortcode function for views count
	function wp_youtube_counters_views_count( $attributes ) {
		global $viewsCount;
		
		if( $viewsCount < 0 ) {
			getYouTubeData( $attributes );
		}
	
		return $viewsCount;
	}
	add_shortcode( 'youtube_views_count', 'wp_youtube_counters_views_count' );
	
	// shortcode function for subscribers count
	function wp_youtube_counters_subscribers_count( $attributes ) {
		global $subsCount;
		
		if( $subsCount < 0 ) {
			getYouTubeData( $attributes );
		}
	
		return $subsCount;
	}
	add_shortcode( 'youtube_subscribers_count', 'wp_youtube_counters_subscribers_count' );
?>