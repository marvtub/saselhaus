<?php
function sforc_settings_page() 
{

	global $sforc_optarray;

	/* DEBUG */
	if(isset($_GET['fields']))
	{

		$fieldsstest=new sf28c_Curl();
		$rawresponse=$fieldsstest->getCurl('fields',array('object'=>'Account'));

		echo '<pre>';
    	print_r(sf28c_Response::format($rawresponse,'fields'));
		echo '</pre>';
			
	}
	/* DEBUG */

	if(isset($_GET['reset']))
	{
		update_option( 'sforc_connection_settings', array());
	}	


	if(isset($_GET['refresh']))
	{
		$refresh=new sf28c_Curl();
		$refresh->postCurl('refresh');
 	}

	/* Code returned from Salesforce */
	$authorizestatus=null;
	if(isset($_GET['code']))
	{
		$authorize=new sf28c_Curl();
  		$authorize->postCurl('authorize');
  		$authorizestatus=$authorize->status;
	}


	?>

<style type="text/css">
	.click-to-select
{
    -webkit-touch-callout: all; /* iOS Safari */
    -webkit-user-select: all; /* Safari */
    -khtml-user-select: all; /* Konqueror HTML */
    -moz-user-select: all; /* Firefox */
    -ms-user-select: all; /* Internet Explorer/Edge */
    user-select: all; /* Chrome and Opera */

    color:#44C1FF;
    font-weight: bold;
}

@media screen and (min-width: 1250px) { 

 	#sf28c-admin-settings{
	background-image: url(<?php echo plugins_url( 'img/setup.png', __FILE__ );?>);
    background-repeat: no-repeat;
    background-position: right bottom; 
    position: relative;
    background-size: auto 70%;
	}
}
</style>

<div class='wrap'>
		<h2>Connection Settings</h2>


		<?php if (!function_exists('curl_version')) : ?>
		<div style='margin:2em 0em 2em 0em;background-color:white;padding:1em;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);border-left: 4px solid red;'>
			<h2>Your website needs cURL extension enabled to receive information from Salesforce. Start by following the steps<a href='https://sfplugin.com/docs/' target="_blank"> on our guide </a> to enable cURL.</h2>
		</div>	
			 
		<?php endif; ?>


		<?php if (!is_ssl()) : ?>

		<div style='margin:2em 0em 2em 0em;background-color:white;padding:1em;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);border-left: 4px solid red;'>
			<h2>Your website needs SSL activated to securely connect to Salesforce. Start by following the steps<a href='https://sfplugin.com/docs/' target="_blank"> on our guide </a> to setup SSL.</h2>
		</div>		

		<?php endif; ?>

		<?php if ((isset($authorizestatus) && $authorizestatus!=200) || (!isset($_GET['code']) && isset($sforc_optarray['status']) && $sforc_optarray['status']!=200)): ?>
		
		<div style='margin:2em 0em 2em 0em;background-color:white;padding:1em;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);border-left: 4px solid red;'>
			<h2>Connection not setup correctly. Start by following the steps<a href='https://sfplugin.com/docs/' target="_blank"> on our guide </a> to connecting Salesforce.</h2>
		</div>		

		<?php endif; ?>

		<?php if (isset($authorizestatus) && $authorizestatus==200): ?>
		
		<div style='margin:2em 0em 2em 0em;background-color:white;padding:1em;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);border-left: 4px solid #33ce33;'>
			<h2>All Done! Successfully connected to Salesforce, <a href='/wp-admin/admin.php?page=new_sforc_cards'> create your first page</a>.</h2>
		</div>		

		<?php endif; ?>

			<div id="sf28c-admin-settings" style='margin:2em 0em 2em 0em;background-color:white;padding:2em;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);'>
				<form method="post" class="validate" novalidate="novalidate" action="<?php echo admin_url( 'admin-post.php'); ?>">
					<?php 
						settings_fields( 'sforc_settings_group' ); 
					    do_settings_sections( 'sforc_settings_group' );  
						wp_nonce_field( 'sforce_setup_oauth', 'sforc_admin_menu' );
					?>

					<input type="hidden" name="action" value="sforce_setup_oauth" />
					<table class="form-table">
 
						<tr valign="top">
							<th scope="row">Consumer Key</th>
							<td><input required class="large-text" type="text" name="client_id" value="<?php echo esc_attr( $sforc_optarray["client_id"] ); ?>" /></td>
						</tr>
						<tr valign="top">
							<th scope="row">Consumer Secret</th>
							<td><input aria-required="true" type="password" name="client_secret" value="<?php echo esc_attr( $sforc_optarray["client_secret"] ); ?>" /></td>
						</tr>
						<tr valign="top">
							<th scope="row">Login Url</th>
							<td><input required type="text" placeholder="login.salesforce.com" name="login_url" value="<?php echo esc_attr( $sforc_optarray["login_url"] ); ?>"/></td>
						</tr>

						<tr valign="top">
							<th scope="row">Callback URL</th>
							<td><span class="click-to-select"><?php 

							if( is_multisite() )
						         $sforc_admin_site_url = network_admin_url();
						       else
						         $sforc_admin_site_url = admin_url();


							echo $sforc_admin_site_url.'admin.php?page=sforc-settings'; ?></span> </br> <span class="description">Copy this to the Callback URL field in Connected App Settings on Salesforce</span></td>
						</tr>
					</table>
					<?php submit_button('Connect Salesforce'); ?> </form>
			</div>
		</div>

		<?php

		include 'helplinks.php';

}