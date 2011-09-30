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
				<a href="index.php?userAction=logoutAction">Logout</a> |
				<a href="index.php?userAction=generateScriptAction">DB Tables SQL script</a> 
				<br/>
				<hr/>
			<?php 
			$userAction = RequestHelper::getUserAction();
			if($userAction == "generateScriptAction") {
				echo str_replace(" ", "&nbsp;", str_replace("\n","<br/>", DBHelper::createTablesScript($tables)));
			} elseif($userAction == "somethingElse") {
				
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
