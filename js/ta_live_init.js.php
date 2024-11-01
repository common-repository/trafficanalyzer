/**
 *	Date : 05-Mar-2012 
 *	Author : George Mathew K
 *	E-Mail : georgemathewk@yahoo.com
 */
<?php
	include '../config.php';


	function sanitize_id($id){
                        $str = str_replace(" ", "-", $id); // Replaces all spaces with hyphens.
                        $str = preg_replace('/[^A-Za-z0-9]/', '', $str); // Removes special chars.
			$str = strip_tags($str);		// Removes all the script tags
			return $str;

        }


	$id = $_GET['ta_aoid'];
	$id = sanitize_id($id);

	if(strlen($id) == 32  ){	// Ensures that id is 32 characters in length

?>

(function(){
						
                        var head= document.getElementsByTagName('head')[0];
                        var script= document.createElement('script');
                        script.type= 'text/javascript';
			
			<?php
				if($server==1){
			?>
                        	script.src= 'http://wptrafficanalyzer.in/ta_live.js.php?ta_aoid=<?php echo $id; ?>';                        
			<?php
				}else if($server==0){
			?>
				
                        	script.src= 'http://localhost/ta_live.js.php?ta_aoid=<?php echo $id; ?>';                        
			<?php
				}
			?>
			
                        
                        head.appendChild(script);
})();


<?php
	}
?>


