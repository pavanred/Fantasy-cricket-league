
		<?php if(!isUserLoggedIn()) { ?>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="forgot-password.php">Forgot Password</a></li>
                <li><a href="resend-activation.php">Resend Activation Email</a></li>
            </ul>
       <?php } else { ?>
       		<ul>
				<li><a href="account.php">Home</a></li>
				<li></br></li>
				<li><b>My Fantasy Cricket</b></li>
				

				<li><a href="CreateLeague.php">Create your League</a></li>  
				<li><a href="JoinLeague.php">Join private League</a></li>  
            	<li><a href="CreateTeam.php">Create your Team</a></li>   
				<li><a href="HOF.php">Hall of Fame</a></li>	
				<li><a href="PrivateHOF.php">Private league HOF</a></li>
			
				<li></br></li>
				
				<li><b>Statistics</b></li>				
       			<li><a href="TeamStats.php">Team stats</a></li>    
                <li><a href="PlayerStats.php">Player stats</a></li>    
				<li><a href="MatchStats.php">Match stats</a></li>    
				<li></br></li>
				<li><b>My Account</b></li>				
            	        	
       			<li><a href="change-password.php">Change password</a></li>
                <li><a href="update-email-address.php">Update email id</a></li>
				<li><a href="logout.php">Logout</a></li>    
       		</ul>
       <?php } ?>
            
            <div id="build">
                <a href="http://usercake.com"><span>UserCake</span></a>
            </div>
            
