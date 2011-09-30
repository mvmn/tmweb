<?php
	// #### GLOBAL SETTINGS
	
	// Admin Login
	global $adminPwdMD5;
	global $adminPwdSHA256;
	global $loginFailureTimeoutSeconds;
	global $maxAdminLoginFailures;
	$adminPwdMD5 = 'aa0aa374cc1438727580a7014003f138'; // Default admin password is 70457M4573R5. hash('md5','password text'); 
	$adminPwdSHA256 = 'eac0b4eb3b01d7be73871b5001a1298f4cdf3731bf867238e71cac6c9fafb0e6';  // hash('sha256','password text');
	$loginFailureTimeoutSeconds = 1800;
	$maxAdminLoginFailures = 5;	
?>
