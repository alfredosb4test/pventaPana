<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
$accesos_rapidos = $conn->get_accesos_rapidos( $_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal'] );
?> 

<script type="text/javascript">
$(document).ready(function(e) {  
    $("#txt_cj_nombre").autocomplete({
		source: "crud_pventas.php?accion=autocompleta_producto_caja",			
		//appendTo: '#menu-container',
		minLength: 3,						
		select: function (event, ui) {				
			add_prod(ui.item.codigo);
		    $('#txt_cj_nombre').attr('value','').focus();						
		},
	});	
});
function select_producto($id_prod, $imagen){
    $("#img_prod").attr('src', 'img_productos/' + $imagen);
    $("#img_prod").attr('id_prod', $id_prod);
}
function add_prod($id_prod){
    if( $id_prod === undefined || $id_prod == '' )
        return;

    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded", 
        url: "crud_pventas.php",
        data: "accion=add_accesoCaja&id_prod="+$id_prod,
        beforeSend:function(){/* $("#ajax_respuesta").html($load); */},	 
        success: function(datos){ 
        //alert(datos)   
            var obj = jQuery.parseJSON(datos);	
            $("#ajax_respuesta").empty();		
            if(obj.status == "ok"){
                $("#btn_accesoRapido").click()
                $("#ajax_x_producto").html('<div align="center" class="msg alerta_ok t_verde_fuerte">Agregado</div>');
                
            }	 			  
            if(obj.status == "error_sql"){
                $("#ajax_x_producto").html('<div class="msg alerta_err">Problemas con el SQL</div>');
                $("#txt_id_prod").val("");
            } 	
        },
        timeout:90000,
        error: function(){ 					
                $("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
        }	   
    });
    console.log($id_prod)
}
function del_prod(){
    $id_prod = $("#img_prod").attr('id_prod');
    $("#ajax_x_producto").empty();
    if( $id_prod === undefined || $id_prod == '' )
        return;


    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded", 
        url: "crud_pventas.php",
        data: "accion=del_accesoCaja&id_prod="+$id_prod,
        beforeSend:function(){/* $("#ajax_respuesta").html($load); */},	 
        success: function(datos){ 
        //alert(datos)   
            var obj = jQuery.parseJSON(datos);	
            $("#ajax_respuesta").empty();	
            if(obj.status == "ok"){
                $("#img_prod").attr('id_prod', '');
                $("#img_prod").attr('src', 'images/cantidad_del.png');
                $("#"+$id_prod).remove();
                $("#ajax_x_producto").html('<div align="center" class="msg alerta_ok t_verde_fuerte">Eliminado</div>');
                
            }	 			  
            if(obj.status == "error_sql"){
                $("#ajax_x_producto").html('<div class="msg alerta_err">Problemas con el SQL</div>');
                $("#txt_id_prod").val("");
            } 	
        },
        timeout:90000,
        error: function(){ 					
                $("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
        }	   
    });
    console.log($id_prod)
}

</script>

<div class="f_negro titulo_frm" style="position: fixed; width:100%; z-index:20">
  <div style="position:relative; top:7px;">Accesos Rapidos en Caja</div>  
</div> 

<div id="cont_productos" style="position: relative; width:30%;  float:left; margin:50px 0 0 5px;">
    <div>
        <div class="f_negro table_top">
            <div style="margin: 5px 0 0 5px; position: absolute;">Eliminar producto</div>
        </div>
    </div>
    <br>
    <div id="cont_prod">
        <img id="img_prod" class="icoCaja" src="images/cantidad_del.png" style="margin-left:7px">
    </div>
    <div class="button_cobrar" onclick="del_prod()" style="margin-left:9px; width:73px; height:30px; font-size:12px">
        <div style="padding-top:9px; width:70px;">Eliminar</div>
    </div>
    <br>
    <div style="width:150px; height:50px;" id="ajax_x_producto"></div>

    <div style="position:relative; margin-top:20px;">
        <div class="f_negro table_top">
            <div style="margin: 5px 0 0 5px; position: absolute;">Agregar producto</div>
        </div>
    </div>
    <br>
    <input class="text_box" id="txt_cj_nombre" type="text" placeholder="Nombre del Articulo" style="width:250px;" required>
</div>
<div id="cont_productos" style="position: relative; width:68%;  float:left; margin:50px 0 0 5px;">
<?php
            
    foreach($accesos_rapidos as $key=>$prod){
        // echo  $accesos_rapidos[$key]['nombre']."<br>";
        $tabla .= '<table id="'.$accesos_rapidos[$key]["id_prod"].'" style="position:relative; float:left;" border="0" cellpadding="1" cellspacing="0" width="65">';

        $tabla .= 
            '<tr>
                <td>
                    <img class="hand icoCaja" 
                        onclick="select_producto(\''.$accesos_rapidos[$key]["id_prod"].'\', \''.$accesos_rapidos[$key]["imagen"].'\')" 
                        src="img_productos/'.$accesos_rapidos[$key]["imagen"].'"
                        alt="'.$accesos_rapidos[$key]["nombre"].'"
                        title="'.$accesos_rapidos[$key]["nombre"].'" >
                </td>
            </tr>';
        $tabla .= "</table>";    
    }
    echo $tabla;	
?>
</div> 

 
 
 

