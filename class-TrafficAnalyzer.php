<?php
if(!class_exists("TrafficAnalyzer")){

class TrafficAnalyzer {
		
		const DEFAULT_NO_OF_TITLES = 3; 
		
		private $wpta_cookie = "";

				
		
		public function cookie(){
			
			if(!isset($_COOKIE['wpta'])){
				$hid = hash("md5",time());
				setcookie("wpta",$hid);
				$this->wpta_cookie = $hid;
			}else{
				$this->wpta_cookie = $_COOKIE['wpta'];
			}			
		}

		public  function activate() {

			global $wp_version,$wpdb;
			global $wp_roles;
			
			if(version_compare($wp_version, "3.0","<"))
				die("Traffic Analyzer requires Wordpress Version 3.0 or greater");
			
			$sql = 	" create table if not exists $wpdb->prefix"."tanalyzer_visits ( ". 
					" 	id integer primary key auto_increment, ".
					" 	ip varchar(100) , ".
					" 	script_name varchar(2000), ".
					" 	user_agent varchar(2000), ".
					"	request_uri	varchar(2000), ".
					" 	remarks text, ".
					"	browser	varchar(1000),".
					"	resource_type	varchar(100),".
					"	resource	varchar(2000),".
					" 	vtime timestamp default now(),".
					"	http_referer text " .			
					" ) ";
			$wpdb->query($sql);		// wp_tanalyzer_visits
			
			
			$sql = 	" create table if not exists $wpdb->prefix"."tanalyzer_resources ( ". 
					" 	resource_id int primary key , ".
					" 	resource_type varchar(100) , ".
					" 	resource_title varchar(2000) ".								
					" ) ";			
			$wpdb->query($sql);		// wp_tanalyzer_resources
			
			
			$sql = 	" create table if not exists $wpdb->prefix"."tanalyzer_pre ( ". 
					" 	hid varchar(100) , ".
					" 	ip varchar(100) , ".
					" 	script_name varchar(2000), ".
					" 	user_agent varchar(2000), ".
					"	request_uri	varchar(2000), ".
					" 	remarks text, ".
					"	browser	varchar(1000),".
					"	resource_type	varchar(100),".
					"	resource	varchar(2000),".
					" 	vtime timestamp default now(),".
					"	http_referer text " .								
					" ) ";			
			$wpdb->query($sql);		// wp_tanalyzer_pre
			
			
			
			
			$sql = " delete from $wpdb->prefix{tanalyzer_resources} ";
			$wpdb->query($sql);
			
			
			$sql = 	" insert into $wpdb->prefix"."tanalyzer_resources(resource_id,resource_type,resource_title) " .
					" values( -1,'HOME','Home') " ;			
			$wpdb->query($sql);
			
			$sql = "update $wpdb->prefix"."_tanalyzer_visits set resource='-1' where resource='' and resource_type='HOME' ";
			$wpdb->query($sql);
			
			$wp_roles->add_cap("administrator","ta_settings");
			
			$wp_roles->add_cap("administrator","ta_visits");
			
			$wp_roles->add_cap("administrator","ta_visits_visits");
			
			$sql = " alter table $wpdb->prefix"."tanalyzer_visits add column wpta_cookie varchar(100) ";
			$wpdb->query($sql);

			$sql = " alter table $wpdb->prefix"."tanalyzer_pre add column wpta_cookie varchar(100) ";
			$wpdb->query($sql);

		}
		
		/**
		 * 
		 * Unused Since 1.9.0, instead use pre_loaded and post_loaded
		 */
		public function loaded() {
			$str="";
			global $wp_query;
			
			$resource_type = "";
			if($wp_query->is_home){
				$resource_type="HOME";
			}else if($wp_query->is_page) {
				$resource_type="PAGE";
			}else if($wp_query->is_single){
				$resource_type="POST";
			}else if($wp_query->is_comments_popup){
				$resource_type="COMMENTS_POPUP";
			}else if($wp_query->is_comment_feed){
				$resource_type="COMMENTS_FEED";
			}else if($wp_query->is_archive){
				$resource_type="ARCHIVE";
			}else if($wp_query->is_author){
				$resource_type="AUTHOR";
			}else if($wp_query->is_post_type_archive){
				$resource_type="POST_TYPE_ARCHIVE";
			}else if ($wp_query->is_robots){
				$resource_type="ROBOTS";
			}else if ( $wp_query->is_feed){
				$resource_type="FEED";
			}else if($wp_query->is_trackback){
				$resource_type="TRACKBACK";
			}else if($wp_query->is_404){
				$resource_type="404";
			}else if($wp_query->is_category){
				$resource_type="CATEGORY";
			}else if($wp_query->is_tag){
				$resource_type="TAG";
			}else if($wp_query->is_tax){
				$resource_type="TAX";
			}else if ( $wp_query->is_search){
				$resource_type="SEARCH";
			}else if($wp_query->is_admin){
				$resource_type="ADMIN";				
			}else {
				$resource_type="OTHER";
			}
			
			$browser="";
			
			if($GLOBALS["is_IE"]){
				$browser = "InternetExplorer";
			}else if($GLOBALS["is_gecko"]){
				$browser = "Firefox";
			}else if( $GLOBALS["is_chrome"]){
				$browser = "Chrome";
			}else if($GLOBALS["is_iphone"]){
				$browser = "IPhone";
			}else if ($GLOBALS["is_lynx"]){
				$browser = "Lynx";
			}else if($GLOBALS["is_macIE"]){
				$browser = "MAC IE";
			}else if ( $GLOBALS["is_NS4"]){
				$browser = "Netscape";
			}else if ( $GLOBALS["is_opera"]){
				$browser = "Opera";
			}else if( $GLOBALS["is_safari"]) {
				$browser = "Safari";
			}else if( $GLOBALS["is_winIE"]) {
				$browser = "Windows IE";
			}else {
				$browser = "Other";
			}

			$post = $wp_query->post;

			if(isset($post))
				$resource = $post->ID;
			else 
				$resource = "";
			
			$referer = wp_get_referer();			
									
			global $wpdb;
			
			if($resource_type=="HOME" && empty($_SERVER['QUERY_STRING'])) 	
				$resource="-1";
			else if($resource_type=="HOME" && !empty($_SERVER['QUERY_STRING'])) // css, js and image request should not be recorded
				return;
					

			$sql = " insert into $wpdb->prefix"."tanalyzer_visits ( ip, script_name, user_agent, request_uri,resource_type,browser,resource,http_referer,remarks ) values (%s,%s,%s,%s,%s,%s,%s,%s,%s)";
			$remarks = 'VIA SHUTDOWN';
			$sql = $wpdb->prepare($sql,array($_SERVER["REMOTE_ADDR"], $_SERVER["SCRIPT_NAME"],$_SERVER["HTTP_USER_AGENT"], $_SERVER["REQUEST_URI"], $resource_type, $browser, $resource, $referer,$remarks));

			if(in_array($resource_type, array("POST","PAGE","HOME")))
				$wpdb->query($sql);
		}

		public function pre_loaded() {
			$str="";
			global $wp_query;
			
			$resource_type = "";
			if($wp_query->is_home){
				$resource_type="HOME";
			}else if($wp_query->is_page) {
				$resource_type="PAGE";
			}else if($wp_query->is_single){
				$resource_type="POST";
			}else if($wp_query->is_comments_popup){
				$resource_type="COMMENTS_POPUP";
			}else if($wp_query->is_comment_feed){
				$resource_type="COMMENTS_FEED";
			}else if($wp_query->is_archive){
				$resource_type="ARCHIVE";
			}else if($wp_query->is_author){
				$resource_type="AUTHOR";
			}else if($wp_query->is_post_type_archive){
				$resource_type="POST_TYPE_ARCHIVE";
			}else if ($wp_query->is_robots){
				$resource_type="ROBOTS";
			}else if ( $wp_query->is_feed){
				$resource_type="FEED";
			}else if($wp_query->is_trackback){
				$resource_type="TRACKBACK";
			}else if($wp_query->is_404){
				$resource_type="404";
			}else if($wp_query->is_category){
				$resource_type="CATEGORY";
			}else if($wp_query->is_tag){
				$resource_type="TAG";
			}else if($wp_query->is_tax){
				$resource_type="TAX";
			}else if ( $wp_query->is_search){
				$resource_type="SEARCH";
			}else if($wp_query->is_admin){
				$resource_type="ADMIN";				
			}else {
				$resource_type="OTHER";
			}
			
			$browser="";
			
			if($GLOBALS["is_IE"]){
				$browser = "InternetExplorer";
			}else if($GLOBALS["is_gecko"]){
				$browser = "Firefox";
			}else if( $GLOBALS["is_chrome"]){
				$browser = "Chrome";
			}else if($GLOBALS["is_iphone"]){
				$browser = "IPhone";
			}else if ($GLOBALS["is_lynx"]){
				$browser = "Lynx";
			}else if($GLOBALS["is_macIE"]){
				$browser = "MAC IE";
			}else if ( $GLOBALS["is_NS4"]){
				$browser = "Netscape";
			}else if ( $GLOBALS["is_opera"]){
				$browser = "Opera";
			}else if( $GLOBALS["is_safari"]) {
				$browser = "Safari";
			}else if( $GLOBALS["is_winIE"]) {
				$browser = "Windows IE";
			}else {
				$browser = "Other";
			}

			$post = $wp_query->post;

			if(isset($post))
				$resource = $post->ID;
			else 
				$resource = "";
			
			$referer = wp_get_referer();			
									
			global $wpdb;
			
			if($resource_type=="HOME" && empty($_SERVER['QUERY_STRING'])) 	
				$resource="-1";
			else if($resource_type=="HOME" && !empty($_SERVER['QUERY_STRING']))		// css, js and image request should not be recorded
				return;
					
			$data = time() . $resource .$_SERVER['REMOTE_ADDR'];
						
			$hid = hash("md5",$data);

			$sql = $wpdb->prepare(" insert into $wpdb->prefix"."tanalyzer_pre ( hid,ip, script_name, user_agent, request_uri,resource_type,browser,resource,http_referer,wpta_cookie ) values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s) ",array($hid, $_SERVER["REMOTE_ADDR"], $_SERVER["SCRIPT_NAME"],$_SERVER["HTTP_USER_AGENT"], $SERVER["REQUEST_URI"], $resource_type, $browser, $resource, $referer, $this->wpta_cookie) );

			if(in_array($resource_type, array("POST","PAGE","HOME")))
				$wpdb->query($sql);
				
			$js_src = plugins_url("/js",__FILE__);

			$ta_aoid = get_option('ta_aoid');
			
			echo "<script type='text/javascript' src='".$js_src."/ta_loaded.js.php?hid=". $hid ."'></script>";
								
		}		
		
		public function add_style() {		
			$css_src = plugins_url("/css",__FILE__);			
			wp_enqueue_style('ta_handle1',$css_src."/ta_main.css",null,null);
		}

				
		public function add_script() {
			$js_src = plugins_url("/js",__FILE__);			
			wp_enqueue_script('ta_handle2',$js_src."/ta_main.js.php","jquery",null);			
		}
		
		public function add_multiselect(){
			$js_src = plugins_url("/js",__FILE__);
			$deps=array("jquery");	// Using the jquery bundled with this plugin. Other wise, unselect all is not working
		}
				
		public function add_settings_script() {
			$js_src = plugins_url("/js",__FILE__);			 
			wp_enqueue_script('ta_main_settings',$js_src."/ta_main_settings.js.php","jquery");			
		}
		
		public function add_jqgrid(){
			$js_src = plugins_url("/js",__FILE__);						
			$deps_js=array("jquery","jquery-ui_1816");
			$deps_css=array("ta_jqueryui_css");
			
			wp_enqueue_style('ta_jqgrid_',$js_src."/jqgrid/css"."/ui.jqgrid.css",$deps_css);			
			wp_enqueue_script('ta_jqgrid_lang',$js_src."/jqgrid/js/i18n"."/grid.locale-en.js",$deps_js);
			wp_enqueue_script('ta_jqgrid',$js_src."/jqgrid/js"."/jquery.jqGrid.min.js",$deps_js);		
			
		}
		
		
		/**
		 * 
		 * Since 2.2.0
		 */
		public function add_jqplot(){
			$js_src = plugins_url("/js",__FILE__);
			
			$deps_js=array("jquery");			
			
			if(!is_admin()){
				wp_enqueue_style('ta_jqplot_css',$js_src."/jqplot/jquery.jqplot.min.css");
				
				wp_enqueue_script('ta_jqplot',$js_src."/jqplot/jquery.jqplot.min.js",$deps_js);	
				wp_enqueue_script('ta_jqplot_excanvas',$js_src."/jqplot/excanvas.js",$deps_js);
				wp_enqueue_script('ta_jqplot_category_axis',$js_src."/jqplot/plugins/jqplot.categoryAxisRenderer.min.js",$deps_js);
				wp_enqueue_script('ta_jqplot_date_axis',$js_src."/jqplot/plugins/jqplot.dateAxisRenderer.min.js",$deps_js);
				wp_enqueue_script('ta_jqplot_canvas_text',$js_src."/jqplot/plugins/jqplot.canvasTextRenderer.min.js",$deps_js);
				wp_enqueue_script('ta_jqplot_canvas_text',$js_src."/jqplot/plugins/jqplot.canvasLabelRenderer.min.js",$deps_js);
				wp_enqueue_script('ta_jqplot_canvas_axis',$js_src."/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js",$deps_js);
				wp_enqueue_script('ta_jqplot_tick_renderer',$js_src."/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js",$deps_js);
				wp_enqueue_script('ta_jqplot_highlighter',$js_src."/jqplot/plugins/jqplot.highlighter.min.js",$deps_js);				
				wp_enqueue_script('ta_jqplot_loader',$js_src."/ta_jqplot_loader.js.php",$deps_js);
			}			
		}
		
		
		/**
		 * @since 2.8.0
		 * Loading flot for Trend Widget
		 */
		public function add_flot_loader_trend_widget(){
			$js_src = plugins_url("/js",__FILE__);
			
			$deps_js=array("jquery");
			wp_enqueue_script('ta_flot_views',$js_src."/flot/jquery.flot.min.js",$deps_js);			
			
			wp_enqueue_script('ta_flot_excanvas',$js_src."/flot/excanvas.min.js",$deps_js);
			wp_enqueue_script('ta_flot_loader_trend_widget',$js_src."/ta_trend_widget_flot.js.php",$deps_js);
						
		}

		/**
		* @since 3.4.0
		* Jquery for DateRangepicker and Flot
		*/

		public function add_custom_jquery(){
			$js_src = plugins_url("/js",__FILE__);
			wp_register_script( "jquery_1_6_4", $js_src."/flot/jquery-1.6.4.min.js" );
		}
		
		
		/**
		 * @since 2.3.0
		 * Loading jqplot for Analyzer->Views->ta_my_chart
		 */
		public function add_jqplot_loader_views(){
			$js_src = plugins_url("/js",__FILE__);
			
			$deps_js=array("jquery");
			
			wp_enqueue_style('ta_jqplot_views_css',$js_src."/jqplot/jquery.jqplot.min.css");
				
			wp_enqueue_script('ta_jqplot_views',$js_src."/jqplot/jquery.jqplot.min.js",$deps_js);	
			wp_enqueue_script('ta_jqplot_excanvas_views',$js_src."/jqplot/excanvas.js",$deps_js);
			//wp_enqueue_script('ta_jqplot_category_axis_views',$js_src."/jqplot/plugins/jqplot.categoryAxisRenderer.min.js",$deps_js);
			wp_enqueue_script('ta_jqplot_category_axis_views_non_min',$js_src."/jqplot/plugins/jqplot.categoryAxisRenderer.js",$deps_js);
			wp_enqueue_script('ta_jqplot_date_axis_views',$js_src."/jqplot/plugins/jqplot.dateAxisRenderer.min.js",$deps_js);
			wp_enqueue_script('ta_jqplot_canvas_text_views',$js_src."/jqplot/plugins/jqplot.canvasTextRenderer.min.js",$deps_js);
			wp_enqueue_script('ta_jqplot_canvas_text_views',$js_src."/jqplot/plugins/jqplot.canvasLabelRenderer.min.js",$deps_js);
			wp_enqueue_script('ta_jqplot_canvas_axis_views',$js_src."/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js",$deps_js);
			wp_enqueue_script('ta_jqplot_tick_renderer_views',$js_src."/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js",$deps_js);
			//wp_enqueue_script('ta_jqplot_highlighter_views',$js_src."/jqplot/plugins/jqplot.highlighter.min.js",$deps_js);
			wp_enqueue_script('ta_jqplot_highlighter_views_non_min',$js_src."/jqplot/plugins/jqplot.highlighter.js",$deps_js);
			wp_enqueue_script('ta_jqplot_renderer_bar_views',$js_src."/jqplot/plugins/jqplot.barRenderer.min.js",$deps_js);
			wp_enqueue_script('ta_jqplot_renderer_bubble_views',$js_src."/jqplot/plugins/jqplot.bubbleRenderer.min.js",$deps_js);
			wp_enqueue_script('ta_jqplot_loader_views',$js_src."/ta_jqplot_loader_views.js.php",$deps_js);
			
		}
		
		
		/**
		 * @since 2.3.0
		 * Loading flot for Analyzer->Views->ta_my_chart
		 */
		public function add_flot_loader_views(){
			$js_src = plugins_url("/js",__FILE__);
			$screen = get_current_screen();
			
			$deps_js=array("jquery_1_6_4");
			wp_enqueue_script('ta_flot_views',$js_src."/flot/jquery.flot.min.js",$deps_js);
			//wp_enqueue_script('ta_flot_pie_views',$js_src."/flot/jquery.flot.pie.min.js",$deps_js);
			wp_enqueue_script('ta_flot_pie_views',$js_src."/flot/jquery.flot.pie.resize_update.js",$deps_js);
			wp_enqueue_script('ta_flot_excanvas',$js_src."/flot/excanvas.min.js",$deps_js);
			wp_enqueue_script('ta_flot_loader_views',$js_src."/ta_flot_loader_views.js.php?screen=".$screen->id,$deps_js);
						
		}
		
		
		/**
		 * 
		 * Add Live chart script
		 */
		public function add_live_chart() {
			$aoid = get_option('ta_aoid');
			$js_src = plugins_url("/js",__FILE__);
			wp_enqueue_script('ta_live_chart',$js_src."/ta_live_init.js.php?ta_aoid=".$aoid);
		}
		
		/**
		 * 
		 * Add Drag n Drop feature
		 */
		public function add_drag_n_drop(){
			$js_src = plugins_url("/js",__FILE__);
			$css_src = plugins_url("/css",__FILE__);
			$deps_js=array("jquery");
			wp_enqueue_style('ta_drag_n_drop_css',$css_src."/ta_drag_n_drop.css");
			wp_enqueue_script('ta_drag_n_drop_js',$js_src."/ta_drag_n_drop.js",$deps_js);
		}
		
		
		
		public function add_daterangepicker(){
			$css_src = plugins_url("/css",__FILE__);
			$js_src = plugins_url("/js",__FILE__);			
			$ta_charts_src = plugins_url("",__FILE__);
					
			wp_enqueue_style('ta_daterangepicker_css',$js_src."/daterangepicker/css"."/ui.daterangepicker.css");
			wp_enqueue_style('ta_jqueryui_css',$js_src."/daterangepicker/css/redmond"."/jquery-ui-1.7.1.custom.css");
					
			
			$deps=array("jquery_1_6_4");			
			wp_enqueue_script('jquery-ui_1816',$js_src."/daterangepicker/js"."/jquery-ui-1.8.16.custom.min.js",$deps); // Daterangepicker is not working with its bundled jquery-ui-1.7.1.custom.min.js
						
			$deps=array("jquery_1_6_4","jquery-ui_1816");			
								
			wp_enqueue_script('ta_handle4',$js_src."/daterangepicker/js"."/daterangepicker.jQuery.js",$deps);
			wp_enqueue_script('ta_handle7',$js_src."/daterangepicker/js"."/jquery.ui.datepicker-ml.js",$deps);
			wp_enqueue_script('ta_handle8',$js_src."/daterangepicker/js"."/jquery.ui.datepicker-ar.js",$deps);
			wp_enqueue_script('ta_handle9',$js_src."/daterangepicker/js"."/jquery.ui.datepicker-es.js",$deps);
			wp_enqueue_script('ta_handle10',$js_src."/daterangepicker/js"."/jquery.ui.datepicker-fr.js",$deps);
			wp_enqueue_script('ta_handle11',$js_src."/daterangepicker/js"."/jquery.ui.datepicker-ta.js",$deps);
			wp_enqueue_script('ta_handle12',$js_src."/daterangepicker/js"."/jquery.ui.datepicker-en-GB.js",$deps);		
			
					
						
			//wp_enqueue_script('ta_handle14',$ta_charts_src."/ta_charts/js"."/loader.js.php?".$qry_string,$deps);	//Commented for implementing flot based graph
				
			
						
		}
		
	
	}	
}
