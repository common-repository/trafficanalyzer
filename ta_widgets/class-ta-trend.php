<?php

/**
 * 
 * Statistics Widget, displays visits of today, yesterday, last 7 days and total
 * @author George Mathew K < georgemathewk@yahoo.com >
 * @since 2.2.0 < Creation Date : 27-Jan-2012 > 
 *
 */


class TA_Trend extends WP_Widget {

	
	
	
	/**
	 * 
	 * Constructor function for setting up widget
	 * @author George Mathew K < georgemathewk@yahoo.com >
	 * @since 2.2.0
	 */
	public  function __construct(){
		$id_base = "ta_trend";
		$name = "Analyzer : Trend Widget";
		$widget_options = array(
					'description'=>"Trend chart of website's visits"		
				);
		$control_options = array();
		parent::__construct($id_base,$name,$widget_options,$control_options);
	}
	
	
	
	
	
	/**
	 * Widget's Options Form
	 * @author George Mathew K < georgemathewk@yahoo.com >
	 * @since 2.2.0
	 * @param array $instance Current settings
	 */
	public function form($instance){
		
		$defaults = array(
					'title'=>'Chart : Visits',
					'no_of_days'=>4,
					'xaxis'=>false,					
					'powered'=>false
		
				);
				
		$instance = wp_parse_args((array)$instance,$defaults);
		
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title</label>
			<input type="text" value="<?php if(isset($instance['title'])) echo $instance['title']; ?>" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('no_of_days'); ?>">No of Days</label>		
			<select name="<?php echo $this->get_field_name('no_of_days'); ?>" id="<?php echo $this->get_field_id('no_of_days'); ?>" >
				<option value='2' <?php if(isset($instance['no_of_days'])) if($instance['no_of_days']=='2') echo 'selected';  ?> >2</option>
				<option value='3' <?php if(isset($instance['no_of_days'])) if($instance['no_of_days']=='3') echo 'selected'; ?> >3</option>
				<option value='4' <?php if(isset($instance['no_of_days'])) if($instance['no_of_days']=='4') echo 'selected'; ?> >4</option>
				<option value='5' <?php if(isset($instance['no_of_days'])) if($instance['no_of_days']=='5') echo 'selected'; ?> >5</option>
				<option value='6' <?php if(isset($instance['no_of_days'])) if($instance['no_of_days']=='6') echo 'selected'; ?> >6</option>
				<option value='7' <?php if(isset($instance['no_of_days'])) if($instance['no_of_days']=='7') echo 'selected'; ?> >7</option>				
			</select>
		</p>
		
		<p>
			<input type="checkbox" <?php checked( $instance['xaxis'], true ); ?> id="<?php echo $this->get_field_id('xaxis'); ?>" name="<?php echo $this->get_field_name('xaxis'); ?>" />
			<label for="<?php echo $this->get_field_id('xaxis'); ?>">Show X-Axis Labels</label>
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
	 * @since 2.2.0
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	public function update($new_instance, $old_instance){
		$instance = $old_instance;

		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['no_of_days'] = strip_tags( $new_instance['no_of_days'] );
		$instance['xaxis'] = isset($new_instance['xaxis']);	
		$instance['powered'] = isset($new_instance['powered']);
		
		update_option('ta_xaxis',$instance['xaxis']);
		update_option('ta_no_of_days',$instance['no_of_days']);
		update_option('ta_trend_powered',$instance['powered']);	
		
		return $instance;
		
	}
	
	
	
	
	
	
	/**
	 * Displays the widget content in front end - Client area
	 * @author George Mathew K < georgemathewk@yahoo.com >
	 * @since 2.2.0
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	public function widget($args, $instance) {
		extract($args);
		$title  = apply_filters('widget_title',$instance['title']);
		
		echo $before_widget;
		
		echo $before_title . $title . $after_title;
		
		?>
		<div id='trenddiv' style="width:100%;height:200px">
		</div>
		
		<?php 
		
		echo "<div align='center' id='wpta_trend'><a href='http://wptrafficanalyzer.in'>Powered by wpta</a></div>";
		
		echo $after_widget;
		
	}
	
	
	public function visits_today(){
		global $wpdb;
		$today = date("Y-m-d");
		$where_cond = " and date(vtime) = '" . $today . "'";
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
		
		
		return $cnt[0];
	}

	public function visits_yesterday(){
		global $wpdb;
		$yesterday = date("Y-m-d", strtotime("-1 day"));
		$where_cond = " and date(vtime) = '" . $yesterday . "'";
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
		
		return $cnt[0];
	}
	
	public function visits_last7(){
		global $wpdb;
		$start_date = date("Y-m-d", strtotime("-6 day"));
		$end_date = date("Y-m-d");		
		$where_cond = " and date(vtime) between  '" . $start_date . "'  and '". $end_date ."' ";
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
		
		return $cnt[0];
	}
}