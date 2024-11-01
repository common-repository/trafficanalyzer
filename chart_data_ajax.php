<?php 

		/*
		 * This is needed, beacuse any warning if generated at any php file will make an error in the chart
		 */
		if(function_exists("ini_set")){ 
			ini_set("display_errors", 1);
		}


		if(!function_exists('add_action')){
	    	require_once("../../../wp-config.php");
	    }
	    
	    
	    require_once('../../../wp-load.php');
		require_once('../../../wp-admin/includes/admin.php');
		
		require_once './classes/class-ta-resource.php';
		require_once './classes/class-ta-visits-resource.php';		
		require_once './classes/class-ta-period.php';
		require_once './classes/class-ta-line-chart.php';
		require_once './classes/class-ta-pie-chart.php';
		require_once './classes/class-ta-no-chart.php';
		
		
			
		
		if(isset($_GET['daterangepicker_start']) && !empty($_GET['daterangepicker_start'] )){
			$sdate_get = ta_filter_date($_GET['daterangepicker_start']);		
		}
		
		if(isset($_GET['daterangepicker_end']) && !empty($_GET['daterangepicker_end'] )){
			$edate_get = ta_filter_date($_GET['daterangepicker_end']);	
		}
		
		
		if(empty($sdate_get) && empty($edate_get)){
				$sdate = ta_get_first_view_date();
				$edate = ta_get_last_view_date();	
		}
		
		if(!empty($sdate_get) && empty($edate_get)){
			$sdate = $sdate_get;
			$edate = $sdate_get;
		}
		
		if( !empty($sdate_get) && !empty($edate_get)) {
			$sdate = $sdate_get;
			$edate = $edate_get;
		}
		
		
		
		
		//Filtering $_GET['screen']
		if(isset($_GET['screen']) && !empty($_GET['screen'])){
			switch($_GET['screen']){
				case 'views':
					$ta_screen = 'views';
					break;
				case 'visits':
					$ta_screen  = 'visits';
					break;
				default:
					$ta_screen = 'views';
			}			
		
		}
		
		
		if(isset($_GET['period']) && !empty($_GET['period'])){
			$periodicity = ta_filter_period($_GET['period']);
		}
		
		if(empty($_GET['period'])){
					$periodicity = "a";		// This should be "a"
			}
		
		
		
		
		global $current_user;
		
		if(isset($current_user->ID)){
			
			if(isset($_GET['daterangepicker_start']))
				$daterangepicker_start = $_GET['daterangepicker_start'];
			if(isset($_GET['daterangepicker_end']))
				$daterangepicker_end = $_GET['daterangepicker_end'];
			
				
			//if(isset($_GET['daterangepicker_end'])) {
			//	$daterangepicker_start = $_GET['daterangepicker_start'];
			//if(isset($_GET['daterangepicker_end']))
			//	$daterangepicker_end = $_GET['daterangepicker_end'];
			//}
				
			
			
			if(isset($_GET['titles']))
				$titles = $_GET['titles'];
			else 
				$titles = 3;
				
				
			$opt_daterangepicker_start[$current_user->ID] = $daterangepicker_start;
			$opt_daterangepicker_end[$current_user->ID] = $daterangepicker_end;
			$opt_period[$current_user->ID] = $periodicity;
			$opt_titles[$current_user->ID] = $titles;
			
			update_option('ta_daterangepicker_start', $opt_daterangepicker_start);
			update_option('ta_daterangepicker_end', $opt_daterangepicker_end);
			update_option('ta_period', $opt_period);
			
			if($ta_screen=='views')
				update_option('ta_titles', $opt_titles);
			
		}
		
		
		//hardcoding $sdate and $edate for testing
		//$sdate='2011/10/13';
		//$edate='2011/10/30';
		//$edate='2011/10/25';
		//$periodicity = "d";
		
		$period = new TA_Period($periodicity,$sdate,$edate);
		
		
		$ta_settings = new TrafficAnalyzerSettings();
		$num = $ta_settings->get_num();
		
		$id=array();
		if($num=="c")
			$id = $ta_settings->get_id();
		$order = "desc";
		
		
		
		
			
			
		/*
		 * argument1 : No. of resources to be displayed. 0 for all
		 * argument2 : desc for top to bottom and asc for bottom to top
		 * argument3 : Period object
		 */
		if($ta_screen=='views')				
			$resource = new TA_Resource($num,$order,$period,$id,'views');
		else if($ta_screen=='visits')
			$resource = new TA_Visits_Resource($num,$order,$period,$id,'visits');
		
		if($resource->is_multi()){
			$chart = new TA_Line_Chart($resource,$period,$ta_screen);
		}		
		else
			$chart = new TA_Pie_Chart($resource,$period,$ta_screen);
		
		$data_xml = $chart->getXMLData($period);	
		
		echo $data_xml		
		
		?>
