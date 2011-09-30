<?php
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
	};
?>
