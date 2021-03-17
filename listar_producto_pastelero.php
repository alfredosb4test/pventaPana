<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();

if(array_key_exists("accion", $_POST) && $_POST['accion']=='autocomplete'){
	$id_producto = $_POST['id_producto'];
	//echo "ID:".$id_producto;
	$array_prove = $conn->listar_producto($id_producto, "ORDER BY id ASC", $_SESSION['g_id_empresa'], $_SESSION['g_sucursales']);	
}else if(array_key_exists("accion", $_POST) && $_POST['accion']=='order_prove'){
	$array_prove = $conn->listar_producto($id_producto, $_POST["orderby"], $_SESSION['g_id_empresa'], $_SESSION['g_sucursales']);	
}else
	$array_prove = $conn->listar_producto($id_producto, "ORDER BY id ASC", $_SESSION['g_id_empresa'], $_SESSION['g_sucursales']);	

if($array_prove == 'no_data'){
	echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
	exit;
}else{	
	$tabla = '<table class="tbl_datos" border="1" cellpadding="2" cellspacing="0" width="100%">';
		$tabla .= "
		 	<tr class='f_negro table_top'>
				<th width='40%' id='th_ambito' class='hand t_underline t_hover_amarillo ordenar_query' campo=' ORDER BY nombre '><img src='images/sort.png' style='position:absolute;margin-top:2px;margin-left:-20px'>Producto</th>
				<th width='10%' class='hand t_underline t_hover_amarillo ordenar_query' campo=' ORDER BY cantidad '><img src='images/sort.png' style='position:absolute;margin-top:2px;margin-left:-20px'>Cantidad</th>
			</tr>";
			
	foreach($array_prove['id'] as $key=>$nombre){
		if($array_prove['activo'][$key])
			$activo = '<img class="producto_activo hand" id="'.$array_prove['id'][$key].'" valor="0" src="images/ok.png">';
		else
			$activo = '<img class="producto_activo hand" id="'.$array_prove['id'][$key].'" valor="1" src="images/tache.png">';
	
		
		
		$tabla .= 
		'<tr class="tbl_fila tr_detalles" 
			id="'.$array_prove['id'][$key].'">
			<td>
				<div class="t_marron editar_prod " id_prod="'.$array_prove['codigo'][$key].'" 
					style="position:relative; width:120px; float:left;font-weight:bold;">'.$array_prove['codigo'][$key].'
				</div> 
				<div style="position:relative; float:left;">'.$array_prove['nombre'][$key].'</div> 				
				<div class="t_cafe" style="position:relative; clear:both;">'.$array_prove['sucursal'][$key].'</div>	
			</td>
			
			<td align="center" class="hand t_azul_fuerte"
				onclick="edit_cantidad_prod(\''.$array_prove['id'][$key].'\',\''.$array_prove['cantidad'][$key].'\')">
				<span id="cantidad_'.$array_prove['id'][$key].'">'.$array_prove['cantidad'][$key].'</span> '.$array_prove['unidades'][$key].
			'</td>
		</tr>'; 	
	}
	$tabla .= "</table>";	
}
//echo "<pre>"; print_r($array_prove); echo "</pre>"; 
?> 
<div id="cont_buscar_producto">
 
  <input type="hidden" name="accion" id="accion" value="insert_prove" />
  
  <table width="100%" border="0">
  <tr class="info">
    <td width="80"><label for="textfield">Sucursal: </label></td>
    <td width="220">
        <span class="cont_sucursales_admin" style="display: ; position: relative; margin:0; float:left;">
              <select id='lst_sucursales_pastelero' name='lst_sucursales_pastelero' style="width:200px; " >
                <?php echo $conn->lst_sucursales_admin('tbl_sucursal', 'id_sucursal', 'sucursal', '', '', $_SESSION['g_id_empresa'],$_SESSION['g_sucursales']); ?> 
             </select>
        </span>
    </td>  
    <td width="80">Buscar:</td>
    <td width="290" colspan="2"><input type="text" class="text_box" onkeypress="key_buscar_producto(event,this)" size="30" maxlength="100" id="txt_buscar_producto" /></td>
    <td width="100%"><div id="ajax_buscar_pastelero"></div></td>
  </tr>
  
  </table> 
  <?php if($id_producto): ?>
  		<input type="hidden"  value="<?=$id_producto?>" id="busqueda" />
  <?php endif ?> 
 
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
	
	
	$("#select_cantidad").click(function(){
		$cantidad_nueva = parseInt($("#cantidad_prod_actual").html());
		$("#cantidad_prod_actual").html($cantidad_nueva+1);
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
			 url: 'listar_producto_pastelero.php',
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
});

function buscar_producto_devo($codigo){
  $id_sucursal = $("#lst_sucursales_pastelero option:selected").attr('value');
  $nombre_sucursal = $("#lst_sucursales_pastelero option:selected").text();
  //alert($nombre)
  if($id_sucursal == ""){
  	$("#ajax_buscar_pastelero").html('<div class="msg alerta_err">Seleccione una sucursal.</div>');
	return;
  }
  $.ajax({
   type: "POST",
   contentType: "application/x-www-form-urlencoded", 
   url: "crud_pventas.php",
   data: "accion=pastelero_buscar_producto&codigo="+$codigo+"&id_sucursal="+$id_sucursal,
   beforeSend:function(){/* $("#ajax_respuesta").html($load); */},	 
   success: function(datos){ 
   //alert(datos)   
	  var obj = jQuery.parseJSON(datos);	
	  $("#ajax_respuesta").empty();	
	  //alert(obj);	
	  if(obj.status == "existe"){ 
		  $("#txt_id_prod").val(obj.id);
		  $("#ajax_buscar_pastelero").html('<div class="msg alerta_ok t_verde_fuerte">'+$nombre_sucursal+' -> '+obj.nombre+'</div>');
		  edit_cantidad_prod(obj.id, obj.cantidad)
	  }
	  if(obj.status == "no_existe"){
		  $("#ajax_buscar_pastelero").html('<div class="msg alerta_err">Producto no existente</div>');
	  }		 			  
	  if(obj.status == "error_sql"){
		  $("#ajax_buscar_pastelero").html('<div class="msg alerta_err">Problemas con el SQL</div>');
	  } 	
   },
   timeout:90000,
   error: function(){ 					
		  $("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
	  }	   
  });	
}
function key_buscar_producto (elEvento, e) {	
	
	var evento = elEvento || window.event;
	var caracter = evento.charCode || evento.keyCode;
	if ( caracter == 13 ) {
		if($(e).val() == "")
			return;
		else 
			buscar_producto_devo($(e).val());
	}
}	


function edit_cantidad_prod($id, $cantidad){
	$cantidad = parseInt($("#cantidad_"+$id).html());
	$("#cantidad_prod_actual").html($cantidad);
	
	$("#dialog_edit_catidad").dialog({
		width: 300,
		resizable: false,
		show: { effect: "blind", pieces: 8, duration: 10 },
		title: "Editar Cantidad",
		close: function( event, ui ) {  
			  $("#popup_edit_catidad, #ajax_buscar_pastelero").empty(); 
		 },
		buttons: {					  
		  Aceptar: function() {
			$cantidad_nueva = parseInt($("#cantidad_prod_actual").html());
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded", 
			 url: "crud_pventas.php",
			 data: "accion=edit_cantidad_producto&cantidad="+$cantidad_nueva+"&id="+$id,
			 beforeSend:function(){/* $("#ajax_respuesta").html($load); */},	 
			 success: function(datos){ 
			 //alert(datos)
				var obj = jQuery.parseJSON(datos);	
				$("#ajax_respuesta").empty();	
				//alert(obj);	
				if(obj.tipo == "producto_update"){ 
					$("#cantidad_"+$id).html($cantidad_nueva);
					$("#dialog_edit_catidad").dialog( "close" );		
				}
				if(obj.tipo == "error_execute"){
					$("#popup_edit_catidad").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
				}		 			  
				if(obj.tipo == "error_sql" || obj.tipo == "error_parametros"){
					$("#popup_edit_catidad").html('<div class="msg alerta_err">Problemas con el SQL</div>');
				}
			 },
			 timeout:90000,
			 error: function(){ 					
					$("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
				}	   
			});				  
			
		  }			  
		}
	});		
}
</script>
<div id="dialog_detalles" style="width:90%; display:none">
    <div id="popup_contenido" style="position: relative; overflow-y: scroll; height: auto;"></div>
</div>
<div id="dialog_edit_catidad" style="width:90%; display:none">
	<table border="1">
    <tr>
    	<td>Cantidad: </td>
        <td width="150">
			<div id="cantidad_prod_actual" class="font_22 t_azul_fuerte" style="position:relative; float:left; width:40px"></div>
            <div id="select_cantidad" class="font_22 t_azul_fuerte hand" style="position:relative; margin-left:10px;"><img src="images/agregar.png" /></div>
        </td>   
    </tr>
    </table>       
    <div id="popup_edit_catidad" style="position: relative; overflow-y: scroll; height: auto;">
    
    </div>
</div>
