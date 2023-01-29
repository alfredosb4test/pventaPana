<?php
session_start();
//ini_set('error_reporting', 0);
//echo "<pre>"; print_r($_SESSION); echo "</pre>"; 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<title>Punto de Ventas</title>
 
<link href='images/esfera_1.ico' rel='shortcut icon' type='image/x-icon' />
<link rel="stylesheet" href="css/estilos4.css">
<!--[if lt IE 9]>
	<link rel="stylesheet" type="text/css" href="css/ie.css" />
<![endif]-->    
<script type="text/javascript" src="js/jquery.js"></script>
<script src="js/valida_campos_2.js"></script>


<link type="text/css" href="js/ui_azul/jquery-ui.min.css" rel="stylesheet" />
<script src="js/ui_azul/jquery-ui.min.js"></script>

<script src="js/chart_4/highcharts.js"></script>
<script src="js/chart_4/highcharts-3d.js"></script>
<script src="js/chart_4/highcharts-more.js"></script>
<script src="js/chart_4/modules/exporting.js"></script>
<script type="text/javascript" src="js/AjaxUpload.2.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="css/jquery.multiselect_style.css" />
<script type="text/javascript" src="js/jquery.multiselect.min.js"></script>

<script src="js/jquery.jrumble.1.3.min.js"></script>  
<script src="js/jquery.hotkeys.js"></script>  
<script src="js/jquery.blockUI.js "></script>
<script src="js/index.js"></script>
 
<script src="js/jquery.table2excel.min.js"></script>

</head>
<body onKeyDown="checkKey(event)">
<input type="hidden" name="txt_editar_prod_ir" id="txt_editar_prod_ir" value="" />
<div id="wrap">
    <div id="bar_top">
		<?php 
		//include ('inc_menu_top.php'); 
		?> 
    </div> 	
    <?php include ('inc_login.php'); ?>
    <div id="inc_bienvenida" style="position:relative;"></div>
	<div id="contenido">
		<div id="contenido_resul"></div>
        <?php //include ('inc_bienvenida.php'); ?>
    </div>
    
</div>
<div id="btn_capa" style="position:absolute; width:28px; height:28px;  display: none"><img src="images/btn_capa.png" border="0" ></div>
<input type="hidden" id="txt_IVA" >
<input type="hidden" id="txt_usr_nombre">
<input type="hidden" id="txt_id_sucursal">
<input type="hidden" id="txt_id_empresa">	
<input type="hidden" id="txt_sucursal">	
</body>
</html>
<?php $conn->close_mysqli(); ?>