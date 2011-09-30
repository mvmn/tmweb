<?php
	// #### GLOBAL SETTINGS
	// MySQL DB
	global $dbName;
	global $dbPass;
	$dbName = 'lvivtm';
	$dbPass = 'lviv70457';
	// Admin Login
	global $adminPwdMD5;
	global $adminPwdSHA256;
	global $loginFailureTimeoutSeconds;
	global $maxAdminLoginFailures;
	$adminPwdMD5 = 'aa0aa374cc1438727580a7014003f138'; // Default admin password is 70457M4573R5. hash('md5','password text'); 
	$adminPwdSHA256 = 'eac0b4eb3b01d7be73871b5001a1298f4cdf3731bf867238e71cac6c9fafb0e6';  // hash('sha256','password text');
	$loginFailureTimeoutSeconds = 1800;
	$maxAdminLoginFailures = 5;
	// Admin notification
	global $adminEmail;
	$adminEmail = 'mvmn.inbox@gmail.com'; 	
	
	// #### DB Structure
	class DBField {
	
		private $type;
		private $typeModifier;
		private $identifier;
		private $notNull;
		private $defaultValue;
		
		public function __construct($identifier, $type, $typeModifier, $notNull, $defaultValue) {
			$this->identifier = $identifier;
			$this->type = $type;
			$this->typeModifier = $typeModifier;
			$this->notNull = $notNull;
			$this->defaultValue = $defaultValue;
		}
	
		public function getType() {
			return $this->type;
		}
	
		public function getTypeModifier() {
			return $this->typeModifier;
		}
	
		public function getIdentifier() {
			return $this->identifier;
		}
	
		public function getNotNull() {
			return $this->notNull;
		}
	
		public function getDefaultValue() {
			return $this->defaultValue();
		}
	
		public function __toString() {
			$result = $this->identifier.' '.$this->type;
			if(!empty($this->typeModifier)) {
				$result = $result.'('.$this->typeModifier.')';
			}
			if(!empty($this->notNull)) {
				$result = $result.' NOT NULL';
			}
			if(!empty($this->defaultValue)) {
				$result = $result.' DEFAULT(\''.str_replace('\'','\\\'', $this->defaultValue).'\')';
			}
			return $result;
		}
	};
	
	$tables = array(
		"Member" => array(
			"email" => new DBField("email", "nvarchar", "1024", true, NULL),
			"pwdhash" => new DBField("pwdhash", "varchar", "1024", true, NULL),
			"contactInfo" => new DBField("contactInfo", "text", NULL, false, NULL),
			"image" => new DBField("image", "blob", NULL, false, NULL)
		),
		"Meeting" => array(
			"dateandtime" => new DBField("dateandtime", "datetime", NULL, true, NULL),
			"place" => new DBField("place", "text", NULL, false, NULL)
		),
		"Role" => array(
			"name" => new DBField("name", "nvarchar", "1024", true, NULL),
			"description" => new DBField("description", "text", NULL, false, NULL),
			"iconone" => new DBField("iconone", "blob", NULL, false, NULL),
			"iconten" => new DBField("iconten", "blob", NULL, false, NULL)
		),
		"SpeechesProgram" => array(
			"name" => new DBField("name", "nvarchar", "1024", true, NULL),
			"shortname" => new DBField("shortname", "nvarchar", "8", true, NULL),
			"description" => new DBField("description", "text", NULL, false, NULL)
		),
		"SpeechProject" => array(
			"speechProgramId" => new DBField("speechProgramId", "int", NULL, true, NULL),
			"description" => new DBField("description", "text", NULL, false, NULL),
			"speechTime" => new DBField("speechTime", "nvarchar", "1024", false, NULL),
			"projectNumber" => new DBField("projectNumber", "smallint", NULL, false, NULL)
		),
		"RoleParticipation" => array(
			"meetingId" => new DBField("meetingId", "int", NULL, true, NULL),
			"roleId" => new DBField("roleId", "int", NULL, true, NULL),
			"memberId" => new DBField("memberId", "int", NULL, true, NULL),
			"remarks" => new DBField("remarks", "text", NULL, false, NULL)
		),
		"SpeechDelivery" => array(
			"meetingId" => new DBField("meetingId", "int", NULL, true, NULL),
			"memberId" => new DBField("memberId", "int", NULL, true, NULL),
			"speechProjectId" => new DBField("speechProjectId", "int", NULL, true, NULL),
			"remarks" => new DBField("remarks", "text", NULL, false, NULL)
		)
	);
	
	class DBHelper {
		
		public static function createTablesScript($dbFieldsArraysArray) {
			$script = '';
			foreach ($dbFieldsArraysArray as $tblKey => $table) {
				$script = $script.DBHelper::createTableScript($tblKey, $dbFieldsArraysArray[$tblKey]);
			}
			return $script;
		}
		
		public static function createTableScript($tableName, $dbFieldArray) {
			$script =  'CREATE TABLE '.$tableName." (\n";
			foreach ($dbFieldArray as $fldKey => $field) {
				$script = $script.'    '.$dbFieldArray[$fldKey].",\n";
			}	
			$script = $script."    id int not null auto_increment,\n    primary key(id)\n);\n\n";
			return $script;
		}
	};
	
	class AdminLogin {
		
		private $loginFailuresCount;
		private $loginAttempt;
		private $loggedIn;
		private $ipBlocked;
		
		private function __construct($loggedIn, $loginAttempt, $loginFailuresCount, $ipBlocked) {
			$this->loggedIn = $loggedIn;
			$this->loginAttempt = $loginAttempt;
			$this->loginFailuresCount = $loginFailuresCount;
			$this->ipBlocked = $ipBlocked;
		}
		
		public function isLoggedIn() {
			return $this->loggedIn;
		}
		
		public function isLoginAttempt() {
			return $this->loginAttempt;
		}
		
		public function getLoginFailuresCount() {
			return $this->loginFailuresCount;
		}
		
		public function isIPBlocked() {
			return $this->ipBlocked;
		}
		
		public static function processRequest() {
			global $siteName;
			global $adminPwdMD5;
			global $adminPwdSHA256;
			global $loginFailureTimeoutSeconds;
			global $maxAdminLoginFailures;
			global $adminEmail;
						
			$reqIp = $_SERVER['REMOTE_ADDR'];
			
			echo $GLOBALS['adminLoginFailuresCount_'.$reqIp];
			
			$loginFailures = 0;
			if(!empty($GLOBALS['adminLoginFailuresCount_'.$reqIp])) { // FIXME: GLOBALS aren't application scope! PHP sucks )-:
				$loginFailures = $GLOBALS['adminLoginFailuresCount_'.$reqIp];
				if($loginFailureTimeoutSeconds>0 && !empty($GLOBALS['adminLoginLastFailureTime_'.$reqIp])) {
					$lastFailureTime = $GLOBALS['adminLoginLastFailureTime_'.$reqIp];
					if(time()-$lastFailureTime>$loginFailureTimeoutSeconds) {
						$loginFailures = 0;
						$GLOBALS['adminLoginFailuresCount_'.$reqIp] = 0;
					}
				}
			}
									
			$loginAttempt = false;
			if(RequestHelper::getUserAction() == 'loginAction' && !empty($_REQUEST['password'])) {
				$loginAttempt = true;
				if($loginFailures < $maxAdminLoginFailures) {
					$pwdParam = $_REQUEST['password'];
					$pwdParamMD5 = hash('md5',$pwdParam);
					$pwdParamSHA256 = hash('sha256',$pwdParam);
					
					if($pwdParamMD5 == $adminPwdMD5 && $pwdParamSHA256 == $adminPwdSHA256) {
						$_SESSION['userIsAdmin'] = true;
					} else {
						$_SESSION['userIsAdmin'] = false;
						$loginFailures += 1;
						$GLOBALS['adminLoginFailuresCount_'.$reqIp] = $loginFailures;
						$GLOBALS['adminLoginLastFailureTime_'.$reqIp] = time();
						 
						if($loginFailures >= $maxAdminLoginFailures) {
							try {
								mail($adminEmail, $siteName.' admin login failures limit reached.', 'Attempts: '.$loginFailures.'; IP: '.$reqIp);
							} catch (Exception $e) {}
						}
					}
				}
			} elseif(RequestHelper::getUserAction() == 'logoutAction') {
				$_SESSION = array();
				$loginFailures = 0;
			}
			
			$ipBlocked = false;
			if($loginFailures>=$maxAdminLoginFailures) {
				$ipBlocked = true;
			}
			
			$loggedIn = false;
			if(!empty($_SESSION['userIsAdmin'])) {
				$loggedIn = $_SESSION['userIsAdmin'];
			}	

			return new AdminLogin($loggedIn, $loginAttempt, $loginFailures, $ipBlocked);
		}
	};

?>
