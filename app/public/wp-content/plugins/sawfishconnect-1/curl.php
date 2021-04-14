<?php 
Class sf28c_Curl
{
	//GET 
	private $access_token;
	private $instance_url;
	private $login_url;


	private $querytype;
	private $queryatt;	

	function getCURL ($querytype, $queryatt=NULL)
	{

	/*
		Required
			'records', array('query'=>'SELECT id from Account')
			'fields', array('object'=>'Account')
			'objects'
	*/
		$this->querytype=$querytype;
		$this->queryatt=$queryatt;


		if($querytype =='records')
			$url = $this->instance_url."/services/data/v48.0/query?q=" . urlencode($queryatt['query']);

		else if($querytype =='fields')
		    $url = $this->instance_url."/services/data/v48.0/sobjects/".$queryatt['object']."/describe/";

		else if($querytype =='objects')
	    	$url = $this->instance_url."/services/data/v48.0/sobjects/";
 
		$curl = wp_safe_remote_get( $url, array('headers' => 
								array('Authorization' =>  'OAuth '.$this->access_token,
									'Content-Type' => 'application/json',
									'Accept' => 'application/json',
									'Depth' => '1',
									'Prefer' => 'return-minimal'),)); 

	    return $this->execute($curl, 'fetch');	      

	}

	//POST
	private $client_id;
	private $client_secret;

	private $refresh_token;

	function postCURL ($grant)
	{

	/*
		Required

			$_GET['code']
			site_url()	
	*/

	  /* Updated POST with WP Remote POST */
		

	 	$params = array('client_id' => $this->client_id,
	 				'client_secret' => $this->client_secret);


       $sforc_admin_site_url = '';
       
       if( is_multisite() )
         $sforc_admin_site_url = network_admin_url();
       else
         $sforc_admin_site_url = admin_url();


		if($grant == 'authorize')
		{
			$params = array_merge($params, array('grant_type' => 'authorization_code', 
									  'redirect_uri' => $sforc_admin_site_url.'admin.php?page=sforc-settings', 
									  'grant_type' => 'authorization_code', 
									  'code' => $_GET['code'],));
		}
	 	else if($grant == 'refresh') 
		{
			$params = array_merge($params, array(
						'grant_type' => 'refresh_token', 
						'refresh_token' => $this->refresh_token, 
						));
		}

	  	$sf_loginurl = "https://".$this->login_url."/services/oauth2/token";

		$postresponse = wp_safe_remote_post( $sf_loginurl, array( 'body' => $params ) );
 		 
 		$this->execute($postresponse, 'config');
 
	}	

	//GET & POST
	public $response;
	public $status;

	function execute($curl, $curltype)
	{

 
		if($curltype == 'fetch')
		{
			//$rawresponse = curl_exec($curl);
			$this->response = json_decode( wp_remote_retrieve_body($curl),true); 
			$this->status = wp_remote_retrieve_response_code($curl);
		}


		else // Refresh Token, Config Authorize
		{
			$this->response = json_decode( wp_remote_retrieve_body($curl),true); 
			$this->status = wp_remote_retrieve_response_code($curl);
		}

 
		if($this->status == 200)
		{
			if($curltype == 'config')
			{	
				sf28c_Settings::set($this->response, $this->status);

				if(isset($this->response['access_token']))
					$this->access_token=$this->response['access_token'];
			}

			else if ($curltype == 'fetch')
				return $this->response;
		}

		else if($this->status != 200)
		{

			if($curltype == 'config')
			{ 
				sf28c_Settings::set($this->response, $this->status);
				return false;
			}

			if($curltype == 'fetch')
			{
				if(isset($this->response[0]["errorCode"]) && $this->response[0]["errorCode"] == "INVALID_SESSION_ID")
				{		
 					$this->postCurl('refresh');

 					if($this->status == 200) //Refresh Succeeded
						$this->getCURL ($this->querytype, $this->queryatt); //Try getting records again after refreshed token
				}
			}

				return $this->response;
		}
		

	}

	function __construct()
	{		

		$sforc_optarray=sf28c_Settings::get();

		if(isset($sforc_optarray['client_id']))
	 		$this->client_id=$sforc_optarray['client_id'];
	 	
		if(isset($sforc_optarray['client_secret']))
			$this->client_secret=$sforc_optarray['client_secret'];
		
		if(isset($sforc_optarray['refresh_token']))
			$this->refresh_token=$sforc_optarray['refresh_token'];
		
		if(isset($sforc_optarray['access_token']))
			$this->access_token=$sforc_optarray['access_token'];
		
		if(isset($sforc_optarray['instance_url']))
			$this->instance_url=$sforc_optarray['instance_url'];

		if(isset($sforc_optarray['login_url']))
			$this->login_url=$sforc_optarray['login_url'];
		
	}


}