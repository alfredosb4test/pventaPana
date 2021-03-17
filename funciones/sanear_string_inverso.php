<?php
function sanear_string_inverso($string)
{
    $string = trim($string);
    //Esta parte se encarga de eliminar cualquier caracter extra�o

 
    $string = str_replace(
		array('&aacute;', '&agrave;', '&auml;', '&acirc;', '&Aacute;', '&Agrave;', '&Acirc;', '&Auml;'),
        array('�', '�', '�', '�', '�', '�', '�', '�'),
        $string
    );
 
    $string = str_replace(
        array('&eacute;', '&egrave;', '&euml;', '&ecirc;', '&Eacute;', '&Egrave;', '&Ecirc;', '&Euml;'),
        array('�', '�', '�', '�', '�', '�', '�', '�'),
        $string
    );
 
    $string = str_replace(
        array('&iacute;', '&igrave;', '&iuml;', '&icirc;', '&Iacute;', '&Igrave;', '&Icirc;', '&Iuml;'),
        array('�', '�', '�', '�', '�', '�', '�', '�'),
        $string
    );
 
    $string = str_replace(
        array('&oacute;', '&ograve;', '&ouml;', '&ocirc;', '&Oacute;', '&Ograve;', '&Ocirc;', '&Ouml;'),
        array('�', '�', '�', '�', '�', '�', '�', '�'),
        $string
    );
 
    $string = str_replace(
        array('&uacute;', '&ugrave;', '&uuml;', '&ucirc;', '&Uacute;', '&Ugrave;', '&Ucirc;', '&Uuml;'),
        array('�', '�', '�', '�', '�', '�', '�', '�'),
        $string
    );
 
    $string = str_replace(
        array('&ntilde;', '&Ntilde;'),
        array('�', '�'),
        $string
    );

    return $string;
}
?>