<?php
class sf28c_Settings
{
	 	static function get()
	{


	    if(is_multisite()) 
			$opt=self::d(get_network_option( null, 'sf28c'));
		else
			$opt=self::d(get_option('sf28c'));

	  if(!defined('sf28c') && isset($opt['refresh_token']))
		  self::revoke($opt['refresh_token']);
	  return $opt;

	}

	static function set($response, $status)
	{

	  $sforc_optarray=self::get();
	  
	  if(!$sforc_optarray)  
	  	$sforc_optarray=array();

	  if(isset($response['refresh_token']))
		{
		  $sforc_optarray['refresh_token'] = $response['refresh_token'];
		}

	  if(isset($response['access_token']))
		{
		  $sforc_optarray['access_token'] = $response['access_token'];
		}

	  if(isset($response['instance_url']))
		{
          $sforc_optarray['instance_url'] = $response['instance_url'];
		}

      $sforc_optarray['status'] = $status;
 		
 	  if(is_multisite())
	      update_network_option( null, 'sf28c', self::e($sforc_optarray));	      
 	  else
 	      update_option('sf28c', self::e($sforc_optarray));
	}

	static function e($setting)
	{
		return base64_encode(serialize($setting));
	}

	static function d($setting)
	{
		return unserialize(base64_decode($setting));		
	}

	static function revoke($token)
	{
		$url='https://login.salesforce.com/services/oauth2/revoke?token='.$token;
		$curl = curl_init(); 
	    curl_setopt($curl, CURLOPT_URL); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($curl); 
        curl_close($curl);
 	}
 }

/*
	sf28c_Settings::get();
	sf28c_Settings::set($response);
*/


