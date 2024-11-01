<?php 
		if(!function_exists('add_action')){
	    	require_once("../../../../wp-config.php");
	    }

	   ?>

<?php 
	$hid="";

	$id = sanitize_id($_GET['hid']);

	if(strlen($id)==32){
		$hid = "hid=".$id;
	}

	$post_loaded_script_path = plugins_url("/",__FILE__);
	$post_loaded_script_file = $post_loaded_script_path . "ta_post_loaded.php?".$hid;	


	 function sanitize_id($id){
                        $str = str_replace(" ", "-", $id); // Replaces all spaces with hyphens.
                        $str = preg_replace('/[^A-Za-z0-9]/', '', $str); // Removes special chars.
                        $str = strip_tags($str);                // Removes all the script tags
                        return $str;
        }

?>

jQuery(document).ready(function(){
		var url = "<?php echo $post_loaded_script_file;?>";
		var xhr = new XMLHttpRequest();
		xhr.open("get",url,"true");
		xhr.send();
	}
);


window.onload = function(){
	/*
		var url = "<?php echo $post_loaded_script_file;?>";
		var xhr = new XMLHttpRequest();
		xhr.open("get",url,"true");
		xhr.send();
	*/
}
