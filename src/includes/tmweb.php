<?php 
	global $siteName;
	$siteName = 'Lemberg Toastmasters';
	
	class RequestHelper {
		public static function getUserAction() {
			$result = '';
			if(!empty($_REQUEST['userAction'])) $result = $_REQUEST['userAction'];
			return $result;
		}
	}
?>