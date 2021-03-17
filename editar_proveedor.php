<?php
include('funciones/conexion_class.php');
$conn = new class_mysqli();
$_POST = $conn->sanitize($_POST);
?> 
<script type="text/javascript">

var error=0;
$(document).ready(function(e) {
	$('#codigo').focus();
	$("#btn_guarda_prove").click(function(){
		error=0;
		
		$('#txt_empresa, #txt_contacto').jrumble({		
			x: 1,
			y: 1,
			rotation: .2,
			speed: 2,
			opacity: true
		}); // habilita efecto vibrar
		$codigo=$('#codigo').val();
		valida_campo2(["txt_empresa", "txt_contacto"],'','','',["txt_empresa", "txt_contacto"], ["#FF5D00"], ["#E6FACB"]);			
		if(error){	
			return;
		}
		$("#btn_guarda_prove").hide();
		var str_post = $("form").serialize();
		//alert(str_post)
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: "crud_pventas.php",
		 data: str_post,
		 beforeSend:function(){/* $("#ajax_respuesta").html($load); */},	 
		 success: function(datos){ 
		// alert(datos)
			var obj = jQuery.parseJSON(datos);	
			$("#ajax_respuesta").empty();	
			//alert(obj);	
			if(obj.tipo == "prove_registrado"){
				$("input:text, textarea").attr('value','');
				$("#popup_contenido").html('<div class="msg alerta_ok">Datos Guardados</div>');
			}
			if(obj.tipo == "prove_error"){
				$("#popup_contenido").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
			}		 			  
			  
			$("#dialog_detalles").dialog({
				width: 900,
				resizable: false,
				show: { effect: "blind", pieces: 8, duration: 300 },
				title: "Aviso",
				close: function( event, ui ) {  
					  $("#popup_contenido").empty();
					  $("#btn_guarda_prove").show();
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
<form>
  <input type="hidden" name="accion" id="accion" value="insert_prove" />
  <table width="100%" border="0">
  <tr class="info">
    <td colspan="2"><div class="f_negro titulo_frm">Crear Proveedor</div></td>
  </tr>
  <tr>
    <td><label for="textfield">Empresa: </label></td>
    <td><input type="text" name="empresa" id="txt_empresa" class="text_box" size="120" maxlength="200" value="empresa" ></td>
  </tr> 
  <tr>
    <td><label for="textfield">Contacto: </label></td>
    <td><input type="text" name="contacto" id="txt_contacto" class="text_box" size="50" maxlength="200" value="contacto" ></td>
  </tr>  
  <tr>
    <td><label for="textfield">Direccion: </label></td>
    <td><input type="text" name="dir" id="txt_direccion" class="text_box" size="120" maxlength="1000" value="direccion" ></td>
  </tr> 
  <tr>
    <td><label for="textfield">Ciudad: </label></td>
    <td><input type="text" name="ciudad" id="txt_ciudad" class="text_box" size="50" maxlength="200" value="ciudad" ></td>
  </tr> 
  <tr>
    <td><label for="textfield">Telefono: </label></td>
    <td><input type="text" name="tel" id="txt_telefono" class="text_box" size="50" maxlength="200" value="telefono" ></td>
  </tr> 
  <tr>
    <td><label for="textfield">Celular: </label></td>
    <td><input type="text" name="cel" id="txt_celular" class="text_box" size="50" maxlength="200" value="celular" ></td>
  </tr>             
  <tr>
    <td><label for="textfield">Correo: </label></td>
    <td><input type="text" name="correo" id="txt_correo" class="text_box" size="50" maxlength="200" value="correo" ></td>
  </tr>  
  <tr>
    <td><label for="textfield">Observacion: <br />(<span id="numtxt_obs"> 0 </span>/ 1500 ) </label></td>
    <td><label for="textarea"></label><textarea name="obs" id="txt_obs" cols="45" rows="5" onkeypress="return limita(1500,'txt_obs', event)" class="text_area">textarea</textarea></td>
  </tr>  
  <tr>
  	<td colspan="2" align="center"><button class="button" type="button" id="btn_guarda_prove" style="width:180px; height:30px;">Guardar</button></td>
  </tr>  	
</table>
</form>
</div>
<div id="dialog_detalles" style="width:90%; display:none">
    <div id="popup_contenido" style="position: relative; overflow-y: scroll; height:50px;">
</div>