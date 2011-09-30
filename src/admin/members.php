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
			$members = DBPersistenceHelper::loadConditional("Member", "disabled = 0");
			if(!$members)  { 
				echo mysql_error();
			} else {
				foreach($members as $member) {
					echo 'Member: '.$member->getEmail().'<br/>';
				}
			}
		} elseif($userAction == "addMember") {
			
		} elseif($userAction == "editMember") {
			
		}
		
		// #### END
		require './fragments/tail.php';
	}
	
?>
