<?php
function call($controller, $action) {    
	require_once("controllers/core.php");
	$core = new Core();
	$core->viewPage($controller, $action);
}

call($controller, $action);
?>