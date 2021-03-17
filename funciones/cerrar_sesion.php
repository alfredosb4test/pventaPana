<?php 
session_start();
if ($_SESSION["g_nombre"]) {
	$_SESSION = array();
	$session_name = session_name();
	// Para borrar las cookies asociadas a la sesión
	// Es necesario hacer una petición http para que el navegador las elimine
	if ( isset( $_COOKIE[ $session_name ] ) ) {
		if ( setcookie(session_name(), '', time()-3600, '/') ) {
			session_destroy();  
		}
	}
}
//print_r($_SESSION);
?>