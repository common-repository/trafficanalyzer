<?php
	if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    	exit();

	global $wpdb;
	/*	Commented  because, no data should be lost on uninstalling the plugin
	delete_option('ta_titles');
	delete_option('ta_titles_id');
    	$sql = " drop table if exists $wpdb->prefix"."tanalyzer_visits ";    

	$wpdb->query($sql);
	$sql = " drop table if exists $wpdb->prefix"."tanalyzer_resources ";    
	$wpdb->query($sql);
	*/
?>
