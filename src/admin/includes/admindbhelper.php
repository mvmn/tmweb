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
?>
