<?php

$config['isSmtp']         	= false;
$config['smtpServer']      	= "your smtp server adress";
$config['smtpPort']        	= "25";
$config['smtpUser']        	= "your username";
$config['smtpPass']        	= "your password";

$config['method']			= "get";

$config['sendingEmail']    	= "kontakt@dburgmann.de";
$config['receivingEmail']  	= "abzhibilt@gmail.com";
$config['name']            	= "max mustermann";
$config['domain']          	= "www.dburgmann.de";
$config['processingPage']  	= "http://www.dburgmann.de/showroom/cf";
$config['prefix']			= "cf_";
$config['fields']		   	= Array("name" => "input", "email" => "input", "message" => "textarea");
$config['mandatory']		= Array("name", "email", "message");
$config['validation']		= Array("name" 		=> array("required", "text"), 
									"email" 	=> array("required", "email"),
									"message" 	=> array("required", "text"));

