<?php
function sanear_string_inverso($string)
{
    $string = trim($string);
    //Esta parte se encarga de eliminar cualquier caracter extraño

 
    $string = str_replace(
		array('&aacute;', '&agrave;', '&auml;', '&acirc;', '&Aacute;', '&Agrave;', '&Acirc;', '&Auml;'),
        array('á', 'à', 'ä', 'â', 'Á', 'À', 'Â', 'Ä'),
        $string
    );
 
    $string = str_replace(
        array('&eacute;', '&egrave;', '&euml;', '&ecirc;', '&Eacute;', '&Egrave;', '&Ecirc;', '&Euml;'),
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        $string
    );
 
    $string = str_replace(
        array('&iacute;', '&igrave;', '&iuml;', '&icirc;', '&Iacute;', '&Igrave;', '&Icirc;', '&Iuml;'),
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        $string
    );
 
    $string = str_replace(
        array('&oacute;', '&ograve;', '&ouml;', '&ocirc;', '&Oacute;', '&Ograve;', '&Ocirc;', '&Ouml;'),
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        $string
    );
 
    $string = str_replace(
        array('&uacute;', '&ugrave;', '&uuml;', '&ucirc;', '&Uacute;', '&Ugrave;', '&Ucirc;', '&Uuml;'),
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        $string
    );
 
    $string = str_replace(
        array('&ntilde;', '&Ntilde;'),
        array('ñ', 'Ñ'),
        $string
    );

    return $string;
}
?>