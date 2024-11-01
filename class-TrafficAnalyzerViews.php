<?php
class TrafficAnalyzerViews extends TrafficAnalyzer {
	
	public function __construct(){
		$this->actions();	
	}
	
	public function form() {
				
		global $current_user;
		
		
					
		?>			
		
		<div class="wrap">			
		<?php screen_icon(); ?>			
		
		<h2>Traffic Analyzer : Views</h2>
		
		<?php
			/*
			 if (isset($_SERVER['HTTP_USER_AGENT']) &&  (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
				echo "<h4>This page may not work as expected in Internet Explorer</h4>";
			*/
		?>		
		
		<?php 
			global $current_user;
			$opt_daterangepicker_start = get_option('ta_daterangepicker_start');
			$opt_daterangepicker_end = get_option('ta_daterangepicker_end');
			$opt_period = get_option('ta_period');		
		
		
		?>
					
		<form method="get" action="">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<br />
			
			<input type="text" readonly id="daterangepicker" name="daterangepicker" value="<?php echo $opt_daterangepicker_start[$current_user->ID] . '~'. $opt_daterangepicker_end[$current_user->ID] ; ?>" />
			<input type="hidden"  id="daterangepicker_start" name="daterangepicker_start" value="<?php echo $opt_daterangepicker_start[$current_user->ID]; ?>" />
			<input type="hidden"  id="daterangepicker_end" name="daterangepicker_end"  value="<?php echo $opt_daterangepicker_end[$current_user->ID]; ?>" />
			<select id='period' name='period'>
					<option value="a" <?php echo ($opt_period[$current_user->ID]=='a'?'selected':''); ?>  >Consolidated</option>
					<option value="d" <?php echo ($opt_period[$current_user->ID]=='d'?'selected':''); ?>  >Daily</option>
					<option value="w" <?php echo ($opt_period[$current_user->ID]=='w'?'selected':''); ?>  >Weekly</option>
					<option value="m" <?php echo ($opt_period[$current_user->ID]=='m'?'selected':''); ?>  >Monthly</option>				
			</select>
			
			
			<?php 
				$titles = get_option("ta_titles");
				global $current_user;
				$ta_title = $titles[$current_user->ID];
				if(!isset($ta_title) || empty($ta_title))
					$ta_title = self::DEFAULT_NO_OF_TITLES;							
			?>
			<select name="ta_titles" id="ta_titles">
				<option value="1" <?php echo ($ta_title==1)?"selected":"";  ?> >Top1</option>
				<option value="3" <?php echo ($ta_title==3)?"selected":"";  ?> >Top3</option>
				<option value="5" <?php echo ($ta_title==5)?"selected":"";  ?> >Top5</option>
				<option value="7" <?php echo ($ta_title==7)?"selected":"";  ?> >Top7</option>
				<option value="10" <?php echo ($ta_title==10)?"selected":"";  ?>  >Top10</option>								
				<option value="a" <?php echo ($ta_title=="a")?"selected":"";  ?> >All</option>
				<!-- option value="c" <?php echo ($ta_title=="c")?"selected":"";  ?> >Custom</option-->
			</select>
			
			<!--  select name="custom"  id="custom" multiple >
				<?php //echo $this->dropdown_articles();	?>
			</select-->
			
			<button type='button' id='ta_apply'  onclick="apply()">Apply</button>
			
		</form>
			
					
			<div align="center">
				<h3><span id="ta_my_chart_title" ></span></h3>
				<div id="ta_my_chart" align="center" style='width:100%; height:400px;' >
					<img src="<?php  echo plugin_dir_url(__FILE__). "images/wait30.gif" ;  ?>" />
				</div>
			</div>
			
			<br />
			
			<div class="ts_live">

				<div class="column">
				        <div class="portlet">
				                <div class="portlet-header">Legend</div>
				                <div class="portlet-content" id='url_portlet'>
				                	<div id="ta_my_chart_legend">
									</div>
									<div style='text-align:center'>
										<span id='span_show_all' ><input type='checkbox' id='show_all' onchange='show_all(this)' checked ><label for='show_all'>Show All</label></span>						
										<span id='span_fill_all'><input type='checkbox' id='fill_all' onchange='fill_all(this)' ><label for='fill_all'>Fill All</label></span>
									</div>			                
				                </div>
				        </div>
				</div>
				
				
				<div class="column" id='chart_type'>
				        <div class="portlet">
				                <div class="portlet-header">Chart Properties</div>
				                <div class="portlet-content" id='os_portlet'>
				                <div id='chart_type' style='text-align:center'>
								 	<input type="checkbox" name='line_chart' id='line_chart'  checked onchange='line_chart_changed(this)'/><label for='line_chart'>Line</label>				 					 	
								 	<input type="checkbox" name='bar_chart' id='bar_chart'  onchange='bar_chart_changed(this)' /><label for='bar_chart'>Bar</label>
								 	<input type="checkbox" name='point_chart' id='point_chart' checked  onchange='point_chart_changed(this)' /><label for='point_chart'>Points</label>
								</div>
				                
				                </div>
				        </div>			
				</div>
				
				<div class="column">
				         <div class="portlet">
				                <div class="portlet-header">Feedbacks</div>
						    	    <div class="portlet-content" id='color_portlet'>
									    <div style="text-align:center">
									 	Post your comments <a href='http://wptrafficanalyzer.in/comments' target='_blank'>here</a><br />
									 	or <br />
									 	Feedback to info@wptrafficanalyzer.in				 					 
									</div>
				                
				                
				                </div>
				        </div>
				</div>

				

		</div><!-- End ts_live -->

			
			
			<div id='ta_data'">
				<div id="jqgrid">
	    			<table id="grid"></table>
	    			<div id="gridpager"></div>
				</div>
			</div>
			
				
		</div>
		
		
		
		
					
		<?php
		
	}
	
	public function actions() {
		//Will be executed only on loading the views page
		add_action('load-toplevel_page_report_traffic-analyzer',array(&$this,"add_style"));				
		add_action('load-toplevel_page_report_traffic-analyzer',array(&$this,"add_custom_jquery"));				
		add_action('load-toplevel_page_report_traffic-analyzer',array(&$this,"add_daterangepicker"));
		add_action('load-toplevel_page_report_traffic-analyzer',array(&$this,"add_jqgrid"));		
		add_action('load-toplevel_page_report_traffic-analyzer',array(&$this,"add_script"));	
		add_action('load-toplevel_page_report_traffic-analyzer',array(&$this,"add_flot_loader_views"));
		add_action('load-toplevel_page_report_traffic-analyzer',array(&$this,"add_drag_n_drop"));		
	}	
	
	
		/**
		 * @since 2.3.0
		 * Returns all the published posts and pages as an html dropdown box
		 * 
		 */		
		public function dropdown_articles(){
			global $current_user;
			$ta_titles_id = get_option("ta_titles_id");
			$id = $ta_titles_id[$current_user->ID];
			$selected="";
			
			$option="";			
			$query = new WP_Query(array( 
										'post_status' => 'publish',
										'post_type' => array('post','page'),
										'posts_per_page'=>-1 ,	
										'orderby'=>'title',
										'order'=>'ASC'
			
										) 
									);			
			
			foreach($query->posts as $post){
					if(in_array($post->ID,$id))
						$selected="selected";
					else 
						$selected = "";			
					$option .= "<option value='".$post->ID."' $selected >".$post->post_title."</option>";			
			}		
			if(in_array(-1,$id))
				$selected = "selected";
			else 	
				$selected = "";
						
			$option .= "<option value='-1' $selected >Home</option>";		
			return $option;		
		}
	
	
	
	
	
}
