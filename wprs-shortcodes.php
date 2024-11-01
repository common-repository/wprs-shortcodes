<?php
	/*
	Plugin Name: WPRS Shortcodes
    Plugin URI: https://wprichsnippets.com
    Description: An add-on for the WPRichSnippets plugin, which help you display any of the Box elements via a shortcode.
    Version: 1.1.1
    Author: Hesham Zebida
    Author URI: http://zebida.com
    */
	
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	//* Filters
	// Add shortcodes to sidebar text widget
	add_filter('widget_text', 'do_shortcode');
	
	
	add_action( 'admin_init', 'wprs_shortcodes_detect_wprs' );
	/*
		Check if WPRichSnippets plugin is installed and active
		@since 1.0
		*/
	function wprs_shortcodes_detect_wprs()  {
		$xplugin = 'wp-rich-snippets/wp-rich-snippets.php';
		$plugin = plugin_basename( __FILE__ );
		if ( !is_plugin_active($xplugin) ) {
			// Display notice if plugin isn't active
			add_action('admin_notices', 'wprs_shortcodes_my_admin_notice');
			// Deactivate
			deactivate_plugins( $plugin );
		}
	}
	
	
	/*
		Admin notice
		@since 1.0
		*/
	function wprs_shortcodes_my_admin_notice(){
		echo '<div class="update-nag"><p>The <a href="https://wprichsnippets.com/" title="WPRichSnippets Plugin" target="_blank" >WPRichSnippets</a> plugin is required. Plugin has been deactivated!</p></div>';
	}

	
	add_shortcode('wprs-shortcodes', 'wprs_do_shortcodes');
	/*
		Shortcode
		@since 1.0
		*/
	function wprs_do_shortcodes($atts) {
		
		global $post;
		
		extract(shortcode_atts(array(
			'post_id' => $post->ID,
			'meta' => '',
        ), $atts));

		// Get template array
		$template = (function_exists('wprs_template') ) ? wprs_template($post_id) : array();
		
		// Get template element by meta 
		$wprs_shortcodes =  ( ! empty($template) ) ? $template[$meta] : '';
		
		// Return value
		return $wprs_shortcodes;
	}

	
	add_action( 'wp', 'wprs_shortcodes_detect' );
	/*
		Detect if shortcode has been used,
		and set a global variable wprs_shortcodes_has_been_used to true
		@since 1.0
		*/
	function wprs_shortcodes_detect() {
		
		global $post;
		
		$pattern = get_shortcode_regex();
		
		if ( ! isset($post->post_content) || $post->post_content != '' ) return;
		
		preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches );

	    if( is_array( $matches ) && array_key_exists( 2, $matches ) && in_array( 'wprs-shortcodes', $matches[2] ) ) {
        	
			//echo 'shortcode is used';
			$wprs_shortcodes_status = true;
			
    	} else {
			
			$wprs_shortcodes_status = false;
			
		}
		$GLOBALS['wprs_shortcodes_has_been_used'] = $wprs_shortcodes_status;
	}
	
