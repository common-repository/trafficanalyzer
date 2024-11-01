<?php

class TrafficAnalyzerSettings extends TrafficAnalyzer {
	
	public function __construct(){
		$this->actions();		
	}
	
	public function form(){
		global $current_user;
		global $wp_roles;
			
			?>
			<div class="wrap">
			
			<?php screen_icon(); ?>
			
			<h2>Traffic Analyzer : Settings</h2>
			
			
			<?php 
					if(isset($_POST['submit'])) {
											
						global $current_user;
												
						$titles[$current_user->ID] = $_POST['ta_titles'];
						
						if(isset($_POST['ta_aoid']) && !empty($_POST['ta_aoid']) && strlen($_POST['ta_aoid'])==32 || strlen($_POST['ta_aoid'])==0){
							update_option('ta_aoid',$_POST['ta_aoid']);	
						}
						
						if(isset($_POST['ta_live'] ) ) {
							update_option('ta_live',$_POST['ta_live']);							
						}else {
							update_option('ta_live',0);
						}


						if(isset($_POST['ta_live_admin'] ) ) {
							update_option('ta_live_admin',$_POST['ta_live_admin']);							
						}else {
							update_option('ta_live_admin',0);
						}
						
						if(isset($current_user->ID)){
							$this->update_caps("ta_settings");
							$this->update_caps("ta_visits");
							$this->update_caps("ta_visits_visits");
							
							
			?>
						<div class="updated settings-error" id="setting-error-settings_updated"> 
							<p><strong>Settings saved.</strong></p>
						</div>
			<?php 				
						}	
					}
			?>
						
			<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
			
				<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row">
							<label for="ta_titles">Live ID</label>
						</th>
						<td>	
							<input type="text" name="ta_aoid" value="<?php  echo get_option('ta_aoid'); ?>" size="40" />
							<a href='http://wptrafficanalyzer.in/get-live-id' target="_blank">Get Live ID</a>
						</td>
					</tr>					
					
					<tr valign="top">
						<th scope="row">
							<label for="ta_titles">Views Menu is allowed to : </label>
						</th>
						<td>
							<?php 
								echo $this->generate_role_checkboxes("1","ta_visits");
							?>					
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row">
							<label for="ta_titles">Settings Menu is allowed to : </label>
						</th>
						<td>
							<?php 
								echo $this->generate_role_checkboxes("2","ta_settings");
							?>				
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row">
							<label for="ta_titles">Visits Menu is allowed to :</label>
						</th>
						<td>
							<?php 
								echo $this->generate_role_checkboxes("3","ta_visits_visits");
							?>				
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row">
							<label>Enable Live Chart</label>
						</th>
						<td>
							<input type='checkbox' name='ta_live' id='ta_live' value='1'  <?php echo (get_option('ta_live')==1?'checked':'') ; ?>  />
							<label for="ta_live">Yes</label>	[ <a href='http://wptrafficanalyzer.in/access-live-chart/' target='_blank'>Access Live Chart</a> ]  	
						</td>
					</tr>			
					

					<tr valign="top">
						<th scope="row">
							<label>Monitor Admin Panel in Live Chart</label>
						</th>
						<td>
							<input type='checkbox' name='ta_live_admin' id='ta_live_admin' value='1'  <?php echo (get_option('ta_live_admin')==1?'checked':'') ; ?>  />
							<label for="ta_live_admin">Yes</label>
						</td>
					</tr>			
									
				</tbody>
				
				</table>
				<p class="submit"><input type="submit" value="Save Changes" class="button-primary" id="submit" name="submit">
				</p>
			</form>
			
			</div>
			
		
			<?php 	
						
		}
		
		/*
		 * Adds / Removes $cap(ta_settings or ta_visits) to all the existing roles
		 */		
		public function update_caps($cap){
			global $wp_roles;
			$roles = $wp_roles->role_objects;
			
			foreach($roles as $role){
				if(is_array($_POST[$cap])){
					if(in_array($role->name,$_POST[$cap]))
						$wp_roles->add_cap($role->name,$cap);
					else if($role->name !="administrator")
						$wp_roles->remove_cap($role->name, $cap);
				}else if($role->name != "administrator") {
					$wp_roles->remove_cap($role->name, $cap);
				}		
			}		
		}
		
		
		/*
		 * Generates Checkboxes in a row corresponding to each roles
		 */		
		public function generate_role_checkboxes($suffix="",$cap=""){
			global $wp_roles;
			$wp_roles = new WP_Roles();		
			
			$disabled_checked="";
			$check="";
								
			foreach($wp_roles->role_objects as $role){
				
				if($role->name=="administrator")
					$disabled_checked="disabled checked";
				else if($role->has_cap($cap))	
					$disabled_checked="checked";
				else 
					$disabled_checked="";
					
				$id = $role->name."_".$suffix;
				$attr_id = "id='".$id."'";
				
				$name = $cap."[]";
				$attr_name = "name='".$name."'";
				
				$value = $role->name;
				$attr_value = "value='".$value."'";
				
				$label = "<label for='".$id."' >".ucfirst($value)."</label>"   ;
				
				$check .= "<input type='checkbox' $attr_id $attr_name $attr_value $disabled_checked />";
				$check .= $label;			 
			}			
			return $check;
		}
		
		public function get_num(){
                        $titles = get_option("ta_titles");
                        global $current_user;
                        $ta_title = $titles[$current_user->ID];
                        return $ta_title;
		}
		
	
		
		public function actions(){
			// Will be executed only on loading the settings page
			add_action('load-analyzer_page_settings_traffic-analyzer',array(&$this,"add_style"));
		}
		
}
