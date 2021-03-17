var error = 0;
var $load = $("<div class='span_load'>&nbsp;</div>").addClass('load');
$(document).ready(function(){
	
	var click_edit = function(event){
		$img = $(this);
		$td = $(this).parent();
		
		if ($(event.target).is('.edit_rol')) {
			$(".cont_rol").show();
			$td.children(".cont_rol").hide();					
			$id = $img.attr('id_rol');
			$(".listBox_rol option[value="+$id+"]").attr('selected',true); 	
			$lista = $(".listBox_rol").show();					
			$rol_anterior = $("#lst_edit_rol option:selected").text(); // almacena la nueva rol para pasarla con ajax	
			$("#txt_rol_actual").val($rol_anterior);
			$td.append($lista);			
		}
		
		if ($(event.target).is('.edit_ambito')) {
			$(".cont_ambito").show();
			$td.children(".cont_ambito").hide();					
			$id = $img.attr('id_ambito_solicitado');
			$(".listBox_ambito option[value="+$id+"]").attr('selected',true); 	
			$lista = $(".listBox_ambito").show();					
			$ambito_anterior = $("#lst_edit_ambito option:selected").text(); // almacena la nueva rol para pasarla con ajax	
			//alert($ambito_anterior)
			$("#txt_ambito_actual").val($ambito_anterior);
			$td.append($lista);			
		}	
		
		if ($(event.target).is('.no_aprobar_rol')) {	
			$("#txt_comentario_no_aprobar_rol").css({'background-color':'#FFF'}).val('');		
			$id_cambios_evaluador = $img.attr('id_cambios_evaluador');
			$id_usuario = $img.attr('id_usuario');
			$id_evaluador = $img.attr('id_contacto');
			$rol_solicitado = $img.attr('rol_solicitado');
			$nombre_rol = $img.attr('nombre_rol');
			
			//alert($id_usuario); return;
				$( "#dialog_rechazar_rol" ).dialog({
					width: 390,
					close: function( event, ui ) {  },
					buttons: {	
					  "Aceptar": function() {
							error = 0;
							valida_campo2(['txt_comentario_no_aprobar_rol'],'','','',['txt_comentario_no_aprobar_rol'], ["#FFCC00"], ["#FFF"]);
							if(error){
								$('#txt_motivo_rechazar_rol_err').html("<div class='msg_err'><span class='alert'>&nbsp;</span>Escriba algun motivo.</div>");
								return;
							}			
							$comentario_no_aprobar_rol = $("#txt_comentario_no_aprobar_rol").val();	  
							$.ajax({
							 type: "POST",
							 contentType: "application/x-www-form-urlencoded", 
							 url: "funciones/operaciones_eidd.php",
							 data: "accion=rechazar_rol&id_cambios_evaluador="+$id_cambios_evaluador+"&comentario_no_aprobar_rol="+$comentario_no_aprobar_rol+"&id_usuario="+$id_usuario+"&id_evaluador="+$id_evaluador+"&nombre_rol="+$nombre_rol+"&rol_solicitado="+$rol_solicitado,
							 beforeSend:function(){ $('#txt_motivo_rechazar_rol_err').html($load); },	   
							 success: function(datos){
								//alert(datos)
								$('#txt_motivo_rechazar_rol_err').empty();
								if(datos == 'rechazar_ok'){
									$("#dialog_rechazar_rol").dialog( "close" );
									$("#status_rol"+$id_cambios_evaluador).html('Rechazado Validador').removeClass('aprobado_revisar').addClass('rechazado_revisar');
									//$tr.remove();
								}
								if(datos == 'rechazar_err'){
									$("#dialog_rechazar_rol").dialog( "close" );
									alert("Problemas con el Servidor");
								}
								else if (datos.indexOf('Error SQL')!=-1) {
									$("#dialog_rechazar_rol").dialog( "close" );
									alert("Problemas con la Base de Datos.");
								}					
							 },
							 timeout:90000,
							 error: function(){ 
									alert('Problemas con el servidor intente de nuevo.');
								}	   
							});
					  },									  
					  Cancel: function() {
						$( this ).dialog( "close" );
						$(".cont_ag").show();
					  }
					}
				});				
		}				
		if ($(event.target).is('.aprobar_rol_ok')) {	
			//alert("#")
			$("#txt_comentario_aprobar_rol").css({'background-color':'#FFF'}).val('');							
			$id_cambios_evaluador = $img.attr('id_cambios_evaluador');
			$id_usuario = $img.attr('id_usuario');
			$id_evaluador = $img.attr('id_contacto');
			$rol_solicitado = $img.attr('rol_solicitado');
			$nombre_rol = $img.attr('nombre_rol');
				$( "#dialog_aprobar_rol" ).dialog({
					width: 390,
					close: function( event, ui ) {  },
					buttons: {	
					  "Aceptar": function() {
							error = 0;
							valida_campo2(['txt_comentario_aprobar_rol'],'','','',['txt_comentario_aprobar_rol'], ["#FFCC00"], ["#FFF"]);
							if(error){
								$('#txt_motivo_aprobar_rol_err').html("<div class='msg_err'><span class='alert'>&nbsp;</span>Escriba algun motivo.</div>");
								return;
							}			
							$comentario_aprobar_rol = $("#txt_comentario_aprobar_rol").val();	  
							$.ajax({
							 type: "POST",
							 contentType: "application/x-www-form-urlencoded", 
							 url: "funciones/operaciones_eidd.php", 
							 data: "accion=aprobado_rol&id_cambios_evaluador="+$id_cambios_evaluador+"&comentario_aprobar_rol="+$comentario_aprobar_rol+"&id_usuario="+$id_usuario+"&id_evaluador="+$id_evaluador+"&nombre_rol="+$nombre_rol+"&rol_solicitado="+$rol_solicitado,
							 beforeSend:function(){ $('#txt_motivo_aprobar_rol_err').html($load); },	   
							 success: function(datos){
								//alert(datos)
								$('#txt_motivo_aprobar_rol_err').empty();
								if(datos == 'aprobado_ok'){
									$("#dialog_aprobar_rol").dialog( "close" );
									$("#status_rol"+$id_cambios_evaluador).html('Aprobado Validador').removeClass('rechazado_revisar').addClass('aprobado_revisar');
									//$tr.remove();
								}
								if(datos == 'aprobado_err'){
									$("#dialog_aprobar_rol").dialog( "close" );
									alert("Problemas con el Servidor");
								}
								else if (datos.indexOf('Error SQL')!=-1) {
									$("#dialog_aprobar_rol").dialog( "close" );
									alert("Problemas con la Base de Datos.");
								}					
							 },
							 timeout:90000,
							 error: function(){ 
									alert('Problemas con el servidor intente de nuevo.');
								}	   
							});
					  },									  
					  Cancel: function() {
						$( this ).dialog( "close" );
						$(".cont_ag").show();
					  }
					}
				});				
		}
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////      AMBITO
		if ($(event.target).is('.no_aprobar_ambito')) {	
			$("#txt_comentario_no_aprobar_ambito").css({'background-color':'#FFF'}).val('');		
			$id_cambios_evaluador = $img.attr('id_cambios_evaluador');
			$id_usuario = $img.attr('id_usuario');
			$id_evaluador = $img.attr('id_contacto');
			$ambito_solicitado = $img.attr('ambito_solicitado');
			$nombre_ambito = $img.attr('nombre_ambito');
	
				$( "#dialog_rechazar_ambito" ).dialog({
					width: 390,
					close: function( event, ui ) {  },
					buttons: {	
					  "Aceptar": function() {
							error = 0;
							valida_campo2(['txt_comentario_no_aprobar_ambito'],'','','',['txt_comentario_no_aprobar_ambito'], ["#FFCC00"], ["#FFF"]);
							if(error){
								$('#txt_motivo_rechazar_ambito_err').html("<div class='msg_err'><span class='alert'>&nbsp;</span>Escriba algun motivo.</div>");
								return;
							}			
							$comentario_no_aprobar_ambito = $("#txt_comentario_no_aprobar_ambito").val();	  
							$.ajax({
							 type: "POST",
							 contentType: "application/x-www-form-urlencoded", 
							 url: "funciones/operaciones_eidd.php",
							 data: "accion=rechazar_ambito&id_cambios_evaluador="+$id_cambios_evaluador+"&comentario_no_aprobar_ambito="+$comentario_no_aprobar_ambito+"&id_usuario="+$id_usuario+"&id_evaluador="+$id_evaluador+"&nombre_ambito="+$nombre_ambito+"&ambito_solicitado="+$ambito_solicitado,
							 beforeSend:function(){ $('#txt_motivo_rechazar_ambito_err').html($load); },	   
							 success: function(datos){
								//alert(datos)
								$('#txt_motivo_rechazar_ambito_err').empty();
								if(datos == 'rechazar_ok'){
									$("#dialog_rechazar_ambito").dialog( "close" );
									$("#status_ambito"+$id_cambios_evaluador).html('Rechazado Validador').removeClass('aprobado_revisar').addClass('rechazado_revisar');
									//$tr.remove();
								}
								if(datos == 'rechazar_err'){
									$("#dialog_rechazar_ambito").dialog( "close" );
									alert("Problemas con el Servidor");
								}
								else if (datos.indexOf('Error SQL')!=-1) {
									$("#dialog_rechazar_ambito").dialog( "close" );
									alert("Problemas con la Base de Datos.");
								}					
							 },
							 timeout:90000,
							 error: function(){ 
									alert('Problemas con el servidor intente de nuevo.');
								}	   
							});
					  },									  
					  Cancel: function() {
						$( this ).dialog( "close" );
						$(".cont_ag").show();
					  }
					}
				});				
		}				
		if ($(event.target).is('.aprobar_ambito_ok')) {	
			//alert("#")	nombre_ambito	ambito_solicitado
			$("#txt_comentario_aprobar_ambito").css({'background-color':'#FFF'}).val('');							
			$id_cambios_evaluador = $img.attr('id_cambios_evaluador');
			$id_usuario = $img.attr('id_usuario');
			$id_evaluador = $img.attr('id_contacto');
			$ambito_solicitado = $img.attr('ambito_solicitado');
			$nombre_ambito = $img.attr('nombre_ambito');
				$( "#dialog_aprobar_ambito" ).dialog({
					width: 390,
					close: function( event, ui ) {  },
					buttons: {	
					  "Aceptar": function() {
							error = 0;
							valida_campo2(['txt_comentario_aprobar_ambito'],'','','',['txt_comentario_aprobar_ambito'], ["#FFCC00"], ["#FFF"]);
							if(error){
								$('#txt_motivo_aprobar_ambito_err').html("<div class='msg_err'><span class='alert'>&nbsp;</span>Escriba algun motivo.</div>");
								return;
							}			
							$comentario_aprobar_ambito = $("#txt_comentario_aprobar_ambito").val();	  
							$.ajax({
							 type: "POST",
							 contentType: "application/x-www-form-urlencoded", 
							 url: "funciones/operaciones_eidd.php",
							 data: "accion=aprobado_ambito&id_cambios_evaluador="+$id_cambios_evaluador+"&comentario_aprobar_ambito="+$comentario_aprobar_ambito+"&id_usuario="+$id_usuario+"&id_evaluador="+$id_evaluador+"&nombre_ambito="+$nombre_ambito+"&ambito_solicitado="+$ambito_solicitado,
							 beforeSend:function(){ $('#txt_motivo_aprobar_ambito_err').html($load); },	   
							 success: function(datos){
								//alert(datos)
								$('#txt_motivo_aprobar_ambito_err').empty();
								if(datos == 'aprobado_ok'){
									$("#dialog_aprobar_ambito").dialog( "close" );
									$("#status_ambito"+$id_cambios_evaluador).html('Aprobado Validador').removeClass('rechazado_revisar').addClass('aprobado_revisar');
									//$tr.remove();
								}
								if(datos == 'aprobado_err'){
									$("#dialog_aprobar_ambito").dialog( "close" );
									alert("Problemas con el Servidor");
								}
								else if (datos.indexOf('Error SQL')!=-1) {
									$("#dialog_aprobar_ambito").dialog( "close" );
									alert("Problemas con la Base de Datos.");
								}					
							 },
							 timeout:90000,
							 error: function(){ 
									alert('Problemas con el servidor intente de nuevo.');
								}	   
							});
					  },									  
					  Cancel: function() {
						$( this ).dialog( "close" );
						$(".cont_ag").show();
					  }
					}
				});				
		}	
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////      AG
		if ($(event.target).is('.no_aprobar_ag')) {	
			$("#txt_comentario_no_aprobar_ag").css({'background-color':'#FFF'}).val('');		
			$id_cambios_evaluador = $img.attr('id_cambios_evaluador');
			$id_usuario = $img.attr('id_usuario');
			$id_evaluador = $img.attr('id_contacto');
			$ag_solicitado = $img.attr('ag_solicitado');
			$ag = $img.attr('ag');
				$( "#dialog_rechazar_ag" ).dialog({
					width: 390,
					close: function( event, ui ) {  },
					buttons: {	
					  "Aceptar": function() {
							error = 0;
							valida_campo2(['txt_comentario_no_aprobar_ag'],'','','',['txt_comentario_no_aprobar_ag'], ["#FFCC00"], ["#FFF"]);
							if(error){
								$('#txt_motivo_rechazar_ag_err').html("<div class='msg_err'><span class='alert'>&nbsp;</span>Escriba algun motivo.</div>");
								return;
							}			
							$comentario_no_aprobar_ag = $("#txt_comentario_no_aprobar_ag").val();	
							//alert($comentario_no_aprobar_ag);  
							$.ajax({
							 type: "POST",
							 contentType: "application/x-www-form-urlencoded", 
							 url: "funciones/operaciones_eidd.php",
							 data: "accion=rechazar_ag&id_cambios_evaluador="+$id_cambios_evaluador+"&comentario_no_aprobar_ag="+$comentario_no_aprobar_ag+"&id_usuario="+$id_usuario+"&id_evaluador="+$id_evaluador+"&ag="+$ag+"&ag_solicitado="+$ag_solicitado,
							 beforeSend:function(){ $('#txt_motivo_rechazar_ag_err').html($load); },	   
							 success: function(datos){
								//alert(datos)
								$('#txt_motivo_rechazar_ag_err').empty();
								if(datos == 'rechazar_ok'){
									$("#dialog_rechazar_ag").dialog( "close" );
									$("#status_ag"+$id_cambios_evaluador).html('Rechazado Validador').removeClass('aprobado_revisar').addClass('rechazado_revisar');
									//$tr.remove();
								}
								if(datos == 'rechazar_err'){
									$("#dialog_rechazar_ag").dialog( "close" );
									alert("Problemas con el Servidor");
								}
								else if (datos.indexOf('Error SQL')!=-1) {
									$("#dialog_rechazar_ag").dialog( "close" );
									alert("Problemas con la Base de Datos.");
								}					
							 },
							 timeout:90000,
							 error: function(){ 
									alert('Problemas con el servidor intente de nuevo.');
								}	   
							});
					  },									  
					  Cancel: function() {
						$( this ).dialog( "close" );
						$(".cont_ag").show();
					  }
					}
				});				
		}				
		if ($(event.target).is('.aprobar_ag_ok')) {	
			//alert("#")
			$("#txt_comentario_aprobar_ag").css({'background-color':'#FFF'}).val('');							
			$id_cambios_evaluador = $img.attr('id_cambios_evaluador');
			$id_usuario = $img.attr('id_usuario');
			$id_evaluador = $img.attr('id_contacto');
			$ag_solicitado = $img.attr('ag_solicitado');
			$ag = $img.attr('ag');
				$( "#dialog_aprobar_ag" ).dialog({
					width: 390,
					close: function( event, ui ) {  },
					buttons: {	
					  "Aceptar": function() {
							error = 0;
							valida_campo2(['txt_comentario_aprobar_ag'],'','','',['txt_comentario_aprobar_ag'], ["#FFCC00"], ["#FFF"]);
							if(error){
								$('#txt_motivo_aprobar_ag_err').html("<div class='msg_err'><span class='alert'>&nbsp;</span>Escriba algun motivo.</div>");
								return;
							}			
							$comentario_aprobar_ag = $("#txt_comentario_aprobar_ag").val();	  
							$.ajax({
							 type: "POST",
							 contentType: "application/x-www-form-urlencoded", 
							 url: "funciones/operaciones_eidd.php",
							 data: "accion=aprobado_ag&id_cambios_evaluador="+$id_cambios_evaluador+"&comentario_aprobar_ag="+$comentario_aprobar_ag+"&id_usuario="+$id_usuario+"&id_evaluador="+$id_evaluador+"&ag="+$ag+"&ag_solicitado="+$ag_solicitado,
							 beforeSend:function(){ $('#txt_motivo_aprobar_ag_err').html($load); },	   
							 success: function(datos){
								//alert(datos)
								$('#txt_motivo_aprobar_ag_err').empty();
								if(datos == 'aprobado_ok'){
									$("#dialog_aprobar_ag").dialog( "close" );
									$("#status_ag"+$id_cambios_evaluador).html('Aprobado Validador').removeClass('rechazado_revisar').addClass('aprobado_revisar');
									//$tr.remove();
								}
								if(datos == 'aprobado_err'){
									$("#dialog_aprobar_ag").dialog( "close" );
									alert("Problemas con el Servidor");
								}
								else if (datos.indexOf('Error SQL')!=-1) {
									$("#dialog_aprobar_ag").dialog( "close" );
									alert("Problemas con la Base de Datos.");
								}					
							 },
							 timeout:90000,
							 error: function(){ 
									alert('Problemas con el servidor intente de nuevo.');
								}	   
							});
					  },									  
					  Cancel: function() {
						$( this ).dialog( "close" );
						$(".cont_ag").show();
					  }
					}
				});				
		}		
		
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////      ALTA USUARIO
		if ($(event.target).is('.no_aprobar_alta')) {		// aprobar_alta_ok	
			$("#txt_comentario_no_aprobar_alta").css({'background-color':'#FFF'}).val('');		
			$id_cambios_evaluador = $img.attr('id_cambios_evaluador');
			$id_usuario = $img.attr('id_usuario');
			$id_evaluador = $img.attr('id_evaluador');
			//alert($id_cambios_evaluador+'|'+$id_usuario+'|'+$id_evaluador)
				$( "#dialog_rechazar_alta" ).dialog({
					width: 390,
					close: function( event, ui ) {  },
					buttons: {	
					  "Aceptar": function() {
							error = 0;
							valida_campo2(['txt_comentario_no_aprobar_alta'],'','','',['txt_comentario_no_aprobar_alta'], ["#FFCC00"], ["#FFF"]);
							if(error){
								$('#txt_motivo_rechazar_alta_err').html("<div class='msg_err'><span class='alert'>&nbsp;</span>Escriba algun motivo.</div>");
								return;
							}			
							$comentario_no_aprobar_alta = $("#txt_comentario_no_aprobar_alta").val();	
							//alert($comentario_no_aprobar_ag);  
							$.ajax({
							 type: "POST",
							 contentType: "application/x-www-form-urlencoded", 
							 url: "funciones/operaciones_eidd.php",
							 data: "accion=rechazar_alta&id_cambios_evaluador="+$id_cambios_evaluador+"&comentario_no_aprobar_alta="+$comentario_no_aprobar_alta+"&id_usuario="+$id_usuario+"&id_evaluador="+$id_evaluador,
							 beforeSend:function(){ $('#txt_motivo_rechazar_alta_err').html($load); },	   
							 success: function(datos){
								//alert(datos)
								$('#txt_motivo_rechazar_alta_err').empty();
								if(datos == 'rechazar_ok'){
									$("#dialog_rechazar_alta").dialog( "close" );
									$("#status_alta"+$id_cambios_evaluador).html('Rechazado Validador').removeClass('aprobado_revisar').addClass('rechazado_revisar');
									//$tr.remove();
								}
								if(datos == 'rechazar_err'){
									$("#dialog_rechazar_alta").dialog( "close" );
									alert("Problemas con el Servidor");
								}
								else if (datos.indexOf('Error SQL')!=-1) {
									$("#dialog_rechazar_alta").dialog( "close" );
									alert("Problemas con la Base de Datos.");
								}					
							 },
							 timeout:90000,
							 error: function(){ 
									alert('Problemas con el servidor intente de nuevo.');
								}	   
							});
					  },									  
					  Cancel: function() {
						$( this ).dialog( "close" );
						$(".cont_ag").show();
					  }
					}
				});				
		}				
		if ($(event.target).is('.aprobar_alta_ok')) {	
			//alert("#")
			$("#txt_comentario_aprobar_alta").css({'background-color':'#FFF'}).val('');							
			$id_cambios_evaluador = $img.attr('id_cambios_evaluador');
			$id_usuario = $img.attr('id_usuario');
			$id_evaluador = $img.attr('id_evaluador');			
			//alert($id_cambios_evaluador+'|'+$id_usuario+'|'+$id_evaluador)
				$( "#dialog_aprobar_alta" ).dialog({
					width: 390,
					close: function( event, ui ) {  },
					buttons: {	
					  "Aceptar": function() {
							error = 0;
							valida_campo2(['txt_comentario_aprobar_alta'],'','','',['txt_comentario_aprobar_alta'], ["#FFCC00"], ["#FFF"]);
							if(error){
								$('#txt_motivo_aprobar_alta_err').html("<div class='msg_err'><span class='alert'>&nbsp;</span>Escriba algun motivo.</div>");
								return;
							}			
							$comentario_aprobar_alta = $("#txt_comentario_aprobar_alta").val();	  
							$.ajax({
							 type: "POST",
							 contentType: "application/x-www-form-urlencoded", 
							 url: "funciones/operaciones_eidd.php",
							 data: "accion=aprobado_alta&id_cambios_evaluador="+$id_cambios_evaluador+"&comentario_aprobar_alta="+$comentario_aprobar_alta+"&id_usuario="+$id_usuario+"&id_evaluador="+$id_evaluador,
							 beforeSend:function(){ $('#txt_motivo_aprobar_alta_err').html($load); },	   
							 success: function(datos){
								//alert(datos)
								$('#txt_motivo_aprobar_alta_err').empty();
								if(datos == 'aprobado_ok'){
									$("#dialog_aprobar_alta").dialog( "close" );
									$("#status_alta"+$id_cambios_evaluador).html('Aprobado Validador').removeClass('rechazado_revisar').addClass('aprobado_revisar');
									//$tr.remove();
								}
								if(datos == 'aprobado_err'){
									$("#dialog_aprobar_alta").dialog( "close" );
									alert("Problemas con el Servidor");
								}
								else if (datos.indexOf('Error SQL')!=-1) {
									$("#dialog_aprobar_alta").dialog( "close" );
									alert("Problemas con la Base de Datos.");
								}					
							 },
							 timeout:90000,
							 error: function(){ 
									alert('Problemas con el servidor intente de nuevo.');
								}	   
							});
					  },									  
					  Cancel: function() {
						$( this ).dialog( "close" );
						//$(".cont_ag").show();
					  }
					}
				});				
		}	

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////      BAJA USUARIO
		if ($(event.target).is('.no_aprobar_baja')) {		// aprobar_baja_ok	
			$("#txt_comentario_no_aprobar_baja").css({'background-color':'#FFF'}).val('');		
			$id_cambios_evaluador = $img.attr('id_cambios_evaluador');
			$id_usuario = $img.attr('id_usuario');
			$id_evaluador = $img.attr('id_evaluador');
			//alert($id_cambios_evaluador+' | '+$id_usuario+' | '+$id_evaluador); return;
				$( "#dialog_rechazar_baja" ).dialog({
					width: 390,
					close: function( event, ui ) {  },
					buttons: {	
					  "Aceptar": function() {
							error = 0;
							valida_campo2(['txt_comentario_no_aprobar_baja'],'','','',['txt_comentario_no_aprobar_baja'], ["#FFCC00"], ["#FFF"]);
							if(error){
								$('#txt_motivo_rechazar_baja_err').html("<div class='msg_err'><span class='alert'>&nbsp;</span>Escriba algun motivo.</div>");
								return;
							}			
							$comentario_no_aprobar_baja = $("#txt_comentario_no_aprobar_baja").val();	
							//alert($comentario_no_aprobar_ag);  
							$.ajax({
							 type: "POST",
							 contentType: "application/x-www-form-urlencoded", 
							 url: "funciones/operaciones_eidd.php",
							 data: "accion=rechazar_baja&id_cambios_evaluador="+$id_cambios_evaluador+"&comentario_no_aprobar_baja="+$comentario_no_aprobar_baja+"&id_usuario="+$id_usuario+"&id_evaluador="+$id_evaluador,
							 beforeSend:function(){ $('#txt_motivo_rechazar_baja_err').html($load); },	   
							 success: function(datos){
								//alert(datos)
								$('#txt_motivo_rechazar_baja_err').empty();
								if(datos == 'rechazar_ok'){
									$("#dialog_rechazar_baja").dialog( "close" );
									$("#status_baja"+$id_cambios_evaluador).html('Rechazado Validador').removeClass('aprobado_revisar').addClass('rechazado_revisar');
									//$tr.remove();
								}
								if(datos == 'rechazar_err'){
									$("#dialog_rechazar_baja").dialog( "close" );
									alert("Problemas con el Servidor");
								}
								else if (datos.indexOf('Error SQL')!=-1) {
									$("#dialog_rechazar_baja").dialog( "close" );
									alert("Problemas con la Base de Datos.");
								}					
							 },
							 timeout:90000,
							 error: function(){ 
									alert('Problemas con el servidor intente de nuevo.');
								}	   
							});
					  },									  
					  Cancel: function() {
						$( this ).dialog( "close" );
						$(".cont_ag").show();
					  }
					}
				});				
		}				
		if ($(event.target).is('.aprobar_baja_ok')) {	
			//alert("#")
			$("#txt_comentario_aprobar_baja").css({'background-color':'#FFF'}).val('');							
			$id_cambios_evaluador = $img.attr('id_cambios_evaluador');
			$id_usuario = $img.attr('id_usuario');
			$id_evaluador = $img.attr('id_evaluador');
				$( "#dialog_aprobar_baja" ).dialog({
					width: 390,
					close: function( event, ui ) {  },
					buttons: {	
					  "Aceptar": function() {
							error = 0;
							valida_campo2(['txt_comentario_aprobar_baja'],'','','',['txt_comentario_aprobar_baja'], ["#FFCC00"], ["#FFF"]);
							if(error){
								$('#txt_motivo_aprobar_baja_err').html("<div class='msg_err'><span class='alert'>&nbsp;</span>Escriba algun motivo.</div>");
								return;
							}			
							$comentario_aprobar_baja = $("#txt_comentario_aprobar_baja").val();	  
							$.ajax({
							 type: "POST",
							 contentType: "application/x-www-form-urlencoded", 
							 url: "funciones/operaciones_eidd.php",
							 data: "accion=aprobado_baja&id_cambios_evaluador="+$id_cambios_evaluador+"&comentario_aprobar_baja="+$comentario_aprobar_baja+"&id_usuario="+$id_usuario+"&id_evaluador="+$id_evaluador,
							 beforeSend:function(){ $('#txt_motivo_aprobar_baja_err').html($load); },	   
							 success: function(datos){
								//alert(datos)
								$('#txt_motivo_aprobar_baja_err').empty();
								if(datos == 'aprobado_ok'){
									$("#dialog_aprobar_baja").dialog( "close" );
									$("#status_baja"+$id_cambios_evaluador).html('Aprobado Validador').removeClass('rechazado_revisar').addClass('aprobado_revisar');
									//$tr.remove();
								}
								if(datos == 'aprobado_err'){
									$("#dialog_aprobar_baja").dialog( "close" );
									alert("Problemas con el Servidor");
								}
								else if (datos.indexOf('Error SQL')!=-1) {
									$("#dialog_aprobar_baja").dialog( "close" );
									alert("Problemas con la Base de Datos.");
								}					
							 },
							 timeout:90000,
							 error: function(){ 
									alert('Problemas con el servidor intente de nuevo.');
								}	   
							});
					  },									  
					  Cancel: function() {
						$( this ).dialog( "close" );
						//$(".cont_ag").show();
					  }
					}
				});				
		}		
		
	}		
	//$(".edit_rol").click(click_edit);
	//$(".edit_ambito").click(click_edit);
	$(".no_aprobar_rol").click(click_edit);
	$(".aprobar_rol_ok").click(click_edit);
	$(".no_aprobar_ambito").click(click_edit);
	$(".aprobar_ambito_ok").click(click_edit);	
	$(".no_aprobar_ag").click(click_edit);
	$(".aprobar_ag_ok").click(click_edit);	
	$(".no_aprobar_alta").click(click_edit);
	$(".aprobar_alta_ok").click(click_edit);
	$(".no_aprobar_baja").click(click_edit);
	$(".aprobar_baja_ok").click(click_edit);
	
	
	var click_msg = function(event){
		//alert("OK")
		$etiqueta = $(this);		
		if ($(event.target).is('.rechazado_revisar_rol') || $(event.target).is('.aprobado_revisar_rol')) {			
			$id_cambios_evaluador = $etiqueta.attr('id_cambios_evaluador');
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded", 
			 url: "funciones/operaciones_eidd.php",
			 data: "accion=ver_msg_rol_status&tipo=rol&id_cambios_evaluador="+$id_cambios_evaluador,
			 beforeSend:function(){ $("#msg_status").html($load); },	   
			 success: function(datos){ 
				$("#msg_status").html(datos);				
			 },
			 timeout:90000,
			 error: function(){ 
					alert('Problemas con el servidor intente de nuevo.');
				}	   
			});					
		}
		if ($(event.target).is('.rechazado_revisar_ambito') || $(event.target).is('.aprobado_revisar_ambito')) {	
			//alert("ambito")		
			$id_cambios_evaluador = $etiqueta.attr('id_cambios_evaluador');
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded", 
			 url: "funciones/operaciones_eidd.php",
			 data: "accion=ver_msg_rol_status&tipo=ambito&id_cambios_evaluador="+$id_cambios_evaluador,
			 beforeSend:function(){ $("#msg_status").html($load); },	   
			 success: function(datos){ 
				$("#msg_status").html(datos);				
			 },
			 timeout:90000,
			 error: function(){ 
					alert('Problemas con el servidor intente de nuevo.');
				}	   
			});					
		}
		if ($(event.target).is('.rechazado_revisar_ag') || $(event.target).is('.aprobado_revisar_ag')) {			
			$id_cambios_evaluador = $etiqueta.attr('id_cambios_evaluador');
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded", 
			 url: "funciones/operaciones_eidd.php",
			 data: "accion=ver_msg_rol_status&tipo=ag&id_cambios_evaluador="+$id_cambios_evaluador,
			 beforeSend:function(){ $("#msg_status").html($load); },	   
			 success: function(datos){ 
				$("#msg_status").html(datos);				
			 },
			 timeout:90000,
			 error: function(){ 
					alert('Problemas con el servidor intente de nuevo.');
				}	   
			});					
		}
		if ($(event.target).is('.rechazado_revisar_alta') || $(event.target).is('.aprobado_revisar_alta')) {			
			$id_cambios_evaluador = $etiqueta.attr('id_cambios_evaluador');
			//alert($id_cambios_evaluador)
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded", 
			 url: "funciones/operaciones_eidd.php",
			 data: "accion=ver_msg_rol_status&tipo=alta&id_cambios_evaluador="+$id_cambios_evaluador,
			 beforeSend:function(){ $("#msg_status").html($load); },	   
			 success: function(datos){ 
				$("#msg_status").html(datos);				
			 },
			 timeout:90000,
			 error: function(){ 
					alert('Problemas con el servidor intente de nuevo.');
				}	   
			});					
		}

		if ($(event.target).is('.rechazado_revisar_baja') || $(event.target).is('.aprobado_revisar_baja')) {			
			$id_cambios_evaluador = $etiqueta.attr('id_cambios_evaluador');
			//alert($id_cambios_evaluador)
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded", 
			 url: "funciones/operaciones_eidd.php",
			 data: "accion=ver_msg_rol_status&tipo=baja&id_cambios_evaluador="+$id_cambios_evaluador,
			 beforeSend:function(){ $("#msg_status").html($load); },	   
			 success: function(datos){ 
				$("#msg_status").html(datos);				
			 },
			 timeout:90000,
			 error: function(){ 
					alert('Problemas con el servidor intente de nuevo.');
				}	   
			});					
		}		
			
		$( "#dialog_msg" ).dialog({
			width: 570,
			close: function( event, ui ) { },
			buttons: {					  
			  Aceptar: function() {
				$( this ).dialog( "close" );
			  }
			}
		});	
			
	}
	$(".rechazado_revisar_rol").click(click_msg);
	$(".aprobado_revisar_rol").click(click_msg);
	$(".rechazado_revisar_ambito").click(click_msg);
	$(".aprobado_revisar_ambito").click(click_msg);
	$(".rechazado_revisar_ag").click(click_msg);
	$(".aprobado_revisar_ag").click(click_msg);		
	$(".rechazado_revisar_alta").click(click_msg);
	$(".aprobado_revisar_alta").click(click_msg);
});