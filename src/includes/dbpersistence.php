<?php 

	class DBPersistenceHelper {
		public static function getDBConnection() {
			global $dbHost;
			global $dbName;
			global $dbPass;
			
			$dbLink = mysql_connect($dbHost, $dbName, $dbPass);
			if($dbLink) {
				mysql_select_db($dbName);
			}
			return $dbLink;	
		}
		
		public static function loadAll($className, $orderBy) {
			return DBPersistenceHelper::loadConditional($className, NULL, $orderBy);
		}
		
		public static function loadConditional($className, $whereClause, $orderBy) {
			$dbLink = DBPersistenceHelper::getDBConnection();
			$table = $className;
			$fields = DBPersistenceHelper::getClassFields($className);

			$query = "select ".implode(", ", $fields)." from ".$table;
			if(!empty($whereClause)) {
				$query = $query." WHERE ".$whereClause;
			}
			if(!empty($orderBy)) {
				$query = $query." ORDER BY ".$orderBy;
			}
			
			global $debugMode;
			if($debugMode == true) {
				echo "<!-- DEBUG: query = ".$query." -->";
			}
						
			$queryResult = mysql_query($query, $dbLink);
			$result = array();
			if($queryResult) {
				while ($row = mysql_fetch_assoc($queryResult)) {
					$reflectionObj = new ReflectionClass($className); 
					$result[]= $reflectionObj->newInstanceArgs($row);
				}
			}
			mysql_close($dbLink);
			return $result;
		}
		
		public static function insert($instance) {
			$className = get_class($instance);
			$dbLink = DBPersistenceHelper::getDBConnection();
			
			$fields = DBPersistenceHelper::getClassFields($className);
			unset($fields[0]); // Remove ID field
			
			$query = "insert into ".$className." (".implode(", ", $fields).") values (";
			$methods = get_class_methods($className);
			foreach($methods as $methodName) {
				if(substr($methodName, 0, 3) === "get" && $methodName!="getId") {
					$value = call_user_func(array($instance, $methodName));
					if($value === NULL) { 
						$valueEscapedQuoted="NULL";
					} else {
						$valueEscapedQuoted = "'".mysql_real_escape_string($value, $dbLink)."'";
					}
					$query = $query.$valueEscapedQuoted.",";										
				}
			}
			$query = substr($query, 0, strlen($query)-1).");";
			
			global $debugMode;
			if($debugMode == true) {
				echo "<!-- DEBUG: query = ".$query." -->";
			}
			
			$queryResult = mysql_query($query, $dbLink);
			mysql_close($dbLink);
			
			return $queryResult;
		}
		
		public static function update($id, $instance) {
			$className = get_class($instance);
			$dbLink = DBPersistenceHelper::getDBConnection();
			
			$query = "update ".$className." set ";
			$methods = get_class_methods($className);
			foreach($methods as $methodName) {
				if(substr($methodName, 0, 3) === "get" && $methodName!="getId") {
					$fieldName = substr($methodName, 3);;
					$value = call_user_func(array($instance, $methodName));
					if($value === NULL) { 
						$valueEscapedQuoted="NULL";
					} else {
						$valueEscapedQuoted = "'".mysql_real_escape_string($value, $dbLink)."'";
					}
					$query = $query.$fieldName." = ".$valueEscapedQuoted.",";
				}
			}
			$query = substr($query, 0, strlen($query)-1)." where id='".mysql_real_escape_string($id, $dbLink)."';";
			
			global $debugMode;
			if($debugMode == true) {
				echo "<!-- DEBUG: query = ".$query." -->";
			}			
			$queryResult = mysql_query($query, $dbLink);
			mysql_close($dbLink);
			
			return $queryResult;
		}		
		
		public static function getClassFields($className) {
			$fields = array("id");
			$methods = get_class_methods($className);
			foreach($methods as $methodName) {
				if(substr($methodName, 0, 3) === "get" && $methodName!="getId") {
					$fields[] = substr($methodName, 3);
				}
			}
			return $fields;
		}
	}
?>
