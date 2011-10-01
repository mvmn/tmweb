<?php
	require './includes/init.php';
	if($loginInfo->isLoggedIn()!=true) {
		require './fragments/notloggedin.php';
	} else {
		require './fragments/head.php';
		require './fragments/topmenu.php';

		// #### START
		?>
			<a href="members.php">List members</a> | 
			<a href="members.php?userAction=addMember">Add member</a>
			<br/>
			<br/>
		<?php 		
		$userAction = RequestHelper::getUserAction();
		if($userAction == "") {
			$members = DBPersistenceHelper::loadAll("Member", "id");
			if($members instanceof ErrorResult) { 
				echo "Error: ".$members->getErrorMessage();
			} else {
				echo '<table border="1" cellpadding="2" cellspacing="0"><thead><tr><td>Fist Name</td><td>Last Name</td><td>E-mail</td><td>Contacts</td><td>Disabled</td><td>Actions</td></tr></thead><tbody>';
				foreach($members as $member) {
					echo '<tr><td>'.$member->getFirstName().'</td>';
					echo '<td>'.$member->getLastName().'</td>';
					echo '<td>'.$member->getEmail().'</td>';
					echo '<td>'.$member->getContactInfo().'</td>';
					echo '<td><input type="checkbox" disabled="true" '.($member->getDisabled()!=0?'checked="checked"':'').' /></td>';
					echo '<td><a href="members.php?userAction=editMember&memberId='.$member->getId().'">edit</a></td>';
					echo '</tr>';
										
				}
				echo '</tbody></table>';
			}
		} elseif($userAction == "addMember") {
			?>
				<form action="members.php" method="POST">
					<input type="hidden" name="userAction" value="submitNewMember" />
					E-mail: <input type="text" name="email" value="" /><br/>
					Password: <input type="password" name="password" value="" /><br/>
					First name: <input type="text" name="firstName" value="" /><br/>
					Last name: <input type="text" name="lastName" value="" /><br/>
					Contact info:<br/>
					<textarea name="contactInfo"></textarea><br/>
					<input type="submit" value="Save" />
				</form>
				
			<?php
		} elseif($userAction == "submitNewMember" || $userAction == "submitMember") {
			$memberId = 0;
			$parFirstName = $_REQUEST['firstName'];
			$parLastName = $_REQUEST['lastName'];
			$parEmail = $_REQUEST['email'];
			$parPassword = $_REQUEST['password'];
			$parContact = $_REQUEST['contactInfo'];
			$pwdHash = hash('md5', $parPassword);
			$disabled = false;
			// TODO: validation
			$valid = true;
			if($userAction == "submitMember") {
				$valid = false;
				if(!empty($_REQUEST['memberId']) && preg_match('/^\d+$/', $_REQUEST['memberId'])) {
					$memberId = $_REQUEST['memberId'];
					$valid = true;
				}
				if(!empty($_REQUEST['disabled']) && $_REQUEST['disabled']!='false') {
					$disabled = true;
				}
				if(empty($parPassword)) {
					$pwdHash = $_REQUEST['pwdhash'];
				}
			}
			if($valid) {
				$memberData = new Member($memberId, $parFirstName, $parLastName, $parEmail, $pwdHash, $parContact, $disabled, NULL);
				if($memberId>0) {
					$result = DBPersistenceHelper::update($memberId, $memberData);
				} else {
					$result = DBPersistenceHelper::insert($memberData);
				}
				if($result instanceof ErrorResult) {
					echo "Error occurred: ".$result->getErrorMessage();
				} elseif($result == 1) {
					echo "Data saved.";
				} else {
					echo "Unknown error. ";
				}
			} else {
				echo "Invalid data.";
			}
			?>
 				<br/><br/>
				Redirecting to Manage members page.<br/>
				<a href="members.php">Proceed manually</a>.
				<script type="text/javascript">
					setTimeout(function() { window.location.href="members.php"; }, 2000);
				</script>				
			<?php 
		} elseif($userAction == "editMember") {
			$memberId = $_REQUEST['memberId'];
			if(!empty($memberId) && preg_match('/^\d+$/', $memberId)) {
				$members = DBPersistenceHelper::loadConditional("Member", "id=".$memberId, NULL);
				if($members instanceof ErrorResult) {
					echo "Error occurred: ".$members->getErrorMessage();
				} elseif ($members) {
					if(count($members)==1) {
						$member = $members[0];
						if($member) {
							?>
								<form action="members.php" method="POST">
									<input type="hidden" name="userAction" value="submitMember" />
									<input type="hidden" name="memberId" value="<?php echo htmlspecialchars($member->getId()); ?>" />
									E-mail: <input type="text" name="email" value="<?php echo htmlspecialchars($member->getEmail()); ?>" /><br/>
									Password: <input type="password" name="password" value="" /><br/>
									<input type="hidden" name="pwdhash" value="<?php echo htmlspecialchars($member->getPwdHash()); ?>" />
									First name: <input type="text" name="firstName" value="<?php echo htmlspecialchars($member->getFirstName()); ?>" /><br/>
									Last name: <input type="text" name="lastName" value="<?php echo htmlspecialchars($member->getLastName()); ?>" /><br/>
									Contact info:<br/>
									<textarea name="contactInfo"><?php echo htmlspecialchars($member->getContactInfo()); ?></textarea><br/>
									Disabled: <input type="checkbox" name="disabled" <?php if($member->getDisabled()!=0) { echo 'checked="checked"'; } ?> /><br/>
									<input type="submit" value="Save" />
								</form>
							<?php
						} else {
							echo "Error occurred: member not found.";
						}
					} else {
						echo "Error occurred: 1 member expected, ".count($members)." found.";
					}
				} else {
					echo "Error occurred: no results returned. ";
				}
			} else {
				echo "Error: memberId parameter invalid.";
			} 
		}
		
		// #### END
		require './fragments/tail.php';
	}
	
?>
