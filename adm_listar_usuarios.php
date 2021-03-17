<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
 
$array = $conn->listar_adm_usuarios($NumEmp, $_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal']);	
if($array == 'no_data'){
	echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
	exit;
}else{	
	$tabla = '<table id="list_usr" class="tbl_datos" border="0" cellpadding="2" cellspacing="0" width="100%">';
		$tabla .= "
		 	<tr class='f_negro table_top'>
				<th width='65%' id='th_ambito' >Usuario</th>
				<th width='30%'>Sucursal</th>
				<th width='5%'>Activo</th>
			</tr>";
			
	foreach($array['NumEmp'] as $key=>$nombre){
		if($array['registrado'][$key])
			$activo = '<img class="cliente_activo hand" id="'.$array['NumEmp'][$key].'" valor="0" src="images/ok.png">';
		else
			$activo = '<img class="cliente_activo hand" id="'.$array['NumEmp'][$key].'" valor="1" src="images/tache.png">';
	
		
		
		$tabla .= 
		'<tr class="tbl_fila tr_detalles hand" 
			id="'.$array['NumEmp'][$key].'"   				
			nombre="'.$array['Nombre'][$key].'"
			sucursal="'.$array['sucursal'][$key].'"
		 >
			<td>'.
				$array['Nombre'][$key]	
			.'</td>
			<td>'.$array['sucursal'][$key].'</td>
			<td align="center">'.$activo.'</td>
		</tr>';
	}
	$tabla .= "</table>";	
}
//echo "<pre>"; print_r($array); echo "</pre>"; 
?> 
<script type="text/javascript">
var error=0;
$(document).ready(function(e) {
	$( "#list_usr tr:odd" ).addClass("f_tr_hover");

	//**********************************************************************************
	// activar o desactivar USUARIO
	//**********************************************************************************
	function cliente_activo(event){
		event.stopPropagation();
		//event.preventDefault();		
		$event = $(this);
		$id = $event.attr('id');
		$valor = $event.attr('valor');
		$(".cliente_activo, .tr_detalles").unbind("click"); 
		//alert($valor);
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: "crud_pventas.php",
		 data: "accion=activar_desactivar_adm_usuario&valor="+$valor+"&id="+$id,
		 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		 success: function(datos){ 
		 //alert(datos)
			var obj = jQuery.parseJSON(datos);	
			$("#ajax_respuesta").empty();	
			//alert(obj);	
			if(obj.tipo == "cliente_activo"){ 
				$("#popup_contenido").html('<div class="msg alerta_ok">Datos Guardados</div>');
			
						  if($valor == 1)
						  	$event.attr({"src":"images/ok.png","valor":0});
						  else
						  	$event.attr({"src":"images/tache.png","valor":1});
			}
			if(obj.tipo == "cliente_error"){
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
						  //$("#btn_listar_cliente").click();
						  
						  $(".cliente_activo").bind("click",cliente_activo);
						  $(".tr_detalles").bind("click",cliente_detalles);	
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
	$(".cliente_activo").click(cliente_activo);
	

	//**********************************************************************************
	// Detalles del USUARIO opcion para cambiar password
	//**********************************************************************************
	function cliente_detalles(event){
			$("#txt_password").attr("value","");
			$id = $(this).attr('id');
			$nombre = $(this).attr('nombre');
			//$sucursal = $(this).attr('sucursal');

			$(".cliente_activo, .tr_detalles").unbind("click"); 

		 	//alert($id+'->'+$nombre); return;
			$("#id").val($id);
			$("#txt_nombre").val($nombre);
			//$("#txt_ciudad").val($ciudad);

			$("#cont_frm_cliente").appendTo($("#popup_contenido"));
				  $("#cont_frm_cliente").show();
				  $("#dialog_detalles").dialog({
					  width: "400px",
					  show: { effect: "blind", pieces: 8, duration: 30 },
					  title: ""+$nombre,
					  close: function( event, ui ) {   
						  $(".cliente_activo").bind("click",cliente_activo);
						  $(".tr_detalles").bind("click",cliente_detalles);
					  },
					  buttons: {					  
						Cancelar: function() {
						  $( this ).dialog( "close" ); 							  
						},
						Guardar: function() {
							error=0;
							$('#txt_password').jrumble({		
								x: 1,
								y: 1,
								rotation: .2,
								speed: 2,
								opacity: true
							}); // habilita efecto vibrar
							$codigo=$('#codigo').val();
							valida_campo2(["txt_password"],'','','',["txt_password"], ["#FF5D00"], ["#E6FACB"]);			
							if(error){	
								return;
							}
							//$("#btn_guarda_cliente").hide();
							var str_post = $("form[name='frm_edit_admin_usuario']").serialize();
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
								if(obj.tipo == "cliente_update"){
									$("#popup_contenido").html('<div class="msg alerta_ok">Datos Guardados</div>');
									$("#dialog_detalles").dialog({
										width: "500px",
										buttons: {Aceptar: function(){$("#btn_adm_usuarios").click();}} 
									}); 
								}
								if(obj.tipo == "cliente_error"){
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
	$(".tr_detalles").click(cliente_detalles);
	
	// Evento para Agregar un Empleado
	$("#lst_sucursales_admin_prod").selectmenu({});
	// Evento para Mostrar formulario de nuevo empleado
	$("#btn_new_user").button({ 
		text: true,
		icons: {
		  primary: "ui-icon-plusthick"
		}
    }).click(function( event ) {
		$("#cont_registro_cliente").slideDown();
	});
	// Evento para Agregar un Empleado
	$("#btn_insert_user").button({ 
		text: true,
		icons: {
		  primary: "ui-icon-plusthick"
		}
    }).click(function( event ) {
		error=0;
		valida_campo2(["txt_nombre","txt_pwd"],'','','',["txt_nombre","txt_pwd"], ["#FF5D00"], ["#E6FACB"]);			
		if(error){	
			return;
		}
		$suc = $("#lst_sucursales_admin_prod").val();
		$nombre = $("#txt_nombre").val();
		$pwd = $("#txt_pwd").val();		
		if($suc == ""){
			$("#alert_user_new").html('<div class="msg alerta_err">Los datos no son correctos.</div>');
			return;
		}

		$cantidad =  $("#select_cantidad").val();
		$id = $('#text_nombre_prod').attr('id_prod');
		$txt_sucursal = $('#lst_sucursales_admin_prod option:selected').text();
		$.ajax({
		 type: "POST",
		 
		 contentType: "application/x-www-form-urlencoded", 
		 url: 'crud_pventas.php',
		 data: "accion=new_empleado&sucursal="+$suc+"&nombre="+$nombre+"&pwd="+$pwd,
		 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		 success: function(datos){ 					   		
			  var obj = jQuery.parseJSON(datos);
			  //alert(datos)
			  if(obj.tipo == "insert_ok"){
				    $("#txt_nombre, #txt_pwd").attr("value","");
					$("#alert_user_new").html('<div class="msg alerta_ok">Usuario agregado.</div>');
					$("#btn_adm_usuarios").trigger("click");

			  }
			  if(obj.tipo == "duplicado"){
				  $("#alert_user_new").html('<div class="msg alerta_err">Este usuario ya existe.</div>');
			  }
			  if(obj.tipo == "error_execute"){
				  $("#alert_user_new").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
			  }		 			  
			  if(obj.tipo == "error_sql"){
				  $("#alert_user_new").html('<div class="msg alerta_err">Problemas con el SQL</div>');
			  } 	 			  			  				
		 },
		 timeout:90000,
		 error: function(){ 					
				$("#ajax_respuesta_insert").prepend('<div class="msg alerta_ok">Problemas con el servidor intente de nuevo.</div>');
			}	   
		});
    });	
});
</script>

 
<div id="dialog_detalles" style="width:250px; display:none">
    <div id="popup_contenido" style="position: relative; height: auto"></div>
</div>
<button class=""  id="btn_new_user" style="width:200px;">Nuevo Empleado</button>
<div id="cont_registro_cliente" style="display:none;">
    <table>
        <tr>
          <td>Nombre:</td>
          <td width="350">
              <input class="text_box" id="txt_nombre" type="text" value=""  style="width:250px;" required maxlength="25">
          </td>
          <td width="170"><label for="textfield">Selecciona una Sucursal: </label></td>
          <td width="300"> 
              <span class="cont_sucursales_admin" style="display: ; position: relative; margin:0; float:left;">
                    <select id='lst_sucursales_admin_prod' name='lst_sucursales_admin' style="width:250px; "  >
                      <?php echo $conn->lst_sucursales_admin('tbl_sucursal', 'id_sucursal', 'sucursal', $_SESSION['g_id_sucursal'], '', $_SESSION['g_id_empresa'],$_SESSION['g_sucursales']); ?> 
                   </select>
              </span>
          </td>
        <td align="">
            <button class=""  id="btn_insert_user" style="width:200px;">Agregar Usuario</button>
        </td>
        </tr> 
		<tr>
          <td>Contraseña:</td>
          <td width="350">
              <input class="text_box" id="txt_pwd" type="text" value=""  style="width:250px;" required maxlength="25">
          </td>
                                         
        </tr>    
    </table> 
    <div id="alert_user_new">  </div>
</div>  
<?=$tabla;?>   


  
<div id="cont_frm_cliente" style="display: none">
  <form name="frm_edit_admin_usuario">
  <input type="hidden" name="accion" id="accion" value="edit_adm_usuario" />
  <input type="hidden" name="id" id="id" value="<?=$_POST['id'];?>" />
  <table width="100%" border="0">

  <tr>
    <td width="150">Nueva Contraseña:</td>
    <td><input type="text" name="password" id="txt_password" class="text_box" size="15" maxlength="15" value="" ></td>
  </tr>   
  <tr>
  	<td colspan="2"><div id="ajax_update"></div></td>
  </tr>  
  </table>
  </form>
</div>  
<div class="growlUI" style="cursor: default; display: none;">
            <h1>Empleado Insertado</h1>
</div>