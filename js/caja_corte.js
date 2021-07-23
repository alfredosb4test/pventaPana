$total_caja = 0;
$cajaInicio = 0;
$denominaciones = [];
$denominacionesUpdate = [];
$suma_retiros = 0;
$suma_ingresos = 0;
$(document).ready(function(e)
{ 
    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded", 
        url: 'crud_pventas.php',
        data: "accion=corte_caja_dia",
        beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
        success: function(datos){ 
             $total_caja = parseInt(datos); 
             $("#totalVenta").html( "$" + $total_caja );	
            								 							 			
        },
        timeout:90000,
        error: function(){ 					
               $("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
           }	   
    });
    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded", 
        url: 'crud_pventas.php',
        data: "accion=denominaciones",
        beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
        success: function(datos){ 
            //console.log("denominaciones::" + datos);
            obj = jQuery.parseJSON(datos);		
			if(obj.length){
				obj.forEach(element => {
                    $("." + element.denominacion).val(element.cantidad); 
                    calcularDeno(element.denominacion, element.cantidad);                   
                });                                
            }							 							 			
        },
        timeout:90000,
        error: function(){ 					
               $("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
           }	   
    });
    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded", 
        url: 'crud_pventas.php',
        data: "accion=retiro",
        beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
        success: function(datos){ 
            //console.log("retiro::" + datos);
            obj = jQuery.parseJSON(datos);		
			if(obj.length){
                $td = '';
                $tr = '';
				obj.forEach(element => {
                    $td = "<td>" + element.motivo + "</td>";
                    $td += "<td align='right'>$" + element.total + "</td>";
                    $suma_retiros += parseInt(element.total);
                    $tr = "<tr>" + $td + "</tr>";
                    $("#tbody_retiros").append($tr)
                });
                $("#cajaRetiros").html("$"+$suma_retiros);
                // console.log("retiro::", $tr);
                
            }
        },
        timeout:90000,
        error: function(){ 					
               $("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
           }	   
    });
    
    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded", 
        url: 'crud_pventas.php',
        data: "accion=ingreso",
        beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
        success: function(datos){ 
            // console.log("retiro::" + datos);
            obj = jQuery.parseJSON(datos);	
			if(obj.length){
                $td = '';
                $tr = '';
				obj.forEach(element => {
                    $td = "<td>" + element.motivo + "</td>";
                    $td += "<td align='right'>$" + element.total + "</td>";
                    $suma_ingresos += parseInt(element.total);
                    $tr = "<tr>" + $td + "</tr>";
                    $("#tbody_ingresos").append($tr)
                });
                $("#cajaIngresos").html("$"+$suma_ingresos);                
            }
            calTotalCaja();
        },
        timeout:90000,
        error: function(){ 					
               $("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
           }	   
    });

    $('.inputNumber').on('change keyup', function(){
        var valor = parseInt( $(this).val(), 10 );
        if( valor >100){
            $(this).val(100);
            valor = 100;
        }
        $etiqueta = $(this).attr('etiqueta');
        calcularDeno($etiqueta, valor);
    });

    $( "#btn_guardar_deno" ).button({ 
		text: true, 
    });

    $('#btn_guardar_deno').click(function(){
        arrDeno = JSON.stringify(Object.assign({}, $denominacionesUpdate))
        console.log(arrDeno);
 
        array = JSON.stringify($denominacionesUpdate);
        //console.log(array);
        $.ajax({
            type: "POST",
            contentType: "application/x-www-form-urlencoded", 
            url: 'crud_pventas.php',
            data: "accion=guarda_denominaciones&denominaciones="+arrDeno,
            beforeSend:function(){ /* $("#ajax_respuesta").html($load); */ },	 
            success: function(datos){
                try{
                    
                    var obj = jQuery.parseJSON(datos);	 
                    if(obj.status == "ok_update"){
                        $("#resultados_guardar_deno").html('<div class="msg alerta_ok t_verde_fuerte">Datos guardados.</div>');
                    }else{
                        $("#resultados_guardar_deno").html('<div class="msg alerta_err">Problemas con el servidor.</div>');
                    }
                    
                    $("#ajax_respuesta").empty();
                }catch(err) {
                     $("#resultados_guardar_deno").html('<div class="msg alerta_err">'+err.message+'</div>');
                }	
            },
            timeout:90000,
            error: function(){ 					
                   $("#ajax_respuesta").html('Problemas con el servidor intente de nuevo.');
               }	   
           });
    }); 
                                    
});

function calcularDeno($etiqueta, valor){
     
    switch ($etiqueta){
        case 'deno_5c':
            $("#deno_5c").html( '$' + valor * .5 );
            $denominaciones['deno_5c'] = valor * .5;
            $denominacionesUpdate['deno_5c'] = valor;
        break;
        case 'deno_1p':
            $("#deno_1p").html( '$' + valor * 1 );
            $denominaciones['deno_1p'] = valor * 1;
            $denominacionesUpdate['deno_1p'] = valor;
        break;
        case 'deno_2p':
            $("#deno_2p").html( '$' + valor * 2 );
            $denominaciones['deno_2p'] = valor * 2;
            $denominacionesUpdate['deno_2p'] = valor;
        break;
        case 'deno_5p':
            $("#deno_5p").html( '$' + valor * 5 );
            $denominaciones['deno_5p'] = valor * 5;
            $denominacionesUpdate['deno_5p'] = valor;
        break;
        case 'deno_10p':
            $("#deno_10p").html( '$' + valor * 10 );
            $denominaciones['deno_10p'] = valor * 10;
            $denominacionesUpdate['deno_10p'] = valor;
        break;
        case 'deno_20p':
            $("#deno_20p").html( '$' + valor * 20 );
            $denominaciones['deno_20p'] = valor * 20;
            $denominacionesUpdate['deno_20p'] = valor;
        break;
        case 'deno_50p':
            $("#deno_50p").html( '$' + valor * 50 );
            $denominaciones['deno_50p'] = valor * 50;
            $denominacionesUpdate['deno_50p'] = valor;
        break;
        case 'deno_100p':
            $("#deno_100p").html( '$' + valor * 100 );
            $denominaciones['deno_100p'] = valor * 100;
            $denominacionesUpdate['deno_100p'] = valor;
        break;
        case 'deno_200p':
            $("#deno_200p").html( '$' + valor * 200 );
            $denominaciones['deno_200p'] = valor * 200;
            $denominacionesUpdate['deno_200p'] = valor;
        break;
        case 'deno_500p':
            $("#deno_500p").html( '$' + valor * 500 );
            $denominaciones['deno_500p'] = valor * 500;
            $denominacionesUpdate['deno_500p'] = valor;
        break;
    }
    suma = 0;
    Object.keys($denominaciones).forEach ( key =>{
        suma += $denominaciones[key];
        
    });
    $("#total_deno").html("$" + suma);
    $("#cajaInicio").html("$" + suma);
    $cajaInicio = suma;
    calTotalCaja();
    
}
function calTotalCaja(){
    console.log('cajaInicio::', $cajaInicio);
    console.log('total_caja::', $total_caja);
    console.log('suma_ingresos::', $suma_ingresos);
    console.log('suma_retiros::', $suma_retiros);
    $totalCaja = 0;
    $totalCaja = ($cajaInicio + $total_caja + $suma_ingresos) - $suma_retiros;
    $("#totalCaja").html( "$" + $totalCaja );
}

 