<?php
require_once("nusoap/lib/nusoap.php");
$server = new soap_server;
//$ns = "http://localhost/Internet/scripts_php/web_service/";
$ns = "http://codigosphp.com/alfredo/pventa/web_service/";
$server->configureWSDL("miwebservice", $ns);
$server->wsdl->schematargetnamespace = $ns;

$server->wsdl->addComplexType(
	'ArregloNecesidades',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(
			array('ref' => 'SOAP-ENC:ArrayType', 'wsdl:arrayType' => 'tns:Necesidades[]')
		 ),
	'tns:Necesidades'
);
	
$server->wsdl->addComplexType('Necesidades', 'complexType', 'struct', 'all', '',
						array(
							'url' => array('name' => 'url', 'type' => 'xsd:string')
						) 	
);
/* Metodo del WEB SERVICE*/
function ListarNecesidades($tipo){
	if($tipo != ''){
				$toc[0]['url'] = "pasteleriarce.com.mx";
				$toc[1]['url'] = "codigosphp.com";
				$toc[2]['url'] = "localhost";
	}
	return $toc;
}

/* Registrando el Metodo */
$server->register('ListarNecesidades',
				  array('tipo' => 'xsd:string'),
				  array('return' => 'tns:ArregloNecesidades'),
				  $ns
);		

$rawPost = strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') == 0? (isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input")) : NULL;

$server->service($rawPost);
exit;
?>




