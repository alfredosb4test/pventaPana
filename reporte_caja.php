<?php
session_start();
include('funciones/conexion_class.php');
$conn = new class_mysqli();
$activar_cantidades = $_SESSION['activar_cantidades'];
?>


<script type="text/javascript">
var error=0;
$(document).ready(function(e) {
	$("#f_inicial").datepicker(
		{   
		  dateFormat: 'yy-mm-dd',	
		  showAnim: "drop",
		  showOn: "button",
		  buttonImage: "images/calendario.png",
		  buttonImageOnly: true,
		  changeMonth: true,
		  numberOfMonths: 1,
		  onClose: function( selectedDate ) {
			$( "#f_final" ).datepicker( "option", "minDate", selectedDate );
		  }			
	});		
	$("#f_final").datepicker(
		{   
		  dateFormat: 'yy-mm-dd',	
		  showAnim: "drop",
		  showOn: "button",
		  buttonImage: "images/calendario.png",
		  buttonImageOnly: true,
		  changeMonth: true,
		  numberOfMonths: 1,
		  onClose: function( selectedDate ) {
			$( "#f_inicial" ).datepicker( "option", "maxDate", selectedDate );
		  }
	});	

	//****************************************************** HISTORIAL VENTAS
	$( "#btn_filtro, #btn_ventas_realizadas, #btn_pagos_realizados" ).button({ 
		text: true, 
    });	

    $('#btn_filtro').click(function(){
		$("#ajax_x_producto, #ajax_x_producto_err, #txt_cx_producto").empty();
		$("#txt_codigo_dvlcion, #txt_comentario_dvlcion, #txt_id_prod, #txt_cx_producto").val("");

		$("#dialog_x_producto").dialog({
			width: 550,
			resizable: false,
			show: { effect: "blind", pieces: 8, duration: 10 },
			title: "Ventas por Producto",
			close: function( event, ui ) {  
				   $('#txt_cj_codigo').focus();	
			 },
			buttons: {					  
			  Cancelar: function() {				  
				  $( this ).dialog( "close" );
			  },
			  Aceptar: function() {
				  $id = $("#txt_id_prod").val();  
				  if($id == "")
				  	return;
				  $fecha = $("#f_inicial").val();
				  $fecha2 = $("#f_final").val();
				  //alert($fecha)
				  $.ajax({
				   type: "POST",
				   contentType: "application/x-www-form-urlencoded", 
				   url: 'crud_pventas.php',
				   data: "accion=ventas_usuario_x_prod&fecha="+$fecha+"&fecha2="+$fecha2+"&id_producto="+$id,	// ventas_usuario_x_prod		ventas_usuario 
				   beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
				   success: function(datos){
				   		try{
							$("#ajax_pedidos_usr").html(datos);
							$("#ajax_respuesta").empty();
							$("#dialog_x_producto").dialog( "close" );
						}catch(err) {
							alert("err "+err.message)
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
	$('#btn_ventas_realizadas').click(function(){
			//alert("ok")
		  $fecha = $("#f_inicial").val();
		  $fecha2 = $("#f_final").val();
		  //alert($fecha)
		  $.ajax({
		   type: "POST",
		   contentType: "application/x-www-form-urlencoded", 
		   url: 'crud_pventas.php',
		   data: "accion=ventas_usuario&fecha="+$fecha+"&fecha2="+$fecha2,	// ventas_usuario_x_prod		ventas_usuario
		   beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		   success: function(datos){
				//console.log(datos);
				$("#ajax_pedidos_usr").html(datos);
				$("#ajax_respuesta").empty();	
 							 							 			
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
		  $fecha2 = $("#f_final").val();
		  //alert($fecha)
		  $.ajax({
		   type: "POST",
		   contentType: "application/x-www-form-urlencoded", 
		   url: 'crud_pventas.php',
		   data: "accion=pagos_usuario&fecha="+$fecha+"&fecha2="+$fecha2,
		   beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		   success: function(datos){ 
				$("#ajax_pedidos_usr").html(datos);
				$("#ajax_respuesta").empty();								 							 			
		   },
		   timeout:90000,
		   error: function(){ 					
				  $("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
			  }	   
		  });					  	
	});	
	
	
	$("#lst_usuarios").selectmenu({
		change: function( event, data ) {
			//alert($(this).attr('value')); return;
			$id = $(this).attr('value');
			$rdo_estatus = $(".rdo_estatus:radio[checked='checked']").val();
			$fecha = $("#f_inicial").val();
			$fecha2 = $("#f_final").val();			
			if($rdo_estatus == "ventas")
				$param = "accion=ventas_usuario&id="+$id+"&estatus="+$rdo_estatus+"&fecha="+$fecha+"&fecha2="+$fecha2;
			else
				$param = "accion=pagos_usuario&id="+$id+"&estatus="+$rdo_estatus+"&fecha="+$fecha+"&fecha2="+$fecha2;
			
			if($id == '') return;
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded", 
			 url: 'crud_pventas.php',
			 data: $param,
			 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
			 success: function(datos){ 
				  $("#ajax_pedidos_usr").html(datos);
				  $("#ajax_respuesta").empty();								 							 			
			 },
			 timeout:90000,
			 error: function(){ 					
					$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
				}	   
			});
		}
	});	
	// Check Empleados
	$(".rdo_estatus").change(function(){		
		$rdo_estatus = $(".rdo_estatus:radio[checked='checked']").val();
		//alert("OK"+$rdo_estatus)
		$id = $("#lst_usuarios").attr('value');
		$fecha = $("#f_inicial").val();
		$fecha2 = $("#f_final").val();						
		if($rdo_estatus == "ventas")
			$param = "accion=ventas_usuario&id="+$id+"&estatus="+$rdo_estatus+"&fecha="+$fecha+"&fecha2="+$fecha2;
		else
			$param = "accion=pagos_usuario&id="+$id+"&estatus="+$rdo_estatus+"&fecha="+$fecha+"&fecha2="+$fecha2;
		
		if($id == '') return;
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: 'crud_pventas.php',
		 data: $param,
		 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		 success: function(datos){ 
			  $("#ajax_pedidos_usr").html(datos);
			  $("#ajax_respuesta").empty();								 							 			
		 },
		 timeout:90000,
		 error: function(){ 					
				$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
			}	   
		});
	})	

	// Check Activar Cantidades
	$(".rdo_estatus_catidades").change(function(){		
		$rdo_estatus = $(".rdo_estatus_catidades:radio[checked='checked']").val();
		//alert("OK"+$rdo_estatus)					
		$param = "accion=estatus_catidades&estatus="+$rdo_estatus;
		
		if($rdo_estatus == '') return;
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: 'crud_pventas.php',
		 data: $param,
		 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		 success: function(datos){ 
		 	//alert(datos)
 			var obj = jQuery.parseJSON(datos);	
 			if(obj.status == "ok_update"){
				$.post( "crud_pventas.php", { rdo_estatus: $rdo_estatus, accion: "activar_cantidades" })
				  .done(function( data ) {
					$("#btn_caja_ventas").click(); // clic en el menu caja 
				  }); 				
 				
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
	})	

	
	$("#lst_sucursales").selectmenu({
		change: function( event, data ) {
			//alert($(this).attr('value')); return;
			$id = $(this).attr('value');
			$rdo_estatus = $(".rdo_estatus_suc:radio[checked='checked']").val();
			$fecha = $("#f_inicial").val();
			$fecha2 = $("#f_final").val();						
			if($rdo_estatus == "ventas")
				$param = "accion=ventas_sucursales&id="+$id+"&estatus="+$rdo_estatus+"&fecha="+$fecha+"&fecha2="+$fecha2;
			else
				$param = "accion=pagos_sucursales&id="+$id+"&estatus="+$rdo_estatus+"&fecha="+$fecha+"&fecha2="+$fecha2;
			
			if($id == '') return;
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded", 
			 url: 'crud_pventas.php',
			 data: $param,
			 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
			 success: function(datos){ 
				  $("#ajax_pedidos_usr").html(datos);
				  $("#ajax_respuesta").empty();								 							 			
			 },
			 timeout:90000,
			 error: function(){ 					
					$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
				}	   
			});
		}
	});	
	$(".rdo_estatus_suc").change(function(){
		$rdo_estatus = $(".rdo_estatus_suc:radio[checked='checked']").val();
		$id = $("#lst_sucursales").attr('value');
		$fecha = $("#f_inicial").val();
		$fecha2 = $("#f_final").val();						
		if($rdo_estatus == "ventas")
			$param = "accion=ventas_sucursales&id="+$id+"&estatus="+$rdo_estatus+"&fecha="+$fecha+"&fecha2="+$fecha2;
		else
			$param = "accion=pagos_sucursales&id="+$id+"&estatus="+$rdo_estatus+"&fecha="+$fecha+"&fecha2="+$fecha2;
		
		if($id == '') return;
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: 'crud_pventas.php',
		 data: $param,
		 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
		 success: function(datos){ 
			  $("#ajax_pedidos_usr").html(datos);
			  $("#ajax_respuesta").empty();								 							 			
		 },
		 timeout:90000,
		 error: function(){ 					
				$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
			}	   
		});
	})	
});
function excel(){
		console.log("excel");
 
		$("#tbl_histo").table2excel({

			exclude:".noExl", 
			preserveColors:true,
			name:"Ventas",
			filename:"PuntoVentas",//do not include extension
			fileext:".xls" // file extension

		});

}
function key_buscar_producto (elEvento, e) {	
	//alert("key_buscar_producto");
	var evento = elEvento || window.event;
	var caracter = evento.charCode || evento.keyCode;
	if ( caracter == 13 ) {	
		  $codigo = $(e).val()
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
				  $("#txt_id_prod").val(obj.id);
				  $("#ajax_x_producto").html('<div align="center" class="msg alerta_ok t_verde_fuerte">'+obj.nombre+'</div>');
					
			  }
			  if(obj.status == "no_existe"){
				  $("#ajax_x_producto").html('<div class="msg alerta_err">Producto no existente</div>');
				  $("#txt_id_prod").val("");
			  }		 			  
			  if(obj.status == "error_sql"){
				  $("#ajax_x_producto").html('<div class="msg alerta_err">Problemas con el SQL</div>');
				  $("#txt_id_prod").val("");
			  } 	
		   },
		   timeout:90000,
		   error: function(){ 					
				  $("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
			  }	   
		  });	
	}
}	

</script>
<div class="f_negro titulo_frm">
  <div style="position:relative; top:7px;">CAJA ( Ventas / Pagos )</div>  
</div> 
<br />
<div style="position:relative; height: auto; ">

	<div style="position:relative;float:left; "> 
    	<div class="" style="position: relative; margin: 1px 0 0 0px;  width:80px;" align="center">Fecha</div> 
        <div class="div_redondo_azul" style="position: relative; width:70px; height:40px;float:left;">
            <table border="0" width="100%">
                <tr> 
                    <td width="20px">
                        <input type="text" name="f_inicial" id="f_inicial" size="6" maxlength="140" style="height:24px; margin-left:0; display:none" />
                    </td>    
                    <td width="20px">
                        <input type="text" name="f_final" id="f_final" size="6" maxlength="140" style="height:24px; margin-left:0; display:none" />
                    </td>
                </tr>
            </table>
        </div>
    </div> 
                       
    <div style="position:relative;float:left; "> 
    	<div class="" style="position: relative; margin: 1px 0 0 5px;  width:190px;" align="center">Tipo de Busqueda</div> 
        <div class="div_redondo_azul" style="position: relative; width:290px; height:40px;float:left;">
            <table border="0" width="100%">
                <tr>                         
                    <td width="30%">
                    	<button class="" type="button" id="btn_filtro" style="width:85px; padding:0">Filtro</button> 

                        <button class="" type="button" id="btn_ventas_realizadas" style="width:85px; padding:0">Ventas</button> 
                         
                        <button class="" type="button" id="btn_pagos_realizados" style="width:85px;">Pagos</button>  
                    </td>  
                </tr>
            </table>
        </div>
    </div>
 

    <div style="position:relative;float:left;"> 
    	<div class="" style="position: relative; margin: 1px 0 0 5px;  width:100px; " align="center">Empleado</div> 
        <div class="div_redondo_azul" style="position: relative; width:230px; height:40px;float:left;">
            <div class="lst_provedor" style="display: ; position: relative; margin:-3px 0 0 10px; ">
            <table>
            <tr>
            	<td>
                <select id='lst_usuarios' name='lst_usuarios' style="width:140px; " >
                    <?php echo $conn->lst_usuarios('tbl_usuarios', 'NumEmp', 'Nombre', '', '',$_SESSION['g_id_empresa'], $_SESSION['g_sucursales']); ?> 
                </select>
                </td>
                <td>
                <input type="radio" name="rdo_estatus" class="rdo_estatus" value="ventas" checked="checked" />Ventas
                <input type="radio" name="rdo_estatus" class="rdo_estatus" value="pagos" />Pagos                
            	</td>
            </tr>
            </table>    
            </div>    
        </div>
    </div> 

    <div style="position:relative;float:left;"> 
    	<div class="" style="position: relative; margin: 1px 0 0 15px;  width:100px; " align="center">Sucursales</div> 
        <div class="div_redondo_azul" style="position: relative; width:270px; height:40px;float:left;">
            <div class="lst_provedor" style="display: ; position: relative; margin:-3px 0 0 14px; ">
            <table>
            <tr>
            	<td>
                <select id='lst_sucursales' name='lst_sucursales' style="width:160px;" >
                    <?php echo $conn->lst_sucursales_admin('tbl_sucursal', 'id_sucursal', 'sucursal', '', '', $_SESSION['g_id_empresa'],$_SESSION['g_sucursales']); ?> 
                </select>
                </td>
                <td>
                <input type="radio" name="rdo_estatus_suc" class="rdo_estatus_suc" value="ventas" checked="checked" />Ventas
                <input type="radio" name="rdo_estatus_suc" class="rdo_estatus_suc" value="pagos" />Pagos                
            	</td>
            </tr>
            </table>    
            </div>    
        </div>
    </div> 

	<div style="position:relative;float:left;"> 
	    	<div class="" style="position: relative; margin: 1px 0 0 10px;  width:140px; " align="center">Activar Cantidades</div> 
	    	<div class="div_redondo_azul" style="position: relative; width:140px; height:40px;float:left;">
	            <table>
	            <tr>                
	                <td>
	                <input type="radio" name="rdo_estatus_catidades" class="rdo_estatus_catidades" value="1" 
	                	<?php echo ($activar_cantidades) ? $check = 'checked="checked"' : ''; ?> />Activo
	            	</td>
	            </tr>	                
	            <tr>                
	                <td>	                
	                <input type="radio" name="rdo_estatus_catidades" class="rdo_estatus_catidades" value="0"
	                	<?php echo (!$activar_cantidades) ? $check = 'checked="checked"' : ''; ?> />Desactivado                
	            	</td>
	            </tr>
	            </table>	            	
	    	</div>
	</div>    	          
</div>
<div style="height:30px; position:relative;"></div>
<div id="ajax_pedidos_usr" style="position: relative; width:99%; height: auto; margin:0 auto; clear:both;"></div>


<div id="dialog_x_producto" style="width:650px;display:none">
    <table>
        <tr>
            <td width="90">Codigo:</td><td align=""> <input type="text" id="txt_cx_producto" onkeypress="key_buscar_producto(event,this)" class="text_box" size="50" /></td>
        </tr>       
    </table>     
    <div id="ajax_x_producto" style="height:30px; font-size:14px; "></div>
    <div id="ajax_x_producto_err" style="height:30px; font-size:14px; "></div>
    <input type="hidden" id="txt_id_prod"  />
</div>

