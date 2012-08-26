<?php
    require_once("models/config.php");
	require_once("models/funcs.cricket.php");
	
	//Prevent the user visiting the logged in page if he/she is already logged in
	if(!isUserLoggedIn()) { header("Location: login.php"); die(); }
?>

<?php
	if(!empty($_POST))
	{
		global $db,$db_table_prefix,$db_host,$db_user,$db_pass,$db_name;	

		$params['hostname'] = $db_host;
		$params['username'] = $db_user;
		$params['password'] = $db_pass;
		$params['database'] = $db_name;

		$team_id = trim($_POST["teams"]);
		GetTeamStatsSQL($team_id);

		if(!$sql)
		{
			$errors[] = lang("TEAM_STATS_ERROR");
		}
		
		$grid = RGrid::Create($params, $sql);
		
		$grid->showHeaders = true;
		
		$grid->SetDisplayNames(array('Name'  => 'Team',
									 'Played' => 'Played',
									 'Won' => 'Won',
									 'Lost' => 'lost',
									 'Drawn' => 'drawn',
									 'Points' => 'Points',
									 ));
									 
		$grid->SetPerPage(10);
		
		GetTeamCompositionSQL($team_id);

		if(!$sql)
		{
			$errors[] = lang("TEAM_COMPOSITION_ERROR");
		}
		
		$gridTeamPlayers = RGrid::Create($params, $sql);
		
		$gridTeamPlayers->showHeaders = true;
		
		$gridTeamPlayers->SetDisplayNames(array('Player'  => 'Team',
									 'Points' => 'Played',
									 'Value' => 'Won',									
									 ));
									 
		$gridTeamPlayers->SetPerPage(10);
		
	}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Team statistics</title>
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
			<h1>Team statistics</h1>
			
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
		
		<?php }?>
		
		<div id="regbox">
                <form name="TeamStats" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">             

				<p>
				<label>Your teams </label>
				<select name="teams">
				<?php
					GetTeamList($loggedInUser->user_id);
				?>
				</select>
				</p>  

				<p>
                    <label>&nbsp;</label>
                    <input type="submit" value="Get Stats" class="submit" />
               </p>				
			   
			   <p>
			   <?php $grid->Display() ?>   
			   </p>

				<p>
			   <?php $gridTeamPlayers->Display() ?>   
			   </p>
				
                </form>
            </div>

			<div class="clear"></div>
	 	</div>
	</div>
</div>
</body>
</html>
</html>