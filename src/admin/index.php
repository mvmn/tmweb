<?php
	session_start();
	require_once './includes/tmwebadmin.php';
	require_once '../includes/tmweb.php';
		
	$loginInfo = AdminLogin::processRequest();
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<title><?php echo $siteName ?> Administration Page</title>
		<script type="text/javascript">
			function redirectToIndex() {
				window.location.href="index.php";
			}
		</script>
	</head>
	<body>
<?php 
	if($loginInfo->isLoggedIn()!=true) {
		// 1 - NOT LOGGED IN
		if($loginInfo->isLoginAttempt()) { echo 'Login failed<br/>'; }
		if($loginInfo->isIPBlocked()) { echo 'Maximum logins limit exceeded!<br/>'; } 
		?>
			<form action="index.php" method="POST">
				<input type="hidden" name="userAction" value="loginAction" />
				Password: <input type="password" name="password" />
				<input type="submit" />
			</form>
		<?php 
	} else {
		// 2 - LOGGED IN
		if($loginInfo->isLoginAttempt()) {
			// 2.1 - JUST SUBMITTED LOGIN FORM - REDIRECT AFTER POST
			?>
				Login succeeded. You will be redirected to admin pages now.<br/> 
				Nothing happens? <a href="index.php">Proceed manually</a>.
				<script type="text/javascript">
					setTimeout(function() { redirectToIndex(); }, 2000);
				</script>
			<?php 
		} else {
			// 2.2 - LOGGED IN AND REDIRECTED AFTER LOGIN FORM SUBMIT
			?>
				<a href="index.php">Home</a> |
				<a href="index.php?userAction=logoutAction">Logout</a> |
				<a href="index.php?userAction=generateScriptAction">Generate DB SQL script</a> |
				<a href="index.php?userAction=runScriptAction">Run create tables on DB</a> |
				<a href="index.php?userAction=manageMembers">Manage members</a> |
				<a href="index.php?userAction=manageRoles">Manage roles</a> |
				<a href="index.php?userAction=managePrograms">Manage speaking programs</a>
				<br/>
				<hr/>
			<?php 
			$userAction = RequestHelper::getUserAction();
			if($userAction == "") {
				$dbLink = mysql_connect($dbHost, $dbName, $dbPass);
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
				$dbLink = mysql_connect($dbHost, $dbName, $dbPass);
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
					
					foreach ($dbTables as $tblKey => $table) {
						echo 'Creating table '.$tblKey.'...<br/>';
						$script = AdminDBHelper::createTableScript($tblKey, $dbTables[$tblKey]);
						$result = mysql_query($script, $dbLink);
						if (!$result) {
    						echo 'Error occurred: '.mysql_error()."<br/>";
						}
						echo '<br/>';	
					}
					
					mysql_close($dbLink);				
				}
			} elseif($userAction == "manageMembers") {
			} elseif($userAction == "manageRoles") {
			} elseif($userAction == "managePrograms") {
				
			} else {
				?>
					Error: unknown action
				<?php 
				echo ' &quot;'.$userAction.'&quot;.<br/>';
			}
		}
	}
?>
	</body>
</html>
