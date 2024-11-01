<?php

	/**
	 * @author George Mathew K <george@gladsys.in>
	 * @since 1.8.0	 
	 * @package trafficanalyzer
	 * @license GPL V2 
	 */

class TA_No_Chart {
	private $chart;
	
	/**
	 * 
	 * Constructor Function
	 * @since 1.8.0
	 */
	public function __construct(){
		$this->chart = new open_flash_chart();	
		$this->set_title();	
	}
	
	
	/**
	 * 
	 * Setting title to the chart
	 * @since 1.8.0
	 */
	public function set_title(){
		$title_text="No Visitors are found";				
		$title_style = "font-size:20px;padding:20px;width:400px";
		$title = new title($title_text);
		$title->set_style($title_style);
		$this->chart->set_title($title);
	}
	
	
	/**
	 * Drawing the chart
	 * @since 1.8.0
	 */
	public function draw(){
		echo $this->chart->toString();
	}
		
}
