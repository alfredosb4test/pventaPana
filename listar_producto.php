<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();

if(array_key_exists("accion", $_POST) && $_POST['accion']=='autocomplete'){
	$id_producto = $_POST['id_producto'];
	//echo "ID:".$id_producto;
	$array_prove = $conn->listar_producto($id_producto, "ORDER BY id ASC", $_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal']);	
}else if(array_key_exists("accion", $_POST) && $_POST['accion']=='order_prove'){
	//sleep(4);
	$array_prove = $conn->listar_producto($id_producto, $_POST["orderby"], $_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal']);	
}else
	$array_prove = $conn->listar_producto($id_producto, "ORDER BY nombre ASC", $_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal']);	

if($array_prove == 'no_data'){
	echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
	exit;
}else{	
	$tabla = '<table class="tbl_datos" border="1" cellpadding="2" cellspacing="0" width="100%">';
		$tabla .= "
		 	<tr class='f_negro table_top'>
				<th width='40%' id='th_ambito' class='hand t_hover_amarillo ordenar_query' campo=' ORDER BY nombre '><img src='images/sort.png' style='position:absolute;margin-top:2px;margin-left:-20px'>Producto</th>
				<th width='10%' class='hand t_hover_amarillo ordenar_query' campo=' ORDER BY cantidad '><img src='images/sort.png' style='position:absolute;margin-top:2px;margin-left:-20px'>Cantidad</th>
				<th width='12%' class='hand t_hover_amarillo ordenar_query' campo=' ORDER BY precio_provedor '><img src='images/sort.png' style='position:absolute;margin-top:2px;margin-left:-20px'>Prov.</th>
				<th width='12%' class='hand t_hover_amarillo ordenar_query' campo=' ORDER BY precio_venta '><img src='images/sort.png' style='position:absolute;margin-top:2px;margin-left:-20px'>Venta</th>
				<th width='12%' class='hand t_hover_amarillo ordenar_query' campo=' ORDER BY precio_mayoreo '><img src='images/sort.png' style='position:absolute;margin-top:2px;margin-left:-20px'>Mayoreo</th>
				<th width='3%'>Activo</th>
				<th width='3%'>Borrar</th>
			</tr>";
	
	
	foreach($array_prove['id'] as $key=>$nombre){
		$precio_provedor = $array_prove['precio_provedor'][$key];
		if($_SESSION['g_nivel'] == "admin"){
			$btn_eliminar = '<img class="producto_borrar hand" id="'.$array_prove['id'][$key].'" valor="1" src="images/borrar.png" width="25" height="25">';
			if($array_prove['activo'][$key])
				$activo = '<img class="producto_activo hand" id="'.$array_prove['id'][$key].'" valor="0" src="images/ok.png">';
			else
				$activo = '<img class="producto_activo hand" id="'.$array_prove['id'][$key].'" valor="1" src="images/tache.png">';
		}else{
			$activo="";
			$btn_eliminar = '';
			$precio_provedor = 0;
		}
		
		$tabla .= 
		'<tr class="tbl_fila tr_detalles" 
			id="'.$array_prove['id'][$key].'">
			<td> 
				<div class="t_marron hand t_hover_rojo editar_prod" id_prod="'.$array_prove['codigo'][$key].'" 
					style="position:relative; width:120px; float:left;font-weight:bold;">'.$array_prove['codigo'][$key].'
				</div> 
				<div style="position:relative; float:left;">'.$array_prove['nombre'][$key].'</div> 
				<div class="t_cafe" style="position:relative; clear:both;">'.$array_prove['sucursal'][$key].'</div>				
			</td>
			<td align="center">'.$array_prove['cantidad'][$key].' '.$array_prove['unidades'][$key].'</td>
			<td align="center">'.$precio_provedor.'</td>
			<td align="center">'.$array_prove['precio_venta'][$key].'</td>
			<td align="center">'.$array_prove['precio_mayoreo'][$key].'</td>
			<td align="center">'.$activo.'</td>
			<td align="center">'.$btn_eliminar.'</td>
		</tr>'; 	
	}
	$tabla .= "</table>";	
}
//echo "<pre>"; print_r($array_prove); echo "</pre>"; 
?> 
<div id="cont_buscar_producto">
<form>
  <input type="hidden" name="accion" id="accion" value="insert_prove" />
  
  <table width="40%" border="0" style="position:relative; float:left;">
  <tr class="info">
    <td width="80">Buscar:</td><td colspan="2"><input type="text" class="text_box" size="60" maxlength="100" id="txt_buscar_producto" /></td>
  </tr>
  </table>
  <?php if($_SESSION['g_nivel'] == "admin"): ?>
      <table id="tbl_sucursales_admin" border="0" style="position:relative; margin:0 auto;">
        <tr>
          <td><label for="textfield">Selecciona una Sucursal: </label></td>
          <td>
              <span class="cont_sucursales_admin" style="display: ; position: relative; margin:0; float:left;">
                    <select id='lst_sucursales_admin_prod' name='lst_sucursales_admin_prod' style="width:250px; "  >
                      <?php echo $conn->lst_sucursales_admin('tbl_sucursal', 'id_sucursal', 'sucursal', $_SESSION['g_id_sucursal'], '', $_SESSION['g_id_empresa'],$_SESSION['g_sucursales']); ?> 
                   </select>
              </span>
          </td>
        </tr> 
      </table>
  <?php endif ?>       
  <?php if($id_producto): ?>
  		<input type="hidden"  value="<?=$id_producto?>" id="busqueda" />
  <?php endif ?> 
</form>
</div>  
<?=$tabla;?>
<script type="text/javascript">
var error=0;

$(document).ready(function(e) {
	$('#txt_buscar_producto').focus();
	$("tr:odd").addClass("f_tr_hover");
	$(".editar_prod").click(function(){
			$("#txt_editar_prod_ir").attr("value",$(this).attr('id_prod'));
			console.log($("#txt_editar_prod_ir").val())
			$("#btn_nuevo_prod").click(); // clic en el menu nuevo producto  -> ir_menu('nuevo_producto.php','nuevo_prod');	
	});
	
	$( "#lst_sucursales_admin_prod" ).selectmenu({
		change: function( event, data ) {
			$id_sucursal = $("#lst_sucursales_admin_prod option:selected").attr('value');
			$sucursal_act = $('#lst_sucursales_admin_prod option:selected').html();
			//alert($id_sucursal);
			$.post( "crud_pventas.php", { id_sucursal: $id_sucursal, sucursal_act: $sucursal_act, accion: "admin_activar_sucursal" })
			  .done(function( data ) {
				$("#btn_listar_prod").click(); // clic en el menu nuevo producto  -> ir_menu('nuevo_producto.php','nuevo_prod');	
			  });
		}
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
			 url: 'listar_producto.php',
			 data: "accion=order_prove&orderby="+$orderby,
			 beforeSend:function(){ },	 
			 success: function(datos){ 
 					limpiar_datos();
					$.unblockUI();
					$("#contenido_resul").show().empty().html(datos); 
			 
			 },
			 timeout:90000,
			 error: function(){
 
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
		$(".producto_activo, .producto_borrar, .tr_detalles").unbind("click"); 
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
					  $(".producto_borrar").bind("click",producto_borrar);
					  
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
	
	function producto_borrar(event){
		event.stopPropagation();
		//event.preventDefault();
		$event = $(this);
		$id = $event.attr('id');

		$(".producto_borrar, .producto_activo, .tr_detalles").unbind("click"); 
		//alert($id);
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: "crud_pventas.php",
		 data: "accion=borrar_producto&id="+$id,
		 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		 success: function(datos){ 
		  //alert(datos)
			var obj = jQuery.parseJSON(datos);	
			$("#ajax_respuesta").empty();	
			//alert(obj);	
			if(obj.tipo == "update_ok"){ 
				$("#popup_contenido").html('<div class="msg alerta_ok">Producto Eliminado</div>');
				$event.parent().parent().hide("slow").remove();
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
						
					  //$( "#txt_buscar_producto" ).val( $busqueda);
					  $(".producto_borrar").bind("click",producto_borrar);
					  $(".producto_activo").bind("click",producto_activo);				  
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
	$(".producto_borrar").click(producto_borrar);	
});
</script>
<div id="dialog_detalles" style="width:90%; display:none">
    <div id="popup_contenido" style="position: relative; overflow-y: scroll; height: auto;">
</div>