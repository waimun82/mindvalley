<?php
class Common {

	/*
	* Prepare string for storage
	* @param string $variable - Variable to verify
	* @return string
	*/
	public function verifyVariable($variable) {
		$variable = strip_tags($variable);
		$variable = mb_convert_encoding($variable, 'HTML-ENTITIES', 'UTF-8');
		$variable = addslashes($variable);
		return $variable;
	}

	/*
	* Prepare HTML for storage
	* @param string $variable  - Variable to verify
	* @return string
	*/
	public function verifyHTMLVariable($variable) {
		$variable = mb_convert_encoding($variable, 'HTML-ENTITIES', 'UTF-8');
		$variable = addslashes($variable);
		return $variable;
	}

}
?>