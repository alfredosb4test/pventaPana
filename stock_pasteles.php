<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
?> 
<script src="js/armar_stock_pasteles.js"></script>
<div class="f_negro titulo_frm" style="position: fixed; width:100%; z-index:20">
  <div style="position:relative; top:7px;">Solicitar stock pasteles</div>  
</div> 

      <table id="tbl_sucursales_admin" border="0" style="position:relative; margin:39px 0 0 0;">
        <tr>
          <td><label for="textfield">Selecciona una Sucursal: </label></td>
          <td> 
              <span class="cont_sucursales_admin" style="display: ; position: relative; margin:0; float:left;">
                    <select id='lst_sucursales_admin_prod' name='lst_sucursales_admin_prod' style="width:250px; "  >
                      <?php echo $conn->lst_sucursales_admin('tbl_sucursal', 'id_sucursal', 'sucursal', $_SESSION['g_id_sucursal'], '', $_SESSION['g_id_empresa'],$_SESSION['g_sucursales']); ?> 
                   </select>
              </span>
          </td>
        </tr> 
      </table>
<div id="" style="position:relative; display:table;">  
    <div id="cont_productos" style="position: relative; width:510px;  float:left; margin:6px 0 0 5px;">
    <?php
        $array_prove = $conn->listar_producto_pasteles($id_producto, "ORDER BY cantidad ASC", $_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal']);	
        if($array_prove == 'no_data'){
            echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
            exit;
        }else{	
            foreach($array_prove['id'] as $key=>$nombre):?>
                <table border="0" class="item_pastel">
                <tr>
                    <td>
                 <div class="draggable f_azul_degradado2 t_blanco hand" style="position:relative;width:130px; height:16px; border:1px solid #666;z-index:10; margin-top:1px; float:left"" align="center" cantidad="<?=$array_prove['cantidad'][$key];?>">
                    <?=$array_prove['codigo'][$key];?>
                 </div>
                    </td>
                    <td>
                         <?="(".$array_prove['cantidad'][$key].") ".$array_prove['nombre'][$key];?>
                    </td>
                </tr>
                </table>         
     
            <?php endforeach;		
        }
    ?>	 
    </div> 
    
    <div id="cont_items" style="position: fixed; width:196px; margin-left:514px; margin-top:-25px; padding-bottom:4px; background-color:#999"> 
    <?php	for($x=0; $x<=19; $x++): ?>
    
    <div class="droppable f_gris" id="t_<?=$x+1;?>" code="" cantidad="" style="position:relative; float:left; width:140px; height:20px; border:1px solid #000; margin:3px 0 0 3px; padding-top:2px;" align="center"></div>
    
    <div id="cant_<?=$x+1;?>" style="position:relative; float:left; width:41px; height:20px; background-color:#FFF; border:1px solid #000; margin:3px 0 0 3px; padding-top:0px;"></div>
    <?php	endfor; ?>
    </div>
    <div id="pdf_code_bar" style="position: fixed; width:245px; margin-left:750px; margin-top:10px;">
        <label for="textfield">Observacion:(<span id="numtxt_stock_pasteles"> 0 </span>/ 1500 ) </label>
        <textarea name="obs" id="txt_stock_pasteles" cols="51" rows="5" onkeypress="return limita(1500,'txt_stock_pasteles', event)" class="text_area"></textarea>
        <button class="" type="button" id="btn_solicita_pedido" style="width:380px; ">Solicitar pedido de Pasteles</button> 
        <br /><br />
        <div id="popup_contenido"></div>
    </div>
</div>

<div id="ajax_pedidos_usr" style="position: relative; width:100%; height: auto; margin-top:100px; clear:both;"></div>


