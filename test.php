<?php 

/* ob_start(); // Turn on output buffering
system('getmac /NH'); //Execute external program to display output
 
$mycom=ob_get_contents(); // Capture the output into a variable
 
ob_clean(); // Clean (erase) the output buffer

$mac=trim(substr($mycom,0,19)); // Get Physical Address
 
echo $mac;


echo "<br>".$g_ip = $_SERVER['REMOTE_ADDR']; 
echo "<br>".$HTTP_HOST = $_SERVER['HTTP_HOST']; 
echo "<br>".$nombre_host = gethostbyaddr($g_ip); */


set_time_limit(120);
$localhost='localhost';
$usr='root';
$pwd='';
$db_name='pventa_almacen';

$fecha = date("Ymd-His"); //Obtenemos la fecha y hora para identificar el respaldo
 
// Construimos el nombre de archivo SQL Ejemplo: mibase_20170101-081120.sql
$salida_sql = $db_name.'_'.$fecha.'.sql'; 


//Comando para genera respaldo de MySQL, enviamos las variales de conexion y el destino

echo " ".$dump = 'F:\xampp\mysql\bin\mysqldump.exe --force --compress --disable-keys --user=root --password="" --lock-tables --databases pventa_almacen > '.$salida_sql;
$output = array();
exec($dump, $output, $error_code); //Ejecutamos el comando para respaldo
echo "\n test  1 ";

var_dump($output);
echo "<br>";
var_dump($error_code);

$zip = new ZipArchive(); //Objeto de Libreria ZipArchive

//Construimos el nombre del archivo ZIP Ejemplo: mibase_20160101-081120.zip
$salida_zip = $db_name.'_'.$fecha.'.zip';

if($zip->open($salida_zip,ZIPARCHIVE::CREATE)===true) { //Creamos y abrimos el archivo ZIP
    $zip->addFile($salida_sql); //Agregamos el archivo SQL a ZIP
    $zip->close(); //Cerramos el ZIP
    //unlink($salida_sql); //Eliminamos el archivo temporal SQL
    //header ("Location: $salida_zip"); // Redireccionamos para descargar el Arcivo ZIP
} else {
    echo 'Error'; //Enviamos el mensaje de error
}

?>

