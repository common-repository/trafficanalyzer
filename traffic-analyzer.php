<?php
/*

Plugin Name: Traffic Analyzer
Plugin URI: http://wptrafficanalyzer.in
Description: Analyzes the traffic and trend of the site
Version: 3.5.0
Author: George Mathew K
Author URI: http://wptrafficanalyzer.in
License: GPL2

*/


	$plugin_path = plugin_dir_path(__FILE__);	
	require_once $plugin_path . "/ta_commons.php";
	require_once $plugin_path . "/class-TrafficAnalyzer.php";
	require_once $plugin_path . "/class-TrafficAnalyzerViews.php";
	require_once $plugin_path . "/class-TrafficAnalyzerVisits.php";
	require_once $plugin_path . "/class-TrafficAnalyzerSettings.php";
	require_once $plugin_path . "/class-TrafficAnalyzerHelp.php";
	require_once $plugin_path . "/ta_widgets/class-ta-visits.php";
	require_once $plugin_path . "/ta_widgets/class-ta-trend.php";
	
	
	if(class_exists("TrafficAnalyzer")){
		$ta = new TrafficAnalyzer();
		$ta_views = new TrafficAnalyzerViews();			// calls actions()
		$ta_visits = new TrafficAnalyzerVisits();		// calls actions()
		$ta_settings = new TrafficAnalyzerSettings();	// calls actions()
		$ta_help = new TrafficAnalyzerHelp();
	}
	
	if(isset($ta)){
		if(!function_exists('ta_menu')){
			function ta_menu(){
				global $ta;
				global $ta_views;
				global $ta_visits;
				global $ta_settings;
				global $ta_help;
			
				if(function_exists("add_menu_page")) {
					$icon_url = plugin_dir_url(__FILE__)."images/analyzer.png";
					
					add_menu_page("Traffic Analyzer", "Analyzer", "ta_visits", "report_".basename(__FILE__),array(&$ta_views,'form'),$icon_url);
					add_submenu_page("report_".basename(__FILE__), "Views", "Views", "ta_visits"  , "report_".basename(__FILE__),array(&$ta_views,'form'));
					add_submenu_page("report_".basename(__FILE__), "Visits vs Views", "Visits vs Views", "ta_visits_visits"  , "visits_".basename(__FILE__),array(&$ta_visits,'form'));
					add_submenu_page("report_".basename(__FILE__), "Settings", "Settings","ta_settings", "settings_".basename(__FILE__),array(&$ta_settings,'form'));
					add_submenu_page("report_".basename(__FILE__), "Help", "Help",9, "help_".basename(__FILE__),array(&$ta_help,'form'));
					
					//add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug)
				}					
			}		
		}
		
		
		function ta_register_widgets(){
			register_widget('TA_Visits');
			register_widget('TA_Trend');
		}
		
		
		
		add_action('admin_menu','ta_menu');		
		add_action('activate_trafficanalyzer/traffic-analyzer.php',array(&$ta,'activate'),1);
		
		if(!is_admin()){
			$ta->add_flot_loader_trend_widget();
		}
		

		if(get_option('ta_aoid')){				// Enable live chart, if and only if user is having a valid aoid
			if(get_option('ta_live')){			// Is Live chart enabled
				if(get_option('ta_live_admin')){	// Is Live chart for admin pages are enabled
					$ta->add_live_chart();		// This will be executed for both admin pages and other posts
				}else{					// Enable live chart for non-admin pages
					if(!is_admin()){		// Is currently viewed page is admin page or not
						$ta->add_live_chart();	// This will be executed for non-admin pages
					}
				}
			}
		}
		
		wp_enqueue_script('jquery');
		
		add_action('init',array(&$ta,'cookie'));
		add_action('wp_head',array(&$ta,'pre_loaded'));
		remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');	// removes next and prev attribute of link tag. This is needed to prevent the prefetching mechanism of firefox by next attribute
		
		add_action('widgets_init','ta_register_widgets');		// Registers the widgets @since 2.1.0
			

	}
