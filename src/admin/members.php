<?php
	require './includes/init.php';
	if($loginInfo->isLoggedIn()!=true) {
		require './fragments/notloggedin.php';
	} else {
		require './fragments/head.php';
		require './fragments/topmenu.php';

		// #### START
		
		$userAction = RequestHelper::getUserAction();
		if($userAction == "") {
			
		} elseif($userAction == "addMember") {
			
		} elseif($userAction == "editMember") {
			
		}
		
		// #### END
		require './fragments/tail.php';
	}
	
?>
