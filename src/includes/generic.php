<?php 
	class ErrorResult {
		private $errorMessage;
		
		public function __construct($errorMessage) {
			$this->errorMessage = $errorMessage;
		}
		
		public function getErrorMessage() {
			return $this->errorMessage;
		}
		
		public function __toString() {
			return $this->getErrorMessage();
		}
	}
?>
