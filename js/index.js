var $load = $("<div class='span_load'>&nbsp;</div>").addClass('load');
var error=0;
var cont_1 = 1;	// variable para el order by
var $altura = $(window).height();
var $ancho = $(window).width();
var $loadimg = "<img src='images/loader.gif'>";
var $load = $("<div class='span_load'>&nbsp;</div>").addClass('load').css({"margin-top":($altura/2), "margin-left": ($ancho/2-80)}).html("<img src='images/loader.gif'>");
var $ajax_respuesta = $('<div id="ajax_respuesta" style="position: absolute; z-index:1000; float:right; background-color:#000"></div>').css({"margin-top":($altura/2), "margin-left": ($ancho/2-80)});
function mainmenu(){
$(" #nav ul ").css({display: "none"});
$(" #nav li").hover(function(){
	$(this).find('ul:first:hidden').css({visibility: "visible",display: "none"}).slideDown(400);
	},function(){
		$(this).find('ul:first').slideUp(400);
	});
}


$(document).ready(function(e) {
	if($.browser.msie){		 
		 $("#cont_login").empty().html("<div class='msg alerta_err'><strong><h1>Explorador no funcional.</h1></strong></div>");
		 return;
	}
    $(document).bind('keydown.ctrl_j', function (evt) { 
        return false;
    });
	// Mostrar LOADER al hacer ajax
	m_left = ($ancho - 200) /2 + 'px';
	$(document).ajaxStart(function(){
			$.blockUI({ 
				message: $loadimg, fadeIn:  20, fadeOut:  50,
				overlayCSS: { backgroundColor: '#EFEFEF' },
				centerY: 0,  			
				css:{ width: '200px',backgroundColor: '#000', left: m_left,'-webkit-border-radius': '10px','-moz-border-radius': '10px','padding-top': '4px'}
			})
	}
	).ajaxError(function() {
        $.blockUI({ 
			message: 'Error en el Servidor.',
			overlayCSS: { backgroundColor: '#FC2F2F' } 
		}); 
        setTimeout($.unblockUI, 1100);
    }).ajaxStop(function(){ $.unblockUI(); });
	
	$( "#btn_login" ).button({ 
		text: true,
		icons: {
		  primary: "ui-icon-locked"
		}
    });
	
	$('#pwd').focus();
	$("#btn_login").click(function(){
		$pwd = "";
		error=0;
		$('#cont_login').jrumble({		
			x: 5,
			y: 2,
			rotation: 3,
			speed: 1,
			opacity: false
		}); // habilita efecto vibrar
		$pwd=$('#pwd').val();
		valida_campo2(["pwd"],'','','',["pwd"], ["#FFD13A"], ["#E6FACB"]);			
		if(error){
			$('#pwd').focus();
			$('#cont_login').trigger('startRumble');
			demoTimeout = setTimeout(function(){$('#login-box').trigger('stopRumble');}, 300);			
			return;
		}
			
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: "funciones/valida_usr.php",
		 data: "accion=valida_usr&pwd="+$pwd,
		 beforeSend:function(){  },	   
		 success: function(data){
			console.log(data);
			//$("#login-box").html(data);
			//return;
			var obj = jQuery.parseJSON(data);	
			
			$('#ajax_respuesta_login').html("");
			$("#bar_top").show().empty();	
			$("#inc_bienvenida").show(); 

			
			if(obj.tipo == "registrado"){
				//alert(obj.tipo)
				$('#pwd').val('');
				$("#btn_login").hide();
				$("#cont_login").slideUp(250);	
				
				$("#contenido").animate({opacity: .2}, 1000,function(){
					$("#contenido").animate({opacity: 1}, 0);
					//$("#contenido").css({'background-image': 'url(images/sat_logo_4.png)'});
					$("#contenido").css({top:'0px'});					
					$("#bar_top").show().animate({width: "100%"}, 200,function(){
						$("#bar_top").append('<div id="ajax_respuesta" style="position: absolute; z-index:1000; background-color:#FFF; margin-left:'+($ancho-80)+'">load</div>');						
					
						$.post("inc_menu_top.php", function( data ) {
						  $("#bar_top").html( data );
						});

						$.post("inc_bienvenida.php", function( data ) {
						  $("#inc_bienvenida").html( data );
						});	
					});
						
				});
				
				$('#txt_IVA').val(obj.IVA);
				$('#txt_usr_nombre').val(obj.nombre);				
				$('#txt_id_empresa').val(obj.id_empresa);
				$('#txt_id_sucursal').val(obj.id_sucursal);
				$('#txt_sucursal').val(obj.sucursal);

				//$("#bar_top").load("inc_menu_top.php");
				//$("#inc_bienvenida").load("inc_bienvenida.php");


			
			}

			if(obj.tipo == "no_resgistro"){					
				$('#cont_login').trigger('startRumble');
				demoTimeout = setTimeout(function(){$('#login-box').trigger('stopRumble');}, 300);
				$('#ajax_respuesta_login').html("<div class='msg alerta f_rojo_claro t_negro'><strong>Acceso denegado.</strong></div>");
				 
			}

			if(obj.tipo == "errConexion"){	
				$('#cont_login').trigger('startRumble');
				demoTimeout = setTimeout(function(){$('#login-box').trigger('stopRumble');}, 300);	
				$('#ajax_respuesta_login').html("<div class='msg alerta f_rojo_claro t_negro'><strong>Problemas con el servidor.</strong></div>");
			}

			if(obj.tipo == "inactivo"){	
				$('#cont_login').trigger('startRumble');
				demoTimeout = setTimeout(function(){$('#login-box').trigger('stopRumble');}, 300);	
				$('#ajax_respuesta_login').html("<div class='msg alerta f_rojo_claro t_negro'><strong>Cuenta Inactiva</strong></div>");
			}				
		 }   
		});
		return; 
	});	
});

function limpiar_datos() {
	$("div[id*='dialog']").remove();
	$("div[id*='popup']").remove();
	$("#contenido_resul ,#ajax_respuesta").empty();
	//alert("Limpiar")
	//$("#dialog_detalles, #popup_contenido, #dialog_salida_dinero, #dialog_ventas_usr, #dialog_pagos_usr").remove();
	
	//$("txt_editar_prod_ir").attr("value","");
}

function enter_key_estatus ( elEvento ) {	
	var evento = elEvento || window.event;
	var caracter = evento.charCode || evento.keyCode;
	if ( caracter == 13 ) {	
		$("#btn_login").click();
	}
}

function limita(maximoCaracteres, id, elEvento) {
	var elemento = document.getElementById(id);
	var evento = elEvento || window.event;
	var caracter = evento.keyCode;
	if(elemento.value.length >= maximoCaracteres ) {
		if(caracter!=8)
			return false;
	}
	else {
		if(caracter==8 && elemento.value.length > 0)
			$("#num"+id).html(elemento.value.length-1);
		else if(caracter!=8){	
			$("#num"+id).html(elemento.value.length+1);
			return true;
		}
	}	
}
function actualiza_sucursal_admin(){
/*	$id_sucursal = $("#lst_sucursales_admin option:selected").attr('value');
	$sucursal_act = $('#lst_sucursales_admin option:selected').html();
	//alert($id_sucursal);
	$.post( "crud_pventas.php", { id_sucursal: $id_sucursal, accion: "admin_activar_sucursal" })
	  .done(function( data ) {
		//alert(data);
		//$("#tbl_sucursales_admin").hide();
		$("#sucursal_select_admin").html('<div class="msg alerta_ok"><strong>Sucursal activa:'+$sucursal_act+' </strong></div>');
		$("#bar_menu").fadeIn();
		$("#tbl_sucursales_admin").hide();
	  });*/
	  /*
	  $.ajax({
	   type: "POST",
	   contentType: "application/x-www-form-urlencoded", 
	   url: 'crud_pventas.php',
	   data: "accion=admin_activar_sucursal&id_sucursal="+$id_sucursal,
	   beforeSend:function(){ },	 
	   success: function(datos){ 
	   		alert(datos);
			$("#sucursal_select_admin").html('<div class="msg alerta_ok"><strong>Sucursal activa:'+$sucursal_act+' </strong></div>');
	   },
	   timeout:90000,
	   error: function(){ 					
			  $("#sucursal_select_admin").html('Problemas con el servidor intente de nuevo.');
		  }	   
	  });
	  */	  	
}
function cerrar_sistema(){
	//$("#contenido").css({'background-image': 'url(images/sat_logo_3.png)', opacity: .1});
	$("#bar_top,#pie,#contenido_resul").fadeOut(400,function(){
		$("#contenido").css({top:'4.5%'});
		$("#contenido").animate({opacity: 1}, 400,function(){ 
			$("#cont_login").fadeIn(450); 
			$("#btn_login").show(); 
			$('#pwd').focus();
			
		});
		
/*		if ($('cont_login').length){
		 alert("existe");
		}*/
		//return;
		$("#bar_top,#pie").css({width :'5px'}); 
		$(".btn_menu").removeClass('btn_activo');
		$("#cont_bienvenida").hide();
		limpiar_datos();
		
	});
	$.post("funciones/cerrar_sesion.php",function (data){ /*$('#test').html(data) */ });	
}