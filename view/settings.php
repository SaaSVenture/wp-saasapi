
<div class="wrap">	
	<h1>Settings</h1>
	
	<div id="ajax-response"></div>
	<?
	
	$_options = get_option('saas-wpapi-settings');
	//print_r($_options);
	?>
	
	<script>
	jQuery(document).ready(function($) {
		
		jQuery('#site-member-settings').on("submit", function(){
			$(this).api_ajax("#ajax-response","Settings successfully saved.");
			return false;
		});
		
		jQuery('.nav-tab').on("click", function(){
			jQuery('.nav-tab').removeClass('nav-tab-active');
			jQuery(this).addClass('nav-tab-active');
			jQuery(this).blur();
			var tab_ID = jQuery(this).attr('tab-id');
			var tab_title = jQuery(this).attr('title');
			jQuery('.nav-tab-wrapper-content').hide();
			jQuery('#'+tab_ID).fadeIn();
			<?
			$page_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
			$page_link .= "?page={$_GET['page']}&tab=";
			?>
			window.history.pushState("Site Member Settings"+tab_title, "Settings: "+tab_title, "<?=$page_link;?>"+tab_ID);
			return false;
		});
		
	});
	</script>
	<style>
		.hide{display:none}
	</style>
	
	<br/>
	
	<form method="post" name="site-member-settings" id="site-member-settings">
		<input type="hidden" name="action" value="saas_wpapi_settings_save" />
		
		<?php wp_nonce_field(MEMBERS_SECRET.'_settings'); ?>
		
		<table class="form-table">
			<tr>
			
				<th scope="row">API Details </th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span>API Key</span></legend>
						<label for="api_key">
							<input name="api_key" type="text" id="api_key" value="<?=$_options['api_key'];?>" class="regular-text code" />
							App ID
							<p class="description"></p>
						</label>
						<br />
						
						<legend class="screen-reader-text"><span>API Secret</span></legend>
						<label for="api_secret">
							<input name="api_secret" type="text" id="api_secret" value="<?=$_options['api_secret'];?>" class="regular-text code" />
							App Secret
							<p class="description"></p>
						</label>
						<br />
						
					</fieldset>
					
				</td>
			</tr>
			
		</table>
		
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  />
		</p>
		
		<?
		/* $resp = json_decode($this->raw($this->server.'/auth/',array(
			"user"=>$username,
			"pass"=>$password
		)),1);
 */
 
 
 //include(SAAS_WPAPI_DIR_PLUG.'/Saas/Sdk/Api.php');
		

print_r($loginUrl);
?>

		
		
	</form>
	
	<script>
			//default tab
			jQuery(document).ready(function($) {
				$('#<?=$current;?>').fadeIn();
			});
	</script>


</div>