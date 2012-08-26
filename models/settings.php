<?php
	/*
		UserCake Version: 1.4
		http://usercake.com
		
		Developed by: Adam Davis
	*/

	//General Settings
	//--------------------------------------------------------------------------
	
	//Database Information
	$dbtype = "mysql"; 
	$db_host = "localhost";
	$db_user = "fcadmin";
	$db_pass = "fcadmin";
	$db_name = "azharcs_fc";
	$db_port = "";
	$db_table_prefix = "";

	$langauge = "en";
	
	//Generic website variables
	$websiteName = "Fantasy Cricket";
	$websiteUrl = "http://pavankumar.info/FantasyCricket/"; //including trailing slash

	//Do you wish UserCake to send out emails for confirmation of registration?
	//We recommend this be set to true to prevent spam bots.
	//False = instant activation
	//If this variable is falses the resend-activation file not work.
	$emailActivation = false;

	//In hours, how long before UserCake will allow a user to request another account activation email
	//Set to 0 to remove threshold
	$resend_activation_threshold = 1;
	
	//Tagged onto our outgoing emails
	$emailAddress = "noreply@fantasycricket.com";
	
	//Date format used on email's
	$emailDate = date("l \\t\h\e jS");
	
	//Directory where txt files are stored for the email templates.
	$mail_templates_dir = "models/mail-templates/";
	
	$default_hooks = array("#WEBSITENAME#","#WEBSITEURL#","#DATE#");
	$default_replace = array($websiteName,$websiteUrl,$emailDate);
	
	//Display explicit error messages?
	$debug_mode = false;
	
	$Tournament_To_Begin = 1;  //according to value in database
	$Public_League = 1;  //according to value in database
	$TeamSpendingLimit = 1000000;
	
	//---------------------------------------------------------------------------
?>