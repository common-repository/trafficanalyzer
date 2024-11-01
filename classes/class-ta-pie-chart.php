<?php

	/**
	 * @author George Mathew K <georgemathewk@yahoo.com>
	 * @since 1.8.0	 
	 * @package trafficanalyzer
	 * @license GPL V2 
	 */

	class TA_Pie_Chart {
		private $resource;
		private $chart;		
		private $resource_labels=array();
		private $pie_values=array();
		private $pie;
		private $other_total=0;
		private $screen="";
		
		/**
		 * 
		 * Array of solid_dot objects
		 * @var array solid_dot objects
		 */
		private $dot;
		
		
		
		/**
		 * Constructor function 
		 * @since 1.8.0
		 * @param TA_Resource or TA_Visits_Resource $resource
		 */
		public function __construct($resource,TA_Period $period,$screen="views"){
			$this->resource = $resource;
			$this->screen = $screen;
			//$this->chart = new open_flash_chart();
			//$this->set_pie_values($period);
			//$this->set_title($period);
			//$this->pie = new pie();
			//$this->pie->set_values($this->pie_values);
			//$this->chart->add_element($this->pie);
		}
		
		
		/**
		 * Prints XML data to be used in Flex Line Chart
		 * @since 1.9.0
		 * @param TA_Period $period
		 */
		public function getXMLData(TA_Period $period){
			
			$xml 	= "<views>";
			$xml	.= "<header>";
			$xml	.= "<columns>";
			$res_data = $this->resource->get_resource();
			
			// Getting all articles
			$id = array();
			foreach( $res_data as $row){
				if( !isset($row['id']) || empty($row['id']) || $row['id']==""  )
					continue;
				else
					$id["".$row['id'].""] = $row["title"];
			}
			
			foreach($id as $key=>$value){
				//	$xml .= "<col title='".$value."'>id".$key."</col>"; // Commented Since 2.3.0
				$xml .= "<col id='".$key."'><![CDATA[".$value."]]></col>";
			}
			
			$xml	.= "</columns>";
			$xml	.= "<title>".$this->get_title($period)."</title>";
			$xml	.= "<type>pie</type>";		
			$xml	.= "<aoid>".get_option("ta_aoid")."</aoid>";	
			$xml	.= "<screen>".$this->screen."</screen>";
			$xml	.= "</header>";
			
			foreach($res_data as $row){
				if($row['id']=="")
					continue;
				$xml .= "<data id='".$row['id']."'><period>".$row['period']."</period>";
				$xml .= "<sdate>".$period->get_start_date()."</sdate>";
				$xml .= "<edate>".$period->get_end_date()."</edate>";			
				
				$xml .= "<title_grid><![CDATA[".$row['title'] ;
				if($this->screen=="views")
					$xml .= " :"; 
				$xml .= " " .$this->get_grid_title($period)."]]></title_grid>";
				
				$xml .= "<cnt>".$row['count']."</cnt>";
				$xml .= "</data>";
			}
			$xml .= "</views>";
			
			return $xml;
		}
		
		
	/**
		 * 
		 * Returns title to this chart
		 * @param TA_Period $period
		 * @since 1.9.0
		 * @return string String containing the title
		 */
		public function get_title(TA_Period $period){
			$title_text="";
			switch($period->get_periodicity()){
				case 'd':
					$from = date('d-M-Y',strtotime($period->get_start_date()));
					$to = date('d-M-Y',strtotime($period->get_end_date()));
					if($period->get_start_date()==$period->get_end_date()){
						if($this->screen=="views")
							$title_text = "Views on ". $from;
						else if($this->screen=="visits")
							$title_text = "Visits vs Views on ". $from;
					}else{
						if($this->screen=="views")
							$title_text = "Views from ". $from . " to " . $to;
						else if($this->screen=="visits")
							$title_text = "Visits vs Views from ". $from . " to " . $to;
					}
					break;
				case 'w':
					$from = date('\W\e\e\k\-W\(Y\)',strtotime($period->get_start_date()));
					$to = date('\W\e\e\k\-W\(o\)',strtotime($period->get_end_date()));
					if($from == $to){
						if($this->screen=="views")
							$title_text = "Views on " . $from;
						else if($this->screen=="visits")
							$title_text = "Visits vs Views on " . $from;
					}else{
						if($this->screen=="views")
							$title_text = "Views from ". $from  . " to " . $to;
						else if($this->screen=="visits")
							$title_text = "Visits vs Views from ". $from  . " to " . $to;
					}
					break;	
				case 'm':
					$from = date('M-Y',strtotime($period->get_start_date()));
					$to = date('M-Y',strtotime($period->get_end_date()));
					if($from == $to){
						if($this->screen=="views")
							$title_text = "Views on " . $from;
						else if($this->screen=="visits")
							$title_text = "Visits vs Views on " . $from;
					}else{
						if($this->screen=="views")
							$title_text = "Views from ". $from . " to " . $to ;
						else if($this->screen=="visits")
							$title_text = "Visits vs Views from ". $from . " to " . $to ;
					}
					break;	
				case 'a':
					$from = date('d-M-Y',strtotime($period->get_start_date()));
					$to = date('d-M-Y',strtotime($period->get_end_date()));
					if($period->get_start_date()==$period->get_end_date()){
						if($this->screen=="views")
							$title_text = "Views on ". $from;
						else if($this->screen=="visits")
							$title_text = "Visits vs Views on ". $from;
					}else{
						if($this->screen=="views")					
							$title_text = "Views from ". $from . " to " . $to;
						else if($this->screen=="visits") 
							$title_text = "Visits vs Views from ". $from . " to " . $to;
					}
					
					break;
							
					
			}
			return $title_text;
			
		}
		
		
		

		/**
		 * 
		 * Returns title to this chart
		 * @param TA_Period $period
		 * @since 2.0.0
		 * @return string String containing the title
		 */
		public function get_grid_title(TA_Period $period){
			$title_text="";
			switch($period->get_periodicity()){
				case 'd':
					$from = date('d-M-Y',strtotime($period->get_start_date()));
					$to = date('d-M-Y',strtotime($period->get_end_date()));
					if($period->get_start_date()==$period->get_end_date()){
						if($this->screen=="views")
							$title_text = "Views on ". $from;
						else if($this->screen=="visits")
							$title_text = "on ". $from;
					}else{
						if($this->screen=="views")
							$title_text = "Views from ". $from . " to " . $to;
						else if($this->screen=="visits")
							$title_text = "from ". $from . " to " . $to;
					}
					break;
				case 'w':
					$from = date('\W\e\e\k\-W\(Y\)',strtotime($period->get_start_date()));
					$to = date('\W\e\e\k\-W\(o\)',strtotime($period->get_end_date()));
					if($from == $to){
						if($this->screen=="views")
							$title_text = "Views on " . $from;
						else if($this->screen=="visits")
							$title_text = "on " . $from;
					}else{
						if($this->screen=="views")
							$title_text = "Views from ". $from  . " to " . $to;
						else if($this->screen=="visits")
							$title_text = "from ". $from  . " to " . $to;
					}
					break;	
				case 'm':
					$from = date('M-Y',strtotime($period->get_start_date()));
					$to = date('M-Y',strtotime($period->get_end_date()));
					if($from == $to){
						if($this->screen=="views")
							$title_text = "Views on " . $from;
						else if($this->screen=="visits")
							$title_text = "on " . $from;
					}else{
						if($this->screen=="views")
							$title_text = "Views from ". $from . " to " . $to ;
						else if($this->screen=="visits")
							$title_text = "from ". $from . " to " . $to ;
					}
					break;	
				case 'a':
					$from = date('d-M-Y',strtotime($period->get_start_date()));
					$to = date('d-M-Y',strtotime($period->get_end_date()));
					if($period->get_start_date()==$period->get_end_date()){
						if($this->screen=="views")
							$title_text = "Views on ". $from;
						else if($this->screen=="visits")
							$title_text = "on ". $from;
					}else{
						if($this->screen=="views")					
							$title_text = "Views from ". $from . " to " . $to;
						else if($this->screen=="visits") 
							$title_text = "from ". $from . " to " . $to;
					}
					
					break;
							
					
			}
			return $title_text;
			
		}
			
		
		
		
		
		
		
		
		/**
		 * 
		 * Sets the pievalues
		 * @since 1.8.0
		 */
		public function set_pie_values(TA_Period $period){
			$cur_total=0;						
			$res = $this->resource;
			$res_data = $res->get_resource();
			$pie_bounce = new pie_bounce(10);
			$total = intval($res->get_total());	
			foreach($res_data as $row) {
				if($row['title']!="") {										
				
					$pie_value = new pie_value(intval($row['count']),$row['title']);
					$pie_value->set_colour(ta_get_color(intval($row['id'])));
					$pie_value->set_tooltip($row['count']."/".$total. "(".ta_get_percentage($row['count'], $total).")");
					$pie_value->add_animation($pie_bounce);
					
					/* Grid Data */							
						$grid_data["key"] = $row['id'];
						$grid_data["article"] = $row['title'];
						$grid_data['sdate'] = $period->get_start_date();
	                    $grid_data['edate'] = $period->get_end_date();
	                    if($grid_data['sdate'] == $grid_data['edate'])
	                    	$grid_data['period_title'] = "Views on ". date('d-M-Y',strtotime($grid_data['sdate']));
	                    else 
	                    	$grid_data['period_title'] = "Views from ". date('d-M-Y',strtotime($grid_data['sdate'])) . " to ". date('d-M-Y',strtotime($grid_data['edate']));
						$data_json = json_encode($grid_data);
						$pie_value->on_click("show_dialog($data_json)");					
					/* Grid Data */
					
					
					$this->pie_values[] = $pie_value;
					$cur_total += $row['count'];
													
				}
			}	
			
			$all_total = $res->get_total();	
			if($all_total-$cur_total>0){
				$pie_value = new pie_value(intval($all_total-$cur_total),"Other");
				$pie_value->set_colour('#0000ff');
				$pie_value->set_tooltip(intval($all_total-$cur_total)."/".$total. "(".ta_get_percentage(intval($all_total-$cur_total), $total).")");
				$pie_value->add_animation($pie_bounce);
				$this->pie_values[] = $pie_value;
			}
		}	
		
		
		/**
		 * 
		 * Draws the chart
		 * @since 1.8.0
		 */
		public function draw(){
			echo $this->chart->toString();
		}	

		/**
		 * 
		 * Sets a title to this chart
		 * @since 1.8.0
		 */
		public function set_title(TA_Period $period){
			$title_text="";
			switch($period->get_periodicity()){
				case 'd':
					if($period->get_start_date()==$period->get_end_date())
						if($this->screen=="views")
							$title_text = "Views on ". date('d-M-Y',strtotime($period->get_start_date()));
						else if($this->screen=="visits")
							$title_text = "Visits vs Views on ". date('d-M-Y',strtotime($period->get_start_date()));
					else
						if($this->screen=="views")
							$title_text = "Views from ". date('d-M-Y',strtotime($period->get_start_date())) . " to " . date('d-M-Y',strtotime($period->get_end_date()));
						else if($this->screen=="visits")
							$title_text = "Visits vs Views from ". date('d-M-Y',strtotime($period->get_start_date())) . " to " . date('d-M-Y',strtotime($period->get_end_date()));
					break;
				case 'w':
					if(date('W-Y',strtotime($period->get_start_date()))==date('W-Y',strtotime($period->get_end_date())))
						if($this->screen=="views")
							$title_text = "Views on ".date('\W\e\e\k\-W\(Y\)',strtotime($period->get_start_date()));
						else if($this->screen=="visits")
							$title_text = "Visits vs Views on ".date('\W\e\e\k\-W\(Y\)',strtotime($period->get_start_date()));
					else
						if($this->screen=="views")
							$title_text = "Views from ". date('\W\e\e\k\-W\(Y\)',strtotime($period->get_start_date())) . " to " . date('\W\e\e\k\-W\(o\)',strtotime($period->get_end_date()));
						else if($this->screen=="visits")
								$title_text = "Visits vs Views from ". date('\W\e\e\k\-W\(Y\)',strtotime($period->get_start_date())) . " to " . date('\W\e\e\k\-W\(o\)',strtotime($period->get_end_date()));
					break;	
				case 'm':
					if(date('M-Y',strtotime($period->get_start_date()))==date('M-Y',strtotime($period->get_end_date())))
						if($this->screen=="views")
							$title_text = "Views on ".date('M-Y',strtotime($period->get_start_date()));
						else if($this->screen=="visits")
							$title_text = "Visits vs Views on ".date('M-Y',strtotime($period->get_start_date()));
					else
						if($this->screen=="views")
							$title_text = "Views from ". date('M-Y',strtotime($period->get_start_date())) . " to " . date('M-Y',strtotime($period->get_end_date())) ;
						else if($this->screen=="visits")
							$title_text = "Visits vs Views from ". date('M-Y',strtotime($period->get_start_date())) . " to " . date('M-Y',strtotime($period->get_end_date())) ;
					break;	
				default : 
					if($period->get_start_date()==$period->get_end_date())
						if($this->screen=="views")
							$title_text = "Views on ". date('d-M-Y',strtotime($period->get_start_date()));
						else if($this->screen=="visits")
							$title_text = "Visits vs Views on ". date('d-M-Y',strtotime($period->get_start_date()));
					else
						if($this->screen=="views")
							$title_text = "Views from ". date('d-M-Y',strtotime($period->get_start_date())) . " to " . date('d-M-Y',strtotime($period->get_end_date()));
						else if($this->screen=="visits")
							$title_text = "Visits vs Views from ". date('d-M-Y',strtotime($period->get_start_date())) . " to " . date('d-M-Y',strtotime($period->get_end_date()));
					break;					
			}
			
			$title_style = "font-size:20px;padding:20px;width:400px";
			$title = new title($title_text);
			$title->set_style($title_style);
			$this->chart->set_title($title);			

			
		}	
		
		
	}