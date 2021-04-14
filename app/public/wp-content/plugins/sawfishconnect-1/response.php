<?php
Class sf28c_Response
{
	private static $response;
	private static $formatted;	

	private static $compound=array(
						'MailingAddress'=>array('street','city','state','postalCode','country'),
						'BillingAddress'=>array('street','city','state','postalCode','country'),
						'ShippingAddress'=>array('street','city','state','postalCode','country'),
						'OtherAddress'=>array('street','city','state','postalCode','country'),	
						'Address'=>array('street','city','state','postalCode','country'),						
								);

	static function format_objects()
	{

    	foreach ((array) self::$response['sobjects'] as $key => $value) 
   		{
    
	    	if( $value['layoutable'] == true  && ($value['searchable'] == true || $value['name'] == 'CampaignMember') )     	    
    	    	self::$formatted[$value['name']] = $value['label'];
    
    	}
		return self::$formatted;
	}

	static function format_fields()
	{
	    foreach ((array) self::$response['fields'] as $key => $value) 
	    {

		self::$formatted[$value['name']] = $value['label'];

		}
		return self::$formatted;
	}

	static function format_records()
	{
		/*
		
		
		*/
			return self::$response['records'];
	}

	static function has_compound_fields($fieldslist)
	{
		if(count(array_intersect(array_keys(self::$compound),$fieldslist))===0)
			return false;
		else
			return true;
	}

	static function format_compound_fields()
	{		
		foreach ((array) self::$response['records'] as $record => $value) {
			foreach ((array) $value as $fieldname => $fieldvalue)
			{	

					if(array_key_exists($fieldname, self::$compound))
					{
						$combinedfield='';
						foreach (self::$compound[$fieldname] as $key => $field) 
						{
							if(isset(self::$response['records'][$record][$fieldname][$field]))
							{

							    $combinedfield.=self::$response['records'][$record][$fieldname][$field].' ';
							}
						
						}
						self::$response['records'][$record][$fieldname]=$combinedfield;
					}
			}
		}

		return self::$response['records'];
	}

	static function format_date_time_fields()
	{
	    foreach ((array) self::$response['fields'] as $key => $value) 
	    {
	    	if($value['type']=='date' || $value['type']=='datetime')
				self::$formatted[$value['name']] = $value['label'];

		}
		return self::$formatted;
	}


	static function format($response, $responseType)
	{
		self::$formatted = array();
		self::$response=$response;

		if($responseType=='objects')
			return self::format_objects();

		if($responseType=='fields')
			return self::format_fields();

		if($responseType=='records')
			return self::format_records();
 			
 		if($responseType=='records_with_compound')
			return self::format_compound_fields();	

		if($responseType=='date_time_fields')
			return self::format_date_time_fields();

	}



}



