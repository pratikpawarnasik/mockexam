<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

date_default_timezone_set('asia/kolkata');
class PageBase 
{
	static $successmessage = "success";

	static $tempVar = "this dynamic mail content";
	
	public static function GetLocalDate()
	{
		return new DateTime("now", new DateTimeZone('asia/kolkata'));
	}
	
	public static function GetHeaders()
	{
		$headers = '';
		foreach ($_SERVER as $name => $value)
		{
			if (substr($name, 0, 5) == 'HTTP_')
			{
			   $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
	
	public static function GetHeader($headerkey)
	{
		$headervalue = "";
		
		$headerarray = PageBase::GetHeaders();
		foreach($headerarray as $key=>$value)
		{
			if(strcmp(strtolower($key), strtolower($headerkey)) == 0)
			{
				$headervalue = $value;
				break;
			}
		}
		
		return $headervalue;
	}
	
	public static function generatePassword()
	{
		//generate password
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 8; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
		
		return implode($pass);
	}
	
	public static function generateUsername()
	{
		//generate password
		$alphabet = "abcdefghijklmnopqrstuwxyz0123456789";
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 6; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
		return implode($pass);
	}
	
}
?>