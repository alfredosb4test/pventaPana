<?php
class class_mysqli{
    protected $localhost;
    protected $usuario;
    protected $pwd;	
    public $bd;
    public $conn_mysqli;
	public $mysqli_status=0;
	public $procesoDefault=0;
	public $datos_prod 	= array();
	public $datos_empresa = array();
	public $validar_mac_address=0;
	public $validar_HTTP_HOST=0;	
	public $mac_address_autorizado="00-13-D3-F3-89-BB"; // lap:0A-00-27-00-00-10 | ale: 00-E0-81-5A-F0-A0
	private $mysqldump = 'F:\xampp\mysql\bin\mysqldump.exe ';
	// $this->conn_mysqli->error
	function __construct($localhost='localhost', $usr='pventa', $pwd='pv3n74*', $db='pventa_almacen'){
	//function __construct($localhost='localhost', $usr='codig915_pventaA', $pwd='zczs35ky43', $db='codig915_pventa'){	
	//function __construct($localhost='localhost', $usr='pastel11_pventa', $pwd='zczs35ky43', $db='pastel11_pventa'){
        $this->localhost = $localhost;
        $this->usuario = $usr;
        $this->pwd = $pwd;
        $this->bd = $db;			
		ini_set('error_reporting', 0);		
		$this->open_mysqli();	
	}
	function open_mysqli(){
		$this->conn_mysqli = new mysqli($this->localhost, $this->usuario, $this->pwd, $this->bd);
		if (mysqli_connect_errno($this->conn_mysqli)){
		  	$this->mysqli_status=-1;
		}else{ 
			$this->mysqli_status=1;
			//$this->conn_mysqli->query("SET NAMES utf8"); // descomentar esta linea para el servidor		
		}
	}
	function close_mysqli(){
		$this->conn_mysqli->close();
		$this->mysqli_status=0;
	}

	function backup(){
		set_time_limit(120);
		$dir = 'backup/';
		$db_name='pventa_almacen';
		$fecha = date("Ymd-His"); //Obtenemos la fecha y hora para identificar el respaldo
 
		// Construimos el nombre de archivo SQL Ejemplo: mibase_20170101-081120.sql
		$salida_sql = $dir.$db_name.'_'.$fecha.'.sql'; 
		
		//Comando para genera respaldo de MySQL, enviamos las variales de conexion y el destino
		$dump = $this->mysqldump.' --force --compress --disable-keys ' 
		.' --user='.$this->usuario
		.' --password="'.$this->pwd.'"'
		.' --lock-tables --databases '.$this->bd.' > '.$salida_sql;

		exec($dump, $output); //Ejecutamos el comando para respaldo

		$zip = new ZipArchive(); //Objeto de Libreria ZipArchive
		
		//Construimos el nombre del archivo ZIP Ejemplo: mibase_20160101-081120.zip
		$salida_zip = $dir.$db_name.'_'.$fecha.'.zip';
		
		if($zip->open($salida_zip,ZIPARCHIVE::CREATE)===true) { //Creamos y abrimos el archivo ZIP
			$zip->addFile($salida_sql); //Agregamos el archivo SQL a ZIP
			$zip->close(); //Cerramos el ZIP
			unlink($salida_sql); //Eliminamos el archivo temporal SQL
			//header ("Location: $salida_zip"); // Redireccionamos para descargar el Arcivo ZIP
			return $salida_zip;	
		} else {
			return 'Error'; //Enviamos el mensaje de error
		}
	}

	function get_mac_address(){
		ob_start(); // Turn on output buffering
		system('getmac /NH'); //Execute external program to display output
		 
		$mycom=ob_get_contents(); // Capture the output into a variable
		 
		ob_clean(); // Clean (erase) the output buffer
		$mac=trim(substr($mycom,0,19)); // Get Physical Address
		 
		return $mac;
	}

	function get_HTTP_HOST(){
		return $HTTP_HOST = $_SERVER['HTTP_HOST'];
	}

/*******************************************************************************************************************/
/*************************************************  Datos IVA, Empresa *****************************************************/
/*******************************************************************************************************************/
	function getDatos_basicos($idEmpresa, $id_sucursal){
		$sql = "SELECT tbl_empresa.*, tbl_sucursal.id_sucursal,tbl_sucursal.sucursal, 
												suc_datos.direccion as suc_dir, suc_datos.tel1 as suc_tel1, suc_datos.tel2 as suc_tel2, 
												suc_datos.activar_cantidades
												FROM tbl_empresa, tbl_sucursal, tbl_sucursal_datos as suc_datos 
												WHERE tbl_sucursal.id_sucursal = $id_sucursal AND 
												suc_datos.id_sucursal = $id_sucursal AND 
												tbl_empresa.id = $idEmpresa";
		if($result = $this->conn_mysqli->query($sql)) {		 
					if($result->num_rows){	
						while ($row = $result->fetch_assoc()) {
							$this->datos_empresa['id_empresa']	 	= $row["id"];
							$this->datos_empresa['empresa']		 	= $row["empresa"];
							$this->datos_empresa['direccion']		= $row["direccion"];
							$this->datos_empresa['ciudad']  		= $row["ciudad"];
							$this->datos_empresa['tel1']			= $row["tel1"];
							$this->datos_empresa['tel2']			= $row["tel2"];
							$this->datos_empresa['web']				= $row["web"];
							$this->datos_empresa['correo']			= $row["correo"];
							$this->datos_empresa['iva']				= $row["iva"];
							$this->datos_empresa['id_sucursal']		= $row["id_sucursal"];
							$this->datos_empresa['sucursal']		= $row["sucursal"];
							$this->datos_empresa['suc_dir']			= $row["suc_dir"];
							$this->datos_empresa['suc_tel1']		= $row["suc_tel1"];
							$this->datos_empresa['suc_tel2']		= $row["suc_tel2"];
							$this->datos_empresa['activar_cantidades']		= $row["activar_cantidades"];
						}	 		
						return 'existe';						
					}else
						return 'no_existe';
		}else
			return 'error_sql';
		$result->close();
	}
/*******************************************************************************************************************/
/*************************************************  PROVEDORES *****************************************************/
/*******************************************************************************************************************/
	function insert_prove($id_empresa, $id_admin, $empresa, $id_sucursal, $contacto, $dir, $ciudad, $tel, $cel, $correo, $obs){
		$fecha = date('Y-m-d H:i:s');
		$obs = str_replace(array("\r\n","\r","\n"), "<br>",$obs);
		if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_proveedor (id_empresa,id_admin,empresa,id_sucursal,contacto,dir,ciudad,tel,cel,correo,obs) VALUES (?,?,?,?,?,?,?,?,?,?,?)")) {		 
			if($result->bind_param("iisisssssss",$id_empresa, $id_admin, $empresa, $id_sucursal, $contacto, $dir, $ciudad, $tel, $cel, $correo, $obs)){
				if($result->execute()){
				 	return '{"tipo":"prove_registrado"}';
				}else
					return '{"tipo":"error_execute"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}
	
	function edit_prove($empresa, $contacto, $dir, $ciudad, $tel, $cel, $correo, $obs, $id){
		$fecha = date('Y-m-d H:i:s');
		if($result = $this->conn_mysqli->prepare("UPDATE tbl_proveedor SET empresa=?,contacto=?,dir=?,ciudad=?,tel=?,cel=?,correo=?,obs=? WHERE id_proveedor = ?")) {		 
			if($result->bind_param("ssssssssi", $empresa, $contacto, $dir, $ciudad, $tel, $cel, $correo, $obs, $id)){
				if($result->execute()){
				 	return '{"tipo":"prove_update"}';
				}else
					return '{"tipo":"error_execute"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}
	function activa_prove($id, $valor){
		if($result = $this->conn_mysqli->prepare("UPDATE tbl_proveedor SET activo = ? WHERE id_proveedor = ?")) {		 
			if($result->bind_param("si", $valor, $id)){
				if($result->execute()){
				 	return '{"tipo":"prove_activo"}';
				}else
					return '{"tipo":"error_execute"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}	
	
/*************************************************  LISTADO PARA MODULO DE PROVEDORES  *****************************************************/
	function listar_prove($id_proveedor,$id_sucursal,$id_empresa){	//  unidad_negocio='".$usr_ag."'
		$where = "";
		if($id_proveedor)
			$where = "AND id_proveedor = ".$id_proveedor;

		$sql = "SELECT * FROM tbl_proveedor WHERE  tbl_proveedor.id_empresa = $id_empresa AND activo ='1' ".$where;
		if ($result = $this->conn_mysqli->query($sql)) {
			$lista_resultados = array();
			if($result->num_rows){	
				while ($row = $result->fetch_assoc()) {
					$lista_resultados['id_proveedor'][$row["id_proveedor"]] = $row["id_proveedor"];
					$lista_resultados['empresa'][$row["id_proveedor"]] = $row["empresa"];
					$lista_resultados['contacto'][$row["id_proveedor"]] = $row["contacto"];
					$lista_resultados['dir'][$row["id_proveedor"]] = $row["dir"];
					$lista_resultados['ciudad'][$row["id_proveedor"]] = $row["ciudad"];
					$lista_resultados['tel'][$row["id_proveedor"]] = $row["tel"];
					$lista_resultados['cel'][$row["id_proveedor"]] = $row["cel"];
					$lista_resultados['correo'][$row["id_proveedor"]] = $row["correo"];
					$lista_resultados['obs'][$row["id_proveedor"]] = nl2br($row["obs"]);
					$lista_resultados['activo'][$row["id_proveedor"]] = $row["activo"];
				}
			}else
			 return $lista_resultados = "no_data";			
		}
		return $lista_resultados;		
		$result->close();		
	}
/*******************************************************************************************************************/
/*************************************************  PRODUCTOS  *****************************************************/
/*******************************************************************************************************************/
	function insert_producto($id_empresa, $id_admin, $id_sucursal, $id_unidad, $id_seccion, $id_proveedor, $codigo, $codigo_unidades, $nombre, $imagen, $precio_provedor, $precio_venta, $precio_mayoreo, $cantidad, $minimo){
		$fecha = date('Y-m-d H:i:s');
		if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_producto (id_empresa, id_admin, id_sucursal, id_unidad, id_seccion, id_proveedor, codigo, codigo_unidades, nombre, imagen, precio_provedor, precio_venta, precio_mayoreo, cantidad, minimo) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")) {		 
			if($result->bind_param("iiiiiissssdddii",$id_empresa, $id_admin, $id_sucursal, $id_unidad, $id_seccion, $id_proveedor, $codigo, $codigo_unidades, $nombre, $imagen, $precio_provedor, $precio_venta, $precio_mayoreo, $cantidad, $minimo)){
				if($result->execute()){
				 	return '{"tipo":"producto_registrado"}';
				}else
					return '{"tipo":"error_execute"}';	// $result->error;  //
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}
	function edit_producto($id_empresa, $id_admin, $id_sucursal, $id_unidad, $id_seccion, $id_proveedor, $codigo, $codigo_unidades, $nombre, $imagen, $precio_provedor, $precio_venta, $precio_mayoreo, $cantidad, $minimo, $id){
		if($result = $this->conn_mysqli->prepare("UPDATE tbl_producto SET id_empresa = ? , id_admin = ?, id_sucursal = ?, id_unidad = ?, id_seccion = ?, id_proveedor = ?, codigo = ?, codigo_unidades = ?, nombre = ?, imagen = ?, precio_provedor = ?, precio_venta = ?, precio_mayoreo = ?, cantidad = ?, minimo = ? WHERE id = ?")) {		 
			if($result->bind_param("iiiiiissssdddiii",$id_empresa, $id_admin, $id_sucursal, $id_unidad, $id_seccion, $id_proveedor, $codigo, $codigo_unidades, $nombre, $imagen, $precio_provedor, $precio_venta, $precio_mayoreo, $cantidad, $minimo, $id)){
				if($result->execute()){
				 	return '{"tipo":"producto_update"}';
				}else
					return '{"tipo":"error_execute"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();	
	}
	function edit_producto_cantidad($id, $cantidad){
		if($result = $this->conn_mysqli->prepare("UPDATE tbl_producto SET cantidad = ? WHERE id = ?")) {		 
			if($result->bind_param("ii",$cantidad, $id)){
				if($result->execute()){
				 	return '{"tipo":"producto_update"}';
				}else
					return '{"tipo":"error_execute"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();	
	}
	
	function insert_stock_pasteles($id_empresa, $id_sucursal, $id_admin, $sucursal, $nombre, $json_codigos, $obs){
		$fecha = date('Y-m-d H:i:s');
		$obs = str_replace(array("\r\n","\r","\n"), "<br>",$obs);
		if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_stock_pasteles (id_empresa,id_sucursal,id_admin,sucursal,nombre_empleado,json_codigos,estatus,obs) VALUES (?,?,?,?,?,?,?,?)")) {		 
			$status = "abierto";
			if($result->bind_param("iiisssss",$id_empresa, $id_sucursal, $id_admin, $sucursal, $nombre, $json_codigos, $status, $obs)){
				if($result->execute()){
				 	return '{"tipo":"insert_ok"}';
				}else
					return '{"tipo":"insert_error"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}	
	function listar_stock_pasteles($id_empresa){	 
		$sql="SELECT *, 
		DATE_FORMAT(fecha, '%m') AS mes1,DATE_FORMAT(fecha, '%d') AS dia1,DATE_FORMAT(fecha, '%Y') AS anio1,DATE_FORMAT(fecha, '%h:%i %p') AS hrs	
		FROM tbl_stock_pasteles WHERE id_empresa = ".$id_empresa." AND estatus = 'abierto' order by id_stock_pasteles desc";	
		if ($result = $this->conn_mysqli->query($sql)) {
			$lista_resultados = array();
			if($result->num_rows){	
				while ($row = $result->fetch_assoc()) {					
					$lista_resultados['id_stock_pasteles'][$row["id_stock_pasteles"]] = $row["id_stock_pasteles"];
					$lista_resultados['sucursal'][$row["id_stock_pasteles"]] = $row["sucursal"];
					$lista_resultados['json_codigos'][$row["id_stock_pasteles"]] = $row["json_codigos"];
					$lista_resultados['estatus'][$row["id_stock_pasteles"]] = $row["estatus"];
					$lista_resultados['obs'][$row["id_stock_pasteles"]] = $row["obs"];
					$lista_resultados['mes1'][$row["id_stock_pasteles"]] = $row["mes1"];
					$lista_resultados['dia1'][$row["id_stock_pasteles"]] = $row["dia1"];
					$lista_resultados['anio1'][$row["id_stock_pasteles"]] = nl2br($row["anio1"]); 
					$lista_resultados['hrs'][$row["id_stock_pasteles"]] = $row["hrs"];
				}
			}else
			 return $lista_resultados = "no_data";			
		}
		return $lista_resultados;		
		$result->close();		
	}	
	function terminar_stock_prod($id_stock_pasteles, $json_string){
			if($result = $this->conn_mysqli->prepare("UPDATE tbl_stock_pasteles SET json_codigos = ? WHERE id_stock_pasteles = ?")) {		 
				if($result->bind_param("si", $json_string, $id_stock_pasteles)){
					if($result->execute()){
						//$id = $this->conn_mysqli->insert_id;
						return '{"tipo":"update_registrado"}';
					}else
						return '{"tipo":"error_execute"}';
				}else
					return '{"tipo":"error_parametros"}';
			}else
				return '{"tipo":"error_sql"}';			
	}
/*******************************************************************************************************************/
/**********************************************  TRASFERENCIAS *****************************************************/
/*******************************************************************************************************************/
	function update_traspaso($id_empresa, $id_admin, $id_sucursal_origen, $id_sucursal_destino, $codigo, $tras_cantidad){
		$this->conn_mysqli->autocommit(false);
		$sql1 = "UPDATE tbl_producto SET cantidad = (cantidad + $tras_cantidad) WHERE codigo = '$codigo' AND id_sucursal = $id_sucursal_destino;";
		$sql2 = "UPDATE tbl_producto SET cantidad = (cantidad - $tras_cantidad) WHERE codigo = '$codigo' AND id_sucursal = $id_sucursal_origen";
		$update1 = $this->conn_mysqli->query($sql1);
		$update2 = $this->conn_mysqli->query($sql2);
		if($update1 && $update2){			
			$this->conn_mysqli->commit();
			$this->conn_mysqli->autocommit(true);
			return '{"status":"ok_update"}';
		}else{
			$this->conn_mysqli->rollback();
			$this->conn_mysqli->autocommit(true);
			return '{"status":"no_update"}';		//'.$this->conn_mysqli->error.'
			
		}
	}				
	function insert_traspaso($id_empresa, $id_sucursal, $id_admin, $nombre_empleado, $sucursal_origen, $sucursal_destino, $codigo, $nombre_prod, $cant_trasf){
		if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_trasferencias (id_empresa, id_sucursal, id_admin, nombre_empleado, sucursal_origen, sucursal_destino, codigo, nombre_prod, cant_trasf) VALUES (?,?,?,?,?,?,?,?,?)")) {		 
			if($result->bind_param("iiisssssi",$id_empresa, $id_sucursal, $id_admin, $nombre_empleado, $sucursal_origen, $sucursal_destino, $codigo, $nombre_prod, $cant_trasf)){
				if($result->execute()){
				 	return '{"tipo":"insert_ok"}';
				}else
					return '{"tipo":"error_execute"}';	// $result->error;  //
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();
	}
	function listar_trasferencias($order_by, $id_empresa, $sucursales){	
		$sql = "SELECT * FROM tbl_trasferencias
				WHERE id_empresa = $id_empresa AND id_sucursal IN($sucursales) $order_by";
		if ($result = $this->conn_mysqli->query($sql)) {
			$lista_resultados = array();
			if($result->num_rows){	
				while ($row = $result->fetch_assoc()) { 
					$lista_resultados['id_trasferencias'][$row["id_trasferencias"]] = $row["id_trasferencias"];
					$lista_resultados['nombre_empleado'][$row["id_trasferencias"]] = $row["nombre_empleado"];
					$lista_resultados['sucursal_origen'][$row["id_trasferencias"]] = $row["sucursal_origen"]; 
					$lista_resultados['sucursal_destino'][$row["id_trasferencias"]] = $row["sucursal_destino"];
					$lista_resultados['codigo'][$row["id_trasferencias"]] = $row["codigo"];
					$lista_resultados['nombre_prod'][$row["id_trasferencias"]] = $row["nombre_prod"];
					$lista_resultados['cant_trasf'][$row["id_trasferencias"]] = $row["cant_trasf"];
					$lista_resultados['fecha'][$row["id_trasferencias"]] = $row["fecha"];
				}
			}else
			 return $lista_resultados = "no_data";			
		}
		return $lista_resultados;		
		$result->close();		
	}	
	// comprobar si ya existe el producto
	// si existe se edita y el array datos_prod se usara en el include inc_frm_edit_producto.php para insertar los datos del prod
	// Utilizado para agregar item en caja.php, se independiza por el script buscar_prod_caja.php
	// Utilizado al buscar producto para la vista del PASTELERO
	function comprobar_disponibilidad_producto($codigo,$id_empresa,$id_sucursal){	
		if($result = $this->conn_mysqli->query("SELECT * FROM tbl_producto WHERE codigo = '$codigo' AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND activo='1' AND visible='1'")) {		 
					if($result->num_rows){	
						while ($row = $result->fetch_assoc()) {
							$this->datos_prod['id']	= $row["id"];
							$this->datos_prod['id_empresa']	 	= $row["id_empresa"];
							$this->datos_prod['id_admin']	 	= $row["id_admin"];
							$this->datos_prod['id_sucursal'] 	= $row["id_sucursal"];
							$this->datos_prod['id_unidad']	 	= $row["id_unidad"];
							$this->datos_prod['id_seccion']		= $row["id_seccion"];
							$this->datos_prod['id_proveedor']	= $row["id_proveedor"];
							$this->datos_prod['codigo']		 	= $row["codigo"];
							$this->datos_prod['codigo_unidades']= $row["codigo_unidades"];
							$this->datos_prod['nombre']		 	= $row["nombre"];
							$this->datos_prod['imagen']		 	= $row["imagen"];
							$this->datos_prod['precio_provedor']= $row["precio_provedor"];
							$this->datos_prod['precio_venta']	= $row["precio_venta"];
							$this->datos_prod['precio_mayoreo']	= $row["precio_mayoreo"];
							$this->datos_prod['cantidad']		= $row["cantidad"];
							$this->datos_prod['minimo']			= $row["minimo"];
						}	 		
						return 'existe';						
					}else
						return 'no_existe';
		}else
			return 'error_sql';
		$result->close();		
	}

	// Buscar producto para divirlo en unidades solo si el codigo_unidades esta activo y visible
	function producto_a_unidades_buscar($codigo,$id_empresa,$id_sucursal){	
		$sql = "SELECT * FROM tbl_producto WHERE codigo = '$codigo' AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND activo='1' AND visible='1'";
		if($result = $this->conn_mysqli->query($sql)) {		 
			if($result->num_rows){	
				while ($row = $result->fetch_assoc()) {
					$this->datos_prod['id']	= $row["id"];
					$this->datos_prod['id_empresa']	 	= $row["id_empresa"];
					$this->datos_prod['id_admin']	 	= $row["id_admin"];
					$this->datos_prod['id_sucursal'] 	= $row["id_sucursal"];
					$this->datos_prod['id_unidad']	 	= $row["id_unidad"];
					$this->datos_prod['id_seccion']		= $row["id_seccion"];
					$this->datos_prod['id_proveedor']	= $row["id_proveedor"];
					$this->datos_prod['codigo']		 	= $row["codigo"];
					$this->datos_prod['codigo_unidades']= $row["codigo_unidades"];
					$this->datos_prod['nombre']		 	= $row["nombre"];
					$this->datos_prod['imagen']		 	= $row["imagen"];
					$this->datos_prod['precio_provedor']= $row["precio_provedor"];
					$this->datos_prod['precio_venta']	= $row["precio_venta"];
					$this->datos_prod['precio_mayoreo']	= $row["precio_mayoreo"];
					$this->datos_prod['cantidad']		= $row["cantidad"];
					$this->datos_prod['minimo']			= $row["minimo"];
				}	 	
				$sql_2 = "SELECT * FROM tbl_producto 
						  WHERE codigo = '".$this->datos_prod['codigo_unidades']."' 
						  AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND activo='1' AND visible='1'";
				
				//$this->datos_prod['sql']	= $sql_2;
				if($result2 = $this->conn_mysqli->query($sql_2)) {
					if($result2->num_rows){	
						$row2 = $result2->fetch_assoc();
						$this->datos_prod['nombre_codigo_unidades']		= $row2["nombre"];
						$this->datos_prod['cantidad_codigo_unidades']	= $row2["cantidad"];
						$this->datos_prod['id_codigo_unidades']	= $row2["id"];		
						return 'existe';	
					}else{						
						return 'codigo_unidades_no_disponible';								
					}
				}else{					
					return 'error_sql';	
				}
			}else
				return 'no_existe';
		}else
			return 'error_sql';
		$result->close();		
	}
		
	function listar_producto($id_producto, $order_by, $id_empresa, $sucursales){	
		$where = " AND prod.visible='1' ";
		if($id_producto)
			$where = " AND prod.activo='1' AND id = ".$id_producto;

		$sql = "SELECT prod.*, uni.unidades, suc.sucursal, sec.seccion, prov.empresa FROM tbl_producto prod, 
				tbl_unidades uni, tbl_sucursal suc, tbl_seccion sec, tbl_proveedor prov
				WHERE prod.id_unidad = uni.id_unidades AND prod.id_sucursal = suc.id_sucursal AND prod.id_seccion = sec.id_seccion AND
				prod.id_proveedor = prov.id_proveedor  AND prod.id_empresa = $id_empresa AND prod.id_sucursal IN($sucursales) $where  $order_by";
		if ($result = $this->conn_mysqli->query($sql)) {
			$lista_resultados = array();
			if($result->num_rows){	
				while ($row = $result->fetch_assoc()) {
					$lista_resultados['id'][$row["id"]] = $row["id"];
					$lista_resultados['id_sucursal'][$row["id"]] = $row["id_sucursal"];
					$lista_resultados['id_unidad'][$row["id"]] = $row["id_unidad"];
					$lista_resultados['id_seccion'][$row["id"]] = $row["id_seccion"];
					$lista_resultados['id_proveedor'][$row["id"]] = $row["id_proveedor"];
					$lista_resultados['codigo'][$row["id"]] = $row["codigo"];
					$lista_resultados['nombre'][$row["id"]] = $row["nombre"];
					$lista_resultados['imagen'][$row["id"]] = $row["imagen"];
					$lista_resultados['precio_provedor'][$row["id"]] = $row["precio_provedor"];
					$lista_resultados['precio_venta'][$row["id"]] = $row["precio_venta"];
					$lista_resultados['precio_mayoreo'][$row["id"]] = $row["precio_mayoreo"];
					$lista_resultados['cantidad'][$row["id"]] = $row["cantidad"];
					$lista_resultados['minimo'][$row["id"]] = $row["minimo"];
					$lista_resultados['activo'][$row["id"]] = $row["activo"];
					$lista_resultados['unidades'][$row["id"]] = $row["unidades"];
					$lista_resultados['sucursal'][$row["id"]] = $row["sucursal"];
					$lista_resultados['seccion'][$row["id"]] = $row["seccion"];
					$lista_resultados['empresa'][$row["id"]] = $row["empresa"];
				}
			}else
			 return $lista_resultados = "no_data";			
		}
		return $lista_resultados;		
		$result->close();		
	}
	function listar_producto_pasteles($id_producto, $order_by, $id_empresa, $sucursales){	
		$where = "";
		if($id_producto)
			$where = " ' AND id = ".$id_producto;

		$sql = "SELECT prod.*, uni.unidades, suc.sucursal, sec.seccion, prov.empresa FROM tbl_producto prod, 
				tbl_unidades uni, tbl_sucursal suc, tbl_seccion sec, tbl_proveedor prov
				WHERE prod.nombre like '%pastel%' AND prod.id_unidad = uni.id_unidades AND prod.id_sucursal = suc.id_sucursal AND prod.id_seccion = sec.id_seccion AND
				prod.id_proveedor = prov.id_proveedor  AND prod.id_empresa = $id_empresa AND prod.id_sucursal IN($sucursales) 
				AND prod.activo='1' AND prod.visible='1' $where  $order_by";
		if ($result = $this->conn_mysqli->query($sql)) {
			$lista_resultados = array();
			if($result->num_rows){	
				while ($row = $result->fetch_assoc()) {
					$lista_resultados['id'][$row["id"]] = $row["id"];
					$lista_resultados['id_sucursal'][$row["id"]] = $row["id_sucursal"];
					$lista_resultados['id_unidad'][$row["id"]] = $row["id_unidad"];
					$lista_resultados['id_seccion'][$row["id"]] = $row["id_seccion"];
					$lista_resultados['id_proveedor'][$row["id"]] = $row["id_proveedor"];
					$lista_resultados['codigo'][$row["id"]] = $row["codigo"];
					$lista_resultados['nombre'][$row["id"]] = $row["nombre"];
					$lista_resultados['imagen'][$row["id"]] = $row["imagen"];
					$lista_resultados['precio_provedor'][$row["id"]] = $row["precio_provedor"];
					$lista_resultados['precio_venta'][$row["id"]] = $row["precio_venta"];
					$lista_resultados['precio_mayoreo'][$row["id"]] = $row["precio_mayoreo"];
					$lista_resultados['cantidad'][$row["id"]] = $row["cantidad"];
					$lista_resultados['minimo'][$row["id"]] = $row["minimo"];
					$lista_resultados['activo'][$row["id"]] = $row["activo"];
					$lista_resultados['unidades'][$row["id"]] = $row["unidades"];
					$lista_resultados['sucursal'][$row["id"]] = $row["sucursal"];
					$lista_resultados['seccion'][$row["id"]] = $row["seccion"];
					$lista_resultados['empresa'][$row["id"]] = $row["empresa"];
				}
			}else
			 return $lista_resultados = "no_data";			
		}
		return $lista_resultados;		
		$result->close();		
	}
	
	// Listar el Producto con DEVOLUCION
	function listar_producto_devolucion($order_by, $id_empresa, $sucursales){	
		$where = "";

		$sql = "SELECT tbl_dvlcion.*, tbl_producto.nombre FROM tbl_dvlcion, tbl_producto
				WHERE tbl_dvlcion.id_producto = tbl_producto.id AND tbl_dvlcion.id_empresa = $id_empresa AND tbl_dvlcion.id_sucursal IN($sucursales) $order_by";
		if ($result = $this->conn_mysqli->query($sql)) {
			$lista_resultados = array();
			if($result->num_rows){	
				while ($row = $result->fetch_assoc()) {
					$row['motivo'] = str_ireplace('##br##',"<br>",$row['motivo']);
					$lista_resultados['id_dvlcion'][$row["id_dvlcion"]] = $row["id_dvlcion"];
					$lista_resultados['sucursal'][$row["id_dvlcion"]] = $row["sucursal"]; 
					$lista_resultados['nombre_empleado'][$row["id_dvlcion"]] = $row["nombre_empleado"];
					$lista_resultados['id_producto'][$row["id_dvlcion"]] = $row["id_producto"];
					$lista_resultados['nombre'][$row["id_dvlcion"]] = $row["nombre"];
					$lista_resultados['motivo'][$row["id_dvlcion"]] = $row["motivo"];
					$lista_resultados['fecha'][$row["id_dvlcion"]] = $row["fecha"];
				}
			}else
			 return $lista_resultados = "no_data";			
		}
		return $lista_resultados;		
		$result->close();		
	}	
	
	function activa_producto($id, $valor){
		if($result = $this->conn_mysqli->prepare("UPDATE tbl_producto SET activo = ? WHERE id = ?")) {		 
			if($result->bind_param("si", $valor, $id)){
				if($result->execute()){
				 	return '{"tipo":"producto_activo"}';
				}else
					return '{"tipo":"error_execute"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}	
	function borrar_producto($id){
		if($result = $this->conn_mysqli->prepare("UPDATE tbl_producto SET activo = '0', visible = '0' WHERE id = ?")) {		 
			if($result->bind_param("i", $id)){
				if($result->execute()){
				 	return '{"tipo":"update_ok"}';
				}else
					return '{"tipo":"error_execute"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}
	
	
	function get_nombre_producto($id, $n_letras){
		$sql = "SELECT nombre FROM tbl_producto WHERE id=".$id;
		if ($result = $this->conn_mysqli->query($sql)) {
			$row = $result->fetch_assoc();
		}
		return substr($row['nombre'], 0, $n_letras);
	}
	function get_nombre_producto_code($id, $n_letras){
		$sql = "SELECT nombre FROM tbl_producto WHERE codigo='$id'";
		if ($result = $this->conn_mysqli->query($sql)) {
			$row = $result->fetch_assoc();
		}
		return substr($row['nombre'], 0, $n_letras);
	}
	
	function insert_devolucion($id_empresa, $id_sucursal, $sucursal, $NumEmp, $nombre_empleado, $id_producto, $motivo){
		if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_dvlcion (id_empresa, id_sucursal, sucursal, NumEmp, nombre_empleado, id_producto, motivo) VALUES (?,?,?,?,?,?,?)")) {		 
			if($result->bind_param("iisisis",$id_empresa, $id_sucursal, $sucursal, $NumEmp, $nombre_empleado, $id_producto, $motivo)){
				if($result->execute()){
				 	return '{"tipo":"ok_update"}';
				}else
					return '{"tipo":"error_execute"}';	// $result->error;  //
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}	
	
	function insert_venta_caja($id_empresa, $id_sucursal, $sucursal, $NumEmp, $nombre_empleado, $id_productos, $cantidades, $array_precio_prod, $ganancias, $total, $ganancia_total, $card, $aprobacion_card, $nombre_genericos){
		if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_ventas_caja (id_empresa, id_sucursal, sucursal, NumEmp, nombre_empleado, id_productos, cantidades, precios, ganancias, nombre_genericos, total, total_ganancia, tarjeta, aprobacion_card) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)")) {		 
			if($result->bind_param("iisissssssddss",$id_empresa, $id_sucursal, $sucursal, $NumEmp, $nombre_empleado, $id_productos, $cantidades, $array_precio_prod, $ganancias, $nombre_genericos, $total, $ganancia_total, $card, $aprobacion_card)){
				if($result->execute()){
				 	return '{"status":"ok_insert"}';
				}else
					return '{"status":"error"}';	// $this->conn_mysqli->error  //
			}else
				return '{"status":"error"}';
		}else
			return '{"status":"error"}';
		$result->close();		
	}	
	
	function edit_venta_caja($id_ventas_cajas, $id_productos, $cantidades, $array_precio_prod, $ganancias, $total, $ganancia_total){
		if($result = $this->conn_mysqli->prepare("UPDATE tbl_ventas_caja SET id_productos=?, cantidades=?, precios=?, ganancias=?, total=?, total_ganancia=? WHERE id_ventas_cajas = ?")) {		 
			if($result->bind_param("ssssddi", $id_productos, $cantidades, $array_precio_prod, $ganancias, $total, $ganancia_total, $id_ventas_cajas)){
				if($result->execute()){
				 	return '{"status":"venta_editada"}';
				}else
					return '{"status":"no_update"}';	// $result->error;  //
			}else
				return '{"status":"no_update"}';
		}else
			return '{"status":"no_update"}';
		$result->close();		
	}		
	
	function insert_retiro_ingreso_caja($id_empresa, $id_sucursal, $sucursal, $NumEmp, $nombre_empleado, $concepto, $catidad_retiro,$comentario_retiro){
		if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_salida_entrada_caja (id_empresa, id_sucursal, sucursal, NumEmp, nombre_empleado, concepto, motivo, total) VALUES (?,?,?,?,?,?,?,?)")) {		 
			if($result->bind_param("iisisssd",$id_empresa, $id_sucursal, $sucursal, $NumEmp, $nombre_empleado, $concepto, $comentario_retiro, $catidad_retiro)){
				if($result->execute()){
				 	return '{"status":"retiro_ingreso_registrada"}';
				}else
					return '{"status":"error_execute"}';	// $result->error;  //
			}else
				return '{"status":"error_parametros"}';
		}else
			return '{"status":"error_sql"}';
		$result->close();
	}
	function insert_usr_debe($id_empresa, $id_sucursal, $sucursal, $NumEmp, $nombre_empleado, $nombre_deben, $cantidad_deben,$comentario_deben){
		$fecha = date('Y-m-d H:i:s');
		$estatus = 'debe';
		if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_deben (id_empresa, id_sucursal, sucursal, NumEmp, nombre_empleado, nombre, cantidad, nota, fecha, estatus) VALUES (?,?,?,?,?,?,?,?,?,?)")) {		 
			if($result->bind_param("iisissdsss",$id_empresa, $id_sucursal, $sucursal, $NumEmp, $nombre_empleado, $nombre_deben, $cantidad_deben, $comentario_deben, $fecha, $estatus)){
				if($result->execute()){
				 	return '{"status":"ok"}';
				}else
					return '{"status":"error_execute"}';	// $result->error;  //
			}else
				return '{"status":"error_parametros"}';
		}else
			return '{"status":"error_sql"}';
		$result->close();
	}
	function debenUpdate($id){
		$estatus = 'pagado';
		if($result = $this->conn_mysqli->prepare("UPDATE tbl_deben SET estatus = ?  WHERE id = ?")) {		 
			if( $result->bind_param("si", $estatus, $id) ){
				if( $result->execute() ){
					return '{"status":"ok"}';
				}else
					return '{"tipo":"error_execute"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';	
	}

/*******************************************************************************************************************/
/*************************************************  CLIENTES   *****************************************************/
/*******************************************************************************************************************/
	function insert_cliente($id_empresa, $id_admin, $nombre, $id_sucursal, $dir, $ciudad, $tel, $cel, $correo, $obs){
		$fecha = date('Y-m-d H:i:s');
		$obs = str_replace(array("\r\n","\r","\n"), "<br>",$obs);
		if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_cliente (id_empresa,id_admin,nombre,id_sucursal,dir,ciudad,tel,cel,correo,obs) VALUES (?,?,?,?,?,?,?,?,?,?)")) {		 
			if($result->bind_param("iisissssss",$id_empresa, $id_admin, $nombre, $id_sucursal, $dir, $ciudad, $tel, $cel, $correo, $obs)){
				if($result->execute()){
				 	return '{"tipo":"cliente_registrado"}';
				}else
					return '{"tipo":"error_execute"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}
	function edit_cliente($nombre, $dir, $ciudad, $tel, $cel, $correo, $obs, $id){
		$fecha = date('Y-m-d H:i:s');
		if($result = $this->conn_mysqli->prepare("UPDATE tbl_cliente SET nombre=?,dir=?,ciudad=?,tel=?,cel=?,correo=?,obs=? WHERE id_cliente = ?")) {		 
			if($result->bind_param("sssssssi", $nombre, $dir, $ciudad, $tel, $cel, $correo, $obs, $id)){
				if($result->execute()){
				 	return '{"tipo":"cliente_update"}';
				}else
					return '{"tipo":"error_execute"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}		
	
/*******************************************************************************************************************/
/*************************************************  PEDIDOS   *****************************************************/
/*******************************************************************************************************************/
	function insert_pedido($id_empresa, $id_sucursal, $id_admin,$id_cliente,$fecha_inicio, $fecha_final, $total, $anticipo_inicial=0, $estatus, $obs='', $servicios){
		$fecha = date('Y-m-d H:i:s');
		$obs = str_replace(array("\r\n","\r","\n"), "<br>",$obs);
		$sql = "INSERT INTO tbl_pedido (id_empresa,id_sucursal,id_admin,id_cliente,fecha_inicio,fecha_final,total,anticipo_inicial,estatus,obs,servicios) 
										 VALUES ($id_empresa, $id_sucursal, $id_admin,$id_cliente,'$fecha_inicio', '$fecha_final', $total, $anticipo_inicial, '$estatus', '$obs', '$servicios')";
		
		if($result = $this->conn_mysqli->prepare($sql)) {		 
				if($result->execute()){
				 	return '{"tipo":"pedido_registrado"}';				
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"'.$this->conn_mysqli->error.'}';
		$result->close();		
	}
	function add_pago_pedido($num_emp, $nombre, $id_pedido, $anticipo){
		$fecha = date('Y-m-d H:i:s');
		$tipo = "abrir_status";
		$sql = "SELECT total, anticipo_inicial, anticipos FROM tbl_pedido WHERE id_pedido = $id_pedido";
		if ($result = $this->conn_mysqli->query($sql)) {
			$row = $result->fetch_assoc();
			if($row['anticipos'] == ''){				
				$json_string = '[{"num_emp":'.$num_emp.',"nombre":"'.$nombre.'","anticipo":'.$anticipo.',"fecha":"'.$fecha.'"}]';
				$anticipos_total = $anticipo + $row["anticipo_inicial"];
				if($anticipos_total >= $row["total"])
					$tipo = "cerrar_status";
			}else{
				$anticipos = $row['anticipos'];
				$json_string = json_decode($anticipos);

				$datos_pago = new stdClass();
				$datos_pago->num_emp = $num_emp;
				$datos_pago->nombre = $nombre;
				$datos_pago->anticipo = $anticipo;
				$datos_pago->fecha = $fecha;
								
				array_push($json_string, $datos_pago);
				
				// calcular los pagos para cerrar estatus en caso de que ya haya llegado al tope los pagos
				$anticipos_total = 0;
				foreach($json_string as $rowj){
					$anticipos_total = $anticipos_total + $rowj->anticipo;
				}
				$anticipos_total = $anticipos_total + $row["anticipo_inicial"];
				if($anticipos_total >= $row["total"])
					$tipo = "cerrar_status";
					
				$json_string = json_encode($json_string);
			}
			
			
			
			if($json_string == "null"){
				return '{"tipo":"error_execute"}';
			}				
			if($tipo == "abrir_status"){		
				if($result = $this->conn_mysqli->prepare("UPDATE tbl_pedido SET anticipos = ?  WHERE id_pedido = ?")) {		 
					if($result->bind_param("si",$json_string, $id_pedido)){
						if($result->execute()){
							//$id = $this->conn_mysqli->insert_id;
							return '{"tipo":"update_registrado","estatus":"abierto","anticipos":'.$anticipos_total.',"total":'.$row["total"].'}';
						}else
							return '{"tipo":"error_execute"}';
					}else
						return '{"tipo":"error_parametros"}';
				}else
					return '{"tipo":"error_sql"}';	
			}
			if($tipo == "cerrar_status"){
				$estatus = "cerrado";
				if($result = $this->conn_mysqli->prepare("UPDATE tbl_pedido SET anticipos = ?, estatus = ?  WHERE id_pedido = ?")) {		 
					if($result->bind_param("ssi",$json_string, $estatus, $id_pedido)){
						if($result->execute()){
							//$id = $this->conn_mysqli->insert_id;		,"estatus":"cerrado"
							return '{"tipo":"update_registrado","estatus":"cerrado"}';
						}else
							return '{"tipo":"error_execute"}';
					}else
						return '{"tipo":"error_parametros"}';
				}else
					return '{"tipo":"error_sql"}';		
			}
		}
		$result->close();
	}
	
	function ver_pagos_pedido($id_pedido){
		$sql = "SELECT anticipos FROM tbl_pedido WHERE id_pedido = $id_pedido";
		if ($result = $this->conn_mysqli->query($sql)) {
			$row = $result->fetch_assoc();
		}
		return $row['anticipos'];
	}
	function cerrar_pedido($id_pedido){
			$estatus = "cerrado";
			if($result = $this->conn_mysqli->prepare("UPDATE tbl_pedido SET estatus = ?  WHERE id_pedido = ?")) {		 
				if($result->bind_param("si", $estatus, $id_pedido)){
					if($result->execute()){
						//$id = $this->conn_mysqli->insert_id;
						return '{"tipo":"update_registrado"}';
					}else
						return '{"tipo":"error_execute"}';
				}else
					return '{"tipo":"error_parametros"}';
			}else
				return '{"tipo":"error_sql"}';			
	}
	function cancelar_pedido($id_pedido, $obs_cancelado){
			$estatus = "cancelado";
			if($result = $this->conn_mysqli->prepare("UPDATE tbl_pedido SET estatus = ?, obs_cancelado = ?  WHERE id_pedido = ?")) {		 
				if($result->bind_param("ssi", $estatus, $obs_cancelado, $id_pedido)){
					if($result->execute()){
						//$id = $this->conn_mysqli->insert_id;
						return '{"tipo":"update_registrado"}';
					}else
						return '{"tipo":"error_execute"}';
				}else
					return '{"tipo":"error_parametros"}';
			}else
				return '{"tipo":"error_sql"}';			
	}
	function devolver_deposito($id_pedido, $json_string){
			if($result = $this->conn_mysqli->prepare("UPDATE tbl_pedido SET servicios = ? WHERE id_pedido = ?")) {		 
				if($result->bind_param("si", $json_string, $id_pedido)){
					if($result->execute()){
						//$id = $this->conn_mysqli->insert_id;
						return '{"tipo":"update_registrado"}';
					}else
						return '{"tipo":"error_execute"}';
				}else
					return '{"tipo":"error_parametros"}';
			}else
				return '{"tipo":"error_sql"}';			
	}		
/*************************************************  LISTADO PARA MODULO DE PROVEDORES  *****************************************************/
	function listar_clientes($id_cliente, $id_empresa, $id_sucursal){	//  unidad_negocio='".$usr_ag."'
		$where = "WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND activo='1'";
		if($id_cliente)
			$where = "WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND activo='1' AND id_cliente = ".$id_cliente;

		$sql = "SELECT * FROM tbl_cliente ".$where;
		if ($result = $this->conn_mysqli->query($sql)) {
			$lista_resultados = array();
			if($result->num_rows){	
				while ($row = $result->fetch_assoc()) {
					$lista_resultados['id_cliente'][$row["id_cliente"]] = $row["id_cliente"];
					$lista_resultados['nombre'][$row["id_cliente"]] = $row["nombre"];
					$lista_resultados['dir'][$row["id_cliente"]] = $row["dir"];
					$lista_resultados['ciudad'][$row["id_cliente"]] = $row["ciudad"];
					$lista_resultados['tel'][$row["id_cliente"]] = $row["tel"];
					$lista_resultados['cel'][$row["id_cliente"]] = $row["cel"];
					$lista_resultados['correo'][$row["id_cliente"]] = $row["correo"];
					$lista_resultados['obs'][$row["id_cliente"]] = nl2br($row["obs"]);
					$lista_resultados['activo'][$row["id_cliente"]] = $row["activo"];
				}
			}else
			 return $lista_resultados = "no_data";			
		}
		return $lista_resultados;		
		$result->close();		
	}
	function activa_cliente($id, $valor){
		if($result = $this->conn_mysqli->prepare("UPDATE tbl_cliente SET activo = ? WHERE id_cliente = ?")) {		 
			if($result->bind_param("si", $valor, $id)){
				if($result->execute()){
				 	return '{"tipo":"cliente_activo"}';
				}else
					return '{"tipo":"cliente_error"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/****************************************************	FUNCION  PARA INSERT SUCURSAL		**************************************************/
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function insert_sucursal($id_empresa, $sucursal){
		$fecha = date('Y-m-d H:i:s');

		$sql = "SELECT * FROM tbl_sucursal WHERE sucursal = '$sucursal' AND id_empresa = $id_empresa";
		if ($result = $this->conn_mysqli->query($sql)) {
			if(!$result->num_rows){		
				if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_sucursal (id_empresa,sucursal) VALUES (?,?)")) {		 
					if($result->bind_param("is",$id_empresa, $sucursal)){
						if($result->execute()){
							$id = $this->conn_mysqli->insert_id;
							return '{"tipo":"sucursal_registrado","id":'.$id.',"nombre":"'.$sucursal.'"}';
						}else
							return '{"tipo":"error_execute"}';
					}else
						return '{"tipo":"error_parametros"}';
				}else
					return '{"tipo":"error_sql"}';
			}else
				return '{"tipo":"duplicado"}';
		}
		$result->close();		
			
	}	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/****************************************************	FUNCION  PARA INSERT UNIDAD 		**************************************************/
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function insert_unidad($id_empresa,$id_sucursal, $unidad){
		$fecha = date('Y-m-d H:i:s');

		$sql = "SELECT * FROM tbl_unidades WHERE unidades = '$unidad' AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal";
		if ($result = $this->conn_mysqli->query($sql)) {
			if(!$result->num_rows){		
				if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_unidades (id_empresa,id_sucursal,unidades) VALUES (?,?,?)")) {		 
					if($result->bind_param("iis",$id_empresa,$id_sucursal,$unidad)){
						if($result->execute()){
							$id = $this->conn_mysqli->insert_id;
							return '{"tipo":"unidad_registrado","id":'.$id.',"nombre":"'.$unidad.'"}';
						}else
							return '{"tipo":"error_execute"}';
					}else
						return '{"tipo":"error_parametros"}';
				}else
					return '{"tipo":"error_sql"}';
			}else
				return '{"tipo":"duplicado"}';
		}
		$result->close();		
			
	}	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/****************************************************	FUNCION  PARA INSERT SECCION		**************************************************/
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function insert_seccion($id_empresa,$id_sucursal, $seccion){
		$fecha = date('Y-m-d H:i:s');

		$sql = "SELECT * FROM tbl_seccion WHERE seccion = '$seccion' AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal";
		if ($result = $this->conn_mysqli->query($sql)) {
			if(!$result->num_rows){		
				if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_seccion (id_empresa,id_sucursal,seccion) VALUES (?,?,?)")) {		 
					if($result->bind_param("iis",$id_empresa,$id_sucursal,$seccion)){
						if($result->execute()){
							$id = $this->conn_mysqli->insert_id;
							return '{"tipo":"seccion_registrado","id":'.$id.',"nombre":"'.$seccion.'"}';
						}else
							return '{"tipo":"error_execute"}';
					}else
						return '{"tipo":"error_parametros"}';
				}else
					return '{"tipo":"error_sql"}';
			}else
				return '{"tipo":"duplicado"}';
		}
		$result->close();		
			
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/****************************************************	MODULO PARA ADMINISTRADOR		**************************************************/
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*************************************************  LISTADO PARA MODULO DE PROVEDORES  *****************************************************/
	function listar_adm_usuarios($id_cliente, $id_empresa, $id_sucursal){	//  unidad_negocio='".$usr_ag."'
		$where = "WHERE tbl_usuarios.id_sucursal = suc.id_sucursal AND tbl_usuarios.id_empresa = $id_empresa AND activo='1'";
		if($id_cliente)
			$where = "WHERE tbl_usuarios.id_sucursal = suc.id_sucursal AND tbl_usuarios.id_empresa = $id_empresa AND activo='1' AND NumEmp = ".$id_cliente;

		$sql = "SELECT tbl_usuarios.*, suc.sucursal FROM tbl_usuarios, tbl_sucursal suc ".$where;
		if ($result = $this->conn_mysqli->query($sql)) {
			$lista_resultados = array();
			if($result->num_rows){	
				while ($row = $result->fetch_assoc()) {
					$lista_resultados['NumEmp'][$row["NumEmp"]] = $row["NumEmp"];
					$lista_resultados['Nombre'][$row["NumEmp"]] = $row["Nombre"];
					$lista_resultados['sucursal'][$row["NumEmp"]] = $row["sucursal"];
					$lista_resultados['registrado'][$row["NumEmp"]] = $row["registrado"]; 
				}
			}else
			 return $lista_resultados = "no_data";			
		}
		return $lista_resultados;		
		$result->close();		
	}
	function edit_adm_usuario($id, $password){
		//$fecha = date('Y-m-d H:i:s');
		$password = "pwd|".md5($password);
		if($result = $this->conn_mysqli->prepare("UPDATE tbl_usuarios SET password=? WHERE NumEmp = ?")) {		 
			if($result->bind_param("si", $password, $id)){
				if($result->execute()){
				 	return '{"tipo":"cliente_update"}';
				}else
					return '{"tipo":"error_execute"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}		
	function activa_adm_usuario($id, $valor){
		if($result = $this->conn_mysqli->prepare("UPDATE tbl_usuarios SET registrado = ? WHERE NumEmp = ?")) {		 
			if($result->bind_param("si", $valor, $id)){
				if($result->execute()){
				 	return '{"tipo":"cliente_activo"}';
				}else
					return '{"tipo":"cliente_error"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}
	
/*******************************************************************************************************************/
/*********************************************   Nuevo Empleado   **************************************************/
/*******************************************************************************************************************/
	function insert_empleado($id_empresa,$id_sucursal, $nombre, $pwd){
		$fecha = date('Y-m-d H:i:s'); 
		$password = "pwd|".md5($pwd);
		$nivel="vendedor";
		$registrado=1;
		$sql = "SELECT * FROM tbl_usuarios where Nombre = '".$nombre."'";
		$result = $this->conn_mysqli->query($sql);
		$num = $result->num_rows;
		if($num)
			return '{"tipo":"duplicado"}';
							
		if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_usuarios (id_empresa,id_sucursal,sucursales,Nombre,nivel,password,registrado,fecha_registro) VALUES (?,?,?,?,?,?,?,?)")) {		 
			if($result->bind_param("iiisssis",$id_empresa, $id_sucursal, $id_sucursal, $nombre, $nivel, $password, $registrado,$fecha)){
				if($result->execute()){
				 	return '{"tipo":"insert_ok"}';
				}else
					return '{"tipo":"error_execute"}';
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();		
	}	
/*******************************************************************************************************************/
/*********************************************   Nuevo LOG        **************************************************/
/*******************************************************************************************************************/
	function nuevo_log($id_empresa, $id_sucursal, $id_admin, $movimiento){
		if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_log (id_empresa, id_sucursal, id_admin, movimiento) VALUES (?,?,?,?)")) {		 
			if($result->bind_param("iiis",$id_empresa, $id_sucursal, $id_admin, $movimiento)){
				if($result->execute()){
				 	return '{"tipo":"insert_ok"}';
				}else
					return '{"tipo":"error_execute"}';	// $result->error;  //
			}else
				return '{"tipo":"error_parametros"}';
		}else
			return '{"tipo":"error_sql"}';
		$result->close();
	}	
	
/*******************************************************************************************************************/
/*********************************************   Get Accesos Rapidos en Caja         *******************************/
/*******************************************************************************************************************/
function get_accesos_rapidos($id_empresa, $id_sucursal){
	$sql = "SELECT tbl_accrapid.id_prod, prod.nombre, prod.imagen FROM tbl_accrapid, tbl_producto prod
			WHERE tbl_accrapid.id_prod = prod.codigo AND tbl_accrapid.activo = '1' AND tbl_accrapid.id_empresa =".$id_empresa." AND tbl_accrapid.id_sucursal =".$id_sucursal.
			" ORDER BY tbl_accrapid.orden";
	if ($result = $this->conn_mysqli->query($sql)) {
		$lista_resultados = array();
		$lista_resultados2 = array();
		if($result->num_rows){	
			while ($row = $result->fetch_assoc()) { 
				$lista_resultados['id_prod'] = $row["id_prod"];
				$lista_resultados['nombreCompleto'] = $row["nombre"];
				// cortar el nombre si excede
				if( strlen( $row["nombre"] ) >= 30 ){
					$row["nombre"] = substr($row["nombre"], 0, 27) . '...';
					echo $text;
				}

				$lista_resultados['nombre'] = $row["nombre"];
				$lista_resultados['imagen'] = $row["imagen"];
				array_push($lista_resultados2, $lista_resultados);
			}
		}else
			return $lista_resultados = "no_data";			
	}
	return $lista_resultados2;		
	$result->close();
}

/*******************************************************************************************************************/
/*********************************************   Del Accesos Rapidos en Caja         *******************************/
/*******************************************************************************************************************/
function del_accesoCaja($id_prod){
	$sql = "DELETE FROM tbl_accrapid WHERE id_prod = '".$id_prod."' LIMIT 1";

	if ($result = $this->conn_mysqli->query($sql)) { 
		if($this->conn_mysqli->affected_rows){	
			return '{"status":"ok"}';	
		}else
			return '{"status":"error_sql"}';		
	}	 	
	$result->close();
}
function add_accesoCaja($id_prod, $id_empresa, $id_sucursal){
	$activo = '1';
	$sqlCount = "SELECT MAX(orden) + 1 as total FROM tbl_accrapid";
	$result = $this->conn_mysqli->query($sqlCount);
	$row = $result->fetch_assoc();
	$total = $row['total'];

	if($result = $this->conn_mysqli->prepare("INSERT INTO tbl_accrapid (id_empresa,id_sucursal,orden, id_prod,activo) VALUES (?,?,?,?,?)")) {		 
		if($result->bind_param("iiiss",$id_empresa, $id_sucursal, $total, $id_prod, $activo)){
			if($result->execute()){
				 return '{"status":"ok"}';
			}else
				return '{"status":"error_execute"}';
		}else
			return '{"status":"error_parametros"}';
	}else
		return '{"status":"error_sql"}';
	 	
	$result->close();
}
	
/*******************************************************************************************************************/
/*************************************************   LISTBOX   *****************************************************/
/*******************************************************************************************************************/
/*************************************************  LISTBOX DE PROVEDORES  *****************************************************/
	public function lst_provedor($tabla, $value, $txt, $seleccionar='0', $comparar_campo, $id_empresa){
		$sql = "SELECT * FROM $tabla where activo = '1' AND id_empresa =".$id_empresa." ORDER BY $txt";
		$result = $this->conn_mysqli->query($sql);
		$num = $result->num_rows;
		$seleccionar = explode(",", $seleccionar);
		$lista = '';
		if($num){
			$item = '';
			//$lista .= "<option value=''>--AG--</option>";
			while ($row = $result->fetch_assoc()) {
				foreach($seleccionar as $i => $ags){
					$ags = trim($ags, "'");
					if($ags == $row[$value])
						$item = " selected='selected' ";

				}
					$lista .= "<option ".$item." value='".$row[$value]."'>".$row[$txt]."</option>";
					$item = '';				
			}		
		}	
		return $lista;
	}
/*************************************************  LISTBOX DE UNIDADES  *****************************************************/	
	public function lst_unidades($tabla, $value, $txt, $seleccionar='0', $comparar_campo, $id_empresa, $id_sucursal){
		$sql = "SELECT * FROM $tabla where activo = '1' AND id_empresa =".$id_empresa." AND id_sucursal = ".$id_sucursal." ORDER BY $txt";
		$result = $this->conn_mysqli->query($sql);
		$num = $result->num_rows;
		$seleccionar = explode(",", $seleccionar);
		$lista = '';
		if($num){
			$item = '';
			//$lista .= "<option value=''>--AG--</option>";
			while ($row = $result->fetch_assoc()) {
				foreach($seleccionar as $i => $ags){
					if($ags == $row[$value])
						$item = " selected='selected' ";

				}
					$lista .= "<option ".$item." value='".$row[$value]."'>".$row[$txt]."</option>";
					$item = '';				
			}		
		}	
		return $lista;
	}
/*************************************************  LISTBOX DE SECCION  *****************************************************/	
	public function lst_seccion($tabla, $value, $txt, $seleccionar='0', $comparar_campo, $id_empresa, $id_sucursal){
		$sql = "SELECT * FROM $tabla where activo = '1' AND id_empresa =".$id_empresa." AND id_sucursal = ".$id_sucursal." ORDER BY $txt";
		$result = $this->conn_mysqli->query($sql);
		$num = $result->num_rows;
		$seleccionar = explode(",", $seleccionar);
		$lista = '';
		if($num){
			$item = '';
			//$lista .= "<option value=''>--AG--</option>";
			while ($row = $result->fetch_assoc()) {
				foreach($seleccionar as $i => $ags){
					if($ags == $row[$value])
						$item = " selected='selected' ";

				}
					$lista .= "<option ".$item." value='".$row[$value]."'>".$row[$txt]."</option>";
					$item = '';				
			}		
		}	
		return $lista;
	}
/*************************************************  LISTBOX DE SUCURSALES  *****************************************************/	
	public function lst_sucursales($tabla, $value, $txt, $seleccionar='0', $comparar_campo, $id_empresa){
		$sql = "SELECT * FROM $tabla where activo = '1' AND id_empresa =".$id_empresa." ORDER BY $txt";
		$result = $this->conn_mysqli->query($sql);
		$num = $result->num_rows;
		$seleccionar = explode(",", $seleccionar);
		$lista = '';
		if($num){
			$item = '';
			//$lista .= "<option value=''>--AG--</option>";
			while ($row = $result->fetch_assoc()) {
				foreach($seleccionar as $i => $ags){
					if($ags == $row[$value])
						$item = " selected='selected' ";											
				}
					$lista .= "<option ".$item." value='".$row[$value]."'>".$row[$txt]."</option>";
					$item = '';				
			}		
		}	
		return $lista;
	}
/*************************************************  LISTBOX DE SUCURSALES ADMIN  *****************************************************/	
	public function lst_sucursales_admin($tabla, $value, $txt, $seleccionar='0', $comparar_campo, $id_empresa, $id_sucursales, $saltar='0'){
		$sql = "SELECT * FROM $tabla where activo = '1' AND id_empresa =".$id_empresa."  AND id_sucursal IN (".$id_sucursales.") ORDER BY $txt";
		$result = $this->conn_mysqli->query($sql);
		$num = $result->num_rows;
		$seleccionar = explode(",", $seleccionar);
		$lista = '<option value="">-Sucursales-</option>';
		if($num){
			$item = '';
			//$lista .= "<option value=''>--AG--</option>";
			while ($row = $result->fetch_assoc()) {
				foreach($seleccionar as $i => $ags){
					if($ags == $row[$value])
						$item = " selected='selected' ";
				}
				if($saltar == $row[$value])
					continue;		
				$lista .= "<option ".$item." value='".$row[$value]."'>".$row[$txt]."</option>";
				$item = '';				
			}		
		}	
		return $lista;
	}
/*************************************************  LISTBOX DE USUARIOS  *****************************************************/	
	public function lst_usuarios($tabla, $value, $txt, $seleccionar='0', $comparar_campo, $id_empresa, $id_sucursales){
		$sql = "SELECT * FROM $tabla where  id_empresa =".$id_empresa."  AND id_sucursal IN (".$id_sucursales.") ORDER BY $txt";
		$result = $this->conn_mysqli->query($sql);
		$num = $result->num_rows;
		$seleccionar = explode(",", $seleccionar);
		$lista = '<option value="">-Usuarios-</option>';
		if($num){
			$item = '';
			//$lista .= "<option value=''>--AG--</option>";
			while ($row = $result->fetch_assoc()) {
				foreach($seleccionar as $i => $ags){
					if($ags == $row[$value])
						$item = " selected='selected' ";

				}
					$lista .= "<option ".$item." value='".$row[$value]."'>".$row[$txt]."</option>";
					$item = '';				
			}		
		}	
		return $lista;
	}		
/*************************************************  LISTBOX DE SERVICIOS  *****************************************************/	
	public function lst_servicios($tabla, $value, $txt, $seleccionar='0', $comparar_campo, $id_empresa, $id_sucursal){
		$sql = "SELECT pr.*, uni.unidades FROM tbl_producto pr, tbl_unidades uni
				WHERE pr.id_unidad = uni.id_unidades AND uni.unidades = 'renta' AND  pr.activo = '1' AND pr.id_empresa =".$id_empresa." AND pr.id_sucursal = ".$id_sucursal." ORDER BY pr.nombre";
		$result = $this->conn_mysqli->query($sql);
		$num = $result->num_rows;
		$seleccionar = explode(",", $seleccionar);
		$lista = '';
		if($num){
			$item = '';
			//$lista .= "<option value=''>--AG--</option>";
			while ($row = $result->fetch_assoc()) {
				$lista .= "<input class='chk_servicios' 
								  name='".$row[$txt]."' 
								  precio_venta='".$row['precio_venta']."' 
								  type='checkbox' ".$item." 
								  value='".$row[$value]."'>".$row[$txt]."<br />";		
			}		
		}	
		return $lista;
	}	
	
	

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/****************************************************	FUNCION  PARA LIMPIADO DE POST		**************************************************/
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function cleanInput($input) {
	  $search = array(
		'@<script[^>]*?>.*?</script>@si',   // Elimina javascript
		'@<[\/\!]*?[^<>]*?>@si',            // Elimina las etiquetas HTML
		'@<style[^>]*?>.*?</style>@siU',    // Elimina las etiquetas de estilo
		'@<![\s\S]*?--[ \t\n\r]*>@'         // Elimina los comentarios multi-línea
	  );
	    $input = str_replace(array("\r\n","\r","\n"), "##br##",$input);		// en caso de un salto de linea permitirlo
		$output = preg_replace($search, '', $input);
		
		return $output;
	  }
	 
	function sanitize($input) {
		if (is_array($input)) {
			foreach($input as $var=>$val) {
				$output[$var] = $this->sanitize($val);
			}
		}
		else {
			if (get_magic_quotes_gpc()) {
				$input = stripslashes($input);
			}
			$output  = $this->cleanInput($input);
			$output = $this->conn_mysqli->real_escape_string($output);
		}
		return $output;
	}
	function sanear_string_especiales($string){	
		$string = trim($string);
		$string = str_replace(
			array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
			array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
			$string
		);
		$string = str_replace(
			array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
			array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
			$string
		);
		$string = str_replace(
			array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
			array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
			$string
		);
		$string = str_replace(
			array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
			array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
			$string
		);
		$string = str_replace(
			array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
			array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
			$string
		);
		$string = str_replace(
			array('ñ', 'Ñ', 'ç', 'Ç'),
			array('n', 'N', 'c', 'C',),
			$string
		);
		//Esta parte se encarga de eliminar cualquier caracter extraño
		$string = str_replace(
			array("\\", "¨", "º", "-", "~",
				 "#", "@", "|", "!", "\"",
				 "·", "$", "%", "&", "/",
				 "(", ")", "?", "'", "¡",
				 "¿", "[", "^", "`", "]",
				 "+", "}", "{", "¨", "´",
				 ">", "< ", ";", ",", ":",
				 " "),
			'',
			$string
		);
	
		return $string;
	}
	function damemes($mes)
	{
	// Consegir mes actual como numero
	$month = $mes;
	// Seleccionar thema para el mes
	switch($month) {
		case 1:
		return "Ene";
		break;		
		case 2:
		return "Feb";
		break;		
		case 3:
		return "Mar";
		break;		
		case 4:
		return "Abr";
		break;		
		case 5:
		return "May";
		break;		
		case 6:
		return "Jun";
		break;
		case 7:
		return "Jul";
		break;		
		case 8:
		return "Ago";
		break;		
		case 9:
		return "Sep";
		break;		
		case 10:
		return "Oct";
		break;		
		case 11:
		return "Nov";
		break;		
		case 12:
		return "Dic";
		break;
		}	
	}		
}
?>