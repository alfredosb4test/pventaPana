<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
 
$array = $conn->listar_stock_pasteles($_SESSION['g_id_empresa']);
//echo "<pre>"; print_r($array); echo "</pre>"; 	
if($array == 'no_data'){
	$tabla = '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
	exit;
}else{	

			
	foreach($array['id_stock_pasteles'] as $key=>$nombre){
		$json_codigos = $array['json_codigos'][$key];
		$json_codigos = json_decode($json_codigos);
		$obs = str_replace("##br##", "<br>",$array['obs'][$key]);
		$mes = $conn->damemes($array['mes1'][$key]);
		$tabla .= '
				<table border="0" width="99.8%" class="f_cabecera_list_ventas btop bbottom bright bleft" cellspacing="0" class="bbottom">
					<tr>
						<td>
						Fecha de Solicitud: '.$array['dia1'][$key].'/'.$mes.'/'.$array['anio1'][$key].' '.$array['hrs'][$key].'
						</td>
					</tr>
					<tr>	
						<td>
							'.$obs.'
						</td>						
					</tr>
				</table>		
				</table>
				<table border="1" width="99.8%" class="f_sub_cabecera_list_ventas btop bbottom bright bleft" cellspacing="0" class="bbottom">
				  <tr >
					  <th width="100"><strong>Calve</strong></th>
					  <th><strong>Producto</strong></th>
					  <th width="10"><strong>Cantidad</strong></th>
					  <th><strong>Realizado</strong></th>
				  </tr>';	
		foreach($json_codigos as $rowj){
			$sucursal = $rowj->sucursal;
			if($rowj->status == "abierto"){
				if($_SESSION['g_nivel'] == "pastelero" || $_SESSION['g_nivel'] == "admin"){	
					$btn_cerrar = '<img class="producto_activo hand" 
								 id="'.$array['id_stock_pasteles'][$key].$rowj->code.'" 
								 onclick="add_stock_prod(\''.$array['id_stock_pasteles'][$key].'\',\''.$rowj->code.'\')"
								 valor="1" src="images/tache.png">';
				}else{
					$btn_cerrar = '<img src="images/tache.png">';
				}
			}else
				$btn_cerrar = '<img src="images/ok.png">';
			$tabla .= '
				<tr class="">
					<td width="100"><strong>'.$rowj->code.'</strong> </td> 
					<td>'.$conn->get_nombre_producto_code($rowj->code, 50).'</td>
					<td width="10" align="center"><strong>'.$rowj->cantidad.'</strong></td>
					<td width="10" align="center">
						'.$btn_cerrar.'
					</td>
				</tr>';	
		}
			$tabla .= '
				<tr class="t_azul_fuerte" align="right"><td colspan=4><strong>'.$sucursal.'</strong></td><tr>	';	
		$tabla .= "</table><br>";
	}
	$conn->close_mysqli();
		
}
//echo "<pre>"; print_r($array); echo "</pre>"; 
?>  
<script type="text/javascript">
var error=0;
$(document).ready(function(e) {

});
function add_stock_prod($id_stock_pasteles, $code){
	//alert($id); return;
	$("#popup_add_stock").html("Confirmar Producto Terminado: "+$code+" ?");
	$("#dialog_add_stock").dialog({
		width: 430,
		resizable: false,
		show: { effect: "blind", pieces: 8, duration: 10 },
		title: "Stock",
		close: function( event, ui ) {  
			  $("#popup_add_stock").empty();
			  $( this ).dialog( "close" ); 			  
		 },
		buttons: {			  
		  Aceptar: function() {
				$.ajax({
				 type: "POST",
				 contentType: "application/x-www-form-urlencoded", 
				 url: 'crud_pventas.php',
				 data: "accion=terminado_stock_prod&id_stock_pasteles="+$id_stock_pasteles+"&code="+$code,
				 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
				 success: function(datos){	
				  //$("#t1").append(datos)
						var obj = jQuery.parseJSON(datos);	
						$("#ajax_respuesta").empty();	
						//alert(obj);	
						if(obj.tipo == "update_registrado"){		
							$("#"+$id_stock_pasteles+$code).removeClass("hand").attr({"src":"images/ok.png","onclick":""});
							$("#dialog_add_stock").dialog( "close" ); 	
						}
						if(obj.tipo == "error_execute"){
							$("#popup_add_stock").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
						}		 			  
						if(obj.tipo == "error_sql"){
							$("#popup_add_stock").html('<div class="msg alerta_err">Problemas con el SQL</div>');
						} 
						if(obj.tipo == "error_parametros"){
							$("#popup_add_stock").html('<div class="msg alerta_err">Problemas con el SQL: parametros</div>');
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
<div class="f_negro titulo_frm">
  <div style="position:relative; top:7px;">Productos Pendientes de Realizar</div>  
</div>
        
<div id="dialog_add_stock" style="width:90%; display:none; font-size:16px;"> 
    <div id="popup_add_stock" style="height:30px; font-size:16px;"></div>
</div><br /><div id="t1"></div>
  <?=$tabla;?>
