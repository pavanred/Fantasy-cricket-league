<?php


	function GetTournamentList()
	{
		global $db,$db_table_prefix,$Tournament_To_Begin;
		
		$sql = "SELECT trn_id as tournament_id,trn_Name as tournament_name
				FROM ".$db_table_prefix."Tournament
				WHERE
				trn_trstatus_id =".$db->sql_escape(sanitize($Tournament_To_Begin))."";

		$result = mysql_query ($sql); 

		while($nt=mysql_fetch_array($result))
		{	//Array or records stored in $nt 
			echo '<option value='.$nt[tournament_id].'>'.$nt[tournament_name].'</option>'; 
		}
	}
	
	function GetLeagueList($UserId)
	{
		global $db,$db_table_prefix,$Tournament_To_Begin,$Public_League;

		$sql = "SELECT lg_id as league_id,lg_Name as league_name FROM ".$db_table_prefix."League WHERE lg_id NOT IN 
		(SELECT DISTINCT lgMbr_lg_id FROM ".$db_table_prefix."LeagueMember where lgMbr_UserID = ".$UserId.") AND lg_id <> ".$Public_League."";


			//echo '<div>'.$sql.'</div>';	
		$result = mysql_query ($sql);


		 while($nt=mysql_fetch_array($result))
		 {	//Array or records stored in $nt 
			 echo '<option value='.$nt[league_id].'>'.$nt[league_name].'</option>'; 
		 }
		 $result = null;
	}
	
	function AddLeague($league_name,$userid,$date)
	{
		global $db,$db_table_prefix,$db_user,$db_pass;
			
		mysql_query("begin"); 
		//insert the user into the database providing no errors have been found.
		  $sql = "insert into `".$db_table_prefix."league` (
				 `lg_name`,
				 `lg_userid`,
				 `lg_points`,
				 `lg_createdate`				
				 )
				 values (
				 '".$db->sql_escape($league_name)."',
				 '".$db->sql_escape($userid)."',
				 '0',
				 '".$db->sql_escape($date)."')";
				
		 mysql_query($sql);		
		 $lastid = mysql_insert_id (); ;
		 
		$sql = "INSERT INTO `".$db_table_prefix."LeagueMember` (
				`lgMbr_lg_id`,
				`lgMbr_Userid`,
				`lgMbr_JoinDate`	
				)
				VALUES (
				'".$db->sql_escape($lastid)."',
				'".$db->sql_escape($userid)."',
				'".$db->sql_escape($date)."')";
				
		mysql_query($sql);		
		if (mysql_error())
			{
				mysql_query("rollback");
				return 0;
			}
		else 
		{
			mysql_query("commit"); 			
			return 1;
		}
	}
	
	function JoinLeague($league_id,$date,$userid)
	{
		global $db,$db_table_prefix;
			

		//Insert the user into the database providing no errors have been found.
		$sql = "INSERT INTO `".$db_table_prefix."LeagueMember` (
				`lgMbr_lg_id`,
				`lgMbr_Userid`,
				`lgMbr_JoinDate`	
				)
				VALUES (
				'".$db->sql_escape($league_id)."',
				'".$db->sql_escape($userid)."',
				'".$db->sql_escape($date)."')";
				
		return mysql_query($sql);			

	}
	
	function GetHOFsql($LeagueId)
	{
		global $db_table_prefix;
	
		$sql = "SELECT `tm_Name`,`Username`,`tms_Points`
				 FROM ".$db_table_prefix."`Team` 
				 INNER JOIN ".$db_table_prefix."`Users` ON `tm_Userid` = `User_id`
				 INNER JOIN ".$db_table_prefix."`TeamStats` ON tm_id = tms_tm_id
				 WHERE tm_lg_id = ".$LeagueId." ORDER BY `tms_Points` DESC;";
				 
		//$sql = "SELECT user_id as tm_Name, username as Username, email as tms_Points from users";
	 
		return $sql;
	}
	
	
	function GetTeamList($teamId)
	{
		global $db,$db_table_prefix;

		$sql = "SELECT tm_name as Team_Name, tms_Played as Played, tms_Won as Won, tms_Lost as Lost, tms_drawn as Drawn, tms_Points as Points  from ".$db_table_prefix."`TeamStats` INNER JOIN ".$db_table_prefix."`TeamStats` ON tm_id = tms_tm_id
				where `tm_id` = ".$teamId.";";


			//echo '<div>'.$sql.'</div>';	
		$result = mysql_query ($sql);


		 while($nt=mysql_fetch_array($result))
		 {	//Array or records stored in $nt 
			 echo '<option value='.$nt[Team_id].'>'.$nt[Team_name].'</option>'; 
		 }
		 $result = null;
	}
	
	function GetTeamStats($teamid)
	{
	    global $db,$db_table_prefix;
		
		$sql = "SELECT tm_Name as team_name";
	}
	
	function GetTeamCompositionSQL($teamid)
	{
		global $db,$db_table_prefix;
		
		$sql = "Select query with Pivot";
		
	}
	
	
	function GetAllPlayers()
	{
		global $db,$db_table_prefix;

		$sql = "SELECT pl_id, pl_Name from ".$db_table_prefix."`Player`";
			//+ pl_Points + pl_Value

			//echo '<div>'.$sql.'</div>';	
		$result = mysql_query ($sql);


		 while($nt=mysql_fetch_array($result))
		 {	//Array or records stored in $nt 
			 echo '<option value='.$nt[pl_id].'>'.$nt[pl_Name].'</option>'; 
		 }
		 $result = null;
	}
	
	function GetRemainingValue()
	{
		global $db,$db_table_prefix,$TeamSpendingLimit;

		$RemainingValue = $TeamSpendingLimit;
		echo $RemainingValue;
		//echo '<Label> Remaining Value($) '.$RemainingValue.'</label>';
	}
?>