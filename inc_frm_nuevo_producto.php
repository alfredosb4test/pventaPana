<script type="text/javascript">
var array_of_checked_values_open;
	var cont_file = 0;
	button();
$(document).ready(function(){	
	$("#lst_provedor2").selectmenu({
	   multiple: true, 
	   header: "Selecione una Opcion",
	   selectedList: 1
	});  
	$("#lst_unidades2").multiselect({
	   multiple: false, 
	   header: "Selecione una Opcion",
	   selectedList: 1
	});
	$("#lst_seccion2").multiselect({
	   multiple: false, 
	   header: "Selecione una Opcion",
	   selectedList: 1
	});	
	$("#lst_sucursal2").multiselect({
	   multiple: true, 
	   header: "Selecione una Opcion",
	   selectedList: 1,
	   droppable: 'none'
	});		

    $( "#slider-cantidad" ).slider({
      orientation: "horizontal",
      range: "min",
      min: 0,
      max: 2000,
	  step: 1,
      value: $("#select_cantidad").val(),
      slide: function( event, ui ) {
        $("#txt_cantidad").val( ui.value );
		$("#select_cantidad").val(ui.value);
      }
    });
    $( "#txt_cantidad" ).val( $( "#slider-cantidad" ).slider( "value" ) );

 	$( "#select_cantidad" ).change(function() {
      $( "#slider-cantidad" ).slider( "value", this.selectedIndex + 1 );
    });
	
	

    $( "#slider-cantidad_min" ).slider({
      orientation: "horizontal",
      range: "min",
      min: 0,
      max: 100,
	  step: 1,
      value: $("#select_cantidad_min").val(),
      slide: function( event, ui ) {
        $( "#txt_minimo" ).val( ui.value );
		$("#select_cantidad_min").val(ui.value);
      }
    });
    $( "#txt_minimo" ).val( $( "#slider-cantidad_min" ).slider( "value" ) );
 	
	$( "#select_cantidad_min" ).change(function() {
      $( "#slider-cantidad_min" ).slider( "value", this.selectedIndex + 1 );
    });
	/**************************************************************************************************/
	/******************************************  NUEVO ITEM  ******************************************/
	$add_item = function(e){
			if(this.id == 'add_sucursal'){
				//popup_add_sucursal
				$("#dialog_add_sucursal").dialog({
								width: 480,
								resizable: false,
								show: { effect: "blind", pieces: 8, duration: 10 },
								title: "Agregar Sucursal",
								close: function( event, ui ) {  
									  $("#txt_sucursal_nueva").attr("value","");
									  $("#txt_sucursal_nueva").removeClass('text_box_alert').addClass('text_box');	
									  $("#ajax_sucursal_nueva").empty();				  			  
								 },
								buttons: {	
								  Cancelar: function() {
									  $("#txt_sucursal_nueva").attr("value","");
									  $( this ).dialog( "close" );
								  },								  
								  Aceptar: function() {
									 // $( this ).dialog( "close" );
									error=0;
									$suc = $("#txt_sucursal_nueva").val();
									valida_campo2(["txt_sucursal_nueva"],'','','',["txt_sucursal_nueva"], ["#FF5D00"], ["#E6FACB"]);			
									if(error){	
										return;
									}									
									$.ajax({
									 type: "POST",
									 contentType: "application/x-www-form-urlencoded", 
									 url: 'crud_pventas.php',
									 data: "accion=nueva_sucursal&suc="+$suc+'&rd='+Math.random(),
									 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
									 success: function(datos){ 
									 //alert(datos)
										  var obj = jQuery.parseJSON(datos);	
										  if(obj.tipo == "sucursal_registrado"){
											   $("#ajax_respuesta").empty();
											   $("#txt_sucursal_nueva").attr("value","");
											   $("#ajax_sucursal_nueva").html('<div class="msg alerta_ok">Registro correcto:'+$suc+'</div>');
											   $('#lst_sucursal').append('<option value="'+obj.id+'" selected="selected">'+obj.nombre+'</option>');
										  }
										  if(obj.tipo == "duplicado"){
											   $("#ajax_respuesta").empty();
											   //$("#txt_sucursal_nueva").attr("value","");
											   $("#ajax_sucursal_nueva").html('<div class="msg alerta_err">Esta sucursal ya existe</div>');
										  }										  
										  
										  //$("#popup_add_sucursal").append($sql);							
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
			if(this.id == 'add_unidad'){
				$("#dialog_add_unidad").dialog({
								width: 480,
								resizable: false,
								show: { effect: "blind", pieces: 8, duration: 10 },
								title: "Agregar Sucursal",
								close: function( event, ui ) {  
									  $("#txt_unidad_nueva").attr("value","");
									  $("#txt_unidad_nueva").removeClass('text_box_alert').addClass('text_box');
									  $("#ajax_unidad_nueva").empty();				  			  
								 },
								buttons: {	
								  Cancelar: function() {
									  $("#txt_unidad_nueva").attr("value","");
									  $( this ).dialog( "close" );
								  },								  
								  Aceptar: function() {
									 // $( this ).dialog( "close" );
									error=0;
									$unidad = $("#txt_unidad_nueva").val();
									valida_campo2(["txt_unidad_nueva"],'','','',["txt_unidad_nueva"], ["#FF5D00"], ["#E6FACB"]);			
									if(error){	
										return;
									}									
									$.ajax({
									 type: "POST",
									 contentType: "application/x-www-form-urlencoded", 
									 url: 'crud_pventas.php',
									 data: "accion=nueva_unidad&unidad="+$unidad+'&rd='+Math.random(),
									 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
									 success: function(datos){ 
									 //alert(datos)
										  var obj = jQuery.parseJSON(datos);	
										  if(obj.tipo == "unidad_registrado"){
											   $("#ajax_respuesta").empty();
											   $("#txt_unidad_nueva").attr("value","");
											   $("#ajax_unidad_nueva").html('<div class="msg alerta_ok">Registro correcto:'+$unidad+'</div>');
											   $('#lst_unidades').append('<option value="'+obj.id+'" selected="selected">'+obj.nombre+'</option>');											
											   
										  }
										  if(obj.tipo == "duplicado"){
											   $("#ajax_respuesta").empty();
											   //$("#txt_unidad_nueva").attr("value","");
											   $("#ajax_unidad_nueva").html('<div class="msg alerta_err">Esta unidad ya existe</div>');
										  }										  
										  
										  //$("#popup_add_sucursal").append($sql);							
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
			if(this.id == 'add_seccion'){
				$("#dialog_add_seccion").dialog({
								width: 480,
								resizable: false,
								show: { effect: "blind", pieces: 8, duration: 10 },
								title: "Agregar Sucursal",
								close: function( event, ui ) {  
									  $("#txt_seccion_nueva").attr("value","");
									  $("#txt_seccion_nueva").removeClass('text_box_alert').addClass('text_box');
									  $("#ajax_seccion_nueva").empty();				  			  
								 },
								buttons: {	
								  Cancelar: function() {
									  $("#txt_seccion_nueva").attr("value","");
									  $( this ).dialog( "close" );
								  },								  
								  Aceptar: function() {
									 // $( this ).dialog( "close" );
									error=0;
									$seccion = $("#txt_seccion_nueva").val();
									valida_campo2(["txt_seccion_nueva"],'','','',["txt_seccion_nueva"], ["#FF5D00"], ["#E6FACB"]);			
									if(error){	
										return;
									}									
									$.ajax({
									 type: "POST",
									 contentType: "application/x-www-form-urlencoded", 
									 url: 'crud_pventas.php',
									 data: "accion=nueva_seccion&seccion="+$seccion+'&rd='+Math.random(),
									 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
									 success: function(datos){ 
									 //alert(datos)
										  var obj = jQuery.parseJSON(datos);	
										  if(obj.tipo == "seccion_registrado"){
											   $("#ajax_respuesta").empty();
											   $("#txt_seccion_nueva").attr("value","");
											   $("#ajax_seccion_nueva").html('<div class="msg alerta_ok">Registro correcto:'+$seccion+'</div>');
											   $('#lst_seccion').append('<option value="'+obj.id+'" selected="selected">'+obj.nombre+'</option>');
										  }
										  if(obj.tipo == "duplicado"){
											   $("#ajax_respuesta").empty();
											   //$("#txt_seccion_nueva").attr("value","");
											   $("#ajax_seccion_nueva").html('<div class="msg alerta_err">Esta seccion ya existe</div>');
										  }										  
										  
										  //$("#popup_add_sucursal").append($sql);							
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
	}
	$(".btn_add_item").click($add_item);


	/****************************************************************************************************/
	/******************************************  GUARDAR PROD  ******************************************/
	$( "#btn_guarda_producto" ).button({ 
		text: true,
		icons: {
		  primary: "ui-icon-disk"
		}
    });		
	$("#btn_guarda_producto").click(function(){
		error=0;
		err_precio_provedor = 0;
		err_precio_venta = 0;
		err_precio_mayoreo = 0;
		$("#err_precio_provedor, #err_precio_venta, #err_precio_mayoreo").empty();
		
		$('#txt_nombre, #txt_precio_provedor, #txt_precio_venta, #txt_cantidad').jrumble({		
			x: 1,
			y: 1,
			rotation: .2,
			speed: 2,
			opacity: true
		}); // habilita efecto vibrar
		$codigo=$('#codigo').val();
		valida_campo2(["txt_nombre", "txt_precio_provedor", "txt_precio_venta", "txt_cantidad", "txt_precio_mayoreo"],'','','',["txt_nombre", "txt_precio_provedor", "txt_precio_venta", "txt_cantidad", "txt_precio_mayoreo"], ["#FF5D00"], ["#E6FACB"]);			
		if(error){	
			return;
		}
		
		/****************************************** COMPROBAR LAS CANTIDADES ******************************************/
		$txt_precio_provedor = $("#txt_precio_provedor").val();
		if(!($.isNumeric($txt_precio_provedor))){
			$("#err_precio_provedor").html('<div class="msg alerta_err">Cantidad no valida.</div>');
			err_precio_provedor = 1;
		}
		$txt_precio_venta = $("#txt_precio_venta").val();
		if(!($.isNumeric($txt_precio_venta))){
			$("#err_precio_venta").html('<div class="msg alerta_err">Cantidad no valida.</div>');
			err_precio_venta = 1;
		}
		$txt_precio_mayoreo = $("#txt_precio_mayoreo").val();
		if(!($.isNumeric($txt_precio_mayoreo))){
			$("#err_precio_mayoreo").html('<div class="msg alerta_err">Cantidad no valida.</div>');
			err_precio_mayoreo = 1;
		}				
		
		/****************************************** COMPROBAR LAS CANTIDADES ******************************************/
		if(err_precio_mayoreo || err_precio_venta || err_precio_provedor)
			  return;
			  
			  
		$("#btn_guarda_producto").hide();
		var str_post = $("form").serialize();
		$upfile_1 = $("#upfile_1").val();
		if($upfile_1)
			str_post = str_post +"&upfile_1="+$upfile_1;
		else	
			str_post = str_post +"&upfile_1=default_upfile.png";
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
			if(obj.tipo == "producto_registrado"){
				$("input:text, textarea").attr('value','');
				$("#popup_contenido").html('<div class="msg alerta_ok">Datos Guardados</div>');
				if($upfile_1){
						$('#img_upload').attr({"src":"images/clip2.png"}).show();
						$(".resultadosAjax").empty();	
						$('#img_prod').attr({"src":""})
						button();
						$('#contFile_'+cont_file).remove();
						cont_file--;	
				}												
			}
			if(obj.tipo == "prove_error"){
				$("#popup_contenido").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
			}		 			  
			if(obj.tipo == "error_sql"){
				$("#popup_contenido").html('<div class="msg alerta_err">Problemas con el SQL</div>');
			} 
			$("#dialog_detalles").dialog({
				width: 900,
				resizable: false,
				show: { effect: "blind", pieces: 8, duration: 300 },
				title: "Aviso",
				open: function(){$("#ajax_form_producto").empty(); $("#tbl_buscar_producto").hide();},
				close: function( event, ui ) {  
					  $("#popup_contenido").empty();
					  $("#btn_guarda_producto, #tbl_buscar_producto").show();
					  $('#txt_codigo').focus();	 	
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
				$("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
			}	   
		});	
	});
});
	function button(){
        var button = $('#upload_button'), interval;
        new AjaxUpload('#upload_button', {
            action: 'funciones/img_upfile.php',
            data : {'texto': "upfile"},
            onSubmit : function(file , ext){				
                if(cont_file >= 1){
                    alert('Limite para 1 archivo.')
                    return false;
                }			
    
                if ( (ext && /^(exe|php|bat|com|sh)$/.test(ext))){
                    // extensiones no permitidas
                    $(".resultadosAjax_errores").show().html("<br><span class='redondeado sombra Mensaje Error' style='padding-left:50px;'>Archivo Invalido.</span>");
                    return false;
                } else {
                    //button.text('Espere');
					$('#img_upload').attr({"src":"images/loader.gif"});
                    this.disable();
                }
				//alert('OK '+file);
            },
            onComplete: function(file, response){
                extension = response.split('.');
				extension = extension[1];
				//alert(response)
                if(response == 'duplicado'){
                    //alert('Este nombre de archivo ya se encuentra en el servidor.');
					$(".resultadosAjax_errores").show().html("<br><span class='redondeado sombra Mensaje Error' style='padding-left:50px;'>Nombre de archivo duplicado en el servidor.</span>");
					$(".resultadosAjax").html(""); 
                    //button.text('Subir');
					$('#img_upload').attr({"src":"images/clip2.png"}).show();
                    this.enable();				
                    return;	
                }
                if(response.indexOf("MAX_FILE_SIZE")!=-1){
					$(".resultadosAjax_errores").show().html("<span class='redondeado sombra Mensaje Error' style='padding-left:50px;'>Archivo demasiado grande.</span>");
					$(".resultadosAjax").html(""); 
					$('#img_upload').attr({"src":"images/clip2.png"}).show();
                    this.enable();				
                    return;	
                }	
                file = file.replace(/ /gi,'_');
                file = file.replace(/%/gi,'_');
                file = file.toLowerCase();
                //button.text('Subir');
				$('#img_upload').attr({"src":"images/clip2.png"}).hide();
				$('.resultadosAjax_errores').hide();
                this.enable();			
                // Agrega archivo a la lista
                cont_file++
				$('#img_prod').attr({"src":"img_productos/"+response})
                $('.listaUpFile').append('<span class="uploads_files" id="contFile_'+cont_file+'"><img width="20" height="20" src="images/iconos_documentos/'+extension+'.png" border=0 /><span class="mails_upfiles" id="upfileDis_'+cont_file+'">'+response+'</span><input size="40" type="hidden" id="upfile_'+cont_file+'" name="upfile_'+cont_file+'" value="'+response+'" id_file='+cont_file+' /><span onclick="eliminar(this,\''+response+'\' )" id='+cont_file+' file="'+file+'"><span style="cursor: pointer;"><img src="images/tache.png" border=0 /></span></span></span>');
                $(".resultadosAjax").empty();
                //$("#sdi_cont_img").attr("src","images/images_sdi/img_contactos/"+file);
                //alert(response)
            }	
        });		
	}
	function reset_controles(){
		cont_file = 0;
		$('#img_upload').attr({"src":"images/clip2.png"}).show();	
		$(".resultadosAjax").html(""); 
		$(".uploads_files").remove();
	}
	function eliminar(file, nombre){
		
		//var nombre = file.val("file");
		nombre = nombre.replace(/ /gi,'_');
		nombre = nombre.replace(/%/gi,'_');
		nombre = nombre.toLowerCase();
		
		  $.ajax({
				   type: "POST",
				   contentType: "application/x-www-form-urlencoded", 
				   url: 'funciones/img_delfile.php',
				   data:"file="+nombre,
				   beforeSend: function(){ 
				   		//alert(nombre); return;
						$(".resultadosAjax").html("<img src='images/loader.gif'/>"); 
				   },
				   success: function(datos){ 						
						$('#img_upload').attr({"src":"images/clip2.png"}).show();
						$(".resultadosAjax").html(datos); 
						$('#img_prod').attr({"src":"img_productos/default_upfile.png"})
						button();
						cont_file--;
				   },
				   timeout:160000,
				   error: function(){ 
				   			$(".resultadosAjax").html('Problemas en el servidor.'); 
					},
		  }); //listaDatos
		 $('#contFile_'+file.id).remove();		 
		 return; 
	}
</script>
<div id="cont_registro_producto" style="position:relative; margin-top:20px; float:left; width:60%;">
<form>
  <input type="hidden" name="accion" id="accion" value="insert_producto" />
  <table width="100%" border="0">
  
  <tr>
  	<td width="150"><label for="textfield">Codigo del Producto: </label></td>
  	<td><input type="text" name="codigo" id="codigo" class="text_box" value="<?php echo $codigo; ?>" readonly> </td>  
  </tr> 
  <tr>
  	<td><label for="textfield">Sucursal: </label></td>
    <td>
        <span class="lst_sucursal" style="display: ; position: relative; margin:0; float:left;">
            <select id='lst_sucursal' name='lst_sucursal' style="width:280px;" disabled="disabled" >
                <?php echo $conn->lst_sucursales('tbl_sucursal', 'id_sucursal', 'sucursal', $_SESSION['g_id_sucursal'], '',$_SESSION['g_id_empresa']); ?> 
            </select>
        </span>
        &nbsp;<!--<img src="images/agregar.png" class="btn_add_item" id="add_sucursal" />-->
    </td>
  </tr>     
  <tr>
  	<td width="150"><label for="textfield">Nombre: </label></td>
  	<td><input type="text" name="nombre" id="txt_nombre" class="text_box" size="50" maxlength="480" value="" ></td>  
  </tr> 
  
  <tr>
  	<td><label for="textfield">Precio Proveedor: </label></td>
    <td>
        <table border="0">
            <tr>
                <td><input type="text" name="precio_provedor" id="txt_precio_provedor" class="text_box" size="20" maxlength="6" value="" ></td>  
                <td><div id="err_precio_provedor"></div></td>
            </tr>
        </table>  
    </td>         
  </tr>

  <tr>
  	<td><label for="textfield">Precio Venta: </label></td>
    <td>
        <table border="0">
            <tr>
                <td><input type="text" name="precio_venta" id="txt_precio_venta" class="text_box" size="20" maxlength="6" value="" ></td>  
                <td><div id="err_precio_venta"></div></td>
            </tr>
        </table> 
    </td>      
  </tr>
  
  <tr>
  	<td><label for="textfield">Precio Mayoreo: </label></td>
    <td>
        <table border="0">
            <tr>
                <td><input type="text" name="precio_mayoreo" id="txt_precio_mayoreo" class="text_box" size="20" maxlength="6" value="" ></td>  
                <td><div id="err_precio_mayoreo"></div></td>
            </tr>
        </table> 
    </td> 
  </tr> 
  
  <tr>
  	<td><label for="textfield">Cantidad Actual: </label></td>
    <td><input type="hidden" name="cantidad" id="txt_cantidad" class="text_box" size="3" maxlength="6" readonly >
    	<table border="0" width="100%">
        <tr>
        	<td width="50">
                <select name="select_cantidad" id="select_cantidad">
                <?php
                    for($x=0;$x<=2000; $x++){
                        echo "<option>$x</option>";
                    }
                ?> 
                </select>
            </td>
        	<td><div id="slider-cantidad" style="position:relative; height:8px; width:90%; display:none"></div></td>
        </tr>
        </table>    
    </td>
  </tr> 
  
  <tr>
  	<td><label for="textfield">Cantidad Minima: </label></td>
  	<td><input type="hidden" name="minimo" id="txt_minimo" class="text_box" size="20" maxlength="6" value="" >
    	<table border="0" width="100%">
        <tr>
        	<td width="50">
                <select name="select_cantidad_min" id="select_cantidad_min">
                <?php
                    for($x=0;$x<=2000; $x++){
                        echo "<option>$x</option>";
                    }
                ?> 
                </select>            
            </td>
        	<td><div id="slider-cantidad_min" style="position:relative; height:8px; width:90%; display:none"></div></td>
        </tr>
        </table>    
    </td>
  </tr> 

  <tr>
  	<td><label for="textfield">Unidad: </label></td>
    <td>
        <span class="lst_unidades" style="display: ; position: relative; margin:0; float:left;">
            <select id='lst_unidades' name='lst_unidades' style="width:200px; ">
                <?php echo $conn->lst_unidades('tbl_unidades', 'id_unidades', 'unidades', '', '',$_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal']); ?> 
            </select>
        </span>
        &nbsp;<img src="images/agregar.png" class="btn_add_item" id="add_unidad" />
    </td>
  </tr> 

  <tr>
  	<td><label for="textfield">Seccion: </label></td>
    <td>
        <span class="lst_seccion" style="display: ; position: relative; margin:0; float:left;">
            <select id='lst_seccion' name='lst_seccion' style="width:200px; ">
                <?php echo $conn->lst_seccion('tbl_seccion', 'id_seccion', 'seccion', '', '',$_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal']); ?> 
            </select>
        </span>
        &nbsp;<img src="images/agregar.png" class="btn_add_item" id="add_seccion" />
    </td>
  </tr> 
  
  <tr>
  	<td><label for="textfield">Provedor: </label></td>
    <td>
        <span class="lst_provedor" style="display: ; position: relative; margin:0; float:left;">
            <select id='lst_provedor' name='lst_provedor' style="width:200px; " >
                <?php echo $conn->lst_provedor('tbl_proveedor', 'id_proveedor', 'empresa', '', '',$_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal']); ?> 
            </select>
        </span>
    </td>
  </tr>  
  <tr>
  	<td><label for="textfield">Conversion a Unidades: </label></td>
    <td>
        <table border="0">
            <tr>
                <td><input type="text" name="codigo_unidades" id="txt_codigo_unidades" class="text_box" size="20" maxlength="20" value="" ></td>  
                <td><div id="err_codigo_unidades"></div></td>
            </tr>
        </table>  
    </td>         
  </tr>  	
</table>
</form>
</div>
<div id="cont_registro_producto_img" style="position:relative; margin-top:20px; float:left">
	<table width="40%" border="0">
    <tr class="f_negro">
    	<td>Imagen Producto</td>
    </tr> 
    <tr>
    	<td>
        	<div style="width:300px; height:270px; border:#0066CC solid 1px;" align="center">
            	<img id="img_prod" src="img_productos/default_upfile.png" width="270px" height="270px" /> <!--upfile_1-->
            </div>
        </td>
    </tr>
    <tr>  
        <td align="center" height="30">
            <div id="upload_button" style="cursor:pointer;"><img src="images/clip2.png" border="0" id="img_upload" alt="Adjuntar Archivo" style="cursor:pointer;"/></div>
        </td>       
    </tr>
    </table>
    <table border="0" width="100%" height="30">
        <td align="right">
            <div class="listaUpFile" style="font-size:10px;"><div></div></div>
            <div class="resultadosAjax"></div>
            <div class="resultadosAjax_errores"></div>
        </td> 
    </table>    
</div>
	<table border="0" width="100%" style="clear:both">
      <tr>
        <td colspan="2" align="center"><button class="" type="button" id="btn_guarda_producto" style="width:300px;">Guardar Producto</button></td>
      </tr> 
    </table> 
      
<div id="dialog_detalles" style="width:90%; display:none">
    <div id="popup_contenido" style="position: relative; overflow-y: scroll; height:50px;"></div>
</div>

<div id="dialog_add_sucursal" style="width:90%; display:none">
    <div id="popup_add_sucursal" style="position: relative; overflow-y: scroll; height:100px;">
    	Sucursal:<input type="text" class="text_box" id="txt_sucursal_nueva" size="35" />
        <div id="ajax_sucursal_nueva" style="position:relative; width:400px; margin-top:10px;"></div>
    </div>
</div>
<div id="dialog_add_unidad" style="width:90%; display:none">
    <div id="popup_add_sucursal" style="position: relative; overflow-y: scroll; height:100px;">
    	Unidad:<input type="text" class="text_box" id="txt_unidad_nueva" size="35" />
        <div id="ajax_unidad_nueva" style="position:relative; width:400px; margin-top:10px;"></div>
    </div>
</div>
<div id="dialog_add_seccion" style="width:90%; display:none">
    <div id="popup_add_sucursal" style="position: relative; overflow-y: scroll; height:100px;">
    	seccion:<input type="text" class="text_box" id="txt_seccion_nueva" size="35" />
        <div id="ajax_seccion_nueva" style="position:relative; width:400px; margin-top:10px;"></div>
    </div>
</div>
