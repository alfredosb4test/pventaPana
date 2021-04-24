var $iva = $("#txt_IVA").val();
var error=0;
var $array_id = [];
var $array_cantidad = [];
var $array_cantidad_solicitada = [];
var $array_precio_prod = [];
var $existencias_prod=0;  // En devoluciones si no hay existencias que no descuente
var $aprobacion_card = "";
var $img = "";
var $cajaFocus = "txt_cj_nombre";	// txt_cj_codigo
var $card = "";
var $ultimo_codigo_prod = "";
var $cont_generico = 0;	// lleva un conteo para cada producto generico agregado
var $itemRecuperados = false; // si la lista es recuperada  de un pendiente es true
var $fileItemsPend = ''; // almacena el nombre del archivo de listado pendientes en caja
var $cantidad_estricta = ''; // almacena "1,0" 1 para cantidades estrictas el sistema no permite cantidad negativa
function img_mouseenter(event){
	console.log("event img_mouseenter")
	$(event).mouseenter(function(e) {
		altura_img = $(this).offset().top;
		if(altura_img > 280){
			y_top = 120;
		}else{
			y_top = 10;
		}

		// Calculate the position of the image tooltip
		x = e.pageX - $(this).offset().left;
		y = e.pageY - $(this).offset().top;
	   
		// Set the z-index of the current item,
		// make sure it's greater than the rest of thumbnail items
		// Set the position and display the image tooltip
		$(this).css('z-index','15')
		.children("div.tooltip")
		.css({'top': y - y_top,'left': x + 15,'display':'block'});
		  
	   }).mousemove(function(e) {
				altura_img = $(this).offset().top;
				if(altura_img > 280){
					y_top = 120;
				}else{
					y_top = 10
				}
		// Calculate the position of the image tooltip  
		x = e.pageX - $(this).offset().left;
		y = e.pageY - $(this).offset().top;
		
		// This line causes the tooltip will follow the mouse pointer
		$(this).children("div.tooltip").css({'top': y - y_top,'left': x + 15});
		  
	   }).mouseleave(function() {
		  
		// Reset the z-index and hide the image tooltip
		$(this).css('z-index','1')
		.children("div.tooltip")
		.animate({"opacity": "hide"}, "fast");
	   });
}
 
$(document).ready(function(e) {  

	$cajaFocus = $('#txt_focus_caja').val();
	console.log('cajaFocus = ', $cajaFocus )
	$altura = $(window).height();
	$("#ajax_items_add, #btn_productos").css("height",($altura-187));
    $("#txt_cj_codigo, #recibe, #txt_cj_nombre").bind('keydown.ctrl_j', function (evt) { 
        return false;
	});
	$(document).unbind();
    $("#txt_cj_codigo, #txt_cj_nombre").bind('keydown.F2', function (evt) {  $('#btn_caja_cobrar').click(); });
    $(document).bind('keydown.F2', function (evt) {  $('#btn_caja_cobrar').click(); });

    $("#txt_cj_codigo, #txt_cj_nombre").bind('keydown.F3', function (evt) {  $('#btn_caja_pago').click(); });
    $(document).bind('keydown.F3', function (evt) {  $('#btn_caja_pago').click(); });

    $("#txt_cj_codigo, #txt_cj_nombre").bind('keydown.F4', function (evt) {  $('#btn_generico').click(); });
	$(document).bind('keydown.F4', function (evt) {  $('#btn_generico').click(); });
	 
	// Evento de flecha derecha para agregar producto y flecha izquierda para quitar cantidad
	$(document).bind('keydown',function(e){
      key  = e.keyCode;
      if(key == 39){
				if($ultimo_codigo_prod){
					cantidadProd = $("#textbox_precio0_0"+$ultimo_codigo_prod).attr('prod_stock');
					add_cantidad($ultimo_codigo_prod, cantidadProd); 
				}
      }else if(key == 37){
            if($ultimo_codigo_prod)
							del_cantidad($ultimo_codigo_prod); 
    	}
	});
	
	filePendiente();
	
 



	$('#'+$cajaFocus).focus();

	$('#iva').html($iva+"%");
	$("#f_inicial").datepicker(
		{   
			dateFormat: 'yy-mm-dd',
			showAnim:"drop",
			showOn:"button",
			buttonImage:"images/calendario.png",
			buttonImageOnly:true,
			maxDate: '+1Y',
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			changeMonth: true,
			numberOfMonths: 1,
			onClose: function( selectedDate ) {
			  $( "#f_final" ).datepicker( "option", "minDate", selectedDate );
			}
	});	
	
	
	$("#f_final").datepicker(
		{   
			dateFormat: 'yy-mm-dd',
			showAnim:"drop",
			showOn:"button",
			buttonImage:"images/calendario.png",
			buttonImageOnly:true,
			maxDate: '+1Y',
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			changeMonth: true,
			numberOfMonths: 1,
			onClose: function( selectedDate ) {
			  $( "#f_inicial" ).datepicker( "option", "maxDate", selectedDate );
			}			
	});	

	$("#f_devo").datepicker(
		{   
			dateFormat: 'yy-mm-dd',
			showAnim:"drop",
			showOn:"button",
			buttonImage:"images/calendario.png",
			buttonImageOnly:true,
			maxDate: '+1Y',
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			changeMonth: true,
			numberOfMonths: 1 		
	});	
			
	$("#txt_cj_nombre").autocomplete({
		source: "crud_pventas.php?accion=autocompleta_producto_caja",			
		//appendTo: '#menu-container',
		minLength: 3,						
		select: function (event, ui) {				
			buscar_producto(ui.item.codigo);
		    $('#txt_cj_nombre').attr('value','').focus();						
		},
	});	
	/*********************************** Aplicar Mayoreo ******************************************/
	$("#btn_maayoreo").toggle(
		function(){
			calcular_totales_mayoreo();
			$(this).children().eq(0).html("Menudeo");
		},
		function(){
			calcular_totales_menudeo();
			$(this).children().eq(0).html("Mayoreo");
		}
	);
	/*****************************************************************************************/
	$("#btnPDF").click(function(){
		//alert($array_id+"\n"+$array_cantidad_solicitada)
		day = new Date();	
		id = day.getTime();
		$html = $("#ajax_items_add").clone();			
		URL="ticket_html.php?array_id="+$array_id+"&array_cantidad_solicitada="+$array_cantidad_solicitada+"&array_precio_prod="+$array_precio_prod+"&total="+$total+"&nombre_cajero="+$nombre_cajero;
		//$("#ticket_html").attr("src",URL);
		//$("#ticket_html").focus(); $("#ticket_html").printArea({mode: "iframe", popClose: false, popWd:200, popHt:70});
	//ticket.focus();ticket.print(); return;
	//URL="ticket_html.php?array_id="+$array_id+"&array_cantidad_solicitada="+$array_cantidad_solicitada;
	window.open(URL, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=1, left=100, width=800, height=600");
	//eval("page" + id + " = window.open(URL, '" + id + "','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=1,resizable=1,width=700,height=600');");		
	});
	
	
	
	// Retiro o Entrada de DINERO
	$('#btn_caja_pago').click(function(){			
			$("#txt_catidad_retiro").addClass('text_box');
			$("#dialog_salida_dinero").dialog({
				width: 450,
				resizable: false,
				show: { effect: "blind", pieces: 8, duration: 10 },
				title: "Retiro / Ingreso",
				close: function( event, ui ) {  
					$($cajaFocus).focus();					  			  
				 },
				buttons: {	
				  Cancelar: function() {
					  $("#txt_catidad_retiro, #txt_comentario_retiro").val("");
					  $( this ).dialog( "close" );
				  },								  
				  Aceptar: function() {
					  error=0;
					  $comentario_retiro = $("#txt_comentario_retiro").val();
					  $catidad_retiro = $("#txt_catidad_retiro").val();
					  $concepto = $("#opt_concepto").val();

					  valida_campo2(["txt_catidad_retiro"],'','','',["txt_catidad_retiro"], ["#FFD13A"], ["#E6FACB"]);			
					  if(error){
						  $('#txt_catidad_retiro').focus();		
						  return;
					  }					  
					  //alert($comentario_retiro);
					  $.ajax({
					   type: "POST",
					   contentType: "application/x-www-form-urlencoded", 
					   url: 'crud_pventas.php',
					   data: "accion=salida_dinero&comentario_retiro="+$comentario_retiro+"&catidad_retiro="+$catidad_retiro+'&concepto='+$concepto+'&rd='+Math.random(),
					   beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
					   success: function(datos){ 					   		
					   		var obj = jQuery.parseJSON(datos);	
						   	if(obj.status == "retiro_ingreso_registrada"){
								 $("#txt_catidad_retiro, #txt_comentario_retiro").val("");
								 $("#ajax_respuesta").empty();
								 $("#dialog_salida_dinero").dialog( "close" );
								 $($cajaFocus).focus();
							}
						   	//$("#popup_contenido").append($sql);							
					   },
					   timeout:90000,
					   error: function(){ 					
							  $("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
						  }	   
					  });					  
					
				  }
				}
			});
	});
	//************************************************************ Seleccionar Pagar con Tarjeta de Credito
	$(".card").click(function(){
		$img = $(this).attr("img");		
		$card = $(this).attr("card");
		$("#icon_card").attr("src", $img);
		$("#cont_aprobacion_card").show("drop");
		$("#txt_aprobacion_card").focus();
	});	

	//****************************************************** HISTORIAL VENTAS
	$('#btn_ventas_realizadas').click(function(){
		  $fecha = $("#f_inicial").val();
		  //alert($fecha)
		  $.ajax({
		   type: "POST",
		   contentType: "application/x-www-form-urlencoded", 
		   url: 'crud_pventas.php',
		   data: "accion=ventas_usuario&fecha="+$fecha,
		   beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		   success: function(datos){ 
				$("#ajax_ventas_usr").html(datos);
				$("#ajax_respuesta").empty();	
				$("#dialog_ventas_usr").dialog({
					width: 850,
					resizable: false,
					show: { effect: "blind", pieces: 8, duration: 10 },
					title: "Historial Ventas del Dia",
					close: function( event, ui ) {  
						$($cajaFocus).focus();
					 },
					buttons: {					  
					  Cancelar: function() {
						  //$("#popup_contenido, #ajax_respuesta, #cambio_cliente").empty();
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
	});
	
	//****************************************************** HISTORIAL PAGOS
	$('#btn_pagos_realizados').click(function(){
		  $fecha = $("#f_inicial").val();
		  //alert($fecha)
		  $.ajax({
		   type: "POST",
		   contentType: "application/x-www-form-urlencoded", 
		   url: 'crud_pventas.php',
		   data: "accion=pagos_usuario&fecha="+$fecha,
		   beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		   success: function(datos){ 
				$("#ajax_pagos_usr").html(datos);
				$("#ajax_respuesta").empty();	
				$("#dialog_pagos_usr").dialog({
					width: 850,
					resizable: false,
					show: { effect: "blind", pieces: 8, duration: 10 },
					title: "Historial Pagos del Dia",
					close: function( event, ui ) {  
						   $($cajaFocus).focus();	
					 },
					buttons: {					  
					  Cancelar: function() {
						  //$("#popup_contenido, #ajax_respuesta, #cambio_cliente").empty();
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
	});	
	
	//****************************************************** DEBEN
	$('#btn_deben').click(function(){
		$('#txt_nombre_deben, #txt_catidad_deben, #txt_comentario_deben').attr("value","");
		$('#txt_nombre_deben, #txt_catidad_deben').removeClass('text_box_alert').addClass('text_box');
		$("#dialog_deben").dialog({
			width: 440,
			resizable: false,
			show: { effect: "blind", pieces: 8, duration: 10 },
			title: "Pendiente de Pago",
			close: function( event, ui ) {  
				$($cajaFocus).focus();
			 },
			buttons: {					  
			  Cancelar: function() {
				  $( this ).dialog( "close" );
			  },
			  'Pendientes': function() {
				  $( this ).dialog( "close" );
				  debenLista('debe');
			  },
			  'Pagados': function() {
				  $( this ).dialog( "close" );
				  debenLista('pagado');
			  },
			  Guardar: function() {
				error = 0;
				$nombre_deben = $("#txt_nombre_deben").val(); 
				$catidad_deben = $("#txt_catidad_deben").val(); 
				$comentario_deben = $("#txt_comentario_deben").val(); 
				console.log($nombre_deben);
				valida_campo2(["txt_nombre_deben", "txt_catidad_deben"],'','','',["txt_nombre_deben","txt_catidad_deben"], ["#FFD13A"], ["#E6FACB"]);			
				if(error){
					//$('#txt_nombre_deben').focus();		
					return;
				}					  
				console.log($nombre_deben);
				$.ajax({
				type: "POST",
				contentType: "application/x-www-form-urlencoded", 
				url: 'crud_pventas.php',
				data: "accion=debe_usuario&comentario_deben="+$comentario_deben+"&catidad_deben="+$catidad_deben+'&nombre_deben='+$nombre_deben+'&rd='+Math.random(),
				beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
				success: function(datos){ 
						// console.log("test1 "+datos);					   		
						var obj = jQuery.parseJSON(datos);	
						 
						if(obj.status == "ok"){
							$("#ajax_respuesta").empty();
							$("#dialog_deben").dialog( "close" );
							$($cajaFocus).focus();
						}
						//$("#popup_contenido").append($sql);							
				},
				timeout:90000,
				error: function(){ 					
						$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
					}	   
				});
			  }
			}
		});								 							 			
						 
  });	
	//****************************************************** LISTAR DEBEN
	function debenLista($estatus){
		$fecha = $("#f_inicial").val();
		//alert($fecha)
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: 'crud_pventas.php',
		 data: "accion=debenLista&fecha="+$fecha+"&estatus="+$estatus,
		 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		 success: function(datos){ 
			$("#ajax_deben_lista").html(datos);
			$("#ajax_respuesta").empty();	
			$("#dialog_deben_lista").dialog({
				width: 850,
				resizable: false,
				show: { effect: "blind", pieces: 8, duration: 10 },
				title: "Clientes que deben",
				close: function( event, ui ) {  
					$( this ).dialog( "close" );
				},
				buttons: {	
					Regresar: function() {
						$("#dialog_deben").dialog( "open" );
						$( this ).dialog( "close" );
					},				  
					Cancelar: function() {
						//$("#popup_contenido, #ajax_respuesta, #cambio_cliente").empty();
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
  
	//****************************************************** DEVOLUCION PRODUCTO
	$('#btn_dvlcion').click(function(){
		$("#ajax_dvlcion, #ajax_dvlcion_err").empty();
		$("#dialog_dvlcion").dialog({
			width: '90%',
			resizable: false,
			show: { effect: "blind", pieces: 8, duration: 10 },
			title: "Devolucion de Producto",
			close: function( event, ui ) {  
				$($cajaFocus).focus();	
			 },
			buttons: {					  
			  Cancelar: function() {
				  $("#txt_codigo_dvlcion, #txt_comentario_dvlcion, #txt_id_prod").val("");
				  $( this ).dialog( "close" );
			  },
			  Guardar: function() {
				  $id = $("#txt_id_prod").val();
				  $motivo = $("#txt_comentario_dvlcion").val();
				  if($id == "")
				  	return;
				  if($existencias_prod == 1)
				  	return;
					
				  $.ajax({
				   type: "POST",
				   contentType: "application/x-www-form-urlencoded", 
				   url: 'crud_pventas.php',
				   data: "accion=prod_devolucion&id="+$id+"&motivo="+$motivo,
				   beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
				   success: function(datos){ 
				   //alert(datos)
				   		$("#ajax_respuesta").empty();
						var obj = jQuery.parseJSON(datos);	
						if(obj.status == "ok_update"){
							$("#txt_codigo_dvlcion, #txt_comentario_dvlcion, #txt_id_prod").val("");
							$("#dialog_dvlcion").dialog( "close" );
						}
						if(obj.status == "no_update"){
							 $("#ajax_dvlcion_err").html('<div class="msg alerta_err">Problemas con el SQL</div>');
						}						
												
				   },
				   timeout:90000,
				   error: function(){ 					
						  $("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
					  }	   
				  });	
				  
			  }			  
			}
		});	
	}); 

	//****************************************************** AGREGAR UN PRODUCTO GENERICO
	$('#btn_generico').click(function(){
		$("#ajax_generico, #ajax_generico_err").empty();

		$("#dialog_generico").dialog({
			width: 550,
			resizable: false,
			show: { effect: "blind", pieces: 8, duration: 10 },
			title: "Producto Generico",
			close: function( event, ui ) {  
					$($cajaFocus).focus();	
				 	$("#txt_nombre_generico, #txt_costo_generico, #txt_ganancia_generico").addClass('text_box');

			 },
			buttons: {					  
			  Cancelar: function() {
				  $("#txt_nombre_generico, #txt_costo_generico, #txt_ganancia_generico").val("");
				  $( this ).dialog( "close" );
			  },
			  Guardar: function() {
				error = 0;
				$cont_generico++;
				$id = $("#txt_id_prod").val();
				$valor_cantidad = 1;	// cantidad
				$txt_nombre_generico = $("#txt_nombre_generico").val();
				$precio_venta = $("#txt_costo_generico").val();			// Precio del producto
				$ganancia_gen = $("#txt_ganancia_generico").val();		// Ganancia del producto
				$precio_venta_mayoreo = $("#txt_costo_generico").val();	// Precio del producto mayoreo	
				$precio_venta_proveedor = $precio_venta - $ganancia_gen;

				valida_campo2(["txt_nombre_generico", "txt_costo_generico"],'','','',["txt_nombre_generico", "txt_costo_generico"], ["#FFD13A"], ["#E6FACB"]);			
				if(error){ 	
					return;
				}	

				/****************************************** COMPROBAR LAS CANTIDADES ******************************************/

				if(!( $.isNumeric($precio_venta) )){
					$("#ajax_generico_err").html('<div class="msg alerta_err">Cantidad no valida.</div>');
					return;
				}
				
				if(!( $.isNumeric($ganancia_gen) )){
				$("#ajax_generico_err").html('<div class="msg alerta_err">Cantidad no valida.</div>');
				return;
				}
				
				$id_prod_generico = 1433+"-"+$cont_generico;	
				$ganancia_gen = $ganancia_gen * 1; 	
				$codigo = "prod_generico_"+$cont_generico;
				$precio_item_total = $valor_cantidad * $precio_venta;						// para el caso de menudeo 
				$precio_item_total_mayoreo = $valor_cantidad * $precio_venta_mayoreo;		// para el caso de mayoreo
				$caja_cantidad = "<input type='text' value='"+$valor_cantidad+"' id='textbox_"+$codigo+"' class='txt_caja_precio' size='1' readonly>";
			
				$caja_precioMayoreo_item = '<input type="text" status="inactivo" ganancia="'+$ganancia_gen.toFixed(2)+'" proveedor="'+$precio_venta_proveedor+'" menudeo="'+$precio_item_total.toFixed(2)+'" mayoreo="'+$precio_item_total_mayoreo.toFixed(2)+'" onclick="del_mayoreo(\''+$codigo+'\')" style="display:none;border:1px solid #03F; padding:1px; height:25px;" value="$'+$precio_item_total_mayoreo.toFixed(2)+'" id="textbox_precio1_1'+$codigo+'" codigo="'+$codigo+'" id_tbl='+$id_prod_generico+' prod_stock='+$valor_cantidad+'  class="txt_caja_precio f_amarillo hand" size="8" readonly>';
				$caja_precio_item   	   = '<input type="text" status="activo" ganancia="'+$ganancia_gen.toFixed(2)+'" proveedor="'+$precio_venta_proveedor+'" menudeo="'+$precio_item_total.toFixed(2)+'" mayoreo="'+$precio_item_total_mayoreo.toFixed(2)+'" value="$'+$precio_item_total.toFixed(2)+'" id="textbox_precio0_0'+$codigo+'" codigo="'+$codigo+'" id_tbl='+$id_prod_generico+' prod_stock='+$valor_cantidad+'  class="txt_caja_precio" size="8" readonly>';		  		  
				// si el nombre del prod excede de 48 caracteres se ajusta
				if($txt_nombre_generico.length >= 48)
				$item_nombre =  '<div class="item_nombre f_left"><img src="images/borrar.png" width="25" height="25" onclick="del_item(\''+$codigo+'\')" class="hand" style="top: 5px; position: relative;"><span id="cantidad0_0'+$codigo+'">('+$valor_cantidad+') \t '+$txt_nombre_generico+'</span></div>';
				else
				$item_nombre =  '<div class="item_nombre_corto f_left"><img src="images/borrar.png" width="25" height="25" onclick="del_item(\''+$codigo+'\')" class="hand" style="top: 5px; position: relative;"><span id="cantidad0_0'+$codigo+'">('+$valor_cantidad+') \t '+$txt_nombre_generico+'</span></div>';
				
				// almacena la fila para el ITEM	

				$del_cantidad =  '<img src="images/cantidad_del.png" title="Disminuir" width="25" height="25" onclick="del_cantidad(\''+$codigo+'\')" class="hand" style="top: 5px; position: relative;">';
				$add_cantidad =  '<img src="images/cantidad_add.png" title="Aumentar" width="25" height="25" onclick="add_cantidad(\''+$codigo+'\')" class="hand" style="top: 5px; position: relative;">';

				$table_precio = "<table border='0' width='100%' height='46'><tr><td width='60%'>"+$item_nombre+"</td><td width='30%' align='right'>"+$del_cantidad+" - "+$add_cantidad+"</td><td width='30'>"+$caja_cantidad+"</td><td width='80'>"+$caja_precio_item+$caja_precioMayoreo_item+"</td></tr></table>"
				
				$("#ajax_items_add").append('<div class="item" id="'+$codigo+'" nombre_generico="'+$txt_nombre_generico+'">'+$table_precio+'</div>'); // Este id comprueba si ya existe este prod en la lista
				$("#ajax_items_alert").empty();
				$('#select_cantidad_caja option[value=1]').attr('selected', true);	
				$('#textbox_'+$codigo).attr('value', $valor_cantidad);		
				calcular_totales();  		
				$("#txt_nombre_generico, #txt_costo_generico, #txt_ganancia_generico").val("");		  
				$( this ).dialog( "close" );
			  }			  
			}
		});	
	}); 

	//************************************************************ COBRAR
	$('#btn_caja_cobrar').click(function(){
		//alert($('.item').length);
		$array_id = [];
		$array_nomb_generico = [];	 	// si se agrega un producto genericou
		$array_cantidad = [];
		$array_cantidad_solicitada = [];
		$array_ganancia_prod = [];
		$array_precio_prod = [];
		$ganancia_total = 0;
		$('#recibe').attr("value","");		
		if ($('.item').length){
			$nombre_cajero = $('#txt_usr_nombre').val();
			$total = $('#txt_total').val();	// total calculado de todos los productos
			//alert($nombre_cajero)
			$nombre="<tr><td>Cajero:</td><td class='t_azul_fuerte'>"+$nombre_cajero+"</td></tr>";
			//$total="<tr class='negritas'><td>Total:</td><td>"+$total+"</td></tr>";
			$("#td_total_cobrar").html($total);
			$("input[status^='activo']").each(function() { // status^='activo'		id^='textbox_precio0_0'
				$id_tbl = $(this).attr("id_tbl"); 	// obtener el id de la tabla productos
				$precio  = $(this).val();			// precio
				$id = $(this).attr("codigo");		// codigo		$id.split("0_0");
 
				$ganancia = parseFloat($(this).attr("ganancia"));		// ganancia del prod
 
				$ganancia_total = $ganancia_total + $ganancia;
				
				// Detectar si hay productos genericos con el simbolo - en el id_producto
				if($id_tbl.indexOf("-") !== -1){
					$obj_nomb_generico = new Object();
					$id_generico = $id_tbl.split("-");
					// buscamos el DIV que contiene el item y al que se le agrego el attributo nombre_generico
					$nombre_generico = $("#prod_generico_"+$id_generico[1]).attr('nombre_generico');					

					$obj_nomb_generico.id = $id_generico[1];
					$obj_nomb_generico.nom = $nombre_generico;
					$array_nomb_generico.push($obj_nomb_generico);
				}

				$array_id.push($id_tbl);
				$array_precio_prod.push($precio);
				$array_ganancia_prod.push($ganancia);
				$cantidad = $("#textbox_"+$id).val();
				
				$stock = $(this).attr("prod_stock");
				$array_cantidad.push($stock-$cantidad);
				$array_cantidad_solicitada.push($cantidad);
			}); 
	 		 
			if( $array_nomb_generico.length ) {
			 	$JSON_nomb_generico=JSON.stringify($array_nomb_generico);
			}else
				$JSON_nomb_generico = "";
	 		 
			//alert($JSON_nomb_generico)				 
			$("#popup_contenido").html("<table class='font_15'>"+$nombre+"</table>");
			$('#recibe').focus();
			$("#dialog_detalles").dialog({
				width: 450,
				resizable: false,
				show: { effect: "blind", pieces: 8, duration: 10 },
				title: "Cobrar",
				closeOnEscape: true,
				close: function( event, ui ) {  
					  limpiar_datos_cobrar();	
					  $( this ).dialog( "close" );
					  $($cajaFocus).focus();						  			  
				 },
				 
				buttons: {						
				  Cancelar: function() {
				  		limpiar_datos_cobrar();
						$( this ).dialog( "close" );
						$($cajaFocus).focus();		
				  },								  
				  Aceptar: function() {
					 // $( this ).dialog( "close" );
					  $aprobacion_card = $("#txt_aprobacion_card").val();
					  if($aprobacion_card == "")
							$card = "";
							
						if( $itemRecuperados ){
							eliminarItemsPend( $fileItemsPend );
						}
					  //alert($card+" | "+$aprobacion_card)
					  $.ajax({
					   type: "POST",
					   contentType: "application/x-www-form-urlencoded", 
					   url: 'crud_pventas.php',
					   data: "accion=update_productos&array_id="+$array_id+"&array_cantidad="+$array_cantidad+'&array_cantidad_solicitada='+$array_cantidad_solicitada+'&array_precio_prod='+$array_precio_prod+'&array_ganancia_prod='+$array_ganancia_prod+'&ganancia_total='+$ganancia_total+'&total='+$total+'&card='+$card+'&aprobacion_card='+$aprobacion_card+'&nombre_genericos='+$JSON_nomb_generico+'&rd='+Math.random(),
					   beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
					   success: function(datos){ 
						 //	
								console.log(datos);
					   		var obj = jQuery.parseJSON(datos);	
						   	if(obj.status == "ok_insert"){
									$("#ajax_respuesta, #ajax_items_add, #ajax_items_alert").empty();
									$("#subTotal, #total").html("");
									$("#dialog_detalles").dialog( "close" );
									$($cajaFocus).focus();
									$aprobacion_card = "";
									$img = "";
									$("#icon_card").attr("src", "");
								}
								if(obj.status == "error"){
									console.log("error update.insert");
								}
						   	//$("#popup_contenido").append($sql);							
					   },
					   timeout:90000,
					   error: function(){ 					
							  $("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
						  }	   
					  });					  
					
				  }
				}
			});
		}
	});

});	

/******************************** Calcula el cambio ********************************************/
function key_cobrar (elEvento, e) {	
	var evento = elEvento || window.event;
	var caracter = evento.charCode || evento.keyCode;
	//alert(caracter)
	console.log('key_cobrar'+caracter);
	$recibe = $('#recibe').val();
	$total = $('#txt_total').val();
	$cambio = $recibe-$total;

	if (caracter == 8)
		$('#cambio_cliente').empty();
	if(isNaN($cambio))
		return;
	if($cambio < 0)
		return;

		 			
	$('#cambio_cliente').html($cambio.toFixed(2));
}
/******************************** Al presionar ENTER que Guarde ********************************************/
$("#dialog_detalles").keydown(function (event) {if (event.keyCode == 13) {
        $(this).parent().find("button:eq(2)").click();
        return false;
    }
});

/******************************** BUSCAR PROD PARA AGREGARLO A LA LISTA DE LA CAJA ********************************************/
function key_buscar_producto (elEvento, e) {	
	//alert("key_buscar_producto");
	var evento = elEvento || window.event;
	var caracter = evento.charCode || evento.keyCode;
	if ( caracter == 13 ) {	
		if($(e).attr("id") == "txt_cj_codigo")
			buscar_producto($(e).val());
		else
			buscar_producto_devo($(e).val());
	}
}
function buscar_producto($codigo){
  $.ajax({
   type: "POST",
   contentType: "application/x-www-form-urlencoded", 
   url: "funciones/buscar_prod_caja.php",
   data: "accion=cj_buscar_producto&codigo="+$codigo,
   beforeSend:function(){/* $("#ajax_respuesta").html($load); */},	 
   success: function(datos){ 
   	  //alert(datos)
	  $('#txt_cj_codigo').attr("value","");	
	  $('#txt_cj_nombre').attr('value','');	   
	  var obj = jQuery.parseJSON(datos);	
	  $("#ajax_respuesta").empty();	
	  //alert(datos);	
	  if(obj.status == "existe"){
		  $valor_cantidad = $("#select_cantidad_caja").val();	// cantidad
		  $cantidad_estricta = $("#txt_cantidad_estricta").val();	// cantidad_estricta si esta en 1 no permite agregar item si no hay existencias
		 console.log('$cantidad_estricta:', $cantidad_estricta); 
		  $precio_venta_proveedor = obj.precio_provedor; 		// Precio del producto proveedor
		  $precio_venta = obj.precio_venta; 					// Precio del producto 
		  $precio_venta_mayoreo = obj.precio_mayoreo; 			// Precio del producto mayoreo
		  $cantidadMostrar = 0;									// Muestra la antidad del producto al agregarlo		  
		  if(obj.activar_cantidades == 1){		  
			  if(obj.cantidad <= 0 && $cantidad_estricta == 1){
			  	$("#ajax_items_alert").html('<div class="msg alerta_err">Producto sin existencias</div>');
				return;
			  }
			  $cantidadMostrar = obj.cantidad;	
		  }else {
		  	// cantidad se pone a 1000 para que pueda pasar en caja sin alertas de existencias ya que 'activar_cantidades' esta desactivado
		  	obj.cantidad = 1000;
			$cantidadMostrar = 0;	
		  }
		  //alert(obj.cantidad+"-"+$valor_cantidad);	
		  if(obj.cantidad < parseInt($valor_cantidad) && obj.activar_cantidades == 1 && $cantidad_estricta == 1){
		  	$("#ajax_items_alert").html('<div class="msg alerta_err">La cantidad requerida excede al producto existente</div>');
			return;
		  }
		  /***************************** Si ya esta el prod en la lista se suma *****************************************/
		  if ($('#'+obj.codigo).length){
			  // comprobar la cantidad que ya esta registrada del producto mas la que desea incluir de mas
			  $cantidad = parseInt($('#textbox_'+obj.codigo).val()) + parseInt($valor_cantidad);
			  if(obj.cantidad < $cantidad && obj.activar_cantidades == 1){
				$("#ajax_items_alert").html('<div class="msg alerta_err">La cantidad requerida excede al producto existente</div>');
				return;
			  }	

		  	  $ganancia_precio_venta = $cantidad * ($precio_venta - $precio_venta_proveedor); 			// para el caso de proveedor
		  	  $ganancia_precio_mayoreo = $cantidad * ($precio_venta_mayoreo - $precio_venta_proveedor);	// para el caso de proveedor

			  $precio_item_total = $cantidad * $precio_venta;
			  $precio_item_total_mayoreo = $cantidad * $precio_venta_mayoreo;
			  
			  $('#textbox_'+obj.codigo).attr('value', $cantidad); // Almacena la cantidad a comprar del prod 
			  // Actualizar el precio en la caja de texto del producto para mayoreo y menudeo 		  
			  $("#textbox_precio0_0"+obj.codigo).attr('value', "$"+$precio_item_total.toFixed(2));			// para el caso de menudeo
			  $("#textbox_precio1_1"+obj.codigo).attr('value', "$"+$precio_item_total_mayoreo.toFixed(2));	// para el caso de mayoreo
			  // Actualizar la ganancia del producto
			  $("#textbox_precio0_0"+obj.codigo).attr('ganancia', $ganancia_precio_venta.toFixed(2));			// para el caso de menudeo
			  $("#textbox_precio1_1"+obj.codigo).attr('ganancia', $ganancia_precio_mayoreo.toFixed(2));			// para el caso de mayoreo

			  $("#ajax_items_alert").empty();
			  $('#select_cantidad_caja option[value=1]').attr('selected', true);
			  calcular_totales();
			  return;
		  }
		  
		  
		  $ganancia_precio_venta = $valor_cantidad * ($precio_venta - $precio_venta_proveedor); 			// para el caso de proveedor
		  $ganancia_precio_mayoreo = $valor_cantidad * ($precio_venta_mayoreo - $precio_venta_proveedor);	// para el caso de proveedor

		  $precio_item_total = $valor_cantidad * $precio_venta;						// para el caso de menudeo
		  $precio_item_total_mayoreo = $valor_cantidad * $precio_venta_mayoreo;		// para el caso de mayoreo
		  $caja_cantidad = "<input type='text' style='width:25px' value='"+$valor_cantidad+"' id='textbox_"+obj.codigo+"' class='txt_caja_precio' size='1' readonly>";	
		  		  
		  $caja_precioMayoreo_item = '<input type="text" status="inactivo" ganancia="'+$ganancia_precio_mayoreo.toFixed(2)+'" menudeo="'+$precio_item_total.toFixed(2)+'" proveedor="'+$precio_venta_proveedor+'" mayoreo="'+$precio_item_total_mayoreo.toFixed(2)+'" onclick="del_mayoreo(\''+obj.codigo+'\')" style="display:none;border:1px solid #03F; padding:1px; height:25px;width:60px" value="$'+$precio_item_total_mayoreo.toFixed(2)+'" id="textbox_precio1_1'+obj.codigo+'" codigo="'+obj.codigo+'" id_tbl='+obj.id+' prod_stock='+obj.cantidad+'  class="txt_caja_precio f_amarillo hand" size="8" readonly>';
		  $caja_precio_item   	   = '<input type="text" status="activo" ganancia="'+$ganancia_precio_venta.toFixed(2)+'" menudeo="'+$precio_item_total.toFixed(2)+'" proveedor="'+$precio_venta_proveedor+'" mayoreo="'+$precio_item_total_mayoreo.toFixed(2)+'" value="$'+$precio_item_total.toFixed(2)+'" id="textbox_precio0_0'+obj.codigo+'" codigo="'+obj.codigo+'" style="width:60px" id_tbl='+obj.id+' prod_stock='+obj.cantidad+'  class="txt_caja_precio" size="8" readonly>';		  		  
				   //target="_blank"
		  //$thumb = '<a href="img_productos/'+obj.imagen+'" class="popup-link" title="'+obj.nombre+'" >'+
			if (obj.imagen != 'default_upfile.png')
				$class = 'class="thumbnail-item" onclick="img_mouseenter(this)"';
			else
				$class = 'class="thumbnail-item"';
		  
			$thumb = '<div '+$class+'><a href="#" >'+
		  '<img height="46" width="58" src="img_productos/'+obj.imagen+'"></a>'+
		  '<div class="tooltip"><img src="img_productos/'+obj.imagen+'" alt="" width="100" height="90" /><span class="overlay"></span></div></div>';

 


		  
		  // si el nombre del prod excede de 48 caracteres se ajusta
		  if(obj.nombre.length >= 48)
		  	$item_nombre =  '<div class="item_nombre f_left">'+
				'<div style="top: 5px; position: relative; float:left; width: 10%;">'+
					'<img src="images/borrar.png" width="25" height="25" onclick="del_item(\''+obj.codigo+'\')" class="hand" >'+
				'</div>'+
				'<div style="top: 5px; position: relative; float:left; width: 89%;">	'+  
					'<span id="cantidad0_0'+obj.codigo+'">('+$cantidadMostrar+') \t '+obj.nombre+'</span>'+
				'</div>'+
			'</div>';
		  else
		  	$item_nombre =  '<div class="item_nombre_corto f_left">'+
					'<div style="top: 5px; position: relative; float:left; width: 10%;">'+
						'<img src="images/borrar.png" width="25" height="25" onclick="del_item(\''+obj.codigo+'\')" class="hand" >'+
					'</div>'+
					'<div style="top: 5px; position: relative; float:left; width: 89%;">	'+  
						'<span id="cantidad0_0'+obj.codigo+'">('+$cantidadMostrar+') \t '+obj.nombre+'</span>'+
					'</div>'+
				'</div>';
		  
		  // almacena la fila para el ITEM	

		  $del_cantidad =  '<img src="images/cantidad_del.png" title="Disminuir" width="20" height="20" onclick="del_cantidad(\''+obj.codigo+'\')" class="hand" style="top: 5px; position: relative;">';
		  $add_cantidad =  '<img src="images/cantidad_add.png" title="Aumentar" width="20" height="20" onclick="add_cantidad(\''+obj.codigo+'\', \''+obj.cantidad+'\')" class="hand" style="top: 5px; position: relative;">';

		  $mayoreo =  '<img src="images/mayoreo.png" title="Precio Mayoreo" width="20" height="20" onclick="add_mayoreo(\''+$codigo+'\')" class="hand" style="top: 5px; position: relative;">';

		  $table_precio = "<table border='0' width='100%' height='60'><tr><td width='50%'>"+$item_nombre+"</td><td width='30'>"+$thumb+"</td><td width='120' align='left'>"+$mayoreo+" "+$del_cantidad+" "+$add_cantidad+"</td><td width='20' align='right'>"+$caja_cantidad+"</td><td width='50' align='right'>"+$caja_precio_item+$caja_precioMayoreo_item+"</td></tr></table>"
		  
		  $("#ajax_items_add").append('<div class="item" id="'+obj.codigo+'">'+$table_precio+'</div>'); // Este id comprueba si ya existe este prod en la lista
		  $("#ajax_items_alert").empty();
		  $('#select_cantidad_caja option[value=1]').attr('selected', true);	
		  $('#textbox_'+obj.codigo).attr('value', $valor_cantidad);	
		  $ultimo_codigo_prod = obj.codigo;
		  calcular_totales();  	
		  $('.thumbnail-item').click(); // click para activar la vista imagen *parche		
	  }
	  if(obj.status == "no_existe"){
		  $("#ajax_items_alert").html('<div class="msg alerta_err">Producto no existente</div>');
	  }		 			  
	  if(obj.status == "error_sql"){
		  $("#ajax_items_alert").html('<div class="msg alerta_err">Problemas con el SQL</div>');
	  } 	
   },
   timeout:90000,
   error: function(){ 					
		  $("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
	  }	   
  });	
}
//////////////////////////////////////////////////////////////////////////////////////////////////////// PENDIENTE
function buscar_producto_devo($codigo){
	$fecha = $("#f_devo").val();
	console.log( $fecha );
  $.ajax({
   type: "POST",
   contentType: "application/x-www-form-urlencoded", 
   url: "funciones/buscar_prod_caja.php",
   data: "accion=cj_buscar_producto&codigo="+$codigo,
   beforeSend:function(){/* $("#ajax_respuesta").html($load); */},	 
   success: function(datos){ 
   //alert(datos)   
	  var obj = jQuery.parseJSON(datos);	
	  $("#ajax_respuesta").empty();	
	  //alert(obj);	
	  if(obj.status == "existe"){
  
/* 		  if(obj.cantidad <= 0){
		  	$("#ajax_dvlcion").html('<div class="msg alerta_err">Producto sin existencias</div>');
			$existencias_prod = 1;
			return;
		  } 
		  $existencias_prod = 0; */
		  $("#txt_id_prod").val(obj.id);
		  $("#ajax_dvlcion").html('<div align="center" class="msg alerta_ok t_verde_fuerte">'+obj.nombre+'</div>');
			
	  }
	  if(obj.status == "no_existe"){
		  $("#ajax_dvlcion").html('<div class="msg alerta_err">Producto no existente</div>');
		  $("#txt_id_prod").val("");
	  }		 			  
	  if(obj.status == "error_sql"){
		  $("#ajax_dvlcion").html('<div class="msg alerta_err">Problemas con el SQL</div>');
		  $("#txt_id_prod").val("");
	  } 	
   },
   timeout:90000,
   error: function(){ 					
		  $("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
	  }	   
  });	
}

function calcular_totales(){
	$subTotal = 0; 
	$("input[status^='activo']").each(function() {
		$subTotal = $subTotal + parseFloat($(this).val().replace('$', ''));
	}); 
	$("#subTotal").html("$"+$subTotal.toFixed(2));
	iva = $iva * $subTotal.toFixed(2) / 100;
	total = (iva+$subTotal)
	$("#total").html("$"+total.toFixed(2));
	$('#txt_total').val(total.toFixed(2))
	$($cajaFocus).focus();
	
}
function del_mayoreo(codigo){
	$("#textbox_precio1_1"+codigo).attr("status","inactivo").hide();
	$("#textbox_precio0_0"+codigo).attr("status","activo").fadeIn("fast");
	calcular_totales();
}
function add_mayoreo(codigo){
	$("#textbox_precio1_1"+codigo).attr("status","activo").fadeIn("fast");
	$("#textbox_precio0_0"+codigo).attr("status","inactivo").hide();
	calcular_totales();
}
function calcular_totales_menudeo(){
	$("input[id^='textbox_precio0_0']").each(function() {
		codigo = $(this).attr("codigo");
		$cantidad = parseInt($("#textbox_"+codigo).val());
		menudeo = parseFloat($(this).attr("menudeo")) * $cantidad;
		$("#textbox_precio0_0"+codigo).attr("value", '$'+menudeo.toFixed(2)).attr("status","activo");
		$(this).fadeIn("fast");
		$("#textbox_precio1_1"+codigo).attr("status","inactivo").hide();		
	});
	calcular_totales();
}
function calcular_totales_mayoreo(){
	$("input[id^='textbox_precio1_1']").each(function() {
		codigo = $(this).attr("codigo");
		$cantidad = parseInt($("#textbox_"+codigo).val());
		mayoreo = parseFloat($(this).attr("mayoreo")) * $cantidad;
		$("#textbox_precio1_1"+codigo).attr("value", '$'+mayoreo.toFixed(2)).attr("status","activo");
		$(this).fadeIn("fast");
		$("#textbox_precio0_0"+codigo).attr("status","inactivo").hide();
	});
	calcular_totales();
}

function del_item(id_item){
	$("#"+id_item).remove();
	$ultimo_codigo_prod = "";
	if( $('.item').length <= 0 ){
		$itemRecuperados = false;
	}
	calcular_totales();
}

function add_cantidad(codigo, cantidadProd){
	console.log(codigo)
	console.log(cantidadProd)
	$cantidad = parseInt($("#textbox_"+codigo).val()) + 1;

	if( $cantidad > cantidadProd && $cantidad_estricta == 1 ){
		console.log('cantidad excedida');
		return;
	}
	$precio_venta_proveedor = parseFloat($("#textbox_precio0_0"+codigo).attr('proveedor')); 

	$precio_menudeo = parseFloat($("#textbox_precio0_0"+codigo).attr('menudeo'));  
	$precio_item_total_menudeo = $precio_menudeo * $cantidad;

	$precio_mayoreo = parseFloat($("#textbox_precio0_0"+codigo).attr('mayoreo'));  
	$precio_item_total_mayoreo = $precio_mayoreo * $cantidad;

	$ganancia_precio_venta = $cantidad * ($precio_menudeo - $precio_venta_proveedor); 			// para el caso de proveedor
	$ganancia_precio_mayoreo = $cantidad * ($precio_mayoreo - $precio_venta_proveedor);			// para el caso de proveedor
 
	$("#textbox_"+codigo).attr('value', $cantidad);
	$("#textbox_precio0_0"+codigo).attr('value', "$"+$precio_item_total_menudeo.toFixed(2));	// para el caso de menudeo
	$("#textbox_precio1_1"+codigo).attr('value', "$"+$precio_item_total_mayoreo.toFixed(2));	// para el caso de mayoreo	

	// Actualizar la ganancia del producto
	$("#textbox_precio0_0"+codigo).attr('ganancia', $ganancia_precio_venta.toFixed(2));			// para el caso de menudeo
	$("#textbox_precio1_1"+codigo).attr('ganancia', $ganancia_precio_mayoreo.toFixed(2));		// para el caso de mayoreo

	calcular_totales();
	$('#txt_cj_codigo').blur();
}
function del_cantidad(codigo){
	console.log(codigo)
	$cantidad = parseInt($("#textbox_"+codigo).val()) - 1;
	$precio_venta_proveedor = parseFloat($("#textbox_precio0_0"+codigo).attr('proveedor')); 
	
	if ($cantidad <= 0) { return; }

	$precio_menudeo = parseFloat($("#textbox_precio0_0"+codigo).attr('menudeo'));  
	$precio_item_total_menudeo = $precio_menudeo * $cantidad;

	$precio_mayoreo = parseFloat($("#textbox_precio0_0"+codigo).attr('mayoreo'));  
	$precio_item_total_mayoreo = $precio_mayoreo * $cantidad;

	$ganancia_precio_venta = $cantidad * ($precio_menudeo - $precio_venta_proveedor); 			// para el caso de proveedor
	$ganancia_precio_mayoreo = $cantidad * ($precio_mayoreo - $precio_venta_proveedor);			// para el caso de proveedor

	$("#textbox_"+codigo).attr('value', $cantidad);
	$("#textbox_precio0_0"+codigo).attr('value', "$"+$precio_item_total_menudeo.toFixed(2));	// para el caso de menudeo
	$("#textbox_precio1_1"+codigo).attr('value', "$"+$precio_item_total_mayoreo.toFixed(2));	// para el caso de mayoreo	

	// Actualizar la ganancia del producto
	$("#textbox_precio0_0"+codigo).attr('ganancia', $ganancia_precio_venta.toFixed(2));			// para el caso de menudeo
	$("#textbox_precio1_1"+codigo).attr('ganancia', $ganancia_precio_mayoreo.toFixed(2));		// para el caso de mayoreo

	calcular_totales();
}
function limpiar_datos_cobrar(){
	 $("#popup_contenido, #ajax_respuesta, #cambio_cliente").empty();
	 $array_id = [];
	 $array_cantidad = [];
	 $array_cantidad_solicitada = [];					  
	 $("#recibe, #txt_cj_codig, #txt_aprobacion_card").attr('value','');
	 $("#cont_aprobacion_card").hide();
	 $aprobacion_card = "";
	 $img = "";
	 $card = "";
	 $ultimo_codigo_prod = "";
	 $("#icon_card").attr("src", "");					  
	 
}
function updateCantDevo(id){
	console.log('updateCantDevo ',id);
}
function actualiza_devo_confirm(id_ventas_cajas){
	//console.log('actualiza_devo_confirm ',id_ventas_cajas);
	$(".lst_prod").empty();
	$tbl = '<table width="100%">'+
	'<tr>'+
		'<th>Producto</th>'+
		'<th>Actual</th>'+
		'<th>Solicitada</th>'+
	'</tr>';
	$tr = '';

	arrayDevo = [];
	$precioFinal = 0;
	$gananciaFinal = 0;

	$("#venta_"+id_ventas_cajas+" .td_producto").each(function(index) {
/* 		console.log('cantOrg ',$(this).attr("cantOrg"));
		console.log('prod_name ',$(this).attr("prod_name"));
		console.log('value ',$(this).attr("value"));
		console.log('index ',index); */
		var cant_actual = parseFloat($(this).attr("cantOrg"));
		var cant_nueva = parseFloat($(this).attr("value"));
		var id_producto = $(this).attr("id_producto");
		var precio = parseFloat($(this).attr("precio")); 
		var ganancia = parseFloat($(this).attr("ganancia"));
		//console.log('id_producto ',id_producto); 
		if( ( cant_actual != cant_nueva ) && cant_nueva <= cant_actual && cant_nueva >= 0 ){
			precioPubli = precio / cant_actual;
			gan_cant = ganancia / cant_actual;
			precio_provedor = precioPubli - gan_cant;

			precioNuevo = precioPubli * cant_nueva;
			ganancia_nueva = precioNuevo - (precio_provedor * cant_nueva);
			$precioFinal += precioNuevo;
			$gananciaFinal += ganancia_nueva;
			prodDevo = {
				devolucion: 1,
				id_producto: id_producto,
				cantidad: Number = cant_nueva,
				precio: Number = precioNuevo,
				ganancia: Number = ganancia_nueva
			}; 
			
			$tr += '<tr class="f_verde2_ventas f_resalta_verde">'+
							'<td>'+$(this).attr("prod_name")+'</td>'+
							'<td align="center">'+$(this).attr("cantOrg")+'</td>'+
							'<td align="center">'+$(this).attr("value")+'</td>'+
					'</tr>';
		}else{
			$precioFinal += precio;
			$gananciaFinal += ganancia;
			prodDevo = {
				devolucion: 0,
				id_producto: id_producto,
				cantidad: Number = cant_actual,
				precio: Number = precio,
				ganancia: Number = ganancia
			};
		}
		arrayDevo.push(prodDevo);
	});
	$tbl +=  $tr +'</table>';
	$(".lst_prod").append($tbl); 
	totales = {
		precioFinal: Number = $precioFinal,
		gananciaFinal: Number = $gananciaFinal
	};
	arrayDevo.push(totales);
	//console.log('arrayDevo ',arrayDevo);

	$("#dialog_dvlcion").dialog({
		width: '60%',
		resizable: false,
		show: { effect: "blind", pieces: 8, duration: 10 },
		title: "Devolucion de Producto",
		close: function( event, ui ) {  
			$($cajaFocus).focus();	
		 },
		buttons: {					  
		  Cancelar: function() {
			  $("#txt_codigo_dvlcion, #txt_comentario_dvlcion, #txt_id_prod").val("");
			  $( this ).dialog( "close" );
		  },
		  Guardar: function() {
			  //$id = $("#txt_id_prod").val();
			  $motivo = $("#txt_comentario_dvlcion").val();
/* 			  if($id == "")
				  return;
			  if($existencias_prod == 1)
				  return; */
				
			  $.ajax({
			   type: "POST",
			   contentType: "application/x-www-form-urlencoded", 
			   url: 'crud_pventas.php',
			   data: "accion=prod_devolucion&arrayDevo="+JSON.stringify(arrayDevo)+"&motivo="+$motivo+"&id_ventas_cajas="+id_ventas_cajas,
			   beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
			   success: function(datos){ 
					//console.log(datos)
					$("#ajax_respuesta").empty();
					var obj = jQuery.parseJSON(datos);
					if(obj.status == "venta_editada"){
						$(".ui-dialog-content").dialog().dialog("close");
					}
					if(obj.status == "no_update"){
						 $("#ajax_dvlcion_err").html('<div class="msg alerta_err">Problemas con el SQL</div>');
					}						
											
			   },
			   timeout:90000,
			   error: function(){ 					
					  $("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
				  }	   
			  });	
			  
		  }			  
		}
	});	
}

//****************************************************** ACTUALIZAR DEBEN
function actualiza_deben($id, $estatus){
	$.ajax({
		type: "POST",
		contentType: "application/x-www-form-urlencoded", 
		url: 'crud_pventas.php',
		data: "accion=debenUpdate&id="+$id+"&estatus="+$estatus,
		beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		success: function(datos){ 
			var obj = jQuery.parseJSON(datos);	
			//console.log(obj);
			if(obj.status == "ok"){
				$("#ajax_respuesta").empty();
				$("#debe_"+$id).remove(); 
			}else{
				$("#ajax_deben_lista").html('<div class="msg alerta_err">Problemas con el servidor.</div>')
			}		 							 			
		},
		timeout:90000,
		error: function(){ 					
				$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
			}	   
	});
}

//************************************************************ Guardar pendientes, lista de productos
$('#btn_guardar_list').click(function(){
	var productos = '';
	if ($('.item').length){
		//$hijos = $("#ajax_items_add").children('div').html();
		//console.log( $(this) );
		$(".item").each(function( cont ) {
			productos += $(this).context.outerHTML;				
		});
		f = new Date();	
		fecha = f.getDate() + "-" + (f.getMonth() +1) + "-" + f.getFullYear() + "_" + f.getHours() + "$" + f.getMinutes();
		//productos.push( fecha );
		$.ajax({ 
			url: 'crud_pventas.php', // Url to which the request is send 
			contentType: "application/x-www-form-urlencoded", 
			type: "POST",    // Type of request to be send, called as method 
			data: "accion=fileCaja&tmpCaja=" + productos+"&fecha="+fecha, // Data sent to server, a set of key/value pairs (i.e. form fields and values) 
			processData:false,  // To send DOMDocument or non processed data file it is set to false 

			}).done(function(data) { 
				console.log('btn_guardar_list -> archivo guardado'); 
				$(".item").fadeOut('fast');
				setTimeout(()=>{ $("#ajax_items_add").empty(); },1000);
				
				filePendiente();
			});
	}
});
//*********************************************** Recuperar pendientes, lista de productos mostralos en tabs
function filePendiente(){
	$.ajax({
		type: "POST",
		contentType: "application/x-www-form-urlencoded", 
		url: 'crud_pventas.php',
		data: "accion=filesPendientes",
		beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		success: function(datos){ 
			var obj = jQuery.parseJSON(datos);	
			console.log(obj);
			if(obj.length){
				$('#pendientes_prod').empty();
				obj.forEach(element => {
					$nombreArr = element.split('#');
					$nombre = $nombreArr[0].replace('$',':');
					$idItemBorrar = $nombreArr[1].replace('.','');
					console.log($nombre);
					imgBorrar = $("<img onClick=eliminarItemsPend(\'"+element+"\')>").addClass('borrarListPend').attr('src', 'images/borrar.png');
					imgBorrar = $('<div></div>').addClass('divImgListProd').append(imgBorrar);
					$nombre = $("<div onClick=getPendienteList(\'"+element+"\')></div>").addClass('divNomListProd').text($nombre);
					productos = $('<div></div>').addClass('itemListProd').attr('id', $idItemBorrar).append(imgBorrar).append($nombre);
					$('#pendientes_prod').append(productos); 		
				});
				
			}	 							 			
		},
		timeout:90000,
		error: function(){ 					
				$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
			}	   
	});
}
// ****************************** Recuperar los productos y ponerlos en caja para cobrar o agregar mas items 
function getPendienteList ( fileTxt){
	console.log(fileTxt);
	$itemRecuperados = true;
	$fileItemsPend = fileTxt; 
	$.ajax({
		type: "POST",
		contentType: "application/x-www-form-urlencoded", 
		url: 'crud_pventas.php',
		data: "accion=filePendiente&fileTxt="+fileTxt,
		beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		success: function(datos){ 
			$("#ajax_items_add").empty().html(datos);
			calcular_totales();
		},
		timeout:90000,
		error: function(){ 					
				$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
			}	   
	});
}

function eliminarItemsPend( $fileItemsPend ){
	$nombreArr = $fileItemsPend.split('#');
	$idItemBorrar = $nombreArr[1].replace('.','');
	console.log("fileItemsPend::", $idItemBorrar)

	$("#dialog_del_pendientes").dialog({
		width: 450,
		resizable: false,
		show: { effect: "blind", pieces: 8, duration: 10 },
		title: "Lista Pendiente",
		close: function( event, ui ) {  
			$( this ).dialog( "close" );
		},
		buttons: {	
			Aceptar: function() {
				$.ajax({
					type: "POST",
					contentType: "application/x-www-form-urlencoded", 
					url: 'crud_pventas.php',
					data: "accion=filePendienteDel&fileTxt="+$fileItemsPend,
					beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
					success: function(datos){ 
						console.log(datos);
						var obj = jQuery.parseJSON(datos);	
						//console.log(obj);
						if(obj.status == "ok_del"){
							$("#"+ $idItemBorrar).animate({ opacity: .4 }, 200, "linear", function() { $("#"+ $idItemBorrar).remove(); } );
							$("#dialog_del_pendientes").dialog( "close" );
							if( $itemRecuperados ){
								$("#ajax_items_add").empty();
							}
							$itemRecuperados = false;
						}else{
							console.log("error al elimina el archivo");
						}
					},
					timeout:90000,
					error: function(){ 					
							$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
						}	   
				});
			},				  
			Cancelar: function() {
				//$("#popup_contenido, #ajax_respuesta, #cambio_cliente").empty();
				$( this ).dialog( "close" );
			}
		}
	});	


}