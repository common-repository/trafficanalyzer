<?php
/**
 * @author George Mathew K <georgemathewk@yahoo.com>
 * @since 2.0.0
 * @package trafficanalyzer
 * @license GPL V2
 */

class TA_Visits_Resource {

	/**
	 * eg: array(
	 * 			 array (
	 * 					"id"=>"12",
	 * 					"type"=>"post",
	 * 					"title"=>"Traffic Analyzer",
	 * 					"count"=>23,
	 * 					"period"=>"12-Oct-2011",
	 * 					"period_ts"=>12344333
	 * 					)
	 * 			)
	 * @var array
	 */
	private $resources=array();


	private $total=array();

	/**
	 * Constructor function
	 * If array id is not empty, then the property $resources will be populated with that ids
	 * @param int $num Requested number of resources
	 * @param string $order Requested is top or bottom. For top $order is desc and for bottom it is asc
	 * @param TA_Period $period	Views in this period only
	 * @param Array array of specific ids
	 */
	public function __construct($num=0,$order="desc",TA_Period $period,$id=array(),$screen="views"){
		global $wpdb;
			
		if($num>0)
		$limit = " limit $num ";
		else
		$limit = "";

		$where_cond_fields = $this->get_required_resource_id($num, $order, $period);
		if(!empty($where_cond_fields))
		$where_cond = " and visits.resource in ( " . $where_cond_fields . " ) ";
		else
		$where_cond = " ";


		$where_cond .= $this->where_cond_date_range($period);
			
		$sql = 	" select ". $this->get_select_fields($period) .
						" from ".$wpdb->prefix."tanalyzer_visits visits left outer join ".$wpdb->prefix."posts  posts on ( visits.resource = posts.id) " .
						" left outer join ".$wpdb->prefix."tanalyzer_resources resources on ( visits.resource = resources.resource_id)  " .
						" where 1=1  " .
						" $where_cond " .
						" " . ta_user_agent_where() . " " .
						" " . $this->group_by_clause() ." " .			

						" order by cnt $order " ;
			
			
		if(!empty($id)) {
			$id_string = implode(",",$id);
			$sql = 	" select ". $this->get_select_fields($period) .
						" from ".$wpdb->prefix."tanalyzer_visits visits left outer join ".$wpdb->prefix."posts  posts on ( visits.resource = posts.id) " .
						" left outer join ".$wpdb->prefix."tanalyzer_resources resources on ( visits.resource = resources.resource_id)  " .
						" where visits.resource in ( $id_string ) " .
						" $where_cond " .
						" " . ta_user_agent_where() . " " .	
						" " . $this->group_by_clause() ." " .				
						" order by cnt $order " ;					
		}
			
		//echo $sql;
			
		$date_start_obj = new DateTime($period->get_start_date());
		$date_end_obj = new DateTime($period->get_end_date());
			
		//$interval = new DateInterval("P1D");
		
		$date_start_ts = strtotime($period->get_start_date());
		$date_end_ts = strtotime($period->get_end_date());
		
		$interval_ts  = 24*60*60;
			
			
		switch($period->get_periodicity()){
			case 'w':
				//$interval = new DateInterval("P7D");
				$interval_ts  = 6 * 24 * 60 * 60;
				break;
			case 'm':
				//$interval = new DateInterval("P1M");
				$interval_ts = ( date('t',$date_start_ts) -1 ) * 24 * 60 * 60 ;
				break;
		}
			
			
		//for(;$date_start_obj<$date_end_obj ; $date_start_obj = $date_start_obj->add($interval)){
		for(; $date_start_ts <$date_end_ts ; $date_start_ts = $date_start_ts + $interval_ts ) {
			switch($period->get_periodicity()){
				case 'd' :
					//$period_format = $date_start_obj->format("d-M-Y");
					$period_format = date("d-M-Y",$date_start_ts);
					break;
				case 'w':
					//$period_format = $date_start_obj->format("W-Y");
					$period_format = date("W-Y",$date_start_ts);
					
					break;
				case 'm':
					//$period_format = $date_start_obj->format("M-Y");
					$period_format = date("M-Y",$date_start_ts);
					break;
			}
				
			$this->resources[]=array(
										'id'=>'',
										'type'=>'',
										'title'=>'',
										'count'=>'',
										'period'=>$period_format,
										'period_ts'=>strtotime(ta_get_first_day_from_label($period_format,$period->get_periodicity()))					
			);
		}
			
		
		
		
		// Views
		$sql = $this->get_views_sql($period);
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$result = $wpdb->get_results($sql);		
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		
		
		
			
		foreach($result as $row){
			$this->resources[]=array(
										'id'=>$row->resource_id,
										'type'=>$row->type,
										'title'=>$row->resource_title,
										'count'=>$row->cnt,
										'period'=>$row->period,
										'period_ts'=>strtotime(ta_get_first_day_from_label($row->period,$period->get_periodicity()))
			);
		}
		
		// Visits
		$sql = $this->get_visits_sql($period);
		
		//echo $sql;
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$result = $wpdb->get_results($sql);		
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
		
		
		
			
		foreach($result as $row){
			$this->resources[]=array(
										'id'=>$row->resource_id,
										'type'=>$row->type,
										'title'=>$row->resource_title,
										'count'=>$row->cnt,
										'period'=>$row->period,
										'period_ts'=>strtotime(ta_get_first_day_from_label($row->period,$period->get_periodicity()))
			);
		}
		
		
		
		

		/*
		 * Sort $this->resources on period_ts , count
		 */

		$period_ts = array();
		$count = array();
			
		foreach($this->resources as $key=>$row){
			$period_ts[$key] = $row['period_ts'];
			$count[$key] = $row['count'];
		}

			
		if($order="asc"){
			if(is_array($this->resources))
			array_multisort($period_ts,SORT_ASC,$count,SORT_ASC,$this->resources);
		}else{
			if(is_array($this->resources))
			array_multisort($period_ts,SORT_ASC,$count,SORT_DESC,$this->resources);
		}
			
		/*
		 * Finding total view of the argument period(date,week, or month)
		 */
		$this->find_total($period);
			
	}


	public function get_required_resource_id($num,$order,$period){
		global $wpdb;
		$id_string = "";

		if($num==0)
		$limit = " ";
		else
		$limit = " limit $num ";

		$sql = " select visits.resource res_id" ;
		$sql .= " from $wpdb->prefix"."tanalyzer_visits visits ";
		$sql .= " left outer join $wpdb->prefix"."posts posts ";
		$sql .= " on ( visits.resource = posts.id )  ";
		$sql .= " left outer join $wpdb->prefix"."tanalyzer_resources resources ";
		$sql .= " on ( visits.resource = resources.resource_id ) ";
		$sql .= " where date(vtime) between '".$period->get_start_date()."' and '". $period->get_end_date()."'";
		$sql .=  ta_user_agent_where();
		$sql .= " group by res_id ";
		$sql .= " order by count(*) $order";
		$sql .= " $limit " ;
			
		//echo $sql;
			
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$result = $wpdb->get_results($sql);		
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
			
		foreach($result as $row){
			$id[] = $row->res_id;
		}
		if(is_array($id))
		$id_string = implode(",",$id);
			
		return $id_string;
			
			
	}


	/**
	 * @since 2.0.0
	 */
	public function get_visits_sql($period){
		global $wpdb;
		$where_cond = $this->where_cond_date_range($period);
		$sql = 	" select period, 'Visits' as resource_title, '-1' as resource_id, 'none' as type, count(*) as cnt from  ( select ". $this->get_select_fields_visits($period) .
					" from ".$wpdb->prefix."tanalyzer_visits visits " .
					" where 1=1  " .
					" $where_cond " .				
					" " . ta_user_agent_where() . " " .
					" group by period, ip,user_agent, wpta_cookie " .
					" ) as t group by period  "	;
		//echo $sql;		
		return $sql;
	}
		
	
	/**
	 * @since 2.0.0
	 */
	public function get_views_sql($period){
		global $wpdb;
		$where_cond = $this->where_cond_date_range($period);
		
		$sql = 	" select period, 'Views' as resource_title, '-2' as resource_id, 'none' as type, sum(cnt) as cnt from  ( select ". $this->get_select_fields_visits($period) .
					" from ".$wpdb->prefix."tanalyzer_visits visits " .
					" where 1=1  " .		
					" $where_cond " .		
					" " . ta_user_agent_where() . " " .
					" group by period, ip,user_agent, wpta_cookie " .
					" ) as t group by period  "	;		
		return $sql;
	}
	


	/**
	 *
	 * @since 1.8.0
	 * @param TA_Period $period
	 * @return string select fields in comma separated string
	 */
	public  function get_select_fields(TA_Period $period){
		$fields="";
		switch($period->get_periodicity()){
			case 'a':
				$fields  = " '' as period, " ;					// Period
				$fields .= " case  when posts.post_title = '' then resources.resource_title when posts.post_title is null then resources.resource_title else posts.post_title end  resource_title ," ;	// Resource Title
				$fields .= " case  when posts.post_title = '' then resources.resource_id when posts.post_title is null then resources.resource_id else posts.id end  resource_id ," ;	// Resource ID
				$fields .= " visits.resource_type as type, " ;	// Resource Type
				$fields .= "  count(visits.resource) cnt " ;	// Resource Count
				break;
			case 'd':
				$fields  = " date_format(vtime,'%d-%b-%Y') as period, " ;		// Period
				$fields .= " case  when posts.post_title = '' then resources.resource_title when posts.post_title is null then resources.resource_title else posts.post_title end resource_title ," ;	// Resource Title
				$fields .= " case  when posts.post_title = '' then resources.resource_id when posts.post_title is null then resources.resource_id else posts.id end resource_id ," ;	// Resource ID
				$fields .= " visits.resource_type as type, " ;	// Resource Type
				$fields .= "  count(visits.resource) cnt " ;	// Resource Count
				break;
			case 'w':
				$fields  = " date_format(vtime,'%u-%Y') as period, " ;		// Period
				$fields .= " case  when posts.post_title = '' then resources.resource_title when posts.post_title is null then resources.resource_title else posts.post_title end resource_title," ;	// Resource Title
				$fields .= " case when posts.post_title  = '' then resources.resource_id when posts.post_title  is null then resources.resource_id else posts.id end resource_id," ;	// Resource ID
				$fields .= " visits.resource_type as type, " ;	// Resource Type
				$fields .= "  count(visits.resource) cnt " ;	// Resource Count
				break;
			case 'm':
				$fields  = " date_format(vtime,'%b-%Y') as period, " ;	// Period
				$fields .= " case  when posts.post_title = '' then resources.resource_title when posts.post_title is null then resources.resource_title else posts.post_title end resource_title," ;	// Resource Title
				$fields .= " case when posts.post_title  = '' then resources.resource_id when posts.post_title  is null then resources.resource_id else posts.id end resource_id," ;	// Resource ID
				$fields .= " visits.resource_type as type, " ;	// Resource Type
				$fields .= "  count(visits.resource) cnt " ;	// Resource Count
				break;
		}
		return $fields;
	}


	/**
	 *
	 * @since 2.0.0
	 * @param TA_Period $period
	 * @return string select fields in comma separated string
	 */
	public  function get_select_fields_visits(TA_Period $period){
		$fields="";
		switch($period->get_periodicity()){
			case 'd' :
				$fields  = " date_format(vtime,'%d-%b-%Y') as period, " ;		// Period
				$fields .= " 'Visits'  resource_title ," ;	// Resource Title
				$fields .= " '-1'  resource_id ," ;	// Resource ID
				$fields .= " 'none' as type, " ;	// Resource Type
				$fields .= "  count(*) cnt " ;	// Resource Count
				break;
			case 'w':
				$fields  = " date_format(vtime,'%u-%Y') as period, " ;		// Period
				$fields .= " 'Visits'  resource_title ," ;	// Resource Title
				$fields .= " '-1'  resource_id ," ;	// Resource ID
				$fields .= " 'none' as type, " ;	// Resource Type
				$fields .= "  count(*) cnt " ;	// Resource Count
				break;
			case 'm':
				$fields  = " date_format(vtime,'%b-%Y') as period, " ;	// Period
				$fields .= " 'Visits'  resource_title ," ;	// Resource Title
				$fields .= " '-1'  resource_id ," ;	// Resource ID
				$fields .= " 'none' as type, " ;	// Resource Type
				$fields .= "  count(*) cnt " ;	// Resource Count
				break;
					
			case 'a' :
				$fields  = " '' as period, " ;		// Period
				$fields .= " 'Visits'  resource_title ," ;	// Resource Title
				$fields .= " '-1'  resource_id ," ;	// Resource ID
				$fields .= " 'none' as type, " ;	// Resource Type
				$fields .= "  count(*) cnt " ;	// Resource Count
				break;
					
		}
		return $fields;
	}




	/**
	 * Returns the where clause
	 * @since 1.8.0
	 * @param TA_Period $period
	 */
	public  function where_clause(TA_Period $period){
		$where = " and visits.vtime between '".$period->get_start_date()."' and '".$period->get_end_date()."' ";
		return $where;
	}



	/**
	 * Returns the group by clause
	 * @since 1.8.0
	 */
	public function group_by_clause(){
		$group = " group by period,visits.resource" ;
		return $group;
	}

	/**
	 *
	 * Finds the total view for each item in the specified period and stores in the array $total
	 * @param TA_Period $period
	 */
	public function find_total(TA_Period $period){
		global $wpdb;
		$field = "";
		$group_by = "";
		switch($period->get_periodicity()){
			case 'a':
				$field = " '' as period , ";
				break;
			case 'd':
				$field = "date_format(vtime,'%d-%b-%Y') as period ,";
				$group_by = " group by period ";
				break;
			case 'm':
				$field = "date_format(vtime,'%b-%Y') as period , ";
				$group_by = " group by period ";
				break;
			case 'w':
				$field = "date_format(vtime,'%u-%Y') as period , ";
				$group_by = " group by period ";
				break;
		}
			
			
		$sql = " select $field count(*) as cnt ";
		$sql .= " from $wpdb->prefix"."tanalyzer_visits visits " ;
		$sql .= " left outer join $wpdb->prefix"."posts  posts " ;
		$sql .= " on ( visits.resource = posts.id ) " ;
		$sql .= " left outer join $wpdb->prefix"."tanalyzer_resources resources ";
		$sql .= " on ( resources.resource_id = visits.resource ) " ;
		$sql .= " where date(vtime) between '".$period->get_start_date()."' and '". $period->get_end_date() . "'";
		$sql .= ta_user_agent_where();
		$sql .= $group_by;
			
		//echo $sql;
			
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$result = $wpdb->get_results($sql);		
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
		
			
		$this->total=array();
		foreach($result as $row){
			$this->total["'".$row->period."'"]=$row->cnt;
		}
			
	}

	/**
	 *
	 * @return array $this->total
	 * @since 1.8.0
	 */
	public function get_total($key=""){
		if(!empty($key)){
			if(isset($this->total["'".$key."'"]))
			return $this->total["'".$key."'"];
			else
			return 0;
		}else{							// Finds all total
			$total=0;
			foreach($this->total as $value){
				$total .= $value;
			}
			return $total;
		}
			
	}


	/**
	 * Returns the array $resources
	 * @since 1.8.0
	 *
	 */
	public function get_resource(){
		return $this->resources;
	}


	/**
	 * @return boolean true if line chart else pie chart
	 * @since 1.8.0
	 */
	public function is_multi(){
		foreach($this->resources as $row){
			$period["'".$row['period']."'"] = $row['period'];
			if(count($period)>1)
			return true;
		}
		return false;
	}

	/**
	 *
	 * @return boolean true if no data
	 * @since 1.8.0
	 */
	public function is_no_data(){
		foreach($this->resources as $row){
			if($row['title']!="")
			return false;
		}
		return true;
	}


	/**
	 * @param TA_Period $period
	 * @return string Returning conditions for the date range
	 */
	public function where_cond_date_range(TA_Period $period){
		$sdate = $period->get_start_date();
		$edate = $period->get_end_date();
		$where_cond = " and date(vtime) between '".$sdate."' and '".$edate."' ";
		return $where_cond;
	}
}
