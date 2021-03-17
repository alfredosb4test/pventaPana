<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();

if(array_key_exists("accion", $_POST) && $_POST['accion']=='btn_listar_prove'){
	$id_proveedor = $_POST['id_proveedor'];
}
$array_prove = $conn->listar_prove($id_proveedor, $_SESSION['g_id_sucursal'], $_SESSION['g_id_empresa']);	
if($array_prove == 'no_data'){
	echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
	exit;
}else{	
	$tabla = '<table class="tbl_datos" border="0" cellpadding="2" cellspacing="0" width="100%">';
		$tabla .= "
		 	<tr class='f_negro table_top'>
				<th width='65%' id='th_ambito' >Proveedor</th>
				<th width='30%'>Correo</th>
				<th width='5%'>Activo</th>
			</tr>";
			
	foreach($array_prove['id_proveedor'] as $key=>$nombre){
		if($array_prove['activo'][$key])
			$activo = '<img class="prove_activo hand" id="'.$array_prove['id_proveedor'][$key].'" valor="0" src="images/ok.png">';
		else
			$activo = '<img class="prove_activo hand" id="'.$array_prove['id_proveedor'][$key].'" valor="1" src="images/tache.png">';
	
		
		
		$tabla .= 
		'<tr class="tbl_fila tr_detalles hand" 
			id="'.$array_prove['id_proveedor'][$key].'"   				
			empresa="'.$array_prove['empresa'][$key].'"
			contacto="'.$array_prove['contacto'][$key].'"
			ciudad="'.$array_prove['ciudad'][$key].'"
			obs="'.$array_prove['obs'][$key].'"
			dir="'.$array_prove['dir'][$key].'"
			tel="'.$array_prove['tel'][$key].'"
			cel="'.$array_prove['cel'][$key].'"
			correo="'.$array_prove['correo'][$key].'"
		 >
			<td>'.
				$array_prove['empresa'][$key].
				'<br><span class="t_cafe">'.$array_prove['tel'][$key].'</span><strong> | </strong>
				 <span class="t_verde_fuerte">'.$array_prove['cel'][$key].'</span> 	
				 <br><span class="t_marron">'.$array_prove['contacto'][$key].'</span>'	
			.'</td>
			<td>'.$array_prove['correo'][$key].'</td>
			<td align="center">'.$activo.'</td>
		</tr>';
	}
	$tabla .= "</table>";	
}
//echo "<pre>"; print_r($array_prove); echo "</pre>"; 
?> 
<div id="cont_buscar_prove">
<form>
  <input type="hidden" name="accion" id="accion" value="insert_prove" />
  <table width="100%" border="0">
  <tr class="info">
    <td width="80">Buscar:</td><td colspan="2"><input type="text" class="text_box" size="60" maxlength="100" id="txt_buscar_prove" /></td>
  </tr>
  
  </table>
  <?php if($id_proveedor): ?>
  		<input type="hidden"  value="<?=$id_proveedor?>" id="busqueda" />
  <?php endif ?> 
</form>
</div>  
<?=$tabla;?>
<script type="text/javascript">
var error=0;
$(document).ready(function(e) {
	$('#txt_buscar_prove').focus();
	$( "tr:odd" ).addClass("f_tr_hover");
	$("#txt_buscar_prove").autocomplete({
		source: "crud_pventas.php?accion=autocompleta_prove",
		focus: function( event, ui ) {
			$( "#txt_buscar_prove" ).val( ui.item.empresa );
			return false;
		},				
		//appendTo: '#menu-container',
		minLength: 1,						
		select: function (event, ui) {				
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded", 
			 url: 'listar_proveedor.php',
			 data: "accion=btn_listar_prove&id_proveedor="+ui.item.value,
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
	
	
	function prove_activo(event){
		event.stopPropagation();
		//event.preventDefault();
		$event = $(this);
		$id = $event.attr('id');
		$busqueda = $("#busqueda").val();
		$valor = $event.attr('valor');
		$(".prove_activo, .tr_detalles").unbind("click"); 
		//alert($valor);
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: "crud_pventas.php",
		 data: "accion=activar_desactivar_prove&valor="+$valor+"&id="+$id+"&id_proveedor="+$busqueda,
		 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		 success: function(datos){ 
		// alert(datos)
			var obj = jQuery.parseJSON(datos);	
			$("#ajax_respuesta").empty();	
			//alert(obj);	
			if(obj.tipo == "prove_activo"){ 
				$("#popup_contenido").html('<div class="msg alerta_ok">Datos Guardados</div>');
			}
			if(obj.tipo == "prove_error"){
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
						
					  $( "#txt_buscar_prove" ).val( $busqueda);
					  $(".prove_activo").bind("click",prove_activo);
					  $(".tr_detalles").bind("click",prove_detalles);				  
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
	$(".prove_activo").click(prove_activo);
	


	function prove_detalles(event){
			$id = $(this).attr('id');
			$empresa = $(this).attr('empresa');
			$contacto = $(this).attr('contacto');
			$ciudad = $(this).attr('ciudad');
			$obs = $(this).attr('obs');
			$dir = $(this).attr('dir');
			$tel = $(this).attr('tel');
			$cel = $(this).attr('cel');
			$correo = $(this).attr('correo');
			$(".prove_activo, .tr_detalles").unbind("click"); 

		 	//alert($id+'->'+$ciudad); return;
			$("#id").val($id);
			$("#txt_empresa").val($empresa);
			$("#txt_contacto").val($contacto);
			$("#txt_ciudad").val($ciudad);
			$("#txt_direccion").val($dir);
			$("#txt_telefono").val($tel);
			$("#txt_celular").val($cel);
			$("#txt_correo").val($correo);
			$("#txt_obs").val($obs);
			$("#cont_frm_prove").appendTo($("#popup_contenido"));
				  $("#cont_frm_prove").show();
				  $("#dialog_detalles").dialog({
					  width: "90%",
					  show: { effect: "blind", pieces: 8, duration: 30 },
					  title: ""+$empresa,
					  close: function( event, ui ) {   
						  $(".prove_activo").bind("click",prove_activo);
						  $(".tr_detalles").bind("click",prove_detalles);
					  },
					  buttons: {					  
						Cancelar: function() {
						  $( this ).dialog( "close" ); 							  
						},
						Guardar: function() {
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
							//$("#btn_guarda_prove").hide();
							var str_post = $("form[name='frm_edit_prove']").serialize();
							//alert(str_post); return;
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
								if(obj.tipo == "prove_update"){
									$("#popup_contenido").html('<div class="msg alerta_ok">Datos Guardados</div>');
									$("#dialog_detalles").dialog({
										buttons: {Aceptar: function(){$("#btn_listar_prove").click();}} 
									}); 
								}
								if(obj.tipo == "prove_error"){
									$("#ajax_update").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
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
	$(".tr_detalles").click(prove_detalles);
});
</script>

<div id="cont_registro_prove">

</div>
<div id="dialog_detalles" style="width:90%; display:none">
    <div id="popup_contenido" style="position: relative; overflow-y: scroll; height: auto;"></div>
</div>
<div id="cont_frm_prove" style="display:none">
  <form name="frm_edit_prove">
  <input type="hidden" name="accion" id="accion" value="edit_prove" />
  <input type="hidden" name="id" id="id" value="<?=$_POST['id'];?>" />
  <table width="100%" border="0">
  <tr class="info">
    <td colspan="2"><div class="titulo_frm">Detalles Proveedor</div></td>
  </tr>
  <tr>
    <td width="15%">Empresa:</td>
    <td><input type="text" name="empresa" id="txt_empresa" class="text_box" size="120" maxlength="200" value="" ></td>
  </tr>
  <tr>
    <td>Contacto:</td>
    <td><input type="text" name="contacto" id="txt_contacto" class="text_box" size="50" maxlength="200" value="" ></td>
  </tr>  
  <tr>
    <td>Direccion:</td>
    <td><input type="text" name="dir" id="txt_direccion" class="text_box" size="120" maxlength="1000" value="" ></td>
  </tr> 
  <tr>
    <td>Ciudad:</td>
    <td><input type="text" name="ciudad" id="txt_ciudad" class="text_box" size="50" maxlength="200" value="" ></td>
  </tr> 
  <tr>
    <td>Telefono:</td>
    <td><input type="text" name="tel" id="txt_telefono" class="text_box" size="50" maxlength="200" value="" ></td>
  </tr> 
  <tr>
    <td>Celular:</td>
    <td><input type="text" name="cel" id="txt_celular" class="text_box" size="50" maxlength="200" value="" ></td>
  </tr>             
  <tr>
    <td>Correo:</td>
    <td><input type="text" name="correo" id="txt_correo" class="text_box" size="50" maxlength="200" value="" ></td>
  </tr>  
  <tr>
    <td>Observaciones:<br />(<span id="numtxt_obs"> 0 </span>/ 1500 )</td>
    <td>
    	<label for="textarea"></label><textarea name="obs" id="txt_obs" cols="80" rows="5" onkeypress="return limita(1500,'txt_obs', event)" class="text_area"></textarea>    	
    </td>
  </tr>  
  <tr>
  	<td colspan="2"><div id="ajax_update"></div></td>
  </tr>  
  </table>
  </form>
</div>  