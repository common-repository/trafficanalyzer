<?php
class TA_Sql{
	private $_select="";
	private $_from="";
	private $_where="";	
	private $_order_by="";
	private $_limit="";
	
	public function __construct(){
	}
	
	
	public function select($s){
		$this->_select = "select ";
		$this->_select .= implode(",",$s);			
	}
	
	public function from($from){
		global $wpdb;
		$this->_from = " from ";
		$this->_from .= $wpdb->prefix.$from;
	}
	
	public function where($where){
		$this->_where = "where 1=1 " . $where;
	}
	
	public function order_by($order_by,$order){
		if(!empty($order_by))
			$this->_order_by = " order by $order_by $order ";
		else
			$this->_order_by = "";		
	}

	public function limit($limit,$offset){
		if(!empty($limit))
			$this->_limit = "limit $limit offset $offset";
	}
	
	public function get_results(){
		global $wpdb;
		$sql = " $this->_select  $this->_from  $this->_where  $this->_order_by  $this->_limit ";
		
		$local_tzone = cur_tzone();		// Getting current MySQL Time Zone
		set_tzone();					// Overwriting the MySQL Time Zone with WordPress Time Zone
		$results = $wpdb->get_results($sql);
		restore_tzone($local_tzone);	// Restoring MySQL Time Zone
				
		return $results;		 
	}
		
}