<?php

/*
 * All member variables, prefixed with arg are initialized with constructor 
 * 
 */


class TA_Grid_Visits {
	private $arg_key;
	private $arg_start_date;
	private $arg_end_date;
	private $arg_period;
	private $arg_order_by_field;
	private $arg_order;
	private $arg_page_no;
	private $arg_per_page;
	private $arg_search;
	
	private $start_date;
	private $end_date;
	private $view_count=0;
	private $page_count=0;
	private $visits_count=0;
	private $visits_page_count=0;
	
	public function __construct($key,$start_date,$end_date,$period,$order_by_field,$order,$page_no,$per_page,$search=array()){			
		$this->arg_key = intval($key);
		$this->arg_start_date = ta_filter_date($start_date);
		$this->arg_end_date = ta_filter_date($end_date);
		$this->arg_period = ta_filter_period($period);
		$this->arg_order_by_field = ta_filter_order_by_field($order_by_field);		
		$this->arg_order = ta_filter_order($order);
		$this->arg_page_no = intval($page_no);
		$this->arg_per_page = intval($per_page);
		$this->arg_search = ta_filter_search($search);
				
		if(!empty($this->arg_start_date)){
        	$this->start_date = ta_get_first_day($this->arg_start_date, $this->arg_period);
	       	if(!empty($this->arg_end_date))
				$this->end_date = ta_get_last_day($this->arg_end_date, $this->arg_period);
			else 		
				$this->end_date = ta_get_last_day($this->arg_start_date, $this->arg_period);
		}
	}

	public function select(){
		$select = array(
			"ip",
			"date_format(vtime,'%d-%b-%Y %H:%i') as dt",
			"user_agent",
			"http_referer"
		);
		return $select;
	}

	/*
	 * From tables without table prefix of wordpress
	 */
	
	public function from(){
		$from = "tanalyzer_visits";
		return $from;
	}
	
	public function where(){
		global $user_agent;
		$ta_robot = get_option("ta_robot");
		
		$where = " ";
		
		
		if(!empty($this->start_date) && !empty($this->end_date)){
			$where .= " and date(vtime) between '$this->start_date' and '$this->end_date'";
		}else if(!empty($this->start_date)){
			$where .= " and date(vtime) = '$this->start_date'";
		}
		
		$where .= ta_user_agent_where();
		
		if(is_array($this->arg_search)) {
			foreach($this->arg_search as $key=>$search){
				$where .= " and $key like '%".$search."%' ";
			}
		}
		
		if($this->arg_key=="-1")		// Visits
			$where .= " group by ip, user_agent,wpta_cookie ";
						
		return $where;		
	}	
	
	
	function offset(){
		$offset = ( $this->arg_page_no - 1 ) * $this->arg_per_page ;
		return $offset;
	}
	
	function get_view_count(){
		global $wpdb;
		
		
		$sql .= " select count(*) as cnt from ".$wpdb->prefix."tanalyzer_visits ";
		$sql .= " where 1=1 " ;
		$sql .= $this->where();
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$view_cnt = $wpdb->get_col($sql,0);		
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		$this->view_count = $view_cnt[0];		
		return $this->view_count;	
	}
	
	function get_page_count(){
		$count = $this->get_view_count();
		if($count%$this->arg_per_page==0)
			$page_count = $count / $this->arg_per_page;
		else 
			$page_count =  intval( $count / $this->arg_per_page ) + 1;
		$this->page_count = $page_count;
		return $page_count;
	}
	
	/**
	 * 
	 * @since 2.0.0
	 */
	function get_visits_count(){
		global $wpdb;
		$sql = " select count(*) as cnt from ( " ;
		$sql .= " select count(*) as cnt from ".$wpdb->prefix."tanalyzer_visits ";
		$sql .= " where 1=1 " ;
		$sql .= $this->where();
		$sql .= " ) as t " ;	
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$visits_cnt = $wpdb->get_col($sql,0);
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		$this->visits_count = $visits_cnt[0];
		return $this->visits_count;
		
	}

	/**
	 * 
	 * @since 2.0.0
	 */	
	function get_visits_page_count(){
		$count = $this->get_visits_count();
		if($count%$this->arg_per_page==0)
			$page_count = $count / $this->arg_per_page;
		else 
			$page_count =  intval( $count / $this->arg_per_page ) + 1;
		$this->visits_page_count = $page_count;
		return $page_count;	
	}
	
	
	/**
	 * 
	 * @since 2.0.0
	 */
	function get_visits($type="xml"){
		global $wpdb;
		if(empty($this->visits_count) || $this->visits_count == 0 )
			$visits_count = $this->get_visits_count();
		else
			$visits_count = $this->visits_count;
			
		if(empty($this->visits_page_count) || $this->visits_page_count == 0)
			$page_count = $this->get_visits_page_count();
		else 
			$page_count = $this->visits_page_count;
		
		$type ="xml";	// Now this function supports only xml
		
		$sql  = " select " ;
		$sql .= " ip, date_format(vtime,'%d-%b-%Y %H:%i') as dt, user_agent, http_referer" ;
		$sql .= " from " . $wpdb->prefix . "tanalyzer_visits " ;
		$sql .= " where 1=1 " ;
		$sql .= $this->where();
		$sql .= " order by ". $this->arg_order_by_field . " " . $this->arg_order ;
		$sql .= " limit " . $this->arg_per_page . " offset " . $this->offset() ;
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$results = $wpdb->get_results($sql);
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		$xml =  "<?xml version='1.0' encoding='utf-8' ?>";
		$xml .= "<rows>";
		$xml .= "<page>".$this->arg_page_no."</page>";
		$xml .= "<total>".$page_count."</total>";
		$xml .= "<records>". $visits_count."</records>";
		//$xml .= "<col4title>User Agent</col4title>";	
		
		foreach($results as $row){
			$xml .= "<row>";
			$xml .= "<cell>$row->dt</cell>";
			$xml .= "<cell>$row->ip</cell>";
			$xml .= "<cell><![CDATA[$row->http_referer]]></cell>";
			$xml .= "<cell><![CDATA[$row->user_agent]]></cell>";
			$xml .= "<cell><![CDATA[$row->title]]></cell>";			
			$xml .= "</row>";
		}
		$xml .= "</rows>";
		return $xml;		
	}
		

	
	function get_view($type="xml"){		
		
		global $wpdb;
		
		if(empty($this->view_count) || $this->view_count == 0 )
			$view_count = $this->get_view_count();
		else
			$view_count = $this->view_count;
			
		if(empty($this->page_count) || $this->page_count == 0)
			$page_count = $this->get_page_count();
		else 
			$page_count = $this->page_count;
		
		$type ="xml";	// Now this function supports only xml
		
		$sql  = " select " ;
		$sql .= " ip, date_format(vtime,'%d-%b-%Y %H:%i') as dt, http_referer, ifnull(p.post_title,r.resource_title) as title,user_agent " ;
		$sql .= " from " . $wpdb->prefix . "tanalyzer_visits v " ;
		$sql .= " left outer join " . $wpdb->prefix . "posts p on ( v.resource = p.id )  " ;
		$sql .= " left outer join " . $wpdb->prefix . "tanalyzer_resources r on ( v.resource = 	r.resource_id) " ;
		$sql .= " where 1=1 " ;
		$sql .= $this->where();
		$sql .= " order by ". $this->arg_order_by_field . " " . $this->arg_order ;
		$sql .= " limit " . $this->arg_per_page . " offset " . $this->offset() ;
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$results = $wpdb->get_results($sql);
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone

		$xml =  "<?xml version='1.0' encoding='utf-8' ?>";
		$xml .= "<rows>";
		$xml .= "<page>".$this->arg_page_no."</page>";
		$xml .= "<total>".$page_count."</total>";
		$xml .= "<records>". $view_count."</records>";	
		//$xml .= "<col4title>Title</col4title>";
		
		foreach($results as $row){
			$xml .= "<row>";
			$xml .= "<cell>$row->dt</cell>";
			$xml .= "<cell>$row->ip</cell>";
			$xml .= "<cell><![CDATA[$row->http_referer]]></cell>";			
			$xml .= "<cell><![CDATA[$row->user_agent]]></cell>";
			$xml .= "<cell><![CDATA[$row->title]]></cell>";
			$xml .= "</row>";
		}
		$xml .= "</rows>";
		return $xml;		
	}
}