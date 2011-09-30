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
		
		public static function loadAll($className) {
			return DBPersistenceHelper::loadConditional($className, NULL);
		}
		
		public static function loadConditional($className, $whereClause) {
			$dbLink = DBPersistenceHelper::getDBConnection();
			$table = $className;
			$methods = get_class_methods($className);
			$fields = "id";
			foreach($methods as $methodName) {
				if(substr($methodName, 0, 3) === "get" && $methodName!="getId")
				$fields=$fields.", ".substr($methodName, 3);
			}
			
			$query = "select ".$fields." from ".$table;
			if(!empty($whereClause)) {
				$query = $query." WHERE ".$whereClause;
			}
			
			$queryResult = mysql_query($query, $dbLink);
			$result = array();
			if($queryResult) {
				while ($row = mysql_fetch_assoc($queryResult)) {
					$reflectionObj = new ReflectionClass($className); 
					$result[]= $reflectionObj->newInstanceArgs($row);
				}
			}
			return $result;
		}
	}
?>
