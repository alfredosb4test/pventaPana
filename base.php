<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
?> 
<script src="js/script.js"></script>
<div class="f_negro titulo_frm" style="position: fixed; width:100%; z-index:20">
  <div style="position:relative; top:7px;">Titulo</div>  
</div> 

<div  style="position:relative; width:100%; height:500px; clear:both; margin-top:44px;">
     Test
    <div  style="position:relative; width:50%;  float:left;">
         <div id="ajax_respuesta"></div>
    </div>
</div>





