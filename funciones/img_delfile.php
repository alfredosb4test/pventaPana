<?php
$file=trim($_POST['file']);
	if($borrar = unlink('../img_productos/'.$file))
		echo "Archivo Eliminado";
	else
		echo "Error al eliminar el archivo";	
?>