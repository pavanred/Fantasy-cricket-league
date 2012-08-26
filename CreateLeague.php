<?php
    require_once("models/config.php");
	require_once("models/funcs.cricket.php");
	
	//Prevent the user visiting the logged in page if he/she is already logged in
	if(!isUserLoggedIn()) { header("Location: login.php"); die(); }
?>

<?php
	if(!empty($_POST))
	{
		$errors = array();
		$league_name = trim($_POST["LeagueName"]);
		$userid = $loggedInUser->user_id;
		//$tournament_id = trim($_POST["tournaments"]);
		$date = date("Y-m-d"); 
		
		if(minMaxRange(5,25,$league_name))
		{
			$errors[] = lang("LEAGUE_USER_CHAR_LIMIT",array(5,25));
		}			
		//End data validation

		if(count($errors) == 0)
		{					
			//Attempt to add the league to the database
			if(!AddLeague($league_name,$userid,$date))
			{
				$errors[] = lang("JOIN_LEAGUE_ERROR");
			}	
		}
	}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Create Private league</title>
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
			
            <h1>Create your private league</h1>
			
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
           		 } else {
          
            	$message = lang("CREATE_LEAGUE_SUCCESS");      
            	
        ?> 
		<div id="success">
        
           <p><?php echo $message ?></p>
           
        </div>
		<?php } }?>
		
		<div id="regbox">
                <form name="CreateLeague" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                
                <p>
                    <label>League Name </label>
                    <input type="text" name="LeagueName" />
                </p>  

				<!--<p>
				<label>Tournament </label>
				<select name="tournaments">
				<?php
				 //GetTournamentList();
				?>
				</select>
				</p>  -->
				
				<p>
                    <label>&nbsp;</label>
                    <input type="submit" value="Create league"/>
               </p>			               
				
                </form>
            </div>

			<div class="clear"></div>
	 	</div>
	</div>
</div>
</body>
</html>

		
		