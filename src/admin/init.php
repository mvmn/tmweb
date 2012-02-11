<?php
	session_start();
	require_once '../includes/includes.php';
	require_once './includes/includes.php';
		
	$loginInfo = AdminLogin::processRequest(); 
?>
