<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
$fecha = date("d-m-Y");
?> 
<script src="js/caja_corte.js"></script>
<div class="f_negro titulo_frm" style="position: fixed; width:100%; z-index:20">
  <div style="position:relative; top:7px;">Corte de Caja</div>  
</div> 

<div style="position:relative; width:100%; height:500px; clear:both; margin-top:44px;">
    <div style="position:relative; width:49%; float:left;">
        <table class="styled-table" width="430">
            <thead>
            <tr>
                <th>Denominacion</th>
                <th>Cantidad</th>
                <th align="center">Total</th>
            </tr> 
            </thead>  
            <tbody>
            <tr>
                <td>.50 centavos</td>
                <td><input type="number" etiqueta="deno_5c" min="0" max="100" style="width:70px; height:20px; font-size:14px" class="inputNumber deno_5c"></td>
                <td><div style="width:80px; text-align: right;" id="deno_5c"></div></td>
            </tr>
            <tr>
                <td>1 pesos</td>
                <td><input type="number" etiqueta="deno_1p" min="0" max="100" style="width:70px; height:20px; font-size:14px" class="inputNumber deno_1p"></td>
                <td><div style="width:80px; text-align: right;" id="deno_1p"></div></td>
            </tr>
            <tr>
                <td>2 pesos</td>
                <td><input type="number" etiqueta="deno_2p" min="0" max="100" style="width:70px; height:20px; font-size:14px" class="inputNumber deno_2p"></td>
                <td><div style="width:80px; text-align: right;" id="deno_2p"></div></td>
            </tr>
            <tr>
                <td>5 pesos</td>
                <td><input type="number" etiqueta="deno_5p" min="0" max="100" style="width:70px; height:20px; font-size:14px" class="inputNumber deno_5p"></td>
                <td><div style="width:80px; text-align: right;" id="deno_5p"></div></td>
            </tr>
            <tr>
                <td>10 pesos</td>
                <td><input type="number" etiqueta="deno_10p" min="0" max="100" style="width:70px; height:20px; font-size:14px" class="inputNumber deno_10p"></td>
                <td><div style="width:80px; text-align: right;" id="deno_10p"></div></td>
            </tr>
            <tr>
                <td>20 pesos</td>
                <td><input type="number" etiqueta="deno_20p" min="0" max="100" style="width:70px; height:20px; font-size:14px" class="inputNumber deno_20p"></td>
                <td><div style="width:80px; text-align: right;" id="deno_20p"></div></td>
            </tr>
            <tr>
                <td>50 pesos</td>
                <td><input type="number" etiqueta="deno_50p" min="0" max="100" style="width:70px; height:20px; font-size:14px" class="inputNumber deno_50p"></td>
                <td><div style="width:80px; text-align: right;" id="deno_50p"></div></td>
            </tr>
            <tr>
                <td>100 pesos</td>
                <td><input type="number" etiqueta="deno_100p" min="0" max="100" style="width:70px; height:20px; font-size:14px" class="inputNumber deno_100p"></td>
                <td><div style="width:80px; text-align: right;" id="deno_100p"></div></td>
            </tr>
            <tr>
                <td>200 pesos</td>
                <td><input type="number" etiqueta="deno_200p" min="0" max="100" style="width:70px; height:20px; font-size:14px" class="inputNumber deno_200p"></td>
                <td><div style="width:80px; text-align: right;" id="deno_200p"></div></td>
            </tr>
            <tr>
                <td>500 pesos</td>
                <td><input type="number" etiqueta="deno_500p" min="0" max="100" style="width:70px; height:20px; font-size:14px" class="inputNumber deno_500p"></td>
                <td><div style="width:80px; text-align: right;" id="deno_500p"></div></td>
            </tr>
        </tbody> 
        <tfoot>
            <tr>
                <td colspan="2" align="right">Total</td>
                <td align="center" id="total_deno">$0</td>
            </tr>
            
        </tfoot>
        </table>
        <table width="430">
            <tr>
                <td align="right"><button class="" type="button" id="btn_guardar_deno" style="width:72; padding:0">Guardar</button> </td> 
            </tr>
            <tr>
                <td align="right"><div id="resultados_guardar_deno"></div></td> 
            </tr>
        </table>    
    </div>
    <div style="position:relative; width:49%; float:left; margin: 25px 0;">    
    
        <div style="width:99%; background-Color: #FFF; padding:5px;">
            <div style="position: relative; margin-top:0px; height:30px;">
                <div style="width:50%;float: left;">Fecha:</div>
                <div style="width:50%;float: left;text-align: right;"><?=$fecha;?></div>
            </div>
            <div style="position: relative; margin-top:2px; height:30px; padding: 6px 5px 0 5px" class="tr_gris">
                <div style="width:50%;float: left;margin-top: 3px;">Caja Inicio:</div>
                <div style="width:50%;float: left;text-align: right;margin-top: 3px;"><span id="cajaInicio">$0</span></div>
            </div>
            <div style="position: relative; margin-top:2px; height:30px; padding: 6px 5px 0 5px">
                <div style="width:50%;float: left;margin-top: 3px;">Caja Retiros:</div>
                <div style="width:50%;float: left;text-align: right;margin-top: 3px;"><span id="cajaRetiros">$0</span></div>
            </div>
            <div style="position: relative; margin-top:2px; height:30px; padding: 6px 5px 0 5px" class="tr_gris">
                <div style="width:50%;float: left;margin-top: 3px;">Caja Ingresos:</div>
                <div style="width:50%;float: left;text-align: right;margin-top: 3px;"><span id="cajaIngresos">$0</span></div>
            </div>
            <div style="position: relative; margin-top:2px; height:30px; padding: 6px 5px 0 5px">
                <div style="width:50%;float: left;margin-top: 3px;">Total de Venta:</div>
                <div style="width:50%;float: left;text-align: right;margin-top: 3px;"><span id="totalVenta">$0</span></div>
            </div>
            <div style="position: relative; margin-top:2px; height:30px; padding: 6px 5px 0 5px" class="tr_gris">
                <div style="width:50%;float: left;margin-top: 3px;">Total en Caja:</div>
                <div style="width:50%;float: left;text-align: right;margin-top: 3px;"><span id="totalCaja">$0</span></div>
            </div>
        </div>
        <br>
        <div style="width:99%; background-Color: #FFF; padding:0px; top:20px;">
            <table class="styled-table" width="100%">
                <thead>
                <tr>
                    <th colspan="2" align="center">Retiros</th>
                </tr>
                <tr>
                    <th width="80%">Concepto</th>
                    <th>Total</th>
                </tr> 
                </thead>
                <tbody id="tbody_retiros"></tbody>
            </table>
        </div>
         
        <div style="width:99%; background-Color: #FFF; padding:0px; top:20px;">
            <table class="styled-table" width="100%">
                <thead>
                <tr>
                    <th colspan="2" align="center">Ingresos</th>
                </tr>
                <tr>
                    <th width="80%">Concepto</th>
                    <th>Total</th>
                </tr> 
                </thead>
                <tbody id="tbody_ingresos"></tbody>
            </table>
        </div>
    </div>
    <div  style="position:relative; width:50%;  float:left;">
         <div id="ajax_respuesta"></div>
    </div>
</div>

<style>
.styled-table {
    border-collapse: collapse;
    margin: 25px 5px;
    font-size: 0.9em;
    font-family: sans-serif;
    min-width: 400px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}
.styled-table thead tr {
    background-color: #009879;
    color: #ffffff;
    text-align: left;
}
.styled-table tfoot tr {
    background-color: #009879;
    color: #ffffff;
    text-align: left;
}
.styled-table th,
.styled-table td {
    padding: 4px 10px;
}
.styled-table tbody tr {
    border-bottom: 1px solid #dddddd;
}

.styled-table tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

.styled-table tbody tr:last-of-type {
    border-bottom: 2px solid #009879;
}
.styled-table tbody tr.active-row {
    font-weight: bold;
    color: #009879;
}
</style>



