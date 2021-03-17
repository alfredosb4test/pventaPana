var $prod_existente=0;
var $cantidad_org = 0;
var $nombre_prod = "";
var $eq = -1; // variable que lleva la cantidad de procto a insertar
var $bar_cod = "" // variable que almacena el codigo de barras si es diferente al anterior la variable $eq se resetea 
$(document).ready(function(e)
{
	$("#txt_cj_codigo").focus();
	altura=$(window).height();
	// ******************************************  Aumentar la cantidad del producto
	$("#btn_insert_list").button({ 
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
		$id = $('#text_nombre_prod').attr('id_prod');
		$txt_sucursal = $('#lst_sucursales_admin_prod option:selected').text();
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: 'crud_pventas.php',
		 data: "accion=sumar_cantidad_producto&id="+$id+"&cantidad="+$cantidad,
		 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		 success: function(datos){ 					   		
			  var obj = jQuery.parseJSON(datos);
			  if(obj.status == "ok_update"){
				catidad_total = parseInt($cantidad_org) + parseInt($cantidad);
				//alert(catidad_total)
				$("#ajax_respuesta_insert").prepend('<div class="msg alerta_ok">Cantidad Agregada Correctamente. ('+$txt_sucursal+')<br><strong>Total:</strong> '+catidad_total+' - '+$nombre_prod+'</div>');
				
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
			buscar_producto($(e).val()); 
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
   data: "accion=pastelero_buscar_producto&codigo="+$codigo+"&id_sucursal="+$id_sucursal,
   beforeSend:function(){/* $("#ajax_respuesta").html($load); */},	 
   success: function(datos){ 
   //alert(datos) 	   
	  var obj = jQuery.parseJSON(datos);	
	  $("#ajax_respuesta").empty();	
	  //alert(obj.id);	
	  if(obj.status == "existe"){
		  // $valor_cantidad = $("#select_cantidad_caja").val();	// cantidad
		  var $eq;
		  $prod_existente=1;		  
		  $nombre_prod = obj.nombre;
		  $cantidad_org = obj.cantidad;
		  $('#text_nombre_prod').html('<div class="msg alerta_ok">'+$nombre_prod+'<strong> / Cantidad Actual ('+$cantidad_org+')</strong></div>');
		  $('#text_nombre_prod').attr('id_prod',obj.id);
		  $('#tr_cantidad').slideDown();
		  $("#txt_cj_codigo").attr('value','').focus();
		  
		  if($bar_cod != $codigo)
		  	$eq = 0;
		  else
		  	$eq+=1;
		  
		  $("#select_cantidad option:eq("+$eq+")").prop('selected', true); // seleccionar el primer elemento del select
		  $("#select_cantidad").selectmenu( "refresh" );	// aplicar el actualizar al select de lo contrario no pone el 1 en el texto del select		 
		  $bar_cod = $codigo;
		  //$('#ajax_respuesta_insert').empty();
	  }
	  if(obj.status == "no_existe"){
		  $("#text_nombre_prod").html('<div class="msg alerta_err">Producto no existente</div>');
	  }		 			  
	  if(obj.status == "error_sql"){
		  $("#text_nombre_prod").html('<div class="msg alerta_err">Problemas con el SQL</div>');
	  } 	
   },
   timeout:90000,
   error: function(){ 					
		  $("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
	  }	   
  });	
}