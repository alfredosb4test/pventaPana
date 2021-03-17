<?php
require_once("nusoap/lib/nusoap.php");
$wsdl = "http://codigosphp.com/alfredo/pventa/web_service/servidor.php?wsdl"; // url sel webservice
$client = new nusoap_client($wsdl,'wsdl');
$param = array('tipo' => 'b');
$necesidades = $client->call('ListarNecesidades', $param);

// Si hay Error
if ($client->fault){
	echo "<br>fault";
	echo "No se completo la operacion";
	die();
}else{	
	$error = $client->getError();
	if ($error){
		echo "<br>getError";
		echo $error;
		die();
	}
}
echo "<pre>";
print_r($necesidades);
echo "</pre>";
if(is_array($necesidades)){
	echo '<table border="1">';
	for($i = 0; $i < count($necesidades); $i++){
		echo "<tr>";
		echo "<td>".$necesidades[$i]['permiso']."</td>";
		echo "<td>".$necesidades[$i]['usuario']."</td>";
		echo "<td>".$necesidades[$i]['url']."</td>";
		echo "</tr>";
	}
	echo "</table>";
}else
	echo "Sin Registros";
?>