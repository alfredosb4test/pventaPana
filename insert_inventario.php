<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
?> 
<script src="js/insert_inventario_min.js"></script>
<div class="f_negro titulo_frm" style="position: fixed; width:100%; z-index:20">
  <div style="position:relative; top:7px;">Agregar Productos al Inventario</div>  
</div> 

<div  style="position:relative; width:100%; height:500px; clear:both; margin-top:44px;">
    <div  style="position:relative; width:35%; height:250px; float:left; ">
          <table id="tbl_sucursales_admin" border="0" style="position:relative; width:100%;">
            <tr>
              <td width="200"><label for="textfield">Selecciona una Sucursal: </label></td>
              <td> 
                  <span class="cont_sucursales_admin" style="display: ; position: relative; margin:0; float:left;">
                        <select id='lst_sucursales_admin_prod' name='lst_sucursales_admin_prod' style="width:250px; "  >
                          <?php echo $conn->lst_sucursales_admin('tbl_sucursal', 'id_sucursal', 'sucursal', $_SESSION['g_id_sucursal'], '', $_SESSION['g_id_empresa'],$_SESSION['g_sucursales']); ?> 
                       </select>
                  </span>
              </td>
          </tr> 
          <tr>
            <td>Codigo de Producto:</td>
            <td>
                <input class="text_box" id="txt_cj_codigo" type="text" value="" placeholder="Codigo del Articulo" onkeypress="key_buscar_producto(event,this)" style="width:250px;" required  maxlength="25"> 
            </td>
          </tr>
          <tr>
            <td colspan="2">
                <div id="text_nombre_prod" id_prod="" style="position: relative; width:100%; height: auto; margin-top:3px; "></div>
            </td>
          </tr>
          <tr id="tr_cantidad" style="display:none;">
            <td>Cantidad a Agregar:</td>
            <td>
                <select name="select_cantidad" id="select_cantidad" style="width:80px; ">
                <?php
                    for($x=1;$x<=20; $x++){
                        echo "<option>$x</option>";
                    }
                ?> 
                </select> 
            </td>
          </tr>    
          <tr>
          	<td>&nbsp;</td>
            <td align="">
                <button class="" type="button" id="btn_insert_list" style="width:300px;">Agregar al Inventario</button>
            </td>
          </tr>     
          <tr>
            <td colspan="2">
                
            </td>
          </tr>         
             
          </table>
    </div>
     
    <div  style="position:relative; width:50%;  float:left;">
         <div id="ajax_respuesta_insert"></div>
    </div>
</div>





