<?php

	class DBTable {
		private $name;
		private $fields;
		private $indexes;
		
		public function __construct($name, $fields, $indexes) {
			$this->name = $name;
			$this->fields = $fields;
			$this->indexes = $indexes;
		}
		
		public function getName() {
			return $this->name;
		}

		public function getFields() {
			return $this->fields;
		}
	
		public function getIndexes() {
			return $this->indexes;
		}
		
		public function __toString() {
			$script =  'CREATE TABLE '.$this->getName()." (\n";
			foreach ($this->getFields() as $field) {
				$script = $script.'    '.$field.",\n";
			}	
			$script = $script."    id int not null auto_increment,\n    primary key(id)";
			if($this->getIndexes() !=null && count($this->getIndexes())>0) {
				foreach($this->getIndexes() as $index) {
					$script = $script.",\n    ".$index;
					//$script = $script.",\n    INDEX ".$index->getName()." (";
					//foreach($index->getFieldsDefs() as $fieldDef) {
					//	$script = $script.$fieldDef.", ";	
					//}
					//$script = substr($script, 0, -2).")";
				}
			}
			$script = $script."\n);\n\n";
			return $script;			
		}
	}

	class DBIndex {
		private $name;
		private $fieldsDefs;
		
		public function __construct($name, $fieldsDefs) {
			$this->name = $name;
			$this->fieldsDefs = $fieldsDefs;
		}
		
		public function getName() {
			return $this->name;
		}

		public function getFieldsDefs() {
			return $this->fieldsDefs;
		}
		
		public function __toString() {
			$result = "INDEX ".$this->name." (";
			foreach ($this->fieldsDefs as $fieldDef) {
				$result = $result.$fieldDef.', ';
			}
			$result = substr($result, 0, -2).')';
			return $result;
		}
	}
	
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
			return $this->defaultValue;
		}
	
		public function __toString() {
			$result = $this->identifier.' '.$this->type;
			if(isset($this->typeModifier) && strlen($this->typeModifier)>0) {
				$result = $result.'('.$this->typeModifier.')';
			}
			if(isset($this->notNull) && $this->notNull==true) {
				$result = $result.' NOT NULL';
			}
			if(isset($this->defaultValue)  && strlen($this->defaultValue)>0) {
				$result = $result.' DEFAULT \''.str_replace('\'','\\\'', $this->defaultValue).'\'';
			}
			return $result;
		}
	};
	
	// #### DB STRUCTURE
	global $dbTables;
	$dbTables = array(
		"Member" => new DBTable(
			"Member", 
			array(
				"firstName" => new DBField("firstName", "nvarchar", "1024", true, NULL),
				"lastName" => new DBField("lastName", "nvarchar", "1024", true, NULL),
				"email" => new DBField("email", "nvarchar", "1024", true, NULL),
				"pwdhash" => new DBField("pwdhash", "varchar", "1024", true, NULL),
				"contactInfo" => new DBField("contactInfo", "text", NULL, false, NULL),
				"image" => new DBField("image", "nvarchar", "1024", false, NULL),
				"disabled" => new DBField("disabled", "boolean", NULL, true, "0")
			),
			array(
				"emailIndex" => new DBIndex(
					"I_EMAIL",
					array("email(32)") 
				),
				"disabledIndex" => new DBIndex(
					"I_DISABLED",
					array("disabled") 
				),
				"emailIndex" => new DBIndex(
					"I_EMAIL",
					array("email(32)") 
				),
				"emailDisabledIndex" => new DBIndex(
					"I_EMAILDISABLED",
					array("email(32)", "disabled") 
				),
				"idDisabledIndex" => new DBIndex(
					"I_IDDISABLED",
					array("id", "disabled") 
				),
				"lastNameIndex" => new DBIndex(
					"I_LASTNAME",
					array("lastName(16)") 
				)
			)
		),
		"Meeting" => new DBTable(
			"Meeting", 
			array(
				"dateandtime" => new DBField("dateandtime", "datetime", NULL, true, NULL),
				"place" => new DBField("place", "text", NULL, false, NULL)
			),
			null
		),
		"Role" => new DBTable(
			"Role", 
			array(
				"name" => new DBField("name", "nvarchar", "1024", true, NULL),
				"description" => new DBField("description", "text", NULL, false, NULL),
				"iconone" => new DBField("iconone", "nvarchar", "1024", true, NULL),
				"iconten" => new DBField("iconten", "nvarchar", "1024", true, NULL)
			),
			null
		),
		"SpeechesProgram" => new DBTable(
			"SpeechesProgram",
			array(
				"name" => new DBField("name", "nvarchar", "1024", true, NULL),
				"shortname" => new DBField("shortname", "nvarchar", "8", true, NULL),
				"description" => new DBField("description", "text", NULL, false, NULL)
			),
			null
		),
		"SpeechProject" => new DBTable(
			"SpeechProject",
			array(
				"speechProgramId" => new DBField("speechProgramId", "int", NULL, true, NULL),
				"description" => new DBField("description", "text", NULL, false, NULL),
				"speechTime" => new DBField("speechTime", "nvarchar", "1024", false, NULL),
				"projectNumber" => new DBField("projectNumber", "smallint", NULL, false, NULL)
			),
			null
		),
		"RoleParticipation" => new DBTable(
			"RoleParticipation",
			array(
				"meetingId" => new DBField("meetingId", "int", NULL, true, NULL),
				"roleId" => new DBField("roleId", "int", NULL, true, NULL),
				"memberId" => new DBField("memberId", "int", NULL, true, NULL),
				"remarks" => new DBField("remarks", "text", NULL, false, NULL)
			),
			null
		),
		"SpeechDelivery" => new DBTable(
			"SpeechDelivery",
			array(
				"meetingId" => new DBField("meetingId", "int", NULL, true, NULL),
				"memberId" => new DBField("memberId", "int", NULL, true, NULL),
				"speechProjectId" => new DBField("speechProjectId", "int", NULL, true, NULL),
				"remarks" => new DBField("remarks", "text", NULL, false, NULL)
			),
			null
		)
	);

	// !!! IMPORTANT NOTICE: 
	// - Order of constructor parameters MUST match order of getter methods
	// - Names of getter methods MUST match get<DB Field Name> pattern
	// - Names of classses MUST match names of corresponding DB tables
	class Member {
		private $id;
		private $firstName;
		private $lastName;
		private $email;
		private $pwdHash;
		private $contactInfo;
		private $disabled;
		private $image;

		public function __construct($id, $firstName, $lastName, $email, $pwdHash, $contactInfo, $disabled, $image) {
			$this->id = $id;
			$this->firstName = $firstName;
			$this->lastName = $lastName;
			$this->email = $email;	
			$this->pwdHash = $pwdHash;
			$this->contactInfo = $contactInfo;
			$this->disabled = $disabled;
			$this->image = $image;	
		}
		
		public function getId() { return $this->id; }
		public function getFirstName() { return $this->firstName; }
		public function getLastName() { return $this->lastName; }
		public function getEmail() { return $this->email; }
		public function getPwdHash() { return $this->pwdHash; }
		public function getContactInfo() { return $this->contactInfo; }
		public function getDisabled() { return $this->disabled; }
		public function getImage() { return $this->image; }
	};
	
	class Meeting {
		private $id;
		private $dateAndTime;
		private $place;
	
		public function __construct($id, $dateAndTime, $place) {
			$this->id = $id;
			$this->dateAndTime = $dateAndTime;
			$this->place = $place;
		}
		
		public function getId() { return $this->id; }
		public function getDateAndTime() { return $this->dateAndTime; }
		public function getPlace() { return $this->place; }
	};
	
	class Role {
		private $id;
		private $name;
		private $description;
		private $iconOne;
		private $iconTen;
	
		public function __construct($id, $name, $description, $iconOne, $iconTen) {
			$this->id = $id;
			$this->name = $name;
			$this->description = $description;
			$this->iconOne = $iconOne;
			$this->iconTen = $iconTen;
		}
	
		public function getId() { return $this->id; }
		public function getName() { return $this->name; }
		public function getDescription() { return $this->description; }
		public function getIconOne() { return $this->iconOne; }
		public function getIconTen() { return $this->iconTen; }
	
	};
	
	class SpeechesProgram {
		private $id;
		private $name;
		private $shortName;
		private $description;
	
		public function __construct($id, $name, $shortName, $description) {
			$this->id = $id;
			$this->name = $name;
			$this->shortName = $shortName;
			$this->description = $description;
		}
		
		public function getId() { return $this->id; }
		public function getName() { return $this->name; }
		public function getShortName() { return $this->shortName; }
		public function getDescription() { return $this->description; }
	
	};
	
	class SpeechProject {
		private $id;
		private $speechProgramId;
		private $description;
		private $speechTime;
		private $projectNumber;
	
		public function __construct($id, $speechProgramId, $description, $speechTime, $projectNumber) {
			$this->id = $id;
			$this->speechProgramId = $speechProgramId;
			$this->description = $description;
			$this->speechTime = $speechTime;
			$this->projectNumber = $projectNumber;
		}
	
		public function getId() { return $this->id; }
		public function getSpeechProgramId() { return $this->speechProgramId; }
		public function getDescription() { return $this->description; }
		public function getSpeechTime() { return $this->speechTime; }
		public function getProjectNumber() { return $this->projectNumber; }
	
	};
	
	class RoleParticipation {
		private $id;
		private $meetingId;
		private $roleId;
		private $memberId;
		private $remarks;
	
		public function __construct($id, $meetingId, $roleId, $memberId, $remarks) {
			$this->id = $id;
			$this->meetingId = $meetingId;
			$this->roleId = $roleId;
			$this->memberId = $memberId;
			$this->remarks = $remarks;
		}
	
		public function getId() { return $this->id; }
		public function getMeetingId() { return $this->meetingId; }
		public function getRoleId() { return $this->roleId; }
		public function getMemberId() { return $this->memberId; }
		public function getRemarks() { return $this->remarks; }
	};
	
	class SpeechDelivery {
		private $id;
		private $meetingId;
		private $speechProjectId;
		private $remarks;
		private $memberId;
	
		public function __construct($id, $meetingId, $speechProjectId, $remarks, $memberId) {
			$this->id = $id;
			$this->meetingId = $meetingId;
			$this->speechProjectId = $speechProjectId;
			$this->remarks = $remarks;
			$this->memberId = $memberId;
		}
		
		public function getId() { return $this->id; }
		public function getMeetingId() { return $this->meetingId; }
		public function getMemberId() { return $this->memberId; }
		public function getSpeechProjectId() { return $this->speechProjectId; }
		public function getRemarks() { return $this->remarks; }
	};
?>
