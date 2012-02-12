<?php
	class AdminDBHelper {
		
		public static function createTablesScript($dbTablesArray) {
			$script = '';
			foreach ($dbTablesArray as $tblKey => $table) {
				$script = $script.$table;
			}
			return $script;
		}
	};
?>
