<?php

function __autoload($name) {
	global $libraryDirectory;
	
	// search local
	includeRekursiv(realpath('.'), $name);
	$libraryDirectory = (is_array($libraryDirectory)) ? $libraryDirectory : array($libraryDirectory);
	foreach($libraryDirectory as $curLib) {
	    includeRekursiv($curLib, $name);
	}
}

function includeRekursiv($dir, $name) {
	if (file_exists("$dir/$name.php")) {
		include_once "$dir/$name.php";
		return true;
	}
	$found = false;
	$handle = dir($dir);
	while ($curObj = $handle->read()) {
		if (substr($curObj, 0, 1) != '.') { // very important (skip back, cur and hidden files)
			$curObj = "$dir/$curObj";
			if (is_dir($curObj)) {
				if (includeRekursiv($curObj, $name)) {
					$found = true; 
					break;
				}
			}
		}
	}
	return $found;
}

?>