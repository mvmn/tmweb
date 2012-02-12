<?php
	require './init.php';
	require './fragments/head.php';

	if($loginInfo->isLoggedIn()!=true) {
		// 1 - NOT LOGGED IN
		if($loginInfo->isLoginAttempt()) { echo 'Login failed<br/>'; }
		if($loginInfo->isIPBlocked()) { echo 'Maximum logins limit exceeded!<br/>'; }
		$proceedUrl = "index.php";
		if(isset($_REQUEST['proceedUrl']) && strlen($_REQUEST['proceedUrl'])>0) {
			$proceedUrl = $_REQUEST['proceedUrl'];
		}
		?>
			<form action="index.php" method="POST">
				<input type="hidden" name="userAction" value="loginAction" />
				<input type="hidden" name="proceedUrl" value="<?php echo $proceedUrl ?>" />
				Password: <input type="password" name="password" />
				<input type="submit" />
			</form>
		<?php 
	} else {
		// 2 - LOGGED IN
		if($loginInfo->isLoginAttempt()) {
			// 2.1 - JUST SUBMITTED LOGIN FORM - REDIRECT AFTER POST
			$proceedUrl = "index.php";
			if(isset($_REQUEST['proceedUrl']) && strlen($_REQUEST['proceedUrl'])>0) {
				$proceedUrl = $_REQUEST['proceedUrl'];
			}
			?>
				Login succeeded. You will be redirected to admin pages now.<br/> 
				Nothing happens? <a href="<?php echo $proceedUrl ?>">Proceed manually</a>.
				<script type="text/javascript">
					setTimeout(function() { window.location.href="<?php echo $proceedUrl ?>"; }, 2000);
				</script>
			<?php 
		} else {
			// 2.2 - LOGGED IN AND REDIRECTED AFTER LOGIN FORM SUBMIT
			require './fragments/topmenu.php'; 
			?>
				<a href="index.php?userAction=testDBConnection">Test DB connection</a> | 
				<a href="index.php?userAction=generateScriptAction">Generate DB SQL script</a> |
				<a href="index.php?userAction=runScriptAction">Run create tables on DB</a> <br/>
				<br/>
			<?php 
			$userAction = RequestHelper::getUserAction();
			if($userAction == "testDBConnection") {
				$dbLink = mysql_connect($dbHost, $dbUser, $dbPass);
				if (!$dbLink) {
				    echo 'Could not connect to database: '.mysql_error().'.';
				    echo '<br/><br/>';
				} else {
					echo 'DB connection ok.';
					echo '<br/><br/>';
					mysql_close($dbLink);
				}
			} elseif($userAction == "generateScriptAction") {
				echo str_replace(" ", "&nbsp;", str_replace("\n","<br/>", AdminDBHelper::createTablesScript($dbTables)));
			} elseif($userAction == "runScriptAction") {
				$dbLink = mysql_connect($dbHost, $dbUser, $dbPass);
				if (!$dbLink) {
				    echo 'Could not connect to database: '.mysql_error().'.';
				    echo '<br/><br/>';
				} else {
					echo 'Connected to DB successfully.';
					echo '<br/>';
					echo 'Switching DB to &quot;'.$dbName.'&quot;.<br/>';
					if(!mysql_select_db($dbName)) {
						echo 'Error occurred: '.mysql_error()."<br/>";
					}
					echo '<br/>';
					
					foreach ($dbTables as $dbTable) {
						echo 'Creating table '.$dbTable->getName().'...<br/>';
						$script = $dbTable;
						$result = mysql_query($script, $dbLink);
						if (!$result) {
    						echo 'Error occurred: '.mysql_error()."<br/>";
						}
						echo '<br/>';	
					}
					
					mysql_close($dbLink);				
				}
			} elseif($userAction == "") {	
			} else {
				?>
					Error: unknown action
				<?php 
				echo ' &quot;'.$userAction.'&quot;.<br/>';
			}
		}
	}
	
	require './fragments/tail.php';
?>
