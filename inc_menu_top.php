<?php
session_start();
?>
<script type="text/javascript">
function mainmenu(){
	$(" #nav ul ").css({display: "none"});
	$(" #nav li").hover(function(){
		$(this).find('ul:first:hidden').css({visibility: "visible",display: "none"}).slideDown(400);
		},function(){
			$(this).find('ul:first').slideUp(400);
		});
}
$(document).ready(function(e) {
	mainmenu();

	$('.boton').mouseenter(function(){
		switch (this.id) {
			  case 'cerrar_sistema':
				//alert(this.id);
				$clone = $('#btn_capa').clone().addClass('enterCapa hand').attr({'alt': 'Cerrar Sesion','title':'Cerrar Sesion'}).show().click(cerrar_sistema);
				$('#cerrar_sistema').append($clone);
				break;		
			  case 'font_size_menos':		  	
				$clone = $('#btn_capa').clone().addClass('enterCapa hand').attr({'alt': 'Cerrar Sesion','title':'Disminuir Letra'}).show().click(font_size_menos);
				$('#font_size_menos').append($clone);
				break;
			  case 'font_size_mas':		  
				$clone = $('#btn_capa').clone().addClass('enterCapa hand').attr({'alt': 'Cerrar Sesion','title':'Aumentar Letra'}).show().click(font_size_mas);
				$('#font_size_mas').append($clone);
				break;						
		}
	});
	$('.boton').mouseleave(function(){
		$('.enterCapa').remove();
	});
	
	
	$btn_click = function(e){
		$("#cont_bienvenida").hide();
		limpiar_datos();
		$('.btn_menu').unbind('click');
		//alert(this.id);
		if(this.id == 'btn_caja'){
			ir_menu('caja.php','caja');
		}
		///////////////////////////////////****************************** PRODUCTOS *********************************//////////////////////////////////////
		if(this.id == 'btn_nuevo_prod'){
			ir_menu('nuevo_producto.php','nuevo_prod');		
		}
		if(this.id == 'btn_insert_inventario'){
			ir_menu('insert_inventario.php','btn_insert_inventario');		
		}		
		
		if(this.id == 'btn_nuevo_prove'){
			ir_menu('nuevo_proveedor.php','btn_nuevo_prove');				
		}			
		if(this.id == 'btn_listar_prove'){
			ir_menu('listar_proveedor.php','btn_listar_prove');			
		}
		if(this.id == 'btn_listar_prod'){
			ir_menu('listar_producto.php','btn_listar_producto');					
		}
		if(this.id == 'btn_prod_unidades'){
			ir_menu('producto_a_unidades.php','btn_prod_unidades');					
		}				
		if(this.id == 'btn_stock_pasteles'){
			ir_menu('stock_pasteles.php','btn_stock_pasteles');					
		}	
		if(this.id == 'btn_listar_stock_pastelero'){
			ir_menu('listar_stock_pastelero.php','btn_listar_stock_pastelero');					
		}					
		
		if(this.id == 'btn_listar_prod_pastelero'){
			ir_menu('listar_producto_pastelero.php','btn_listar_producto');					
		}		
		if(this.id == 'btn_armar_ticket'){
			ir_menu('armar_tickets_big_1.php','btn_armar_ticket');					
		}
		
		///////////////////////////////////****************************** PEDIDOS *********************************//////////////////////////////////////
		if(this.id == 'btn_pedidos'){
			ir_menu('nuevo_pedido.php','btn_pedidos');					
		}		
		if(this.id == 'btn_listar_pedido'){
			ir_menu('listar_pedido.php','btn_listar_pedido');					
		}				
		///////////////////////////////////****************************** CLIENTES *********************************//////////////////////////////////////
		if(this.id == 'btn_nuevo_cliente'){
			ir_menu('nuevo_cliente.php','btn_nuevo_cliente');				
		}	
		if(this.id == 'btn_listar_cliente'){
			ir_menu('listar_cliente.php','btn_listar_cliente');					
		}
		///////////////////////////////////****************************** HERRAMIENTAS *****************************//////////////////////////////////////
		if(this.id == 'btn_adm_usuarios'){
			ir_menu('adm_listar_usuarios.php','btn_adm_usuarios');				
		}	
		if(this.id == 'btn_adm_backup'){
			ir_menu('adm_backup.php','btn_adm_backup');				
		}				
		///////////////////////////////////****************************** REPORTES *********************************//////////////////////////////////////
		if(this.id == 'btn_caja_ventas'){
			ir_menu('reporte_caja.php','btn_caja_ventas');
		}
		if(this.id == 'btn_devoluciones'){
			ir_menu('listar_devoluciones.php','btn_devoluciones');
		}
		if(this.id == 'btn_trasferencias'){
			ir_menu('listar_trasferencias.php','btn_trasferencias');
		}	
		if(this.id == 'btn_accesoRapido'){
			ir_menu('accesoRapido.php','btn_accesoRapido');
		}				
		
	}
	$('.btn_menu').click($btn_click);
	
	function ir_menu($php,$accion){
		//console.log($accion)
			$.ajax({
			 type: "POST",
			 contentType: "application/x-www-form-urlencoded",
			 url: $php,
			 data: "accion="+$accion,
			 beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
			 success: function(datos){ 
				$(".btn_menu").removeClass('btn_activo');
				$("#btn_unidad").addClass('btn_activo');
				$('#contenido_resul').animate({'height':'97%'},130, function(){
					$("#contenido_resul").show().empty().html(datos);
					$("#ajax_respuesta").empty();
					$('.btn_menu').bind('click',$btn_click);
				});
			 },
			 timeout:90000,
			 error: function(){ 					
					$("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
				}	   
			});			
	}

});

function font_size_mas() {
	var $speech = $('#contenido');
	var num = parseFloat( $speech.css('fontSize'), 10 );
	
	var $cssmenu = $('#cssmenu a');		
	var num_cssmenu = parseFloat( $cssmenu.css('fontSize'), 10 );
	if($('.item_nombre_corto').length){
		var $caja_corto = $('.item_nombre_corto');		
		var num_caja_corto = parseFloat( $caja_corto.css('fontSize'), 10 );
		num_caja_corto *= 1.2;  
	}
	if($('.item_nombre').length){
		var $caja_largo = $('.item_nombre');		
		var num_caja_largo = parseFloat( $caja_largo.css('fontSize'), 10 );
		num_caja_largo *= 1.1; 
	}
	
	num *= 1.2;
	num_cssmenu *= 1.2;
	   
	if(num <= 18){   
		$speech.animate({fontSize: num + 'px'}, 'slow');
		$cssmenu.animate({fontSize: num_cssmenu + 'px'}, 'slow');
		if($('.item_nombre_corto').length)
			$caja_corto.animate({fontSize: num_caja_corto + 'px'}, 'slow');
		if($('.item_nombre').length)
			$caja_largo.animate({fontSize: num_caja_largo + 'px'}, 'slow');
		
	}
}
function font_size_menos() {
	var $speech = $('#contenido'); 	
	var num = parseFloat( $speech.css('fontSize'), 10 );

	var $cssmenu = $('#cssmenu a');		
	var num_cssmenu = parseFloat( $cssmenu.css('fontSize'), 10 );
		
	if($('.item_nombre_corto').length){
		var $caja_corto = $('.item_nombre_corto'); 
		var num_caja_corto = parseFloat( $caja_corto.css('fontSize'), 10 );	
		num_caja_corto /= 1.2;
	}
	if($('.item_nombre').length){
		var $caja_largo = $('.item_nombre'); 
		var num_caja_largo = parseFloat( $caja_largo.css('fontSize'), 10 );	
		num_caja_largo /= 1.1;
	}
	
	num /= 1.2;
	num_cssmenu /= 1.2;
	
	if(num >= 12){
		$speech.animate({fontSize: num + 'px'}, 'slow');
		$cssmenu.animate({fontSize: num_cssmenu + 'px'}, 'slow');
		if($('.item_nombre_corto').length)
			$caja_corto.animate({fontSize: num_caja_corto + 'px'}, 'slow');
		if($('.item_nombre').length)	
			$caja_largo.animate({fontSize: num_caja_largo + 'px'}, 'slow');
	}
}
</script>
<?php
//echo $_SESSION['g_nivel'];
if($_SESSION['g_nivel']== "admin"): ?>
    <div id="bar_menu" style="position:relative; width:85%; height:28px; float:left; top:1px; display:none;">
        <div id='cssmenu'>
            <ul>
               <li class='has-sub btn_menu' id="btn_caja"><a href='#'><span>Caja</span></a></li>    
               <li class='has-sub'><a href='#'><span>Productos y Proveedores</span></a>
                  <ul>
                     <li class='has-sub btn_menu' id="btn_nuevo_prod"><a href='#'><span>Nuevo Producto</span></a></li>
                     <li class='has-sub btn_menu' id="btn_insert_inventario"><a href='#'><span>Agregar Productos Inventario</span></a></li> 
                     <li class='has-sub btn_menu' id="btn_listar_prod"><a href='#'><span>Listado de Productos</span></a></li>
                     <li class='has-sub btn_menu' id="btn_prod_unidades"><a href='#'><span>Producto a Unidades</span></a></li>
                     <li class='has-sub'><a href='#'><span>Stock Pasteles ></span></a>
                         <ul>
                            <li class='has-sub btn_menu' id="btn_stock_pasteles"><a href='#'><span>Solicitar Stock Pasteles</span></a></li>
                            <li class='has-sub btn_menu' id="btn_listar_stock_pastelero"><a href='#'><span>Pasteles Pendientes de Realizar</span></a></li>
                         </ul>
                     </li>
                     <li class='has-sub btn_menu' id="btn_nuevo_prove"><a href='#'><span>Nuevo Proveedores</span></a></li>
                     <li class='has-sub btn_menu' id="btn_listar_prove"><a href='#'><span>Proveedores</span></a></li>
                     <li class='has-sub btn_menu' id="btn_armar_ticket"><a href='#'><span>Armar Ticket</span></a></li>
                  </ul>
               </li>
               
               <li class='has-sub'><a href='#'><span>Pedidos</span></a>
                  <ul>
                     <li class='has-sub btn_menu' id="btn_pedidos"><a href='#'><span>Nuevo Pedido</span></a></li>
                     <li class='has-sub btn_menu' id="btn_listar_pedido"><a href='#'><span>Listado de Pedidos</span></a></li>
                  </ul>
               </li>
      <!--                   <li class='has-sub'><a href='#'><span>Usuarios</span></a>
                  <ul>
                     <li class='has-sub'><a href='#'><span>Nuevo Usuarios</span></a></li>
                     <li class='has-sub'><a href='#'><span>Listado de Usuarios</span></a></li>
                  </ul>
               </li>-->
               <li class='has-sub'><a href='#'><span>Clientes</span></a>
                  <ul>
                     <li class='has-sub btn_menu' id="btn_nuevo_cliente"><a href='#'><span>Nuevo Clientes</span></a></li>
                     <li class='has-sub btn_menu' id="btn_listar_cliente"><a href='#'><span>Listado de Clientes</span></a></li>
                  </ul>
               </li>    
               <li class='has-sub'><a href='#'><span>Herramientas</span></a>
                  <ul>
				  <li class='has-sub btn_menu' id="btn_adm_usuarios"><a href='#'><span>Usuarios</span></a></li>
				  <li class='has-sub btn_menu' id="btn_adm_backup"><a href='#'><span>Respaldos</span></a></li>					 
                  </ul>
               </li>   
               <li class='has-sub'><a href='#'><span>Ventas</span></a>
                  <ul>
                     <li class='has-sub btn_menu' id="btn_caja_ventas"><a href='#'><span>Caja</span></a></li>
                     <li class='has-sub btn_menu' id="btn_accesoRapido"><a href='#'><span>Acceso Rapido Caja</span></a></li>					 
                     <li class='has-sub btn_menu' id="btn_devoluciones"><a href='#'><span>Devoluciones</span></a></li>
                     <li class='has-sub btn_menu' id="btn_trasferencias"><a href='#'><span>Trasferencias</span></a></li>
                  </ul>
               </li>                                
            </ul>
        </div>
  
    </div>	
    <div id="" style="position: relative; float: right; margin:18px 16px 0 0px;width:10%; background-color:; ">
        <div id="cont_btn" style="position:relative; float:right; width:86px;">
            <div style="width:28px; height:28px;float:left;" class="boton" id="font_size_mas">
                <img src="images/btn_font_mas.png" border="0" style="position: absolute;">
            </div> 
            <div style="width:28px; height:28px;float:left;" class="boton" id="font_size_menos">
                <img src="images/btn_font_menos.png" border="0" style="position: absolute;">
            </div>                     	 	
            <div style="width:28px; height:28px;float:left;" class="boton" id="cerrar_sistema">
                <img src="images/cerrar_1.png" border="0" style="position: absolute;" >
            </div>
        </div>
        <div   style="position:absolute;  color: #FF0; margin:-17px 0 0 -170px; font-size:12px; width:300px;">
        	<table border="0" width="100%">
        		<tr>
        			<td align="right">
        				Usuario: <span class="t_verde2 t_italic"><?= $_SESSION['g_nombre']; ?></span>
        			</td>
        		</tr>		
        	</table>
        </div>
    </div>
<?php 
endif;
if($_SESSION['g_nivel']== "vendedor"):?>
		<div id="bar_menu" style="position:relative; width:85%; height:28px; float:left; top:1px; display:none;">
                <div id='cssmenu'>
                <ul>
                   <li class='has-sub btn_menu' id="btn_caja"><a href='#'><span>Caja</span></a></li>    
                   <li class='has-sub'><a href='#'><span>Productos y Proveedores</span></a>
                      <ul>
                         <li class='has-sub btn_menu' id="btn_listar_prod"><a href='#'><span>Listado de Productos</span></a></li>
                         <li class='has-sub btn_menu' id="btn_prod_unidades"><a href='#'><span>Producto a Unidades</span></a></li>
                         <li class='has-sub btn_menu' id="btn_listar_stock_pastelero"><a href='#'><span>Pasteles Pendientes de Realizar</span></a></li> 
                      </ul>
                   </li>
                   <li class='has-sub'><a href='#'><span>Pedidos</span></a>
                      <ul>
                         <li class='has-sub btn_menu' id="btn_pedidos"><a href='#'><span>Nuevo Pedido</span></a></li>
                         <li class='has-sub btn_menu' id="btn_listar_pedido"><a href='#'><span>Listado de Pedidos</span></a></li>
                      </ul>
                   </li>  
                   
<!--                   <li class='has-sub'><a href='#'><span>Usuarios</span></a>
                      <ul>
                         <li class='has-sub'><a href='#'><span>Nuevo Usuarios</span></a></li>
                         <li class='has-sub'><a href='#'><span>Listado de Usuarios</span></a></li>
                      </ul>
                   </li>-->
                   <li class='has-sub'><a href='#'><span>Clientes</span></a>
                      <ul>
                         <li class='has-sub btn_menu' id="btn_nuevo_cliente"><a href='#'><span>Nuevo Clientes</span></a></li>
                         <li class='has-sub btn_menu' id="btn_listar_cliente"><a href='#'><span>Listado de Clientes</span></a></li>
                      </ul>
                   </li> 
                   <li class='has-sub btn_menu' id="btn_devoluciones"><a href='#'><span>Devoluciones</span></a></li>                                   
                </ul>
                
                </div>




         </div>	
    	 <div id="" style="position: relative; float: right; margin:18px 16px 0 0px;width:10%; background-color:; ">
         	<div id="cont_btn" style="position:relative; float:right; width:86px;">
                <div style="width:28px; height:28px;float:left;" class="boton" id="font_size_mas">
                    <img src="images/btn_font_mas.png" border="0" style="position: absolute;">
                </div> 
                <div style="width:28px; height:28px;float:left;" class="boton" id="font_size_menos">
                    <img src="images/btn_font_menos.png" border="0" style="position: absolute;">
                </div>                     	 	
                <div style="width:28px; height:28px;float:left;" class="boton" id="cerrar_sistema">
                    <img src="images/cerrar_1.png" border="0" style="position: absolute;" >
                </div>
            </div>
	        <div   style="position:absolute;  color: #FF0; margin:-17px 0 0 -170px; font-size:12px; width:300px;">
	        	<table border="0" width="100%">
	        		<tr>
	        			<td align="right">
	        				Usuario: <span class="t_verde2 t_italic"><?= $_SESSION['g_nombre']; ?></span>
	        			</td>
	        		</tr>		
	        	</table>
	        </div>       
         </div>
<?php 
endif;

if($_SESSION['g_nivel']== "pastelero"):?>

    	<div id="bar_menu" style="position:relative; width:85%; height:28px; float:left; top:1px; display:none;">
         
                <div id='cssmenu'>
                <ul>
                   <li class='has-sub btn_menu' id="btn_listar_pedido"><a href='#'><span>Listado de Pedidos</span></a></li>
                   <li class='has-sub btn_menu' id="btn_listar_prod_pastelero"><a href='#'><span>Listado de Productos</span></a></li>
                   <li class='has-sub btn_menu' id="btn_insert_inventario"><a href='#'><span>Agregar Productos Inventario</span></a></li> 
                   <li class='has-sub btn_menu' id="btn_listar_stock_pastelero"><a href='#'><span>Pasteles Pendientes de Realizar</span></a></li> 
                </ul>
                </div>


         </div>	
    	 <div id="" style="position: relative; float: right; margin:10px 16px 0 0px;width:10%; background-color:; ">
         	<div id="cont_btn" style="position:relative; float:right; width:86px;">
                <div style="width:28px; height:28px;float:left;" class="boton" id="font_size_mas">
                    <img src="images/btn_font_mas.png" border="0" style="position: absolute;">
                </div> 
                <div style="width:28px; height:28px;float:left;" class="boton" id="font_size_menos">
                    <img src="images/btn_font_menos.png" border="0" style="position: absolute;">
                </div>                     	 	
                <div style="width:28px; height:28px;float:left;" class="boton" id="cerrar_sistema">
                    <img src="images/cerrar_1.png" border="0" style="position: absolute;" >
                </div>
            </div>
         </div>
<?php 
endif;
?>

