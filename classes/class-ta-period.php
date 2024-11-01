<?php
	/**
	 * @author George Mathew K <george@gladsys.in>
	 * @since 1.8.0
	 * @package trafficanalyzer
	 * @license GPL V2
	 */


	class TA_Period {
		/**
		 * @var string $periodicity
		 */
		private $periodicity="";
		
		/**
		 * @var string $start_date format : yyyy/mm/dd
		 */
		private $start_date="";
		
		/**
		 * @var string $start_date format : yyyy/mm/dd
		 */		
		private $end_date="";
		
		/** 
		 * Constructor Function
		 * @param string $periodicity
		 * @param string $start_date in yyyy/mm/dd format
		 * @param string $end_date in yyyy/mm/dd format
		 * @since 1.8.0
		 */
		public function __construct($periodicity="a",$start_date="",$end_date=""){
			$this->periodicity = $periodicity;
			$this->start_date = ta_get_first_day($start_date,$periodicity);
			$this->end_date = ta_get_last_day($end_date,$periodicity);	
		}	
		
		/**
		 * 
		 * @return string a=>All,d=>Daily,w=>Weekly,m=>Monthly
		 * @since 1.8.0
		 */
		public function get_periodicity(){
			return $this->periodicity;
		}
		

		/**
		 * 
		 * @return string start_date in yyyy/mm/dd format
		 * @since 1.8.0
		 */
		public  function get_start_date(){
			return $this->start_date;
		}
		
		
		/**
		 * 
		 * @return string end_date in yyyy/mm/dd format
		 * @since 1.8.0
		 */
		public  function get_end_date(){
			return $this->end_date;
		}
		
		/**
		 * 
		 * @return boolean Returns true, if the periodicity is weekly
		 * @since 1.8.0
		 */
		public function is_weekly(){
			if($this->periodicity=='w')
				return true;
			else 
				return false;
		}
		
		
	}
	
	