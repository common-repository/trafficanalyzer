<?php 
		if(!function_exists('add_action')){
	    	require_once("../../../../wp-config.php");
	    }
		include '../config.php';

	   ?>

<?php 
	global $current_user;
	$post_loaded_script_path = plugins_url("/",__FILE__);
	
	
	if(is_active_widget(false,false,'ta_trend',true))
		$trend_widget='yes';
	else 
		$trend_widget='no';
		
	if(is_active_widget(false,false,'ta_visits',true))
		$visit_widget='yes';
	else 
		$visit_widget='no';

		$server_data[0] = get_option('ta_no_of_days');	 	// No of days
		$server_data[1] = get_option('ta_xaxis');	 		// show or hide x-axis labels
		$server_data[2] = get_option('ta_trend_powered');	// show powered for trend widget
		$server_data[3] = get_option('ta_visit_powered');	// show powered for visit widget
		
		if(empty($server_data[0]))
			$server_data[0] = 4;
			
		if(empty($server_data[1]))
			$server_data[1] = 0;
			
		if(empty($server_data[2]))
			$server_data[2] = 0;
			
		if(empty($server_data[3]))
			$server_data[3] = 0;
		
?>

var lbl = new Array();

jQuery(document).ready(function(){								
								
								
								<?php									
									if($trend_widget=='no' || $server_data[2]=='1' ) {																
								?>
									jQuery('#wpta_trend').hide();								
								<?php 
									}
								?>
								
								
								<?php 
									if($visit_widget=='no' || $server_data[3] == '1'){																
								?>
									jQuery('#wpta_powered').hide();								
								<?php 
									}
								?>								
								
								
                                <?php 
                                	global $wpdb;
                                	$sql  = " select date_format(dt,'%d-%M-%Y') as dt ,count(ip) cnt from ( select date(vtime) as dt ,count(ip) as ip ";
                                	$sql .= " from wp_tanalyzer_visits ";
                                	$sql .= " where 1=1" ;

                                	
                                	$sql .= ta_user_agent_where();
                                	$sql .= "group by date(vtime),ip,user_agent,wpta_cookie ) as t ";
                                	$sql .= " where dt between date_sub(current_date(), interval $server_data[0] day) and current_date() ";
									
									$sql .= " group by dt ";
									$sql .= " order by dt asc ";
									
									$result = $wpdb->get_results($sql);
									$data_array=array();
									$lbl_array = array();
									
									foreach($result as $row){								
										$dt = date('d-M-y', strtotime($row->dt));
										$data_array[] = $row->cnt ;
										$lbl_array[]  = "'" . $row->dt . "'";										
									}
									
									if(is_array($data_array)) {
										$data_string = implode(',',$data_array);
									}else{
										$data_string = "";
									}
									
									if(is_array($lbl_array)) {
										$lbl_string = implode(',',$lbl_array);
									}else{
										$lbl_string = "";
									}
								
                                ?>                                
                                
                          var data = <?php echo "[" . $data_string ."]" ; ?>;
                          lbl = <?php echo "[" . $lbl_string ."]" ; ?>;
                          
                          var datapoint = new Array();
                          
                          for(var i=0;i<data.length;i++){
                          	datapoint[i] = new Array(i,data[i]);
                          }
                          
                                
                           var plot_options = 	{ 
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
									points: {
										show:true
									}
								},
								xaxis : {
									tickFormatter: function(val,axis){
										
										if(lbl[val]==undefined)
											return '';
										else{
											if(<?php echo $server_data[1]; ?> != 0) 	
												return lbl[val];
											else
												return  '';
										}
					
									}
							
								}
								
					
							};
                                
                                   
                                if(datapoint.length>0){
                                
                                if(jQuery('#trenddiv').length>0){
	                                plot = jQuery.plot( jQuery('#trenddiv'), [ datapoint ] , plot_options );  
	                            }
                                }else{
                                         	jQuery('#trenddiv').html("No Data Found");
                               }
                               
                               
                               jQuery("#trenddiv").bind("plothover", function (event, pos, item) {							
            					if (item){            							            							
            							jQuery('#tooltip').remove();
            							showTooltip(pos.pageX,pos.pageY,item);
            							
                				}else{
                					jQuery('#tooltip').remove();
                				}
								}
    					);
                                                              
                        	}
                );
                

function showTooltip(x, y, item) {
		
		var x_point = item.datapoint[0];  // => x-axis
		var y_point = item.datapoint[1];  // => y-axis
		var x_lbl = lbl[item.datapoint[0]] //=> X-Axis Label
				
        jQuery('<div id="tooltip"><div align="center"><span style="float:left;width:40px">Date:</span>'+x_lbl+'<br /><span style="float:left;width:40px">Count:</span>' + y_point + '</div>').css( {
            position: 'absolute',
            top: y + 5,
            left: x + 5,
            border: '1px solid #000',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body");
        
        var total_x_points = lbl.length;
		var current_x_point = x_point;
		
		var width = parseFloat(jQuery('#tooltip').css('width'));
		
		if(current_x_point >= total_x_points/2)
				jQuery('#tooltip').css('left', x-width ) ;
    
}
                
