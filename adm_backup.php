<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
 

$conn->close_mysqli();
//echo "<pre>"; print_r($array); echo "</pre>"; 
?> 
<script type="text/javascript">
var error=0;
$(document).ready(function(e) { 
	// Evento Backup
	$("#btn_respaldo").button({ 
		text: true,
		icons: {
		  primary: "ui-icon-plusthick"
		}
    }).click(function( event ) {
 
		$.ajax({
					   type: "POST",
					   contentType: "application/x-www-form-urlencoded", 
					   url: 'crud_pventas.php',
					   data: "accion=backup",
					   beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
					   success: function(datos){ 	
						$(".resplados").show();		
						$icono = '<img src="images/iconos_documentos/rar.png">';	   		
						$(".backup_zip").html('<a href="'+datos+'">'+$icono+'</a>');							
					   },
					   timeout:90000,
					   error: function(){ 					
							  $("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
						  }	   
					  });			 
	});
});
</script>

 
<div id="dialog_detalles" style="width:250px; display:none">
    <div id="popup_contenido" style="position: relative; height: auto"></div>
</div>
<div class="centerdiv">
	<button class=""  id="btn_respaldo" style="width:200px;">Respaldos</button>
</div>
<div class="resplados hide" style="width: 360px;margin: 0 auto;">
	<br>
	<div >
		<table width="100%">
			<tr>
				<td><h3>Respaldo creado correctamente</h3></td>
				<td class="backup_zip"></td>
			</tr>
		</table>
	</div>
</div>



  
 
<div class="growlUI" style="cursor: default; display: none;">
            <h1>Empleado Insertado</h1>
</div>
<style>
.centerdiv{
	margin: 0 auto;
	width:200px;
	margin-top: 90px;
}
</style>