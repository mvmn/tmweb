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
	
	// Admin notification
	global $adminEmail;
	$adminEmail = 'mvmn.inbox@gmail.com'; 
	
	// #### DB STRUCTURE
	global $dbTables;
	$dbTables = array(
		"Member" => array(
			"email" => new DBField("email", "nvarchar", "1024", true, NULL),
			"pwdhash" => new DBField("pwdhash", "varchar", "1024", true, NULL),
			"contactInfo" => new DBField("contactInfo", "text", NULL, false, NULL),
			"image" => new DBField("image", "blob", NULL, false, NULL),
			"disabled" => new DBField("disabled", "boolean", NULL, true, "0")
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
?>
