<?php
    require_once("models/config.php");
	require_once("models/funcs.cricket.php");
	require_once("models/RGrid.php");
	
	//Prevent the user visiting the logged in page if he/she is already logged in
	if(!isUserLoggedIn()) { header("Location: login.php"); die(); }
?>

<?php

	global $db,$db_table_prefix,$db_host,$db_user,$db_pass,$db_name;	
	
	$params['hostname'] = $db_host;
    $params['username'] = $db_user;
    $params['password'] = $db_pass;
    $params['database'] = $db_name;

    $sql = GetHOFsql($Public_League);

	$grid = RGrid::Create($params, $sql);
	
	$grid->showHeaders = true;
	
	$grid->SetDisplayNames(array('tm_Name'    => 'Team',
								 'Username'   => 'Manager',
								 'tms_Points' => 'Points',
                                 ));
								 
    $grid->SetPerPage(10);	
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Hall of fame</title>
<link href="cakestyle.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="wrapper">
	<div id="content">
	
    	 <div id="left-nav">
        <?php include("layout_inc/left-nav.php"); ?>
            <div class="clear"></div>
        </div>
        
        <div id="main">
			<h1>Hall of fame</h1>
			
				<?php
				if(!empty($_POST))
				{
					if(count($errors) > 0)
					{
				?>
				<div id="errors">
				<?php errorBlock($errors); ?>
				</div>     
				<?php
					 }  
            	
        ?> 

		<?php  }?>
		
		<div id="regbox">
            <?php $grid->Display() ?>   
        </div>

		<div class="clear"></div>
	 	</div>
	</div>
</div>
</body>
</html>