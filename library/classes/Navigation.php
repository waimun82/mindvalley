<?php
class Navigation {

	/*
	* Check navigation 
	* @param string $type - Level of navigation (1 = Main navigation, 2 = Sub-menu overlay, 3 = Sub-menu)
	* @param array $links - Script name
	* @return string
	*/
	public function checkNavigation($type, $links) {
		$links = is_array($links) ? $links : explode(",", $links);
		$navigation = false;
		foreach ($links AS $link) {
			if (preg_match ("/".str_replace(" ", "", $link)."/i", $_SERVER['PHP_SELF'])) {
				$navigation = true;
			}
		}
		switch ($type) {
			case 1: if ($navigation) { return " class=\"current\""; } else { return " class=\"select\""; } break;
			case 2: if ($navigation) { return " class=\"select_sub show\""; } else { return " class=\"select_sub\""; } break;
			case 3: if ($navigation) { return " class=\"sub_show\""; } else { return ""; } break;
		}
	}

}