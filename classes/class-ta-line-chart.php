<?php

	/**
	 * @author George Mathew K <george@gladsys.in>
	 * @since 1.8.0	 
	 * @package trafficanalyzer
	 * @license GPL V2 
	 */

	class TA_Line_Chart {
		private $resource;
		private $chart;
		private $x_labels=array();
		private $resource_labels=array();
		private $screen = "";
		
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
			//$this->set_x_y_axis($period);
			//$this->set_lines($period);
			//$this->set_title($period);
			
		}
		
		
		/**
		 * Prints XML data to be used in jqPlot
		 * @since 2.3.0
		 * @param TA_Period $period
		 */
		public function getJQPlotData(TA_Period $period){
			return "[  [ '30-Jan-12',7,'test' ], ['31-Jan-12',10,-2], ['01-Feb-12',12,-3] , [ '02-Feb-12',4,-4] ]" ;
		}
		
		
		/**
		 * Prints XML data to be used in Flex Line Chart
		 * @since 1.9.0
		 * @param TA_Period $period
		 * Modified for 2.3.0
		 */
		public function getXMLData(TA_Period $period){
			$xml 	= "<views>";
			$xml	.= "<header>";
			$xml	.= "<columns>";
			$res_data = $this->resource->get_resource();
			
			// For the array $id, key is ID and value is Title
			$id = array();
			foreach( $res_data as $row){
				if( !isset($row['id']) || empty($row['id']) || $row['id']==""  )
					continue;
				else
					$id["".$row['id'].""] = $row["title"];
			}
			
			
						
			foreach($id as $key=>$value){
				//$xml .= "<col title='".$value."'>id".$key."</col>";	Commented Since 2.3.0
				$xml .= "<col id='".$key."'><![CDATA[".$value."]]></col>";
			}
			
			$xml	.= "</columns>";
			$xml	.= "<title>".$this->get_title($period)."</title>";
			$xml	.= "<type>line</type>";
			$xml	.= "<aoid>".get_option("ta_aoid")."</aoid>";
			$xml	.= "<screen>".$this->screen."</screen>";
			$xml	.= "</header>";

			
			// For the two dimensional array $data, key1 is Period, key2 is integers(0,1,2,...) and value is another array with elements (id,count,sdate_ts,edate_ts,title) 
			foreach($res_data as $row){
				$data["".$row['period'].""][] = array(
														"id"=>$row['id'],
														"count"=>$row['count'],
														"sdate_ts"=>$row['period_ts'],
														"edate_ts"=>$row['period_ts'],
														"title"=>$row['title']				
												);					
			}
			
						
			foreach($data as $k=>$d){
				
				$tmp_id = $id;		// Count should be zero for unviewed articles. Filling the array with all the articles
				foreach($d as $v){
					
					if( is_null($v['id']) ||   empty($v['id']) ||  $v['id']=="" )
						continue;
					
					
					$xml .= "<data id='".$v['id']."'><period>".$k."</period>";
					
					if($period->get_periodicity()=='d'){
							$xml .= "<sdate>". date('Y/m/d',$d[0]['sdate_ts'])."</sdate>";
							$xml .= "<edate>". date('Y/m/d',$d[0]['edate_ts'])."</edate>";						
					}else if($period->get_periodicity()=='m'){
						$sdate = date('Y/m/d',strtotime("01"."-".$k));
						$edate = date('Y/m/d',strtotime(date('t', strtotime($sdate))."-".$k));
						$cur_date_ts = mktime(0,0,0);
						if( $cur_date_ts < strtotime($edate) )
							$edate = date('Y/m/d',$cur_date_ts);					
						$xml .= "<sdate>". $sdate ."</sdate>";
						$xml .= "<edate>". $edate ."</edate>";										
					}else if($period->get_periodicity()=="w"){					
						$sdate = ta_week_start_date(intval(substr($k,0,2)), intval(substr($k,3,4)));
						$edate = date('Y/m/d', strtotime('+6 days', strtotime($sdate)));
						$cur_date_ts = mktime(0,0,0);
						if( $cur_date_ts < strtotime($edate) )
							$edate = date('Y/m/d',$cur_date_ts);
						$xml .= "<sdate>". $sdate ."</sdate>";
						$xml .= "<edate>". $edate ."</edate>";
					}
						
						if($this->screen=="views")
							$xml .= "<title_grid><![CDATA[".$v['title']." : Views on ".$k."]]></title_grid>";
						else if($this->screen=="visits")
							$xml .= "<title_grid><![CDATA[".$v['title']." on ".$k."]]></title_grid>";													
						//$xml .= "<id".$v['id'].">".$v['count']."</id".$v['id'].">";	commented since 2.3.0
						$xml .= "<cnt>". $v['count'] ."</cnt>";	// Replaced above line
						unset($tmp_id["".$v['id'].""]);		// Count should be zero for unviewed articles, Removing viewed article from the array
						
						$xml .= "</data>";		
				}
				
				// Count should be zero for unviewed articles. Now $tmp_id will contain unviewed articles
				foreach($tmp_id as $tk=>$tv){
					//$xml .= "<id".$tk.">0</id".$tk.">";	//commented since 2.3.0
						if( is_null($v['id']) ||   empty($v['id']) ||  $v['id']=="" )
							continue;
					
					$xml .= "<data id='".$tk."'><period>".$k."</period>";
					
					if($period->get_periodicity()=='d'){
							$xml .= "<sdate>". date('Y/m/d',$d[0]['sdate_ts'])."</sdate>";
							$xml .= "<edate>". date('Y/m/d',$d[0]['edate_ts'])."</edate>";						
					}else if($period->get_periodicity()=='m'){
						$sdate = date('Y/m/d',strtotime("01"."-".$k));
						$edate = date('Y/m/d',strtotime(date('t', strtotime($sdate))."-".$k));
						$cur_date_ts = mktime(0,0,0);
						if( $cur_date_ts < strtotime($edate) )	
								$edate = date('Y/m/d',$cur_date_ts);				
						$xml .= "<sdate>". $sdate ."</sdate>";
						$xml .= "<edate>". $edate ."</edate>";										
					}else if($period->get_periodicity()=="w"){					
						$sdate = ta_week_start_date(intval(substr($k,0,2)), intval(substr($k,3,4)));
						$edate = date('Y/m/d', strtotime('+6 days', strtotime($sdate)));
						$cur_date_ts = mktime(0,0,0);
						if( $cur_date_ts < strtotime($edate) )	
								$edate = date('Y/m/d',$cur_date_ts);
						$xml .= "<sdate>". $sdate ."</sdate>";
						$xml .= "<edate>". $edate ."</edate>";
					}
						
					
						if($this->screen=="views")
							$xml .= "<title_grid><![CDATA[".$tv." : Views on ".$k."]]></title_grid>";
						else if($this->screen=="visits")
							$xml .= "<title_grid><![CDATA[".$tv." on ".$k."]]></title_grid>";
					
					
					$xml .= "<cnt>0</cnt>";

					$xml .= "</data>";
				}
				
								
			}		
			
			$xml .= "</views>";
			//print  $xml;	commented since 2.3.0, and instead returns the xml data for jqplot 
			return $xml; 	
			
		}
		
		
		
		/**
		 * 
		 * Creates X and Y Axis and their labels
		 * @since 1.8.0
		 */
		public function set_x_y_axis(TA_Period $period){			
			$labels=array();
			$y_min=0;
			$y_max=0;
			$no_steps=10;
			
			$no_x_labels_limit = 10;	// Sets maximum number of X-Axis labels
			$no_x_labels = 0;
			$step_x=0;
			
			$res = $this->resource;
			$res_data = $res->get_resource();
			
			foreach($res_data as $row) {
				
				/*
				 * Generating All X-Axis labels
				 */
				$this->x_labels["'".$row['period']."'"] = $row['period'];
				
				/*
				 * Determining highest count value for setting y-axis range
				 */
				if($y_max<$row['count']){
					$y_max = $row['count'];
				}				
				
				/*
				 * Creating Dot values corresponding to X-Axis label and Resource Title
				 */
				if($row['title']!="") {										
					$this->resource_labels[$row['id']] = $row['title'];					
					$this->dot["'".$row['period']."'"]["'".$row['title']."'"] = new solid_dot((int)$row['count']);
					
					/* Grid Data */
					$grid_data["key"] = $row['id'];	// post_id
					$grid_data["article"] = $row['title'];		// post_title
					
					if($period->get_periodicity()=='d' ){
                    	$grid_data['sdate'] = date('Y/m/d',$row['period_ts']);
                        $grid_data['edate'] = date('Y/m/d',$row['period_ts']);
                        $grid_data['period_title'] = "Views on ". $row['period'];
                    }else if($period->get_periodicity()=='w') {
		                $grid_data['sdate'] = ta_week_start_date(intval(substr($row['period'],0,2)), intval(substr($row['period'],3,4)));                                         
                        $grid_data['edate'] = date('Y/m/d', strtotime('+6 days', strtotime($grid_data['sdate'])));
                        $grid_data['period_title'] = "Views on week ".$row['period']." (".date('d-M-Y',strtotime($grid_data['sdate']))." - ". date('d-M-Y',strtotime($grid_data['edate'])). ")";                                                                                                
                    }else if($period->get_periodicity() == 'm') {
                    	$grid_data['sdate'] = date('Y/m/d',strtotime("01"."-".$row['period'])) ;// Hyphen(1) is the separator                                           
                    	$grid_data['edate'] = date('Y/m/d',strtotime(date('t', strtotime($grid_data['sdate']))."-".$row['period'])) ;
						$grid_data['period_title'] = "Views on ".$row['period'];
                    }
											
					$data_json = json_encode($grid_data);
					$this->dot["'".$row['period']."'"]["'".$row['title']."'"]->on_click("show_dialog($data_json)");					
					/* Grid Data */
				}								
			}
			
			/*
			 * X-Axis Step
			 */
			$no_x_labels = count($this->x_labels);
			$step_x = intval($no_x_labels / $no_x_labels_limit ) ;		
			$i=1;
			
			/*
			 * X-Axis labels to be displayed
			 */
			foreach($this->x_labels as $lbl){
				if($step_x!=0){
					if($i%$step_x==0)
						$x_lbls[] = $lbl;
					else
						$x_lbls[] = "";				
					$i++;
				}else {
					$x_lbls[] = $lbl;
				}
				
			}
			
			/*
			 * Y-Axis Step
			 */
			$step = intval($y_max / $no_steps) ; 
			
			$x_axis_labels = new x_axis_labels();
			$x_axis_labels->set_labels($x_lbls);
			$x_axis_labels->rotate(320);
			
			$x_axis = new x_axis();
			$y_axis = new y_axis();
			
			$x_axis->set_labels($x_axis_labels);
			$y_axis->set_range($y_min, $y_max, $step);			

			$this->chart->set_x_axis($x_axis);
			$this->chart->set_y_axis($y_axis) ;
		}
		
		
		
		/**
		 * 
		 * Generating the Lines corresponding to all resources
		 * @since 1.8.0
		 */
		public function set_lines(TA_Period $period){			
			$data = array();
			/*
			 * Iterates through all the resources 
			 */
			foreach($this->resource_labels as $id=>$r_label){
				reset($this->x_labels);
				/*
				 * Iterates through all the X-Axis labels
				 */
				foreach($this->x_labels as $x) {

					
					$tip = "(#key#)<br>";															// Tooltip Line1
					if($period->is_weekly()){
						$tip .= "Week Start Date :" . date('d-M-Y',strtotime(ta_week_start_date(intval(substr($x,0,2)),intval(substr($x,3,4))))) . "<br>" ;
						$tip .= "Week End Date :" . date('d-M-Y',strtotime("+6 days",strtotime(ta_week_start_date(intval(substr($x,0,2)),intval(substr($x,3,4)))))) . "<br>" ; 
					}
					$tip .= "View on:  ".$x ."<br>" ;												// Tooltip Line2
					$tip .= "View Count: #val#/".$this->resource->get_total($x)."<br>";				// Tooltip Line3
					$tip .= "Percentage: ".ta_get_percentage($this->dot["'".$x."'"]["'".$r_label."'"]->value, $this->resource->get_total($x));  // Tooltip Line4
					
					/*
					 * Retrieving all the Dot objects created in set_x_y_axis()
					 */
					if(isset($this->dot["'".$x."'"]["'".$r_label."'"])){	
						$dot = 	$this->dot["'".$x."'"]["'".$r_label."'"];
						$dot->tooltip($tip);		
						$dot->size(3);			
						$data[] = $dot;
					}else{
						$dot = new solid_dot(0);
						$dot->size(3);
						$dot->tooltip($tip);
						$data[] = $dot;
					}				
				}
				$line = new line();				
				$line->set_colour(ta_get_color($id));
				$line->set_values($data);
				$line->set_text("$r_label");
				//$line->set_width(3);
				$data = array();
				$this->chart->add_element($line);
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
					$title_text = "Views from ". date('d-M-Y',strtotime($period->get_start_date())) . " to " . date('d-M-Y',strtotime($period->get_end_date()));
					break;
				case 'w':
					$title_text = "Views from ". date('\W\e\e\k\-W\(Y\)',strtotime($period->get_start_date())) . " to " . date('\W\e\e\k\-W\(o\)',strtotime($period->get_end_date()));
					break;	
				case 'm':
					$title_text = "Views from ". date('M-Y',strtotime($period->get_start_date())) . " to " . date('M-Y',strtotime($period->get_end_date())) ;
					break;			
					
			}
			
			$title_style = "font-size:20px;padding:20px;width:400px";
			$title = new title($title_text);
			$title->set_style($title_style);
			$this->chart->set_title($title);			

			
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
					if($this->screen=="views")
						$title_text = "Views from ". date('d-M-Y',strtotime($period->get_start_date())) . " to " . date('d-M-Y',strtotime($period->get_end_date()));
					else if($this->screen=="visits") 	
						$title_text = "Visits vs Views from ". date('d-M-Y',strtotime($period->get_start_date())) . " to " . date('d-M-Y',strtotime($period->get_end_date()));
					break;
				case 'w':
					if($this->screen=="views")
						$title_text = "Views from ". date('\W\e\e\k\-W\(Y\)',strtotime($period->get_start_date())) . " to " . date('\W\e\e\k\-W\(o\)',strtotime($period->get_end_date()));
					else if($this->screen=="visits")
						$title_text = "Visits vs Views from ". date('\W\e\e\k\-W\(Y\)',strtotime($period->get_start_date())) . " to " . date('\W\e\e\k\-W\(o\)',strtotime($period->get_end_date()));
					break;	
				case 'm':
					if($this->screen=="views")
						$title_text = "Views from ". date('M-Y',strtotime($period->get_start_date())) . " to " . date('M-Y',strtotime($period->get_end_date())) ;
					else if($this->screen=="visits")
						$title_text = "Visits vs Views from ". date('M-Y',strtotime($period->get_start_date())) . " to " . date('M-Y',strtotime($period->get_end_date())) ;	
					break;			
					
			}
			return $title_text;
			
		}
		
		
		
	}