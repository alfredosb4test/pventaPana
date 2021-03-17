<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
?> 
<script src="js/armar_tickets_big_1.js"></script>
<div class="f_negro titulo_frm" style="position: fixed; width:100%; z-index:20">
  <div style="position:relative; top:7px;">TIKETS</div>  
</div> 

<div id="cont_productos" style="position: relative; width:451px;  float:left; margin:50px 0 0 5px;">
<?php
	$array_prove = $conn->listar_producto($id_producto, "ORDER BY id ASC", $_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal']);	
	if($array_prove == 'no_data'){
		echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
		exit;
	}else{	
		foreach($array_prove['id'] as $key=>$nombre):?>
        	<table border="0">
            <tr>
            	<td>
             <div class="draggable f_azul_degradado2 t_blanco hand" style="position:relative;width:130px; height:16px; border:1px solid #666;z-index:10; margin-top:1px; float:left"" align="center" nombre="<?=$array_prove['nombre'][$key];?>">
			 	<?=$array_prove['codigo'][$key];?>
             </div>
             	</td>
                <td>
            		 <?=$array_prove['nombre'][$key];?>
                </td>
            </tr>
            </table>         
 
		<?php endforeach;		
	}
?>	 
</div> 

<div id="cont_items" style="position: fixed; width:650px; margin-left:500px; margin-top:45px; padding-bottom:4px; background-color:#999"> 
<?php	for($x=0; $x<=55; $x = $x+4): ?>

<div class="droppable f_gris" id="t_<?=$x+1;?>" code="" nombre="" style="position:relative; float:left; width:210px; height:26px; border:1px solid #000; margin:3px 0 0 3px; padding-top:2px;" align="center"></div>
<div class="droppable f_gris" id="t_<?=$x+2;?>" code="" nombre="" style="position:relative; float:left; width:210px; height:26px; border:1px solid #000; margin:3px 0 0 3px; padding-top:2px;" align="center"></div>
<div class="droppable f_gris" id="t_<?=$x+3;?>" code="" nombre="" style="position:relative; float:left; width:210px; height:26px; border:1px solid #000; margin:3px 0 0 3px; padding-top:2px;" align="center"></div> 

<?php	endfor; ?>
</div>
<div id="pdf_code_bar" style="position: fixed; width:45px; margin-left:450px; margin-top:48px;"><img src="images/pdf_chico.png" style="cursor:pointer;" /></div>
<div id="dialog_pedidos_cobrar" style="display:none">
    <table border="0">
    <tr>
		<td>Cantidad:<input type="text"  class="text_box" id="txt_cantidad" size="15" /></td>
        <td><div id="err_cantidad"></div></td>
    </tr>
    </table>    
    <div id="popup_contenido"></div>
</div>

<div id="ajax_pedidos_usr" style="position: relative; width:100%; height: auto; margin-top:100px; clear:both;"></div>


