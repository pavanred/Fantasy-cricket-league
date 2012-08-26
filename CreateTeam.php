<?php
    require_once("models/config.php");
	require_once("models/funcs.cricket.php");
	require_once("models/RGrid.php");
	
	//Prevent the user visiting the logged in page if he/she is already logged in
	if(!isUserLoggedIn()) { header("Location: login.php"); die(); }
?>

<?php

	
	
	
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Create your team</title>
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
			
            <h1>Create your team</h1>
			
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
          
            	$message = lang("CREATE_TEAM_SUCCESS");      
            	
        ?> 
		<div id="success">
        
           <p><?php echo $message ?></p>
           
        </div>
		<?php } }?>
		
		<div id="regbox">
                <form name="CreateLeague" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

                <p>
                    <label>Team Name </label>
                    <input type="text" name="TeamName" />
                </p>  

		<p>
                    <label>Remaining Value($) </label>
                    <input type="text" name="TeamName" disabled="true" value = <?php GetRemainingValue(); ?>/>
                </p> 

		<p>
		
		<select multiple size="11" name="AllPlayers">
		<?php
			GetAllPlayers();
		?>
		</select>
		<select multiple size="11" name="AllPlayers">
		<?php

		?>
		</select>
		
		</p>
		
		<p>
                    &nbsp;&nbsp;<input type="submit" name="Add" value="-->"/>&nbsp;&nbsp;
                    <input type="submit" name="Remove" value="<--"/>
               </p>

		
		<p>
                    <label>&nbsp;</label>
                    <input type="submit" name="create" value="Create team"/>
               </p>

                </form>
            </div>

			<div class="clear"></div>
	 	</div>
	</div>
</div>
</body>
</html>