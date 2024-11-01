<?php
	$user_agent = array(
		'bot', 'yahoo', 'msn', 'spider' , 'crawl'  ,  '@' , 'ripper' , 'google', 'robot' , 'radian', 'python', '.exe' , 'perl','redirect','java'
	);
	
	function ta_filter_period($period){
		$filtered_period = "";
		switch($period){
			case 'd':
				$filtered_period = 'd';
				break;
			case 'w':
				$filtered_period = 'w';
				break;
			case 'm':
				$filtered_period = 'm';
				break;	
			case 'a':
				$filtered_period = 'a';
				break;			
		}
		return $filtered_period;		
	}
	
	
	/*
	 * Date should be in yyyy/mm/dd format, else return date 01/01/1970
	 */
	function ta_filter_date($dt){
		if(!empty($dt))
			$filtered_date = date("Y/m/d",strtotime($dt));
		else 
			$filtered_date = "";
		return $filtered_date;		
	}
	
	/*
	 * In the case of jqgrid, order by field is passed from javascript
	 */
	function ta_filter_order_by_field($field){
		$filtered_field="";
		switch($field){
			case 'vtime':
				$filtered_field = "vtime";
				break;
			default:
				$filtered_field = "vtime";
				break;
			
		}
		return $filtered_field;
	}
	
	/*
	 * In the case of jqgrid, order  is passed from javascript
	 */
	function ta_filter_order($order){
		$filtered_order="";
		switch($order){
			case 'asc':
				$filtered_order = "asc";
				break;
			case 'desc':
				$filtered_order = "desc";
				break;
			default:
				$filtered_order = "desc";
				break;
							
		}
		return $filtered_order;
	}
	
	/*
	 * Filter the search array created in grid_data.php for jqgrid search
	 */
	function ta_filter_search($search=array()){
		$filtered_search=array();
		global $wpdb;
		foreach($search as $key=>$value){
			$filtered_search[$key] = $wpdb->escape($value); 
		}
		return $search;
	}
	
	
	/*
	 * Checks whether requested is monthly or not
	 * @param string $period
	 * @return bool
	 */
	function ta_is_monthly($period){
		if($period=="m")
			return true;
		else 
			return false;
	}
	
	/*
	 * Checks whether requested is weekly or not
	 * @param string $period
	 * @return bool
	 */
	function ta_is_weekly($period){
		if($period=="w")
			return true;
		else 
			return false;
	}
		
	
	
		
	/*
	 * Function to determine the first day for the week or month corresponding to the argument date, assuming monday is the first day of the week
	 * @param string $dt , date in format "yyyy/mm/dd" 
	 * @param string $period, m for first day of month , w for first day of week
	 * @return string ,  first day of the week for the argument date
	 */	
	function ta_get_first_day($dt,$period){
		if(ta_is_monthly($period)){	// For first day of month
			$dt = substr($dt,0,4)."/".substr($dt,5,2)."/01";
		}else if(ta_is_weekly($period)){	// For first day of week		
			/* Buggy Implementation
			$ts = mktime(0,0,0,substr($dt,5,2),substr($dt,8,2),substr($dt,0,4));
			$week = date("W",$ts);
			$year = date("o",$ts);			
			$wk_ts  = strtotime('+' . $week . ' weeks', strtotime($year . '0101'));
		   	$mon_ts = strtotime('-6'  . ' days', $wk_ts);   // monday timestamp	    	
		   	$dt = date('Y-m-d', $mon_ts);
		   	*/
			
			$ts = mktime(0,0,0,substr($dt,5,2),substr($dt,8,2),substr($dt,0,4));
			$week = date("w",$ts);
			if($week==0)
				$dt_ts  = strtotime( '-' . 6  . ' days ', $ts);
			else 
				$dt_ts = strtotime( '-' .  ( $week - 1 )  . ' days ', $ts);
			$dt = date('Y-m-d',$dt_ts);

			
		}
		return $dt;
	}

		/*
	 * Function to determine the last day for the week corresponding to the argument date, assuming the sunday is the last day of the week
	 * @param string $dt , date in format "yyyy/mm/dd" 
	 * @return string ,  last day of the week for the argument date
	 */	
	function ta_get_last_day($dt,$period){
		if(ta_is_weekly($period)){		// Weekly			
			$ts = mktime(0,0,0,substr($dt,5,2),substr($dt,8,2),substr($dt,0,4));
			$week = date("w",$ts);
			if($week==0)
				$dt_ts  = $ts;
			else 
				$dt_ts = strtotime('+' . ( 7- $week ) . ' days ', $ts);		
			
			$dt = date('Y-m-d',$dt_ts);
			
		}else if(ta_is_monthly($period)) {	// Monthly
			$dt_ts = strtotime($dt);
			$last_day = date("t",$dt_ts);
			
			$dt = substr($dt,0,4)."/".substr($dt,5,2)."/".$last_day;			
		}
		return $dt;
	}
	
	/*
	 * Function returns First date corresponding to the arguments week and year
	 */
	function ta_week_start_date($wk_num, $yr, $first = 1, $format = 'Y/m/d'){
	    $wk_ts  = strtotime('+' . $wk_num . ' weeks', strtotime($yr . '0101'));
	    
	    $wk_no = date('w',$wk_ts);
        if($wk_no==0)
                $mon_ts = strtotime('-' . 6  . ' days', $wk_ts);
        else
                $mon_ts = strtotime('-' . date('w',$wk_ts) + $first  . ' days', $wk_ts);
	    
	    return date($format, $mon_ts);	
	}
	
	/*
	 * @param int Numerator
	 * @param int Denominator
	 * @return int Percentage
	 */	
	function ta_get_percentage($nr,$dr){
		$perc = 0;
		if($dr!=0)
			$perc = intval(( $nr / $dr ) * 100 );
		return $perc."%";	
	}
	
	/*
	 * Retrieves the first view date from database
	 */
	function ta_get_first_view_date(){
		global $wpdb;
		$min_date='';
		$sql = " select min(date(vtime)) min_date from $wpdb->prefix"."tanalyzer_visits " ;
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$result = $wpdb->get_results($sql);		
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		
		foreach($result as $row){
			$min_date=$row->min_date;
		}
		return $min_date;
	}

	/*
	 * Retrieves the last view date from the database
	 */
	function ta_get_last_view_date(){
		global $wpdb;
		$max_date='';
		$sql = " select max(date(vtime)) max_date from $wpdb->prefix"."tanalyzer_visits " ;
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$result = $wpdb->get_results($sql);
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		foreach($result as $row){
			$max_date=$row->max_date;
		}
		return $max_date;
	}
	
	/*
	 * Return date in yyyy/mm/dd format corresponding to the label
	 * @param string eg : Oct-2011 for periodicity is m and 12-2011 for periodicity is w
	 * @param string  
	 */
	
	function ta_get_first_day_from_label($label,$periodicity){
		$start_date='';
		switch($periodicity){
			case 'w':
				$start_date = ta_week_start_date(substr($label,0,2),substr($label,3,4));
				break;
			case 'm':
				$start_date = "01-".$label;				
				break;
			default:
				$start_date = $label; 
		}
		return $start_date;			
	}	
	
	
		/*
		 * 
		 * @return string An hexadecimal value corresponding to the numeric argument
		 * @since 1.8.0
		 * @param int $num
		 */
		function ta_get_color($num){
			$hex = hexdec(($num-1)*12345);
			$hex = "#".$hex;
			$hex = str_pad($hex,7,"0",STR_PAD_RIGHT);			
			return $hex;	
		}
		
		
		
		function ta_user_agent_where(){
			global $user_agent;
			//$ta_robot = get_option("ta_robot");
			$ta_robot = "1" ; // Option for with robot and with out robot is removed since 1.9.0. So only Without robots is available
			
			// Loading the array $user_agent from plugin_dir/commons.php
			$like_operator="";
			$eq_operator="";		
			$logical_operator="";
			$where="";
			switch($ta_robot){
				case "1":		// Without robots
					$like_operator="not like";
					$logical_operator = " and " ;
					$eq_operator="<>";
					break;
				case "3":		// Only robots
					$like_operator="like";
					$eq_operator="=";
					$logical_operator = " or " ;
					break;
				case "2" :		// With robots
					$like_operator="";
					$eq_operator="";		
					$logical_operator="";
					break;
				default :	// if $ta_robot is not set
					$like_operator="not like";
					$logical_operator = " and " ;
					$eq_operator="<>";
					break;											
			}
			
			if(!empty($like_operator) && !empty($eq_operator)){
				$where .= " and ( " ;
				$i=0;
				foreach($user_agent as $agent){
					if($i!=0)
						$where .= " $logical_operator " ;
					$where .= "user_agent $like_operator '%$agent%' ";
					$i++;
				}		
				if($i!=0)
					$where .= " $logical_operator ";		
				$where .= " user_agent $eq_operator  ''";
				$where .= " ) " ;
			}
			
			return $where;
			
		}
		
		
		/**
		 * 
		 * Passes offset of timezone to the function and returns timezone string
		 * @param int $offset
		 * @since 3.1.0
		 */
		function ta_offset_to_timezone($offset){
			$time_zone=array(
				'-12'=>'-12:00',
				'-11.5'=>'-11:30',
				'-11'=>'-11:00',
				'-10.5'=>'-10:30',
				'-10'=>'-10:00',
				'-9.5'=>'-09:30',
				'-9'=>'-09:00',
				'-8.5'=>'-08:30',
				'-8'=>'-08:00',
				'-7.5'=>'-07:30',
				'-7'=>'-07:00',
				'-6.5'=>'-06:30',
				'-6'=>'-06:00',
				'-5.5'=>'-05:30',
				'-5'=>'-05:00',
				'-4.5'=>'-04:30',
				'-4'=>'-04:00',
				'-3.5'=>'-03:30',
				'-3'=>'-03:45',
				'-2.5'=>'-02:30',
				'-2'=>'-02:00',
				'-1.5'=>'-01:30',
				'-1'=>'-01:00',
				'-.5'=>'-00:30',
				'0'=>'+00:00',
				'14'=>'+14:00',
				'13.75'=>'+13:45',			
				'13'=>'+13:00',
				'12.75'=>'+12:45',
				'12'=>'+12:00',
				'11.5'=>'+11:30',
				'11'=>'+11:00',
				'10.5'=>'+10:30',
				'10'=>'+10:00',
				'9.5'=>'+09:30',
				'9'=>'+09:00',
				'8.5'=>'+08:30',
				'8'=>'+08:00',
				'7.5'=>'+07:30',
				'7'=>'+07:00',
				'6.5'=>'+06:30',
				'6'=>'+06:00',
				'5.5'=>'+05:30',
				'5'=>'+05:00',
				'4.5'=>'+04:30',
				'4'=>'+04:00',
				'3.5'=>'+03:30',
				'3'=>'+03:45',
				'2.5'=>'+02:30',
				'2'=>'+02:00',
				'1.5'=>'+01:30',
				'1'=>'+01:00',
				'.5'=>'+00:30'
			
			);
			return $time_zone[$offset];
		}
		
		
		/**
		 * @since 3.1.0
		 * To synchronize wordpress timezone and mysql timezone
		 */
		
		if (!function_exists('set_tzone')){
			function set_tzone(){
				global $wpdb;			
				$gm_offset = get_option( 'gmt_offset' );
				$tz = ta_offset_to_timezone($gm_offset);
				
				$sql_tz_set = " set time_zone='" . $tz . "'";			
				$wpdb->query($sql_tz_set);			
			}
		}
		
		/**
		 * @since 3.1.0
		 * To synchronize wordpress timezone and mysql timezone
		*/
		
		
		if(!function_exists('restore_tzone')){
			function restore_tzone($arg_tzone){
				global  $wpdb;
				$tz = $arg_tzone;
				$sql_tz_set = " set time_zone='" . $tz . "'";			
				$wpdb->query($sql_tz_set);		
			}
		}
	
		
		/**
		 * @since 3.1.0
		 * To synchronize wordpress timezone and mysql timezone
		*/
		if(!function_exists('cur_tzone')){
			function cur_tzone(){
				global  $wpdb;
				$sql = " select @@time_zone " ;
				$zone = $wpdb->get_var($sql);
				return $zone;
			}
		}		
		
	