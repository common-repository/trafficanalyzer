<?php

/**
 * 
 * Statistics Widget, displays visits of today, yesterday, last 7 days and total
 * @author George Mathew K < georgemathewk@yahoo.com >
 * @since 2.1.0 < Creation Date : 20-Jan-2012 > 
 *
 */


class TA_Visits extends WP_Widget {
	
	/**
	 * 
	 * Constructor function for setting up widget
	 * @author George Mathew K < georgemathewk@yahoo.com >
	 * @since 2.1.0
	 */
	public  function __construct(){
		$id_base = "ta_visits";
		$name = "Analyzer : Visits";
		$widget_options = array(
					'description'=>"A front end widget for visitor's statistics"		
				);
		$control_options = array();
		parent::__construct($id_base,$name,$widget_options,$control_options);
	}
	
	
	
	
	
	/**
	 * Widget's Options Form
	 * @author George Mathew K < georgemathewk@yahoo.com >
	 * @since 2.1.0
	 * @param array $instance Current settings
	 */
	public function form($instance){
		
		$defaults = array(
					'title'=>'Statistics : Visits',
					'online'=>true,	
					'last24'=>true,
					'today'=>true,
					'yesterday'=>true,
					'last7'=>false,
					'last30'=>true,
					'total'=>false,
					'powered'=>false,					
					
					'online_label'=>'Online Visitor\'s Count',
					'last24_label'=>'Last 24 Hours Count',
					'today_label'=>'Today\'s Count',
					'yesterday_label'=>'Yesterday\'s Count',
					'last7_label'=>'Last 7 Day\'s Count',
					'last30_label'=>'Last 30 Day\'s Count',
					'total_label'=>'All Time Count'
		
				);
				
		$instance = wp_parse_args((array)$instance,$defaults);
		
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title</label>
			<input type="text" value="<?php if(isset($instance['title'])) echo $instance['title']; ?>" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" />
		</p>
		
		<p>
			<input type="checkbox" title='Show/Hide' alt='Show/Hide' <?php checked( $instance['online'], true ); ?>  id="<?php echo $this->get_field_id('online'); ?>" name="<?php echo $this->get_field_name('online'); ?>" />
			<input type="text" alt="<?php echo $defaults['online_label'];?>" title="<?php echo $defaults['last24_label'];?>" value="<?php if(isset($instance['last24_label'])) echo $instance['online_label']; ?>" id="<?php echo $this->get_field_id('online_label'); ?>" name="<?php echo $this->get_field_name('online_label'); ?>" />
		</p>
		
		<p>
			<input type="checkbox" title='Show/Hide' alt='Show/Hide' <?php checked( $instance['last24'], true ); ?>  id="<?php echo $this->get_field_id('last24'); ?>" name="<?php echo $this->get_field_name('last24'); ?>" />
			<input type="text" alt="<?php echo $defaults['last24_label'];?>" title="<?php echo $defaults['last24_label'];?>" value="<?php if(isset($instance['last24_label'])) echo $instance['last24_label']; ?>" id="<?php echo $this->get_field_id('last24_label'); ?>" name="<?php echo $this->get_field_name('last24_label'); ?>" />
		</p>
		
		<p>
			<input type="checkbox" title='Show/Hide' alt='Show/Hide' <?php checked( $instance['today'], true ); ?>  id="<?php echo $this->get_field_id('today'); ?>" name="<?php echo $this->get_field_name('today'); ?>" />
			<input type="text"  alt="<?php echo $defaults['today_label'];?>" title="<?php echo $defaults['today_label'];?>" value="<?php if(isset($instance['today_label'])) echo $instance['today_label']; ?>" id="<?php echo $this->get_field_id('last24_label'); ?>" name="<?php echo $this->get_field_name('today_label'); ?>" />
		</p>
		
		<p>
			<input type="checkbox" title='Show/Hide' alt='Show/Hide' <?php checked( $instance['yesterday'], true ); ?>  id="<?php echo $this->get_field_id('yesterday'); ?>" name="<?php echo $this->get_field_name('yesterday'); ?>" />
			<input type="text" alt="<?php echo $defaults['yesterday_label'];?>" title="<?php echo $defaults['yesterday_label'];?>"  value="<?php if(isset($instance['yesterday_label'])) echo $instance['yesterday_label']; ?>" id="<?php echo $this->get_field_id('yesterday_label'); ?>" name="<?php echo $this->get_field_name('yesterday_label'); ?>" />
			
		</p>
		
		<p>
			<input type="checkbox" title='Show/Hide' alt='Show/Hide' <?php checked( $instance['last7'], true ); ?>  id="<?php echo $this->get_field_id('last7'); ?>" name="<?php echo $this->get_field_name('last7'); ?>" />
			<input type="text"  alt="<?php echo $defaults['last7_label'];?>" title="<?php echo $defaults['last7_label'];?>" value="<?php if(isset($instance['last7_label'])) echo $instance['last7_label']; ?>" id="<?php echo $this->get_field_id('last7_label'); ?>" name="<?php echo $this->get_field_name('last7_label'); ?>" />
		</p>
		
		
		<p>
			<input type="checkbox" title='Show/Hide' alt='Show/Hide' <?php checked( $instance['last30'], true ); ?> id="<?php echo $this->get_field_id('last30'); ?>" name="<?php echo $this->get_field_name('last30'); ?>" />
			<input type="text" alt="<?php echo $defaults['last30_label'];?>" title="<?php echo $defaults['last30_label'];?>" value="<?php if(isset($instance['last30_label'])) echo $instance['last30_label']; ?>" id="<?php echo $this->get_field_id('last30_label'); ?>" name="<?php echo $this->get_field_name('last30_label'); ?>" />
		</p>
		
		
		<p>
			<input type="checkbox" title='Show/Hide' alt='Show/Hide' <?php checked( $instance['total'], true ); ?> id="<?php echo $this->get_field_id('total'); ?>" name="<?php echo $this->get_field_name('total'); ?>" />
			<input type="text" alt="<?php echo $defaults['total_label'];?>" title="<?php echo $defaults['total_label'];?>" value="<?php if(isset($instance['total_label'])) echo $instance['total_label']; ?>" id="<?php echo $this->get_field_id('total_label'); ?>" name="<?php echo $this->get_field_name('total_label'); ?>" />
		</p>
		
		<p>
			<input type="checkbox" <?php checked( $instance['powered'], true ); ?> id="<?php echo $this->get_field_id('powered'); ?>" name="<?php echo $this->get_field_name('powered'); ?>" />
			<label for="<?php echo $this->get_field_id('powered'); ?>">Hide Powered by Text</label>
		</p>
		
		
		<br />
		
		
		
		<?php 
	}
	
	
	
	/**
	 * Updates the widget options
	 * @author George Mathew K < georgemathewk@yahoo.com >
	 * @since 2.1.0
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	public function update($new_instance, $old_instance){
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['powered'] = isset($new_instance['powered']);	
			
		$instance['today'] = isset($new_instance['today']);
		$instance['online'] = isset($new_instance['online']);
		$instance['yesterday'] = isset($new_instance['yesterday']);
		$instance['last7'] = isset($new_instance['last7']);
		$instance['total'] = isset($new_instance['total']);	
		$instance['last24'] = isset($new_instance['last24']);
		$instance['last30'] = isset($new_instance['last30']);
		
		$instance['online_label'] = strip_tags( $new_instance['online_label'] );
		$instance['today_label'] = strip_tags( $new_instance['today_label'] );
		$instance['yesterday_label'] = strip_tags( $new_instance['yesterday_label'] );
		$instance['last7_label'] = strip_tags( $new_instance['last7_label'] );
		$instance['total_label'] = strip_tags( $new_instance['total_label'] );
		$instance['last24_label'] = strip_tags( $new_instance['last24_label'] );
		$instance['last30_label'] = strip_tags( $new_instance['last30_label'] );

		update_option('ta_visit_powered',$instance['powered']);

		return $instance;		
	}
	
	
	/**
	 * Displays the widget content in front end - Client area
	 * @author George Mathew K < georgemathewk@yahoo.com >
	 * @since 2.1.0
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	public function widget($args, $instance) {
		extract($args);
		
		
		$defaults = array(
					'title'=>'Statistics : Visits',
					'online'=>true,
					'last24'=>true,
					'today'=>false,
					'yesterday'=>true,
					'last7'=>false,
					'last30'=>true,
					'total'=>false,
					'powered'=>false,					
		
					'online_label'=>'Online Visitors Count',
					'last24_label'=>'Last 24 Hours Count',
					'today_label'=>'Today\'s Count',
					'yesterday_label'=>'Yesterday\'s Count',
					'last7_label'=>'Last 7 Day\'s Count',
					'last30_label'=>'Last 30 Day\'s Count',
					'total_label'=>'All Time Count',
		
				);
				
		$instance = wp_parse_args((array)$instance,$defaults);
		
		
		
		$title  = apply_filters('widget_title',$instance['title']);
		
		echo $before_widget;
		
		echo $before_title . $title . $after_title;
		
		if($instance['online'])
			echo "<p><span style='width:150px;float:left'>" . $instance['online_label'] . "</span>: ".  $this->visits_online()  ."</p>";
		
		
		if($instance['last24'])
			echo "<p><span style='width:150px;float:left'>" . $instance['last24_label'] . "</span>: ".  $this->visits_last24()  ."</p>";
		
		if($instance['today'])
			echo "<p><span style='width:150px;float:left'>". $instance['today_label'] . "</span>: ".  $this->visits_today()  ."</p>";
			
		if($instance['yesterday'])
			echo "<p><span style='width:150px;float:left'>" . $instance['yesterday_label'] . "</span>: ".  $this->visits_yesterday()  ."</p>";
		
		if($instance['last7'])
			echo "<p><span style='width:150px;float:left'>" . $instance['last7_label'] . "</span>: ".  $this->visits_last7()  ."</p>";
			
		if($instance['last30'])
			echo "<p><span style='width:150px;float:left'>" . $instance['last30_label'] . "</span>: ".  $this->visits_last30()  ."</p>";

		if($instance['total'])
			echo "<p><span style='width:150px;float:left'>". $instance['total_label'] . "</span>: ".  $this->visits_total()  ."</p>";
			
		echo "<div align='center' id='wpta_powered'><a href='http://wptrafficanalyzer.in'>Powered by wpta</a></div>";

		echo $after_widget;
		
	}
	
	
	public function visits_last24(){
		global $wpdb;
		
		$where_cond = " and vtime between date_sub(now(),interval 24 hour) and now() ";
		$sql = 	" select count(*) as cnt from  ( select 1 " .
					" from ".$wpdb->prefix."tanalyzer_visits visits " .
					" where 1=1  " .
					" $where_cond " .				
					" " . ta_user_agent_where() . " " .
					" group by ip,user_agent, wpta_cookie " .
					" ) as t "	;	

		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$cnt = $wpdb->get_col($sql,0);	// Returns an array		
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		
		if(empty($cnt[0]) || $cnt[0]==0)
			$cnt[0] = 1;
		
		return $cnt[0];
	}
	
	public function visits_online(){
		global $wpdb;
		
		$where_cond = " and vtime between date_sub(now(),interval 30 minute) and now() ";
		$sql = 	" select count(*) as cnt from  ( select 1 " .
					" from ".$wpdb->prefix."tanalyzer_visits visits " .
					" where 1=1  " .
					" $where_cond " .				
					" " . ta_user_agent_where() . " " .
					" group by ip,user_agent, wpta_cookie " .
					" ) as t "	;	

		
		
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$cnt = $wpdb->get_col($sql,0);	// Returns an array		
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		
		
		if(empty($cnt[0]) || $cnt[0]==0)
			$cnt[0] = 1;
		
		return $cnt[0];
	}
	
	
	
	public function visits_today(){
		global $wpdb;
		$where_cond = " and date(vtime) = date(now()) ";
		$sql = 	" select count(*) as cnt from  ( select 1 " .
					" from ".$wpdb->prefix."tanalyzer_visits visits " .
					" where 1=1  " .
					" $where_cond " .				
					" " . ta_user_agent_where() . " " .
					" group by ip,user_agent, wpta_cookie " .
					" ) as t "	;

		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$cnt = $wpdb->get_col($sql,0);	// Returns an array		
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		if(empty($cnt[0]) || $cnt[0]==0)
			$cnt[0] = 1;
		
		return $cnt[0];
	}

	public function visits_yesterday(){
		global $wpdb;		
		$where_cond = " and date(vtime) =  date(date_sub(now(),interval 1 day)) ";
		$sql = 	" select count(*) as cnt from  ( select 1 " .
					" from ".$wpdb->prefix."tanalyzer_visits visits " .
					" where 1=1  " .
					" $where_cond " .				
					" " . ta_user_agent_where() . " " .
					" group by ip,user_agent, wpta_cookie " .
					" ) as t "	;		
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$cnt = $wpdb->get_col($sql,0);	// Returns an array		
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		if(empty($cnt[0]) || $cnt[0]==0)
			$cnt[0] = 1;
		
		return $cnt[0];
	}
	
	public function visits_last7(){
		global $wpdb;
				
		$where_cond = " and date(vtime) between  date(date_sub(now(),interval 6 day))  and date(now()) ";		
		$sql = 	" select count(*) as cnt from  ( select 1 " .
					" from ".$wpdb->prefix."tanalyzer_visits visits " .
					" where 1=1  " .
					" $where_cond " .				
					" " . ta_user_agent_where() . " " .
					" group by ip,user_agent, wpta_cookie " .
					" ) as t "	;		
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$cnt = $wpdb->get_col($sql,0);	// Returns an array		
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		
		if(empty($cnt[0]) || $cnt[0]==0)
			$cnt[0] = 1;
		
		return $cnt[0];
	}
	
	public function visits_last30(){
		global $wpdb;
				
		$where_cond = " and date(vtime) between  date(date_sub(now(),interval 29 day))  and date(now()) ";		
		$sql = 	" select count(*) as cnt from  ( select 1 " .
					" from ".$wpdb->prefix."tanalyzer_visits visits " .
					" where 1=1  " .
					" $where_cond " .				
					" " . ta_user_agent_where() . " " .
					" group by ip,user_agent, wpta_cookie " .
					" ) as t "	;		
		
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$cnt = $wpdb->get_col($sql,0);	// Returns an array		
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		
		
		
		if(empty($cnt[0]) || $cnt[0]==0)
			$cnt[0] = 1;
		
		return $cnt[0];
	}
	
	public function visits_total(){
		global $wpdb;
		$sql = 	" select count(*) as cnt from  ( select 1 " .
					" from ".$wpdb->prefix."tanalyzer_visits visits " .
					" where 1=1  " .				
					" " . ta_user_agent_where() . " " .
					" group by ip,user_agent, wpta_cookie " .
					" ) as t "	;		
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$cnt = $wpdb->get_col($sql,0);	// Returns an array		
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		if(empty($cnt[0]) || $cnt[0]==0)
			$cnt[0] = 1;
		
		
		return $cnt[0];
	}
	
}
