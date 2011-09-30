<?php
	session_start();
	require_once '../includes/tmweb.php';
	require_once './includes/tmwebadmin.php';
		
	$loginInfo = AdminLogin::processRequest(); 
?>
