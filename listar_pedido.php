<?php
session_start(); 
//echo "<pre>"; print_r($array_cliente); echo "</pre>"; 
?> 
<script type="text/javascript">
var error=0;
$(document).ready(function(e) {
	//$('#txt_buscar_cliente').focus();
	$("#f_inicial").datepicker(
		{   
		  dateFormat: 'yy-mm-dd',	
		  showAnim: "drop",
		  showOn: "button",
		  buttonImage: "images/calendario.png",
		  buttonImageOnly: true,
		  changeMonth: true,
		  numberOfMonths: 1,
		  onClose: function( selectedDate ) {
			$( "#f_final" ).datepicker( "option", "minDate", selectedDate );
		  }
	});	
	$("#f_final").datepicker(
		{   
		  dateFormat: 'yy-mm-dd',	
		  showAnim: "drop",
		  showOn: "button",
		  buttonImage: "images/calendario.png",
		  buttonImageOnly: true,
		  changeMonth: true,
		  numberOfMonths: 1,
		  onClose: function( selectedDate ) {
			$( "#f_inicial" ).datepicker( "option", "maxDate", selectedDate );
		  }
	});	
	//****************************************************** HISTORIAL VENTAS POR FECHAS
	$( "#btn_buscar_pedidos" ).button({ 
		text: true,
    });		
	$('#btn_buscar_pedidos').click(function(){
		  $fecha = $("#f_inicial").val();
		  $fecha2 = $("#f_final").val();
		  $rdo_estatus = $(".rdo_estatus:radio[checked='checked']").val();
		 // alert($rdo_estatus)
		  $.ajax({
		   type: "POST",
		   contentType: "application/x-www-form-urlencoded", 
		   url: 'crud_pventas.php',
		   data: "accion=ver_pedidos&fecha="+$fecha+"&fecha2="+$fecha2+"&estatus="+$rdo_estatus,
		   beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		   success: function(datos){ 
		   
				$("#ajax_pedidos_usr").html(datos);
				$("#ajax_respuesta").empty();								 							 			
		   },
		   timeout:90000,
		   error: function(){ 					
				  $("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
			  }	   
		  });					  	
	});
/**********************************************************************************************************/
/****************************************   Autocompleta Cliente   ***********************************************/
/**********************************************************************************************************/
	$("#txt_nombre_cliente").autocomplete({
		source: "crud_pventas.php?accion=autocompleta_pedido_cliente",
		focus: function( event, ui ) {
			$("#txt_nombre_cliente").val(ui.item.nombre);
			return false;
		},				
		//appendTo: '#menu-container',
		minLength: 1,						
		select: function (event, ui) {				
			//alert(ui.item.nombre)
		    $rdo_estatus = $(".rdo_estatus:radio[checked='checked']").val();
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded", 
			 url: 'crud_pventas.php',
			 data: "accion=ver_pedidos&id_cliente="+ui.item.id_cliente+"&estatus="+$rdo_estatus,
			 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
			 success: function(datos){ 
			 
				  $("#ajax_pedidos_usr").html(datos);
				  $("#ajax_respuesta").empty();								 							 			
			 },
			 timeout:90000,
			 error: function(){ 					
					$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
				}	   
			});						
		}
	});	
	//****************************************************** HISTORIAL VENTAS POR ID
	$( "#btn_buscar_no" ).button({ 
		text: true,
    });			
	$('#btn_buscar_no').click(function(){
		  $txt_no_pedido = $("#txt_no_pedido").val(); 
		  $rdo_estatus = $(".rdo_estatus:radio[checked='checked']").val();
		  //alert($fecha)
		  $.ajax({
		   type: "POST",
		   contentType: "application/x-www-form-urlencoded", 
		   url: 'crud_pventas.php',
		   data: "accion=ver_pedidos&no_pedido="+$txt_no_pedido+"&estatus="+$rdo_estatus,
		   beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		   success: function(datos){ 
		   
				$("#ajax_pedidos_usr").html(datos);
				$("#ajax_respuesta").empty();								 							 			
		   },
		   timeout:90000,
		   error: function(){ 					
				  $("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
			  }	   
		  });					  	
	});	
});
/**********************************************************************************************************/
/****************************************   COBRAR PEDIDO   ***********************************************/
/**********************************************************************************************************/
function cobro_pedido($id_pedido){
	$("#popup_contenido").html("");
	$("#dialog_pedidos_cobrar").dialog({
		width: 600,
		resizable: false,
		show: { effect: "blind", pieces: 8, duration: 10 },
		title: "Agregar Pago",
		close: function( event, ui ) {  
			  $("#popup_contenido").empty();
			  $("#err_cantidad").empty();
			  $("#txt_cantidad").attr("value","");			  
		 },
		buttons: {	
		  Cancelar: function() {
			$( this ).dialog( "close" );
		  },  
		  Guardar: function() {
			  error=0; 
			  $txt_cantidad = $("#txt_cantidad").val();
			  if(!($.isNumeric($txt_cantidad))){
				  $("#err_cantidad").html('<div class="msg alerta_err">Cantidad no valida.</div>');
				  return;
			  }
			  valida_campo2(["txt_cantidad"],'','','',["txt_cantidad"], ["#FF5D00"], ["#E6FACB"]);			
			  if(error){	
				  return;
			  }				  
			  $.ajax({
			   type: "POST",
			   contentType: "application/x-www-form-urlencoded", 
			   url: 'crud_pventas.php',
			   data: "accion=add_pago_pedido&id_pedido="+$id_pedido+"&anticipos="+$txt_cantidad,
			   beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
			   success: function(datos){
				      //alert(datos);	
					  var obj = jQuery.parseJSON(datos);	
					  $("#ajax_respuesta").empty();	
					  	
					  if(obj.tipo == "update_registrado"){
						  $("#txt_cantidad").attr("value","");
						  $("#popup_contenido").html('<div class="msg alerta_ok">Datos Guardados</div>');
						  $("#dialog_pedidos_cobrar").dialog( "close" );
						  if(obj.estatus == "cerrado"){
							  $("#btn_cobrar_"+$id_pedido).hide();
							  $("#btn_cancelar_"+$id_pedido).hide();								  
						  }else{
							  $("#txt_no_pedido").attr("value",$id_pedido);
							  $('#btn_buscar_no').click();
						  }
					  }
					  if(obj.tipo == "error_execute"){
						  $("#popup_contenido").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
					  }		 			  
					  if(obj.tipo == "error_sql"){
						  $("#popup_contenido").html('<div class="msg alerta_err">Problemas con el SQL</div>');
					  } 
					  if(obj.tipo == "error_parametros"){
						  $("#popup_contenido").html('<div class="msg alerta_err">Problemas con el SQL: parametros</div>');
					  } 					  					  						 							 			
			   },
			   timeout:90000,
			   error: function(){ 					
					  $("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el servidor intente de nuevo.</div>');
				  }	   
			  });
		  }	  			  
		}		
	});
	//alert($id_pedido);
}
/**********************************************************************************************************/
/****************************************     VER PAGOS     ***********************************************/
/**********************************************************************************************************/
function ver_pagos($id_pedido){
	$.ajax({
	 type: "POST",
	 contentType: "application/x-www-form-urlencoded", 
	 url: 'crud_pventas.php',
	 data: "accion=ver_pagos&id_pedido="+$id_pedido,
	 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
	 success: function(data){	
			//alert(data)
			var obj = jQuery.parseJSON(data);	
			$("#ajax_respuesta").empty();	
			if(!data){
				$("#popup_ver_pagos").html('<div class="msg alerta_err">Sin Registros.</div>');
			} 
			
			
/*					  $("#popup_ver_pagos").append('<table cellpadding="0" cellspacing="0" border="1" class="ver_pagos" width="98%">');
				$("#popup_ver_pagos").append("<tr>");
					$("#popup_ver_pagos").append("<th>Empleado</th>");
					$("#popup_ver_pagos").append("<th>Cantidad</th>");
				$("#popup_ver_pagos").append("</tr>");*/
			var tabla = '<table cellpadding="1" cellspacing="1" border="0"  width="98%" class="f_caja_histoHead">';
				tabla += '<caption></caption>';
				tabla += '<thead>';
				tabla += '<tr class="f_negro">';
				tabla += '<th>Empleado</th><th>Fecha</th><th>Cantidad</th>';
				tabla += '</tr>';
				tabla += '</thead>';
				tabla += '<tbody>';
				tr = '';						  
			$.each(obj, function(entryIndex, entry){	
				tr += '<tr>';
				tr += '<td>'+entry['nombre']+'</td><td>'+entry['fecha']+'</td><td align="right">$'+entry['anticipo']+'</td>';
				tr += '</tr>';
/*								$("#popup_ver_pagos").append("<tr>");
						  $("#popup_ver_pagos").append("<td>"+entry['nombre']+"</td>");
						  $("#popup_ver_pagos").append("<td>"+entry['anticipo']+"</td>");
					  $("#popup_ver_pagos").append("</tr>");	*/
				  
				  //$("#popup_ver_pagos").append($resultado);
			});
			
			tabla += tr;
			tabla += '</tbody></table>';
			$('#popup_ver_pagos').html( tabla );		  
			//$("#popup_ver_pagos").append("</table>");	  
																			  
	 },
	 timeout:90000,
	 error: function(){ 					
			$("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el servidor intente de nuevo.</div>');
		}	   
	});	
	$("#popup_contenido").html("");
	$("#dialog_ver_pagos").dialog({
		width: 700,
		resizable: false,
		show: { effect: "blind", pieces: 8, duration: 10 },
		title: "Historial de Pagos",
		close: function( event, ui ) {  
			  $("#popup_ver_pagos").empty();			  
		 },
		buttons: {			  
		  Aceptar: function() {			  
		  		$( this ).dialog( "close" ); 
		  }
		}		
	});
	//alert($id_pedido);
}
function cerrar_pedido($id_pedido){
	
	$("#popup_cerrar_pagos").html("");
	$("#dialog_cerrar_pagos").dialog({
		width: 430,
		resizable: false,
		show: { effect: "blind", pieces: 8, duration: 10 },
		title: "Cerrar Pago",
		close: function( event, ui ) {  
			  $("#popup_cerrar_pagos").empty();
			  $( this ).dialog( "close" ); 			  
		 },
		buttons: {			  
		  Aceptar: function() {			  
		  		//alert($id_pedido)
				$.ajax({
				 type: "POST",
				 contentType: "application/x-www-form-urlencoded", 
				 url: 'crud_pventas.php',
				 data: "accion=cerrar_pedido&id_pedido="+$id_pedido,
				 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
				 success: function(datos){	
				 //alert(datos)
						var obj = jQuery.parseJSON(datos);	
						$("#ajax_respuesta").empty();	
						//alert(obj);	
						if(obj.tipo == "update_registrado"){
							$("#cerrar_pedido_"+$id_pedido).html("cerrado").removeClass('hand, hover_rojo').addClass('t_naranja_fuerte , negritas').attr("onclick","");
							$("#dialog_cerrar_pagos").dialog( "close" ); 	
							$("#btn_cobrar_"+$id_pedido).hide();
							$("#btn_cancelar_"+$id_pedido).hide();							
						}
						if(obj.tipo == "error_execute"){
							$("#popup_cerrar_pagos").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
						}		 			  
						if(obj.tipo == "error_sql"){
							$("#popup_cerrar_pagos").html('<div class="msg alerta_err">Problemas con el SQL</div>');
						} 
						if(obj.tipo == "error_parametros"){
							$("#popup_cerrar_pagos").html('<div class="msg alerta_err">Problemas con el SQL: parametros</div>');
						} 					  					  						 							 			
				 },
				 timeout:90000,
				 error: function(){ 					
						$("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el servidor intente de nuevo.</div>');
					}	   
				});				
		  }
		}		
	});	
}
function cancelar($id_pedido){
	$("#popup_cancelar_pagos").html("");
	$("#dialog_cancelar_pagos").dialog({
		width: 430,
		resizable: false,
		show: { effect: "blind", pieces: 8, duration: 10 },
		title: "Cancelar Pago",
		close: function( event, ui ) {  
			  $("#popup_cancelar_pagos").empty();
			  $( this ).dialog( "close" ); 			  
		 },
		buttons: {			  
		  Aceptar: function() {			  
		  		$txt_obs = $("#txt_obs").val();
				$.ajax({
				 type: "POST",
				 contentType: "application/x-www-form-urlencoded", 
				 url: 'crud_pventas.php',
				 data: "accion=cancelar_pedido&id_pedido="+$id_pedido+"&obs_cancelado="+$txt_obs,
				 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
				 success: function(datos){	
				 //alert(datos)
						var obj = jQuery.parseJSON(datos);	
						$("#ajax_respuesta").empty();	
						//alert(obj);	
						if(obj.tipo == "update_registrado"){
							$("#cerrar_pedido_"+$id_pedido).html("cancelado").removeClass('hand, hover_rojo').addClass('t_naranja_fuerte , negritas').attr("onclick","");
							$("#btn_cobrar_"+$id_pedido).hide();
							$("#btn_ver_pedidos_"+$id_pedido).hide();
							$("#btn_cancelar_"+$id_pedido).hide();			
							$("#dialog_cancelar_pagos").dialog( "close" ); 	
						}
						if(obj.tipo == "error_execute"){
							$("#popup_cancelar_pagos").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
						}		 			  
						if(obj.tipo == "error_sql"){
							$("#popup_cancelar_pagos").html('<div class="msg alerta_err">Problemas con el SQL</div>');
						} 
						if(obj.tipo == "error_parametros"){
							$("#popup_cancelar_pagos").html('<div class="msg alerta_err">Problemas con el SQL: parametros</div>');
						} 					  					  						 							 			
				 },
				 timeout:90000,
				 error: function(){ 					
						$("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el servidor intente de nuevo.</div>');
					}	   
				});				
		  }
		}		
	});	
}
function regresar_deposito($id_pedido, $id, $nombre){
	//alert($id); return;
	$("#popup_deposito").html("Pagar este Deposito: "+$nombre+" ?");
	$("#dialog_deposito").dialog({
		width: 430,
		resizable: false,
		show: { effect: "blind", pieces: 8, duration: 10 },
		title: "Pagar Deposito",
		close: function( event, ui ) {  
			  $("#popup_deposito").empty();
			  $( this ).dialog( "close" ); 			  
		 },
		buttons: {			  
		  Aceptar: function() {
				$.ajax({
				 type: "POST",
				 contentType: "application/x-www-form-urlencoded", 
				 url: 'crud_pventas.php',
				 data: "accion=pagar_deposito&id="+$id+"&id_pedido="+$id_pedido,
				 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
				 success: function(datos){	
				 //alert(datos)
						var obj = jQuery.parseJSON(datos);	
						$("#ajax_respuesta").empty();	
						//alert(obj);	
						if(obj.tipo == "update_registrado"){		
							$("#serv_"+$id).attr("onclick","").removeClass('hand f_resalta_azul').append('<span class="font_15 t_gris t_italic"> *Deposito pagado al Cliente</span>');
							$("#dialog_deposito").dialog( "close" ); 	
						}
						if(obj.tipo == "error_execute"){
							$("#popup_deposito").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
						}		 			  
						if(obj.tipo == "error_sql"){
							$("#popup_deposito").html('<div class="msg alerta_err">Problemas con el SQL</div>');
						} 
						if(obj.tipo == "error_parametros"){
							$("#popup_deposito").html('<div class="msg alerta_err">Problemas con el SQL: parametros</div>');
						} 					  					  						 							 			
				 },
				 timeout:90000,
				 error: function(){ 					
						$("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el servidor intente de nuevo.</div>');
					}	   
				});				
		  }
		}		
	});	
}
</script>
 
<div id="dialog_pedidos_cobrar" style="display:none">
	<strong>Atiende: </strong><span><?=$_SESSION['g_nombre'];?></span><br />
    <table border="0">
    <tr>
		<td>Cantidad:<input type="text"  class="text_box" id="txt_cantidad" size="15" /></td>
        <td><div id="err_cantidad"></div></td>
    </tr>
    </table>    
    <div id="popup_contenido"></div>
</div>

<div id="dialog_ver_pagos" style="width:90%; display:none;"> 
    <div id="popup_ver_pagos" style="height:300px; font-size:16px;"></div>
</div>
<div id="dialog_cerrar_pagos" style="width:90%; display:none; font-size:16px;"> 
	Desea cerrar este pedido?
    <div id="popup_cerrar_pagos" style="height:30px; font-size:16px;"></div>
</div>
<div id="dialog_cancelar_pagos" style="width:90%; display:none; font-size:16px;"> 
	Desea cancelar este pedido?
    <br />
    Motivo (<span id="numtxt_obs"> 0 </span>/ 1500 ):
	<textarea name="obs" id="txt_obs" cols="35" rows="5" onkeypress="return limita(1500,'txt_obs', event)" class="text_area"></textarea>
    <div id="popup_cancelar_pagos" style="height:30px; font-size:16px;"></div>
</div>
<div id="dialog_deposito" style="width:90%; display:none; font-size:16px;"> 
    <div id="popup_deposito" style="height:30px; font-size:16px;"></div>
</div>


<div class="f_negro titulo_frm">
  <div style="position:relative; top:7px;">Ver Pedido</div>  
</div> 
<div style="position:relative; margin-top:15px;">


	<div style="position:relative;float:left;">  
	<div class="" style="position: relative; margin: 1px 0 0 5px; width:100px; " align="center">Fecha</div>
    <div id="cont_frm_fecha" class="div_redondo_azul" style="position:relative; margin: 5px 0 0 10px; height:32px; float:left; background:#FFFFFF; ">        
        <input type="text" name="f_inicial" id="f_inicial" size="6" maxlength="140" style="height:24px; margin-left:0;" />
        <input type="text" name="" id="f_final" size="6" maxlength="140" style="height:24px; margin-left:0;" />
        
        <button class="button" type="button" id="btn_buscar_pedidos" style="width:40px; height:30px;"><img src="images/lupa.png"  style="margin:-1px 0 0 -6px;"/> </button>        
    </div> 
    </div>  
    
    <div style="position:relative;float:left;">  
    <div class="" style="position: relative; margin: 1px 0 0 5px; width:100px; " align="center">Cliente</div>
    <div id="cont_frm_nombre" class="div_redondo_azul" style="position:relative; margin: 5px 0 0 10px; height:32px; float:left;"> 
        <input type="text" class="text_box" id="txt_nombre_cliente" size="30" />
    </div>
    </div>
    
    <div style="position:relative;float:left;"> 
    <div class="" style="position: relative; margin: 1px 0 0 5px;  width:100px; " align="center">Folio</div> 
    <div id="cont_frm_nombre" class="div_redondo_azul" style="position:relative; margin: 5px 0 0 10px; height:32px; float:left;"> 
        <input type="text" class="text_box" id="txt_no_pedido" size="9" />
        <button class="button" type="button" id="btn_buscar_no" style="width:40px; height:30px;"><img src="images/lupa.png" style="margin:-1px 0 0 -6px;" /></button>
    </div> 
    </div>
<?php // if($_SESSION['g_nivel'] == "admin"): ?>
    <div style="position:relative;float:left;"> 
    <div class="" style="position: relative; margin: 1px 0 0 5px;  width:100px; " align="center">Estatus</div> 
    <div id="cont_frm_nombre" class="div_redondo_azul" style="position:relative; margin: 5px 0 0 10px; height:32px; float:left;"> 
        <input type="radio" name="rdo_estatus" class="rdo_estatus" value="abierto" checked="checked" />Abiertos
        <input type="radio" name="rdo_estatus" class="rdo_estatus" value="cerrado" />Cerrados
        <input type="radio" name="rdo_estatus" class="rdo_estatus" value="cancelado" />Cancelados
    </div> 
    </div> 
<?php //endif ?>       
</div>

<div id="ajax_pedidos_usr" style="position: relative; width:100%; height: auto; margin-top:100px; clear:both;"></div>


