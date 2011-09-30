<?php 
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