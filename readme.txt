==== Plugin Name ====
Contributors: georgemathewk
Tags: traffic analyzer, statistics, visitor counter widget, traffic counter, traffic widget, visitor statistics, trend, widget, visits chart, graph, live visits, realtime, analytics, live chart, active visits, online
Requires at least: 3.1
Tested up to: 4.3.1
Stable tag: 3.5.0

An Infographic tool for analyzing visitors which provide various statistical information in graphs and a customizable visitor counter widget

== Description ==

This is a true wordpress plugin for traffic analysis. It provides the following functionalities :  

* Live Visits [**Access Live Chart**](http://wptrafficanalyzer.in/access-live-chart)
* Visitors Statistics widget with multi language and customizable labels
* Visits vs Views Graph for daily, weekly, monthly and all time
* Posts / Pages View Count graph for daily, weekly, monthly and all time
* Consolidated views for all time
* Daily Views for all timetime
* Weekly Views for all time
* Monthly views for all time
* Consolidated Views on specific date
* Daily Views on a specific date
* Weekly views on a specific date
* Monthly Views on a specific date
* Consolidated Views in a range of dates
* Daily Views in a range of date
* Weekly Views in a range of dates
* Monthly Views in a range of dates
* Consolidated Visits vs Views for all time
* Daily Visits vs Views for all timetime
* Weekly Visits vs Views for all time
* Monthly Visits vs Views for all time
* Consolidated Visits vs Views on specific date
* Daily Visits vs Views on a specific date
* Weekly Visits vs Views on a specific date
* Monthly Visits vs Views on a specific date
* Consolidated Visits vs Views in a range of dates
* Daily Visits vs Views in a range of dates
* Weekly Visits vs Views in a range of dates
* Monthly Visits vs Views in a range of dates


Features:

* Live Visits [**More ...**](http://wptrafficanalyzer.in/live-chart-introduction)
* Will run in any browser with HTML support
* Visit widget with multilanguage and customizable labels
* Trend Widget which can be drag and drop to side bar
* Visitors vs Views Graph
* Views graph for posts and pages in daily, weekly, monthly wise
* Can be drill down to to load grid data
* Role based access


== Installation ==

1. Upload trafficanalyzer directory to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Upgrading users should ensure that, the plugin is deactivated and then reactivated after automatic upgradation

== Screenshots ==

1. TrafficAnalyzer - Settings
2. Views - Consolidated View
3. Views - Daily View
4. Views - Weekly View
5. Views - Monthly View
6. Visits vs Views - Consolidated View
7. Visits vs Views - Daily View 
8. Visits vs Views - Weekly View 
9. Visits vs Views - Monthly View 
10. Visit Widget : Settings
11. Visit Widget : Public View
12. Trend Widget : Settings
13. Trend Widget : Public View

== Changelog ==
= 3.5.0 =
* Fix: Fixed a major security issue reported by WordPress Team


= 3.4.2 =
* Fix: Sanitized the variable ta_aoid and fixed a security issue

= 3.4.1 =
* New: Trafficanalyzer is upgraded to work with Wordpress 3.6.1
* Fix: wptrafficanalyzer.in access is limited to aoid entered clients

= 3.4.0 =
* New: TrafficAnalyzer is upgraded to work with Wordpress 3.5.2
* Fix: Sanitization is added to ta_loaded.js.php
* Fix: Unused variable aoid is removed from the file ta_loaded.js.php

= 3.3.2 =
* Fix: Bug in the class TrafficAnalyzer is fixed
* Tested in Wordpress 3.4.2


= 3.3.1 =
* Fix: Missing the busy wait gif

= 3.3.0 =
* New: Busy Process icon is added to the charts 


= 3.2.1 =
* Fix: Trend Widget, Option for displaying X-Axis labels is corrected 

= 3.2.0 =
* New: New item 'This Week' is added to DateRangePicker

= 3.1.0 =
* New: Timezone synchronization between WordPress Timezone and MySQL Timezone  is implemented

= 3.0.3 =
* Fix: Tooltip at the right extreme corner is not clearly visible, if datapoints are just two

= 3.0.2 =
* Fine tuning the code
* Commenting the drop table queries from the uninstall.php

= 3.0.1 =
* Fix: Calculation of date difference was wrong, which affected the accuracy of Visits Per Day Field

= 3.0.0 =
* New: Visits Per Day Field is added to Tooltip of Visits vs Views Graph

= 2.9.1 =
* Fix: Removing the unwanted file ta_jqplot_loader.js.php from the repository

= 2.9.0 =
* New: Views Per Visit Field is added to Tooltip of Visits vs Views Graph

= 2.8.2 =
* Fix: Portlet contents were not selectable

= 2.8.1 =
* Fix: Removing the jqplot library from the repository, this is unused from 2.8.0

= 2.8.0 =
* New: Trend Widget is revamped using flot, replaced jqPlot

= 2.7.1 =
* Fix: The PHP classes, DateInterval and DateTime were replaced

= 2.7.0 =
* New: New field namely Last 24 Hours is added to Visitors Widget
* New: New field namely Last 30 Days is added to Visitors Widget
* New: New field namely Online Visitor's Count is added to Visitors Widget
* New: Customizable labels for Visitor's Widgets

= 2.6.0 =
* New: Legend, Chart Type and Feedback are added to drag and drop portlet

= 2.5.2 =
* Fix: Tooltip position of the chart at the right extreme corner is fixed
* Fix: Sort on the Time Field is fixed for Grid Data

= 2.5.1 =
* Fix: Advanced Options ID is changed to Live ID
* New: Get Live ID link is added to trafficanalyzer's settings

= 2.5.0 =
* New: Advanced options of Visit Widget is now the part of Widget's options
* New: Advanced options of Trend Widget is now the part of Widget's options

= 2.4.1 =
* Fix: Live Chart Access Page is updated in the plugin's Settings


= 2.4.0 =
* New: Integrated Live Visits Chart

= 2.3.0 =
* New: Filtering options are enhanced
* New: All the charts are revamped using flot.
* Fix: Wrong weekly start date and end date

= 2.2.1 =
* Fix: window.onload is replaced with jquery.ready() for compatibility with IE
* Fix: User agent with Alexa Toolbar is treated as bot visit

= 2.2.0 =
* New: Visitor Trend Chart widget is added

= 2.1.0 =
* New: Visitor Statistics widget is added
* New: Help is Added
* Fix: Ajax call was synchronous. It is changed to asynchronous

= 2.0.0 =
* New: Visits vs Views Graph is added
* New: Introduced Cookies
* Fix: Chart is stucked when the grid chart contains only one row
* Fix: Error in the Weekly start date and weekly end date

= 1.9.0 =
* New: Different kinds of datapoints like curved, segment, step, horizontal etc are added
* New: Different kinds of charts like area chart, column chart, bar chart etc are added
* New: Graph is implemented using Adobe Flex
* Advanced options are added
* Fix: Accuracy level of plugin is increased alot
* New: New website http://wptrafficanalyzer.in is opened for the plugin


= 1.8.0 =
* New: Option to select yesterday is added in DateRange Picker
* New: Home or Front page is also added for trend analysis
* New: Application architecture is revamped
* New: Tooltip of line chart is updated
* New: X-Axis labels are modified
* New: Background colour of Chart title is removed

= 1.7.0 =
* New: Search option for grid data is added

= 1.6.1 =
* Fix: With robot data is shown, if no settings is done. ( This should be Without robot data ) 

= 1.6.0 =
* New: Settings for Robots is added. Now With robots, without robots and robots only graph data can be generated

= 1.5.0 =
* New: Grid data for Line Chart

= 1.4.0 =
* New: Grid data will be opened on clicking the pie chart

= 1.3.0 =
* New: Trend analysis for selected articles.
* New: Architecture is changed. New classes TrafficAnalyzer, TrafficAnalyzerViews and TrafficAnalyzerSettings were introduced

= 1.2.0 =
* New: Role based access is implemented
* Fix: "Visits" is renamed to "Views"

= 1.1.3 =
* Fix: Excluding some general bots like google-bots, Yahoo Slurp, MSN Bot etc

= 1.1.2 =
* Fix: Necessary table on activation is not created. This is fixed

= 1.1.1 =
* Fix: Total Visits on Line chart tooltip is modified from daterange total to period total

= 1.1.0 =
* New: Total Visits is added to the line chart tooltip

= 1.0.0 = 
* New: Initial Release


== Upgrade Notice ==
= 3.5.0 =
Fixed a major security issue reported by WordPress Team

= 3.4.2 =
Security issue is fixed

= 3.4.1 =
Fine tuned and upgraded to work with Wordpress 3.6.1

= 3.4.0 = 
Upgraded TrafficAnalyzer to work with Wordpress 3.5.2

= 3.3.1 =
Uploaded the missed busy wait gif

= 3.3.0 =
Busy Process icon is added to the charts

= 3.2.1 =
Trend Widget, Option for displaying X-Axis labels is corrected

= 3.2.0 =
New item 'This Week' is added to DateRangePicker

= 3.1.0 =
Timezone synchronization between WordPress Timezone and MySQL Timezone  is implemented

= 3.0.3 =
Tooltip at the right extreme corner is not clearly visible, if datapoints are just two

= 3.0.2 =
Fine tuning the code. Commenting the drop table queries from the uninstall.php

= 3.0.1 =
Corrected the bug in the calculation of date difference

= 3.0.0 =
Visits Per Day Field is added to Tooltip of Visits vs Views Graph

= 2.9.1 =
Removing the unwanted file ta_jqplot_loader.js.php from the repository

= 2.9.0 = 
Views Per Visit field is added to Tooltip of Visits vs Views Graph

= 2.8.2 =
Portlet contents are selectable now

= 2.8.1 =
Removing the jqplot library from the repository, this is unused from 2.8.0

= 2.8.0 =
Trend Widget is revamped using flot

= 2.7.1 =
The PHP classes, DateInterval and DateTime were replaced

= 2.7.0 =
New fields like Online Visitors, Last 30 Days, Last 24 Hours are added to Visitors Widget
Field labels are customizable, so that labels can be displayed in native languages

= 2.6.0 =
Legend, Chart Type and Feedback are added to drag and drop portlet


= 2.5.2 =
Bug in Tooltip position is fixed
Bug in the sorting of the Time Field is fixed for Grid Data


= 2.5.1 =
Advanced Options ID is changed to Live ID

= 2.5.0 =
More options are added to Visitors Widget and Trend Widget

= 2.4.1 =
Live Chart Access Page is updated in the plugin's Settings

= 2.4.0 =
Support to Live Visits is added

= 2.2.1 = 
Fixed some bugs

= 2.2.0 =
Visitors trend chart widget is introduced 

= 2.1.0 =
Customizable Visit Counter(today, yesterday, last week and all time )  Widget is introduced.

