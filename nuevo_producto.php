<script type="text/javascript">

var error=0;
$(document).ready(function(e) {
    $("#txt_codigo").bind('keydown.ctrl_j', function (evt) { 
        return false;
    });		
	$('#txt_codigo').focus();
		
	$( "#btn_nvo_producto" ).button({ 
		text: true,
		icons: {
		  primary: "ui-icon-plusthick"
		}
    });	
	$("#btn_nvo_producto").click(function(){
		error=0;
		$('#txt_codigo').jrumble({		
			x: 1,
			y: 1,
			rotation: .2,
			speed: 2,
			opacity: true
		}); // habilita efecto vibrar
		$codigo=$('#txt_codigo').val();
		valida_campo2(["txt_codigo"],'','','',["txt_codigo"], ["#FF5D00"], ["#E6FACB"]);			
		if(error){	
			return;
		}
		// $("#btn_guarda_prove").hide();
		//var str_post = $("form").serialize();
		//alert(str_post)
		$.ajax({
		 type: "POST",
		 contentType: "application/x-www-form-urlencoded", 
		 url: "crud_pventas.php",
		 data: "accion=comprobar_disponibilidad_producto&codigo="+$codigo,
		 beforeSend:function(){/* $("#ajax_respuesta").html($load); */},	 
		 success: function(datos){ 
		 	//alert(datos)
			$("#ajax_respuesta").empty();	
			$("#ajax_form_producto").html(datos);
			// En caso de entrar para editar algun producto limpiar el valor de esta texbox para que al entrar a este modulo no guarde
			// el id del producto y evitar entrar a modo editar de ese id
			$('#txt_editar_prod_ir').attr("value", "");
		 },
		 timeout:90000,
		 error: function(){ 					
				$("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
			}	   
		});	
	});
	// si se dio clic en el codigo del producto desde el mod Listar Productos
	$modo_editar = $("#txt_editar_prod_ir").val();
	if($modo_editar != ""){
		$('#txt_codigo').attr("value", $modo_editar);
		$("#btn_nvo_producto").click();
	}	
});
	function key_buscar_producto ( elEvento ) {	
		var evento = elEvento || window.event;
		var caracter = evento.charCode || evento.keyCode;
		if ( caracter == 13 ) {	
			$("#btn_nvo_producto").click();
		}
	}
</script>
  <div class="f_negro titulo_frm">
  	<div style="position:relative; top:7px;">Nuevo Producto</div>  
  </div> 
<table align="center" width="100%"  border="0" id="tbl_buscar_producto">
  <tr>
      <td align="right">
      	<input class="text_box" id="txt_codigo" type="text" placeholder="Codigo del Articulo" maxlength="20" onkeypress="key_buscar_producto(event)" style="width:250px; " required>
      </td>
      <td width="200"><button class="" type="button" id="btn_nvo_producto" style="width:280px; ">Confirmar Codigo</button></td>
  </tr>
</table>
<div id="ajax_form_producto"></div>
  
            
             
       
