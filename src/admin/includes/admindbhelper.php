<?php
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
			if(isset($this->notNull)) {
				$result = $result.' NOT NULL';
			}
			if(isset($this->defaultValue)  && strlen($this->defaultValue)>0) {
				$result = $result.' DEFAULT \''.str_replace('\'','\\\'', $this->defaultValue).'\'';
			}
			return $result;
		}
	};

	class AdminDBHelper {
		
		public static function createTablesScript($dbFieldsArraysArray) {
			$script = '';
			foreach ($dbFieldsArraysArray as $tblKey => $table) {
				$script = $script.AdminDBHelper::createTableScript($tblKey, $dbFieldsArraysArray[$tblKey]);
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
		
		public static function insertMember($dbLink, $email, $pwdhash, $contacts, $picture) {
			$query = 'insert into Member (email, pwdhash, contactInfo, image) values (\''
				.mysql_real_escape_string($email, $dbLink).'\', \''
				.mysql_real_escape_string($pwdhash, $dbLink).'\', \''
				.mysql_real_escape_string($contacts, $dbLink).'\', \''
				.mysql_real_escape_string($picture, $dbLink).'\');';
			return mysql_query($query, $dbLink);
		}
		
		public static function updateMember($dbLink, $memberId, $email, $pwdhash, $contacts, $picture) {
			$query = 'update Member set ';
			$commaNeeded = false;
			if(isset($email)) {
				$query = $query.' email = \''.mysql_real_escape_string($email, $dbLink).'\'';
				$commaNeeded = true;
			}
			if(isset($pwdhash)) {
				if($commaNeeded) $query = $query.', ';
				$query = $query.' pwdhash = \''.mysql_real_escape_string($pwdhash, $dbLink).'\'';
				$commaNeeded = true;
			}
			if(isset($contacts)) {
				if($commaNeeded) $query = $query.', ';
				$query = $query.' contactInfo = \''.mysql_real_escape_string($contacts, $dbLink).'\'';
				$commaNeeded = true;
			}
			if(isset($picture)) {
				if($commaNeeded) $query = $query.', ';
				$query = $query.' image = \''.mysql_real_escape_string($picture, $dbLink).'\'';
			}
			$query = $query.' where id = '.mysql_real_escape_string($memberId, $dbLink);
			return mysql_query($query, $dbLink);
		}
	};
?>
