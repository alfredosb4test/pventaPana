var $prod_existente=0;
var $cantidad_org = 0;
var $nombre_codigo_unidades = "";
var $cantidad_total_unidades = 0;
var $nombre_prod = "";
var $eq = -1; // variable que lleva la cantidad de procto a insertar
var $bar_cod = "" // variable que almacena el codigo de barras si es diferente al anterior la variable $eq se resetea 
$(document).ready(function(e)
{
		  /****************************  Ocultar Combo y boton para convertir manual  ***********************************************/
		  $("#btn_convertir_unidades").hide();	// oculta el boton de dividir ya q por default sera 8
		  /***************************************************************************************************************************/	
	$("#txt_cj_codigo").focus();
	altura=$(window).height();
	// ******************************************  Aumentar la cantidad del producto
	$("#btn_convertir_unidades").button({ 
		text: true,
		icons: {
		  primary: "ui-icon-plusthick"
		}
    }).click(function( event ) {
		if($prod_existente == 0){
			$("#text_nombre_prod").html('<div class="msg alerta_err">Los datos no son correctos.</div>');
			return;
		}
		$cantidad =  $("#select_cantidad").val();
		$cantidad_total_unidades = parseInt($cantidad_total_unidades) + parseInt($cantidad);
		$id_prod = $('#text_nombre_prod').attr('id_prod');
		$id_codigo_unidades = $('#text_nombre_prod').attr('id_codigo_unidades');
		$txt_sucursal = $('#lst_sucursales_admin_prod option:selected').text();
		//alert($id_prod+" "+$id_codigo_unidades+" "+$cantidad); //return;
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: 'crud_pventas.php',
		 data: "accion=producto_a_unidades_update&id_prod="+$id_prod+"&id_codigo_unidades="+$id_codigo_unidades+"&cantidad="+$cantidad+"&nombre_prod_1="+$nombre_prod+"&nombre_prod_2="+$nombre_codigo_unidades,
		 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		 success: function(datos){ 	
		 	  //alert(datos) 					   		
			  var obj = jQuery.parseJSON(datos);
			  if(obj.status == "ok_update"){ 
				//alert(catidad_total)
				$("#ajax_respuesta_insert").prepend('<div class="msg alerta_ok">Cantidad Agregada Correctamente. ('+$txt_sucursal+')<br><strong>Total:</strong> '+$cantidad_total_unidades+' - '+$nombre_codigo_unidades+'</div>');
				
				$("#select_cantidad option:eq(0)").prop('selected', true); // seleccionar el primer elemento del select
				$("#select_cantidad").selectmenu( "refresh" );	// aplicar el actualizar al select de lo contrario no pone el 1 en el texto del select
				limpiar_datos_insert();	
			  }
			  if(obj.tipo == "no_update"){
				  $("#ajax_respuesta_insert").prepend('<div class="msg alerta_err">Problemas con el Servidor</div>');
			  }		 			  
			  
									
		 },
		 timeout:90000,
		 error: function(){ 					
				$("#ajax_respuesta_insert").prepend('<div class="msg alerta_ok">Problemas con el servidor intente de nuevo.</div>');
			}	   
		});
    });	
	
	$("#lst_sucursales_admin_prod").selectmenu({
		change: function( event, data ) {
			limpiar_datos_insert();
			if($id_sucursal == "")
				return;
		}
	});
	$("#select_cantidad").selectmenu({});
});
function limpiar_datos_insert(){
			$("#tr_cantidad").hide();
			$("#txt_cj_codigo").attr('value','').focus();
			$("#text_nombre_prod").empty();
			$prod_existente=0;
			$eq = -1;
}
function key_buscar_producto (elEvento, e) {	
	var evento = elEvento || window.event;
	var caracter = evento.charCode || evento.keyCode;
	if ( caracter == 13 ) {	
		$codigo = $(e).val();
		if($codigo == "") {
			$("#text_nombre_prod").html('<div class="msg alerta_err">Ingrese un codigo.</div>');
			return;
		}
		buscar_producto($codigo); 
	}
}
function buscar_producto($codigo){  
  $id_sucursal = $("#lst_sucursales_admin_prod").val();
  if($id_sucursal == ""){
	  $("#text_nombre_prod").html('<div class="msg alerta_err">Seleccione una Sucursal.</div>');
	  return;
  }
  $prod_existente=0;
  $('#tr_cantidad').hide();
  $.ajax({
   type: "POST",
   contentType: "application/x-www-form-urlencoded", 
   url: "crud_pventas.php",
   data: "accion=producto_a_unidades_buscar&codigo="+$codigo+"&id_sucursal="+$id_sucursal,
   beforeSend:function(){/* $("#ajax_respuesta").html($load); */},	 
   success: function(datos){ 
   //alert(datos) 	   
	  var obj = jQuery.parseJSON(datos);	
	  $codigo_unidades = obj.codigo_unidades;
	  $nombre_prod = obj.nombre;
	  $("#ajax_respuesta").empty();	
	  //alert(obj.id);		  
	  if(obj.status == "existe"){
		  // $valor_cantidad = $("#select_cantidad_caja").val();	// cantidad 
		  $prod_existente=1;
		  $cantidad_org = obj.cantidad;		  
		  // si al producto que se quiere convertir a unidades no tiene stock no procede
		  if($cantidad_org == 0){
			  $("#text_nombre_prod").html('<div class="msg alerta_err">Producto sin existencias.</div>');
			  return;
		  }
		  $nombre_codigo_unidades = obj.nombre_codigo_unidades;
		  $cantidad_total_unidades = obj.cantidad_codigo_unidades;
		  $prod_original = '<div class="msg alerta_ok">'+$nombre_prod+'<strong> / Cantidad Actual ('+$cantidad_org+')</strong>';
		  $prod_unidades = ', convertirlo a unidades para <strong>'+$nombre_codigo_unidades+'</strong> <strong> / Cantidad Actual ('+obj.cantidad_codigo_unidades+')</strong> </div>';
		  $('#text_nombre_prod').html($prod_original + $prod_unidades);
		  $('#text_nombre_prod').attr('id_prod',obj.id);								// id del producto a convertir a unidades
		  $('#text_nombre_prod').attr('id_codigo_unidades',obj.id_codigo_unidades);		// id del producto al q se le sumen las unidades
		  
		  /****************************  Ocultar Combo y boton para convertir manual  ***********************************************/
		  //$('#tr_cantidad').slideDown();	// oculta el combo q contiene la cantidad a dividir ya q por default sera 8 
		  /***************************************************************************************************************************/
		  
		  $("#txt_cj_codigo").attr('value','').focus();
		  
		  $("#select_cantidad option:eq(0)").prop('selected', true); // seleccionar el primer elemento del select
		  $("#select_cantidad").selectmenu( "refresh" );	// aplicar el actualizar al select de lo contrario no pone el 1 en el texto del select		 
		  $bar_cod = $codigo;
		  //$('#ajax_respuesta_insert').empty();
		  		
		  $("#nomb_prod_unidades").html($nombre_prod);	
		  $("#dialog_confirm_unidades").dialog({
			  width: 480,
			  resizable: false,
			  show: { effect: "blind", pieces: 8, duration: 10 },
			  title: "Agregar Sucursal",
			  close: function( event, ui ) {  
										  
			   },
			  buttons: {	
				Cancelar: function() {
					$( this ).dialog( "close" );
					limpiar_datos_insert();	
				},								  
				Aceptar: function() {
				   // $( this ).dialog( "close" );
					$("#btn_convertir_unidades").trigger('click');
					$( this ).dialog( "close" );
					$("#select_cantidad option:eq(0)").prop('selected', true); // seleccionar el primer elemento del select
					$("#select_cantidad").selectmenu( "refresh" );	// aplicar el actualizar al select de lo contrario no pone el 1 en el texto del select
					limpiar_datos_insert();																					  							
				}
			  }
			});			  
	  }
	  if(obj.status == "no_existe"){
		  $("#text_nombre_prod").html('<div class="msg alerta_err">Producto no existente</div>');
	  }		
	  if(obj.status == "codigo_unidades_no_disponible"){
		  if(obj.codigo_unidades != "")
		  	$("#text_nombre_prod").html('<div class="msg alerta_err">El producto con codigo: <strong>'+obj.codigo_unidades+'</strong> asociado a <strong>'+obj.nombre+'</strong> no esta disponible.</div>');
		  else
		  	$("#text_nombre_prod").html('<div class="msg alerta_err">Este producto no tiene asociado un codigo de producto para convertirlo a unidades.</div>');
	  }		 
	  if(obj.status == "codigo_unidades_no_existe"){
		  $("#text_nombre_prod").html('<div class="msg alerta_err">El producto con codigo: '+obj.codigo_unidades+' no existe.</div>');
	  }		   			  
	  if(obj.status == "error_sql"){
		  $("#text_nombre_prod").html('<div class="msg alerta_err">Problemas con el SQL</div>');
	  }
	  /* 
	  if(obj.codigo_unidades == ""){
		  $('#text_nombre_prod').html('<div class="msg alerta_err">'+$nombre_prod+'<strong> no tiene un codigo de producto vinculado para convertirlo a unidades.</strong></div>');
		  return;
	  }	
	  */  	
   },
   timeout:90000,
   error: function(){ 					
		  $("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
	  }	   
  });	
}