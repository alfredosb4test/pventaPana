var $drop_activo=0;
$(document).ready(function(e)
{
	altura=$(window).height();
	$("#cont_productos").css("height",altura-105);
	$(".draggable").draggable({
		helper:"clone",
		cursor:"move",
		revert:!0,
		opacity:.9,
		revertDuration:200,
		drag:function(){}
  	});
	$drop_activo=0;
	$(".droppable").droppable({
		greedy:false,
		activeClass:"",
		hoverClass:"f_verde_degradado",
		cursor:"crosshair",
		drop:function(event, ui){
			$drop_activo=1,
			$codigo=$.trim(ui.draggable.html()),
			$cantidad=$.trim(ui.draggable.attr('cantidad')),
			
			$(this).html($codigo),
			$(this).attr("code",$codigo),
			$(this).attr("cantidad",$cantidad),
			$(this).addClass("f_azul_degradado2 t_blanco hand")
			$id = $(this).attr("id").split("_");
			//alert($id[1])
		    $options = '<select class="list_stock" id="select_stock'+$id[1]+'">';
		    $options = $options+'<option value="">#</option>'; 
		    for(x=1; x <= 10; x=x+1){
			    $options = $options+'<option value="'+x+'">'+x+'</option>'; 
		    }
		    //alert($cantidad_restante)
		    $("#cant_"+$id[1]).append($options);
			ui.draggable.parentsUntil($("table.item_pastel" )).remove();	
		}
	})
	$("#btn_solicita_pedido").button({ 
		text: true,
		icons: {
		  primary: "ui-icon-plusthick"
		}
    });	
	
	$("#btn_solicita_pedido").click(function(){
		day=new Date;
		id=day.getTime();
		var $array_cod=[];
		
		if($drop_activo){
			//alert("ok1")
			$(".droppable").each(function(){

				if ($(this).hasClass('f_azul_degradado2')){
					//alert("ok2")
					$id_select = $(this).next().attr("id").split("_");
					$catn_stock_solicitada = $("#select_stock"+$id_select[1]).val();
					//alert($id_select+" | "+$catn_stock_solicitada);
					if($catn_stock_solicitada){
						$id=$(this).attr("id");
						$code=$(this).attr("code");	
						$id_sucursal = $("#lst_sucursales_admin_prod").val();	
						$sucursal = $("#lst_sucursales_admin_prod option:selected").text();	 // $("#lst_sucursales_admin_prod").text(); 
						//alert($id_sucursal+' - '+$sucursal)				
						prod1=new Codigo($code,$catn_stock_solicitada, $id_sucursal, $sucursal, "abierto");
						$array_cod.push(prod1);	
					}
				}
				
				//return false;
				
			})
			
			if($array_cod == "")
				return
			var $codeJSON=JSON.stringify($array_cod);
			$obs =  $("#txt_stock_pasteles").val();
				
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded", 
			 url: 'crud_pventas.php',
			 data: "accion=insert_stock_pasteles&codeJSON="+$codeJSON+"&obs="+$obs+"&obs="+$obs,
			 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
			 success: function(datos){ 					   		
				  var obj = jQuery.parseJSON(datos);
				  //alert(datos)	
				  if(obj.tipo == "insert_ok"){
					$("#ajax_respuesta").empty();
					$("#btn_stock_pasteles").click();					
				  }
				  if(obj.tipo == "insert_error"){
					  $("#popup_contenido").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
				  }		 			  
				  if(obj.tipo == "error_sql"){
					  $("#popup_contenido").html('<div class="msg alerta_err">Problemas con el SQL</div>');
				  } 
				  if(obj.tipo == "error_parametros"){
					  $("#popup_contenido").html('<div class="msg alerta_err">Problemas con el SQL-parametros</div>');
				  } 				  
				  						
			 },
			 timeout:90000,
			 error: function(){ 					
					$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
				}	   
			});
		}
	});
	
	$( "#lst_sucursales_admin_prod" ).selectmenu({
		change: function( event, data ) {
			$id_sucursal = $("#lst_sucursales_admin_prod option:selected").attr('value');
			$sucursal_act = $('#lst_sucursales_admin_prod option:selected').html();
			//alert($id_sucursal);
			if($id_sucursal == "")
				return;
			$.post( "crud_pventas.php", { id_sucursal: $id_sucursal, sucursal_act: $sucursal_act, accion: "admin_activar_sucursal" })
			  .done(function( data ) {
				$('#txt_id_sucursal').val($id_sucursal);
				$('#txt_sucursal').val($sucursal_act);				  
				$("#btn_stock_pasteles").click(); // clic en el menu nuevo producto  -> ir_menu('btn_stock_pasteles.php','btn_stock_pasteles');	
			  });
		}
	});		
});
function Codigo(code, cantidad, id_sucursal, sucursal, status){
	this.code=code;
	this.cantidad=cantidad;
	this.id_sucursal=id_sucursal;
	this.sucursal=sucursal;
	this.status=status;
}