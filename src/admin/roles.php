<?php
	require './init.php';
	if($loginInfo->isLoggedIn()!=true) {
		require './fragments/notloggedin.php';
	} else {
		require './fragments/head.php';
		require './fragments/topmenu.php';

		// #### START
		
		$userAction = RequestHelper::getUserAction();
		
		// #### END
		require './fragments/tail.php';
	}
	
?>
