<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
?> 
<script type="text/javascript">

var error=0;
var $id_cont = 1;
$(document).ready(function(e) {
	$('#txt_cliente').focus();

    $( "#txt_fecha_inicio" ).datepicker({
	  dateFormat: 'yy-mm-dd',	
	  showAnim: "drop",
	  showOn: "button",
	  buttonImage: "images/calendario.png",
	  buttonImageOnly: true,			
 
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( "#txt_fecha_final" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#txt_fecha_final" ).datepicker({
	  dateFormat: 'yy-mm-dd',	
	  showAnim: "drop",
	  showOn: "button",
	  buttonImage: "images/calendario.png",
	  buttonImageOnly: true,			
 
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( "#txt_fecha_inicio" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
 
	$( "#btn_guarda_pedido" ).button({ 
		text: true,
		icons: {
		  primary: "ui-icon-disk"
		}
    });	
	$("#btn_guarda_pedido").click(function(){
		error=0;
		$("#alert_cliente").html('');
		$('#txt_fecha_inicio, #txt_fecha_final, #txt_total, #txt_cliente, #txt_anticipo').jrumble({		
			x: 1,
			y: 1,
			rotation: .2,
			speed: 2,
			opacity: true
		}); // habilita efecto vibrar
		$codigo=$('#codigo').val();
		valida_campo2(["txt_cliente","txt_fecha_inicio","txt_fecha_final","txt_total","txt_anticipo"],'','','',["txt_cliente","txt_fecha_inicio","txt_fecha_final","txt_total","txt_anticipo"], ["#FF5D00"], ["#E6FACB"]);			
		if(error){	
			return;
		}
		$id_cliente = $("#id_cliente").val();
		if(!$id_cliente){
			$("#alert_cliente").html('<div class="msg alerta_err">Seleccione un cliente existente.</div>');	
			return;
		}
		
		$txt_total = $("#txt_total").val();
		$txt_anticipo = $("#txt_anticipo").val();
		if(!($.isNumeric($txt_total))){
			$("#err_cantidad_total").show().html('Cantidad no valida.');
			return;
		}
		$("#err_cantidad_total").html("").hide();
		if(!($.isNumeric($txt_anticipo))){
			$("#err_cantidad_anticipo").show().html('Cantidad no valida.');
			return;
		}
		$("#err_cantidad_anticipo, #err_cantidad_total").html("").hide();
		
		
		$("#btn_guarda_cliente").hide();
		var str_post = $("form").serialize();
		$ser = $("#lst_serv_json").text();
		$atiende = $("#atiende").text(); 
		$cli_nombre = $("#cli_nombre").text();
		$cli_tel = $("#cli_tel").text();
		$cli_cel = $("#cli_cel").text();
		$cli_correo = $("#cli_correo").text();	
		$lst_serv_input = $("#lst_serv_input").text();	
		str_post = str_post+"&servicios="+$ser+"&atiende="+$atiende+"&cli_nombre="+$cli_nombre+"&cli_tel="+$cli_tel+"&cli_cel="+$cli_cel+"&cli_correo="+$cli_correo+"&lst_serv_input="+$lst_serv_input
		//alert(str_post); return;
		
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: "crud_pventas.php",
		 data: str_post,
		 beforeSend:function(){/* $("#ajax_respuesta").html($load); */},	 
		 success: function(datos){ 
		 //alert(datos);// return;
			var obj = jQuery.parseJSON(datos);	
			$("#ajax_respuesta").empty();	
			//alert(obj);	
			if(obj.tipo == "pedido_registrado"){
				$("input:text, textarea").attr('value','');
				$("#id_cliente").attr('value','');
				$("#txt_anticipo").attr('value','0');
				$("#datos_cliente").slideUp();
				$("#lst_serv, #lst_serv_json, #lst_serv_input").empty();
				$("#popup_contenido").html('<div class="msg alerta_ok">Datos Guardados</div>');
			}
			if(obj.tipo == "error_execute"){
				$("#popup_contenido").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
			}		 			  
			if(obj.tipo == "error_sql"){
				$("#popup_contenido").html('<div class="msg alerta_err">Problemas con el SQL</div>');
			} 
/*			$("#dialog_detalles").dialog({
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
			});*/
		 },
		 timeout:90000,
		 error: function(){ 					
				$("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
			}	   
		});	
	});
	
	
	$("#add_servicio").click(function(){
		$(".chk_servicios").prop('checked',false);
		$("#dialog_add_servicio").dialog({
			width: 500,
			resizable: false,
			show: { effect: "blind", pieces: 8, duration: 10 },
			title: "Agregar Servicio",
			close: function( event, ui ) {   				  			  
			 },
			buttons: {	
			  Cancelar: function() {
				  $( this ).dialog( "close" );
			  },								  
			  Aceptar: function() {
				 // $( this ).dialog( "close" );
				//$chk_serv = $(".chk_servicios:checkbox[checked='checked']").val();
				$id_cont = $id_cont + 1;
				$(".chk_servicios:checkbox[checked='checked']").each(function(index, element) {
					$id = $(this).val();
					$item = $(this).attr('name');
					$precio = $(this).attr('precio_venta');
					$cont = ($("#lst_serv").children().length) + 1; 
     
 					aleatorio = Math.floor(Math.random() * 50000) + 10;
					
					$("#lst_serv").append('<div id="ser_'+$cont+'" class="hand" onclick="eliminar_serv(\''+aleatorio+'_'+$cont+'\')"><img src="images/tache.png">$'+$precio+' '+$item+'</div>');

					$("#lst_serv_input").append('$'+$precio+':'+$item+' | ');
					// el span con la clase span_pedido se usa para identificar el servicio que se elimino mediansi ID 
					// al passarlo por la URL al serializarlo elimina las etiquetas span para que pase el JSON limpio 
					$("#lst_serv_json").append('<span class="span_pedido" id="span_'+aleatorio+'_'+$cont+'">{"id":"'+aleatorio+'_'+$cont+'","id_servicio":"'+$id+'","nombre":"'+$item+'","precio":"'+$precio+'","status":"abierto"},</span>');
					$("#dialog_add_servicio").dialog( "close" ); 
				});
				//alert($array_id); return;							  
				$("#lst_serv")
			  }
			}
		});							
	});
	
	
	$("#txt_cliente").autocomplete({
		source: "crud_pventas.php?accion=autocompleta_pedido_cliente",
		focus: function( event, ui ) {
			$("#txt_cliente").val(ui.item.nombre);
			return false;
		},				
		//appendTo: '#menu-container',
		minLength: 1,						
		select: function (event, ui) {		
			//alert(ui.item.nombre)
		   $("#id_cliente").val(ui.item.id_cliente);
		   $("#cli_nombre").html(ui.item.nombre);
		   $("#cli_dir").html(ui.item.dir);
		   $("#cli_ciudad").html(ui.item.ciudad);
		   $("#cli_tel").html(ui.item.tel);
		   $("#cli_cel").html(ui.item.cel);
		   $("#cli_correo").html(ui.item.correo);
		   $("#datos_cliente").slideDown();							
		}
	});	
});
function eliminar_serv($index){
	//alert($index)
	$index_id = $index.split("_");
	$("#ser_"+$index_id[1]).remove();
	$("#span_"+$index).remove();
	$("#txt_ser_"+$index_id[1]).remove();
	
}
</script>
<div id="cont_registro_prove">
  <div class="f_negro titulo_frm">
  	<div style="position:relative; top:7px;">Nuevo Pedido</div>  
  </div> 
<form>
  <input type="hidden" name="accion" id="accion" value="insert_pedido" />
  <input type="hidden" name="id_cliente" id="id_cliente" value="" />
  <table width="100%" border="0">
  <tr>
    <td width="170"><label for="textfield">Cliente: </label></td>
    <td width="270"><input type="text" name="nombre" id="txt_cliente" class="text_box" size="70" maxlength="200" value="" ></td>
    <td><div id="alert_cliente" style="position:relative; float:left; width:200px; background-color:"></div></td>
  </tr> 
  </table>
  <div id="datos_cliente" style="display:none; background-color: #EDF7FA; border:1px solid #036; margin-left:178px; width:50%; font-size:14px;">
      <table width="100%" border="0" style="position:relative;">
      <tr>
        <td colspan="2" align="center" bgcolor="#0FA1E0">Datos del Cliente</td>
      </tr>
      <tr>
        <td width="100">Nombre</td><td><span id="cli_nombre"></span></td>
      </tr>
      <tr>
        <td>Direccion</td><td><span id="cli_dir"></span></td>
      </tr>
      <tr>
        <td>Ciudad</td><td><span id="cli_ciudad"></span></td>
      </tr>
      <tr>
        <td>Telefono</td><td><span id="cli_tel"></span></td>
      </tr>
      <tr>
        <td>Celular</td><td><span id="cli_cel"></span></td>
      </tr>
      <tr>
        <td>Correo</td><td><span id="cli_correo"></span></td>
      </tr>                              
      </table>  
      
  </div>
  <table width="100%" border="0">  
<!--  <tr>
    <td><label for="textfield">Contacto: </label></td>
    <td><input type="text" name="contacto" id="txt_contacto" class="text_box" size="50" maxlength="200" value="contacto" ></td>
  </tr> --> 
  <tr>
    <td width="170"><label for="textfield">Fecha de Inicio: </label></td>
    <td><input type="text" name="fecha_inicio" id="txt_fecha_inicio" class="text_box" size="8" maxlength="100" value="" readonly ></td>
  </tr> 
  <tr>
    <td><label for="textfield">Fecha de Entrega: </label></td>
    <td><input type="text" name="fecha_final" id="txt_fecha_final" class="text_box" size="8" maxlength="100" value="" readonly ></td>
  </tr>
  <tr>
    <td><label for="textfield">Total: </label></td>
    <td><input type="text" name="total" id="txt_total" class="text_box" size="10" maxlength="100" value="" value="0" >
    	<span id="err_cantidad_total" style="width:180px; display:none" class="msg alerta_err"></span>
    </td>
  </tr> 
  <tr>
    <td>Agregar Servicio:</td>
    <td>
    	<span class="t_azul_fuerte hand" id="add_servicio"><strong>+ Agregar</strong></span>
    	<div id="lst_serv"></div>
        <div id="lst_serv_input" style="display:none;"></div>
        <div id="lst_serv_json" style="display:none;"></div>
    </td>
  </tr>     
  <tr>
    <td><label for="textfield">Anticipo: </label></td>
    <td><input type="text" name="anticipo" id="txt_anticipo" class="text_box" size="10" maxlength="100" value="0" >
    	<span id="err_cantidad_anticipo"  style="width:180px; display:none" class="msg alerta_err"></span>        
    </td>
  </tr>   
  <tr>
    <td><label for="textfield">Descripcion del Pedido: <br />(<span id="numtxt_obs"> 0 </span>/ 1500 ) </label></td>
    <td><label for="textarea"></label><textarea name="obs" id="txt_obs" cols="60" rows="5" onkeypress="return limita(1500,'txt_obs', event)" class="text_area"></textarea></td>
  </tr> 
  <tr>
    <td>Estado del Ticket:</td>
    <td><span class="msg alerta_status_open">&laquo; <strong>abierto</strong> &raquo; por <span id="atiende"><?=$_SESSION['g_nombre'];?></span></span></td>
  </tr>     
  <tr>
  	<td colspan="2" align="center"><button class="" type="button" id="btn_guarda_pedido" style="width:180px;">Guardar</button></td>
  </tr>  	
</table>
</form>
</div>
<div id="dialog_detalles" style="width:90%; display:none">
    <div id="popup_contenido" style="position: relative; overflow-y: scroll; height:50px;"></div>
</div>
<div id="dialog_add_servicio" style="width:90%; display:none">
    <div id="popup_add_servicio" style="position: relative; overflow-y: scroll; height:200px; width:470px;">
    	
		<?php echo $conn->lst_servicios('tbl_producto', 'id', 'nombre', '', '',$_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal']); ?>         
    </div>
</div>