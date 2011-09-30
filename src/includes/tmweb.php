<?php
	// General globals 
	global $siteName;
	$siteName = 'Lemberg Toastmasters';
	// MySQL DB
	global $dbHost;
	global $dbName;
	global $dbPass;
	$dbHost = '127.0.0.1';
	$dbName = 'lvivtm';
	$dbPass = 'lviv70457';
	
	class RequestHelper {
		public static function getUserAction() {
			$result = '';
			if(!empty($_REQUEST['userAction'])) $result = $_REQUEST['userAction'];
			return $result;
		}
	}
?>
