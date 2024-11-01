<?php 
	
		/*
		 * This file is used for Views chart and Visits vs Views Chart 	
		 */

		/*
		 * This is needed, beacuse any warning if generated at any php file will make an error in the Chart
		 */
		if(function_exists("ini_set")){ 
			ini_set("display_errors", 1);
		}


		if(!function_exists('add_action')){
	    	require_once("../../../../wp-config.php");
	    }
	    
	    
	    require_once('../../../../wp-load.php');
		require_once('../../../../wp-admin/includes/admin.php');
		
		require_once '../classes/class-ta-resource.php';
		require_once '../classes/class-ta-visits-resource.php';
		require_once '../classes/class-ta-period.php';
		require_once '../classes/class-ta-line-chart.php';
		require_once '../classes/class-ta-pie-chart.php';
		require_once '../classes/class-ta-no-chart.php';
		
		
		global $current_user;
		$opt_daterangepicker_start = get_option('ta_daterangepicker_start');
		$opt_daterangepicker_end = get_option('ta_daterangepicker_end');
		$opt_period = get_option('ta_period');
		
		$_GET['daterangepicker_start'] = $opt_daterangepicker_start[$current_user->ID];
		$_GET['daterangepicker_end'] = $opt_daterangepicker_end[$current_user->ID];
		$_GET['period'] = $opt_period[$current_user->ID];
		
		
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
		
		if(isset($_GET['period']) && !empty($_GET['period'])){
			$periodicity = ta_filter_period($_GET['period']);
		}
		
		if(empty($_GET['period'])){
				$periodicity = "a";		// This should be "a"
		}
		
		// hardcoding $sdate and $edate for testing
		//$sdate='2011/10/13';
		//$edate='2011/11/30';
		//$edate='2011/10/25';
		//$periodicity = "d";
		
		$period = new TA_Period($periodicity,$sdate,$edate);
		
		
		$ta_settings = new TrafficAnalyzerSettings();
		$num = $ta_settings->get_num();
		
		$id=array();
		if($num=="c")
			$id = $ta_settings->get_id();
		$order = "desc";
		
		/**
		 * Identifying the requested page
		 */
		
		$ta_screen = $_GET['screen'];
		
		switch($ta_screen){
			case 'toplevel_page_report_traffic-analyzer':
				$ta_screen = 'views';
				break;
			case 'analyzer_page_visits_traffic-analyzer':
				$ta_screen = 'visits';
				break;
		}	
		
		
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
		
		
		
		?>
			var xml_string = "<?php echo $data_xml; ?>";
			var x_lbl = [];
			var start_date = [];
			var end_date = [];
			var article_id = [];
			var grid_title = [];	// Two Dimensional array
			var series_obj = [];
			var data_series=new Array();
			var plot;
			var chart_type;
			var chart_title;
			var plot_options = { };
			var screen="";
			var visit_count = [];
			
		<?php 
		
		

	global $current_user;
	$post_loaded_script_path = plugins_url("/",__FILE__);
	
	$aoid = get_option('ta_aoid');	
	
		
?>

							

		<?php
			 
			$opt_sdate = get_option('ta_daterangepicker_start');
			$opt_edate = get_option('ta_daterangepicker_end');
			$opt_period = get_option('ta_period');
			$opt_titles = get_option("ta_titles");
			
			global $current_user;
		
			if(isset($current_user->ID)){
				
				$init_sdate = $opt_sdate[$current_user->ID];				
				$init_edate = $opt_edate[$current_user->ID];
				$init_period = $opt_period[$current_user->ID];
				$init_title = $opt_titles[$current_user->ID];		
				
			}	
			
		?>
		
		var init_sdate = "<?php echo $init_sdate; ?>";
		var init_edate = "<?php echo $init_edate; ?>";
		var init_period = "<?php echo $init_period; ?>";
		var init_title = "<?php echo $init_title; ?>";
		

jQuery(document).ready(function(){
	// jQuery('#ta_my_chart_title').html(chart_title);	
	init_apply(init_sdate,init_edate,init_period,init_title);

	}
);


function init_plot() {

	// Setting plot_options object
	plot_options = 	{ 
								grid: {
									show:true,
									hoverable:true,
									clickable:true
								},
								series : {
									lines: {
										show:true,
										align:'center'
									},
									pie: {
										show:false,
										label:{
											show:true,
											radius:2/4,
											formatter: function(label, series){
						                        return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;"><br/>'+series.data[0][1]+"<br />"+Math.round(series.percent)+'%</div>';
						                    },
										}			
									},
									bars:{
										show:false									
									},
									points: {
										show:true
									}
								},
											
								
								legend:{
									show:true,
									container:jQuery('#ta_my_chart_legend'),
									labelFormatter:function(str,obj){											
										if(chart_type.text()=='line') { 								
											str = ' <input type="checkbox" checked  value="'+ series_obj.length + '" id="chk_show_' + series_obj.length + '" onclick="show_changed(this)" /><label for="chk_show_' + series_obj.length + '">Show</label> ' + '<span id="span_fill_' + series_obj.length + '"><input type="checkbox"  value="'+ series_obj.length + '" id="chk_fill_' + series_obj.length + '"   onclick="fill_changed(this)"      /><label for="chk_fill_' + series_obj.length + '">Fill</label></span> : <span alt="' + str + '" title="' + str + '"><b>' + (str.length > 15 ? str.substring(0,15)+"..." : str) + '</span></b>';										
											series_obj.push(obj);
											jQuery('#span_fill_all').css('display','inline');
											jQuery('#span_show_all').css('display','inline');
											jQuery('#chart_type').css('display','block');
										}else{
											jQuery('#span_fill_all').css('display','none');
											jQuery('#span_show_all').css('display','none');
											jQuery('#chart_type').css('display','none');
										}
										return str;
									}									
								},
								xaxis : {
									tickFormatter: function(val,axis){
										if(x_lbl[val]==undefined)
											return '';
										else
											return x_lbl[val];
					
									}
							
								}
							};
							
		
		if(jQuery('#ta_my_chart').length>0){
		
		
			if(chart_type.text()=='pie'){
				jQuery('#ta_my_chart').css('border','1px solid #000');		
			}else {
				jQuery('#ta_my_chart').css('border','');
			}
			
			
			if(chart_type.text()=='pie'){
	 			plot_options.series.lines.show=false;
	 			plot_options.series.pie.show=true;
 			}else if(chart_type.text()=='line'){
 				plot_options.series.lines.show=true;
	 			plot_options.series.pie.show=false;
 			}
		
	
		jQuery("#ta_my_chart").bind("plothover", function (event, pos, item) {							
            					if (item){            							
            							jQuery('#tooltip').remove();
            							if(chart_type.text()=='pie')
            								showTooltip(pos.pageX,pos.pageY,item);
            							else
											showTooltip(item.pageX,item.pageY,item);
									
                				}else{
                					jQuery('#tooltip').remove();
                				}
					}
    		);
							
							
							
			jQuery("#ta_my_chart").bind("plotclick", function (event, pos, item) {	
    													
            					if (item) {

            						var key;
            						var sdate;
            						var edate;
            						var grd_title;
            						var period;
            						
            						
            						
            					    key = article_id[item['seriesIndex']];
            					    period = jQuery('#period').val();
            					    
            					    if(chart_type.text()=='pie'){
            					    	sdate = start_date[item['seriesIndex']];
            					    	edate = end_date[item['seriesIndex']];
            					    	grd_title = grid_title[item['seriesIndex']][0];
            					    }else if(chart_type.text()=='line'){
            					    	sdate = start_date[item.datapoint[0]];
            					    	edate = end_date[item.datapoint[0]];            		
            					    	grd_title = grid_title[item['seriesIndex']][item.datapoint[0]];			    
            					    }
            					    
            						showJQGrid(key,sdate,edate,grd_title,period,item['seriesIndex']);

            						
            						
            						var width = jQuery('#grid').jqGrid('getGridParam','width' );
            						
									jQuery('#ta_data').dialog({ 
										closeOnEscape: true,
										title: 'Traffic Analyzer',
										width:width + 10 
										
									} );
									
                				}
			}
			);
			
		}
	



}
 
function showTooltip(x, y, contents) {
		
		var cnt=0;
		var period="";
		var per_visit = 0;
		var per_visit_string = "";
		var per_day = 0;
		var per_day_string = "";
		var per_day_sdate = new Date();
		var per_day_edate = new Date();
		
				
		if(chart_type.text()=='pie'){
			cnt = contents.datapoint[1][0][1];			
			period = '<span style="float:left;width:80px">Start Date:</span>' + get_formatted_date(start_date[contents['seriesIndex']]);
			period += '<br /><span style="float:left;width:80px">End Date:</span>' + get_formatted_date(end_date[contents['seriesIndex']]);
		}
		else if(chart_type.text()=='line'){
			cnt = contents.datapoint[1];
			
			if(jQuery('#period').val()=='d') {
				period = '<span style="float:left;width:80px">Date:</span>' + x_lbl[contents.datapoint[0]];
			}else{
				period = '<span style="float:left;width:80px">Start Date:</span>' + get_formatted_date(start_date[contents.datapoint[0]]);
				period += '<br /><span style="float:left;width:80px">End Date:</span>' + get_formatted_date(end_date[contents.datapoint[0]]);
			}
			
		}
		
		// Per Visit Tooltip
		if(screen.text()=='visits'){
			if(contents.series.label=='Views'){
				if(chart_type.text()=='line'){					
					var num = parseInt(cnt) / parseInt(visit_count[contents.datapoint[0]]) ; 					
					per_visit = new Number( num );
					per_visit = per_visit.toFixed(2);
					per_visit_string = '<br /><span style="float:left;width:80px">Per Visit:</span>' + per_visit;
				}
			}			 
		}
	
		// Per Day Tooltip		
		var yr,mon,dy,days_diff;
		
		if(chart_type.text()=='pie'){
			
			yr = start_date[contents['seriesIndex']].substring(0,4);			
			mon = start_date[contents['seriesIndex']].substring(5,7);			
			dy = start_date[contents['seriesIndex']].substring(8,10);			
			per_day_sdate = new Date(yr,mon-1,dy);
			
			yr = end_date[contents['seriesIndex']].substring(0,4);
			mon = end_date[contents['seriesIndex']].substring(5,7);
			dy = end_date[contents['seriesIndex']].substring(8,10);			
			per_day_edate = new Date(yr,mon-1,dy);
			
		}else {
			
			yr = start_date[contents.datapoint[0]].substring(0,4);
			mon = start_date[contents.datapoint[0]].substring(5,7);
			dy = start_date[contents.datapoint[0]].substring(8,10);			
			per_day_sdate = new Date(yr,mon-1,dy);
			
			yr = end_date[contents.datapoint[0]].substring(0,4);
			mon = end_date[contents.datapoint[0]].substring(5,7);
			dy = end_date[contents.datapoint[0]].substring(8,10);			
			per_day_edate = new Date(yr,mon-1,dy);
		
		}
		
		days_diff = ( per_day_edate - per_day_sdate ) / ( 1000 * 60 * 60 * 24 );		 
		if(screen.text()=='visits' && contents.series.label=='Visits'){		
			if(days_diff > 0){
					per_day = cnt / ( days_diff + 1 ) ;
					per_day = per_day.toFixed(2);
					per_day_string = '<br /><span style="float:left;width:80px">Per Day:</span>' + per_day;
			}
		}			 
				
			
        jQuery('<div id="tooltip"><div align="center"><b>' +  contents.series.label + '</b></div><hr />' + period + ' <br /><span style="float:left;width:80px">Count :</span>' + cnt  +  per_visit_string +   per_day_string + '</div>').css( {
            position: 'absolute',
            top: y + 5,
            left: x + 5,
            border: '1px solid #000',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body");
        
        var total_x_points = start_date.length;
		var current_x_point = contents.datapoint[0];
		
		var width = parseFloat(jQuery('#tooltip').css('width'));
		
		if(current_x_point >= total_x_points/2)
				jQuery('#tooltip').css('left', x-width ) ;
     
}



<?php 
	$current_path = plugins_url("/",__FILE__);
	$required_path = $current_path . "../chart_data_ajax.php";
?>

function init_apply(start_date,end_date,period,titles){
	var start_date = jQuery('#daterangepicker_start').val();
	var end_date = jQuery('#daterangepicker_end').val();
	var period = jQuery('#period').val(); 
	var titles = jQuery('#ta_titles').val();	
	
	var qry_string = "";
	qry_string = "&daterangepicker_start="+start_date;
	qry_string += "&daterangepicker_end="+end_date;
	qry_string += "&period="+period;
	if(titles!='undefined')
		qry_string += "&titles="+ titles;
	qry_string += "&screen=<?php echo $ta_screen; ?>";
	
	
	jQuery.ajax({
 		url:'<?php echo $required_path; ?>?1=1'+qry_string ,
 		context:document.body,
 		success:function(data,textStatus,jqXHR){
 			data_series=[];
 			series_data(data);
 			init_plot();
 			
 			if(chart_type.text()=='pie'){
	 			plot_options.series.lines.show=false;
	 			plot_options.series.pie.show=true;
	 			jQuery('#ta_my_chart').css('border','1px solid #000');		// Draws the border, if pie chart
 			}else if(chart_type.text()=='line'){
 				plot_options.series.lines.show=true;
	 			plot_options.series.pie.show=false;
	 			jQuery('#ta_my_chart').css('border','');					// Removes the border, if line chart
 			}
 			
 			
 			plot = jQuery.plot( jQuery('#ta_my_chart'), data_series, plot_options );
 			jQuery('#ta_my_chart_title').html(chart_title);
 			jQuery('#line_chart').attr('checked','checked');
 			jQuery('#point_chart').attr('checked','checked');
 			jQuery('#bar_chart').attr('checked',false);
 			jQuery('#fill_all').prop('disabled',false);
 			
 			
 		},
 		
 		error:function(jqXHR,textStatus,errorThrown){
 			alert(errorThrown);
 		}
 	
 	});
}






function apply(){
	var start_date = jQuery('#daterangepicker_start').val();
	var end_date = jQuery('#daterangepicker_end').val();
	var period = jQuery('#period').val(); 
	var titles = jQuery('#ta_titles').val();	
	
	var qry_string = "";
	qry_string = "&daterangepicker_start="+start_date;
	qry_string += "&daterangepicker_end="+end_date;
	qry_string += "&period="+period;
	if(titles!='undefined')
		qry_string += "&titles="+ titles;
	qry_string += "&screen=<?php echo $ta_screen; ?>";
	
	
	// Waiting Icon
	jQuery('#ta_my_chart').html('<img src="<?php  echo plugin_dir_url(__FILE__). "../images/wait30.gif" ;  ?>" /> ');
	
	
	jQuery.ajax({
 		url:'<?php echo $required_path; ?>?1=1'+qry_string ,
 		context:document.body,
 		success:function(data,textStatus,jqXHR){
 			data_series=[];
 			series_data(data);
 			
 			if(chart_type.text()=='pie'){
	 			plot_options.series.lines.show=false;
	 			plot_options.series.pie.show=true;
	 			jQuery('#ta_my_chart').css('border','1px solid #000');		// Draws the border, if pie chart
 			}else if(chart_type.text()=='line'){
 				plot_options.series.lines.show=true;
	 			plot_options.series.pie.show=false;
	 			jQuery('#ta_my_chart').css('border','');					// Removes the border, if line chart
 			}
 			
 			
 			plot = jQuery.plot( jQuery('#ta_my_chart'), data_series, plot_options );
 			jQuery('#ta_my_chart_title').html(chart_title);
 			jQuery('#line_chart').attr('checked','checked');
 			jQuery('#point_chart').attr('checked','checked');
 			jQuery('#bar_chart').attr('checked',false);
 			jQuery('#fill_all').prop('disabled',false);
 			
 			
 		},
 		
 		error:function(jqXHR,textStatus,errorThrown){
 			alert(errorThrown);
 		}
 	
 	});
}

function get_formatted_date(str_date){
	var dt = str_date.substr(8,2);
	var m = str_date.substr(5,2);
	var yr = str_date.substr(0,4);
	var mon = "";
	
	switch(parseInt(m)){
		case 1:
			mon = "Jan";
			break;
		case 2:
			mon = "Feb";
			break;
		case 3:
			mon = "Mar";
			break;
		case 4:
			mon = "Apr";
			break;
		case 5:
			mon = "May";
			break;
		case 6:
			mon = "Jun";
			break;
		case 7:
			mon = "Jul";
			break;
		case 8:
			mon = "Aug";
			break;
		case 9:
			mon = "Sep";
			break;
		case 10:
			mon = "Oct";
			break;
		case 11:
			mon = "Nov";
			break;
		case 12:
			mon = "Dec";
			break;	
	}
	
	return dt + "-" + mon + "-" + yr ;

}


function fill_changed(obj){
	series_obj[obj.value].lines.fill=obj.checked;
	if(obj.checked==false)	
		jQuery('#fill_all').prop('checked',false);
		
	if(is_fill_all_checkable())
		jQuery('#fill_all').prop('checked',true);
	plot.draw();	
}



function show_changed(obj){
		if(jQuery('#line_chart').prop('checked')==true)
			series_obj[obj.value].lines.show=obj.checked;
		if(jQuery('#bar_chart').prop('checked')==true)
			series_obj[obj.value].bars.show=obj.checked;		
		if(jQuery('#point_chart').prop('checked')==true)
			series_obj[obj.value].points.show=obj.checked;	

		if(obj.checked==false)	
			jQuery('#show_all').prop('checked',false);
			
		if(is_show_all_checkable())
			jQuery('#show_all').prop('checked',true);
		
			
		plot.draw();
		return true;
}



/**
	* Pass true for show all fill checkboxes
	* Pass false for hide all fill checkboxes
*/
function show_fill(show){
	var i=0;
	for(i=0;i < series_obj.length;i++){
		if(show)
			jQuery('#chk_fill_'+i).prop('disabled',false);
		else
			jQuery('#chk_fill_'+i).prop('disabled','disabled');
	}
}


function show_all(show){
	var i=0;
	for(i=0;i < series_obj.length;i++){
		if(show.checked){
			jQuery('#chk_show_'+i).prop('checked',true);
			jQuery('#chk_show_'+i).trigger('onclick');
		}else{
			jQuery('#chk_show_'+i).prop('checked',false);
			jQuery('#chk_show_'+i).trigger('onclick');
			
		}	
	}
}


function fill_all(show){
	var i=0;
	for(i=0;i < series_obj.length;i++){
		if(show.checked){
			jQuery('#chk_fill_'+i).prop('checked',true);
			jQuery('#chk_fill_'+i).trigger('onclick');
		}else{
			jQuery('#chk_fill_'+i).prop('checked',false);
			jQuery('#chk_fill_'+i).trigger('onclick');
			
		}	
	}
}

function is_fill_all_checkable(){
	var i=0;
	for(i=0;i < series_obj.length;i++){
		if(jQuery('#chk_fill_'+i).prop('checked')==false)
			return false;
	}
	return true;
}


function is_show_all_checkable(){
	var i=0;
	for(i=0;i < series_obj.length;i++){
		if(jQuery('#chk_show_'+i).prop('checked')==false)
			return false;
	}
	return true;
}




function line_chart_changed(obj){
	var i=0;
	for(i=0;i < series_obj.length;i++){
			if(jQuery('#chk_show_'+i).prop('checked'))
				series_obj[i].lines.show = obj.checked;
	}
	
	if(!obj.checked)	{ // Hide all fill checkboxes, if no  line
		show_fill(false);
		jQuery('#fill_all').prop('disabled','disabled');
	}else{
		show_fill(true);
		jQuery('#fill_all').prop('disabled',false);
	}
	plot.draw();
}

function bar_chart_changed(obj){
	var i=0;
	for(i=0;i < series_obj.length;i++){
			if(jQuery('#chk_show_'+i).prop('checked'))
				series_obj[i].bars.show = obj.checked;			
	}
	plot.draw();
}

function point_chart_changed(obj){
	var i=0;
	for(i=0;i < series_obj.length;i++){
			if(jQuery('#chk_show_'+i).prop('checked'))
				series_obj[i].points.show = obj.checked;			
	}
	plot.draw();
}






function series_data(xml_string){
			var xml_doc = jQuery.parseXML(xml_string);
			var xml = jQuery(xml_doc);
			
			chart_title = xml.find('title');			
			var col_title = xml.find('col');			
			chart_type = xml.find('type');
			screen = xml.find('screen');
			
			var i=0,j=0;
			var series = new Array();
			var element = [];
			var y_max=0;
			
			data_series = [];
			start_date = [];
			end_date = [];
			article_id = [];
			grid_title = [];
			visit_count = [] ;
			col_title.each(function(){
					var id = jQuery(this).attr('id');
					var col_title = jQuery(this).text();					
					var data = xml.find('data[id='+id+']');
					
					j = 0;	
					series = [];
					var row_data = [];
					var item = [];
					article_id[i] = id;
					grid_title[i] = [];
					
					data.each(function(){						
						item = [];
						x_lbl[j] = jQuery(this).find('period').text();
						item[0] = j;						
						item[1] =  jQuery(this).find('cnt').text() ;
						
						if(screen.text()=='visits'){
							if(id==-1){			// Only for Views Series Index
									visit_count.push(parseInt(jQuery(this).find('cnt').text()));
								}
							
						}
												
						var grd_title = jQuery(this).find('title_grid').text();
						grid_title[i].push(grd_title);
						
						
						if(chart_type.text()=='pie'){
							start_date[i] = jQuery(this).find('sdate').text();
							end_date[i] = jQuery(this).find('edate').text();						
						}else if(chart_type.text()=='line'){
							start_date[j] = jQuery(this).find('sdate').text();
							end_date[j] = jQuery(this).find('edate').text();						
						}
						
						
						row_data.push(item);
						val = item[1];
						
						j++;				
					}
					);
					
					var obj;
					if(chart_type.text()=='pie') {
						obj = {
							label:col_title,
							data:parseInt(val)
						};					
					}else if(chart_type.text()=='line'){
						obj = {
							label:col_title,
							data:row_data
						};					
					}										
					data_series[i] = obj;
					
					i++;
					
			}
			
			);
			
}


function showJQGrid(key,sdate,edate,period_title,period,seriesIndex){

	<?php 
		if(!function_exists('add_action')){
	    	require_once("../../../../wp-config.php");
	    }
	    
	    if($ta_screen=='views'){
	    	$grid_data = $current_path . "../grid_data.php";
	    }else {
	    	$grid_data = $current_path . "../grid_data_visits.php";
	    }
	    	    
    ?>
	url = "<?php echo $grid_data; ?>?key="+key+"&sdate="+sdate+"&edate="+edate+"&period="+period ;
	
	jQuery("#grid").jqGrid({
                                url:url,
                                datatype: "xml",
                                mtype: 'GET',
                                colNames:['Time', 'IP', 'Referer', 'User Agent', 'Title'  ],
                                colModel:[
                                      	{name:'vtime',index:'vtime',width:150,search:false}, 
                                        {name:'ip',index:'ip',sortable:false,width:150},
                                      	{name:'http_referer',index:'http_referer',sortable:false,width:200},                                        
                                        {name:'user_agent',index:'user_agent',sortable:false,width:200},
                                        {name:'title',index:'title',sortable:false,width:200},
                                                                               
                                        
                                ],
                                jsonReader : {
                                        repeatitems:false
                                },
                                rowNum:10,
                                rowList:[10,20,30,50,100],
                                pager: jQuery('#gridpager'),
                                sortname: 'vtime',
                                viewrecords: true,
                                sortorder: "desc",
                                caption:period_title                          
                                
                        }).navGrid('#gridpager',
                        	{
                        		add:false,
                        		edit:false,
                        		del:false,
                        		search:false
                        		
                        	}
                        );
                        
                        
		<?php 
			if($ta_screen=='visits'){	
		?>
		
		if(chart_type.text() == 'line'){			
			if(data_series[seriesIndex].label=='Views'){
				//col4="Title";
				jQuery('#grid').jqGrid('hideCol','user_agent');
				jQuery('#grid').jqGrid('showCol','title');
			}
			else{
				//col4="User Agent";
				jQuery('#grid').jqGrid('showCol','user_agent');
				jQuery('#grid').jqGrid('hideCol','title');
			}
		}else{
				jQuery('#grid').jqGrid('showCol','user_agent');
				jQuery('#grid').jqGrid('hideCol','title');
		
		}
		<?php 
			}else {
		?>
				jQuery('#grid').jqGrid('showCol','user_agent');
				jQuery('#grid').jqGrid('hideCol','title');
		<?php 	
			}
		?>
               
       jQuery('#grid').jqGrid('setGridParam',{ url:url });
       jQuery('#grid').setCaption(period_title);
       jQuery('#grid').clearGridData();
       jQuery('#grid').trigger('reloadGrid');   
       jQuery('#grid').filterToolbar();
       //jQuery('#grid').toggleToolbar();    

}







	
