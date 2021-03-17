<?php
session_start(); 
date_default_timezone_set('America/Mexico_City');
include('funciones/conexion_class.php');
if(array_key_exists("accion", $_POST) && $_POST['accion']=='insert_cliente'){
	//echo $_SESSION['g_id_empresa'];
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	echo $conn->insert_cliente($_SESSION['g_id_empresa'],$_SESSION['g_NumEmp'],$_POST['nombre'], $_SESSION['g_id_sucursal'], $_POST['dir'], $_POST['ciudad'], $_POST['tel'], $_POST['cel'], $_POST['correo'], $_POST['obs']);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='edit_cliente'){
	//echo "OK1";
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	//echo "ID:".$_POST['correo'];
	echo $conn->edit_cliente($_POST['nombre'], $_POST['dir'], $_POST['ciudad'], $_POST['tel'], $_POST['cel'], $_POST['correo'], $_POST['obs'], $_POST['id']);
}


if(array_key_exists("accion", $_POST) && $_POST['accion']=='insert_prove'){
	//echo $_SESSION['g_id_empresa'];
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	echo $conn->insert_prove($_SESSION['g_id_empresa'],$_SESSION['g_NumEmp'],$_POST['empresa'], $_SESSION['g_id_sucursal'], $_POST['contacto'], $_POST['dir'], $_POST['ciudad'], $_POST['tel'], $_POST['cel'], $_POST['correo'], $_POST['obs']);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='edit_prove'){
	//echo "OK1";
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	//echo "ID:".$_POST['correo'];
	echo $conn->edit_prove($_POST['empresa'], $_POST['contacto'], $_POST['dir'], $_POST['ciudad'], $_POST['tel'], $_POST['cel'], $_POST['correo'], $_POST['obs'], $_POST['id']);
}

if(array_key_exists("accion", $_POST) && $_POST['accion']=='insert_pedido'){
	//echo $_SESSION['g_id_empresa'];
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$_POST['servicios'] = "[".rtrim($_POST['servicios'],",")."]";
	// print_r($_POST); exit;
	$hoy = getdate();
	$_POST['fecha_inicio'] = $_POST['fecha_inicio'].' '.$hoy['hours'].':'.$hoy['minutes'].':'.$hoy['seconds'];
	$_POST['fecha_final'] = $_POST['fecha_final'].' '.$hoy['hours'].':'.$hoy['minutes'].':'.$hoy['seconds'];
	if($_POST['anticipo'] >= $_POST['total'])
		$estatus = "cerrado";
	else
		$estatus = "abierto";
	//print_r($_POST);
	//$id_empresa, $id_sucursal, $id_admin,$id_cliente,$fecha_inicio, $fecha_final, $total, $anticipo_inicial, $estatus, $obs
	echo $conn->insert_pedido($_SESSION['g_id_empresa'],$_SESSION['g_id_sucursal'],$_SESSION['g_NumEmp'], $_POST['id_cliente'],$_POST['fecha_inicio'], $_POST['fecha_final'], $_POST['total'], $_POST['anticipo'], $estatus, $_POST['obs'], $_POST['servicios']);
		
		include("phpmailer/class.phpmailer.php");
		$mail = new PHPMailer();
		//$mail->Host = "localhost";
		$mail->IsSMTP(); 
		$mail->SMTPAuth = false; // True para que verifique autentificación de la cuenta o de lo contrario False 
		$mail->Username = "alfredo@pasteleriarce.com.mx"; // Cuenta de e-mail 
		$mail->Password = "PVenta100"; // Password 	
		
		$mail->Host = "localhost";
		$mail->From = 'pedidos@pasteleriarce.com.mx';	// PVenta100
		$mail->FromName = $_SESSION['g_sucursal']; // REMITENTE
		$mail->Subject = "Pedido Nuevo"; //ASUNTO
		$mail->AddAddress('pedidos@pasteleriarce.com.mx',"Pedido");
		//$mail->AddBCC('alfredosb2@hotmail.com',$nombre);
// atiende="+$atiende+"&cli_nombre="+$cli_nombre+"&cli_tel="+$cli_tel+"&cli_cel="+$cli_cel+"&cli_correo="+$cli_correo		
		$body  = "<h2>Cliente: <strong>".$_POST['cli_nombre']."</strong><br></h2>";
		 
		$body .= '
			<table>
				 <tr>
				 	<th align="left">Atiende</th>
					<td>'.$_POST['atiende'].'</td>
				 </tr>	
				 <tr>
				 	<th align="left">Fecha_inicio</th>
					<td>'.$_POST['fecha_inicio'].'</td>
				 </tr>	
				 <tr>
				 	<th align="left">Fecha_final</th>
					<td>'.$_POST['fecha_final'].'</td>
				 </tr>	
				 <tr>
				 	<th align="left">Total</th>
					<td style="color:#01DF01; font-weight:bold">$'.$_POST['total'].'</td>
				 </tr>	
				 <tr>
				 	<th align="left">Anticipo</th>
					<td style="color:#01DF01; font-weight:bold">$'.$_POST['anticipo'].'</td>
				 </tr>	
				 <tr>
				 	<th align="left">Servicios</th>
					<td>'.$_POST['lst_serv_input'].'</td>
				 </tr>
				 <tr>
				 	<th align="left">Observaciones</th>
					<td>'.str_replace("##br##", "<br>",$_POST['obs']).'</td>
				 </tr>	
			<table>	 				 				 				 				 				 
		';
		$body .= '<hr><br>
			<h2>Datos del Cliente</h2>
			<table>
				 <tr>
				 	<th align="left">Cliente</th>
					<td>'.$_POST['cli_nombre'].'</td>
				 </tr>
				 <tr>
				 	<th align="left">Telefono</th>
					<td>'.$_POST['cli_tel'].'</td>
				 </tr>
				 <tr>
				 	<th align="left">Celular</th>
					<td>'.$_POST['cli_cel'].'</td>
				 </tr>
				 <tr>
				 	<th align="left">Correo</th>
					<td>'.$_POST['cli_correo'].'</td>
				 </tr>				 				 
			<table>';		
		$mail->Body  = $body;
		$mail -> IsHTML (true);
		if(!$mail->Send())
		{
			$mail->ErrorInfo; 	
		}	
}



if(array_key_exists("accion", $_POST) && $_POST['accion']=='activar_desactivar_prove'){
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	echo $conn->activa_prove($_POST['id'], $_POST['valor']);	
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='activar_desactivar_producto'){
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	//print_r( $_POST );
	echo $conn->activa_producto($_POST['id'], $_POST['valor']);	
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='borrar_producto'){
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	//print_r( $_POST );
	echo $conn->borrar_producto($_POST['id'], $_POST['valor']);	
}

if(array_key_exists("accion", $_POST) && $_POST['accion']=='activar_desactivar_cliente'){
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	echo $conn->activa_cliente($_POST['id'], $_POST['valor']);	
}

if(array_key_exists("accion", $_REQUEST) && $_REQUEST['accion'] == 'autocompleta_prove'){ 

	$buscar_empresa = $_REQUEST['term'];
	$items[] = array();//creamos un array llamado items			
	$sql="SELECT * FROM tbl_proveedor  WHERE empresa LIKE '%".$buscar_empresa."%' ";	

	//si no hay registros retornamos
	$conn = new class_mysqli();
	if ($result = $conn->conn_mysqli->query($sql)) {			
		$i=0; //creo una variable del tipo entero
		while ($row = $result->fetch_assoc()) {
			$i++;
			$id = '{"id":"OK",';
			$label = '"label":"'.str_replace('"', "'", $row["empresa"]).'",';
			$value = '"value":"'.str_replace('"', "'", $row["id_proveedor"]).'"}';
			$json_completo .= $id
			.$label
			.$value.',';	
		}
		$json_completo =  "[".rtrim($json_completo, " ,")."]";
		echo $json_completo;
		$conn->close_mysqli();
	}
}


if(array_key_exists("accion", $_REQUEST) && $_REQUEST['accion'] == 'autocompleta_producto'){ 

	$buscar_empresa = $_REQUEST['term'];
	$items[] = array();//creamos un array llamado items			
	$sql="SELECT * FROM tbl_producto  WHERE codigo LIKE '%".$buscar_empresa."%' 
		  AND id_empresa = ".$_SESSION['g_id_empresa']." AND id_sucursal = ".$_SESSION['g_id_sucursal']." AND activo='1' ";	

	//si no hay registros retornamos
	$conn = new class_mysqli();
	if ($result = $conn->conn_mysqli->query($sql)) {			
		$i=0; //creo una variable del tipo entero
		while ($row = $result->fetch_assoc()) {
			$i++;
			$id = '{"id":"OK",';
			$label = '"label":"'.str_replace('"', "'", $row["nombre"]).'",';
			$value = '"value":"'.str_replace('"', "'", $row["id"]).'"}';
			$json_completo .= $id
			.$label
			.$value.',';	
		}
		$json_completo =  "[".rtrim($json_completo, " ,")."]";
		echo $json_completo;
		$conn->close_mysqli();
	}
}

if(array_key_exists("accion", $_REQUEST) && $_REQUEST['accion'] == 'autocompleta_producto_caja'){ 

	$buscar_empresa = $_REQUEST['term'];
	$items[] = array();//creamos un array llamado items			
	$sql="SELECT * FROM tbl_producto  WHERE nombre like '%".$buscar_empresa."%'  AND activo='1' AND id_sucursal = ".$_SESSION['g_id_sucursal'];	

	//si no hay registros retornamos
	$conn = new class_mysqli();
	if ($result = $conn->conn_mysqli->query($sql)) {			
		$i=0; //creo una variable del tipo entero
		while ($row = $result->fetch_assoc()) {
			$i++;
			$id = '{"id":"OK",';
			$label = '"label":"'.str_replace('"', "'", $row["nombre"]).'",';
			$imagen = '"codigo":"'.str_replace('"', "'", $row["codigo"]).'",';
			$value = '"value":" "}';
			$json_completo .= $id
			.$imagen
			.$label
			.$value.',';	
		}
		$json_completo =  "[".rtrim($json_completo, " ,")."]";
		echo $json_completo;
		$conn->close_mysqli();
	}
}

if(array_key_exists("accion", $_REQUEST) && $_REQUEST['accion'] == 'autocompleta_cliente'){ 

	$buscar_empresa = $_REQUEST['term'];
	$items[] = array();//creamos un array llamado items			
	$sql="SELECT * FROM tbl_cliente  WHERE nombre LIKE '%".$buscar_empresa."%' 
		  AND id_empresa = ".$_SESSION['g_id_empresa']." AND id_sucursal = ".$_SESSION['g_id_sucursal']." AND activo='1' ";	

	//si no hay registros retornamos
	$conn = new class_mysqli();
	if ($result = $conn->conn_mysqli->query($sql)) {			
		$i=0; //creo una variable del tipo entero
		while ($row = $result->fetch_assoc()) {
			$i++;
			$id = '{"id":"OK",';
			$label = '"label":"'.str_replace('"', "'", $row["nombre"]).'",';
			$value = '"value":"'.str_replace('"', "'", $row["id_cliente"]).'"}';
			$json_completo .= $id
			.$label
			.$value.',';	
		}
		$json_completo =  "[".rtrim($json_completo, " ,")."]";
		echo $json_completo;
		$conn->close_mysqli();
	}
}

if(array_key_exists("accion", $_REQUEST) && $_REQUEST['accion'] == 'autocompleta_pedido_cliente'){ 
	$buscar_empresa = $_REQUEST['term'];
	$items[] = array();//creamos un array llamado items			
	$sql="SELECT * FROM tbl_cliente  WHERE nombre LIKE '%".$buscar_empresa."%' 
		  AND id_empresa = ".$_SESSION['g_id_empresa']." AND id_sucursal IN (".$_SESSION['g_sucursales'].") AND activo='1' ";	

	//si no hay registros retornamos
	$conn = new class_mysqli();
	if ($result = $conn->conn_mysqli->query($sql)) {			
		$i=0; //creo una variable del tipo entero
		while ($row = $result->fetch_assoc()) {
			$i++;
			$id = '{"id":"OK",';
			$nombre = '"nombre":"'.str_replace('"', "'", $row["nombre"]).'",';
			$dir = '"dir":"'.str_replace('"', "'", $row["dir"]).'",';
			$ciudad = '"ciudad":"'.str_replace('"', "'", $row["ciudad"]).'",';
			$tel = '"tel":"'.str_replace('"', "'", $row["tel"]).'",';
			$cel = '"cel":"'.str_replace('"', "'", $row["cel"]).'",';
			$correo = '"correo":"'.str_replace('"', "'", $row["correo"]).'",';
			$obs = '"obs":"'.str_replace('"', "'", $row["obs"]).'",';
			$id_cliente = '"id_cliente":"'.str_replace('"', "'", $row["id_cliente"]).'",';
			$value = '"value":"'.str_replace('"', "'", $row["nombre"]).'"}';
			$json_completo .= $id
			.$nombre
			.$dir
			.$ciudad
			.$tel
			.$cel
			.$correo
			.$obs
			.$id_cliente
			.$value.',';	
		}
		$json_completo =  "[".rtrim($json_completo, " ,")."]";
		echo $json_completo;
		$conn->close_mysqli();
	}
}




if(array_key_exists("accion", $_POST) && $_POST['accion']=='test'){
	echo "OK";
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='detalles_prove'){
	$obs = str_replace("##br##", "\n",$_POST['obs']);
?>

<?php  	
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='insert_producto'){
	//echo $_SESSION['g_id_empresa'];
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$_POST['codigo'] = $conn->sanear_string_especiales($_POST['codigo']);	
	//$_POST['nombre'] = $conn->sanear_string_especiales($_POST['nombre']);	
	echo $conn->insert_producto($_SESSION['g_id_empresa'],
								$_SESSION['g_NumEmp'],
								$_SESSION['g_id_sucursal'],
								$_POST['lst_unidades'],
								$_POST['lst_seccion'],
								$_POST['lst_provedor'],
								$_POST['codigo'],
								$_POST['codigo_unidades'],
								$_POST['nombre'],
								$_POST['upfile_1'],
								$_POST['precio_provedor'],
								$_POST['precio_venta'],
								$_POST['precio_mayoreo'],
								$_POST['select_cantidad'],
								$_POST['select_cantidad_min']);	
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='edit_producto'){
	//echo $_SESSION['g_id_empresa'];
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	echo $conn->edit_producto($_SESSION['g_id_empresa'],
							  $_SESSION['g_NumEmp'],
							  $_SESSION['g_id_sucursal'],
							  $_POST['lst_unidades'],
							  $_POST['lst_seccion'],
							  $_POST['lst_provedor'],
							  $_POST['codigo'],
							  $_POST['codigo_unidades'],
							  $_POST['nombre'],
							  $_POST['upfile_1'],
							  $_POST['precio_provedor'],
							  $_POST['precio_venta'],
							  $_POST['precio_mayoreo'],
							  $_POST['select_cantidad'],
							  $_POST['select_cantidad_min'], 
							  $_POST['id']);	
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='edit_cantidad_producto'){
	$conn = new class_mysqli();
	$id = $_POST['id'];
	$cantidad = $_POST['cantidad'];
	echo $conn->edit_producto_cantidad($id, $cantidad);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='comprobar_disponibilidad_producto'){		
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$codigo = $_POST['codigo'];
	$existe = $conn->comprobar_disponibilidad_producto($codigo, $_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal']);	
	if($existe == "no_existe")
		include('inc_frm_nuevo_producto.php');
	if($existe == "existe"){
		include('inc_frm_edit_producto.php');
	}		
}

// codigo de buscar producto separado actual: buscar_prod_caja.php
// if(array_key_exists("accion", $_POST) && $_POST['accion']=='cj_buscar_producto')
/*
if(array_key_exists("accion", $_POST) && $_POST['accion']=='cj_buscar_producto'){		
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$codigo = trim($_POST['codigo']);
	$existe = $conn->comprobar_disponibilidad_producto($codigo, $_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal']);	
	if($existe == "existe"){
	  $activar_cantidades = $_SESSION['activar_cantidades'];
	  echo '{
		  	"status":"existe",
		  	"id":"'.$conn->datos_prod['id'].'",
			"id_empresa":"'.$conn->datos_prod['id_empresa'].'",
			"id_admin":"'.$conn->datos_prod['id_admin'].'",
			"id_sucursal":"'.$conn->datos_prod['id_sucursal'].'",
			"id_unidad":"'.$conn->datos_prod['id_unidad'].'",
			"id_seccion":"'.$conn->datos_prod['id_seccion'].'",
			"id_proveedor":"'.$conn->datos_prod['id_proveedor'].'",
			"codigo":"'.$conn->datos_prod['codigo'].'",
			"nombre":"'.$conn->datos_prod['nombre'].'",
			"imagen":"'.$conn->datos_prod['imagen'].'",
			"precio_provedor":"'.$conn->datos_prod['precio_provedor'].'",
			"precio_venta":"'.$conn->datos_prod['precio_venta'].'",
			"precio_mayoreo":"'.$conn->datos_prod['precio_mayoreo'].'",
			"cantidad":"'.$conn->datos_prod['cantidad'].'",
			"activar_cantidades":"'.$activar_cantidades.'",
			"minimo":"'.$conn->datos_prod['minimo'].'"}';			
	}
	if($existe == "no_existe"){
	  echo '{"status":"no_existe"}';
	}	
	if($existe == "error_sql"){
	  echo '{"status":"error_sql"}';
	}				
}
*/
//  CASO DE EDITAR CANTIDAD EN EXISTENCIA DESDE USARIO PASTELERO
if(array_key_exists("accion", $_POST) && $_POST['accion']=='pastelero_buscar_producto'){		
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$codigo = $_POST['codigo'];
	$id_sucursal = $_POST['id_sucursal'];
	$existe = $conn->comprobar_disponibilidad_producto($codigo, $_SESSION['g_id_empresa'], $id_sucursal);	
	if($existe == "existe"){
	  echo '{
		  	"status":"existe",
		  	"id":"'.$conn->datos_prod['id'].'",
			"id_empresa":"'.$conn->datos_prod['id_empresa'].'",
			"id_admin":"'.$conn->datos_prod['id_admin'].'",
			"id_sucursal":"'.$conn->datos_prod['id_sucursal'].'",
			"id_unidad":"'.$conn->datos_prod['id_unidad'].'",
			"id_seccion":"'.$conn->datos_prod['id_seccion'].'",
			"id_proveedor":"'.$conn->datos_prod['id_proveedor'].'",
			"codigo":"'.$conn->datos_prod['codigo'].'",
			"codigo_unidades":"'.$conn->datos_prod['codigo_unidades'].'",
			"nombre":"'.$conn->datos_prod['nombre'].'",
			"imagen":"'.$conn->datos_prod['imagen'].'",
			"precio_provedor":"'.$conn->datos_prod['precio_provedor'].'",
			"precio_venta":"'.$conn->datos_prod['precio_venta'].'",
			"precio_mayoreo":"'.$conn->datos_prod['precio_mayoreo'].'",
			"cantidad":"'.$conn->datos_prod['cantidad'].'",
			"minimo":"'.$conn->datos_prod['minimo'].'"}';			
	}
	if($existe == "no_existe"){
	  echo '{"status":"no_existe"}';
	}	
	if($existe == "error_sql"){
	  echo '{"status":"error_sql"}';
	}				
}
//  Buscar un producto para convertirlo a unidades
if(array_key_exists("accion", $_POST) && $_POST['accion']=='producto_a_unidades_buscar'){		
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$codigo = $_POST['codigo'];
	$id_sucursal = $_POST['id_sucursal'];
	$existe = $conn->producto_a_unidades_buscar($codigo, $_SESSION['g_id_empresa'], $id_sucursal);	
	if($existe == "existe"){
	  echo '{
		  	"status":"existe",
		  	"id":"'.$conn->datos_prod['id'].'",
			"id_empresa":"'.$conn->datos_prod['id_empresa'].'",
			"id_admin":"'.$conn->datos_prod['id_admin'].'",
			"id_sucursal":"'.$conn->datos_prod['id_sucursal'].'",
			"id_unidad":"'.$conn->datos_prod['id_unidad'].'",
			"id_seccion":"'.$conn->datos_prod['id_seccion'].'",
			"id_proveedor":"'.$conn->datos_prod['id_proveedor'].'",
			"codigo":"'.$conn->datos_prod['codigo'].'",			
			"nombre":"'.$conn->datos_prod['nombre'].'",
				"id_codigo_unidades":"'.$conn->datos_prod['id_codigo_unidades'].'",
				"codigo_unidades":"'.$conn->datos_prod['codigo_unidades'].'",
				"nombre_codigo_unidades":"'.$conn->datos_prod['nombre_codigo_unidades'].'",
				"cantidad_codigo_unidades":"'.$conn->datos_prod['cantidad_codigo_unidades'].'",
			"imagen":"'.$conn->datos_prod['imagen'].'",
			"precio_provedor":"'.$conn->datos_prod['precio_provedor'].'",
			"precio_venta":"'.$conn->datos_prod['precio_venta'].'",
			"precio_mayoreo":"'.$conn->datos_prod['precio_mayoreo'].'",
			"cantidad":"'.$conn->datos_prod['cantidad'].'",
			"minimo":"'.$conn->datos_prod['minimo'].'"}';			
	}
	if($existe == "no_existe"){
	  echo '{"status":"no_existe"}';
	}
	if($existe == "codigo_unidades_no_disponible"){
	  echo '{"status":"codigo_unidades_no_disponible",
	   		 "codigo_unidades":"'.$conn->datos_prod['codigo_unidades'].'",
			 "nombre":"'.$conn->datos_prod['nombre'].'"  
			}';
	}	
	//if($existe == "codigo_unidades_no_existe")
	//  echo '{"status":"codigo_unidades_no_existe"}';
		
	if($existe == "error_sql"){
	  echo '{"status":"error_sql"}';
	}				
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='producto_a_unidades_update'){		
	$conn = new class_mysqli();
	$id_codigo_unidades = $_POST['id_codigo_unidades'];
	$id_prod = $_POST['id_prod'];
	$cantidad = $_POST['cantidad'];
	$nombre_prod_1 = $_POST['nombre_prod_1'];
	$nombre_prod_2 = $_POST['nombre_prod_2'];
	$conn->conn_mysqli->autocommit(false);
	$sql1 = "UPDATE tbl_producto SET cantidad = (cantidad + $cantidad) WHERE id = $id_codigo_unidades";
	$sql2 = "UPDATE tbl_producto SET cantidad = (cantidad - 1) WHERE id = $id_prod";
	$update1 = $conn->conn_mysqli->query($sql1);
	$update2 = $conn->conn_mysqli->query($sql2);
	if($update1 && $update2){			
		$conn->conn_mysqli->commit();
		$conn->conn_mysqli->autocommit(true);
		$movimiento = "Conversion a unidades del producto: $nombre_prod_1 a $nombre_prod_2. Usuario:".$_SESSION['g_nombre'];
		$conn->nuevo_log($_SESSION['g_id_empresa'],$_SESSION['g_id_sucursal'],$_SESSION['g_NumEmp'],$movimiento);
		echo '{"status":"ok_update"}';
	}else{
		$conn->conn_mysqli->rollback();
		$conn->conn_mysqli->autocommit(true);
		echo '{"status":"no_update"}';		//'.$this->conn_mysqli->error.'
		
	}
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='update_productos'){	
	$conn = new class_mysqli();
	//$_POST = $conn->sanitize($_POST);
	$array_ids = explode(",",$_POST['array_id']); 
	$array_cantidad = explode(",",$_POST['array_cantidad']);
	$array_ganancia_prod = $_POST['array_ganancia_prod'];
	
	//$nombre_genericos = $_POST['nombre_genericos'];	
	//$nombre_genericos = (!get_magic_quotes_gpc()) ? $_POST['nombre_genericos']  : stripslashes($_POST['nombre_genericos']);
	$_POST['nombre_genericos']= (!get_magic_quotes_gpc()) ? $_POST['nombre_genericos']  : stripslashes($_POST['nombre_genericos']);
	//exit;
	$ganancia_total = $_POST['ganancia_total'];
	$total = $_POST['total'];
	$card = $_POST['card'];
	$aprobacion_card = $_POST['aprobacion_card'];

	$ids = $_POST['array_id']; 

	// Descontar cantidades si esta activada esta opcion de lo contrario solo registra la venta
	$activar_cantidades_error = false;
	if($_SESSION['activar_cantidades']){ 
		$sql = "UPDATE tbl_producto SET cantidad = CASE id "; 
		foreach ($array_ids as $key => $id) {     
			$sql .= sprintf("WHEN %d THEN %d ", $id, $array_cantidad[$key]); 
		} 
		$sql .= "END WHERE id IN ($ids)";   
		
		$conn->conn_mysqli->autocommit(FALSE);
		if($result = $conn->conn_mysqli->query($sql)){
			// echo '{"status":"ok_update"}';
			$conn->conn_mysqli->commit();
		}else{
			//echo '{"status":"no_update"}';
			$activar_cantidades_error = true;
			$conn->conn_mysqli->rollback();
		}
		$conn->conn_mysqli->autocommit(TRUE);
	}
	if($activar_cantidades_error){
		echo '{"status":"error"}';
		return;
	}

	$array_id = $_POST['array_id'];
	$array_cantidad_solicitada = $_POST['array_cantidad_solicitada'];
	$array_precio_prod = $_POST['array_precio_prod'];
	//print_r($_POST);insert_venta_caja($id_empresa, $id_sucursal, $sucursal, 															$NumEmp, $nombre_empleado, $id_productos, $cantidades, 			   $array_precio_prod, $ganancias, $total, $ganancia_total, $card, $aprobacion_card, $nombre_genericos){
	echo $resul = $conn->insert_venta_caja($_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal'], $_SESSION['g_sucursal'], $_SESSION['g_NumEmp'], $_SESSION['g_nombre'], $array_id, $array_cantidad_solicitada, $array_precio_prod, $array_ganancia_prod, $total, $ganancia_total, $card, $aprobacion_card, $_POST['nombre_genericos']);
	
}

if(array_key_exists("accion", $_POST) && $_POST['accion']=='prod_devolucion'){	
	$conn = new class_mysqli();
	//print_r($_POST);  
	$arrayDevo = json_decode($_POST['arrayDevo']); 
 
	//print_r($arrayDevo); 
	//$_POST = $conn->sanitize($_POST);
	$motivo = $_POST['motivo'];

	$array_ids_devo = [];
	$array_catidad_devo = [];
	$array_ids = $array_cantidad_solicitada = $array_precio_prod = $array_ganancia_prod = [];
	foreach ($arrayDevo as $key1 => $obj1) { 
		//print_r($arrayDevo[$key1]); 
		//$json = json_encode($obj1);
		//echo $obj1->precio; 
		//echo "<br>id:".$obj1->id_producto; 
		if( $obj1->devolucion) {
			array_push($array_ids_devo, $obj1->id_producto);
			array_push($array_catidad_devo, $obj1->cantidad);
			$id_producto = $obj1->id_producto;
			$resul = $conn->insert_devolucion(
				$_SESSION['g_id_empresa'], 
				$_SESSION['g_id_sucursal'], 
				$_SESSION['g_sucursal'], 
				$_SESSION['g_NumEmp'], 
				$_SESSION['g_nombre'], 
				$id_producto, 
				$motivo);
		}
		if( $obj1->id_producto) {
			array_push($array_ids, $obj1->id_producto);
			array_push($array_cantidad_solicitada, $obj1->cantidad);
			array_push($array_precio_prod, '$'.number_format($obj1->precio,2));
			array_push($array_ganancia_prod, $obj1->ganancia);
		}else{
			$total = $obj1->precioFinal;
			$total_ganancia = $obj1->gananciaFinal;
		}
	}
 
	$array_ids = implode(',',$array_ids);
	$array_cantidad_solicitada = implode(',',$array_cantidad_solicitada);
	$array_precio_prod = implode(',',$array_precio_prod);
	$array_ganancia_prod = implode(',',$array_ganancia_prod);
/* 	$array_cantidad = explode(",",$_POST['array_cantidad']);
	$array_ganancia_prod = $_POST['array_ganancia_prod'];	 */
/* 	print_r($array_ids); 
	exit; */

	if($_SESSION['activar_cantidades']){
		if(count($array_ids_devo)){  
			$ids = implode(',', $array_ids_devo);
			$sql = "UPDATE tbl_producto SET cantidad = CASE id "; 
			foreach ($array_ids_devo as $key => $id) {     
				$sql .= sprintf("WHEN %d THEN %d ", $id, cantidad + $array_catidad_devo[$key]); 
			} 
			$sql .= "END WHERE id IN ($ids)";   
			
			$conn->conn_mysqli->autocommit(FALSE);
			if($result = $conn->conn_mysqli->query($sql)){
				//echo '{"status":"ok_update"}';
				$conn->conn_mysqli->commit();
			}else{
				echo '{"status":"no_update"}';				
				$conn->conn_mysqli->rollback();
				$conn->conn_mysqli->autocommit(TRUE);
				exit;
			}
			$conn->conn_mysqli->autocommit(TRUE);
		}
	}

	
	echo $resul = $conn->edit_venta_caja(
		$_POST['id_ventas_cajas'], 
		$array_ids, 
		$array_cantidad_solicitada, 
		$array_precio_prod, 
		$array_ganancia_prod, 
		$total, 
		$total_ganancia);
}

if(array_key_exists("accion", $_POST) && $_POST['accion']=='salida_dinero'){	
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$comentario_retiro = $_POST['comentario_retiro'];
	$catidad_retiro = $_POST['catidad_retiro']; 
	$concepto = $_POST['concepto']; 
	echo $resul = $conn->insert_retiro_ingreso_caja($_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal'], $_SESSION['g_sucursal'], $_SESSION['g_NumEmp'], $_SESSION['g_nombre'], $concepto, $catidad_retiro,$comentario_retiro);
}

if(array_key_exists("accion", $_POST) && $_POST['accion']=='debe_usuario'){	
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$comentario_deben = $_POST['comentario_deben'];
	$catidad_deben = $_POST['catidad_deben']; 
	$nombre_deben = $_POST['nombre_deben']; 
	echo $resul = $conn->insert_usr_debe($_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal'], $_SESSION['g_sucursal'], $_SESSION['g_NumEmp'], $_SESSION['g_nombre'], $nombre_deben, $catidad_deben, $comentario_deben);
}


if(array_key_exists("accion", $_POST) && $_POST['accion']=='debenLista'){	
	$conn = new class_mysqli();
	$estatus = $_POST['estatus'];

	if($_POST['fecha'])
		$fecha = $_POST['fecha'];
	else
		$fecha = date("Y-m-d");

	if($_POST['fecha2'])
		$fecha2 = $_POST['fecha2'];
	else
		$fecha2 = date("Y-m-d");
	//DATE_FORMAT(fecha,'%Y-%m-%d')  BETWEEN '$fecha' AND '$fecha2' 
		$sql = "SELECT *, DATE_FORMAT(fecha, '%m') AS mes1,
				DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,
				DATE_FORMAT(fecha, '%h:%i %p') AS hrs1
				FROM `tbl_deben` WHERE estatus = '$estatus' AND id_empresa = ".$_SESSION['g_id_empresa']." ORDER BY fecha DESC";

	//echo "$sql <hr>";		// AND id_productos IN (1236)
	
	if($result = $conn->conn_mysqli->query($sql)){
		if($result->num_rows){	
			$tbl_completa = "";
			$cont = 0;
			while ($row = $result->fetch_assoc()) {
				$cont++;

				if( $estatus == 'debe' ){
					$btn_pagar = '
					<div class="button_caja" id="btn_par_deben" onclick="actualiza_deben('.$row["id"].', \'pagado\')" style="width:60px; height:25px; font-size:12px">
						<div style="padding-top:7px; width:60px;">Cobrar</div>
					</div> ';
				}else{
					$btn_pagar = '';
				}
								
				$row["cantidad"] = number_format($row["cantidad"],2);
				$row["mes1"] = $conn->damemes($row["mes1"]);
				$row["fecha"] = '<span class="t_italic">'.$row["dia1"].' de '.$row["mes1"].' del '.$row["anio1"].' '.$row["hrs1"].'</span>';

				if($cont%2)
					$clase = ' f_verde2_ventas ';
				else
					$clase = ' f_verde1_ventas ';

				$tabla_1 = '<table id="debe_'.$row["id"].'" 
				border="0" width="100%" cellspacing="0" class="'.$clase.' btop2 bbottom2 bright2 bleft2">
				<tr class=" t_negro">
					<td width="90"><span class="negritas t_negro">Fecha:</span></td>
					<td width="70%"><span class=" t_negro">'.$row["fecha"].'</span></td>
					
					<td rowspan="2" align="right" width="90"><p>Cantidad</p></td>

					<td rowspan="2" align="right"><span class="negritas t_rojo">$'.$row["cantidad"].'</span></td>
				</tr>
				<tr class="t_negro">
					<td width="90"><span class="negritas t_negro">Nombre:</span></td>
					<td width="70%"><span class=" t_negro">'.$row["nombre"].'</span></td>
				</tr>
				<tr class="t_negro">
					<td width="90"><span class="negritas t_negro">Nota:</span></td>
					<td width="70%" colspan="2"><span class=" t_negro">'.$row["nota"].'</span></td>
					<td width="90" align="right"> 
						'.$btn_pagar.'
					</td>
				</tr>';
				
			
				$tbl_completa .= $tabla_1."<br>";	
			}
			echo $tbl_completa;
		}else{
			echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
		}
	}
}

if(array_key_exists("accion", $_POST) && $_POST['accion']=='debenUpdate'){	
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$id = $_POST['id']; 
	//echo "OK: $id";
	echo $conn->debenUpdate($id);

}
/************************************************************************************************************************************/
/***********************************************    REPORTE DE VENTAS   *************************************************************/
/************************************************************************************************************************************/
if(array_key_exists("accion", $_POST) && $_POST['accion']=='ventas_usuario'){	
	$conn = new class_mysqli();
	$id = $_POST['id'];
	if($_POST['fecha'])
		$fecha = $_POST['fecha'];
	else
		$fecha = date("Y-m-d");

	if($_POST['fecha2'])
		$fecha2 = $_POST['fecha2'];
	else
		$fecha2 = date("Y-m-d");
		
				
	//$sql = "SELECT * FROM `tbl_ventas_caja` WHERE DATE_FORMAT(fecha,'%d')=DAY(NOW()) AND NumEmp = ".$_SESSION['g_NumEmp']." ORDER BY fecha DESC";
		$sql = "SELECT *, DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs1 
				FROM `tbl_ventas_caja` WHERE DATE_FORMAT(fecha,'%Y-%m-%d') BETWEEN '$fecha' AND '$fecha2' AND NumEmp = ".$_SESSION['g_NumEmp']." ORDER BY fecha DESC";
	
	if($_SESSION['g_nivel'] == "admin")
		$sql = "SELECT *, DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs1
			    FROM `tbl_ventas_caja` WHERE DATE_FORMAT(fecha,'%Y-%m-%d')  BETWEEN '$fecha' AND '$fecha2' AND id_empresa = ".$_SESSION['g_id_empresa']." ORDER BY fecha DESC";
	// Si la busqueda es por Usuario
	if($id)
		$sql = "SELECT *, DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs1 
				FROM `tbl_ventas_caja` WHERE DATE_FORMAT(fecha,'%Y-%m-%d')  BETWEEN '$fecha' AND '$fecha2' AND NumEmp = $id AND id_empresa = ".$_SESSION['g_id_empresa']." ORDER BY fecha DESC";
	
	//echo "$sql <hr>";		// AND id_productos IN (1236)
	$tabla_xls = '';
	if($result = $conn->conn_mysqli->query($sql)){
		if($result->num_rows){	
			$tbl_completa = ""; 
			$tabla_xls .= '
			<table border="1" id="tbl_histo" class="hide" >
			<tr>
				<th bgcolor="#539ae6" >Fecha</th>
				<th bgcolor="#539ae6" >Empleado</th>
				<th bgcolor="#539ae6" >Tipo de Pago</th>
				<th bgcolor="#539ae6" >Folio</th>
				<th bgcolor="#539ae6" >Producto</th>
				<th bgcolor="#539ae6" >Cantidad</th>
				<th bgcolor="#539ae6" >Precio Unitario</th>
				<th bgcolor="#539ae6" >Ganancia</th>
				<th bgcolor="#539ae6" >Precio Total</th>
			</tr>';
			$con = 0;
			while ($row = $result->fetch_assoc()) {
				if( $row["id_productos"] == '')
					continue;
					
				if($con%2)
					$trColor = ' bgcolor="#e1e7eb" ';
				else
					$trColor = ' bgcolor="#deffa1" ';
				$con++;

				$row["mes1"] = $conn->damemes($row["mes1"]);
				$row["fecha"] = '<span class="t_italic">'.$row["dia1"].' de '.$row["mes1"].' del '.$row["anio1"].' '.$row["hrs1"].'</span>';				
				$array_id_prod = explode(",",$row["id_productos"]);
				$cantidades = explode(",",$row["cantidades"]);
				$precios = explode(",",$row["precios"]);
				$nombre_genericos = $row["nombre_genericos"];
				$folio = sprintf("%07s",  $row["id_ventas_cajas"]);
				$total_x_dia = $total_x_dia+$row["total"];
				$ganancias = explode(",",$row["ganancias"]);
				$total_ganancia_x_dia = $total_ganancia_x_dia+$row["total_ganancia"];
				$card = $row["card"];

				// Datos Para el Ticket
				$array_id = implode(",",$array_id_prod);
				$array_cantidad_solicitada = implode(",",$cantidades);
				$array_precio_prod = implode(",",$precios);
				$urlTicket="ticket_html.php?folio=".$folio."&array_id=".$array_id."&array_cantidad_solicitada=".$array_cantidad_solicitada.
				"&array_precio_prod=".$array_precio_prod."&total=".$total_x_dia."&nombre_cajero=".$row["nombre_empleado"];

				if($card != "")
					$tipo_pago = 'Pago con Tarjeta  
									<img src="images/t_'.$card.'.png" height="22" width="28" style="position: relative; margin-top:0px">
								   , 
								  aprobacion: <span class="negritas t_negro">'.$row["aprobacion_card"].'</span>';
				else
					$tipo_pago = '<span class="negritas t_negro">Pago en Efectivo</span>';	
				

				$tabla_1 = '
						  <table border="0" class="f_cabecera_list_ventas btop2  bright2 bleft2"  width="100%">
						  <tr>
							<td width="90"><span class="negritas t_negro">Fecha:</span></td>
							<td><span class=" t_negro">'.$row["fecha"].'</span></td>
							<td align="right"><strong>Folio:</strong> <span class="negritas t_azul_fuerte f_blanco" style="padding:0 5px 0 5px;">'.$folio.'</span></td>
						  </tr>
						  <tr>
						  	<td><span class="negritas t_negro">Empleado:</span></td>
							<td><span class=" t_negro">'.$row["nombre_empleado"].'</span></td>
							<td align="right">'.$tipo_pago.'</td>
						  </tr>
						  </table>';
				$tabla_2 = '<table id="venta_'.$row["id_ventas_cajas"].'"
								border="0" width="100%" cellspacing="0" class="bbottom2 bright2 bleft2">
								<tr class="f_sub_cabecera_list_ventas t_negro">
									<th width="80%">Producto</th>
									<th width="20">Cant.</th>
									<th width="30">Ganancia</th>
									<th width="30" align="right">Precio</th>
								</tr>';
				//print_r($precios);					  
				foreach($array_id_prod as $key => $valor){
					// si los id_productos contienes claves genericas (-n) se buscaran los nombres 
					// en el JSON del campo nombre_genericos 
					if(stripos($valor, "-")){						
						$id_generico = explode("-", $valor);
						$valor = $id_generico[1];
						
						$array_JSON = json_decode($nombre_genericos);
						//print_r($array_JSON);
						foreach ($array_JSON as $key1 => $obj1) { 
							//echo  " <br> $valor | $key ".$obj1->nom;
							if ($valor == $obj1->id) {	// elimina elemento		 	 
								$row_prod['nombre'] = "<span class='f_blanco t_rojo negritas'> &raquo;</span>Generico - ".$obj1->nom;
								$row_prod['id'] = '1433-'.$valor;
							}
						}							 				
					}else{
						$sql = "SELECT id,nombre FROM `tbl_producto` WHERE id = ".$valor; 
						$result_prod = $conn->conn_mysqli->query($sql);
						$row_prod = $result_prod->fetch_assoc();
					}
					//echo "<br>Nom::".$row_prod['nombre'];
					if($key%2)
						$clase = 'class="f_verde2_ventas f_resalta_verde "';
					else
						$clase = 'class="f_verde1_ventas f_resalta_verde "';

					$precio = str_replace('$','',$precios[$key]);
					$precioUni = $precio / $cantidades[$key];

					if($_SESSION['g_nivel'] == "vendedor"){
						$row["total_ganancia"]=0;
						$ganancias = 0;
					}

					$tabla_2 .= '<tr '.$clase.'  >
									<td>'.$row_prod['nombre'].'</td>
									<td align="center">
										<input type="number" class="td_producto"
											cantOrg='.$cantidades[$key].'
											precio='.$precio.'
											ganancia='.$ganancias[$key].'
											id_producto='.$row_prod['id'].'
											prod_name="'.$row_prod['nombre'].'"
											max='.$cantidades[$key].' 
											min=0 value="'.$cantidades[$key].'">
									</td>
									<td align="center">$'.$ganancias[$key].'</td>
									<td align="right">'.$precios[$key].'</td>
							    </tr>';
					
					
					$tabla_xls .= '
						<tr>
							<td '.$trColor.'>'.$row["fecha"].'</td>
							<td '.$trColor.'>'.$row["nombre_empleado"].'</td>
							<td '.$trColor.'>'.$tipo_pago.'</td>
							<td '.$trColor.'>'.$folio.'</td>
							<td '.$trColor.'>'.$row_prod['nombre'].'</td>
							<td '.$trColor.'>'.$cantidades[$key].'</td>
							<td '.$trColor.'>'.$precioUni.'</td>
							<td '.$trColor.'>'.$ganancias[$key].'</td>
							<td '.$trColor.'>'.$precios[$key].'</td>
						</tr>';
				}
				
											
				$row["total"] = number_format($row["total"],2);
				$row["total_ganancia"] = number_format($row["total_ganancia"],2);
				/*
				$tabla_3 = '<table border="1" class="f_sub_cabecera_list_ventas bright bleft bbottom font_16"  width="250" style="position:relative; float:right">
					  <tr>
					    <td align="right">Total: <strong><span class="negritas t_rojo">$'.$row["total_ganancia"].'</span></strong></td>
					   	<td align="right">Total: <strong><span class="negritas t_rojo">$'.$row["total"].'</span></strong></td>
					  </tr>
					  </table><br>';
				*/
				$tabla_2 .= '
					  <tr class="f_cabecera_list_ventas">
					  	<td align="right">
						  	<a href="'.$urlTicket.'" target="_blank"><div class="t_azul_fuerte">Ticket</div></a>
						</td>
					  	<td onclick="actualiza_devo_confirm('.$row["id_ventas_cajas"].')" class="hand t_verde_fuerte">Actualizar</td>
					    <td align="center"> <strong><span class="negritas t_rojo">$'.$row["total_ganancia"].'</span></strong></td>
					   	<td align="right"> <strong><span class="negritas t_rojo">$'.$row["total"].'</span></strong></td>
					  </tr>
					  ';					  
				$tabla_2 .= '</table>';	  				
				$tbl_completa .= $tabla_1.$tabla_2.$tabla_3."<br>";	
			}
				$total_x_dia = number_format($total_x_dia,2);
				echo '<table border="0" class="bright bleft btop font_22 f_amarillo t_rojo"  width="430" style="position:relative; float:right;">
					  <tr>
					  	<td align="right"><a onclick="excel()" class="hand">Excel</a></td>
					    <td align="right">Total: <strong>$'.$total_ganancia_x_dia.'</strong></td>
					   	<td align="right">Total: <strong>$'.$total_x_dia.'</strong></td>
					  </tr>
					  </table><br>
					  '; //<a onclick="excel()">excel</a>
				echo $tbl_completa.$tabla_xls;	// .$tabla_xls
		}else{
			echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
		}
	}
}
/************************************************************************************************************************************/
/***********************************************    REPORTE DE VENTAS X PRODUCTO  *************************************************************/
/************************************************************************************************************************************/
if(array_key_exists("accion", $_POST) && $_POST['accion']=='ventas_usuario_x_prod'){	
	$conn = new class_mysqli();
	$id = $_POST['id'];
	$id_producto = $_POST['id_producto'];
	if($_POST['fecha'])
		$fecha = $_POST['fecha'];
	else
		$fecha = date("Y-m-d");

	if($_POST['fecha2'])
		$fecha2 = $_POST['fecha2'];
	else
		$fecha2 = date("Y-m-d");
		
				
	//$sql = "SELECT * FROM `tbl_ventas_caja` WHERE DATE_FORMAT(fecha,'%d')=DAY(NOW()) AND NumEmp = ".$_SESSION['g_NumEmp']." ORDER BY fecha DESC";
		$sql = "SELECT *, DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs1 
				FROM `tbl_ventas_caja` WHERE DATE_FORMAT(fecha,'%Y-%m-%d') BETWEEN '$fecha' AND '$fecha2' AND NumEmp = ".$_SESSION['g_NumEmp']." ORDER BY fecha DESC";
	
	if($_SESSION['g_nivel'] == "admin")
		$sql = "SELECT *, DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs1
			    FROM `tbl_ventas_caja` WHERE DATE_FORMAT(fecha,'%Y-%m-%d')  BETWEEN '$fecha' AND '$fecha2' AND id_empresa = ".$_SESSION['g_id_empresa']." ORDER BY fecha DESC";
	// Si la busqueda es por Usuario
	if($id)
		$sql = "SELECT *, DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs1 
				FROM `tbl_ventas_caja` WHERE DATE_FORMAT(fecha,'%Y-%m-%d')  BETWEEN '$fecha' AND '$fecha2' AND NumEmp = $id AND id_empresa = ".$_SESSION['g_id_empresa']." ORDER BY fecha DESC";
	
	 // echo "$sql <hr>";		// AND id_productos IN (1236)
	
	if($result = $conn->conn_mysqli->query($sql)){
		if($result->num_rows){	
			$tbl_completa = "";
			while ($row = $result->fetch_assoc()) {
				$row["mes1"] = $conn->damemes($row["mes1"]);
				$row["fecha"] = '<span class="t_italic">'.$row["dia1"].' de '.$row["mes1"].' del '.$row["anio1"].' '.$row["hrs1"].'</span>';				
				$array_id_prod = explode(",",$row["id_productos"]);
				
				//echo "array_id_prod <pre>"; print_r($array_id_prod); echo "</pre>";
				$key_array_id_prod = array_search($id_producto, $array_id_prod); // buscamos la key donde se encuentra el id del producto
				
				if(!is_numeric($key_array_id_prod))
					continue;

				unset($array_id_prod); // eliminamos el array
				$array_id_prod = array();	// declaramos nuevamente el array
				array_push ($array_id_prod, $id_producto); // agregamos el id del producto al array como unico elemento de este
				//echo "<br>a1 <pre>"; print_r($array_id_prod); echo "</pre>";

				$cantidades = explode(",",$row["cantidades"]);
					$tmp_cantidades = $cantidades[$key_array_id_prod];
					unset($cantidades);
					$cantidades = array($tmp_cantidades);
					//echo "a2 <pre>"; print_r($cantidades); echo "</pre>";


				$precios = explode(",",$row["precios"]);
					$tmp_precios = $precios[$key_array_id_prod];
					unset($precios); 
					$precios = array($tmp_precios);
					// echo "<br>precios<pre>"; print_r($precios); echo "</pre>";


			
				$nombre_genericos = $row["nombre_genericos"];
				$folio = sprintf("%07s",  $row["id_ventas_cajas"]);				
				$precio_int = str_replace("$","",$tmp_precios);	 // eliminamos el $ de la cadena para convertirse en int y poder sumarse
				$total_x_dia = $total_x_dia+$precio_int;
				$ganancias = explode(",",$row["ganancias"]);				
					$tmp_ganancias = $ganancias[$key_array_id_prod];
					unset($ganancias);
					$ganancias = array($tmp_ganancias);
				//echo "<br>GANACIAS<pre>"; print_r($ganancias); echo "</pre>";


				$total_ganancia_x_dia = $total_ganancia_x_dia+$tmp_ganancias;
				$card = $row["card"];
				if($card != "")
					$tipo_pago = 'Pago con Tarjeta  
									<img src="images/t_'.$card.'.png" height="22" width="28" style="position: relative; margin-top:0px">
								   , 
								  aprobacion: <span class="negritas t_negro">'.$row["aprobacion_card"].'</span>';
				else
					$tipo_pago = '<span class="negritas t_negro">Pago en Efectivo</span>';	
				$tabla_1 = '
						  <table border="0" class="f_cabecera_list_ventas btop2  bright2 bleft2"  width="100%">
						  <tr>
							<td width="90"><span class="negritas t_negro">Fecha:</span></td>
							<td><span class=" t_negro">'.$row["fecha"].'</span></td>
							<td align="right"><strong>Folio:</strong> <span class="negritas t_azul_fuerte f_blanco" style="padding:0 5px 0 5px;">'.$folio.'</span></td>
						  </tr>
						  <tr>
						  	<td><span class="negritas t_negro">Empleado:</span></td>
							<td><span class=" t_negro">'.$row["nombre_empleado"].'</span></td>
							<td align="right">'.$tipo_pago.'</td>
						  </tr>
						  </table>';
				$tabla_2 = '<table border="0" width="100%" cellspacing="0" class="bbottom2 bright2 bleft2">
								<tr class="f_sub_cabecera_list_ventas t_negro">
									<th width="80%">Producto</th>
									<th width="20">Cant.</th>
									<th width="30">Ganancia</th>
									<th width="30" align="right">Precio</th>
								</tr>';
				//print_r($precios);	
				// echo "array_id_prod<pre>"; print_r($array_id_prod); echo "</pre>";				  
				foreach($array_id_prod as $key => $valor){
					// si los id_productos contienes claves genericas (-n) se buscaran los nombres 
					// en el JSON del campo nombre_genericos 
					if(stripos($valor, "-")){						
						$id_generico = explode("-", $valor);
						$valor = $id_generico[1];
						
						$array_JSON = json_decode($nombre_genericos);
						//print_r($array_JSON);
						foreach ($array_JSON as $key1 => $obj1) { 
							//echo  " <br> $valor | $key ".$obj1->nom;
							if ($valor == $obj1->id) {	// elimina elemento		 	 
								$row_prod['nombre'] = "<span class='f_blanco t_rojo negritas'> &raquo;</span>Generico - ".$obj1->nom;
							}
						}							 				
					}else{
						$sql = "SELECT id, nombre FROM `tbl_producto` WHERE id = ".$valor; 
						$result_prod = $conn->conn_mysqli->query($sql);
						$row_prod = $result_prod->fetch_assoc();
					}
					//echo "<br>".$row_prod['nombre'];
					if($key%2)
						$clase = 'class="f_verde2_ventas f_resalta_verde"';
					else
						$clase = 'class="f_verde1_ventas f_resalta_verde"';	
					$tabla_2 .= '<tr '.$clase.'>
									<td>'.$row_prod['nombre'].'</td>
									<td align="center">'.$cantidades[$key].'</td>
									<td align="center">$'.$ganancias[$key].'</td>
									<td align="right">'.$precios[$key].'</td>
							     </tr>';
				}
				
									 	
				$row["total"] = number_format($precios[0],2);
				$row["total_ganancia"] = number_format($ganancias[0],2);
				/*
				$tabla_3 = '<table border="1" class="f_sub_cabecera_list_ventas bright bleft bbottom font_16"  width="250" style="position:relative; float:right">
					  <tr>
					    <td align="right">Total: <strong><span class="negritas t_rojo">$'.$row["total_ganancia"].'</span></strong></td>
					   	<td align="right">Total: <strong><span class="negritas t_rojo">$'.$row["total"].'</span></strong></td>
					  </tr>
					  </table><br>';
				*/
				$tabla_2 .= '
					  <tr class="f_cabecera_list_ventas">
					  	<td></td>
					  	<td></td>
					    <td align="center"> <strong><span class="negritas t_rojo">$'.$row["total_ganancia"].'</span></strong></td>
					   	<td align="right"> <strong><span class="negritas t_rojo">'.$precios[0].'</span></strong></td>
					  </tr>
					  ';					  
				$tabla_2 .= '</table>';	  				
				$tbl_completa .= $tabla_1.$tabla_2.$tabla_3."<br>";	
			}

				$total_x_dia = number_format($total_x_dia,2);
				echo '<table border="0" class="bright bleft btop font_22 f_amarillo t_rojo"  width="430" style="position:relative; float:right;">
					  <tr>
					    <td align="right">Total: <strong>$'.$total_ganancia_x_dia.'</strong></td>
					   	<td align="right">Total: <strong>$'.$total_x_dia.'</strong></td>
					  </tr>
					  </table><br>';
				echo $tbl_completa;			
		}else{
			echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
		}
	}
}

if(array_key_exists("accion", $_POST) && $_POST['accion']=='pagos_usuario'){	
	$conn = new class_mysqli();
	$id = $_POST['id'];
	if($_POST['fecha'])
		$fecha = $_POST['fecha'];
	else
		$fecha = date("Y-m-d");

	if($_POST['fecha2'])
		$fecha2 = $_POST['fecha2'];
	else
		$fecha2 = date("Y-m-d");
		
	$tipo = $_POST['estatus'];
	
	
	// busqueda desde caja.js
	$sql = "SELECT *, DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs1 
			FROM `tbl_salida_entrada_caja` WHERE DATE_FORMAT(fecha,'%Y-%m-%d') BETWEEN '$fecha' AND '$fecha2' AND NumEmp = ".$_SESSION['g_NumEmp']." ORDER BY fecha DESC";
	
	// Si la busqueda es por Usuario en reporte ventas
	if($id)
		$sql = "SELECT *, DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs1 
				FROM `tbl_salida_entrada_caja` WHERE DATE_FORMAT(fecha,'%Y-%m-%d') BETWEEN '$fecha' AND '$fecha2' AND NumEmp = $id AND id_empresa = ".$_SESSION['g_id_empresa']." ORDER BY fecha DESC";
	
	if($result = $conn->conn_mysqli->query($sql)){
		if($result->num_rows){	
			$tbl_completa = "";
			while ($row = $result->fetch_assoc()) {
				$folio = sprintf("%07s",  $row["id_salida_entrada_caja"]);
				$total_x_dia = $total_x_dia+$row["total"];
				$row['motivo'] = str_ireplace('##br##',"<br>",$row['motivo']);
				$row["total"] = number_format($row["total"],2);
				$row["mes1"] = $conn->damemes($row["mes1"]);
				$row["fecha"] = $row["dia1"].'<strong> / </strong>'.$row["mes1"].'<strong> / </strong>'.$row["anio1"].' '.$row["hrs1"];					
				$tabla_1 = '
						  <table border="0" class="f_cabecera_list_ventas btop bbottom bright bleft"  width="100%">
						  <tr>
							<td width="90"><span class="negritas t_negro">Fecha:</span></td>
							<td><span class=" t_negro">'.$row["fecha"].'</span></td>
							<td width="100" rowspan="2" align="center" class="negritas t_negro">'.$row['concepto'].'</td>
							<td width="120" align="right"><strong>Folio:</strong> <span class="negritas t_negro f_blanco" style="padding:0 5px 0 5px;">'.$folio.'</span></td>
						  </tr>
						  <tr>
						  	<td><span class="negritas t_negro">Empleado:</span></td>
							<td><span class=" t_negro">'.$row["nombre_empleado"].'</span></td>
							<td align="right"><span class="negritas t_negro">$'.$row["total"].'</span></td>
						  </tr>
						  </table>';
				$tabla_2 = '<table border="0" width="100%" cellspacing="0" class="bbottom bright bleft">';		  
					$tabla_2 .= '<tr '.$clase.'>
									<td><span class="negritas t_negro">Descripcion:</span>
										'.$row['motivo'].'
									</td>
							     </tr>';
				$tabla_2 .= '</table>';
				$tbl_completa .= $tabla_1.$tabla_2."<br>";	
			}
				$total_x_dia = number_format($total_x_dia,2);
				echo '<table border="0" class="bright bleft btop font_22 f_amarillo t_rojo"  width="230" style="position:relative; float:right;">
					  <tr>
					   	<td align="right">Total: <strong>$'.$total_x_dia.'</strong></td>
					  </tr>
					  </table><br>';
				echo $tbl_completa;			
		}else{
			echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
		}
	}
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='ventas_sucursales'){	
	$conn = new class_mysqli();
	$id = $_POST['id'];
	if($_POST['fecha'])
		$fecha = $_POST['fecha'];
	else
		$fecha = date("Y-m-d");

	if($_POST['fecha2'])
		$fecha2 = $_POST['fecha2'];
	else
		$fecha2 = date("Y-m-d");
		
				
	//$sql = "SELECT * FROM `tbl_ventas_caja` WHERE DATE_FORMAT(fecha,'%d')=DAY(NOW()) AND NumEmp = ".$_SESSION['g_NumEmp']." ORDER BY fecha DESC";
	$sql = "SELECT * FROM `tbl_ventas_caja` WHERE DATE_FORMAT(fecha,'%Y-%m-%d') BETWEEN '$fecha' AND '$fecha2' AND NumEmp = ".$_SESSION['g_NumEmp']." ORDER BY fecha DESC";
	if($_SESSION['g_nivel'] == "admin")
		$sql = "SELECT *, DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs1
		 		FROM `tbl_ventas_caja` WHERE DATE_FORMAT(fecha,'%Y-%m-%d')  BETWEEN '$fecha' AND '$fecha2' AND id_empresa = ".$_SESSION['g_id_empresa']." ORDER BY fecha DESC";
	// Si la busqueda es por Usuario
	if($id)
		$sql = "SELECT *, DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs1 
				FROM `tbl_ventas_caja` WHERE DATE_FORMAT(fecha,'%Y-%m-%d')  BETWEEN '$fecha' AND '$fecha2' AND id_sucursal = $id AND id_empresa = ".$_SESSION['g_id_empresa']." ORDER BY fecha DESC";
	
		//echo $sql;
	if($result = $conn->conn_mysqli->query($sql)){
		if($result->num_rows){	
			$tbl_completa = "";
			while ($row = $result->fetch_assoc()) {
				$row["mes1"] = $conn->damemes($row["mes1"]);
				$row["fecha"] = $row["dia1"].'<strong> / </strong>'.$row["mes1"].'<strong> / </strong>'.$row["anio1"].' '.$row["hrs1"];				
				$array_id_prod = explode(",",$row["id_productos"]);
				$cantidades = explode(",",$row["cantidades"]);
				$precios = explode(",",$row["precios"]);
				$folio = sprintf("%07s",  $row["id_ventas_cajas"]);
				$total_x_dia = $total_x_dia+$row["total"];
				$card = $row["card"];
				if($card != "")
					$tipo_pago = 'Pago con Tarjeta  
									<img src="images/t_'.$card.'.png" height="22" width="28" style="position: relative; margin-top:0px">
								   , 
								  aprobacion: <span class="negritas t_negro">'.$row["aprobacion_card"].'</span>';
				else
					$tipo_pago = '<span class="negritas t_negro">Pago en Efectivo</span>';	
				$tabla_1 = '
						  <table border="0" class="f_cabecera_list_ventas btop bbottom bright bleft"  width="100%">
						  <tr>
							<td width="90"><span class="negritas t_negro">Fecha:</span></td>
							<td><span class=" t_negro">'.$row["fecha"].'</span></td>
							<td align="right"><strong>Folio:</strong> <span class="negritas t_azul_fuerte f_blanco" style="padding:0 5px 0 5px;">'.$folio.'</span></td>
						  </tr>
						  <tr>
						  	<td><span class="negritas t_negro">Empleado:</span></td>
							<td><span class=" t_negro">'.$row["nombre_empleado"].'</span></td>
							<td align="right">'.$tipo_pago.'</td>
						  </tr>
						  </table>';
				$tabla_2 = '<table border="0" width="100%" cellspacing="0" class="bbottom bright bleft">
								<tr class="f_sub_cabecera_list_ventas t_negro">
									<th width="80%">Producto</th>
									<th width="20">Cant.</th>
									<th width="30">Precio</th>
								</tr>';		  
				foreach($array_id_prod as $key => $valor){
					$sql = "SELECT nombre FROM `tbl_producto` WHERE id = ".$valor;
					$result_prod = $conn->conn_mysqli->query($sql);
					$row_prod = $result_prod->fetch_assoc();
					//echo "<br>".$row_prod['nombre'];
					if($key%2)
						$clase = 'class="f_verde2_ventas f_resalta_verde"';
					else
						$clase = 'class="f_verde1_ventas f_resalta_verde"';	
					$tabla_2 .= '<tr '.$clase.'>
									<td>'.$row_prod['nombre'].'</td>
									<td align="center">'.$cantidades[$key].'</td>
									<td align="right">'.$precios[$key].'</td>
							     </tr>';
				}
				$tabla_2 .= '</table>';
											
				$row["total"] = number_format($row["total"],2);
				$tabla_3 = '<table border="0" class="f_sub_cabecera_list_ventas bright bleft bbottom font_16"  width="150" style="position:relative; float:right">
					  <tr>
					   	<td align="right">Total: <strong><span class="negritas t_rojo">$'.$row["total"].'</span></strong></td>
					  </tr>
					  </table><br>';				
				$tbl_completa .= $tabla_1.$tabla_2.$tabla_3."<br>";	
			}
				$total_x_dia = number_format($total_x_dia,2);
				echo '<table border="0" class="bright bleft btop font_22 f_amarillo t_rojo"  width="230" style="position:relative; float:right;">
					  <tr>
					   	<td align="right">Total: <strong>$'.$total_x_dia.'</strong></td>
					  </tr>
					  </table><br>';
				echo $tbl_completa;		
		}else{
			echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
		}
	}
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='pagos_sucursales'){	
	$conn = new class_mysqli();
	$id = $_POST['id'];
	if($_POST['fecha'])
		$fecha = $_POST['fecha'];
	else
		$fecha = date("Y-m-d");

	if($_POST['fecha2'])
		$fecha2 = $_POST['fecha2'];
	else
		$fecha2 = date("Y-m-d");
		
	$tipo = $_POST['estatus'];
	
	$sql = "SELECT *, DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs1 
			FROM `tbl_salida_entrada_caja` WHERE DATE_FORMAT(fecha,'%Y-%m-%d') BETWEEN '$fecha' AND '$fecha2' AND NumEmp = ".$_SESSION['g_NumEmp']." ORDER BY fecha DESC";
	
	// Si la busqueda es por Usuario
	if($id)
		$sql = "SELECT *, DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs1 
				FROM `tbl_salida_entrada_caja` WHERE DATE_FORMAT(fecha,'%Y-%m-%d') BETWEEN '$fecha' AND '$fecha2' AND id_sucursal = $id AND id_empresa = ".$_SESSION['g_id_empresa']." ORDER BY fecha DESC";
	//echo $sql;
	if($result = $conn->conn_mysqli->query($sql)){
		if($result->num_rows){	
			$tbl_completa = "";
			while ($row = $result->fetch_assoc()) {
				$folio = sprintf("%07s",  $row["id_salida_entrada_caja"]);
				$total_x_dia = $total_x_dia+$row["total"];
				$row['motivo'] = str_ireplace('##br##',"<br>",$row['motivo']);
				$row["total"] = number_format($row["total"],2);
				$row["mes1"] = $conn->damemes($row["mes1"]);
				$row["fecha"] = $row["dia1"].'<strong> / </strong>'.$row["mes1"].'<strong> / </strong>'.$row["anio1"].' '.$row["hrs1"];					
				$tabla_1 = '
						  <table border="0" class="f_cabecera_list_ventas btop bbottom bright bleft"  width="100%">
						  <tr>
							<td width="90"><span class="negritas t_negro">Fecha:</span></td>
							<td><span class=" t_negro">'.$row["fecha"].'</span></td>
							<td width="100" rowspan="2" align="center" class="negritas t_negro">'.$row['concepto'].'</td>
							<td width="120" align="right"><strong>Folio:</strong> <span class="negritas t_azul_fuerte f_blanco" style="padding:0 5px 0 5px;">'.$folio.'</span></td>
						  </tr>
						  <tr>
						  	<td><span class="negritas t_negro">Empleado:</span></td>
							<td><span class=" t_negro">'.$row["nombre_empleado"].'</span></td>
							<td align="right"><span class="negritas t_negro">$'.$row["total"].'</span></td>
						  </tr>
						  </table>';
				$tabla_2 = '<table border="0" width="100%" cellspacing="0" class="bbottom bright bleft">';		  
					$tabla_2 .= '<tr '.$clase.'>
									<td><span class="negritas t_negro">Descripcion:</span>
										'.$row['motivo'].'
									</td>
							     </tr>';
				$tabla_2 .= '</table>';
				$tbl_completa .= $tabla_1.$tabla_2."<br>";	
			}
				$total_x_dia = number_format($total_x_dia,2);
				echo '<table border="0" class="bright bleft btop font_22 f_amarillo t_rojo"  width="230" style="position:relative; float:right;">
					  <tr>
					   	<td align="right">Total: <strong>$'.$total_x_dia.'</strong></td>
					  </tr>
					  </table><br>';
				echo $tbl_completa;			
		}else{
			echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
		}
	}
}
/************************************************************************************************************************************/
/***********************************************    REPORTE DE VENTAS   *************************************************************/
/************************************************************************************************************************************/

if(array_key_exists("accion", $_POST) && $_POST['accion']=='ver_pedidos'){	
	$conn = new class_mysqli();
	$id_cliente = $_POST['id_cliente'];
	$no_pedido = $_POST['no_pedido'];
	
	$estatus = " AND estatus='abierto'";
	if($_POST['estatus'] == 'abierto')
		$estatus = " AND estatus='abierto'";
	if($_POST['estatus'] == 'cerrado')
		$estatus = " AND estatus='cerrado'";
	if($_POST['estatus'] == 'cancelado')
		$estatus = " AND estatus='cancelado'";
		
				
	if($_POST['fecha'])
		$fecha = $_POST['fecha'];
	else
		$fecha = date("Y-m-d");
	if($_POST['fecha2'])
		$fecha2 = $_POST['fecha2'];
	else
		$fecha2 = date("Y-m-d");		
	$sql = "SELECT 
	DATE_FORMAT(pe.fecha_inicio, '%m') AS mes1,DATE_FORMAT(pe.fecha_inicio, '%d') AS dia1,DATE_FORMAT(pe.fecha_inicio, '%Y') AS anio1,DATE_FORMAT(pe.fecha_inicio, '%h:%i %p') AS hrs1,
	DATE_FORMAT(pe.fecha_final, '%m') AS mes2,DATE_FORMAT(pe.fecha_final, '%d') AS dia2,DATE_FORMAT(pe.fecha_final, '%Y') AS anio2,DATE_FORMAT(pe.fecha_final, '%h:%i %p') AS hrs2,
	pe.*, cl.nombre as nombreCliente, us.Nombre as nombreEmpleado
			FROM tbl_pedido pe, tbl_cliente cl,  tbl_usuarios us
			WHERE pe.id_cliente = cl.id_cliente AND pe.id_admin = us.NumEmp AND 
				  DATE_FORMAT(pe.fecha,'%Y-%m-%d') BETWEEN '$fecha' AND '$fecha2' AND
				  pe.id_empresa = ".$_SESSION['g_id_empresa']." AND pe.id_sucursal IN (".$_SESSION['g_sucursales'].") $estatus ORDER BY pe.fecha DESC";
	if($id_cliente){
		$sql = "SELECT 
	DATE_FORMAT(pe.fecha_inicio, '%m') AS mes1,DATE_FORMAT(pe.fecha_inicio, '%d') AS dia1,DATE_FORMAT(pe.fecha_inicio, '%Y') AS anio1,DATE_FORMAT(pe.fecha_inicio, '%h:%i %p') AS hrs1,
	DATE_FORMAT(pe.fecha_final, '%m') AS mes2,DATE_FORMAT(pe.fecha_final, '%d') AS dia2,DATE_FORMAT(pe.fecha_final, '%Y') AS anio2,DATE_FORMAT(pe.fecha_final, '%h:%i %p') AS hrs2,		
		pe.*, cl.nombre as nombreCliente, us.Nombre as nombreEmpleado
				FROM tbl_pedido pe, tbl_cliente cl,  tbl_usuarios us
				WHERE pe.id_cliente = cl.id_cliente AND pe.id_admin = us.NumEmp AND pe.id_cliente = ".$id_cliente." AND
					  pe.id_empresa = ".$_SESSION['g_id_empresa']." AND pe.id_sucursal IN (".$_SESSION['g_sucursales'].") $estatus ORDER BY pe.fecha DESC";		
	}
	if($no_pedido){
		$sql = "SELECT 
	DATE_FORMAT(pe.fecha_inicio, '%m') AS mes1,DATE_FORMAT(pe.fecha_inicio, '%d') AS dia1,DATE_FORMAT(pe.fecha_inicio, '%Y') AS anio1,DATE_FORMAT(pe.fecha_inicio, '%h:%i %p') AS hrs1,
	DATE_FORMAT(pe.fecha_final, '%m') AS mes2,DATE_FORMAT(pe.fecha_final, '%d') AS dia2,DATE_FORMAT(pe.fecha_final, '%Y') AS anio2,DATE_FORMAT(pe.fecha_final, '%h:%i %p') AS hrs2,		
		pe.*, cl.nombre as nombreCliente, us.Nombre as nombreEmpleado
				FROM tbl_pedido pe, tbl_cliente cl,  tbl_usuarios us
				WHERE pe.id_cliente = cl.id_cliente AND pe.id_admin = us.NumEmp AND pe.id_pedido = ".$no_pedido." AND
					  pe.id_empresa = ".$_SESSION['g_id_empresa']." AND pe.id_sucursal IN (".$_SESSION['g_sucursales'].") $estatus ORDER BY pe.fecha DESC";		
	}
	
	if($result = $conn->conn_mysqli->query($sql)){
		if($result->num_rows){	
			$tbl_completa = "";
			while ($row = $result->fetch_assoc()) {
				$btn_ver_pedidos = "";
				$btn_cobrar = "";
				$id_pedido = $row["id_pedido"];
				$cantidades = explode(",",$row["cantidades"]);
				$precios = explode(",",$row["precios"]);
				$folio = sprintf("%07s",  $row["id_pedido"]);
				$total_x_dia = $total_x_dia + $row["total"];
				$anticipos = 0;
				$row["mes1"] = $conn->damemes($row["mes1"]);
				$row["mes2"] = $conn->damemes($row["mes2"]);
				/************************** PASTELERO NO VE CANTIDADES *******************************/
				if($_SESSION['g_nivel'] == "pastelero"){
					$row["total"] = $row["anticipo_inicial"] = $total_x_dia = 0;
				}
				/************************** PAGOS *******************************/
				if($row['anticipos'] == ''){
					$anticipos = $row["total"] - $row["anticipo_inicial"];
				}else{
					$json_anticipos = $row['anticipos'];
					$json_anticipos = json_decode($json_anticipos);
					foreach($json_anticipos as $rowj){
						$anticipos = $anticipos + $rowj->anticipo;
					}
					$anticipos = $row["total"] - ($anticipos + $row["anticipo_inicial"]);
				}

				/************************** SERVICIOS *******************************/
				$servicios = '';
				if($row['servicios'] == ''){
					$btn_estatus = 'Sin servicios';
				}else{
					$json_servicios = $row['servicios'];
					$json_servicios = json_decode($json_servicios);
					foreach($json_servicios as $rows){
					  if($rows->status == "abierto"){
						  if($_SESSION['g_nivel'] != "pastelero"){
						  $servicios .= '<div id="serv_'.$rows->id.'" class="hand f_resalta_azul" 
											 onclick="regresar_deposito(\''.$id_pedido.'\',\''.$rows->id.'\',\''.$rows->nombre.'\')" >
											 <span class="t_naranja_fuerte">$'.$rows->precio.'</span> - '.$rows->nombre.'</div>';
						  }else{
						  $servicios .= '<div id="serv_'.$rows->id.'" class="">
											 <span class="t_naranja_fuerte">$'.$rows->precio.'</span> - '.$rows->nombre.'</div>';
						  }
					  }else{
						  $servicios .= '<div id="serv_'.$rows->id.'" >
											 <span class="t_naranja_fuerte">$'.$rows->precio.'</span> - '.$rows->nombre.' 
											 <span class="font_15 t_gris t_italic"> *Deposito pagado al Cliente</span></div>';
					  }
					}
				}
				
				
				if($row["estatus"] == "abierto"){
					// onclick="cerrar_pedido(\''.$id_pedido.'\')"
					$btn_estatus = '<span class="t_verde_fuerte negritas hand hover_rojo" 
												id="cerrar_pedido_'.$id_pedido.'" 
												>'.$row["estatus"].
							   	   '</span>';
				}else
					$btn_estatus = '<span class="t_naranja_fuerte negritas">'.$row["estatus"].'</span>';
											  				
				$tabla_1 = '
						  <table border="0" class="f_caja_histoHead btop bleft bright"  width="100%" height="30">
						  <tr class="f_negro t_blanco">
						  	<td width="70%">&nbsp;'.$row["nombreEmpleado"].'</td>
						  	<td colspan="2" align="right"><span class="negritas t_negro f_verde font_18">'.$folio.'</span></td>							
						  </tr>
						  </table>
						  <table border="0" class="f_caja_histoHead bleft bright"  width="100%">
						  <tr>
						  	<td width="110"><span class="negritas t_negro">Cliente:</span></td>						  
						  	<td width="70%"><span class="t_negro">'.$row['nombreCliente'].'</span></td>
							<td align="right"><span class="negritas t_negro">Estatus:</span></td>
							<td align="right">'.$btn_estatus.'</td>
						  </tr>
						  <tr>
							<td><span class="negritas t_negro">Fecha Pedido:</span></td>
							<td><span class="">'.$row["dia1"].'/'.$row["mes1"].'/'.$row["anio1"].' '.$row["hrs1"].'</span></td>
							<td align="right"><span class="negritas t_negro">Total:</span></td>
							<td align="right">$'.$row["total"].'</span></td>
						  </tr>
						  <tr>
						  	<td><span class="negritas t_negro">Fecha Entrega:</span></td>
							<td><span class="">'.$row["dia2"].'/'.$row["mes2"].'/'.$row["anio2"].'</span></td>
							<td align="right"><span class="negritas t_negro">Anticipo:</span></td>
							<td align="right">$'.$row["anticipo_inicial"].'</span></td>
						  </tr>
						  <tr>
						  	<td><span class="negritas t_negro">Servicios</span></td>
							<td><span class="t_negro">'.$servicios.'</span></td>
							<td align="right"><span class="negritas t_negro">Resta:</span></td>
							<td align="right">$'.$anticipos.'</span></td>
						  </tr>						  
						  </table>';
				$row['obs'] = str_ireplace('##br##',"<br>",$row['obs']);
				$tabla_2 = "";				
				$tabla_2 = '<table border="0" width="100%" class="f_caja_histoHead bleft bright bbottom" cellspacing="0" class="bbottom">
								<tr class="">
									<td width="110"><strong>Descripcion: </strong></td>
									<td>'.$row['obs'].'</td>
								</tr>
							</table>';
				$tabla_3 = "";					  
				if($row['estatus'] == 'cancelado'){
					$obs_cancelado = str_replace("##br##", "<br>",$row['obs_cancelado']);
					$tabla_3 = '<table border="0" width="100%" class="f_caja_histoHead bleft bright bbottom" cellspacing="0" class="bbottom">
									<tr class="">
										<td width="160"><strong>Motivo Cancelacion: </strong></td>
										<td>'.$obs_cancelado.'</td>
									</tr>
								</table>';					
				}
				/*********************************************** BOTONES *******************************************/
				$btn_cancelar = '
				  <div style="position:relative; width:182px; float:right" id="1">
					  <button class="button_cobrar_pedido" type="button" style="width:180px; height:25px;" 
					  		  id="btn_cancelar_'.$id_pedido.'" onclick="cancelar(\''.$id_pedido.'\')">
						  Cancelar
					  </button>
				  </div>';			 
					$btn_cobrar = '
					  <div style="position:relative; width:182px; float:right" id="1">
						  <button class="button_cobrar_pedido" type="button" style="width:180px; height:25px;" 
						  		  id="btn_cobrar_'.$id_pedido.'" onclick="cobro_pedido(\''.$id_pedido.'\')">
							  Realizar Cobro
						  </button>
					  </div>';
 
				if($row["anticipos"] != ''){
					$btn_ver_pedidos = '
					  <div style="position:relative; width:182px; float:right" id="1">
						  <button class="button_cobrar_pedido" type="button" style="width:180px; height:25px;" 
						  		  id="btn_ver_pedidos_'.$id_pedido.'" onclick="ver_pagos(\''.$id_pedido.'\')">
							  Ver Pagos
						  </button>
					  </div>';
				}
				/************************************** SI ACCEDE COMO PASTELERO ************************************/
				if($_SESSION['g_nivel'] == "pastelero"){
					$btn_cancelar = '';
					$btn_cobrar = '';
					$btn_ver_pedidos = '';
				}
				if($row["estatus"] == 'cerrado' || $row["estatus"] == 'cancelado'){
					$btn_cobrar = '';
					$btn_cancelar = '';	  				
				}
				$tbl_completa .= $tabla_1.$tabla_2.$tabla_3.$btn_cobrar.$btn_cancelar.$btn_ver_pedidos."<br><br>";	
			}
				echo '<table border="0" class="f_caja_totalxdia btop bbottom"  width="100%">
					  <tr>
					   	<td align="right">TOTAL: <strong>$'.$total_x_dia.'</strong></td>
					  </tr>
					  </table>';
				echo $tbl_completa;			
		}else{
			echo '<div class="msg alerta_err"><strong>Sin Registros</strong></div>';
		}
	}	
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='pagar_deposito'){
	$conn = new class_mysqli();
	$id_pedido = $_POST['id_pedido'];
	$id = $_POST['id'];
	
	$sql = "SELECT servicios FROM tbl_pedido WHERE id_pedido = ".$id_pedido;
	if($result = $conn->conn_mysqli->query($sql)){
		if($result->num_rows){	
			$row = $result->fetch_assoc();
			$json_format = json_decode($row['servicios']);
			foreach($json_format as $row_ser){
				//echo gettype($row);
				if($row_ser->id == $id)
					$row_ser->status = "cerrado";
			}
			$json_string = json_encode($json_format);
			echo $conn->devolver_deposito($id_pedido, $json_string); 			
		}
	}else
		echo '{"tipo":"error_sql"}';
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='admin_activar_sucursal'){
	$conn = new class_mysqli();
	$_SESSION['g_id_sucursal'] = $_POST['id_sucursal'];
	$_SESSION["g_sucursal"] = $_POST['sucursal_act'];
	echo "ID:".$_SESSION['g_id_sucursal'];
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='nueva_sucursal'){
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$sucursal = $conn->sanear_string_especiales($_POST['suc']);
	echo $conn->insert_sucursal($_SESSION['g_id_empresa'],$sucursal);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='nueva_unidad'){
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$unidad = $conn->sanear_string_especiales($_POST['unidad']);
	echo $conn->insert_unidad($_SESSION['g_id_empresa'],$_SESSION['g_id_sucursal'],$unidad);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='nueva_seccion'){
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$seccion = $conn->sanear_string_especiales($_POST['seccion']);
	echo $conn->insert_seccion($_SESSION['g_id_empresa'],$_SESSION['g_id_sucursal'],$seccion);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='add_pago_pedido'){
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$id_pedido = $_POST['id_pedido'];
	$anticipos = $_POST['anticipos'];
	echo $conn->add_pago_pedido($_SESSION['g_NumEmp'], $_SESSION['g_nombre'], $id_pedido, $anticipos);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='ver_pagos'){
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$id_pedido = $_POST['id_pedido']; 
	echo $conn->ver_pagos_pedido($id_pedido);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='cerrar_pedido'){
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$id_pedido = $_POST['id_pedido']; 
	//echo "OK: $id_pedido";
	echo $conn->cerrar_pedido($id_pedido);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='cancelar_pedido'){
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$id_pedido = $_POST['id_pedido']; 
	$obs_cancelado = $_POST['obs_cancelado']; 
	//echo "OK: $id_pedido";
	echo $conn->cancelar_pedido($id_pedido, $obs_cancelado);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='busca_traspaso'){
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$id_sucursal = $_POST['id_sucursal']; 
	$tras_cantidad = $_POST['tras_cantidad']; 
	$codigo = $_POST['codigo'];

	$sql="SELECT * FROM tbl_producto WHERE id_sucursal = ".$id_sucursal." AND codigo = '".$codigo."'";	

	if ($result = $conn->conn_mysqli->query($sql)) {			
		if($result->num_rows){	
			while ($row = $result->fetch_assoc()) {
				$id = '{"id":"'.$row["id"].'",';
				$status = '"status":"existe",';
				$cantidad = '"cantidad":"'.$row["cantidad"].'",';
				$codigo = '"codigo":"'.$row["codigo"].'"}';
				$json_completo .= $id
				.$status
				.$cantidad
				.$codigo.',';	
			}
			$json_completo =  rtrim($json_completo, " ,");
			echo $json_completo;
		}else
			echo '{"status":"sin_resultado"}';
		$conn->close_mysqli();
	}
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='insert_traspaso'){
	//id_sucursal="+$id_sucursal+'&cant_trasf='+$cant_trasf+'&codigo='+$codigo,
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	$id_sucursal_origen = $_POST['id_sucursal_origen']; 
	$id_sucursal_destino = $_POST['id_sucursal_destino']; 
	$cant_trasf = $_POST['cant_trasf']; 
	$codigo = $_POST['codigo'];
	$sucursal_destino = $_POST['sucursal_destino'];
	$nombre_prod = $_POST['nombre_prod'];
	//echo "g_sucursal:".$_SESSION['g_sucursal'];
	echo $conn->update_traspaso($_SESSION['g_id_empresa'],$_SESSION['g_NumEmp'], $id_sucursal_origen, $id_sucursal_destino, $codigo, $cant_trasf);
	$conn->insert_traspaso($_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal'], $_SESSION['g_NumEmp'], $_SESSION['g_nombre'], $_SESSION['g_sucursal'], $sucursal_destino, $codigo, $nombre_prod, $cant_trasf);
	
	
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='insert_stock_pasteles'){
	$conn = new class_mysqli();	
	$json_codigos = $_POST['codeJSON']; 
	$_POST = $conn->sanitize($_POST);
	$obs = $_POST['obs']; 
	//$json_codigos = json_decode($json_codigos);
	//foreach($json_codigos as $rowj){
	echo $conn->insert_stock_pasteles($_SESSION['g_id_empresa'], $_SESSION['g_id_sucursal'], $_SESSION['g_NumEmp'], $_SESSION['g_sucursal'], $_SESSION['g_nombre'], $json_codigos, $obs);
	//}
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='ver_stock_pastelesXXX'){
	$conn = new class_mysqli();
	$sql="SELECT *, 
	DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs	
	FROM tbl_stock_pasteles WHERE id_empresa = ".$_SESSION['g_id_empresa']." AND estatus = 'abierto'";		
	if($result = $conn->conn_mysqli->query($sql)){
		if($result->num_rows){	
			$tbl_completa = "";
			while ($row = $result->fetch_assoc()) {
				$id_stock_pasteles = $row["id_stock_pasteles"];
				$id_stock_pasteles = $row["sucursal"];
				$id_stock_pasteles = $row["json_codigos"];
				$id_stock_pasteles = $row["estatus"];
				$id_stock_pasteles = $row["obs"];
				$row["mes1"] = $conn->damemes($row["mes1"]);
				$fecha = $row["dia1"].'/'.$row["mes1"].'/'.$row["anio1"].' '.$row["hrs"];				
				/************************** PASTELERO NO VE CANTIDADES *******************************/
				if($_SESSION['g_nivel'] == "pastelero"){
					//$row["total"] = $row["anticipo_inicial"] = $total_x_dia = 0;
				}
				/************************** PAGOS *******************************/

				$json_codigos = $row['json_codigos'];
				$json_codigos = json_decode($json_codigos);
						
				foreach($json_codigos as $rowj){
					$rowj->code;
					$rowj->catidad;
					$tabla .= '
						<tr class="">
							<td width="160"><strong>'.$rowj->code.'</strong></td>
							<td width="10" align="center"><strong>'.$rowj->catidad.'</strong></td>
						</tr>';						
				}
				echo $tabla .= '</table>';	
				
			}
		}
	}
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='add_stock_prod'){
	$conn = new class_mysqli();
	$id_pedido = $_POST['id_pedido'];
	$id = $_POST['id'];
	exit;
	$sql = "SELECT servicios FROM tbl_pedido WHERE id_pedido = ".$id_pedido;
	if($result = $conn->conn_mysqli->query($sql)){
		if($result->num_rows){	
			$row = $result->fetch_assoc();
			$json_format = json_decode($row['servicios']);
			foreach($json_format as $row_ser){
				//echo gettype($row);
				if($row_ser->id == $id)
					$row_ser->status = "cerrado";
			}
			$json_string = json_encode($json_format);
			echo $conn->devolver_deposito($id_pedido, $json_string); 			
		}
	}else
		echo '{"tipo":"error_sql"}';
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='terminado_stock_prod'){
	$conn = new class_mysqli();
	$id_stock_pasteles = $_POST['id_stock_pasteles'];
	$code = $_POST['code'];
	$id_sucursal = $_SESSION['g_id_sucursal'];
	$id_empresa = $_SESSION['g_id_empresa'];
	
	$sql = "SELECT json_codigos FROM tbl_stock_pasteles WHERE id_stock_pasteles = ".$id_stock_pasteles;
	if($result = $conn->conn_mysqli->query($sql)){
		if($result->num_rows){	
			$row = $result->fetch_assoc();
			$json_format = json_decode($row['json_codigos']);
			foreach($json_format as $row_ser){
				//echo gettype($row);
				if($row_ser->code == $code){
					$row_ser->status = "cerrado";
					$cantidad = $row_ser->cantidad;
					$id_sucursal = $row_ser->id_sucursal;
				}				
			}
			/******************************************************************************* Bloque que afecta al inventario
			$sql = "UPDATE tbl_producto SET cantidad = cantidad + ".$cantidad." 
					WHERE codigo = '$code' AND id_sucursal = $id_sucursal AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal";
			if($result = $conn->conn_mysqli->query($sql)){
				$json_string = json_encode($json_format);
				echo $conn->terminar_stock_prod($id_stock_pasteles, $json_string); 	
			}
			*/	
				$json_string = json_encode($json_format);
				echo $conn->terminar_stock_prod($id_stock_pasteles, $json_string);				
		}
	}else
		echo '{"tipo":"error_sql"}';
	$conn->close_mysqli();	
}

if(array_key_exists("accion", $_POST) && $_POST['accion']=='sumar_stock_prod'){	
	$conn = new class_mysqli();
	$id = $_POST['id'];
	
	$sql = "UPDATE tbl_producto SET cantidad = cantidad - 1 WHERE id  = $id"; 
	
	$conn->conn_mysqli->autocommit(FALSE);
	if($result = $conn->conn_mysqli->query($sql)){
		echo '{"status":"ok_update"}';
		$conn->conn_mysqli->commit();
	}else{
		echo '{"status":"no_update"}';
		$conn->conn_mysqli->rollback();
	}
	$conn->conn_mysqli->autocommit(TRUE);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='edit_adm_usuario'){
	//echo "OK1";
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	//echo "ID:".$_POST['correo'];
	echo $conn->edit_adm_usuario($_POST['id'], $_POST['password']);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='activar_desactivar_adm_usuario'){
	$conn = new class_mysqli();
	$_POST = $conn->sanitize($_POST);
	echo $conn->activa_adm_usuario($_POST['id'], $_POST['valor']);	
}

if(array_key_exists("accion", $_POST) && $_POST['accion']=='sumar_cantidad_producto'){	
	$conn = new class_mysqli();
	$id = $_POST['id'];
	$cantidad = $_POST['cantidad'];
	$sql = "UPDATE tbl_producto SET cantidad = cantidad + $cantidad WHERE id  = $id"; 
	
	$conn->conn_mysqli->autocommit(FALSE);
	if($result = $conn->conn_mysqli->query($sql)){
		echo '{"status":"ok_update"}';
		$conn->conn_mysqli->commit();
	}else{
		echo '{"status":"no_update"}';
		$conn->conn_mysqli->rollback();
	}
	$conn->conn_mysqli->autocommit(TRUE);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='new_empleado'){
	$conn = new class_mysqli();
	$sucursal = $_POST['sucursal'];
	$nombre = $_POST['nombre'];
	$pwd = $_POST['pwd'];
	echo $conn->insert_empleado($_SESSION['g_id_empresa'], $sucursal, $nombre, $pwd);
}
// activar o desactivar el descuento en stck de productos
if(array_key_exists("accion", $_POST) && $_POST['accion']=='estatus_catidades'){	
	$conn = new class_mysqli();
	$estatus = $_POST['estatus']; 
	$id_empresa = $_SESSION['g_id_empresa'];
	$id_sucursal = $_SESSION['g_id_sucursal'];
	$sql = "UPDATE tbl_sucursal_datos SET activar_cantidades ='$estatus' WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal"; 
	
	if($result = $conn->conn_mysqli->query($sql)){
		echo '{"status":"ok_update"}';
	}else{
		echo '{"status":"no_update"}';
	}
}
// Actualizar la variable activar_cantidades de $_SESSION 
if(array_key_exists("accion", $_POST) && $_POST['accion']=='activar_cantidades'){	
	$conn = new class_mysqli();
	$estatus = $_POST['rdo_estatus']; 
	$_SESSION['activar_cantidades'] = $estatus;
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='backup'){
	//echo "backup";
	$conn = new class_mysqli();
	echo $resp = $conn->backup();
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='fileCaja'){
	$fecha = $_POST['fecha'];
	$conn = new class_mysqli();
	$nombre = uniqid();
	$fh = fopen("pendientes/".$fecha."#".$nombre.".txt", 'w') or die("Se produjo un error al crear el archivo");
	echo $_POST['tmpCaja'];
	$texto = $_POST['tmpCaja'];

	fwrite($fh, $texto) or die("No se pudo escribir en el archivo");
	
	fclose($fh);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='filesPendientes'){
	$conn = new class_mysqli();
	$filesPen = [];
	// Abrimos la carpeta que nos pasan como parámetro
	$dir = opendir("pendientes/");
	// Leo todos los ficheros de la carpeta
	while ($elemento = readdir($dir)){
		// Tratamos los elementos . y .. que tienen todas las carpetas
		if( $elemento != "." && $elemento != ".."){
			array_push($filesPen, $elemento);
		}
	}
	echo json_encode( $filesPen );
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='filePendiente'){
	$conn = new class_mysqli();
	$nombreFile = $_POST['fileTxt'];
	$fp = fopen("pendientes/".$nombreFile,"r");
	while(!feof($fp)) {

		$linea = fgets($fp);
		
		//$linea . "<br />";
		
	}
	echo $linea;
	fclose($fp);
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='filePendienteDel'){
	$conn = new class_mysqli();
	$file = $_POST['fileTxt'];
	if( unlink("pendientes/".$file) )
		echo '{"status":"ok_del"}';
	else
		echo '{"status":"error"}';	
}
$conn->close_mysqli();
?> 