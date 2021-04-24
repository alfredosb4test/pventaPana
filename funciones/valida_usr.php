<?php
session_start();
include("conexion_class.php");
$conn = new class_mysqli();
//sleep(2);
if(array_key_exists("accion", $_POST) && $_POST['accion']=='valida_usr'){
	$pwd = trim($_POST['pwd']);

	$pwd2 = "pwd|".md5($pwd);
	$sql = "SELECT tbl_usuarios.*, tbl_empresa.empresa as empresaNombre 
			FROM tbl_usuarios, tbl_empresa 
			WHERE tbl_usuarios.id_empresa = tbl_empresa.id AND password = '$pwd2' AND registrado = 1";
	if ($result = $conn->conn_mysqli->query($sql)) {
		if($result->num_rows){
			$row_r = $result->fetch_assoc();
			
			///////////////////////////////////////////////////////
			// 				validar macaddress
			if($conn->validar_mac_address){
				$mac_address = $conn->get_mac_address();
				if($conn->mac_address_autorizado != $mac_address){
					echo '{"tipo":"no_resgistro"}';
					exit;
				}
			}
			///////////////////////////////////////////////////////
			///////////////////////////////////////////////////////
			// 				validar HOST
			if($conn->validar_HTTP_HOST){
				$HTTP_HOST = $conn->get_HTTP_HOST();
				$autorizado = 0;
				require_once("nusoap/lib/nusoap.php");
				$wsdl = "http://codigosphp.com/alfredo/pventa/web_service/servidor.php?wsdl"; // url sel webservice
				$client = new nusoap_client($wsdl,'wsdl');
				$param = array('tipo' => 'b');
				$necesidades = $client->call('ListarNecesidades', $param);
				// Si hay Error
				if ($client->fault){
					echo '{"tipo":"errConexion"}';
					exit;
				}else{	
					$error = $client->getError();
					if ($error){
						echo '{"tipo":"errConexion"}';
						exit;
					}
				}
				if(is_array($necesidades)){
					for($i = 0; $i < count($necesidades); $i++){
						if($necesidades[$i]['url'] == $HTTP_HOST)
							$autorizado = 1;							
					}
					if(!$autorizado){
						echo '{"tipo":"no_resgistro","url":"no_permitida"}';
						exit;
					}
				}else{
					echo '{"tipo":"errConexion"}';
					exit;
				}				
			}
			///////////////////////////////////////////////////////
			
			$_SESSION['g_nombre']=$row_r['Nombre'];
			$_SESSION['g_id_empresa']=$row_r['id_empresa'];
			$_SESSION['g_empresaNombre']=$row_r['empresaNombre'];
			$_SESSION['g_id_sucursal']=$row_r['id_sucursal'];
			$_SESSION['g_NumEmp']=$row_r['NumEmp'];
			$_SESSION['g_nivel']=$row_r['nivel'];
			$_SESSION['g_sucursales']=$row_r['sucursales']; // en caso de acceso a varias sucursales
			
			$_SESSION['g_ip'] = $_SERVER['REMOTE_ADDR']; 
			$_SESSION['g_nombre_host'] = gethostbyaddr($_SESSION['g_ip']);

			$conn->getDatos_basicos($row_r['id_empresa'], $row_r['id_sucursal']);	// Cargar Datos de la Empresa
			$_SESSION['g_sucursal']=$conn->datos_empresa["sucursal"];
			$_SESSION['g_empresa']=$conn->datos_empresa["empresa"];
			$_SESSION['g_suc_dir']=$conn->datos_empresa["suc_dir"];
			$_SESSION['g_suc_tel1']=$conn->datos_empresa["suc_tel1"];
			$_SESSION['g_suc_tel2']=$conn->datos_empresa["suc_tel2"];
			$_SESSION['activar_cantidades']=$conn->datos_empresa["activar_cantidades"];
			$_SESSION['accesos_caja']=$conn->datos_empresa["accesos_caja"];
			$_SESSION['txt_focus_caja']=$conn->datos_empresa["txt_focus_caja"];
			$_SESSION['cantidad_estricta']=$conn->datos_empresa["cantidad_estricta"];
			echo '{"tipo":"registrado"
					,"nombre":"'.$row_r['Nombre'].'"
					,"IVA":"'.$conn->datos_empresa["iva"].'"
					,"NumEmp":"'.$row_r['NumEmp'].'"
					,"id_empresa":"'.$row_r['id_empresa'].'"
					,"id_sucursal":"'.$row_r['id_sucursal'].'"
					,"sucursal":"'.$_SESSION['g_sucursal'].'"
					,"nivel":"'.$row_r['nivel'].'"
					,"nombre_host":"'.$_SESSION['g_nombre_host'].'"}';

				
			$sql = "insert into tbl_historial (num_empleado,ag,ip, nombre_host, fecha) values";
			//$sql.= "(".$_SESSION['g_NumEmp'].",'".$_SESSION['g_ags']."','".$_SESSION['g_ip']."','".$_SESSION['g_nombre_host']."',now())";
			//$result = $conn->conn_mysqli->query($sql);
			$conn->close_mysqli();
			unset($conn);			
			//sleep(1);
			exit;
		}else
			echo '{"tipo":"inactivo","err_txt":"Cuenta Inactiva"}';
	}else
		echo '{"tipo":"errConexion","err_txt":"Datos Invalidos"}';	

		exit;

	// Si ya esta registrado 
	$sql = "SELECT * FROM tbl_usuarios WHERE NumEmp = $pwd AND registrado = 1";
	if ($result = $conn->conn_mysqli->query($sql)) {
		if($result->num_rows){
			echo '{"tipo":"usr_registrado"}';
			exit;
		}
	}
	
	$conn->conn_mysqli->query("SET NAMES utf8");	
	$sql = "SELECT * FROM tbl_usuarios WHERE NumEmp = '$pwd' AND registrado = 0";
	if ($result = $conn->conn_mysqli->query($sql)) {
		if($result->num_rows){
			$row_r = $result->fetch_assoc();
			if($row_r['Nombre'])
				echo '{"tipo":"nuevo_ingreso",
				"nombre":"'.$row_r['Nombre'].'",
				"ag":"'.$row_r['ag'].'",
				"NumEmp":"'.$row_r['NumEmp'].'",
				"Correo":"'.$row_r['Correo'].'",
				"grado":"'.$row_r['grado'].'",
				"puesto":"'.$row_r['puesto'].'"}';				
		}else{
			$pwd = "pwd|".md5($pwd);
			$sql = "SELECT * FROM tbl_usuarios WHERE password = '$pwd'";
			if ($result = $conn->conn_mysqli->query($sql)) {
				$row_r = $result->fetch_assoc();
				if($result->num_rows){
					echo '{"tipo":"registrado"
							,"nombre":"'.$row_r['Nombre'].'","ag":"'.$row_r['ag'].'"
							,"NumEmp":"'.$row_r['NumEmp'].'","Correo":"'.$row_r['Correo'].'"
							,"grado":"'.$row_r['grado'].'","puesto":"'.$row_r['puesto'].'"}';

					$_SESSION['g_nombre']=$row_r['Nombre'];
					$_SESSION['g_ags']=$row_r['ag'];
					$_SESSION['g_id_unidadDepto']=$row_r['id_unidadDepto'];
					$_SESSION['g_NumEmp']=$row_r['NumEmp'];
					$_SESSION['g_nivel']=$row_r['nivel'];
					$_SESSION['g_ag_permisos']=$row_r['ag_permisos'];
					$_SESSION['g_unidades_permisos']=$row_r['descripcion_unidad_permisos'];						
				}else
					echo '{"tipo":"no_resgistro"}';
			}			
		}
	}else
		echo '{"tipo":"errConexion","err_txt":"Datos Invalidos"}';	
   		
	$conn->close_mysqli();	
}
if(array_key_exists("accion", $_POST) && $_POST['accion']=='registra_pwd'){
	$pwd = "pwd|".md5(trim($_POST['pwd']));
	$pwd_1 = trim($_POST['pwd']);
	$no_empleado = $_POST['no_empleado'];
	
	if($pwd_1 != $no_empleado){
		$sql = "SELECT NumEmp FROM tbl_usuarios WHERE NumEmp = $pwd_1
				UNION
				SELECT NumEmp FROM tbl_usuarios WHERE `password` = '$pwd'";
		if ($result = $conn->conn_mysqli->query($sql)) {
			if($result->num_rows){			
				echo '{"tipo":"pwd_ocupado"}';
				exit;
			}
		}	
	}
	
	$sql = "UPDATE tbl_usuarios SET password = '$pwd', registrado = 1, fecha_registro = '".date('Y-m-d H:i:s')."' WHERE NumEmp = '$no_empleado'";
	if ($result = $conn->conn_mysqli->query($sql)) {
		if($conn->conn_mysqli->affected_rows){
			echo '{"tipo":"updateOK","pwd":"'.$pwd_1.'"}';
		}else
			echo '{"tipo":"updateERR"}';
	}else
		echo '{"tipo":"errConexion","err_txt":"'.$conn->conn_mysqli->error.'"}';
		
	$conn->close_mysqli();
}
?>