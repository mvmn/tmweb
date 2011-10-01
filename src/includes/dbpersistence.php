<?php 

	class DBPersistenceHelper {
		public static function getDBConnection() {
			global $dbHost;
			global $dbName;
			global $dbPass;
			
			$result = mysql_pconnect($dbHost, $dbName, $dbPass);
			if($result) {
				$db_selected = mysql_select_db($dbName, $result);
				if (!$db_selected) {
					$result = new ErrorResult(mysql_error($result));
				}
			} else {
				$result = new ErrorResult(mysql_error());
			}
			return $result;	
		}
		
		public static function loadAll($className, $orderBy) {
			return DBPersistenceHelper::loadConditional($className, NULL, $orderBy);
		}
		
		public static function loadConditional($className, $whereClause, $orderBy) {
			$result = NULL;
			$dbLink = DBPersistenceHelper::getDBConnection();
			if(!$dbLink instanceof ErrorResult) {
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
				if($queryResult) {
					$result = array();
					while ($row = mysql_fetch_assoc($queryResult)) {
						$reflectionObj = new ReflectionClass($className); 
						$result[]= $reflectionObj->newInstanceArgs($row);
					}
				} else {
					$result = new ErrorResult(mysql_error($dbLink));
				}
				mysql_close($dbLink);
			} else {
				$result = $dbLink;
			}
			return $result;
		}
		
		public static function insert($instance) {
			$result = NULL;
			$className = get_class($instance);
			$dbLink = DBPersistenceHelper::getDBConnection();
			if(!$dbLink instanceof ErrorResult) {
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
				if($queryResult) {
					$result = $queryResult;
				} else {
					$result = new ErrorResult(mysql_error($dbLink));
				}
				mysql_close($dbLink);
			} else {
				$result = $dbLink;
			}
			return $result;
		}
		
		public static function update($id, $instance) {
			return DBPersistenceHelper::updateSN($id, $instance, false);
		}
		
		public static function updateSN($id, $instance, $skipNull) {
			$result = NULL;
			$className = get_class($instance);
			$dbLink = DBPersistenceHelper::getDBConnection();
			if(!$dbLink instanceof ErrorResult) {
				$query = "update ".$className." set ";
				$methods = get_class_methods($className);
				foreach($methods as $methodName) {
					if(substr($methodName, 0, 3) === "get" && $methodName!="getId") {
						$fieldName = substr($methodName, 3);;
						$value = call_user_func(array($instance, $methodName));
						if($value === NULL) {
							if($skipNull===true) continue;
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
				if($queryResult) {
					$result = $queryResult;
				} else {
					$result = new ErrorResult(mysql_error($dbLink));
				}
				mysql_close($dbLink);
			} else {
				$result = $dbLink;
			}
			return $result;
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
