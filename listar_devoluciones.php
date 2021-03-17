<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();

if(array_key_exists("accion", $_POST) && $_POST['accion']=='order_prove'){
	$array_prove = $conn->listar_producto_devolucion($_POST["orderby"], $_SESSION['g_id_empresa'], $_SESSION['g_sucursales']);	
}else
	$array_prove = $conn->listar_producto_devolucion("ORDER BY id_dvlcion DESC", $_SESSION['g_id_empresa'], $_SESSION['g_sucursales']);	

if($array_prove == 'no_data'){
	echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
	exit;
}else{	
	$tabla = '<table class="tbl_datos" border="1" cellpadding="2" cellspacing="0" width="100%">';
		$tabla .= "
		 	<tr class='f_negro table_top'>
				<th width='10%' id='th_ambito' class='hand t_hover_amarillo ordenar_query' campo=' ORDER BY sucursal '><img src='images/sort.png' style='position:absolute;margin-top:2px;margin-left:-20px'>Sucursal</th>
				<th width='20%' class='hand t_hover_amarillo ordenar_query' campo=' ORDER BY nombre_empleado '><img src='images/sort.png' style='position:absolute;margin-top:2px;margin-left:-20px'>Empleado</th>
				<th width='22%' class='hand t_hover_amarillo ordenar_query' campo=' ORDER BY id_producto '><img src='images/sort.png' style='position:absolute;margin-top:2px;margin-left:-20px'>Producto.</th>
				<th width='32%'>Motivo</th>
				<th width='12%' class='hand t_hover_amarillo ordenar_query' campo=' ORDER BY fecha '><img src='images/sort.png' style='position:absolute;margin-top:2px;margin-left:-20px'>Fecha</th>
			</tr>";
			
	foreach($array_prove['id_dvlcion'] as $key=>$nombre){

		$tabla .= 
		'<tr class="tbl_fila tr_detalles" 
			id="'.$array_prove['id_dvlcion'][$key].'">
			<td>'.$array_prove['sucursal'][$key].'</td>
			<td align="center">'.$array_prove['nombre_empleado'][$key].'</td>
			<td align="center">'.$array_prove['nombre'][$key].'</td>
			<td align="center">'.$array_prove['motivo'][$key].'</td>
			<td align="center">'.$array_prove['fecha'][$key].'</td>
		</tr>'; 	
	}
	$tabla .= "</table>";	
}
//echo "<pre>"; print_r($array_prove); echo "</pre>"; 
?> 
  <div class="f_negro titulo_frm">
  	<div style="position:relative; top:7px;">Devoluciones</div>  
  </div> 
<?=$tabla;?>
<script type="text/javascript">
var error=0;

$(document).ready(function(e) {
	$('#txt_buscar_producto').focus();
	$("tr:odd").addClass("f_tr_hover");
	$(".editar_prod").click(function(){
			$("#txt_editar_prod_ir").attr("value",$(this).attr('id_prod'));
			//alert($("#txt_editar_prod_ir").val())
			$("#btn_nuevo_prod").click();
	});
	$(".ordenar_query").click(function(){
			$campo = $(this).attr('campo');
			if(cont_1%2)
				$orderby = $campo+'DESC';
			else
				$orderby = $campo+'ASC';
			cont_1++;
			//alert(cont_1)
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded", 
			 url: 'listar_devoluciones.php',
			 data: "accion=order_prove&orderby="+$orderby,
			 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
			 success: function(datos){ 
 					limpiar_datos();
					$("#contenido_resul").show().empty().html(datos); 
			 
			 },
			 timeout:90000,
			 error: function(){ 					
					$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
				}	   
			});			
	});
	$("#txt_buscar_producto").autocomplete({
		source: "crud_pventas.php?accion=autocompleta_producto",
		focus: function( event, ui ) {
			$( "#txt_buscar_producto" ).val( ui.item.empresa );
			return false;
		},				
		//appendTo: '#menu-container',
		minLength: 1,						
		select: function (event, ui) {				
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded", 
			 url: 'listar_producto.php',
			 data: "accion=autocomplete&id_producto="+ui.item.value,
			 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
			 success: function(datos){ 
				//$(".btn_menu").removeClass('btn_activo');
				//$("#btn_unidad").addClass('btn_activo');
				$('#contenido_resul').animate({'height':'97%'},130, function(){
					$("#contenido_resul").show().empty().html(datos);
					$("#ajax_respuesta").empty();
					//$('.btn_menu').bind('click',$btn_click);
				});
			 },
			 timeout:90000,
			 error: function(){ 					
					$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
				}	   
			});
		   //$("#test").val(ui.item.value);							
		},
	});	
	
	function producto_activo(event){
		event.stopPropagation();
		//event.preventDefault();
		$event = $(this);
		$id = $event.attr('id');
		$busqueda = $("#busqueda").val();
		$valor = $event.attr('valor');
		$(".producto_activo, .tr_detalles").unbind("click"); 
		//alert($id);
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: "crud_pventas.php",
		 data: "accion=activar_desactivar_producto&valor="+$valor+"&id="+$id+"&id_producto="+$busqueda,
		 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		 success: function(datos){ 
		  //alert(datos)
			var obj = jQuery.parseJSON(datos);	
			$("#ajax_respuesta").empty();	
			//alert(obj);	
			if(obj.tipo == "producto_activo"){ 
				$("#popup_contenido").html('<div class="msg alerta_ok">Datos Guardados</div>');
			}
			if(obj.tipo == "producto_error"){
				$("#popup_contenido").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
			}
			if(obj.tipo == "error_sql" || obj.tipo == "error_execute"){
				$("#popup_contenido").html('<div class="msg alerta_err">Error SQL</div>');
			}
			$("#dialog_detalles").dialog({
				width: 900,
				resizable: false,
				show: { effect: "blind", pieces: 8, duration: 10 },
				title: "Aviso",
				close: function( event, ui ) {  
					  $("#popup_contenido").empty();
					  //$("#btn_listar_prove").click();
					  if($valor == 1)
					  	$event.attr({"src":"images/ok.png","valor":0});
					  else
					  	$event.attr({"src":"images/tache.png","valor":1});
						
					  $( "#txt_buscar_producto" ).val( $busqueda);
					  $(".producto_activo").bind("click",producto_activo);
					  //$(".tr_detalles").bind("click",prove_detalles);				  
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
				$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
			}	   
		});	
	}
	$(".producto_activo").click(producto_activo);
});
</script>
<div id="dialog_detalles" style="width:90%; display:none">
    <div id="popup_contenido" style="position: relative; overflow-y: scroll; height: auto;">
</div>