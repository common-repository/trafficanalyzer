
function show_dialog(obj){
	
	showJQGrid(obj.key,obj.sdate,obj.edate,obj.period_title,obj.period);
	
	jQuery('#data').dialog( {
				closeOnEscape: true,
				title: "Traffic Analyzer : "+obj.article,
				width: 750,
				height:315			
		} 
		
	);
}

/*
	Clears the daterangepicker content
*/
function clearDateRangePicker(){
	jQuery('#daterangepicker').val(""); 
	jQuery('#daterangepicker_start').val("");
	jQuery('#daterangepicker_end').val("");
	return false;
}


/*
	Assume Monday is the start day of the week
*/
function weekStartDate(){
	var d = new Date();
	var n = d.getDay();		// 0 for sunday, 6 for saturday
	
	if(n==0)	// For Sunday
		n = 6;
	else		// For other days
		n = n - 1; 
	
	d.setDate(d.getDate() - n );
	return d;
}


function showJQGrid(key,sdate,edate,period_title,period){
	<?php 
		if(!function_exists('add_action')){
	    	require_once("../../../../wp-config.php");
	    }
	    $plugin_url = plugins_url("/",dirname(__FILE__));
	    $grid_data = $plugin_url."/grid_data.php";
	    	    
    ?>

	url = "<?php echo $grid_data; ?>?key="+key+"&sdate="+sdate+"&edate="+edate+"&period="+period ;
	
	jQuery("#grid").jqGrid({
                                url:url,
                                datatype: "xml",
                                mtype: 'GET',
                                colNames:['Time', 'IP', 'Referer', 'User Agent'],
                                colModel:[
                                        {name:'vtime',index:'vtime',width:150,search:false},
                                        {name:'ip',index:'ip',sortable:false,width:150},
                                        {name:'http_referer',index:'http_referer',sortable:false,width:200},
                                        {name:'user_agent',index:'user_agent',sortable:false,width:200}                                       
                                        
                                ],
                                jsonReader : {
                                        repeatitems:false
                                },
                                rowNum:10,
                                rowList:[10,20,30,50,100],
                                pager: jQuery('#gridpager'),
                                sortname: 'vtime',
                                viewrecords: true,
                                sortorder: "asc",
                                caption:period_title                          
                                
                        }).navGrid('#gridpager',
                        	{
                        		add:false,
                        		edit:false,
                        		del:false,
                        		search:false
                        		
                        	}
                        );
               
       jQuery('#grid').jqGrid('setGridParam',{ url:url } );       
       jQuery('#grid').setCaption(period_title);
       jQuery('#grid').clearGridData();
       jQuery('#grid').trigger('reloadGrid');   
       jQuery('#grid').filterToolbar();  
}

jQuery(window).load(function() {
						var language = "en";
						
						if(!typeof(navigator.language)==="undefined"){		// NonIE
							language = navigator.language;
						}else if(!typeof(navigator.browserLanguage)==="undefined") {	// IE
							language = navigator.browserLanguage;
						}
						
						frmt = "mm/dd/yy";
					
						if(!(typeof(jQuery.datepicker.regional[language]) ==="undefined")){
							frmt = jQuery.datepicker.regional[language].dateFormat;						
						}
						
											
						jQuery('#daterangepicker').daterangepicker({
							dateFormat : frmt,
							presetRanges: [
									{text: 'Today', dateStart: 'today', dateEnd: 'today' },
									{text: 'Yesterday', dateStart: 'today-1days', dateEnd: 'today-1days' },
									{text: 'Last 7 days', dateStart: 'today-7days', dateEnd: 'today' },
									{text: 'Month to date', dateStart: function(){ return Date.parse('today').moveToFirstDayOfMonth();  }, dateEnd: 'today' },
									{text: 'Year to date', dateStart: function(){ var x= Date.parse('today'); x.setMonth(0); x.setDate(1); return x; }, dateEnd: 'today' },									
									{text: 'The previous Month', dateStart: function(){ return Date.parse('1 month ago').moveToFirstDayOfMonth();  }, dateEnd: function(){ return Date.parse('1 month ago').moveToLastDayOfMonth();  } },
									{text: 'This Week', dateStart: weekStartDate, dateEnd: 'today' },
									
									//extras:
									{text: 'Clear',dateStart:clearDateRangePicker,dateEnd:clearDateRangePicker}										
								],
							
							onChange:function(){
								var dt = jQuery('#daterangepicker').val();
								dt_array = dt.split("~");												
															
								jQuery('#daterangepicker_start').val("");
								jQuery('#daterangepicker_end').val("");
								
								jQuery('#daterangepicker_start').datepicker();
								jQuery('#daterangepicker_start').datepicker("option","dateFormat",frmt);
								var str_start_dt = jQuery.trim(dt_array[0]);
								jQuery('#daterangepicker_start').datepicker("setDate",str_start_dt);
								jQuery('#daterangepicker_start').datepicker("option","dateFormat","yy/mm/dd");
							
								if(dt_array.length > 1){
										jQuery('#daterangepicker_end').datepicker();
										jQuery('#daterangepicker_end').datepicker("option","dateFormat",frmt);
										var str_end_dt = jQuery.trim(dt_array[1]);
										jQuery('#daterangepicker_end').datepicker("setDate",str_end_dt);
										jQuery('#daterangepicker_end').datepicker("option","dateFormat","yy/mm/dd");
									
									}
								},
								rangeSplitter : "~"
					} );					
		}
);
