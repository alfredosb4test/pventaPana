<?php
session_start(); 
if(array_key_exists("accion", $_POST) && $_POST['accion']=='cj_buscar_producto'){		
	$codigo = trim($_POST['codigo']);
	$id_empresa = $_SESSION['g_id_empresa'];
	$id_sucursal = $_SESSION['g_id_sucursal'];
	$sql="SELECT * FROM tbl_producto WHERE codigo = '$codigo' AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND activo='1' AND visible='1'";
	$conn = new mysqli('localhost', 'pventa', 'pv3n74*', 'pventa_almacen');
	if($result = $conn->query($sql)) {		 
				if($result->num_rows){	
					while ($row = $result->fetch_assoc()) {
					  	$activar_cantidades = $_SESSION['activar_cantidades'];
					  	echo '{
						  	"status":"existe",
						  	"id":"'.$row['id'].'",
							"id_empresa":"'.$row['id_empresa'].'",
							"id_admin":"'.$row['id_admin'].'",
							"id_sucursal":"'.$row['id_sucursal'].'",
							"id_unidad":"'.$row['id_unidad'].'",
							"id_seccion":"'.$row['id_seccion'].'",
							"id_proveedor":"'.$row['id_proveedor'].'",
							"codigo":"'.$row['codigo'].'",
							"nombre":"'.$row['nombre'].'",
							"imagen":"'.$row['imagen'].'",
							"precio_provedor":"'.$row['precio_provedor'].'",
							"precio_venta":"'.$row['precio_venta'].'",
							"precio_mayoreo":"'.$row['precio_mayoreo'].'",
							"cantidad":"'.$row['cantidad'].'",
							"activar_cantidades":"'.$activar_cantidades.'",
							"minimo":"'.$row['minimo'].'"}';	
					}	
				}else
					echo '{"status":"no_existe"}';
	}else
		echo '{"status":"error_sql"}';
	$result->close();				
}
$conn->close();
?>