<?php

//echo "hola"; exit;
echo imagen($_FILES);

function imagen($archivo){
	$tamanio = $_FILES['userfile']['size'];
	$maxUpload = 524288; // 7978043}
	// si el archivo sobrepasa el limite de tamaño
	// o si el archivo sobrepasa el limite de tamaño especificado en del archivo php.ini regresara NULL el motor php
	if($tamanio >= $maxUpload || $tamanio == NULL){
		return 'MAX_FILE_SIZE :'.$tamanio;
		exit;
	}
	
	if ($_FILES['userfile']['error'] > 0 && $_FILES['userfile']['error'] <= 3)
	{
		switch ($_FILES['userfile']['error']) //$userfile_error
		{
			case 1: return 'MAX_FILE_SIZE :'.$tamanio;	break;
			case 2: return 'MAX_FILE_SIZE :'.$tamanio;	break;
			//case 3: $tipoProblema = 'The uploaded file was only partially uploaded. ';break;
			//case 4: echo '<h1>No file uploaded</h1></div></div>';break;
		}			
		exit;
	}
	//print_r($_POST); echo " type:".$_FILES['userfile']['type']; exit;
	//if($_FILES['userfile']['error'] == 4){ return $upfile="doc/mx_marketing/no_imagen_sys.jpg"; }
	$_FILES['userfile']['name'] = str_replace('%', '_', $_FILES['userfile']['name']);
	$_FILES['userfile']['name'] = strtolower(str_replace(' ', '_', $_FILES['userfile']['name']));
	$_FILES['userfile']['name'] = sanear_string_especiales($_FILES['userfile']['name']);
	$duplicado = buscar(basename($_FILES['userfile']['name']));
	if($duplicado == 'duplicado'){
		// renombrar el archivo y agregarle un numero aleatorio	
		srand((double) microtime() * 1000000);
		$rand_number = rand(0, 999);
		$num_rand = $rand_number;
		$_FILES['userfile']['name'] = "file_".$num_rand."_".$_FILES['userfile']['name'];		
	}	
	$nombreArchivo = $_FILES['userfile']['name'];
	// coloque el archivo donde desee
	$uploaddir = '../img_productos/';
	$upfile = $uploaddir . basename($nombreArchivo);	

	if(is_uploaded_file($_FILES['userfile']['tmp_name']))
	{
		if(!move_uploaded_file($_FILES['userfile']['tmp_name'], $upfile))
		{
			return "Problema: No se pudo copiar el archivo al directorio destino.";
			exit;
		}else
			return $nombreArchivo;
	}else{
		return "Problema: Posiblemente el archivo fue intersectado.";
		exit;
	}		
}
function buscar($file_usuario) {
	$directorio = '../img_productos/';
	$dir = opendir($directorio);
	while($file = readdir($dir)){
	  if(is_file($directorio.'/'.$file))
	  {
		$file2 = basename($file);
		if($file2 == $file_usuario)
			return 'duplicado';
	  }
	}
	closedir($dir);
}
function sanear_string_especiales($string)
{

    $string = trim($string);
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );
    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
        array("\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "< ", ";", ",", ":",
             " "),
        '',
        $string
    );

    return $string;
}
  
?>