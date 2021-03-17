<?php
session_start(); 
?> 
<script type="text/javascript">

var error=0;
$(document).ready(function(e) {
	$('#txt_nombre').focus();
	
	$("#btn_guarda_cliente").button({ 
		text: true,
		icons: {
		  primary: "ui-icon-disk"
		}
    });		
	$("#btn_guarda_cliente").click(function(){
		error=0;
		
		$('#txt_nombre').jrumble({		
			x: 1,
			y: 1,
			rotation: .2,
			speed: 2,
			opacity: true
		}); // habilita efecto vibrar
		$codigo=$('#codigo').val();
		valida_campo2(["txt_nombre"],'','','',["txt_nombre"], ["#FF5D00"], ["#E6FACB"]);			
		if(error){	
			return;
		}
		$("#btn_guarda_cliente").hide();
		var str_post = $("form").serialize();
		//alert(str_post)
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: "crud_pventas.php",
		 data: str_post,
		 beforeSend:function(){/* $("#ajax_respuesta").html($load); */},	 
		 success: function(datos){ 
		 //alert(datos)
			var obj = jQuery.parseJSON(datos);	
			$("#ajax_respuesta").empty();	
			//alert(obj);	
			if(obj.tipo == "cliente_registrado"){
				$("input:text, textarea").attr('value','');
				$("#popup_contenido").html('<div class="msg alerta_ok">Datos Guardados</div>');
			}
			if(obj.tipo == "error_execute"){
				$("#popup_contenido").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
			}		 			  
			if(obj.tipo == "error_sql"){
				$("#popup_contenido").html('<div class="msg alerta_err">Problemas con el SQL</div>');
			} 
			$("#dialog_detalles").dialog({
				width: 900,
				resizable: false,
				show: { effect: "blind", pieces: 8, duration: 300 },
				title: "Aviso",
				close: function( event, ui ) {  
					  $("#popup_contenido").empty();
					  $("#btn_guarda_cliente").show();
				 },
				buttons: {					  
				  Aceptar: function() {
					$( this ).dialog( "close" );
				  }
				}
			});
		 },
		 timeout:90000,
		 error: function(){ 					
				$("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
			}	   
		});	
	});
});
</script>
<div id="cont_registro_prove">
  <div class="f_negro titulo_frm">
  	<div style="position:relative; top:7px;">Nuevo Cliente</div>  
  </div> 
<form>
  <input type="hidden" name="accion" id="accion" value="insert_cliente" />
  <table width="100%" border="0">
  <tr>
    <td><label for="textfield">Nombre: </label></td>
    <td><input type="text" name="nombre" id="txt_nombre" class="text_box" size="120" maxlength="200" value="" ></td>
  </tr> 
<!--  <tr>
    <td><label for="textfield">Contacto: </label></td>
    <td><input type="text" name="contacto" id="txt_contacto" class="text_box" size="50" maxlength="200" value="contacto" ></td>
  </tr> --> 
  <tr>
    <td><label for="textfield">Direccion: </label></td>
    <td><input type="text" name="dir" id="txt_direccion" class="text_box" size="120" maxlength="1000" value="" ></td>
  </tr> 
  <tr>
    <td><label for="textfield">Ciudad: </label></td>
    <td><input type="text" name="ciudad" id="txt_ciudad" class="text_box" size="50" maxlength="200" value="" ></td>
  </tr> 
  <tr>
    <td><label for="textfield">Telefono: </label></td>
    <td><input type="text" name="tel" id="txt_telefono" class="text_box" size="50" maxlength="200" value="" ></td>
  </tr> 
  <tr>
    <td><label for="textfield">Celular: </label></td>
    <td><input type="text" name="cel" id="txt_celular" class="text_box" size="50" maxlength="200" value="" ></td>
  </tr>             
  <tr>
    <td><label for="textfield">Correo: </label></td>
    <td><input type="text" name="correo" id="txt_correo" class="text_box" size="50" maxlength="200" value="" ></td>
  </tr>  
  <tr>
    <td><label for="textfield">Observacion: <br />(<span id="numtxt_obs"> 0 </span>/ 1500 ) </label></td>
    <td><label for="textarea"></label><textarea name="obs" id="txt_obs" cols="45" rows="5" onkeypress="return limita(1500,'txt_obs', event)" class="text_area"></textarea></td>
  </tr>  
  <tr>
  	<td colspan="2" align="center"><button class="" type="button" id="btn_guarda_cliente" style="width:180px;">Guardar</button></td>
  </tr>  	
</table>
</form>
</div>
<div id="dialog_detalles" style="width:90%; display:none">
    <div id="popup_contenido" style="position: relative; overflow-y: scroll; height:50px;">
</div>