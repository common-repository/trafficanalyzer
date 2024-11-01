<?php
		// hid should be 32 in length
	
		if(!function_exists('add_action')){
	    	require_once("../../../../wp-config.php");
	    }
	    
	    if(strlen($_GET['hid'])!=32)
	    	exit;
	    
	    $sql = "insert into ".$wpdb->prefix."tanalyzer_visits ( ip,script_name,user_agent,request_uri,remarks,browser,resource_type,";
	    $sql .= "resource,http_referer,wpta_cookie) ";
	    $sql .= " select ip,script_name,user_agent,request_uri,remarks,browser,resource_type,resource,http_referer,wpta_cookie " ;
	    $sql .= " from ". $wpdb->prefix."tanalyzer_pre ";
	    $sql .= " where hid=%s";
	    
	    $prepared_sql = $wpdb->prepare($sql,$_GET['hid']);
	    
	    $wpdb->query($prepared_sql);   
