<?php
session_start();

date_default_timezone_set('America/Mexico_City');
?>
<!DOCTYPE html>
<html lang="es">
<head>
</head>
<body style="margin:0; padding:0;" onLoad="window.print();">
<?php 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
// Include the main TCPDF library (search for installation path).


$array_ids = explode(",",$_GET['array_id']);
$array_cantidad_solicitada = explode(",",$_GET['array_cantidad_solicitada']);
$array_precio_prod = explode(",",$_GET['array_precio_prod']);
$total = $_GET['total'];
$nombre_cajero = $_GET['nombre_cajero'];

$tr="";
foreach ($array_ids as $key => $id) {
	$tr .= '<tr  style="border-bottom:1px solid #000">
				<td>'.$array_cantidad_solicitada[$key].'</td><td>'.$conn->get_nombre_producto($id,30).'</td><td align="right">'.$array_precio_prod[$key]."</td>
				
			</tr>";	
}
$anio = date('Y');
$mes = $conn->damemes(date('m'));
$dia = date('d');
$hora = date('H:i:s'); 
?>
	<table width="200" border="0" style="font-size:10px">
	<tr>
		<td colspan="2" align="center"> <?=$_SESSION['g_empresaNombre'];?> </td>
	</tr>	
	<tr>
		<td width="40">Sucursal:</td><td><?=$_SESSION['g_sucursal'];?></td>
	</tr>
	<tr>
		<td>Telefono:</td><td><?=$_SESSION['g_suc_tel1'];?>  <?=$_SESSION['g_suc_tel2'];?></td>        
	</tr>
    <tr>
		<td>Atendio:</td><td><?=$nombre_cajero;?></td>
    </tr>        
	<tr>
		<td colspan="2" align="right" style="font-size:8px; text-decoration:underline"><?=$dia."/".$mes."/".$anio." ".$hora?></td>
	</tr>	
	<table>
	
    <table width="200" border="0" style="font-size:7px">
		<tr bgcolor="#CCCCCC">
			<th width="12" align="center">#</th>
			<th width="100" align="center">Producto</th>
			<th width="25" align="center">Precio.</th>
		</tr>	
		<?=$tr?>
		<tr bgcolor="#CCCCCC">
			<th width="100" align="right" colspan="2">Total:</th>
			<th width="25" align="right">$<?=$total;?></th>
		</tr>        
        
	</table>
    <table width="200" border="0" style="font-size:7px">
		<tr>
			<td width="25" align="center"><?=$_SESSION['g_suc_dir'];?></td>
		</tr> 
    </table>        
    
</body>
</html> 
