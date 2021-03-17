<?php 
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();	
?>
<script>
$(document).ready(function(e) {
  $("#cont_bienvenida").show();
	$( "#lst_sucursales_admin" ).selectmenu({
		change: function( event, data ) {
			$id_sucursal = $("#lst_sucursales_admin option:selected").attr('value');
			$sucursal_act = $('#lst_sucursales_admin option:selected').html();
			//alert($id_sucursal);
			$.post( "crud_pventas.php", { id_sucursal: $id_sucursal, sucursal_act:$sucursal_act, accion: "admin_activar_sucursal" })
			  .done(function( data ) {
				//alert(data);
				//$("#tbl_sucursales_admin").hide();
				$("#sucursal_select_admin").html('<div class="msg alerta_ok"><strong>Sucursal activa:'+$sucursal_act+' </strong></div>');
				$("#bar_menu").fadeIn();
				$("#inc_bienvenida").hide();
			  });
		}
	});
});
</script>
    <div id="cont_bienvenida" style="">
			<center><h1>Bienvenido</h1></center>
            <hr />
            <br />
            <center><h2><?=$_SESSION['g_nombre'];?></h2></center>
            
            <br /> 
            
                <table id="tbl_sucursales_admin" border="0" style="position:relative; margin:0 auto;">
                  <tr>
                    <td><label for="textfield">Selecciona una Sucursal: </label></td>
                    <td>
                        <span class="cont_sucursales_admin" style="display: ; position: relative; margin:0; float:left;">
                              <select id='lst_sucursales_admin' name='lst_sucursales_admin' style="width:250px; " onchange="actualiza_sucursal_admin()" >
                                <?php echo $conn->lst_sucursales_admin('tbl_sucursal', 'id_sucursal', 'sucursal', '', '', $_SESSION['g_id_empresa'],$_SESSION['g_sucursales']); ?> 
                             </select>
                        </span>
                    </td>
                  </tr> 
                  	
                </table>
          		<table>	
                    <tr><td><div id="sucursal_select_admin"></div></td></tr>       
                </table>    
                <table id="tbl_sucursales_admin" border="0" style="position:relative; margin:0 auto;">
                  <tr>
                    <td>
                        <div style="position: relative; float:left; ">
                            <img src="images/logarc.jpg" width="291" height="257"/>
                        </div>  
                    </td>
                  </tr>
                </table>            
           
        </div>
         

	<?php 
	$conn->close_mysqli();
?>
            
            </p>
        </div> 
