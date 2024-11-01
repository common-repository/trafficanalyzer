<?php
	/*
	 * This file is invoked from plugin_dir/js/ta_main.js.php
	 * 
	 */

	/*
 		* This is needed, beacuse any warning if generated at any php file will make an error in the generation of corrupted xml data
 		* 
 	*/

	if(function_exists("ini_set")){ 
		ini_set("display_errors", 0);
	}


	header("Content-Type:text/xml;charset:utf-8");
	
	if(!function_exists('add_action')){
	    	require_once("../../../wp-config.php");
	}
	 
		
	require_once 'ta_commons.php';
	
	require_once 'class-ta-sql.php';
	require_once 'class-ta-grid.php';
	$search = array();
	if(isset($_GET['_search']) && $_GET['_search']=='true'){
		if(isset($_GET['ip']) && !empty($_GET['ip']))
			$search['ip'] = $_GET['ip'];
		if(isset($_GET['http_referer']) && !empty($_GET['http_referer']) )
			$search['http_referer'] = $_GET['http_referer'];
		if(isset($_GET['user_agent']) && !empty($_GET['user_agent']) )
			$search['user_agent'] = $_GET['user_agent'];	
	}

	$grid = new TA_Grid($_GET['key'],$_GET['sdate'], $_GET['edate'], $_GET['period'], $_GET['sidx'], $_GET['sord'], $_GET['page'], $_GET['rows'],$search);
	$xml = $grid->get_view();
	echo $xml;
	