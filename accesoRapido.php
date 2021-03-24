<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
$accesos_rapidos = $conn->get_accesos_rapidos( $_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal'] );
?> 


<style>
  #sortable { list-style-type: none; margin: 0; padding: 0; width: 98%; clear: both;}
  #sortable li { margin: 3px 3px 3px 0; padding: 1px; float: left; width: 100px; height: 110px; font-size: 12px; text-align: center; }
</style>

<script type="text/javascript">
$(document).ready(function(e) {  
    $( "#sortable" ).sortable({
         
        update: function (event, ui) {
            
            $array_id =  $(this).sortable('toArray').toString();
            console.log( $array_id );
            
            $.ajax({
                type: "POST",
                contentType: "application/x-www-form-urlencoded", 
                url: "crud_pventas.php",
                data: "accion=orden_accesos_caja&array_id="+$array_id,
                beforeSend:function(){/* $("#ajax_respuesta").html($load); */},	 
                success: function(datos){ 
                console.log(datos);
                return;   
                    var obj = jQuery.parseJSON(datos);	
                    $("#ajax_respuesta").empty();		
                    if(obj.status == "ok"){
                        console.log( 'OK' );
                    }	 			  
                    if(obj.status == "error_sql"){
                        console.log( 'error_sql' );
                    } 	
                },
                timeout:90000,
                error: function(){ 					
                        $("#ajax_respuesta").html('<div class="msg alerta_err">Problemas con el Servidor</div>');
                }	   
            });
        }
    });
    $( "#sortable" ).disableSelection();

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
    $txt = $("#"+$id_prod).children().eq(0).text();
    $("#cont_txtNombreProd").html($txt)
    console.log($txt)
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
            //console.log('success', datos)   
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
                $("#btn_accesoRapido").click();
                // $("#img_prod").attr('id_prod', '');
                // $("#img_prod").attr('src', 'images/cantidad_del.png');
                // $("#"+$id_prod).remove();
                // $("#ajax_x_producto").html('<div align="center" class="msg alerta_ok t_verde_fuerte">Eliminado</div>');
                
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
        <div id="cont_txtNombreProd" style="margin-left:7px; width:90%; height:50px;">

        </div>
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
<div id="cont_productosList" style="position: relative; width:68%;  float:left; margin:50px 0 0 5px;">

    
    <?php
            
    foreach($accesos_rapidos as $key=>$prod){
        // echo  $accesos_rapidos[$key]['nombre']."<br>";
        $li .= '<li class="ui-state-default"  id="'.$accesos_rapidos[$key]["id_prod"].'">'.
                    '<span class="hide nombreCompletoItem">'.$accesos_rapidos[$key]["nombreCompleto"].'</span>
                    <span class="nombreItem">'.$accesos_rapidos[$key]["nombre"].'</span>
                    <img class="hand icoCaja" 
                    onclick="select_producto(\''.$accesos_rapidos[$key]["id_prod"].'\', \''.$accesos_rapidos[$key]["imagen"].'\')" 
                    src="img_productos/'.$accesos_rapidos[$key]["imagen"].'">'.
                '</li>';
        // $tabla .= '<table id="'.$accesos_rapidos[$key]["id_prod"].'" style="position:relative; float:left;" border="0" cellpadding="1" cellspacing="0" width="65">';
        // $tabla .= 
        //     '<tr>
        //         <td>
        //             <img class="hand icoCaja" 
        //                 onclick="select_producto(\''.$accesos_rapidos[$key]["id_prod"].'\', \''.$accesos_rapidos[$key]["imagen"].'\')" 
        //                 src="img_productos/'.$accesos_rapidos[$key]["imagen"].'"
        //                 alt="'.$accesos_rapidos[$key]["nombre"].'"
        //                 title="'.$accesos_rapidos[$key]["nombre"].'" >
        //         </td>
        //     </tr>';
        // $tabla .= "</table>";    
    }
    //echo $tabla;	
    ?>
     
    <ul id="sortable">
        <?=$li;?>
    </ul>
</div> 

 
 
 

